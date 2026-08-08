<!doctype html>
<html>
    <head>
        <title>Please login</title>
        <meta charset="utf-8">
        <link  rel="stylesheet" type="text/css" href="./style.css" id="theme-css" />
    </head>
    <body>
        <h1>Please Log-in</h1>
        <div>
            <form action="login.php" method="post">
                <label for="password">P:</label>
                <input type="password" name="p" id="p">
            </form>
        </div>
    </body>
</html>
<?php 
session_start();

include "common.php";

if($_SERVER["REQUEST_METHOD"] === "POST") {
    $db = new SQLITE3($db_path);
    $h = $db->querySingle("SELECT admin_passwd_hash FROM admin_stuff", true);
    if($h === false) { 
        exit("Password hash missing"); 
    }
    $h_single = $h["admin_passwd_hash"];
    if(isset($_POST["p"])) {
        $pw = $_POST["p"];
        $check = password_verify($pw, $h_single);
        if($check) {
            session_regenerate_id(true);
            $_SESSION["valid"] = true;
            header("Location: deletion.php");
            exit();
        } else if(!$check) {
            session_regenerate_id(true);
            $_SESSION["valid"] = false;
            header("Location: login.php");
            exit();
        }
    }
}
?>

