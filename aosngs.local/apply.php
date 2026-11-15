<?php
require __DIR__ . '/config.php';
$ajax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
function out($ok, $msg) {
    global $ajax;
    if ($ajax) { header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok' => $ok, 'msg' => $msg], JSON_UNESCAPED_UNICODE); }
    else echo '<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width"><p style="font:18px system-ui;padding:24px">' . h($msg) . ' <a href="./">Назад</a></p>';
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(false, 'Неверный запрос');
if (!empty($_POST['hp'])) out(true, 'Спасибо!');
$n = trim($_POST['nm'] ?? ''); $p = trim($_POST['ph'] ?? ''); $t = trim($_POST['tp'] ?? 'Другое'); $c = trim($_POST['cm'] ?? '');
if ($n === '' || strlen(preg_replace('/\D/', '', $p)) < 10 || empty($_POST['ok'])) out(false, 'Проверьте имя, телефон и согласие на обработку данных');
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$q = db()->prepare('SELECT COUNT(*) FROM requests WHERE ip=? AND created > NOW() - INTERVAL 1 HOUR');
$q->execute([$ip]);
if ($q->fetchColumn() >= 5) out(false, 'Слишком много заявок, попробуйте позже');
db()->prepare('INSERT INTO requests(name,phone,topic,comment,ip) VALUES(?,?,?,?,?)')
    ->execute([mb_substr($n, 0, 150), mb_substr($p, 0, 40), mb_substr($t, 0, 100), mb_substr($c, 0, 2000), $ip]);
out(true, 'Спасибо! Заявка отправлена, мы свяжемся с вами.');
