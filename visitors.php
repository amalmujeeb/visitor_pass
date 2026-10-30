<?php
require 'config.php';
need('Admin');
top('All visitors');
$n = trim($_GET['name'] ?? '');
$nc = strtoupper(trim($_GET['nic'] ?? ''));
$ho = trim($_GET['host'] ?? '');
$w = ['1'];
$a = [];
if ($n) {
    $w[] = 'v.name LIKE ?';
    $a[] = "%$n%";
}if ($nc) {
    $w[] = 'v.nic LIKE ?';
    $a[] = "%$nc%";
}if ($ho) {
    $w[] = 'v.host LIKE ?';
    $a[] = "%$ho%";
}
$s = $pdo->prepare("SELECT v.*,(SELECT COUNT(*) FROM visits WHERE visitor_id=v.id) n,(SELECT COUNT(*) FROM visits WHERE visitor_id=v.id AND exit_time IS NULL) inside FROM visitors v WHERE ".implode(' AND ', $w)." ORDER BY v.id DESC");
$s->execute($a);
$rows = $s->fetchAll();
?>
<form class="card filters">
    <input name="name" placeholder="Visitor name" value="<?=h($n)?>">
    <input name="nic" placeholder="NIC number" value="<?=h($nc)?>" style="text-transform:uppercase">
    <input name="host" placeholder="Host" value="<?=h($ho)?>">
    <button>Search</button>
</form>
<div class="card scroll visitors-card">
    <h2>All registered visitors (<?=count($rows)?>)</h2>
    <table class="visitors-table">
        <tr><th>ID</th><th>Name</th><th>NIC</th><th>Phone</th><th>Host</th><th>Registered</th><th>Status</th><th></th></tr>
<?php foreach ($rows as $r) {
    $st = $r['inside'] ? 'Inside' : ($r['n'] ? 'Exited' : 'Not entered yet');
    echo '<tr><td><a href="visitor.php?id='.$r['id'].'">#'.$r['id'].'</a></td><td class="visitor-name">'.h($r['name']).'</td><td>'.h($r['nic']).'</td><td>'.h($r['phone']).'</td><td>'.h($r['host']).'</td><td class="registered">'.$r['created_at'].'</td><td><span class="pill '.($r['inside'] ? 'in' : '').'">'.$st.'</span></td><td class="visitor-actions"><a href="pass.php?id='.$r['id'].'">Pass</a><a href="visitor.php?id='.$r['id'].'">Profile</a></td></tr>';
}
if (!$rows) {
    echo '<tr><td colspan="8" class="muted">No visitors match.</td></tr>';
}
?>
    </table>
</div>
<?php bottom();
