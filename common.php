<?php 
// adjust this here to your needs

$db_path = __DIR__ . "/bbs.db";

function gen_captcha(): void
{
    $a = random_int(1,10);
    $b = random_int(1,10);
    $_SESSION["captcha_answer"] = $a * $b;
    $_SESSION["captcha_question"] = "$a * $b = ?";
}

function check_csrf(): void
{
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        http_response_code(403);
        exit('csrf');
    }
}

function h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

?>
