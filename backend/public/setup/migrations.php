<?php

session_start();

use core\Application;

include_once(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "core" . DIRECTORY_SEPARATOR . "Application.php");


$app = new Application(dirname(__DIR__, 2), __DIR__ . DIRECTORY_SEPARATOR . "db.env");
$app->database->applyMigrations();
