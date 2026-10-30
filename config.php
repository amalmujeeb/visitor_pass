<?php

session_start();
date_default_timezone_set('Asia/Colombo'); // change if you are in another timezone
try {
    $pdo = new PDO('mysql:host=localhost;dbname=visitor_pass;charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    $pdo->exec("SET time_zone='+05:30'");
} catch (PDOException $e) {
    http_response_code(500);
    $why = strpos($e->getMessage(), 'Unknown database') !== false
      ? 'The "visitor_pass" database has not been created yet. Import schema.sql in phpMyAdmin first.'
      : (strpos($e->getMessage(), 'Access denied') !== false
        ? 'The database username/password in config.php is wrong for your MySQL server.'
        : 'Could not reach MySQL. Make sure MySQL is started in your XAMPP/server control panel.');
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Setup needed | Visitor Pass</title></head>
 <body style="font-family:Segoe UI,system-ui,sans-serif;background:#0f2238;color:#fff;display:grid;place-items:center;min-height:100vh;margin:0;padding:20px">
 <div style="background:#fff;color:#0f2238;max-width:480px;padding:32px;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.4)">
 <h1 style="margin:0 0 10px;font-size:1.3rem">Database not ready</h1>
 <p style="margin:0 0 14px;color:#b3261e;font-weight:600">'.htmlspecialchars($why).'</p>
 <p style="margin:0;color:#64748b;font-size:.9rem">Technical detail: '.htmlspecialchars($e->getMessage()).'</p>
 </div></body></html>';
    exit;
}
function h($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES);
}
function need($role = null)
{
    if (empty($_SESSION['u'])) {
        header('Location: index.php');
        exit;
    }
    if ($role && $_SESSION['u']['role'] !== $role) {
        http_response_code(403);
        exit('Access denied: admin only');
    }
}
function top($t)
{
    $u = $_SESSION['u'];
    $p = basename($_SERVER['PHP_SELF']);
    $l = ['dashboard.php' => ['Live dashboard','M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z'],
     'register.php' => ['Register visitor','M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'],
     'scan.php' => ['Scan gate','M9.5 6.5v3h-3v-3h3M11 5H5v6h6V5zm-1.5 9.5v3h-3v-3h3M11 13H5v6h6v-6zm6.5-6.5v3h-3v-3h3M19 5h-6v6h6V5zm-6 8h1.5v1.5H13V13zm1.5 1.5H16V16h-1.5v-1.5zM16 13h1.5v1.5H16V13zm-3 3h1.5v1.5H13V16zm1.5 1.5H16V19h-1.5v-1.5zM16 16h1.5v1.5H16V16zm1.5-1.5H19V16h-1.5v-1.5zm0 3H19V19h-1.5v-1.5zM22 7h-2V4h-3V2h5v5zm0 15v-5h-2v3h-3v2h5zM2 22h5v-2H4v-3H2v5zM2 2v5h2V4h3V2H2z'],
     'history.php' => ['History','M13 3a9 9 0 00-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42A8.954 8.954 0 0013 21a9 9 0 000-18zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z']];
    if ($u['role'] == 'Admin') {
        $l['visitors.php'] = ['All visitors','M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z'];
        $l['admin.php'] = ['Staff accounts','M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z'];
    }
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.h($t).' | Visitor Pass</title><link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="assets/style.css?v=3"></head><body>';
    echo '<div class="topbar"><button id="mb" type="button" aria-label="Open menu">&#9776;</button><b>Visitor Pass</b></div>';
    echo '<aside class="side" id="side"><div class="logo"><span class="mark">VP</span><span>Visitor Pass<small>Gate management</small></span></div><nav>';
    foreach ($l as $f => $n) {
        echo '<a href="'.$f.'"'.($p == $f || ($p == 'pass.php' && $f == 'register.php') ? ' class="on"' : '').'><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="'.$n[1].'"/></svg>'.$n[0].'</a>';
    }
    echo '</nav><div class="user"><span class="av">'.h(strtoupper(substr($u['name'], 0, 1))).'</span><span class="un">'.h($u['name']).'<small>'.h($u['role']).'</small></span><a href="logout.php">Logout</a></div></aside><div class="ov" id="ov"></div><main><h1>'.h($t).'</h1>';
}
function bottom()
{
    echo '</main><script>(function(){var b=document.body,f=function(){b.classList.toggle("open")};
document.getElementById("mb").onclick=f;document.getElementById("ov").onclick=f;
document.querySelectorAll("table").forEach(function(t){var h=[].map.call(t.querySelectorAll("th"),function(x){return x.textContent});
t.querySelectorAll("tr").forEach(function(r){[].forEach.call(r.children,function(c,i){if(c.tagName=="TD"&&h[i])c.setAttribute("data-l",h[i])})})})})()</script></body></html>';
}
