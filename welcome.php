<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$fullName = $_SESSION['full_name'] ?? 'Campus student';
$firstName = trim(explode(' ', $fullName)[0]);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to UniFood...</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --forest-bg-light: #2d6641;
            --forest-bg-mid: #1e462c;
            --forest-bg-dark: #112a1a;
            --uni-green: #183a24;
            --scooter-green: #3cd02b;
            --scooter-light: #68e852;
            --food-orange: #f37021;
            --lime-accent: #cbe896;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Plus Jakarta Sans", Inter, -apple-system, sans-serif;
            background: radial-gradient(circle at 50% 42%, var(--forest-bg-light) 0%, var(--forest-bg-mid) 52%, var(--forest-bg-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            overflow: hidden;
            position: relative;
            transition: opacity 0.45s cubic-bezier(0.4, 0, 0.2, 1), transform 0.45s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Seamless exit transition state */
        body.page-leaving {
            opacity: 0;
            transform: scale(1.05);
            filter: blur(5px);
        }

        /* Ambient glowing orbs & rings on the green background */
        .ambient-glow {
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(203, 232, 150, 0.22) 0%, rgba(60, 208, 43, 0.08) 45%, transparent 72%);
            filter: blur(24px);
            pointer-events: none;
            animation: auraBreath 2.6s ease-in-out infinite alternate;
        }

        @keyframes auraBreath {
            0% { transform: scale(0.92); opacity: 0.65; }
            100% { transform: scale(1.12); opacity: 1; }
        }

        .ambient-ring {
            position: absolute;
            border-radius: 50%;
            border: 1.5px solid rgba(203, 232, 150, 0.12);
            pointer-events: none;
            animation: pulseRing 3s ease-in-out infinite alternate;
        }

        .ring-1 { width: 440px; height: 440px; }
        .ring-2 { width: 620px; height: 620px; border-style: dashed; border-color: rgba(203, 232, 150, 0.15); animation-delay: 0.4s; }
        .ring-3 { width: 820px; height: 820px; animation-delay: 0.8s; }

        @keyframes pulseRing {
            0% { transform: scale(0.96) rotate(0deg); opacity: 0.35; }
            100% { transform: scale(1.04) rotate(5deg); opacity: 0.8; }
        }

        /* Main Splash Wrapper */
        .splash-wrapper {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px;
            animation: cardEntrance 0.75s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes cardEntrance {
            0% { opacity: 0; transform: translateY(24px) scale(0.9); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Crisp White Logo Showcase Card so the dark-green & orange logo pops 100% */
        .logo-showcase-card {
            background: #ffffff;
            border-radius: 40px;
            padding: 22px 38px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow:
                0 28px 65px rgba(7, 20, 12, 0.48),
                0 0 0 8px rgba(255, 255, 255, 0.12),
                0 0 48px rgba(203, 232, 150, 0.22);
            position: relative;
            overflow: hidden;
        }

        /* Subtle top highlight bar inside white card */
        .logo-showcase-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 20%;
            right: 20%;
            height: 4px;
            border-radius: 0 0 99px 99px;
            background: linear-gradient(90deg, var(--scooter-green), var(--food-orange));
            opacity: 0.85;
        }

        /* Mascot Stage */
        .mascot-stage {
            position: relative;
            width: 290px;
            height: 242px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mascot-svg {
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        /* --- MASCOT ANIMATIONS --- */

        /* Scooter & Owl riding entrance + continuous road bounce */
        .rider-group {
            transform-origin: 165px 190px;
            animation:
                rideIn 0.8s cubic-bezier(0.22, 1.28, 0.36, 1) forwards,
                scooterBounce 0.4s ease-in-out infinite alternate;
        }

        @keyframes rideIn {
            0% { transform: translateX(-85px) rotate(-6deg) scale(0.9); opacity: 0; }
            70% { transform: translateX(7px) rotate(2deg) scale(1.02); opacity: 1; }
            100% { transform: translateX(0) rotate(0deg) scale(1); opacity: 1; }
        }

        @keyframes scooterBounce {
            0% { transform: translateY(0px) rotate(-0.8deg); }
            100% { transform: translateY(-6px) rotate(1.3deg); }
        }

        /* Owl head cheerful bobbing */
        .owl-head-group {
            transform-origin: 155px 115px;
            animation: headBob 0.8s ease-in-out infinite alternate;
        }

        @keyframes headBob {
            0% { transform: rotate(-2.5deg) translateY(1px); }
            100% { transform: rotate(3.5deg) translateY(-3px); }
        }

        /* Owl eyes natural blink */
        .owl-eyes {
            transform-origin: 156px 92px;
            animation: eyeBlink 2.3s infinite;
        }

        @keyframes eyeBlink {
            0%, 44%, 52%, 100% { transform: scaleY(1); }
            48% { transform: scaleY(0.08); }
        }

        /* Graduation cap tassel wind swing */
        .cap-tassel {
            transform-origin: 122px 52px;
            animation: tasselSwing 0.48s ease-in-out infinite alternate;
        }

        @keyframes tasselSwing {
            0% { transform: rotate(8deg); }
            100% { transform: rotate(28deg); }
        }

        /* Delivery box slight jiggle */
        .delivery-box-group {
            transform-origin: 105px 148px;
            animation: boxJiggle 0.4s ease-in-out infinite alternate-reverse;
        }

        @keyframes boxJiggle {
            0% { transform: rotate(-1.5deg) translateY(0); }
            100% { transform: rotate(2.2deg) translateY(-2.5px); }
        }

        /* Spinning wheels */
        .wheel-back {
            transform-origin: 128px 218px;
            animation: wheelSpin 0.42s linear infinite;
        }

        .wheel-front {
            transform-origin: 214px 218px;
            animation: wheelSpin 0.42s linear infinite;
        }

        @keyframes wheelSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Speed lines zipping left */
        .speed-line-1 { animation: zipLine 0.36s ease-out infinite; }
        .speed-line-2 { animation: zipLine 0.36s ease-out 0.12s infinite; }
        .speed-line-3 { animation: zipLine 0.36s ease-out 0.06s infinite; }
        .speed-line-4 { animation: zipLine 0.36s ease-out 0.18s infinite; }

        @keyframes zipLine {
            0% { transform: translateX(10px) scaleX(0.4); opacity: 0; }
            45% { opacity: 1; }
            100% { transform: translateX(-18px) scaleX(1.15); opacity: 0; }
        }

        /* Floating Location Pin (Left) */
        .floating-pin {
            transform-origin: 106px 76px;
            animation: floatPin 1.05s ease-in-out infinite alternate;
        }

        @keyframes floatPin {
            0% { transform: translateY(3px) rotate(-4deg) scale(0.97); }
            100% { transform: translateY(-7px) rotate(4deg) scale(1.05); }
        }

        /* Floating Food Cloche Bubble (Right) */
        .floating-cloche {
            transform-origin: 214px 72px;
            animation: floatCloche 1.2s ease-in-out 0.2s infinite alternate;
        }

        @keyframes floatCloche {
            0% { transform: translateY(-5px) rotate(3deg) scale(1.04); }
            100% { transform: translateY(4px) rotate(-3deg) scale(0.96); }
        }

        /* Brand Wordmark: "uni" (dark green) + "food" (orange) */
        .brand-name {
            font-family: "Fredoka", "Plus Jakarta Sans", sans-serif;
            font-size: 62px;
            font-weight: 700;
            letter-spacing: -1.5px;
            line-height: 1;
            margin-top: -4px;
            display: inline-flex;
            align-items: baseline;
            user-select: none;
            animation: titlePop 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) 0.12s backwards;
        }

        @keyframes titlePop {
            0% { opacity: 0; transform: scale(0.82) translateY(10px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        .brand-uni {
            color: var(--uni-green);
        }

        .brand-food {
            color: var(--food-orange);
        }

        /* Frosted glass greeting pill on the green background */
        .welcome-pill-bar {
            margin-top: 26px;
            padding: 12px 24px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            color: rgba(255, 255, 255, 0.92);
            font-weight: 600;
            letter-spacing: -0.2px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.18);
            animation: pillFadeUp 0.7s ease-out 0.22s backwards;
        }

        @keyframes pillFadeUp {
            0% { opacity: 0; transform: translateY(12px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .status-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--lime-accent);
            box-shadow: 0 0 10px var(--lime-accent);
            animation: dotPulse 1s ease-in-out infinite alternate;
        }

        @keyframes dotPulse {
            0% { transform: scale(0.85); opacity: 0.7; }
            100% { transform: scale(1.25); opacity: 1; }
        }

        .welcome-pill-bar strong {
            color: var(--lime-accent);
            font-weight: 800;
        }
    </style>
</head>
<body>
    <!-- Ambient background glow & rings -->
    <div class="ambient-glow"></div>
    <div class="ambient-ring ring-1"></div>
    <div class="ambient-ring ring-2"></div>
    <div class="ambient-ring ring-3"></div>

    <div class="splash-wrapper">
        <!-- White Showcase Card for Mascot + unifood Logo -->
        <div class="logo-showcase-card">
            <div class="mascot-stage">
                <svg class="mascot-svg" viewBox="0 0 300 250" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="UniFood Owl Mascot on Delivery Scooter">
                    <!-- Circular Framing Arcs (Left & Right) -->
                    <path d="M90 100 A 88 88 0 0 0 92 222" stroke="#183a24" stroke-width="4.5" stroke-linecap="round"/>
                    <path d="M214 96 A 88 88 0 0 1 226 222" stroke="#183a24" stroke-width="4.5" stroke-linecap="round"/>

                    <!-- Speed Lines (Animated) -->
                    <g stroke="#183a24" stroke-linecap="round">
                        <line class="speed-line-1" x1="56" y1="138" x2="82" y2="138" stroke-width="3.8"/>
                        <line class="speed-line-2" x1="38" y1="149" x2="82" y2="149" stroke-width="4.2"/>
                        <line class="speed-line-3" x1="52" y1="184" x2="88" y2="184" stroke-width="3.8"/>
                        <line class="speed-line-4" x1="64" y1="194" x2="92" y2="194" stroke-width="4.2"/>
                    </g>

                    <!-- Floating Map Pin (Top Left) -->
                    <g class="floating-pin">
                        <path d="M106 52 C94 52 85 61 85 72.5 C85 87 106 104 106 104 C106 104 127 87 127 72.5 C127 61 118 52 106 52 Z"
                              fill="#3cd02b" stroke="#183a24" stroke-width="4.2" stroke-linejoin="round"/>
                        <circle cx="106" cy="72" r="7.5" fill="#ffffff"/>
                    </g>

                    <!-- Floating Food Cloche Speech Bubble (Top Right) -->
                    <g class="floating-cloche">
                        <path d="M192 89 L187 103 L202 96 A 26 26 0 1 0 192 89 Z"
                              fill="#ffffff" stroke="#183a24" stroke-width="4.2" stroke-linejoin="round"/>
                        <!-- Green Cloche Icon inside Bubble -->
                        <path d="M199 78 C199 68 206 62 215 62 C224 62 231 68 231 78 Z" fill="#3cd02b"/>
                        <circle cx="215" cy="59.5" r="2.8" fill="#3cd02b"/>
                        <rect x="196" y="80" width="38" height="3.8" rx="1.9" fill="#3cd02b"/>
                    </g>

                    <!-- Animated Rider Group (Scooter + Owl + Box) -->
                    <g class="rider-group">

                        <!-- Back Wheel (Spinning) -->
                        <g class="wheel-back">
                            <circle cx="128" cy="218" r="20" fill="#183a24"/>
                            <circle cx="128" cy="218" r="7.5" fill="#ffffff"/>
                            <line x1="128" y1="198" x2="128" y2="207" stroke="#3cd02b" stroke-width="3" stroke-linecap="round"/>
                            <line x1="128" y1="229" x2="128" y2="238" stroke="#3cd02b" stroke-width="3" stroke-linecap="round"/>
                            <line x1="108" y1="218" x2="117" y2="218" stroke="#3cd02b" stroke-width="3" stroke-linecap="round"/>
                            <line x1="139" y1="218" x2="148" y2="218" stroke="#3cd02b" stroke-width="3" stroke-linecap="round"/>
                        </g>

                        <!-- Front Wheel (Spinning) -->
                        <g class="wheel-front">
                            <circle cx="214" cy="218" r="20" fill="#183a24"/>
                            <circle cx="214" cy="218" r="7.5" fill="#ffffff"/>
                            <line x1="214" y1="198" x2="214" y2="207" stroke="#3cd02b" stroke-width="3" stroke-linecap="round"/>
                            <line x1="214" y1="229" x2="214" y2="238" stroke="#3cd02b" stroke-width="3" stroke-linecap="round"/>
                            <line x1="194" y1="218" x2="203" y2="218" stroke="#3cd02b" stroke-width="3" stroke-linecap="round"/>
                            <line x1="225" y1="218" x2="234" y2="218" stroke="#3cd02b" stroke-width="3" stroke-linecap="round"/>
                        </g>

                        <!-- Delivery Box on Rack -->
                        <g class="delivery-box-group">
                            <!-- Rack under box -->
                            <rect x="96" y="164" width="44" height="8" rx="4" fill="#f37021" stroke="#183a24" stroke-width="4"/>
                            <!-- Main Green Box -->
                            <rect x="86" y="114" width="46" height="50" rx="6" fill="#3cd02b" stroke="#183a24" stroke-width="4.2"/>
                            <!-- Lighter Green Lid -->
                            <rect x="86" y="114" width="46" height="12" rx="4" fill="#68e852" stroke="#183a24" stroke-width="4.2"/>
                            <!-- Orange Graduation Cap Icon inside Box -->
                            <polygon points="109,134 95,141 109,148 123,141" fill="#f37021"/>
                            <path d="M101 144 V151 C101 154 117 154 117 151 V144" fill="#f37021"/>
                            <path d="M97 142 V151" stroke="#f37021" stroke-width="2.2" stroke-linecap="round"/>
                        </g>

                        <!-- Owl Body, Torso & Legs -->
                        <g id="owl-body">
                            <!-- Scooter Seat -->
                            <rect x="124" y="162" width="38" height="11" rx="5.5" fill="#e87225" stroke="#183a24" stroke-width="4"/>
                            <!-- Owl Back & Tail Curve -->
                            <path d="M134 118 C122 130 122 158 134 168 L168 168 C176 154 176 132 166 118 Z"
                                  fill="#e87225" stroke="#183a24" stroke-width="4.2" stroke-linejoin="round"/>
                            <!-- Owl Cream Belly Patch -->
                            <path d="M156 124 C168 132 170 152 162 165 L148 165 C144 150 146 132 156 124 Z"
                                  fill="#fbd6ad"/>
                            <!-- Owl Legs reaching scooter floorboard -->
                            <path d="M148 164 C156 174 156 190 154 202 L168 202 C170 186 168 170 160 162 Z"
                                  fill="#e87225" stroke="#183a24" stroke-width="4" stroke-linejoin="round"/>
                            <path d="M158 164 C166 174 166 190 164 202 L176 202 C178 186 176 170 168 162 Z"
                                  fill="#f58b3c" stroke="#183a24" stroke-width="4" stroke-linejoin="round"/>
                        </g>

                        <!-- Scooter Frame & Fender -->
                        <g id="scooter-body">
                            <!-- Rear taillight -->
                            <path d="M99 178 C94 178 93 186 99 187 Z" fill="#f37021" stroke="#183a24" stroke-width="3.8"/>
                            <!-- Main Green Scooter Chassis -->
                            <path d="M98 208 C100 180 116 171 136 171 C150 171 156 184 156 200 L180 200 C182 178 184 154 182 134 L196 134 C206 156 206 180 196 208 Z"
                                  fill="#3cd02b" stroke="#183a24" stroke-width="4.4" stroke-linejoin="round"/>
                            <!-- Orange stripe accent on rear fender -->
                            <path d="M114 184 C126 178 138 182 144 194" stroke="#f37021" stroke-width="5" stroke-linecap="round"/>
                            <!-- Front Wheel Mudguard / Fender -->
                            <path d="M188 206 C194 184 222 184 234 202 L208 210 Z"
                                  fill="#3cd02b" stroke="#183a24" stroke-width="4.4" stroke-linejoin="round"/>
                            <path d="M196 196 C208 189 220 190 228 198" stroke="#f37021" stroke-width="4" stroke-linecap="round"/>
                            <!-- Handlebar Neck & Headlight -->
                            <path d="M174 126 H195 C200 126 203 130 203 135 C203 140 200 144 195 144 H178 Z"
                                  fill="#3cd02b" stroke="#183a24" stroke-width="4.2" stroke-linejoin="round"/>
                            <ellipse cx="202" cy="135" rx="4.5" ry="8" fill="#f37021" stroke="#183a24" stroke-width="3.8"/>
                        </g>

                        <!-- Owl Wing / Arm holding Handlebar -->
                        <g id="owl-wing">
                            <path d="M138 130 C134 148 144 162 162 156 C172 152 178 144 186 140 C190 137 188 130 182 131 C172 133 162 138 154 130 C148 124 140 124 138 130 Z"
                                  fill="#f37021" stroke="#183a24" stroke-width="4.2" stroke-linejoin="round"/>
                            <!-- Wing feather detail line -->
                            <path d="M142 142 C148 150 158 152 166 146" stroke="#183a24" stroke-width="3.6" stroke-linecap="round"/>
                            <!-- Dark green handlebar grip -->
                            <circle cx="180" cy="137" r="5.5" fill="#183a24"/>
                        </g>

                        <!-- Owl Head & Graduation Cap -->
                        <g class="owl-head-group">
                            <!-- Owl Head Outer Shape with Ear Tufts -->
                            <path d="M116 58 C122 58 128 62 134 65 C146 62 162 62 174 65 C180 62 186 58 192 58 C192 70 188 76 186 80 C192 92 190 112 174 120 C160 126 146 126 132 120 C116 112 114 92 120 80 C118 76 116 70 116 58 Z"
                                  fill="#f37021" stroke="#183a24" stroke-width="4.4" stroke-linejoin="round"/>

                            <!-- Peach Facial Disc around Eyes -->
                            <path d="M122 92 C122 79 134 73 144 79 C150 82 156 82 162 79 C172 73 184 79 184 92 C184 108 170 119 153 119 C136 119 122 108 122 92 Z"
                                  fill="#fbd6ad"/>

                            <!-- Brow Line -->
                            <path d="M119 69 C132 76 144 77 153 77 C162 77 174 76 187 69"
                                  stroke="#183a24" stroke-width="4.2" stroke-linecap="round"/>

                            <!-- Eyes Group (Blinking) -->
                            <g class="owl-eyes">
                                <!-- Left Eye -->
                                <circle cx="139" cy="92" r="14" fill="#ffffff" stroke="#183a24" stroke-width="3.8"/>
                                <circle cx="141" cy="92" r="9.2" fill="#183a24"/>
                                <circle cx="137" cy="88" r="3.2" fill="#ffffff"/>

                                <!-- Right Eye -->
                                <circle cx="169" cy="92" r="14" fill="#ffffff" stroke="#183a24" stroke-width="3.8"/>
                                <circle cx="167" cy="92" r="9.2" fill="#183a24"/>
                                <circle cx="163" cy="88" r="3.2" fill="#ffffff"/>
                            </g>

                            <!-- Owl Beak -->
                            <path d="M154 96 C158 96 160 101 158 106 L154 113 L150 106 C148 101 150 96 154 96 Z"
                                  fill="#fbb03b" stroke="#183a24" stroke-width="3.8" stroke-linejoin="round"/>

                            <!-- Graduation Cap (Mortarboard) -->
                            <g id="graduation-cap">
                                <!-- Cap Skull Base -->
                                <path d="M131 48 V64 C131 69 144 73 154 73 C164 73 177 69 177 64 V48 Z"
                                      fill="#23435c" stroke="#183a24" stroke-width="4.2" stroke-linejoin="round"/>
                                <!-- Cap Top Diamond -->
                                <polygon points="154,28 112,44 154,58 196,44"
                                         fill="#1b354a" stroke="#183a24" stroke-width="4.2" stroke-linejoin="round"/>
                                <!-- Tassel (Swinging) -->
                                <g class="cap-tassel">
                                    <path d="M132 45 L123 50 V66" stroke="#183a24" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                    <polygon points="123,62 119,71 127,71" fill="#183a24"/>
                                </g>
                            </g>
                        </g>
                    </g>

                    <!-- Ground Line -->
                    <line x1="76" y1="238" x2="236" y2="238" stroke="#183a24" stroke-width="5.5" stroke-linecap="round"/>
                </svg>
            </div>

            <h1 class="brand-name">
                <span class="brand-uni">uni</span><span class="brand-food">food</span>
            </h1>
        </div>

        <!-- Frosted Glass Greeting Bar on the Green Background -->
        <?php
        $userRole = strtolower($_SESSION['role'] ?? '');
        $destUrl = 'index.php';
        $welcomeMsg = 'Preparing your campus cravings...';
        if ($userRole === 'seller' || $userRole === 'vendor') {
            $destUrl = 'vendor.php';
            $welcomeMsg = 'Opening your Vendor Kitchen Portal...';
        } elseif ($userRole === 'rider' || $userRole === 'runner') {
            $destUrl = 'rider.php';
            $welcomeMsg = 'Connecting to Campus Runner Deliveries 🛵...';
        }
        ?>
        <div class="welcome-pill-bar">
            <span class="status-dot"></span>
            <span>Welcome, <strong><?= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8') ?></strong>! <?= htmlspecialchars($welcomeMsg, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </div>

    <script>
        const TARGET_URL = <?= json_encode($destUrl) ?>;
        // Smooth transition: trigger fade-out at 2.6s, navigate to target at 3.0s
        setTimeout(() => {
            document.body.classList.add('page-leaving');
        }, 2600);

        setTimeout(() => {
            window.location.href = TARGET_URL;
        }, 3000);
    </script>
</body>
</html>
