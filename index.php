<?php
declare(strict_types=1);
include "common.php";
session_start();
if (!isset($_SESSION["captcha_answer"])) {
    gen_captcha();
}
header("Content-Type: text/html; charset=UTF-8");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");
header("Content-Security-Policy: default-src 'self'; object-src 'none'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'");

$db=new SQLite3($db_path);
$db->busyTimeout(5000);
$db->exec("PRAGMA journal_mode=WAL");
$db->exec("PRAGMA foreign_keys=ON");


if (empty($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION["captcha_answer"])) {
    new_captcha();
}

if(isset($_POST['post'])) {
    check_csrf();
    if (
        !isset($_POST["captcha"]) ||
        (int)$_POST["captcha"] !== $_SESSION["captcha_answer"]
    ) {
        gen_captcha();
        exit("Wrong CAPTCHA.");
    }
    $subject = strips_tags(trim($_POST['subject'] ?? ''));
    $name = strip_tags(trim($_POST['name'] ?? ''));
    $body = strip_tags(trim($_POST['body'] ?? ''));

    if ($name === '') { $name = 'Your friendly neighbourhood\'s Anon';}
    
    if(
        ($body === "") ||
        ($subject === "") ||
        mb_strlen($subject) > 80 ||
        mb_strlen($name) > 30 ||
        mb_strlen($body) > 4000
    ) {
        exit('Invalid Input. Go check if your text does not exceed 4000 chars, or the name 30, or the subject 80, or whether they are empty.');
    }
    
    $date = date("Y-m-d H:i:s");  
    
    $stmt = $db->prepare(
        "INSERT INTO posts(date,subject,name,body) VALUES(:d, :s,:n,:b)"
    );

    $stmt->bindValue(":d", $date, SQLITE3_TEXT);
    $stmt->bindValue(":s", $subject, SQLITE3_TEXT);
    $stmt->bindValue(":n", $name, SQLITE3_TEXT);
    $stmt->bindValue(":b", $body, SQLITE3_TEXT);
    $stmt->execute();
    
    header("Location: " .$_SERVER["PHP_SELF"]);
    exit;
}


$posts = $db->query(
    'SELECT * FROM POSTS ORDER BY id DESC'
);

?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Eternal-Monologue Comment Segment</title>
        <link  rel="stylesheet" type="text/css" href="./style.css" id="theme-css" />
    </head>
    <body>
        <h1>Eternal-Monologue Comment Segment</h1>
        <form method="post">
            <input type="hidden" name="csrf" value="<?=h($_SESSION["csrf"])?>">
            <input name="subject" maxlength="80" placeholder="Subject" required>
            <br><br>
            <input name="name" maxlength="30" placeholder="Name">
            <br><br>
            <textarea name="body" maxlength="4000" required></textarea>

            <label> CAPTCHA: <?=h($_SESSION["captcha_question"])?> </label>
            <input type="number" name="captcha" required>
            <br>
            <button name="post">Post</button>
        </form>
        <hr>
        <?php while ($p = $posts->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="post">
                <b><?=h($p["subject"])?></b>
                <br>
                <small><?=h($p["name"])?></small>
                <small><?=h($p["date"])?></small>
                <p><?=nl2br(h($p["body"]))?></p>
                
            </div>
            <hr>
        <?php endwhile; ?>
    </body>
</html>
