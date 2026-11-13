<?php
require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->query("SELECT DATABASE() AS database_name");
$result = $stmt->fetch();

echo "<h1>StockPilot 360</h1>";
echo "<h2>Database connection successful!</h2>";
echo "<p>Database: " .
    htmlspecialchars($result['database_name'], ENT_QUOTES, 'UTF-8') .
    "</p>";