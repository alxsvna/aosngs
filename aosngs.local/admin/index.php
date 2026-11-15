<?php
require __DIR__ . '/../config.php';
const PERM = ['admin' => ['requests','prices','stations','notices','news','hero','users'], 'editor' => ['hero','news','notices'], 'operator' => ['requests','prices','stations','notices']];
const TAB = ['requests' => 'Заявки', 'prices' => 'Цены', 'stations' => 'Карта АЗС', 'notices' => 'Объявления', 'news' => 'Новости', 'hero' => 'Главный экран', 'users' => 'Пользователи'];
const ROLE = ['admin' => 'Администратор', 'editor' => 'Редактор', 'operator' => 'Оператор'];
const SN = ['new' => 'Новая', 'work' => 'В работе', 'done' => 'Закрыта'];
if (isset($_GET['out'])) { session_destroy(); header('Location: ./'); exit; }
$u = $_SESSION['u'] ?? null; $err = ''; $d = db();
function hid($t) { return '<input type="hidden" name="csrf" value="' . h($_SESSION['csrf'] ?? '') . '"><input type="hidden" name="tab" value="' . $t . '">'; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$u) {
        $s = $d->prepare('SELECT * FROM users WHERE login=?'); $s->execute([trim($_POST['login'] ?? '')]); $r = $s->fetch();
        if ($r && password_verify($_POST['pass'] ?? '', $r['pass_hash'])) {
            session_regenerate_id(true);
            $_SESSION['u'] = ['id' => $r['id'], 'login' => $r['login'], 'role' => $r['role']];
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
            header('Location: ./'); exit;
        }
        sleep(1); $err = 'Неверный логин или пароль';
    } else {
        $t = $_POST['tab'] ?? '';
        if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '') || !in_array($t, PERM[$u['role']], true)) die('Доступ запрещён');
        $a = $_POST['a'] ?? ''; $id = (int)($_POST['id'] ?? 0); $P = fn($k) => trim($_POST[$k] ?? '');
        try { switch ("$t:$a") {
            case 'requests:work': case 'requests:done': $d->prepare('UPDATE requests SET status=? WHERE id=?')->execute([$a, $id]); break;
            case 'requests:del': if ($u['role'] === 'admin') $d->prepare('DELETE FROM requests WHERE id=?')->execute([$id]); break;
            case 'prices:save': $d->prepare('UPDATE prices SET name=?, value=? WHERE id=?')->execute([$P('name'), (float)$P('value'), $id]); break;
            case 'prices:add': $d->prepare('INSERT INTO prices(name,value,sort) VALUES(?,?,99)')->execute([$P('name'), (float)$P('value')]); break;
            case 'prices:del': $d->prepare('DELETE FROM prices WHERE id=?')->execute([$id]); break;
            case 'stations:add': $d->prepare('INSERT INTO stations(name,descr,status,lat,lon) VALUES(?,?,?,?,?)')->execute([$P('name'), $P('descr'), $P('status'), (float)$P('lat'), (float)$P('lon')]); break;
            case 'stations:del': $d->prepare('DELETE FROM stations WHERE id=?')->execute([$id]); break;
            case 'notices:add': if ($P('text') !== '') $d->prepare('INSERT INTO notices(text) VALUES(?)')->execute([$P('text')]); break;
            case 'notices:del': $d->prepare('DELETE FROM notices WHERE id=?')->execute([$id]); break;
            case 'news:add': if ($P('title') !== '') $d->prepare('INSERT INTO news(title,body) VALUES(?,?)')->execute([$P('title'), $P('body')]); break;
            case 'news:del': $d->prepare('DELETE FROM news WHERE id=?')->execute([$id]); break;
            case 'hero:save': foreach (['hero_t', 'hero_s'] as $k) $d->prepare('REPLACE INTO settings(k,v) VALUES(?,?)')->execute([$k, $P($k)]); break;
            case 'users:add': if (isset(ROLE[$P('role')]) && $P('login') !== '' && strlen($_POST['pass'] ?? '') >= 8) $d->prepare('INSERT INTO users(login,pass_hash,role) VALUES(?,?,?)')->execute([$P('login'), password_hash($_POST['pass'], PASSWORD_DEFAULT), $P('role')]); break;
            case 'users:pass': if (strlen($_POST['pass'] ?? '') >= 8) $d->prepare('UPDATE users SET pass_hash=? WHERE id=?')->execute([password_hash($_POST['pass'], PASSWORD_DEFAULT), $id]); break;
            case 'users:del': if ($id !== (int)$u['id']) $d->prepare('DELETE FROM users WHERE id=?')->execute([$id]); break;
        } } catch (PDOException $x) {}
        header('Location: ./?t=' . $t); exit;
    }
}
?><!doctype html><html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex"><title>Админ-панель</title><link rel="stylesheet" href="../assets/style.css"></head><body>
<div class="adm"><div class="pn" style="width:100%;animation:none">
<?php if (!$u): ?>
<h2>Вход в админ-панель</h2>
<form method="post"><label>Логин<input name="login" required autocomplete="username"></label><label>Пароль<input name="pass" type="password" required autocomplete="current-password"></label><p class="msg err"><?= h($err) ?></p><button class="btn">Войти</button></form>
<?php else:
$perm = PERM[$u['role']]; $t = in_array($_GET['t'] ?? '', $perm, true) ? $_GET['t'] : $perm[0];
$nw = (int)$d->query("SELECT COUNT(*) FROM requests WHERE status='new'")->fetchColumn(); ?>
<div class="row"><b>Админ-панель</b><span class="mu"><?= h($u['login']) ?> · <?= ROLE[$u['role']] ?> · <a href="../">Сайт</a> · <a href="?out=1">Выйти</a></span></div>
<div class="tabs"><?php foreach ($perm as $k): ?><a class="<?= $k === $t ? 'on' : '' ?>" href="?t=<?= $k ?>"><?= TAB[$k] ?><?= $k === 'requests' && $nw ? " ($nw)" : '' ?></a><?php endforeach ?></div>

