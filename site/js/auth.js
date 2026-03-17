// DirectSponsor Authentication System
// shared across ClickForCharity, FaucetList, etc.

class AuthSystem {
    constructor() {
        this.authUrl = 'https://auth.directsponsor.org';
        this.sessionKey = 'directsponsor_session';
        this.sessionDuration = 30 * 24 * 60 * 60 * 1000;
    }

    isLoggedIn() {
        try {
            const sessionData = localStorage.getItem(this.sessionKey);
            if (!sessionData) return false;
            const data = JSON.parse(sessionData);
            if (data.expires && Date.now() > data.expires) {
                this.logout();
                return false;
            }
            return true;
        } catch (error) {
            return false;
        }
    }

    getSession() {
        try {
            const sessionData = localStorage.getItem(this.sessionKey);
            if (!sessionData) return null;
            const data = JSON.parse(sessionData);
            if (data.expires && Date.now() > data.expires) {
                this.logout();
                return null;
            }
            return data;
        } catch (error) {
            return null;
        }
    }

    login() {
        const redirectUri = encodeURIComponent(window.location.origin + window.location.pathname);
        window.location.href = `${this.authUrl}/jwt-login.php?redirect_uri=${redirectUri}`;
    }

    logout() {
        localStorage.removeItem(this.sessionKey);
        localStorage.removeItem('username');
        localStorage.removeItem('user_id');
        localStorage.removeItem('combined_user_id');
        window.location.reload();
    }

    parseJWT(token) {
        try {
            const base64Url = token.split('.')[1];
            const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            const jsonPayload = decodeURIComponent(atob(base64).split('').map(function (c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));
            return JSON.parse(jsonPayload);
        } catch (error) {
            return null;
        }
    }

    handleAuthCallback() {
        const urlParams = new URLSearchParams(window.location.search);
        const jwtToken = urlParams.get('jwt');
        const error = urlParams.get('error');
        const errorDesc = urlParams.get('error_description');
        
        // Log auth errors for display
        if (error) {
            const errorMsg = `Auth Error: ${error}${errorDesc ? ' - ' + errorDesc : ''}`;
            localStorage.setItem('auth_error', errorMsg);
            const cleanUrl = window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
            return;
        }
        
        if (jwtToken) {
            const payload = this.parseJWT(jwtToken);
            if (payload && payload.sub && payload.username) {
                const userId = payload.sub;
                const username = payload.username;
                const combinedUserId = `${userId}-${username}`;
                const sessionData = {
                    user_id: userId,
                    username: username,
                    combined_user_id: combinedUserId,
                    expires: Date.now() + this.sessionDuration,
                    created: Date.now()
                };
                localStorage.setItem(this.sessionKey, JSON.stringify(sessionData));
                localStorage.setItem('user_id', userId);
                localStorage.setItem('username', username);
                localStorage.setItem('combined_user_id', combinedUserId);
                // Flag that we just logged in (for guest data merge check)
                localStorage.setItem('just_logged_in', 'true');
                // Clear any previous auth errors
                localStorage.removeItem('auth_error');
                const cleanUrl = window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
                // Small delay to ensure auth UI updates before reload
                setTimeout(() => window.location.reload(), 100);
            } else {
                localStorage.setItem('auth_error', 'Invalid JWT payload');
                const cleanUrl = window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
            }
        }
    }
}

const auth = new AuthSystem();
