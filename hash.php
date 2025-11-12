<?php
// Вводим желаемый пароль
$password = '123';
echo password_hash($password, PASSWORD_DEFAULT);