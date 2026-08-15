# Prompt for IDE: Add "Priority" flag to FaucetList

Add a priority/favorite feature to the faucet list so certain faucets (e.g. ones with a daily bonus) surface at the top of the list the moment they're claimable, without disturbing normal countdown sorting while they're still ticking.

## Data model

Add a boolean field to each faucet record, e.g. `priority: false` (default `false`). Store it alongside the existing name/timer/URL fields in whatever flat-file/JSON structure the faucet list already uses. No new file, no new table — just one new key per faucet entry.

## UI changes

1. **Add/Edit Faucet form**: add a checkbox labeled "Priority" (or "⭐ Priority") next to the existing Name/Timer/URL fields. Reflects and writes the `priority` boolean.

2. **Row-level toggle**: add a small star icon (⭐ filled / ☆ empty) in the row itself — clicking it flips `priority` directly via the same update path the edit form uses, without opening the full edit modal. This should be the primary way users toggle it day-to-day.

3. **Visual marker while counting down**: when `priority` is `true`, show a subtle filled star (⭐) next to the faucet name at all times — including while the timer is still running — so it's easy to spot in the list before it's ready. Keep it small/subtle so it doesn't interfere with the existing row gradient styling. When `priority` is `false`, show nothing (or an empty outline star only on hover, if that fits the existing hover pattern).

## Sort logic

Update the sort function with this precedence:

1. **Ready faucets first** (timer at zero / claimable), same as current behavior.
   - Within the ready group: **priority faucets first**, then the rest in the existing tiebreak order (e.g. alphabetical).
2. **Counting-down faucets** after all ready ones, sorted purely by time remaining (ascending). `priority` has **no effect** on ordering here — a priority faucet with hours left should not jump above a non-priority faucet that's ready sooner.

Pseudocode:

```js
function compareFaucets(a, b) {
  const aReady = a.timeRemaining <= 0;
  const bReady = b.timeRemaining <= 0;

  if (aReady && bReady) {
    if (a.priority !== b.priority) return b.priority - a.priority; // priority first
    return a.name.localeCompare(b.name); // existing tiebreak
  }
  if (aReady !== bReady) return aReady ? -1 : 1; // ready ones before counting-down ones
  return a.timeRemaining - b.timeRemaining; // counting down, soonest first
}
```

## Notes

- No new dependencies, no schema migration tooling needed — just a default of `priority: false` for existing records that lack the field (treat missing key as `false`).
- Keep the star icon subtle/small so it doesn't clash with the row background gradient used to distinguish rows.
