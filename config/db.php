<?php

if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $user = 'root';
    $pass = '';
    $db   = 'yii2basic';
} else {
    $user = 'u653416335_user';
    $pass = 'Andrew22Wiggins';
    $db   = 'u653416335_words';
}

return [
    'class' => 'yii\db\Connection',
    'dsn' => "mysql:host=localhost;dbname={$db}",
    'username' => $user,
    'password' => $pass,
    'charset' => 'utf8',
];
