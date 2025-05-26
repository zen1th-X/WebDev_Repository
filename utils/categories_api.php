<?php
header('Content-Type: application/json');
$host = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $category = trim($data['name'] ?? '');
    if ($category === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Category name required']);
        exit;
    }
    $stmt = $conn->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
    $stmt->bind_param("s", $category);
    if ($stmt->execute()) {
        updateCategoryCount($conn); // Update category count after successful insert
        echo json_encode(['success' => true, 'name' => $category]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Insert failed']);
    }
    $stmt->close();
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT category_id, name FROM categories ORDER BY name ASC");
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    echo json_encode($categories);
    exit;
}

// Add this function to both files after a successful insert or delete
function updateCategoryCount($conn) {
    $result = $conn->query("SELECT COUNT(*) AS cnt FROM categories");
    $row = $result->fetch_assoc();
    $total = (int)$row['cnt'];
    // If count_items table is empty, insert a row
    $conn->query("INSERT INTO count_items (total_categories) SELECT $total WHERE NOT EXISTS (SELECT 1 FROM count_items)");
    // Otherwise, update the row
    $conn->query("UPDATE count_items SET total_categories = $total");
}
?>