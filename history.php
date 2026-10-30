<?php
require 'config.php';
need();
$n = trim($_GET['name'] ?? '');
$nc = trim($_GET['vnic'] ?? '');
$ho = trim($_GET['host'] ?? '');
$f = $_GET['from'] ?? '';
$t = $_GET['to'] ?? '';
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
if ($f) {
    $w[] = 'DATE(t.entry_time)>=?';
    $a[] = $f;
}if ($t) {
    $w[] = 'DATE(t.entry_time)<=?';
    $a[] = $t;
}
$s = $pdo->prepare("SELECT v.id vid,v.name,v.host,t.entry_time,t.exit_time,t.entry_method,t.exit_method,a.name ein,b.name eout FROM visits t JOIN visitors v ON v.id=t.visitor_id JOIN staff a ON a.id=t.entry_by LEFT JOIN staff b ON b.id=t.exit_by WHERE ".implode(' AND ', $w)." ORDER BY t.entry_time DESC LIMIT 500");
$s->execute($a);
$rows = $s->fetchAll();
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
if (($_GET['export'] ?? '') === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="visit-history.csv"');
    $out = fopen('php://output', 'w');
    // Add a UTF-8 BOM so spreadsheet applications display names correctly.
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Visit ID', 'Visitor', 'Host', 'Entry time', 'Entry method', 'Entry by', 'Exit time', 'Exit method', 'Exit by', 'Duration']);
    foreach ($rows as $r) {
        fputcsv($out, [$r['vid'], $r['name'], $r['host'], $r['entry_time'], $m($r['entry_method']), $r['ein'], $r['exit_time'] ?: 'Still inside', $m($r['exit_method']), $r['eout'] ?: '-', $duration($r['entry_time'], $r['exit_time'])]);
    }
    fclose($out);
    exit;
}
top('Visitor history');
?>
<form class="card idlookup" action="visitor.php">
    Find visitor by NIC number
    <div class="row">
        <input type="text" name="nic" placeholder="e.g. 991234567V or 200012345678" required style="text-transform:uppercase">
        <button>Open visitor</button>
    </div>
</form>
<form class="card filters">
    <h2>Filter visit records</h2>
    <input name="name" placeholder="Visitor name" value="<?=h($n)?>">
    <input name="vnic" placeholder="NIC number" value="<?=h($nc)?>">
    <input name="host" placeholder="Host" value="<?=h($ho)?>">
    <input type="date" name="from" value="<?=h($f)?>">
    <input type="date" name="to" value="<?=h($t)?>">
    <button>Search</button>
    <button type="submit" name="export" value="csv" formtarget="_blank">Download CSV</button>
</form>
<div class="card scroll">
    <table>
        <tr><th>ID</th><th>Visitor</th><th>Host</th><th>Entry</th><th>Entry by</th><th>Exit</th><th>Exit by</th><th>Duration</th></tr>
<?php foreach ($rows as $r) {
    echo '<tr><td><a href="visitor.php?id='.$r['vid'].'">#'.$r['vid'].'</a></td><td>'.h($r['name']).'</td><td>'.h($r['host']).'</td><td>'.$r['entry_time'].'<br><small>'.$m($r['entry_method']).'</small></td><td>'.h($r['ein']).'</td><td>'.($r['exit_time'] ?: '<i>Still inside</i>').'<br><small>'.$m($r['exit_method']).'</small></td><td>'.h($r['eout'] ?: '-').'</td><td>'.h($duration($r['entry_time'], $r['exit_time'])).'</td></tr>';
}
if (!$rows) {
    echo '<tr><td colspan="8" class="muted">No records match these filters.</td></tr>';
}
?>
    </table>
</div>
<?php bottom();
