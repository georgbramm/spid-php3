<?php
if(!$spid->login("testenv", 0, 0)) {
    echo "Already logged in! <br>";
    echo "<a href=\"/\">Home</a>";
}
?>