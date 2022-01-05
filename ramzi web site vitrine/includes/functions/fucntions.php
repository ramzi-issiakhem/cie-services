<?php
// change title of page
function getTitle()
{
    global $pageTitle;
    if (isset($pageTitle)) {
        echo $pageTitle;
    }
}

/*
** redirect after a few seconds (after readin errors)
** theMsg: type message alert, success 
*/
function redirectHome($theMsg, $url = null, $seconds = 3)
{
    if ($url === null)
        $url = "index.php";
    else {
        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== '')
            $url = $_SERVER['HTTP_REFERER'];
        else
            $url = "index.php";
    }
    echo $theMsg;
    echo "<div class='alert alert-info'>You will be redirected to homepage after $seconds seconds.</div>";
    header("refresh:$seconds;url=$url");
    exit();
}
