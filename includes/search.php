<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = htmlspecialchars(trim($_GET['query'])); // Sanitize input
    $sql = "
        SELECT 
            items.id AS item_id, 
            items.name AS item_name, 
            items.description
        FROM items 
        WHERE items.name LIKE CONCAT('%', ?, '%')
           OR items.description LIKE CONCAT('%', ?, '%')
        LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $query, $query);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    $stmt->close();
    echo json_encode($items);
}
?>
