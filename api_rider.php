<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dsn = sprintf(
    'pgsql:host=%s;port=%s;dbname=%s',
    getenv('DB_HOST') ?: '127.0.0.1',
    getenv('DB_PORT') ?: '5432',
    getenv('DB_DATABASE') ?: 'unifood'
);

$pdo = null;
try {
    $pdo = new PDO($dsn, getenv('DB_USERNAME') ?: 'postgres', getenv('DB_PASSWORD') ?: '123', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 2
    ]);
} catch (Throwable $e) {
    // Database connection fallback handled gracefully
}

// Resolve current rider ID
$riderId = 1;
if (!empty($_SESSION['rider_id'])) {
    $riderId = (int)$_SESSION['rider_id'];
} elseif (!empty($_SESSION['user_id']) && strpos($_SESSION['user_id'], 'rider_') === 0) {
    $riderId = (int)str_replace('rider_', '', $_SESSION['user_id']);
}

$action = $_REQUEST['action'] ?? 'list';

if ($pdo) {
    try {
        $pdo->exec('ALTER TABLE orders ADD COLUMN IF NOT EXISTS customer_name VARCHAR(150)');
        $pdo->exec('ALTER TABLE orders ADD COLUMN IF NOT EXISTS items_summary TEXT');
        $pdo->exec('ALTER TABLE orders ADD COLUMN IF NOT EXISTS stall_label VARCHAR(150)');
    } catch (Throwable $e) {
        // Ignore if already exists
    }
}

