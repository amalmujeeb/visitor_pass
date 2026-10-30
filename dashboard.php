<?php
require 'config.php';
need();
top('Live dashboard');
$c = fn ($q) => $pdo->query($q)->fetchColumn();
$in = $pdo->query("SELECT v.id vid,v.name,v.host,v.purpose,v.phone,t.entry_time,t.entry_method FROM visits t JOIN visitors v ON v.id=t.visitor_id WHERE t.exit_time IS NULL ORDER BY t.entry_time DESC")->fetchAll();
$today = $pdo->query("SELECT v.id vid,v.name,v.host,t.entry_time,t.exit_time FROM visits t JOIN visitors v ON v.id=t.visitor_id WHERE DATE(t.entry_time)=CURDATE() ORDER BY t.entry_time DESC")->fetchAll();
$all = $pdo->query("SELECT v.*,(SELECT COUNT(*) FROM visits WHERE visitor_id=v.id) n,(SELECT COUNT(*) FROM visits WHERE visitor_id=v.id AND exit_time IS NULL) inside FROM visitors v ORDER BY v.id DESC")->fetchAll();
?>
<form class="card idlookup" action="visitor.php">
    Find visitor by NIC number
    <div class="row">
        <input type="text" name="nic" placeholder="e.g. 991234567V or 200012345678" required style="text-transform:uppercase">
        <button>Open visitor</button>
    </div>
</form>
<p class="date">
    <b><?=date('l, d F Y')?></b>
    <span class="muted" id="clk"></span>
</p>
<div class="stats">
    <div><b><?=count($in)?></b>Inside now</div>
    <div><b><?=count($today)?></b>Entries today</div>
    <div><b><?=$c("SELECT COUNT(*) FROM visits WHERE DATE(exit_time)=CURDATE()")?></b>Exits today</div>
    <div><b><?=$c("SELECT COUNT(*) FROM visitors WHERE DATE(created_at)=CURDATE()")?></b>Registered today</div>
    <div><b><?=count($all)?></b>All visitors</div>
</div>
<div class="card scroll">
    <h2>Currently on premises</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Visitor</th>
            <th>Host</th>
            <th>Purpose</th>
            <th>Phone</th>
            <th>Entered</th>
            <th>Method</th>
        </tr>
<?php foreach ($in as $r) {
    echo '<tr><td><a href="visitor.php?id='.$r['vid'].'">#'.$r['vid'].'</a></td><td>'.h($r['name']).'</td><td>'.h($r['host']).'</td><td>'.h($r['purpose']).'</td><td>'.h($r['phone']).'</td><td>'.$r['entry_time'].'</td><td>'.($r['entry_method'] == 'qr' ? 'QR scan' : 'Manual').'</td></tr>';
}
if (!$in) {
    echo '<tr><td colspan="7" class="muted">Nobody is inside right now.</td></tr>';
}?></table></div>
<div class="card scroll today-visits"><h2>Today's visits (<?=date('d M Y')?>)</h2><table><tr><th>ID</th><th>Visitor</th><th>Host</th><th>Entry</th><th>Exit</th></tr>
<?php foreach ($today as $r) {
    echo '<tr><td><a href="visitor.php?id='.$r['vid'].'">#'.$r['vid'].'</a></td><td>'.h($r['name']).'</td><td>'.h($r['host']).'</td><td>'.date('H:i', strtotime($r['entry_time'])).'</td><td>'.($r['exit_time'] ? date('H:i', strtotime($r['exit_time'])) : '<i>Still inside</i>').'</td></tr>';
}
if (!$today) {
    echo '<tr><td colspan="5" class="muted">No visits recorded today.</td></tr>';
}?></table></div>
<div class="card scroll"><h2>All registered visitors</h2><table><tr><th>ID</th><th>Visitor</th><th>Phone</th><th>Host</th><th>Registered</th><th>Status</th><th>Pass</th></tr>
<?php foreach ($all as $r) {
    $st = $r['inside'] ? 'Inside' : ($r['n'] ? 'Exited' : 'Not entered yet');
    echo '<tr><td><a href="visitor.php?id='.$r['id'].'">#'.$r['id'].'</a></td><td>'.h($r['name']).'</td><td>'.h($r['phone']).'</td><td>'.h($r['host']).'</td><td>'.$r['created_at'].'</td><td><span class="pill '.($r['inside'] ? 'in' : '').'">'.$st.'</span></td><td><a href="pass.php?id='.$r['id'].'">View pass</a> &middot; <a href="visitor.php?id='.$r['id'].'">Profile</a></td></tr>';
}
if (!$all) {
    echo '<tr><td colspan="7" class="muted">No visitors registered yet.</td></tr>';
}?></table></div>
<script>function updateClock(){document.getElementById('clk').textContent=new Date().toLocaleTimeString()}updateClock();setInterval(updateClock,1000);setTimeout(()=>location.reload(),20000)</script><?php bottom();
