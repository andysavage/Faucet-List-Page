#!/bin/bash
# run-analytics.sh
# Extracts faucetlist.org Apache logs from DirectAdmin tarballs and runs analytics parser.
# DirectAdmin rotates logs daily into ~/domains/faucetlist.org/logs/Mon-YYYY.tar.gz files.
# Run via cron: 5 * * * * /home/faucetlist/scripts/run-analytics.sh

LOGS_DIR="$HOME/domains/faucetlist.org/logs"
ANALYTICS_SCRIPT="$HOME/scripts/analytics.py"
DATA_DIR="$HOME/domains/faucetlist.org/public_html/data/analytics"
LIVE_LOG="$HOME/domains/faucetlist.org/logs/faucetlist.org.log"
TMP_LOG="/tmp/faucetlist-combined-access.log"

mkdir -p "$DATA_DIR"

# Extract access logs from all tarballs (including .tar.gz.N rotated files) and concatenate
> "$TMP_LOG"
for tarball in "$LOGS_DIR"/*.tar.gz "$LOGS_DIR"/*.tar.gz.*; do
    [ -f "$tarball" ] || continue
    tar -xzf "$tarball" --to-stdout faucetlist.org.log 2>/dev/null >> "$TMP_LOG"
done

# Also include today's live access log (not yet archived)
if [ -f "$LIVE_LOG" ]; then
    cat "$LIVE_LOG" >> "$TMP_LOG"
fi

if [ ! -s "$TMP_LOG" ]; then
    echo "$(date): No log data found in tarballs or live log" >&2
    exit 1
fi

python3 "$ANALYTICS_SCRIPT" "$TMP_LOG" -d "$DATA_DIR"

rm -f "$TMP_LOG"
