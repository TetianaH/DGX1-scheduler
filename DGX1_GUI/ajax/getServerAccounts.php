<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['isAdmin'])) {
    http_response_code(401);
    echo ("Unauthorized access is forbidden");
}

include(__BASE_PATH__ . '/database_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "
        SELECT 
            sa.account_ID AS `id`,
            sa.name AS `name`
        FROM `server_accounts` AS sa
    ";

    // Check DB connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // prepare, bind data and execute
    $accounts = array();
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows != 0) {
        while ($row = $result->fetch_assoc()) {
            $accounts[] = $row;
        }
    }
    $stmt->close();

    header("Content-type: application/json");
    header("X-Content-Type-Options: nosniff");
    echo (json_encode($accounts, TRUE));
}
