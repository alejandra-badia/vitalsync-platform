<?php
// app/config/connection_db.php

$host    = 'localhost';
$db      = 'vitalsync_db'; 
$user    = 'root';          
$pass    = '';              
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Attempt database connection
    $pdo = new PDO($dsn, $user, $pass, $options);

} catch (\PDOException $e) {

    // Log detailed error to server logs (not visible to users)
    error_log("Database connection error: " . $e->getMessage());

    // Set PDO to null so app can detect unavailable DB
    $pdo = null;
}
