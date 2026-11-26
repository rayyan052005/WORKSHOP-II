<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$oldEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';

    if ($action === 'guest') {
        session_regenerate_id(true);
        $_SESSION['user_id'] = 'guest';
        $_SESSION['username'] = 'guest';
        $_SESSION['full_name'] = 'Campus Guest';
        $_SESSION['role'] = 'Guest';
        header('Location: welcome.php');
        exit;
    }

    $oldEmail = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($oldEmail === '' || $password === '') {
        $error = 'Please enter student email and password.';
    } elseif (!filter_var($oldEmail, FILTER_VALIDATE_EMAIL) && strpos($oldEmail, '@') !== false) {
        $error = 'Please enter a valid student email format.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $loggedIn = false;

        // Try checking PostgreSQL database if available
        try {
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                getenv('DB_HOST') ?: '127.0.0.1',
                getenv('DB_PORT') ?: '5432',
                getenv('DB_DATABASE') ?: 'unifood'
            );

            $pdo = new PDO(
                $dsn,
                getenv('DB_USERNAME') ?: 'postgres',
                getenv('DB_PASSWORD') ?: '123',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 2,
                ]
            );

            // 1. Check in dedicated 'sellers' table first
            try {
                $stmtSeller = $pdo->prepare(
                    'SELECT id, stall_name, owner_name, email, password_hash, campus_location
                     FROM sellers
                     WHERE lower(email) = lower(:username)
                        OR lower(split_part(email, \'@\', 1)) = lower(:username)
                        OR lower(stall_name) LIKE lower(:stall)
                     LIMIT 1'
                );
                $stmtSeller->execute([
                    'username' => $oldEmail,
                    'stall' => '%' . $oldEmail . '%'
                ]);
                $sellerUser = $stmtSeller->fetch();

                if ($sellerUser && password_verify($password, $sellerUser['password_hash'])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = 'seller_' . $sellerUser['id'];
                    $_SESSION['seller_id'] = (int)$sellerUser['id'];
                    $_SESSION['username'] = $sellerUser['email'];
                    $_SESSION['full_name'] = $sellerUser['owner_name'] . ' (' . $sellerUser['stall_name'] . ')';
                    $_SESSION['stall_name'] = $sellerUser['stall_name'];
                    $_SESSION['campus_location'] = $sellerUser['campus_location'] ?? 'Engineering Campus';
                    $_SESSION['role'] = 'seller';
                    $loggedIn = true;
                }
            } catch (Throwable $e) {
                // Table sellers may not exist yet, continue
            }

            // 2. Check in 'users' table
            if (!$loggedIn) {
                $stmt = $pdo->prepare(
                    'SELECT id, username, email, password_hash, full_name, role
                     FROM users
                     WHERE lower(username) = lower(:username) OR lower(email) = lower(:username)
                     LIMIT 1'
                );
                $stmt->execute(['username' => $oldEmail]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password_hash'])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'] ?? $user['username'];
                    $userRole = strtolower($user['role'] ?? '');
                    if ($userRole === 'seller' || $userRole === 'vendor') {
                        $_SESSION['role'] = 'seller';
                        // Look up seller id
                        $stmtS = $pdo->prepare('SELECT id, stall_name, campus_location FROM sellers WHERE lower(email) = lower(:e) OR lower(stall_name) LIKE lower(:s) LIMIT 1');
                        $stmtS->execute(['e' => $user['email'] ?? '', 's' => '%' . ($user['username'] ?? '') . '%']);
                        $sRow = $stmtS->fetch();
                        if ($sRow) {
                            $_SESSION['seller_id'] = (int)$sRow['id'];
                            $_SESSION['stall_name'] = $sRow['stall_name'];
                            $_SESSION['campus_location'] = $sRow['campus_location'];
                        } else {
                            $_SESSION['seller_id'] = 1;
                            $_SESSION['stall_name'] = 'Cafe Abang Mirul';
                            $_SESSION['campus_location'] = 'Main Campus';
                        }
                    } elseif ($userRole === 'rider' || $userRole === 'runner') {
                        $_SESSION['role'] = 'rider';
                        $stmtR = $pdo->prepare('SELECT id, rider_name, phone, vehicle_type, vehicle_plate FROM riders WHERE user_id = :uid OR lower(email) = lower(:e) LIMIT 1');
                        $stmtR->execute(['uid' => $user['id'], 'e' => $user['email'] ?? '']);
                        $rRow = $stmtR->fetch();
                        if ($rRow) {
                            $_SESSION['rider_id'] = (int)$rRow['id'];
                            $_SESSION['full_name'] = $rRow['rider_name'] . ' (Campus Runner)';
                            $_SESSION['vehicle'] = $rRow['vehicle_type'] . ' · ' . $rRow['vehicle_plate'];
                            $_SESSION['phone'] = $rRow['phone'];
                        } else {
                            $_SESSION['rider_id'] = (int)$user['id'];
                            $_SESSION['full_name'] = $user['full_name'] ?? 'Muhammad Alif (UTeM Runner)';
                            $_SESSION['vehicle'] = 'Motorcycle (Honda EX5) · MCL 4821';
                        }
                    } else {
                        $_SESSION['role'] = 'Student';
                    }
                    $loggedIn = true;
                }
            }
        } catch (Throwable $exception) {
            // Fall back to demo mode if database is not active
        }

        // Demo Mode: Allow login with any valid email & password >= 6 characters
        if (!$loggedIn) {
            $namePart = explode('@', $oldEmail)[0];
            $cleanName = ucwords(str_replace(['.', '_', '-'], ' ', $namePart));
            $isRiderLogin = (
                stripos($oldEmail, 'rider') !== false ||
                stripos($oldEmail, 'runner') !== false
            );
            $isSellerLogin = (
                stripos($oldEmail, 'seller') !== false ||
                stripos($oldEmail, 'vendor') !== false ||
                stripos($oldEmail, 'albaek') !== false ||
                stripos($oldEmail, 'mirul') !== false
            );

            session_regenerate_id(true);
            if ($isRiderLogin) {
                $_SESSION['user_id'] = 'rider_1';
                $_SESSION['rider_id'] = 1;
                $_SESSION['username'] = $namePart;
                $_SESSION['full_name'] = 'Muhammad Alif (Campus Runner)';
                $_SESSION['vehicle'] = 'Motorcycle (Honda EX5) · MCL 4821';
                $_SESSION['role'] = 'rider';
            } elseif ($isSellerLogin) {
                $isAlbaek = stripos($oldEmail, 'albaek') !== false;
                $_SESSION['user_id'] = 'seller_' . md5($oldEmail);
                $_SESSION['seller_id'] = $isAlbaek ? 2 : 1;
                $_SESSION['username'] = $namePart;
                $_SESSION['full_name'] = $cleanName . ' (Stall Owner)';
                $_SESSION['stall_name'] = $isAlbaek ? 'AL Baek Cafe' : 'Cafe Abang Mirul';
                $_SESSION['campus_location'] = $isAlbaek ? 'Engineering Campus' : 'Main Campus';
                $_SESSION['role'] = 'seller';
            } else {
                $_SESSION['user_id'] = 'demo_' . md5($oldEmail);
                $_SESSION['username'] = $namePart;
                $_SESSION['full_name'] = $cleanName ?: 'Campus Student';
                $_SESSION['role'] = 'Student';
            }
            $loggedIn = true;
        }

        if ($loggedIn) {
            header('Location: welcome.php');
            exit;
        }
    }
}
?>
<!doctype html>
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

                <!-- Quick Role Selector Chips -->
                <div style="margin-bottom:18px;">
                    <span style="display:block; font-size:11px; font-weight:800; color:#68786a; letter-spacing:0.5px; text-transform:uppercase; margin-bottom:8px;">Quick Demo Accounts:</span>
                    <div style="display:flex; flex-wrap:wrap; gap:6px;">
                        <button type="button" onclick="fillDemo('student')" style="padding:6px 11px; font-size:12px; font-weight:700; border-radius:999px; border:1px solid #d4ddd1; background:#ffffff; color:#233a27; cursor:pointer;">
                            🎓 Student
                        </button>
                        <button type="button" onclick="fillDemo('vendor')" style="padding:6px 11px; font-size:12px; font-weight:700; border-radius:999px; border:1px solid #d4ddd1; background:#ffffff; color:#233a27; cursor:pointer;">
                            🏪 Vendor (AL Baek)
                        </button>
                        <button type="button" onclick="fillDemo('rider')" style="padding:6px 11px; font-size:12px; font-weight:700; border-radius:999px; border:1px solid #cbe896; background:#e8f4e6; color:#1e3d29; cursor:pointer;">
                            🛵 Rider (Runner)
                        </button>
                    </div>
                </div>

                <form method="post">
                    <input type="hidden" name="action" value="login">
                    <div class="field">
                        <label for="username">Email / Account</label>
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
                        <span>Sign In ↗</span>
                        <span class="arrow" aria-hidden="true">↗</span>
                    </button>
                </form>

                <div class="divider">OR EXPLORE FIRST</div>

                <form method="post">
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

        function fillDemo(type) {
            const userField = document.getElementById('username');
            const passField = document.getElementById('password');
            if (type === 'rider') {
                userField.value = 'rider@unifood.edu';
                passField.value = 'rider123';
            } else if (type === 'vendor') {
                userField.value = 'albaek@unifood.edu';
                passField.value = 'albaek123';
            } else {
                userField.value = 'student@university.edu';
                passField.value = '123';
            }
        }
    </script>
</body>
</html>
