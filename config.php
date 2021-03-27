<?php
// Сборк ошибок в лог
/* error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', true);
ini_set('error_log', __DIR__ . '/logs/main_error.log'); */
// ------------------

header('Content-Type: text/html; charset=utf-8');
session_start();

define('MAINURL', '/');
define('AUTHURL','https://wotleague.ru/auth');
define('LOGOUTURL','/logout');
define('ADMINURL', '/admin');
define('TICKETURL', '/ticket');
define('FREELEAGUE', '/freeleague');
define('NORMALLEAGUE', '/normalleague');
define('UNDO', '/undo');


define('APPLICATION_ID','');

//подключение к БД через PDO
$driver = 'mysql';
$host = "localhost";
$db = "wotleague_1";
$user = "wotleague_1";
$pass = "biba333";
$charset = "utf8";

$dsn = "$driver:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
];
$pdo = new PDO($dsn, $user, $pass, $opt);

?>