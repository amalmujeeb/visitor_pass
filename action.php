<?php

require 'config.php';
need();
header('Content-Type: application/json');
function out($ok, $m)
{
    echo json_encode(['ok' => $ok,'msg' => $m]);
    exit;
}
$mode = $_POST['mode'] ?? '';
$method = $_POST['method'] ?? '';
$val = trim($_POST['value'] ?? '');
if ($method == 'manual') {
    $val = strtoupper($val);
}
if (!in_array($mode, ['entry','exit']) || !in_array($method, ['qr','manual']) || $val === '') {
    out(false, 'Invalid request.');
}
$s = $pdo->prepare('SELECT * FROM visitors WHERE '.($method == 'qr' ? 'pass_code' : 'nic').'=?');
$s->execute([$val]);
$v = $s->fetch();
if (!$v) {
    out(false, $method == 'qr' ? 'Invalid QR pass.' : 'No visitor found with that NIC number.');
}
$o = $pdo->prepare("SELECT id FROM visits WHERE visitor_id=? AND exit_time IS NULL");
$o->execute([$v['id']]);
$open = $o->fetchColumn();
$me = $_SESSION['u']['id'];
if ($mode == 'entry') {
    if ($open) {
        out(false, $v['name'].' is already inside (open visit). Entry rejected.');
    }
    if ($v['status'] == 'Expired') {
    out(false, 'Pass has expired. Register a new pass.');
    }
    $pdo->prepare("INSERT INTO visits(visitor_id,entry_time,entry_method,entry_by) VALUES(?,NOW(),?,?)")->execute([$v['id'],$method,$me]);
    out(true, 'Entry recorded: '.$v['name'].' ('.($method == 'qr' ? 'QR scan' : 'Manual').')');
}
if (!$open) {
    out(false, $v['name'].' has no open entry. Exit not recorded.');
}
$pdo->prepare("UPDATE visits SET exit_time=NOW(),exit_method=?,exit_by=? WHERE id=?")->execute([$method,$me,$open]);
$pdo->prepare("UPDATE visitors SET status='Used' WHERE id=?")->execute([$v['id']]);
out(true, 'Exit recorded: '.$v['name'].' ('.($method == 'qr' ? 'QR scan' : 'Manual').')');
