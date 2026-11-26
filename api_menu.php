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

// Ensure uploads folder exists
$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0777, true);
}

// Helper to handle uploaded photo
function processUploadedPhoto($fileKey, $dishId) {
    global $uploadDir;
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $tmpName = $_FILES[$fileKey]['tmp_name'];
    $ext = strtolower(pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($ext, $allowed)) {
        return null;
    }
    $filename = 'dish_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $dishId) . '_' . time() . '.' . $ext;
    $targetPath = $uploadDir . '/' . $filename;
    if (move_uploaded_file($tmpName, $targetPath)) {
        return 'uploads/' . $filename;
    }
    return null;
}

$action = $_REQUEST['action'] ?? ($_SERVER['REQUEST_METHOD'] === 'POST' ? 'add' : 'list');

// 1. LIST MENU ITEMS
if ($action === 'list') {
    if (!$pdo) {
        echo json_encode(['success' => false, 'items' => []]);
        exit;
    }

    $sellerFilter = isset($_GET['seller_id']) ? intval($_GET['seller_id']) : null;
    $stallFilter = isset($_GET['stall']) ? trim($_GET['stall']) : null;

    try {
        $sql = '
            SELECT m.id, m.seller_id, m.name AS title, m.category, m.price, m.prep_mins AS "prepMins",
                   m.badge, m.description AS desc, m.is_available AS available,
                   COALESCE(m.stock_quantity, 50) AS quantity,
                   m.image_url AS "imageUrl",
                   s.stall_name AS stall, s.campus_location
            FROM menu_items m
            LEFT JOIN sellers s ON m.seller_id = s.id
        ';
        $params = [];
        if ($sellerFilter) {
            $sql .= ' WHERE m.seller_id = :sid';
            $params['sid'] = $sellerFilter;
        } elseif ($stallFilter) {
            $sql .= ' WHERE lower(s.stall_name) LIKE lower(:stall)';
            $params['stall'] = '%' . $stallFilter . '%';
        }
        $sql .= ' ORDER BY m.id DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        // Ensure sellerId integer is present on each item
        foreach ($items as &$it) {
            $it['sellerId'] = isset($it['seller_id']) ? (int)$it['seller_id'] : null;
        }
        unset($it);

        echo json_encode(['success' => true, 'items' => $items]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage(), 'items' => []]);
    }
    exit;
}

// 2. ADD NEW DISH
if ($action === 'add') {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? 'local');
    $price = floatval($_POST['price'] ?? 5.0);
    $prep = intval($_POST['prep_mins'] ?? 8);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 50;
    $badge = trim($_POST['badge'] ?? 'NEW ITEM');
    $desc = trim($_POST['description'] ?? 'Freshly prepared at campus stall.');
    $imageUrl = trim($_POST['image_url'] ?? '');

    if ($title === '') {
        echo json_encode(['success' => false, 'error' => 'Dish name is required.']);
        exit;
    }

    // Determine current seller ID accurately
    $sellerId = 1;
    if (!empty($_SESSION['seller_id'])) {
        $sellerId = intval($_SESSION['seller_id']);
    } elseif (!empty($_SESSION['user_id']) && strpos($_SESSION['user_id'], 'seller_') === 0) {
        $sellerId = intval(str_replace('seller_', '', $_SESSION['user_id']));
    }

    $stallName = 'Cafe Abang Mirul';
    $campusLocation = 'Main Campus';
    if (!empty($_SESSION['stall_name'])) {
        $stallName = $_SESSION['stall_name'];
    }
    if (!empty($_SESSION['campus_location'])) {
        $campusLocation = $_SESSION['campus_location'];
    }

    // Lookup seller record in database for exact stall identity
    if ($pdo && $sellerId) {
        try {
            $stmtS = $pdo->prepare('SELECT id, stall_name, campus_location FROM sellers WHERE id = :id');
            $stmtS->execute(['id' => $sellerId]);
            $sRow = $stmtS->fetch();
            if ($sRow) {
                $stallName = $sRow['stall_name'];
                $campusLocation = $sRow['campus_location'];
            }
        } catch (Throwable $e) {
            // fallback to session info
        }
    }

    // Process photo if uploaded
    $uploadedPath = processUploadedPhoto('photo', 'new_' . time());
    if ($uploadedPath) {
        $imageUrl = $uploadedPath;
    }

    $dbId = null;
    if ($pdo) {
        try {
            $stmt = $pdo->prepare('
                INSERT INTO menu_items (seller_id, name, category, price, prep_mins, badge, description, stock_quantity, image_url, is_available)
                VALUES (:seller_id, :name, :category, :price, :prep, :badge, :desc, :qty, :img, true)
                RETURNING id
            ');
            $stmt->execute([
                'seller_id' => $sellerId,
                'name' => $title,
                'category' => $category,
                'price' => $price,
                'prep' => $prep,
                'badge' => $badge,
                'desc' => $desc,
                'qty' => $quantity,
                'img' => $imageUrl
            ]);
            $row = $stmt->fetch();
            $dbId = $row['id'] ?? null;
        } catch (Throwable $e) {
            // Error inserting to PostgreSQL
        }
    }

    $uniqueId = $dbId ? ('db_' . $dbId) : ('custom_' . time());

    echo json_encode([
        'success' => true,
        'message' => 'Dish added successfully to campus menu!',
        'item' => [
            'id' => $uniqueId,
            'sellerId' => $sellerId,
            'title' => $title,
            'stall' => $stallName . ' · ' . $campusLocation,
            'category' => $category,
            'badge' => $badge,
            'price' => $price,
            'prepMins' => $prep,
            'quantity' => $quantity,
            'imageUrl' => $imageUrl,
            'rating' => 5.0,
            'popularRank' => 1,
            'desc' => $desc,
            'available' => true
        ]
    ]);
    exit;
}

