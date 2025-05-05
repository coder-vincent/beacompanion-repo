<?php
date_default_timezone_set('Asia/Manila');
require_once realpath(__DIR__ . '/../../vendor/autoload.php');


$dotenv = Dotenv\Dotenv::createImmutable(realpath(__DIR__ . '/../../'));
$dotenv->load();

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
?>