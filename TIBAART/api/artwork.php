<?php

include ('cross_domain.php');
include('conn.php');

// -----------------------------------------
try {
    $sql = " SELECT 
              AW.*,
              A.name as artist
            FROM ARTWORK AW
	            LEFT JOIN ARTIST A
	            ON AW.artist_id = A.id
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