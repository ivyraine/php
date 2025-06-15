<?php

include ('cross_domain.php');
include('conn.php');

// -----------------------------------------
try {
    $sql = " SELECT 
              E.*,
              A.name as artist
            FROM EXPO E
	            LEFT JOIN ARTIST A
	            ON E.artist_id = A.id;
            ORDER BY id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $artworks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($artworks);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>