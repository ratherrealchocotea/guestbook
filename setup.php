#!/usr/bin/env php
<?php 

$db = new SQLite3(__DIR__ . "/bbs.db");
$db->exec("
           CREATE TABLE IF NOT EXISTS posts(
           id INTEGER PRIMARY KEY AUTOINCREMENT,
           subject TEXT NOT NULL,
           name TEXT NOT NULL,
           body TEXT NOT NULL
           )
");
$db->exec("
           CREATE TABLE IF NOT EXISTS admin_stuff(
           admin_passwd_hash TEXT NOT NULL 
           )
");


$p = readline("Enter your desired admin password: ");
$p = password_hash($p, PASSWORD_ARGON2ID);
$stmt = $db->prepare(
    "INSERT INTO admin_stuff(admin_passwd_hash) VALUES(:aph)"
);
$stmt->bindValue(":aph", $p, SQLITE3_TEXT);
$stmt->execute();

echo "Delete this file immediately (will find a better solution later)\n";

exit();

?>
