<?php

include ('cross_domain.php');
include('conn.php');

// -----------------------------------------
try {
    $sql = "SELECT * FROM DONATE ORDER BY id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $artists = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($artists);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>