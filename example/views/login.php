<?php
if(!$spid->login("testenv", "", "")) {
    echo "Already logged in! <br>";
    echo "<a href=\"/\">Home</a>";
}
?>