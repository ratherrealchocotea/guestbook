<?php
 session_start();

 include "common.php";

 if(!isset($_SESSION["valid"])) {
     header("Location: login.php");
     exit();
 }

 if ($_SERVER["REQUEST_METHOD"] === "POST") {
     $db = new SQLite3($db_path);
     check_csrf();
     $stmt = $db->prepare(
         "DELETE FROM posts WHERE id = :id"
     );
     $stmt->bindValue(
         ":id",
         (int)$_POST["post_id"],
         SQLITE3_INTEGER
     );

     $stmt->execute();
     header("Location: " . $_SERVER["PHP_SELF"]);
     exit;
 }
 ?>
<!doctype html>
<html>
    <head> 
        <title>Deletion area</title>
        <meta charset="utf-8">
        <link  rel="stylesheet" type="text/css" href="./style.css" id="theme-css" />
    </head>
    <body>
        <p>You probably shouldn't be here, but you do you...</p>
        <form method="post">
            <input type="hidden" name="csrf" value="<?=h($_SESSION["csrf"])?>">
            <input type="number" name="post_id" placeholder="Post ID" required>
            <button type="submit">Moderation-Delete</button>
        </form>
    </body>
</html>
