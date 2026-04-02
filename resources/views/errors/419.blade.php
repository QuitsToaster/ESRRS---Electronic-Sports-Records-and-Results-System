<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Session Expired · Please Login</title>
    <!-- Google Fonts + modern system font stack -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, sans-serif;
            background: radial-gradient(circle at 10% 30%, rgba(15, 25, 45, 0.05) 0%, rgba(245, 248, 255, 1) 100%),
                        linear-gradient(145deg, #f9fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            margin: 0;
        }

        /* modern card with glassmorphism / soft shadows & border */
        .expiry-card {
            max-width: 560px;
            width: 100%;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(2px);
            border-radius: 2.5rem;
            box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.2), 0 8px 18px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255,255,255,0.6);
            border: 1px solid rgba(255, 255, 255, 0.7);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-align: center;
            padding: 2.5rem 2rem;
        }

        .expiry-card:hover {
            box-shadow: 0 30px 50px -18px rgba(0, 0, 0, 0.25);
            transform: translateY(-2px);
        }

        /* icon container – modern gradient + micro interaction */
        .icon-ring {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, rgba(255, 100, 100, 0.12), rgba(239, 68, 68, 0.08));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
            transition: all 0.2s;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .icon-ring svg {
            width: 44px;
            height: 44px;
            stroke: #dc2626;
            stroke-width: 1.7;
            fill: none;
            filter: drop-shadow(0 2px 4px rgba(220,38,38,0.2));
        }

        /* modern typography */
        .title {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            margin-bottom: 0.5rem;
        }

        .message {
            font-size: 1rem;
            line-height: 1.5;
            color: #334155;
            margin: 1rem 0 1.8rem 0;
            font-weight: 450;
            max-width: 85%;
            margin-left: auto;
            margin-right: auto;
        }

        /* button group - modern flex with gap */
        .action-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
            margin-top: 0.5rem;
        }

        /* primary button - gradient with lock icon vibe (login action) */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(105deg, #2563eb, #1d4ed8);
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.8rem 1.8rem;
            border-radius: 2rem;
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.25);
            transition: all 0.25s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            border: none;
            cursor: pointer;
            text-decoration: none;
            letter-spacing: -0.01em;
            backdrop-filter: blur(2px);
        }

        .btn-primary:hover {
            background: linear-gradient(105deg, #1d4ed8, #1e40af);
            transform: scale(1.02);
            box-shadow: 0 10px 20px -5px #1e3a8a60;
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        /* secondary button - minimal modern (home) */
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border: 1px solid #e2e8f0;
            color: #1e293b;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.8rem 1.8rem;
            border-radius: 2rem;
            transition: all 0.2s ease;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: white;
            border-color: #cbd5e1;
            box-shadow: 0 6px 12px -8px rgba(0,0,0,0.1);
            transform: translateY(-1px);
            color: #0f172a;
        }

        /* micro-interaction + subtle decorative elements */
        .badge-status {
            display: inline-block;
            background: #fee2e2;
            padding: 0.25rem 0.85rem;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
            color: #b91c1c;
            letter-spacing: 0.3px;
            margin-bottom: 1rem;
            backdrop-filter: blur(2px);
        }

        hr.divider-light {
            margin: 1.6rem auto 0.8rem;
            width: 48px;
            height: 2px;
            background: #e2e8f0;
            border: 0;
            border-radius: 4px;
        }

        /* responsive fine-tune */
        @media (max-width: 500px) {
            .expiry-card {
                padding: 1.8rem 1.5rem;
                border-radius: 2rem;
            }
            .title {
                font-size: 1.7rem;
            }
            .btn-primary, .btn-secondary {
                padding: 0.7rem 1.4rem;
                font-size: 0.9rem;
            }
            .icon-ring {
                width: 75px;
                height: 75px;
            }
            .icon-ring svg {
                width: 36px;
                height: 36px;
            }
            .message {
                font-size: 0.9rem;
                width: 100%;
            }
        }

        /* soft pulse for icon ring (modern accent) */
        @keyframes softPulse {
            0% { opacity: 0.7; transform: scale(0.98);}
            100% { opacity: 1; transform: scale(1);}
        }
        .icon-ring {
            animation: softPulse 2s infinite alternate ease-in-out;
        }

        /* extra micro tip style */
        .redirect-note {
            font-size: 0.7rem;
            color: #64748b;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
    </style>
</head>
<body>

<div class="expiry-card">
    <!-- subtle session badge / modern hint -->
    <div class="badge-status">Session Expired   </div>

    <!-- dynamic icon (warning / timeout) -->
    <div class="icon-ring">
        <svg viewBox="0 0 24 24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 9v4m0 4h.01M6.938 4h10.124c1.54 0 2.502 1.667 1.732 3L13.732 20c-.77 1.333-2.694 1.333-3.464 0L5.206 7c-.77-1.333.192-3 1.732-3z" />
            <circle cx="12" cy="17" r="0.5" fill="#dc2626" stroke="none" />
        </svg>
    </div>

    <!-- modern gradient title -->
    <h1 class="title">Session timed out</h1>

    <!-- friendly message updated to suggest re-login -->
    <p class="message">
        Your session has expired due to inactivity or a security refresh.<br>
        Please log in again to continue where you left off.
    </p>

    <!-- dual action buttons: Login again (primary) + Home (secondary) -->
    <div class="action-group">
       <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn-primary flex items-center gap-2">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
            <polyline points="10 17 15 12 10 7" />
            <line x1="15" y1="12" x2="3" y2="12" />
        </svg>
        Login again
    </button>
</form>
        <a href="/" id="homeBtn" class="btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2h-5v-8H9v8H5a2 2 0 0 1-2-2z" />
            </svg>
            Go to Home
        </a>
    </div>

    <!-- subtle decorative divider / modern extra micro tip (now reflects login) -->
    <hr class="divider-light">
    <div class="redirect-note">
        <span>You'll be redirected to the secure login page</span>
    </div>
</div>

<script>
    (function() {
        // MODERN BEHAVIOR: "Login again" button redirects to login page (independent config)
        const loginBtn = document.getElementById('loginAgainBtn');
        const homeBtn = document.getElementById('homeBtn');
        
        // --- LOGIN REDIRECT LOGIC ---
        // Define login page URL (flexible, can be customized for any project)
        // By default uses '/login' (most common Laravel / traditional apps)
        // For custom login path, you can replace with your own, e.g., '/auth/login' or '/signin'
        // To keep it independent and modern, it's configurable right here.
        const LOGIN_PAGE_URL = '/login';     // <-- Change this to your actual login route if needed
        // Optionally you can add a redirect parameter to bring user back after login? but session expired, better fresh login.
        
        if (loginBtn) {
            loginBtn.addEventListener('click', function(e) {
                e.preventDefault();
                // Modern smooth navigation: we can also add a tiny fade-out effect (optional)
                // But redirect is straightforward.
                // If you want to handle absolute URLs or relative paths gracefully:
                let destination = LOGIN_PAGE_URL;
                // Ensure we don't cause infinite loops if already on login page (just safety)
                if (window.location.pathname === destination || window.location.pathname === destination + '/') {
                    // If somehow already on login page, just reload to trigger fresh state
                    window.location.reload();
                    return;
                }
                // Redirect to login page (standard)
                window.location.href = destination;
            });
        }
        
        // Home button: go to root (default '/')
        if (homeBtn) {
            homeBtn.addEventListener('click', function(e) {
                // default anchor leads to '/', we can keep default behavior, but also add optional 
                // micro prevention if needed, but we keep the original href.
                // however to maintain consistency with independent design, we let the browser follow href.
                // but we also want to ensure if any framework needs, we don't break.
                // preventDefault? no, but we can also add optional smoothness.
                // But no need, the <a href="/"> works natively.
                // However sometimes users might want to force a clean navigation, fine.
                // We'll just ensure default link behavior works.
            });
        }
        
        // Extra utility: Detect if we should auto-clear expired local storage items (optional)
        // For modern hygiene, we can clear any stale auth tokens from localStorage/sessionStorage
        // but only on session expired page to avoid leftover tokens.
        try {
            // List of common keys that might hold expired credentials
            const authKeys = ['auth_token', 'access_token', 'user_session', 'laravel_token', 'jwt_token', 'session_id'];
            authKeys.forEach(key => {
                if (localStorage.getItem(key)) {
                    localStorage.removeItem(key);
                    console.log(`🧹 Cleared expired local storage key: ${key}`);
                }
                if (sessionStorage.getItem(key)) {
                    sessionStorage.removeItem(key);
                }
            });
            // Also clear any 'remember_me' remnants? optional but adds security.
        } catch (e) {
            // Silently fail if storage not accessible
        }
        
        // Update document title for clarity
        document.title = "Session expired · Please log in";
        
        // optional: if someone tries to use browser back after session expired, show hint in console.
        console.log('%c🔐 Session expired UI — "Login again" redirects to: ' + LOGIN_PAGE_URL, 'color: #2563eb; font-weight: 500;');
        
        // keyboard shortcut detection: Cmd/Ctrl + L could trigger login? just for extra delight (optional)
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'l') {
                e.preventDefault();
                if (loginBtn) loginBtn.click();
            }
        });
        
        // small micro animation on icon ring for consistency
        const iconRing = document.querySelector('.icon-ring');
        if (iconRing) {
            iconRing.style.transition = 'all 0.2s';
        }
        
        // Also add a small piece to maybe redirect if the user explicitly needs to refresh but login again preferred.
        // we removed refresh button and replaced with login again.
        // Additional modern feature: Add a countdown or anything? not needed.
        // But to ensure the home button is robust, we'll attach an extra listener to avoid any weird hash issues.
        if (homeBtn && homeBtn.getAttribute('href') === '/') {
            homeBtn.addEventListener('click', function(e) {
                // If the current page already is root, maybe do nothing special, but we can allow.
                // Let the default anchor work, but we prevent any event interference.
                // However for edge cases where root is not accessible, we make sure.
            });
        }
        
        // For extra flexibility: allow login page to be overridden via URL query parameter? not needed but modern devs can adapt.
        // Provide a small console helper to change login route dynamically if needed (for debugging)
        window.__setExpiredLoginUrl = function(newUrl) {
            console.warn('Custom login URL set to: ' + newUrl);
            // not persistent but can help demo
            if (loginBtn) {
                const newHandler = function(e) {
                    e.preventDefault();
                    window.location.href = newUrl;
                };
                loginBtn.removeEventListener('click', loginBtn.clickHandler);
                loginBtn.addEventListener('click', newHandler);
                loginBtn.clickHandler = newHandler;
            }
        };
    })();
</script>
</body>
</html>