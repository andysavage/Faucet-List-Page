# Faucet List with Timers

[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/andysavage/Faucet-List-Page)

**Live site: [faucetlist.org](https://directsponsor.github.io/Faucet-List-Page/)**

A cryptocurrency faucet management system that helps you track and claim from crypto earning sites. Currently a standalone web application with plans for a Chrome extension.

## Current Features (Phase 1 - Website)

*   **Add and Remove Faucets:** Easily add your favorite faucets with their name, URL, and a claim timer in minutes.
*   **Automatic Timers:** Each faucet has a countdown timer that shows when it's ready to be claimed again. Timers use real-time calculations and remain accurate even in background tabs or when the browser is minimized.
*   **Guest Mode:** Your faucet list is saved in your browser's `localStorage`, so it persists between sessions on the same device and browser.
*   **Optional Account Sync:** Sign in to sync your faucet list across devices and browsers.
*   **Mobile Responsive:** Optimized for small screens - URL column hides on mobile, compact timer display.
*   **Client-Side:** No server or database required. Just open the `index.html` file in a browser.

## Roadmap

### Phase 1: Website (Current)
- ✅ LocalStorage for guest users
- ✅ Account system with cloud sync via auth.directsponsor.org
- 🔄 Advert integration (pending)
- Chrome extension (future)

### Phase 2: Chrome Extension (Future)
- ~~Browser notifications when faucets are ready~~ ✅ **Now built into the website**
- Small popup showing ready sites
- Works standalone or syncs with web account
- Easy on/off switch for notifications
- Export/backup to local drive

### Target Users
- **Website Members:** Sync, analytics, community features
- **Extension Users:** Privacy-focused, no signup required
- **Both paths supported:** Members and privacy-focused users

## How to Use

1.  **Clone the repository or download the `index.html` file.**
2.  **Open `index.html` in your web browser.**
3.  **Add your faucets using the form.**

Your list will be saved automatically and will be there when you reopen the page.

## Hosting on GitHub Pages

1.  **Fork this repository.**
2.  **Go to the "Settings" tab of your forked repository.**
3.  **Navigate to the "Pages" section.**
4.  **Under "Source," select your `main` (or `master`) branch and click "Save."**

Your site will be published at `https://<your-username>.github.io/<repository-name>/`.

**Live demo:** https://directsponsor.github.io/Faucet-List-Page/
