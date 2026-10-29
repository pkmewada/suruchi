<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/../includes/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$data = home_all(); // existing data is saved to DB here on first open
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Home Page Editor | Suruchi Creation</title>
<style>
:root{--purple:#4b2862;--maroon:#8e254b;--bg:#f6f2f7;--line:#e5dce8;--ink:#251c2c;--muted:#786d7d}
*{box-sizing:border-box}
body{margin:0;font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;background:var(--bg);color:var(--ink);font-size:14px}
.layout{display:grid;grid-template-columns:240px 1fr;min-height:100vh}
aside{background:linear-gradient(160deg,#3e2253,#652751 60%,#9d2b42);color:#fff;padding:24px 16px;position:sticky;top:0;height:100vh;overflow:auto}
aside h1{font-size:16px;margin:0 0 4px}aside small{color:#ffffffb0}
aside nav{margin-top:22px;display:grid;gap:4px}
aside nav button{all:unset;cursor:pointer;padding:10px 12px;border-radius:10px;color:#ffffffd0;font-size:13px}
aside nav button:hover{background:#ffffff1f}aside nav button.active{background:#fff;color:var(--purple);font-weight:700}
.side-links{margin-top:26px;display:grid;gap:8px;font-size:12px}.side-links a{color:#fff;opacity:.85}
main{padding:28px clamp(16px,4vw,48px);max-width:980px}
.top{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:18px;flex-wrap:wrap}
.top h2{margin:0;font-size:22px}
.card{background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px;margin-bottom:16px}
.field{margin-bottom:14px}
.field>label{display:block;font-size:12px;font-weight:700;margin-bottom:6px;color:#413449}
input[type=text],textarea{width:100%;border:1px solid var(--line);border-radius:10px;padding:10px 12px;font:inherit;background:#fff}
textarea{min-height:90px;resize:vertical}
input:focus,textarea:focus{outline:2px solid #8e254b30;border-color:var(--maroon)}
.img-row{display:flex;gap:10px;align-items:center}
.img-row img{width:64px;height:48px;object-fit:cover;border-radius:8px;border:1px solid var(--line);background:#eee;flex:0 0 auto}
.list-item{border:1px dashed #cdbfd3;border-radius:12px;padding:14px;margin-bottom:12px;background:#fcfafd}
.item-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;font-weight:700;font-size:12px;color:var(--maroon)}
.item-head div{display:flex;gap:6px}
button.btn{border:0;border-radius:10px;padding:9px 14px;font:inherit;font-weight:700;cursor:pointer;background:var(--purple);color:#fff}
button.btn.light{background:#efe6f2;color:var(--purple)}button.btn.sm{padding:5px 9px;font-size:12px}
button.btn.danger{background:#fde7ea;color:#a3213a}
.save-bar{position:sticky;bottom:0;background:#fffffff0;border-top:1px solid var(--line);padding:12px 0;display:flex;gap:12px;align-items:center}
.save-btn{background:linear-gradient(105deg,#4b2862,#762d62 56%,#b33b50)!important;padding:12px 26px!important}
#msg{font-size:13px;font-weight:600}.ok{color:#1a7f4b}.err{color:#a3213a}
.hint{font-size:11px;color:var(--muted);margin-top:4px}
@media(max-width:780px){.layout{grid-template-columns:1fr}aside{position:static;height:auto}aside nav{grid-template-columns:1fr 1fr}}
</style>
</head>
<body>
<div class="layout">
  <aside>
    <h1>Suruchi Creation</h1><small>Home page editor</small>
    <nav id="nav"></nav>
    <div class="side-links">
      <a href="../index.php" target="_blank">View website &#8599;</a>
      <a href="logout.php">Logout (<?= e($_SESSION['user_name'] ?? 'Admin') ?>)</a>
    </div>
  </aside>
  <main>
    <div class="top"><h2 id="title"></h2></div>
    <div id="form"></div>
    <div class="save-bar"><button class="btn save-btn" id="saveBtn">Save changes</button><span id="msg"></span></div>
  </main>
</div>

<script>
const CSRF = <?= json_encode($_SESSION['csrf_token']) ?>;
const DATA = <?= json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?>;

const F = {
  text: (k, l, t) => ({k, l, t: t || 'text'}),
  area: (k, l) => ({k, l, t: 'area'}),
  img:  (k, l) => ({k, l, t: 'image'}),
  list: (k, l, fields, itemLabel) => ({k, l, t: 'list', fields, itemLabel})
};
const SCHEMA = {
  banner: {label: 'Banner', fields: [
    F.text('vertical_text', 'Vertical title'), F.text('heading', 'Heading'), F.area('text', 'Paragraph'),
    F.text('btn_text', 'Button text'), F.text('btn_link', 'Button link'),
    F.list('slides', 'Slider images', [F.img('image', 'Image')], 'Slide')]},
  clients: {label: 'Client logos', fields: [
    F.list('logos', 'Logos (shown in two places on the page)', [F.img('image', 'Logo'), F.text('link', 'Link')], 'Logo')]},
  about: {label: 'About us', fields: [
    F.text('tagline', 'Small tagline'), F.text('heading', 'Heading'), F.area('text', 'Paragraph'),
    F.img('image1', 'Image 1 (small, left)'), F.img('image3', 'Image 2 (right)'), F.img('image2', 'Image 3 (bottom)'),
    F.text('btn1_text', 'Button 1 text'), F.text('btn1_link', 'Button 1 link'),
    F.text('btn2_text', 'Button 2 text'), F.text('btn2_link', 'Button 2 link')]},
  stats: {label: 'Highlights (moving cards)', fields: [
    F.list('items', 'Cards', [F.text('title', 'Title'), F.area('text', 'Text')], 'Card')]},
  marquee: {label: 'Big scrolling text', fields: [
    F.list('items', 'Lines', [F.text('text', 'Text')], 'Line')]},
  projects: {label: 'Latest projects', fields: [
    F.text('label', 'Side label'),
    F.list('items', 'Projects', [F.text('tagline', 'Tagline'), F.text('title', 'Title'), F.area('text', 'Description'),
      F.img('image', 'Image'), F.text('btn_text', 'Button text'), F.text('btn_link', 'Button link')], 'Project')]},
  services: {label: 'Services', fields: [
    F.text('tagline', 'Small tagline'), F.text('heading', 'Heading'),
    F.list('items', 'Accordion items', [F.text('title', 'Title'), F.area('text', 'Text')], 'Service')]},
  counters: {label: 'Counters', fields: [
    F.list('items', 'Counters', [F.text('title', 'Title'), F.text('text', 'Text'), F.text('number', 'Number')], 'Counter')]},
  team: {label: 'Team', fields: [
    F.list('items', 'Members', [F.text('name', 'Name'), F.text('role', 'Role'), F.img('image', 'Photo'),
      F.img('hover_image', 'Hover photo'), F.text('social_icon', 'Social icon class (e.g. fa-brands fa-instagram)'),
      F.text('social_link', 'Social link')], 'Member')]},
  testimonials: {label: 'Testimonials', fields: [
    F.text('heading', 'Heading'), F.img('bg_image', 'Background image'),
    F.list('items', 'Reviews', [F.area('text', 'Review (word between *stars* gets highlighted)'), F.text('name', 'Client name')], 'Review')]}
};

let current = Object.keys(SCHEMA)[0];
const $ = s => document.querySelector(s);
const el = (tag, props = {}, ...kids) => {
  const n = Object.assign(document.createElement(tag), props);
  kids.forEach(k => n.append(k));
  return n;
};
const prev = v => !v ? '' : (/^(https?:)?\/\//.test(v) ? v : '../' + v);

function renderFields(fields, obj) {
  const wrap = el('div');
  fields.forEach(f => wrap.append(renderField(f, obj)));
  return wrap;
}

function renderField(f, obj) {
  const box = el('div', {className: 'field'});
  box.append(el('label', {textContent: f.l}));

  if (f.t === 'list') {
    obj[f.k] = Array.isArray(obj[f.k]) ? obj[f.k] : [];
    const holder = el('div');
    const draw = () => {
      holder.innerHTML = '';
      obj[f.k].forEach((item, i) => {
        const card = el('div', {className: 'list-item'});
        const head = el('div', {className: 'item-head'}, el('span', {textContent: f.itemLabel + ' ' + (i + 1)}));
        const tools = el('div');
        const mk = (txt, cls, fn) => tools.append(el('button', {type: 'button', textContent: txt, className: 'btn sm ' + cls, onclick: fn}));
        mk('↑', 'light', () => { if (i > 0) { [obj[f.k][i-1], obj[f.k][i]] = [obj[f.k][i], obj[f.k][i-1]]; draw(); } });
        mk('↓', 'light', () => { if (i < obj[f.k].length - 1) { [obj[f.k][i+1], obj[f.k][i]] = [obj[f.k][i], obj[f.k][i+1]]; draw(); } });
        mk('Delete', 'danger', () => { if (confirm('Delete this item?')) { obj[f.k].splice(i, 1); draw(); } });
        head.append(tools); card.append(head, renderFields(f.fields, item));
        holder.append(card);
      });
    };
    draw();
    box.append(holder, el('button', {type: 'button', className: 'btn light', textContent: '+ Add ' + f.itemLabel, onclick: () => {
      const blank = {}; f.fields.forEach(x => blank[x.k] = ''); obj[f.k].push(blank); draw();
    }}));
    return box;
  }

  if (f.t === 'image') {
    const text = el('input', {type: 'text', value: obj[f.k] || '', placeholder: 'images/...'});
    const img = el('img', {src: prev(obj[f.k]), alt: ''});
    const file = el('input', {type: 'file', accept: 'image/jpeg,image/png,image/webp,image/gif', hidden: true});
    text.oninput = () => { obj[f.k] = text.value; img.src = prev(text.value); };
    file.onchange = async () => {
      if (!file.files[0]) return;
      const fd = new FormData(); fd.append('image', file.files[0]);
      try {
        const r = await fetch('../api/home.php?action=upload', {method: 'POST', headers: {'X-CSRF-Token': CSRF}, body: fd});
        const j = await r.json();
        if (!j.ok) throw new Error(j.error);
        text.value = obj[f.k] = j.path; img.src = prev(j.path);
      } catch (e) { alert('Upload failed: ' + e.message); }
    };
    box.append(el('div', {className: 'img-row'}, img, text,
      el('button', {type: 'button', className: 'btn light sm', textContent: 'Upload', onclick: () => file.click()}), file));
    return box;
  }

  const input = f.t === 'area' ? el('textarea') : el('input', {type: 'text'});
  input.value = obj[f.k] ?? '';
  input.oninput = () => obj[f.k] = input.value;
  box.append(input);
  return box;
}

function show(key) {
  current = key;
  $('#title').textContent = SCHEMA[key].label;
  $('#msg').textContent = '';
  $('#form').innerHTML = '';
  const card = el('div', {className: 'card'});
  card.append(renderFields(SCHEMA[key].fields, DATA[key]));
  $('#form').append(card);
  document.querySelectorAll('#nav button').forEach(b => b.classList.toggle('active', b.dataset.k === key));
}

Object.keys(SCHEMA).forEach(k => {
  const b = el('button', {textContent: SCHEMA[k].label, onclick: () => show(k)});
  b.dataset.k = k;
  $('#nav').append(b);
});

$('#saveBtn').onclick = async () => {
  const msg = $('#msg'); msg.className = ''; msg.textContent = 'Saving...';
  try {
    const r = await fetch('../api/home.php?action=save', {
      method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-Token': CSRF},
      body: JSON.stringify({section: current, data: DATA[current]})
    });
    const j = await r.json();
    if (!j.ok) throw new Error(j.error);
    msg.className = 'ok'; msg.textContent = 'Saved. Website par update ho gaya.';
  } catch (e) { msg.className = 'err'; msg.textContent = 'Error: ' + e.message; }
};

show(current);
</script>
</body>
</html>