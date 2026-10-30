<?php
require 'config.php';
need('Admin');
$msg = '';
if (isset($_POST['add'])) {
    try {
        $pdo->prepare("INSERT INTO staff(name,email,password,role) VALUES(?,?,?,'Security')")->execute([trim($_POST['name']),trim($_POST['email']),password_hash($_POST['password'], PASSWORD_DEFAULT)]);
        $msg = 'Staff account added.';
    } catch (PDOException $e) {
        $msg = 'That email is already used.';
    }
}
if (isset($_POST['toggle']) && $_POST['toggle'] != $_SESSION['u']['id']) {
    $pdo->prepare("UPDATE staff SET active=1-active WHERE id=?")->execute([$_POST['toggle']]);
}
top('Admin');
if ($msg) {
    echo '<div class="msg ok">'.h($msg).'</div>';
}
?>
<form method="post" class="card grid">
    <h2>Add security staff</h2>
    <label>Name<input name="name" required></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Password<input type="password" name="password" minlength="6" required></label>
    <button name="add">Add staff</button>
</form>
<div class="card scroll">
    <h2>Staff accounts</h2>
    <table>
        <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr>
<?php foreach ($pdo->query("SELECT * FROM staff") as $r) {
    echo '<tr><td>'.h($r['name']).'</td><td>'.h($r['email']).'</td><td>'.$r['role'].'</td><td>'.($r['active'] ? 'Active' : 'Deactivated').'</td><td>'.($r['id'] != $_SESSION['u']['id'] ? '<form method="post"><button class="alt" name="toggle" value="'.$r['id'].'">'.($r['active'] ? 'Deactivate' : 'Activate').'</button></form>' : '').'</td></tr>';
}
?>
    </table>
</div>
<p>
    <a class="btn alt" href="visitors.php">Go to all visitors</a>
    <a class="btn alt" href="history.php">All entry/exit records</a>
</p>
<?php bottom();
