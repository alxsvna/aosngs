<?php
require __DIR__ . '/config.php';
$d = db(); $cfg = [];
foreach ($d->query('SELECT k,v FROM settings') as $r) $cfg[$r['k']] = $r['v'];
$prices = $d->query('SELECT * FROM prices ORDER BY sort,id')->fetchAll();
$notices = $d->query('SELECT * FROM notices ORDER BY id DESC LIMIT 5')->fetchAll();
$news = $d->query('SELECT * FROM news ORDER BY id DESC LIMIT 6')->fetchAll();
$stations = $d->query('SELECT name n, descr a, status t, lat, lon FROM stations')->fetchAll();
$dt = fn($s) => date('d.m.Y', strtotime($s));
?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>АО «Саханефтегазсбыт» — нефтепродукты Якутии</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;800&display=swap" rel="stylesheet">
<meta name="description" content="Основной участник рынка нефтепродуктов Республики Саха (Якутия): АЗС, нефтебазы, топливные карты, оптовые поставки.">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header><div class="w">
<a class="logo" href="#top">САХАНЕФТЕГАЗСБЫТ</a>
<nav><a href="#prices">Цены</a><a href="#services">Услуги</a><a href="#map">Карта АЗС</a><a href="#news">Новости</a><a href="#about">О компании</a><a href="#contacts">Контакты</a></nav>
<a class="btn o" href="admin/">Войти</a>
</div></header>
<main id="top">
<div class="hero"><i class="bl b1"></i><i class="bl b2"></i><div class="w">
<h1><?=h($cfg["hero_t"]??"")?></h1><p><?=h($cfg["hero_s"]??"")?></p>
<div class="b"><a class="btn w2" href="#prices">Цены на АЗС</a><a class="btn o" style="color:#fff;border-color:#fff" href="#services">Топливные карты</a></div>
</div></div>
<div class="w"><div class="stats">
<div class="stat"><b data-n="25" data-s=" лет">25 лет</b><span>на рынке с 2000 года</span></div>
<div class="stat"><b data-n="100" data-s="+">100+</b><span>АЗС в сети</span></div>
<div class="stat"><b data-n="19" data-s="">19</b><span>филиалов нефтебаз</span></div>
<div class="stat"><b>Арктика</b><span>юг, центр и север Якутии</span></div>
</div></div>
<section id="prices"><div class="w">
<h2>Цены на АЗС · Якутск</h2>
<div class="prices"><?php foreach($prices as $p):?><div class="pc"><b><?=h($p["name"])?></b><span><?=number_format($p["value"],1,".","")?> ₽</span></div><?php endforeach?></div>
<div style="margin-top:18px"><?php foreach($notices as $n):?><div class="nb"><small><?=$dt($n["created"])?></small><br><?=h($n["text"])?></div><?php endforeach?></div>
</div></section>
<section id="services" style="background:var(--sf)"><div class="w">
<h2>Услуги</h2>
<div class="g">
<div class="card"><div class="ic"><svg viewBox="0 0 24 24"><path d="M4 21V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v16M3 21h12M14 9h2a2 2 0 0 1 2 2v5a1.5 1.5 0 0 0 3 0V8l-3-3M6 8h6"/></svg></div><h3>Розница</h3><p class="mu">Бензин, дизельное топливо и метан на АЗС по всей республике.</p></div>
<div class="card"><div class="ic"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h4"/></svg></div><h3>Топливные карты</h3><p class="mu">Для юридических лиц и ИП. Личный кабинет клиента.</p></div>
<div class="card"><div class="ic"><svg viewBox="0 0 24 24"><ellipse cx="12" cy="6" rx="7" ry="3"/><path d="M5 6v12c0 1.7 3.1 3 7 3s7-1.3 7-3V6M5 12c0 1.7 3.1 3 7 3s7-1.3 7-3"/></svg></div><h3>Опт и мелкий опт</h3><p class="mu">Поставки нефтепродуктов по типовому договору.</p></div>
<div class="card"><div class="ic"><svg viewBox="0 0 24 24"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="M8.5 12l2.5 2.5 4.5-5"/></svg></div><h3>Техосмотр</h3><p class="mu">Станции технического осмотра транспорта.</p></div>
</div>
<p><a class="btn" href="#apply">Оставить заявку</a></p>
</div></section>
<section id="map" style="padding-bottom:16px"><div class="w">
<h2>Карта АЗС</h2>
<div class="mp"><div class="mv"><svg id="sv" viewBox="0 0 600 380" role="img" aria-label="Схема расположения АЗС"></svg></div><div id="sl" class="sl"></div></div>
<p class="mu" style="font-size:14px">Схематичная карта. Полный список станций — <a href="https://aosngs.ru/klientam/karta-azs/">на странице карты АЗС</a>.</p>
</div></section>
<section id="news"><div class="w"><h2>Пресс-центр</h2><div class="g"><?php foreach($news as $n):?><article class="card"><small><?=$dt($n["created"])?></small><h3><?=h($n["title"])?></h3><p class="mu"><?=h($n["body"])?></p></article><?php endforeach?></div></div></section>
<section id="about" style="background:var(--sf)"><div class="w">
<h2>О компании</h2>
<p style="max-width:720px">Гарантированный поставщик качественных нефтепродуктов в Республике Саха (Якутия). Обширная сеть АЗС и КАЗС, 19 филиалов нефтебаз, работа в южных, центральных и арктических районах.</p>
</div></section>
<section id="apply"><div class="w"><div class="ap"><div><h2>Оставить заявку</h2><p class="mu">Топливные карты, договор поставки, оптовые и мелкооптовые закупки. Мы свяжемся с вами в рабочее время.</p></div>
<form class="fm" action="apply.php" method="post" onsubmit="sendReq(event)">
<input name="hp" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px">
<label>Имя или организация<input name="nm" required autocomplete="name"></label>
<label>Телефон<input name="ph" type="tel" required autocomplete="tel" placeholder="+7 (___) ___-__-__"></label>
<label>Тема<select name="tp"><option>Топливные карты</option><option>Договор поставки нефтепродуктов</option><option>Оптовая реализация</option><option>Другое</option></select></label>
<label>Комментарий<textarea name="cm" rows="3"></textarea></label>
<label class="ck"><input type="checkbox" name="ok" value="1" required><span>Согласен на обработку <a href="https://aosngs.ru/upload/%D0%9F%D0%BE%D0%BB%D0%B8%D1%82%D0%B8%D0%BA%D0%B0_%D0%B2_%D0%BE%D1%82%D0%BD%D0%BE%D1%88%D0%B5%D0%BD%D0%B8%D0%B8_%D0%BE%D0%B1%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%BA%D0%B8_%D0%9F%D0%94%D0%BD.pdf">персональных данных</a></span></label>
<button class="btn" type="submit">Отправить заявку</button><p id="fm" class="msg"></p></form></div></div></section>
<section id="contacts" style="background:var(--sf)"><div class="w">
<h2>Контакты</h2>
<div class="g">
<div class="card"><h3>ЛК клиента</h3><p class="mu">+7 (914) 272-02-00</p><a href="https://sngs.lk.cloudio.ru">Войти в кабинет</a></div>
<div class="card"><h3>ЛК «Хранение»</h3><p class="mu">+7 (914) 272-97-78</p><a href="https://lk.aosngs.ru/">Войти в кабинет</a></div>
<div class="card"><h3>Мы в сети</h3><p><a href="https://t.me/ao_sngs">Telegram</a> · <a href="https://vk.com/ao_sngs">VK</a></p></div>
</div></div></section>
</main>
<footer><div class="w"><span>© 2026 АО «Саханефтегазсбыт». Все права защищены.</span><a href="https://aosngs.ru/">Полная версия сайта</a></div></footer>

