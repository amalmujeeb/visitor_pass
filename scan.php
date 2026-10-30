<?php
require 'config.php';
need();
top('Gate scan');
?>
<div class="card">
    <div class="seg">
        <label><input type="radio" name="mode" value="entry" checked>Entry</label>
        <label><input type="radio" name="mode" value="exit">Exit</label>
    </div>
    <div id="msg" class="msg" hidden></div>
    <label class="camlabel">
        Camera
        <select id="camsel"><option value="">Loading cameras…</option></select>
    </label>
    <button id="droidcam" class="alt cam-choice" type="button">Use DroidCam / phone camera</button>
    <div id="reader"></div>
    <div class="row">
        <button id="cam" type="button">Start camera</button>
        <label class="btn alt">
            Scan from photo
            <input id="file" type="file" accept="image/*" hidden>
        </label>
    </div>
    <p class="hint">Tip: to scan using your phone's camera while it's on the desk, connect the phone to this laptop with a USB cable and turn on its "USB camera / webcam" mode (or a webcam app like Iriun/DroidCam), then pick it from the Camera list above.</p>
    <h2 style="margin-top:20px">QR not working? Manual entry / exit</h2>
    <div class="row">
        <input id="vid" type="text" placeholder="Visitor NIC number (e.g. 991234567V)" maxlength="12" style="text-transform:uppercase">
        <button id="man" type="button">Record manually</button>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
const $ = id => document.getElementById(id);
const mode = () => document.querySelector('[name=mode]:checked').value;
let busy = false;
let running = false;
let last = '';
let lastT = 0;
const rd = new Html5Qrcode('reader');
function show(ok, t) {
    const m = $('msg');
    m.hidden = false;
    m.className = 'msg ' + (ok ? 'ok' : 'bad');
    m.textContent = t;
    m.scrollIntoView({block: 'nearest'});
}
async function send(method,value){
    if (busy) return;
    if (method == 'qr' && value === last && Date.now() - lastT < 6000) return;
    busy = true;
    if (method == 'qr') {
        last = value;
        lastT = Date.now();
    }
    const b = new FormData();
    b.append('mode', mode());
    b.append('method', method);
    b.append('value', value);
    try {
        const r = await (await fetch('action.php', {method: 'POST', body: b})).json();
        show(r.ok, r.msg);
        if (r.ok && method == 'manual') $('vid').value = '';
        if (navigator.vibrate) navigator.vibrate(r.ok ? 100 : [80, 60, 80]);
    } catch (e) {
        show(false, 'Network error. Try again.');
    }
    busy = false;
}
async function loadCameras() {
    try {
        const cams = await Html5Qrcode.getCameras();
        const sel = $('camsel');
        sel.innerHTML = '';
        if (!cams.length) {
            sel.innerHTML = '<option value="">No camera found</option>';
            return;
        }
        cams.forEach((c, i) => {
            const o = document.createElement('option');
            o.value = c.id;
            o.textContent = c.label || ('Camera ' + (i + 1));
            sel.appendChild(o);
        });
        const phone = cams.find(c => /usb|webcam|iriun|droidcam|camo|iphone|android/i.test(c.label));
        const back = cams.find(c => /back|rear|environment/i.test(c.label));
        sel.value = (phone || back || cams[0]).id;
    } catch (e) {
        $('camsel').innerHTML = '<option value="">Camera permission needed — click Start camera</option>';
    }
}
async function chooseDroidCam() {
    await loadCameras();
    const sel = $('camsel');
    const option = [...sel.options].find(o => /droidcam|usb|webcam|iriun|camo|android|iphone/i.test(o.textContent));
    if (!option || !option.value) {
        show(false, 'DroidCam camera not found. Connect the phone, open DroidCam, then try again.');
        return;
    }
    sel.value = option.value;
    show(true, option.textContent + ' selected. Click Start camera to scan.');
    if (running) {
        await rd.stop();
        running = false;
        $('cam').textContent = 'Start camera';
        $('cam').click();
    }
}
loadCameras();
$('droidcam').onclick = chooseDroidCam;
$('cam').onclick = async () => {
    if (running) {
        await rd.stop();
        running = false;
        $('cam').textContent = 'Start camera';
        return;
    }
    const id = $('camsel').value;
    try {
        await rd.start(id || {facingMode: 'environment'}, {fps: 10, qrbox: 220}, t => send('qr', t));
        running = true;
        $('cam').textContent = 'Stop camera';
        await loadCameras();
    } catch (e) {
        running = false;
        show(false, 'Camera unavailable. It needs HTTPS or localhost and camera permission. Use "Scan from photo" or manual entry instead.');
    }
};
$('camsel').onchange = async () => {
    if (running) {
        await rd.stop();
        running = false;
        $('cam').click();
    }
};
$('man').onclick = () => send('manual', $('vid').value);
$('vid').onkeydown = e => {
    if (e.key == 'Enter') send('manual', $('vid').value);
};
$('file').onchange = async e => {
    const f = e.target.files[0];
    if (!f) return;
    try {
        const t = await rd.scanFile(f, false);
        last = '';
        await send('qr', t);
    } catch (x) {
        show(false, 'No QR code found in that photo. Try a clearer, closer photo.');
    }
    e.target.value = '';
};
</script>
<?php bottom();
