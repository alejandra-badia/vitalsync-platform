<?php
// public/index.php

// 1. Load the database connection
require_once '../app/config/connection_db.php';

// 2. Load the Controller (The logic)
require_once '../app/controllers/DashboardController.php';

// 3. Start the controller
$controller = new DashboardController($pdo);
$controller->index();
?>