<?php

if ($spid->isAuthenticated())
{
    foreach ($spid->getAttributes() as $key => $attr) {
        echo $key . ' - ' . $attr . '<br>';
    }

    echo "<a href=\"/\">Home</a>";
}
else
{
    echo "Not logged in! <br>";
    echo "<a href=\"/login\">Login</a>";
}

?>