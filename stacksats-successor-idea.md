# Successor to StackSats

**Status:** Project idea  
**Inspired by:** [StackSats.how archive](https://web.archive.org/web/20240414005000/https://www.stacksats.how/#filter)

## Background

StackSats.how was a curated resource for earning small amounts of Bitcoin through legitimate websites and services. Unlike many similar sites, it was not simply a collection of referral links. It focused on useful services such as games, educational platforms, cashback, browser rewards, and Lightning-enabled applications.

Since StackSats disappeared, there does not appear to be a comparable resource.

## Project goal

Create a modern successor to StackSats that focuses on **quality rather than quantity**. Recommend services that have been personally tested or have a strong reputation in the Bitcoin community, rather than listing every site that offers Bitcoin rewards.

The guiding question is: **If you have one hour this week, what is the best use of your time to earn sats?** The directory should help people spend their time wisely, not maximize referral income. Say plainly when an opportunity is poor value, and explain why another one is worthwhile.

## Possible categories

- Bitcoin faucets
- Educational sites
- Games that pay Lightning rewards
- Cashback services
- Browsers and search engines
- Shopping rewards
- Writing and content creation
- Mobile apps
- Community reward platforms
- Promotions and temporary campaigns

## Editorial principles

- List legitimate services; avoid scams, cloud mining schemes, and “deposit first” offers.
- Make referral links optional and clearly identify them.
- Write honest reviews rather than promotional descriptions.
- Explain exactly how each service works.
- Give realistic earning expectations, such as sats per day or per hour.
- Include withdrawal limits, withdrawal methods, and Lightning support.
- Record country restrictions and the date each service was last tested.
- Keep inactive or non-paying services visible with a clear status instead of silently deleting their history.
- Help readers distinguish high-value opportunities from activities that are only worthwhile if they enjoy them.

Possible editorial recommendation labels:

- 🟢 **Excellent** — Worth doing regularly.
- 🟡 **Okay** — Fine if you already enjoy the activity.
- 🟠 **Marginal** — Very low earnings; mainly for enthusiasts.
- 🔴 **Avoid** — No longer paying, excessive ads, or unrealistic claims.

## Listing fields and ongoing status

Each listing could include:

- Overall rating and recommendation label
- Time required and difficulty
- Typical earnings
- Withdrawal method and minimum withdrawal
- Country restrictions
- Last-tested date
- Community comments or reports
- Current operational status
- A dated history of status changes

Possible operational statuses include **Working**, **Reduced payouts**, **Withdrawals delayed**, **Closed**, and **Avoid**. A historical log can show that a service had delayed withdrawals, recovered, and later closed, which tells readers more than a single current green or red indicator and helps them judge reliability over time.

## Curation and maintenance

The value of the project would come not just from its list, but from keeping the information current. A regular review process could use AI for repetitive checks while keeping editorial decisions with a human.

### Draft weekly review workflow

1. **Check listed services:** Visit each site, check that it loads and its SSL certificate is valid, and look for visible changes to withdrawal limits, login requirements, Lightning support, or other listing details.
2. **Monitor community reports:** Search sources such as Reddit, Nostr, Bitcointalk, and GitHub for recent reports. Flag claims such as “withdrawals have stopped,” “support isn’t replying,” or “still paying as of this week.” Treat these as signals to review, not automatically verified facts.
3. **Compare with the previous version:** Detect changes and prepare a report with evidence and confidence, rather than editing the published directory directly.
4. **Request human approval:** Present proposed changes for review. After approval, apply the updates.

For example, a weekly report might say:

- ✓ Bitcoin Miner — no changes detected.
- ⚠ Cointiply — reported withdrawal threshold increased from 50,000 to 75,000 coins; verify before updating.
- ⚠ Fountain — Lightning withdrawals appear temporarily disabled; verify before updating.
- ✗ XYZ Faucet — domain no longer resolves; recommend reviewing its status as Inactive.

The aim is to reduce several hours of manual checking to a short review of the meaningful changes. AI does not need to decide everything; it can do the tedious monitoring and propose changes for a person to assess.

## A bounded agent-assisted editor

This could also be a practical application of an agentic editor for wiki-style content. Rather than starting with a fully autonomous system, begin with a bounded workflow that can:

- Read webpages
- Compare findings with stored listing data
- Produce diffs
- Assign confidence and include supporting evidence
- Suggest edits
- Wait for approval

Specialized agents could eventually monitor site availability, community sentiment, and payout claims, with a human acting as the final editor. The system should report suggested changes, not publish them without approval.

## Relationship with FaucetList

The successor will live on **FaucetList.org** as a broader “Stack Sats” section; there is no plan to create another domain. FaucetList can continue to present its faucet listings clearly, while the new section covers other legitimate ways to earn Bitcoin.

A practical first step is to launch the section with around ten genuinely good recommendations and expand it as the listings are researched and maintained. This keeps the initial workload manageable and lets the new resource build on FaucetList's existing audience and infrastructure.

## Long-term vision

Become the most trusted directory of legitimate Bitcoin earning opportunities. Better curation, honest reviews, regular testing, maintenance history, and transparency should help people avoid wasting hours on low-quality offers and find worthwhile ways to stack sats.

The idea fits a broader preference for building genuinely useful resources rather than chasing affiliate revenue. If the directory becomes largely AI-maintained, the human role can focus on deciding what deserves to be recommended—the editorial judgment that adds the most value.

---

*Inspired by the now-defunct StackSats.how, with the aim of improving on it through better curation, honest reviews, regular testing, and transparency.*

## Source notes

This document combines the local project-idea Markdown file with the substantive additions on the [Nimno wiki page](https://nimno.net/sites/faucetlist-org/successor-to-stacksats/). Conversational acknowledgements and unrelated chat phrasing have been omitted; the ideas and proposals are retained above. The project direction has since been clarified: it will be part of FaucetList.org, with no separate domain.

Original project conversation references:

- https://chatgpt.com/c/6a4198e5-1aec-83eb-862e-b78f58a918da
- https://chatgpt.com/share/6a419ccb-a288-83eb-91b8-94f9c443b20b