// 3. EDIT EXISTING DISH (Title, Price, Quantity/Stock, Description, Category, Photo)
if ($action === 'edit') {
    $dishId = trim($_POST['id'] ?? '');
    $rawId = str_replace('db_', '', $dishId);
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? 'local');
    $price = floatval($_POST['price'] ?? 5.0);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 50;
    $badge = trim($_POST['badge'] ?? 'SPECIAL');
    $desc = trim($_POST['description'] ?? '');
    $imageUrl = trim($_POST['image_url'] ?? '');
    $available = filter_var($_POST['available'] ?? ($quantity > 0), FILTER_VALIDATE_BOOLEAN);

    if ($title === '') {
        echo json_encode(['success' => false, 'error' => 'Dish name is required.']);
        exit;
    }

    // Process photo if uploaded
    $uploadedPath = processUploadedPhoto('photo', $rawId ?: time());
    if ($uploadedPath) {
        $imageUrl = $uploadedPath;
    }

    if ($pdo && is_numeric($rawId)) {
        try {
            $stmt = $pdo->prepare('
                UPDATE menu_items
                SET name = :title,
                    category = :category,
                    price = :price,
                    stock_quantity = :quantity,
                    description = :desc,
                    badge = CASE WHEN :badge != \'\' THEN :badge ELSE badge END,
                    image_url = CASE WHEN :img != \'\' THEN :img ELSE image_url END,
                    is_available = :avail
                WHERE id = :id
            ');
            $stmt->execute([
                'title' => $title,
                'category' => $category,
                'price' => $price,
                'quantity' => $quantity,
                'desc' => $desc,
                'badge' => $badge,
                'img' => $imageUrl,
                'avail' => ($available && $quantity > 0) ? 'true' : 'false',
                'id' => intval($rawId)
            ]);

            // Re-fetch updated image_url if updated from existing
            if ($imageUrl === '') {
                $stmtImg = $pdo->prepare('SELECT image_url FROM menu_items WHERE id = :id');
                $stmtImg->execute(['id' => intval($rawId)]);
                $row = $stmtImg->fetch();
                $imageUrl = $row['image_url'] ?? '';
            }
        } catch (Throwable $e) {
            // Ignore
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Dish updated successfully!',
        'item' => [
            'id' => $dishId,
            'title' => $title,
            'category' => $category,
            'price' => $price,
            'quantity' => $quantity,
            'badge' => $badge,
            'desc' => $desc,
            'imageUrl' => $imageUrl,
            'available' => ($available && $quantity > 0)
        ]
    ]);
    exit;
}

// 4. TOGGLE DISH AVAILABILITY (IN STOCK / SOLD OUT)
if ($action === 'toggle_stock') {
    $dishId = $_POST['id'] ?? '';
    $rawId = str_replace('db_', '', $dishId);
    $available = filter_var($_POST['available'] ?? true, FILTER_VALIDATE_BOOLEAN);

    if ($pdo && is_numeric($rawId)) {
        try {
            $stmt = $pdo->prepare('UPDATE menu_items SET is_available = :avail WHERE id = :id');
            $stmt->execute(['avail' => $available ? 'true' : 'false', 'id' => intval($rawId)]);
        } catch (Throwable $e) {
            // Ignore
        }
    }

    echo json_encode(['success' => true, 'available' => $available]);
    exit;
}
