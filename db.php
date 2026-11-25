<?php

$host = "localhost";
$dbname = "classwall";
$user = "postgres";
$password = "091707";

try {
    $pdo = new PDO(
        "pgsql:host=$host;dbname=$dbname",
        $user,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Database Connected Successfully!";

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>