<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
switch ($path) {
    case '/CheckUsername':
        require 'CheckUsername.php';
        break;
    case '/CreateTransaction':
        require 'CreateTransaction.php';
        break;
    case '/Login':
        require 'Login.php';
        break;
    case '/DeleteMRequest':
        require 'DeleteMRequest.php';
        break;
    case '/Refresh':
        require 'Refresh.php';
        break;
    case '/Register':
        require 'Register.php';
        break;
    case '/SearchforRequest':
        require 'SearchforRequest.php';
        break;
    case '/SendMRequest':
        require 'SendMRequest.php';
        break;
    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
?>