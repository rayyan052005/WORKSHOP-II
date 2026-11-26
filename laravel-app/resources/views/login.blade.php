<?php $error=$errors->first(); $oldEmail=old("username", ""); ?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | UniFood</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --forest: #234b35;
            --forest-dark: #1b3b29;
            --lime: #cbe896;
            --lime-soft: #ecf0e5;
            --ink: #16231a;
            --muted: #636f67;
            --bg: #faf9f5;
            --card: #ffffff;
            --line: #dfe4dd;
            --hint-bg: #f1f3ec;
            --hint-line: #e2e6dc;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Plus Jakarta Sans", Inter, "Segoe UI", Arial, sans-serif;
            color: var(--ink);
            background: var(--bg);
            -webkit-font-smoothing: antialiased;
        }
        button, input { font: inherit; }

        .page-wrap {
            min-height: 100vh;
            padding: 18px 22px;
            display: grid;
            grid-template-columns: minmax(440px, 46.5%) 1fr;
            gap: 24px;
        }

        /* Left Panel (Deep Forest Green) */
        .story-panel {
            position: relative;
            overflow: hidden;
            border-radius: 26px;
            padding: 42px 48px;
            color: #ffffff;
            background: var(--forest);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: calc(100vh - 36px);
        }

        .brand {
            position: relative;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 25px;
            font-weight: 800;
            letter-spacing: -0.7px;
            color: #ffffff;
            text-decoration: none;
        }
        .brand-mark {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 11px;
            background: var(--lime);
            color: var(--forest);
        }

        .story-copy {
            position: relative;
            z-index: 2;
            margin: auto 0;
            padding: 36px 0 28px;
            max-width: 520px;
        }
        .story-eyebrow {
            margin: 0 0 18px;
            color: var(--lime);
            font-size: 11.5px;
            font-weight: 800;
            letter-spacing: 2.1px;
            text-transform: uppercase;
        }
        .story-copy h1 {
            margin: 0 0 26px;
            font-size: clamp(42px, 4.4vw, 62px);
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -2.2px;
        }
        .story-copy h1 .highlight {
            display: block;
            color: var(--lime);
        }
        .story-copy p {
            margin: 0;
            font-size: 15.5px;
            line-height: 1.65;
            color: rgba(255, 255, 255, 0.86);
            max-width: 420px;
        }
        .story-copy p + p {
            margin-top: 4px;
        }

        /* Bottom Glass Card */
        .feature-box {
            position: relative;
            z-index: 2;
            max-width: 385px;
            border-radius: 18px;
            padding: 20px 22px;
            background: rgba(255, 255, 255, 0.055);
            border: 1px solid rgba(255, 255, 255, 0.13);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .salad-icon {
            width: 54px;
            height: 54px;
            flex: 0 0 54px;
            display: grid;
            place-items: center;
        }
        .feature-copy strong {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 5px;
            letter-spacing: -0.2px;
        }
        .feature-copy span {
            display: block;
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.72);
        }

        /* Decorative Rings */
        .rings {
            position: absolute;
            right: -180px;
            bottom: -180px;
            width: 520px;
            height: 520px;
            border-radius: 50%;
            border: 36px solid rgba(255, 255, 255, 0.03);
            pointer-events: none;
        }
        .rings::before {
            content: "";
            position: absolute;
            inset: 38px;
            border-radius: 50%;
            border: 32px solid rgba(255, 255, 255, 0.03);
        }
        .rings::after {
            content: "";
            position: absolute;
            inset: 108px;
            border-radius: 50%;
            border: 28px solid rgba(255, 255, 255, 0.03);
        }

        /* Right Form Panel */
        .form-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 36px 28px 20px;
        }

        .login-card {
            width: min(410px, 100%);
            margin: auto 0;
        }
        .welcome-pill {
            display: inline-block;
            margin-bottom: 16px;
            padding: 7px 13px;
            border-radius: 999px;
            background: var(--lime-soft);
            color: #355440;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }
        .login-card h2 {
            margin: 0 0 8px;
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -1.3px;
            color: var(--ink);
        }
        .subtitle {
            margin: 0 0 28px;
            font-size: 14.5px;
            color: var(--muted);
        }

        .error {
            margin-bottom: 18px;
            border: 1px solid #f3c2b8;
            border-radius: 12px;
            padding: 12px 14px;
            color: #9e2f1d;
            background: #fdf2ef;
            font-size: 13.5px;
            font-weight: 500;
        }

        .field {
            margin-bottom: 18px;
        }
        .field label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #253329;
        }
        .field input {
            width: 100%;
            height: 50px;
            border: 1px solid var(--line);
            border-radius: 11px;
            padding: 0 16px;
            outline: none;
            font-size: 14px;
            color: var(--ink);
            background: var(--card);
            transition: border-color .18s, box-shadow .18s;
        }
        .field input::placeholder {
            color: #9aa49d;
        }
        .field input:focus {
            border-color: var(--forest);
            box-shadow: 0 0 0 3px rgba(35, 75, 53, 0.12);
        }

        .password-row {
            position: relative;
        }
        .password-row input {
            padding-right: 76px;
        }
        .toggle-password {
            position: absolute;
            right: 8px;
            top: 7px;
            height: 36px;
            padding: 0 10px;
            border: 0;
            border-radius: 8px;
            color: var(--ink);
            background: transparent;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
        }
        .toggle-password:hover {
            background: #f2f4f0;
        }

        .demo-hint {
            margin: 20px 0;
            padding: 14px 16px;
            border-radius: 12px;
            background: var(--hint-bg);
            border: 1px solid var(--hint-line);
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12.5px;
            line-height: 1.55;
            color: #556259;
        }
        .demo-hint svg {
            flex: 0 0 16px;
            margin-top: 2px;
            color: #5d6b61;
        }
        .demo-hint strong {
            color: var(--ink);
            font-weight: 700;
        }

        .login-button {
            width: 100%;
            height: 50px;
            border: 0;
            border-radius: 11px;
            padding: 0 20px;
            color: #ffffff;
            background: var(--forest);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background .18s, transform .18s;
        }
        .login-button:hover {
            background: var(--forest-dark);
            transform: translateY(-1px);
        }
        .login-button .arrow {
            font-size: 15px;
            line-height: 1;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 22px 0;
            color: #8c968f;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }
        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e4e8e1;
        }

        .guest-button {
            width: 100%;
            height: 48px;
            border: 1px solid var(--line);
            border-radius: 11px;
            background: #ffffff;
            color: var(--ink);
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            transition: background .18s, border-color .18s;
        }
        .guest-button:hover {
            background: #f4f6f2;
            border-color: #cdd4ca;
        }

        .footer-note {
            margin-top: 28px;
            text-align: center;
            font-size: 12px;
            color: #88928b;
        }

        @media (max-width: 960px) {
            .page-wrap {
                grid-template-columns: 1fr;
                padding: 14px;
            }
            .story-panel {
                min-height: auto;
                padding: 32px 28px;
            }
            .story-copy {
                padding: 28px 0;
            }
            .form-panel {
                padding: 28px 14px;
            }
        }
    </style>
