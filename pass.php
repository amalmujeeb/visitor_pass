<?php
require 'config.php';
need();
$s = $pdo->prepare("SELECT * FROM visitors WHERE id=?");
$s->execute([(int)$_GET['id']]);
$v = $s->fetch() ?: exit('Not found');
top('Visitor pass');
?>
<div class="pass card">
	<div class="tag">VISITOR</div>
	<div id="qr"></div>
	<h2><?=h($v['name'])?></h2>
	<div class="pass-details">
		<div><span>Visitor ID</span><b>#<?=$v['id']?></b></div>
		<div><span>NIC</span><b><?=h($v['nic'])?></b></div>
		<div><span>Host</span><strong><?=h($v['host'])?></strong></div>
		<div><span>Purpose</span><strong><?=h($v['purpose'])?></strong></div>
		<div><span>Status</span><strong class="muted"><?=$v['status']?></strong></div>
	</div>
</div>
<p class="actions">
	<button onclick="print()">Print pass</button>
	<a class="btn alt" href="register.php?id=<?=$v['id']?>">Edit details</a>
	<a class="btn alt" href="scan.php">Go to gate scan</a>
</p>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
	new QRCode(document.getElementById('qr'), {
		text: '<?=$v['pass_code']?>',
		width: 200,
		height: 200
	});
</script>
<?php bottom();
