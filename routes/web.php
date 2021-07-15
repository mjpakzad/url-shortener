<?php
$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) == 'cgi') {
    header("Status: 404 Not Found");
}
else {
    header("HTTP/1.1 404 Not Found");
}

include 'views/404.php';