// 0. CREATE NEW ORDER (from Student Checkout or Sync)
if ($action === 'create_order') {
    $orderCode = trim($_POST['order_code'] ?? '');
    $orderType = trim($_POST['order_type'] ?? 'delivery');
    $deliverySpot = trim($_POST['delivery_spot'] ?? 'Kolej Kediaman Satria');
    $deliveryNote = trim($_POST['delivery_note'] ?? '');
    $totalAmount = (float)($_POST['total_amount'] ?? 0);
    $deliveryFee = (float)($_POST['delivery_fee'] ?? 2.00);
    $customerName = trim($_POST['customer_name'] ?? ($_SESSION['full_name'] ?? 'Campus Student'));
    $itemsSummary = trim($_POST['items_summary'] ?? 'Prepared campus meal package');
    $stallLabel = trim($_POST['stall_name'] ?? 'Cafe Abang Mirul');
    $userId = is_numeric($_SESSION['user_id'] ?? null) ? (int)$_SESSION['user_id'] : 1;

    if ($pdo && $orderCode !== '') {
        try {
            $stmtChk = $pdo->prepare('SELECT id FROM orders WHERE order_code = :code LIMIT 1');
            $stmtChk->execute(['code' => $orderCode]);
            $existing = $stmtChk->fetch();

            if (!$existing) {
                $stmtS = $pdo->prepare('SELECT id FROM sellers WHERE lower(stall_name) LIKE lower(:st) LIMIT 1');
                $stmtS->execute(['st' => '%' . $stallLabel . '%']);
                $sRow = $stmtS->fetch();
                $sellerId = $sRow ? (int)$sRow['id'] : 1;

                $stmtIns = $pdo->prepare("
                    INSERT INTO orders (
                        order_code, user_id, seller_id, order_type,
                        delivery_spot, delivery_note, total_amount, delivery_fee,
                        status, payment_method, customer_name, items_summary, stall_label
                    ) VALUES (
                        :code, :uid, :sid, :otype,
                        :spot, :note, :total, :fee,
                        'preparing', 'Campus Wallet', :cname, :items, :stall
                    ) RETURNING id
                ");
                $stmtIns->execute([
                    'code' => $orderCode,
                    'uid' => $userId,
                    'sid' => $sellerId,
                    'otype' => $orderType,
                    'spot' => $deliverySpot,
                    'note' => $deliveryNote,
                    'total' => $totalAmount,
                    'fee' => $deliveryFee > 0 ? $deliveryFee : 2.00,
                    'cname' => $customerName,
                    'items' => $itemsSummary,
                    'stall' => $stallLabel
                ]);
                $newId = $stmtIns->fetchColumn();
                echo json_encode(['success' => true, 'order_id' => $newId, 'order_code' => $orderCode]);
                exit;
            }
            echo json_encode(['success' => true, 'order_id' => $existing['id'], 'order_code' => $orderCode, 'existing' => true]);
            exit;
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    echo json_encode(['success' => true, 'order_code' => $orderCode]);
    exit;
}

// 1. LIST RIDER ORDERS & JOBS
if ($action === 'list') {
    $availableJobs = [];
    $activeDeliveries = [];
    $completedDeliveries = [];
    $earnings = 0.00;
    $isOnline = true;

    if ($pdo) {
        try {
            // Fetch rider profile info
            $stmtR = $pdo->prepare('SELECT earnings_today, total_delivered, is_online FROM riders WHERE id = :rid OR user_id = :rid LIMIT 1');
            $stmtR->execute(['rid' => $riderId]);
            $rProfile = $stmtR->fetch();
            if ($rProfile) {
                $earnings = (float)($rProfile['earnings_today'] ?? 0.00);
                $isOnline = (bool)($rProfile['is_online'] ?? true);
            }

            // Available jobs: delivery orders not yet claimed by any rider
            $stmtAvail = $pdo->query("
                SELECT o.id, o.order_code, o.order_type, o.delivery_spot, o.delivery_note, 
                       o.total_amount, COALESCE(o.delivery_fee, 2.00) AS delivery_fee, 
                       o.status, o.payment_method, to_char(o.created_at, 'HH12:MI AM') as placed_at,
                       COALESCE(o.stall_label, s.stall_name, 'Cafe Abang Mirul') AS stall_name,
                       COALESCE(s.campus_location, 'Main Campus') AS campus_location,
                       COALESCE(o.customer_name, u.full_name, 'Campus Student') AS customer_name,
                       COALESCE(o.items_summary, 'Prepared campus meal package') AS items_summary
                FROM orders o
                LEFT JOIN sellers s ON o.seller_id = s.id
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.order_type = 'delivery' 
                  AND (o.rider_id IS NULL OR o.rider_id = 0)
                  AND o.status IN ('ready', 'preparing', 'received')
                ORDER BY o.id DESC
            ");
            $availableJobs = $stmtAvail->fetchAll();

            // Active deliveries assigned to this rider
            $stmtActive = $pdo->prepare("
                SELECT o.id, o.order_code, o.order_type, o.delivery_spot, o.delivery_note, 
                       o.total_amount, COALESCE(o.delivery_fee, 2.00) AS delivery_fee, 
                       o.status, o.payment_method, to_char(o.created_at, 'HH12:MI AM') as placed_at,
                       COALESCE(o.stall_label, s.stall_name, 'Cafe Abang Mirul') AS stall_name,
                       COALESCE(s.campus_location, 'Main Campus') AS campus_location,
                       COALESCE(o.customer_name, u.full_name, 'Campus Student') AS customer_name,
                       COALESCE(o.items_summary, 'Prepared campus meal package') AS items_summary
                FROM orders o
                LEFT JOIN sellers s ON o.seller_id = s.id
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.rider_id = :rid 
                  AND o.status IN ('delivering', 'ready', 'preparing', 'received')
                ORDER BY o.id DESC
            ");
            $stmtActive->execute(['rid' => $riderId]);
            $activeDeliveries = $stmtActive->fetchAll();

            // Completed deliveries for this rider
            $stmtComp = $pdo->prepare("
                SELECT o.id, o.order_code, o.delivery_spot, o.total_amount, 
                       COALESCE(o.delivery_fee, 2.00) AS delivery_fee, o.status,
                       to_char(o.created_at, 'HH12:MI AM') as placed_at,
                       COALESCE(o.stall_label, s.stall_name, 'Cafe Abang Mirul') AS stall_name,
                       COALESCE(s.campus_location, 'Main Campus') AS campus_location,
                       COALESCE(o.customer_name, u.full_name, 'Campus Student') AS customer_name,
                       COALESCE(o.items_summary, 'Prepared campus meal package') AS items_summary
                FROM orders o
                LEFT JOIN sellers s ON o.seller_id = s.id
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.rider_id = :rid AND o.status = 'completed'
                ORDER BY o.id DESC
                LIMIT 15
            ");
            $stmtComp->execute(['rid' => $riderId]);
            $completedDeliveries = $stmtComp->fetchAll();

        } catch (Throwable $e) {
            // fallback
        }
    }

    echo json_encode([
        'success' => true,
        'rider_id' => $riderId,
        'is_online' => $isOnline,
        'earnings' => $earnings,
        'available' => $availableJobs,
        'active' => $activeDeliveries,
        'completed' => $completedDeliveries
    ]);
    exit;
}

// 2. ACCEPT DELIVERY JOB
if ($action === 'accept') {
    $orderCode = trim($_POST['order_code'] ?? '');
    $orderId = intval($_POST['order_id'] ?? 0);

    if ($pdo && ($orderCode !== '' || $orderId > 0)) {
        try {
            $stmt = $pdo->prepare('
                UPDATE orders 
                SET rider_id = :rid 
                WHERE (order_code = :code OR id = :id) AND (rider_id IS NULL OR rider_id = 0)
            ');
            $stmt->execute(['rid' => $riderId, 'code' => $orderCode, 'id' => $orderId]);
            echo json_encode(['success' => true, 'message' => 'Job accepted! Head to stall for pickup.']);
            exit;
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    echo json_encode(['success' => true, 'message' => 'Job accepted (client mode)']);
    exit;
}

// 3. PICK UP ORDER FROM STALL
if ($action === 'pickup') {
    $orderCode = trim($_POST['order_code'] ?? '');
    $orderId = intval($_POST['order_id'] ?? 0);

    if ($pdo && ($orderCode !== '' || $orderId > 0)) {
        try {
            $stmt = $pdo->prepare('
                UPDATE orders 
                SET status = \'delivering\' 
                WHERE (order_code = :code OR id = :id)
            ');
            $stmt->execute(['code' => $orderCode, 'id' => $orderId]);
            echo json_encode(['success' => true, 'message' => 'Order picked up! Now on the way to student.']);
            exit;
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    echo json_encode(['success' => true, 'message' => 'Order in transit']);
    exit;
}

// 4. COMPLETE DELIVERY
if ($action === 'deliver') {
    $orderCode = trim($_POST['order_code'] ?? '');
    $orderId = intval($_POST['order_id'] ?? 0);

    if ($pdo && ($orderCode !== '' || $orderId > 0)) {
        try {
            $stmt = $pdo->prepare('
                UPDATE orders 
                SET status = \'completed\' 
                WHERE (order_code = :code OR id = :id)
            ');
            $stmt->execute(['code' => $orderCode, 'id' => $orderId]);

            // Add RM 2.00 delivery fee to rider earnings
            $stmtR = $pdo->prepare('
                UPDATE riders 
                SET earnings_today = earnings_today + 2.00, total_delivered = total_delivered + 1
                WHERE id = :rid OR user_id = :rid
            ');
            $stmtR->execute(['rid' => $riderId]);

            echo json_encode(['success' => true, 'message' => 'Order delivered! RM 2.00 added to your earnings.']);
            exit;
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    echo json_encode(['success' => true, 'message' => 'Order completed']);
    exit;
}

// 5. TOGGLE ONLINE / OFFLINE DUTY
if ($action === 'toggle_duty') {
    $isOnline = filter_var($_POST['online'] ?? true, FILTER_VALIDATE_BOOLEAN);
    if ($pdo) {
        try {
            $stmt = $pdo->prepare('UPDATE riders SET is_online = :online WHERE id = :rid OR user_id = :rid');
            $stmt->execute(['online' => $isOnline ? 'true' : 'false', 'rid' => $riderId]);
        } catch (Throwable $e) {
            // Ignore
        }
    }
    echo json_encode(['success' => true, 'is_online' => $isOnline]);
    exit;
}

// 6. UPLOAD / UPDATE RIDER PROFILE PICTURE
if ($action === 'update_avatar') {
    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0777, true);
    }

    $avatarUrl = trim($_POST['avatar_url'] ?? '');

    // Check if file uploaded
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['avatar']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($ext, $allowed)) {
            $filename = 'rider_' . $riderId . '_' . time() . '.' . $ext;
            $targetPath = $uploadDir . '/' . $filename;
            if (move_uploaded_file($tmpName, $targetPath)) {
                $avatarUrl = 'uploads/' . $filename;
            }
        }
    }

    $isRemove = isset($_POST['remove']) && ($_POST['remove'] === 'true' || $_POST['remove'] === '1');
    if ($isRemove) {
        $avatarUrl = null;
    } elseif ($avatarUrl === '') {
        echo json_encode(['success' => false, 'error' => 'Please select a photo or enter an image URL.']);
        exit;
    }

    if ($pdo) {
        try {
            $stmt = $pdo->prepare('UPDATE riders SET avatar_url = :url WHERE id = :rid OR user_id = :rid');
            $stmt->execute(['url' => $avatarUrl, 'rid' => $riderId]);

            $stmt2 = $pdo->prepare('UPDATE users SET avatar_url = :url WHERE id = :uid OR email = (SELECT email FROM riders WHERE id = :rid)');
            $stmt2->execute(['url' => $avatarUrl, 'uid' => $riderId, 'rid' => $riderId]);
        } catch (Throwable $e) {
            // Ignore
        }
    }

    $_SESSION['avatar_url'] = $avatarUrl;

    echo json_encode([
        'success' => true,
        'message' => $avatarUrl ? 'Profile photo updated successfully!' : 'Profile photo removed.',
        'avatar_url' => $avatarUrl
    ]);
    exit;
}

