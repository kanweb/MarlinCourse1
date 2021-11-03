<?php
session_start();
include_once 'functions.php';

$email = $_POST['email'];
$user = get_user_by_email($email);

if ($user) {
    set_flash_message("alert", "<strong>Уведомление!</strong><br> Этот эл. адрес уже занят другим пользователем!");
    redirect_to("page_register.php");
}

$password = hash('md5', $_POST['password']);
$userid = add_user($email, $password);

if (!$userid) {
    set_flash_message("alert", "<strong>Уведомление!</strong><br> Ошибка регистрации!");
    redirect_to("page_register.php");
}

set_flash_message("success", "Регистрация успешна!<br> Авторизуйтесь.");
redirect_to("page_login.php");