<script>
const ST=<?=json_encode($stations,JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP)?>.map(s=>({...s,lat:+s.lat,lon:+s.lon}));
const $=s=>document.querySelector(s),e=s=>String(s).replace(/[&<>"]/g,c=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;"}[c]));
let sel=0;
const mx=lo=>40+(Math.min(145,Math.max(105,lo))-105)/40*520,my=la=>30+(74-Math.min(74,Math.max(55,la)))/19*320;
function drawMap(){
if(sel>=ST.length)sel=0;let g="";
for(let lo=105;lo<=145;lo+=10){const x=mx(lo);g+=`<line class="gl" x1="${x}" y1="20" x2="${x}" y2="360"/><text x="${x}" y="374" text-anchor="middle">${lo}\u00b0 в.д.</text>`}
for(let la=56;la<=72;la+=4){const y=my(la);g+=`<line class="gl" x1="30" y1="${y}" x2="570" y2="${y}"/><text x="34" y="${y-4}">${la}\u00b0 с.ш.</text>`}
ST.forEach((s,i)=>{const x=mx(s.lon),y=my(s.lat);g+=`<g class="pt${i===sel?" on":""}" onclick="pick(${i})"><circle class="r" cx="${x}" cy="${y}" r="8"/><circle class="c" cx="${x}" cy="${y}" r="7"/><text x="${x+13}" y="${y+4}" style="fill:var(--tx);font-weight:700">${e(s.n.split(",")[0])}</text></g>`});
$("#sv").innerHTML=g;
$("#sl").innerHTML=ST.map((s,i)=>`<button class="si${i===sel?" on":""}" onclick="pick(${i})"><b>${e(s.n)}</b><small>${e(s.a)}</small><small class="mu">${e(s.t)}</small></button>`).join("")}
function pick(i){sel=i;drawMap()}
async function sendReq(ev){ev.preventDefault();const f=ev.target,m=$("#fm");m.className="msg";m.textContent="Отправка...";
try{const r=await fetch("apply.php",{method:"POST",body:new FormData(f),headers:{"X-Requested-With":"fetch"}}),j=await r.json();m.textContent=j.msg;m.className="msg"+(j.ok?"":" err");if(j.ok)f.reset()}catch(_){m.textContent="Не удалось отправить, позвоните нам по телефону";m.className="msg err"}}
function cnt(b){const n=+b.dataset.n,s=b.dataset.s||"",t0=performance.now();(function f(t){const p=Math.min((t-t0)/1600,1);b.textContent=Math.round(n*(1-Math.pow(1-p,3)))+s;if(p<1)requestAnimationFrame(f)})(t0)}
const calm=matchMedia("(prefers-reduced-motion:reduce)").matches,IO="IntersectionObserver"in window&&!calm;
if(IO){const io2=new IntersectionObserver(es=>es.forEach(x=>{if(x.isIntersecting){io2.unobserve(x.target);cnt(x.target)}}),{threshold:.6});document.querySelectorAll("[data-n]").forEach(b=>io2.observe(b));
const io=new IntersectionObserver(es=>es.forEach(x=>{if(x.isIntersecting){const t=x.target;t.classList.add("in");io.unobserve(t);setTimeout(()=>{t.classList.remove("rv","in");t.style.transitionDelay=""},1000)}}),{threshold:.12});
document.querySelectorAll(".stat,.card,.pc,.nb,section h2").forEach((t,i)=>{t.style.transitionDelay=(i%4)*70+"ms";t.classList.add("rv");io.observe(t)})}
addEventListener("scroll",()=>$("header").classList.toggle("sc",scrollY>8),{passive:true});
if(ST.length)drawMap();
</script>
</body>
</html>
