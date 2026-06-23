#!/bin/bash

################################################################################
# Deploy faucetlist.org to DirectAdmin server
#
# PURPOSE:
#   Safely syncs local development changes to the production DirectAdmin server
#   while protecting server-created user data from being overwritten.
#
# USAGE:
#   ./deploy-to-directadmin.sh
#
# WHAT IT DOES:
#   1. Syncs all site files (HTML, JavaScript, CSS, PHP API) to the server
#   2. Data files (ads, user data) are managed on server only via admin interface
#
# DEPLOYMENT TARGETS:
#   Site files:   /home/faucetlist/domains/faucetlist.org/public_html/
#   Remote host:  faucetlist-directadmin (faucetlist DirectAdmin account)
#
# DATA MANAGEMENT:
#   Data files (ads, user data) are NOT synced from local.
#   They are managed entirely on the server via the admin interface.
#
# IMPORTANT NOTES:
#   - Site files use --delete flag (removes deleted local files from server)
#   - Data files sync WITHOUT --delete (preserves server-created files)
#   - Uses SSH key authentication with faucetlist_key (configured in ~/.ssh/config)
#   - Requires rsync installed on both local and remote systems
#
# VERIFICATION:
#   After deployment, verify changes at: https://faucetlist.org
#
################################################################################

# set -e removed - handle errors manually to preserve interactive prompts

# Configuration
REMOTE_HOST="faucetlist-directadmin"
REMOTE_LOGS_PATH="/home/faucetlist/logs"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log() { echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"; }
success() { echo -e "${GREEN}✅ $1${NC}"; }
warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
error_exit() { echo -e "${RED}❌ $1${NC}"; exit 1; }

# Auto-commit pending changes to git
auto_commit_changes() {
    log "Checking for pending git changes..."
    
    if [[ -n $(git status --porcelain) ]]; then
        local change_count=$(git status --porcelain | wc -l)
        log "Found $change_count pending changes"
        
        echo ""
        read -p "Enter commit message (or press Enter for auto-generated): " -r custom_msg
        echo ""
        
        git add -A
        
        if [[ -n "$custom_msg" ]]; then
            local commit_msg="$custom_msg"
        else
            local commit_msg="faucetlist deploy - $(date +'%Y-%m-%d %H:%M:%S')"
        fi
        
        git commit -m "$commit_msg"
        success "Committed $change_count changes: $commit_msg"
        
        log "Pushing to GitHub..."
        if git push origin main 2>/dev/null || git push origin master 2>/dev/null; then
            success "Pushed to GitHub"
        else
            warning "Failed to push to GitHub (continuing with deployment)"
        fi
    else
        log "No pending changes to commit"
    fi
}

# Safety check: preview deletions before they happen
preview_deletions() {
    log "Checking for files that would be DELETED on server..."
    
    local deletions
    deletions=$(rsync -avzn --delete --itemize-changes \
        -e "ssh" \
        --exclude='.git' \
        --exclude='node_modules' \
        --exclude='.DS_Store' \
        --exclude='media/*' \
        --exclude='data/analytics/' \
        "$LOCAL_SITE_DIR/" \
        "faucetlist-directadmin:$REMOTE_SITE_PATH/" 2>/dev/null | grep '^\*deleting' || true)
    
    if [[ -n "$deletions" ]]; then
        warning "The following files will be DELETED from the server:"
        echo "$deletions" | sed 's/^\*deleting   /  - /'
        echo ""
        read -rp "Continue with these deletions? (y/N): " -n 1
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            error_exit "Deployment cancelled to prevent deletions."
        fi
    else
        success "No files will be deleted."
    fi
}
REMOTE_SITE_PATH="/home/faucetlist/domains/faucetlist.org/public_html"

LOCAL_SITE_DIR="./site"

echo "🚀 Deploying faucetlist.org to DirectAdmin server..."
echo "   Remote: $REMOTE_HOST"
echo ""

# Trap errors and pause before exit
trap 'echo ""; echo -e "${RED}❌ Deployment failed or interrupted${NC}"; read -rp "Press Enter to close..."; exit 1' ERR INT TERM

# Auto-commit any pending changes
auto_commit_changes
if [ $? -ne 0 ]; then
    error_exit "Auto-commit failed"
fi

# Verify local directories exist
if [ ! -d "$LOCAL_SITE_DIR" ]; then
    error_exit "Local site directory not found: $LOCAL_SITE_DIR"
fi

# Preview any deletions before proceeding
preview_deletions

echo "📁 Syncing site files (html, js, css, api)..."
rsync -avz --delete -e "ssh" \
    --exclude='.git' \
    --exclude='node_modules' \
    --exclude='.DS_Store' \
    --exclude='media/*' \
    --exclude='data/analytics/' \
    "$LOCAL_SITE_DIR/" \
    "faucetlist-directadmin:$REMOTE_SITE_PATH/"

if [ $? -ne 0 ]; then
    error_exit "Site sync failed"
fi

echo ""
echo "� Ensuring logs directory exists on server..."
ssh faucetlist-directadmin \
    "mkdir -p $REMOTE_LOGS_PATH" || { warning "Could not create logs dir (may already exist)"; }

echo ""
echo "✅ Deployment complete!"
echo ""
echo "📋 Summary:"
echo "   ✓ Site files synced to: $REMOTE_SITE_PATH"
echo "   ✓ Data files managed on server (not synced)"
echo ""
echo "🔍 Verify deployment by visiting: https://faucetlist.org"
echo ""
read -rp "Press Enter to close..."
