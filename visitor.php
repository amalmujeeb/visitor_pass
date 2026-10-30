<?php
require 'config.php';
need();
$nic = strtoupper(trim($_GET['nic'] ?? ''));
$id = (int)($_GET['id'] ?? 0);
if ($nic) {
    $s = $pdo->prepare("SELECT * FROM visitors WHERE nic=?");
    $s->execute([$nic]);
} else {
    $s = $pdo->prepare("SELECT * FROM visitors WHERE id=?");
    $s->execute([$id]);
}
$v = $s->fetch();
top($v ? ('Visitor #'.$v['id']) : 'Visitor not found');
if (!$v) {
    echo '<div class="msg bad">No visitor found for '.($nic ? ('NIC "'.h($nic).'"') : ('ID '.$id)).'.</div>';
    bottom();
    exit;
}
$id = $v['id'];
$open = $pdo->prepare("SELECT COUNT(*) FROM visits WHERE visitor_id=? AND exit_time IS NULL");
$open->execute([$id]);
$isIn = $open->fetchColumn();
$vs = $pdo->prepare("SELECT t.*,a.name ein,b.name eout FROM visits t JOIN staff a ON a.id=t.entry_by LEFT JOIN staff b ON b.id=t.exit_by WHERE t.visitor_id=? ORDER BY t.entry_time DESC");
$vs->execute([$id]);
$rows = $vs->fetchAll();
$m = fn ($x) => $x ? ($x == 'qr' ? 'QR scan' : 'Manual') : '-';
$duration = function ($entry, $exit) {
    if (!$exit) {
        return 'In progress';
    }
    $minutes = max(0, (int)floor((strtotime($exit) - strtotime($entry)) / 60));
    $days = intdiv($minutes, 1440);
    $hours = intdiv($minutes % 1440, 60);
    $mins = $minutes % 60;
    return ($days ? $days.'d ' : '').($hours ? $hours.'h ' : '').$mins.'m';
};
?>
<div class="card profile">
    <div class="pf-main">
        <div class="profile-head">
            <span class="pf-id">ID <b>#<?=$v['id']?></b></span>
            <h2 class="pf-name"><?=h($v['name'])?></h2>
            <span class="pill <?=$isIn ? 'in' : ''?>"><?=$isIn ? 'Inside now' : 'Not inside'?></span>
        </div>
        <div class="pf-grid">
            <div><small>NIC</small><?=h($v['nic'])?></div>
            <div><small>Phone</small><?=h($v['phone'])?></div>
            <div><small>Email</small><?=h($v['email'] ?: '-')?></div>
            <div><small>Host</small><?=h($v['host'])?></div>
            <div><small>Purpose</small><?=h($v['purpose'])?></div>
            <div><small>Pass status</small><?=$v['status']?></div>
            <div><small>Registered</small><?=$v['created_at']?></div>
        </div>
    </div>
    <div class="pf-actions">
        <a class="btn" href="pass.php?id=<?=$v['id']?>">View / print pass</a>
        <a class="btn alt" href="register.php?id=<?=$v['id']?>">Edit details</a>
    </div>
</div>
<div class="card scroll">
    <h2>Visit history for #<?=$v['id']?> (<?=count($rows)?> visit<?=count($rows) == 1 ? '' : 's'?>)</h2>
    <table>
        <tr><th>Visit</th><th>Entry</th><th>Entry by</th><th>Exit</th><th>Exit by</th><th>Duration</th></tr>
<?php foreach ($rows as $r) {
    echo '<tr><td>#'.$r['id'].'</td><td>'.$r['entry_time'].'<br><small>'.$m($r['entry_method']).'</small></td><td>'.h($r['ein']).'</td><td>'.($r['exit_time'] ?: '<i>Still inside</i>').'<br><small>'.$m($r['exit_method']).'</small></td><td>'.h($r['eout'] ?: '-').'</td><td>'.h($duration($r['entry_time'], $r['exit_time'])).'</td></tr>';
}
if (!$rows) {
    echo '<tr><td colspan="6" class="muted">No visits recorded yet for this visitor.</td></tr>';
}
?>
    </table>
</div>
<?php bottom();