<?php if ($t === 'requests'): foreach ($d->query('SELECT * FROM requests ORDER BY id DESC LIMIT 200') as $r): ?>
<div class="rq <?= $r['status'] ?>"><div class="row" style="border:0;padding:0"><b><?= h($r['name']) ?></b><small><?= h($r['created']) ?></small></div>
<div><a href="tel:<?= h(preg_replace('/[^\d+]/', '', $r['phone'])) ?>"><?= h($r['phone']) ?></a> · <?= h($r['topic']) ?></div>
<?php if ($r['comment']): ?><p class="mu" style="margin:4px 0"><?= nl2br(h($r['comment'])) ?></p><?php endif ?>
<form method="post" class="ra"><?= hid($t) ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><span class="st"><?= SN[$r['status']] ?></span>
<?php if ($r['status'] === 'new'): ?><button name="a" value="work">В работу</button><?php endif ?>
<?php if ($r['status'] !== 'done'): ?><button name="a" value="done">Закрыть</button><?php endif ?>
<?php if ($u['role'] === 'admin'): ?><button name="a" value="del" onclick="return confirm('Удалить заявку?')">Удалить</button><?php endif ?></form></div>
<?php endforeach; if (!$nw && !$d->query('SELECT 1 FROM requests LIMIT 1')->fetch()) echo '<p class="mu">Заявок пока нет.</p>'; endif ?>

<?php if ($t === 'prices'): foreach ($d->query('SELECT * FROM prices ORDER BY sort,id') as $r): ?>
<form method="post" class="ra"><?= hid($t) ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><input name="name" value="<?= h($r['name']) ?>" style="width:100px"><input name="value" type="number" step="0.1" value="<?= h($r['value']) ?>" style="width:110px"><button name="a" value="save">Сохранить</button><button name="a" value="del">Удалить</button></form>
<?php endforeach ?>
<h3>Добавить</h3><form method="post" class="ra"><?= hid($t) ?><input name="name" placeholder="Название" required style="width:120px"><input name="value" type="number" step="0.1" placeholder="Цена" required style="width:110px"><button name="a" value="add">Добавить</button></form><?php endif ?>

