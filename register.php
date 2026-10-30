<?php require 'config.php';
need();
$id = (int)($_GET['id'] ?? 0);
$v = ['name' => '','phone' => '','email' => '','nic' => '','purpose' => '','host' => ''];
$err = '';
$locked = false;
if ($id) {
    $s = $pdo->prepare("SELECT * FROM visitors WHERE id=?");
    $s->execute([$id]);
    $v = $s->fetch() ?: exit('Visitor not found');
    $locked = (bool)$pdo->query("SELECT COUNT(*) FROM visits WHERE visitor_id=$id")->fetchColumn();
}
if ($_POST && !$locked) {
    foreach ($v as $k => $_) {
        $v[$k] = trim($_POST[$k] ?? '');
    }$v['nic'] = strtoupper($v['nic']);
    if (!$v['name'] || !$v['purpose'] || !$v['host']) {
        $err = 'Name, purpose and host are required.';
    } elseif (!preg_match('/^[0-9+\- ]{7,15}$/', $v['phone'])) {
        $err = 'Enter a valid phone number (7-15 digits).';
    } elseif ($v['email'] && !filter_var($v['email'], FILTER_VALIDATE_EMAIL)) {
        $err = 'Enter a valid email address.';
    } elseif (!preg_match('/^([0-9]{9}[vVxX]|[0-9]{12})$/', $v['nic'])) {
        $err = 'Enter a valid NIC number (old format: 9 digits + V, or new format: 12 digits).';
    } else {
        try {
            if ($id) {
                $pdo->prepare("UPDATE visitors SET name=?,phone=?,email=?,nic=?,purpose=?,host=? WHERE id=?")->execute([$v['name'],$v['phone'],$v['email'],$v['nic'],$v['purpose'],$v['host'],$id]);
            } else {
                $pdo->prepare("INSERT INTO visitors(name,phone,email,nic,purpose,host,pass_code,created_by) VALUES(?,?,?,?,?,?,?,?)")->execute([...array_values($v),bin2hex(random_bytes(16)),$_SESSION['u']['id']]);
                $id = $pdo->lastInsertId();
            }
            header("Location: pass.php?id=$id");
            exit;
        } catch (PDOException $e) {
            $err = (strpos($e->getMessage(), 'nic') !== false) ? 'This NIC number is already registered (visitor #'.($pdo->query("SELECT id FROM visitors WHERE nic=".$pdo->quote($v['nic']))->fetchColumn()).').' : 'Could not save. Please check the details and try again.';
        }
    }
}
top($id ? 'Edit visitor' : 'Register visitor');
if ($locked) {
    echo '<div class="msg bad">Entry already recorded, so details can no longer be edited.</div>';
}
if ($err) {
    echo '<div class="msg bad">'.h($err).'</div>';
}
?>
<form method="post" class="card grid">
<?php foreach (['name' => 'Full name','nic' => 'NIC number','phone' => 'Phone','email' => 'Email (optional)','purpose' => 'Purpose of visit','host' => 'Host name / department'] as $k => $l) {
    $ph = $k == 'nic' ? ' placeholder="200012345678 or 991234567V"' : '';
    echo '<label>'.$l.'<input name="'.$k.'" value="'.h($v[$k]).'"'.$ph.' '.($locked ? 'disabled' : '').'></label>';
}
?>
    <button <?=$locked ? 'disabled' : ''?>><?=$id ? 'Save changes' : 'Register and generate pass'?></button>
</form>
<?php bottom();
