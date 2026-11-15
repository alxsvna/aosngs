<?php
// Настройки БД Open Server (по умолчанию root без пароля). При необходимости измените.
const DB_HOST = '127.0.1.30', DB_NAME = 'aosngs', DB_USER = 'root', DB_PASS = '';
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();
function db() {
    static $p;
    return $p ??= new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
}
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
