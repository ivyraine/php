<?php

include ('cross_domain.php');
include('conn.php');

// -----------------------------------------
try {
    $sql = "SELECT * FROM MEMBER ORDER BY id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($members);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>