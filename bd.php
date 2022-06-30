<?php
// Соединение с БД
$user = 'u40077';
$password = '3053723';
$db = new PDO('mysql:host=localhost;dbname=u40077', $user, $password, array(PDO::ATTR_PERSISTENT => true));
?>