</head>
<body>
    <main class="page-wrap">
        <!-- Left Panel -->
        <section class="story-panel">
            <a class="brand" href="#">
                <span class="brand-mark" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.5c.7 4.6 2.9 6.8 7.5 7.5-4.6.7-6.8 2.9-7.5 7.5-.7-4.6-2.9-6.8-7.5-7.5 4.6-.7 6.8-2.9 7.5-7.5Z"/>
                    </svg>
                </span>
                Unifood
            </a>

            <div class="story-copy">
                <div class="story-eyebrow">YOUR CAMPUS. YOUR CRAVINGS.</div>
                <h1>
                    Good food.<br>
                    Less waiting.
                    <span class="highlight">More campus life.</span>
                </h1>
                <p>Your favorite campus meals, ready when you are.</p>
                <p>Make your next break a delicious one.</p>
            </div>

            <div class="feature-box">
                <div class="salad-icon" aria-hidden="true">
                    <svg width="52" height="52" viewBox="0 0 64 64" fill="none">
                        <!-- Bottom Shadow -->
                        <ellipse cx="32" cy="55" rx="18" ry="4" fill="rgba(0,0,0,0.22)"/>
                        <!-- Salad Greens -->
                        <circle cx="23" cy="26" r="9" fill="#5dbb47"/>
                        <circle cx="33" cy="23" r="10" fill="#76d156"/>
                        <circle cx="42" cy="27" r="8.5" fill="#5dbb47"/>
                        <!-- Tomatoes & Corn -->
                        <circle cx="25" cy="24" r="3.6" fill="#e84838"/>
                        <circle cx="38" cy="25" r="3.8" fill="#e84838"/>
                        <circle cx="31" cy="28" r="2.3" fill="#ffd84d"/>
                        <circle cx="35" cy="21" r="2.1" fill="#ffd84d"/>
                        <!-- Bowl -->
                        <path d="M12 30h40c0 13.5-8.5 22-20 22S12 43.5 12 30Z" fill="#c7bfd4"/>
                        <path d="M12 30h40c0 3.2-1.2 6.2-3.2 8.7H15.2C13.2 36.2 12 33.2 12 30Z" fill="#d9d2e5"/>
                        <rect x="23" y="50" width="18" height="3.5" rx="1.75" fill="#9e95ad"/>
                    </svg>
                </div>
                <div class="feature-copy">
                    <strong>A better lunch break starts here.</strong>
                    <span>Discover · Order ahead · Pickup & Delivery</span>
                </div>
            </div>

            <div class="rings" aria-hidden="true"></div>
        </section>

        <!-- Right Form Panel -->
        <section class="form-panel">
            <div class="login-card">
                <div class="welcome-pill">WELCOME TO YOUR CAMPUS TABLE</div>
                <h2>Welcome back.</h2>
                <p class="subtitle">Your next favorite meal is waiting for you.</p>

                <?php if ($error): ?>
                    <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <form method="post">
@csrf
                    <input type="hidden" name="action" value="login">
                    <div class="field">
                        <label for="username">Student email</label>
                        <input id="username" name="username" type="email" autocomplete="username"
                               placeholder="you@university.edu"
                               value="<?= htmlspecialchars($oldEmail, ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <div class="password-row">
                            <input id="password" name="password" type="password" autocomplete="current-password"
                                   placeholder="Enter your password"
                                   required>
                            <button class="toggle-password" id="toggle-btn" type="button" onclick="togglePassword(this)">Show</button>
                        </div>
                    </div>

                    <button class="login-button" style="margin-top:20px;" type="submit">
                        <span>Sign in</span>
                        <span class="arrow" aria-hidden="true">↗</span>
                    </button>
                </form>

                <div class="divider">OR EXPLORE FIRST</div>

                <form method="post">
@csrf
                    <input type="hidden" name="action" value="guest">
                    <button class="guest-button" type="submit">Browse food as a guest</button>
                </form>

                <div class="footer-note">UniFood campus dining</div>
            </div>
        </section>
    </main>

    <script>
        function togglePassword(button) {
            const input = document.getElementById('password');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            button.textContent = isPassword ? 'Hide' : 'Show';
        }
    </script>
</body>
</html>
