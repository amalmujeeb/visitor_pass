<?php

// Run once: http://localhost/visitor_pass/install.php
$p = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$p->exec("CREATE DATABASE IF NOT EXISTS visitor_pass CHARACTER SET utf8mb4;USE visitor_pass;
CREATE TABLE IF NOT EXISTS staff(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100) NOT NULL,email VARCHAR(120) NOT NULL UNIQUE,password VARCHAR(255) NOT NULL,role ENUM('Admin','Security') NOT NULL,active TINYINT DEFAULT 1);
CREATE TABLE IF NOT EXISTS visitors(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100) NOT NULL,phone VARCHAR(20) NOT NULL,email VARCHAR(120),nic VARCHAR(20) NOT NULL UNIQUE,purpose VARCHAR(200) NOT NULL,host VARCHAR(120) NOT NULL,pass_code CHAR(32) NOT NULL UNIQUE,status ENUM('Active','Used','Expired') DEFAULT 'Active',created_by INT,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS visits(id INT AUTO_INCREMENT PRIMARY KEY,visitor_id INT NOT NULL,entry_time DATETIME NOT NULL,exit_time DATETIME NULL,entry_method ENUM('qr','manual') NOT NULL,exit_method ENUM('qr','manual') NULL,entry_by INT NOT NULL,exit_by INT NULL,FOREIGN KEY(visitor_id) REFERENCES visitors(id));");
$p->exec("USE visitor_pass");
$columns = $p->query("SHOW COLUMNS FROM visitors LIKE 'nic'")->fetch();
if (!$columns) {
	$p->exec("ALTER TABLE visitors ADD nic VARCHAR(20) NULL AFTER email");
	$p->exec("ALTER TABLE visitors ADD UNIQUE KEY unique_visitor_nic (nic)");
}
$s = $p->prepare("INSERT IGNORE INTO staff(name,email,password,role) VALUES(?,?,?,?)");
$s->execute(['Administrator','admin@vpass.com',password_hash('admin123', PASSWORD_DEFAULT),'Admin']);
$s->execute(['Gate Guard','guard@vpass.com',password_hash('guard123', PASSWORD_DEFAULT),'Security']);
echo 'Installed. <a href="index.php">Go to login</a><br>admin@vpass.com / admin123<br>guard@vpass.com / guard123';
