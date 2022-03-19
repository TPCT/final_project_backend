<?php
date_default_timezone_set('Africa/Cairo');

session_start();

use core\Application;

include_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . "core" . DIRECTORY_SEPARATOR . "Application.php");

$app = new Application(dirname(__DIR__), __DIR__ . DIRECTORY_SEPARATOR . "db.env");


$app->router->get('/api/session', [\controllers\embedded\ApiSessionKeysGeneratorController::class, 'generateSessionKeys']);
$app->router->post('/api/session', [\controllers\embedded\ApiSessionKeysGeneratorController::class, 'generateSessionKeys']);
$app->router->post("/api/login", [\controllers\embedded\ApiAuthenticationController::class, 'index']);
$app->router->post("/api/log_event", [\controllers\embedded\ApiLogEventController::class, 'index']);

/* Tests
    $app->router->post("/api/symmetric/encryption", [\controllers\embedded\ApiSessionKeysGeneratorController::class, 'encryptSymmetric']);
    $app->router->post("/api/symmetric/decryption", [\controllers\embedded\ApiSessionKeysGeneratorController::class, 'decryptSymmetric']);
    $app->router->post("/api/asymmetric/decryption", [\controllers\embedded\ApiSessionKeysGeneratorController::class, 'decryptAsymmetric']);
*/

$app->run();