<?php if ($t === 'stations'): foreach ($d->query('SELECT * FROM stations ORDER BY id') as $r): ?>
<form method="post" class="row"><?= hid($t) ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><span><b><?= h($r['name']) ?></b><br><small><?= h($r['status']) ?> · <?= h($r['lat']) ?>, <?= h($r['lon']) ?></small></span><span class="ra"><button name="a" value="del">Удалить</button></span></form>
<?php endforeach ?>
<h3>Добавить АЗС</h3><form method="post"><?= hid($t) ?><label>Название<input name="name" required></label><label>Описание<input name="descr"></label><label>Режим работы<input name="status"></label><label>Широта<input name="lat" type="number" step="0.0001" required placeholder="62.03"></label><label>Долгота<input name="lon" type="number" step="0.0001" required placeholder="129.73"></label><button class="btn" name="a" value="add">Добавить</button></form><?php endif ?>

<?php if ($t === 'notices'): foreach ($d->query('SELECT * FROM notices ORDER BY id DESC') as $r): ?>
<form method="post" class="row"><?= hid($t) ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><span><small><?= h($r['created']) ?></small><br><?= h($r['text']) ?></span><span class="ra"><button name="a" value="del">Удалить</button></span></form>
<?php endforeach ?>
<h3>Новое объявление</h3><form method="post"><?= hid($t) ?><label><textarea name="text" rows="2" required></textarea></label><button class="btn" name="a" value="add">Опубликовать</button></form><?php endif ?>

<?php if ($t === 'news'): foreach ($d->query('SELECT * FROM news ORDER BY id DESC') as $r): ?>
<form method="post" class="row"><?= hid($t) ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><span><small><?= h($r['created']) ?></small><br><b><?= h($r['title']) ?></b></span><span class="ra"><button name="a" value="del">Удалить</button></span></form>
<?php endforeach ?>
<h3>Новая новость</h3><form method="post"><?= hid($t) ?><label>Заголовок<input name="title" required></label><label>Текст<textarea name="body" rows="3"></textarea></label><button class="btn" name="a" value="add">Добавить</button></form><?php endif ?>

<?php if ($t === 'hero'): $c = []; foreach ($d->query('SELECT k,v FROM settings') as $r) $c[$r['k']] = $r['v']; ?>
<form method="post"><?= hid($t) ?><label>Заголовок<input name="hero_t" value="<?= h($c['hero_t'] ?? '') ?>"></label><label>Подзаголовок<textarea name="hero_s" rows="3"><?= h($c['hero_s'] ?? '') ?></textarea></label><button class="btn" name="a" value="save">Сохранить</button></form><?php endif ?>

<?php if ($t === 'users'): foreach ($d->query('SELECT id,login,role FROM users ORDER BY id') as $r): ?>
<form method="post" class="ra" style="padding:6px 0"><?= hid($t) ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><b style="min-width:110px"><?= h($r['login']) ?></b><span class="mu" style="min-width:110px"><?= ROLE[$r['role']] ?></span><input type="password" name="pass" placeholder="новый пароль, от 8 символов" style="width:220px"><button name="a" value="pass">Сменить пароль</button><?php if ($r['id'] != $u['id']): ?><button name="a" value="del" onclick="return confirm('Удалить пользователя?')">Удалить</button><?php endif ?></form>
<?php endforeach ?>
<h3>Новый пользователь</h3><form method="post"><?= hid($t) ?><label>Логин<input name="login" required></label><label>Пароль (от 8 символов)<input name="pass" type="password" required minlength="8"></label><label>Роль<select name="role"><?php foreach (ROLE as $k => $v): ?><option value="<?= $k ?>"><?= $v ?></option><?php endforeach ?></select></label><button class="btn" name="a" value="add">Создать</button></form><?php endif ?>
<?php endif ?>
</div></div></body></html>
