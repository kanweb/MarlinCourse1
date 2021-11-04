<?php
session_start();
include_once 'functions.php';
if (!is_admin()) {
    set_flash_message('alert', '<strong>Уведомление!</strong><br> Ошибка доступа');
    redirect_to("index.php");
}

//print_r($_FILES);
//die();
$email = $_POST['email'];
$user = get_user_by_email($email);

if ($user) {
    set_flash_message("alert", "<strong>Уведомление!</strong><br> Этот эл. адрес уже занят другим пользователем!");
    redirect_to("create_user.php");
}

$password = hash('md5', $_POST['password']);
$userid = add_user($email,$password);

if (!$userid) {
    set_flash_message("alert", "<strong>Уведомление!</strong><br> Ошибка создания пользователя!");
    redirect_to("create_user.php");
}

$username=$_POST['username'];
$job=$_POST['job'];;
$phone=$_POST['phone'];
$address=$_POST['address'];

edit_user_information($userid, $username, $job, $phone, $address);

$status=$_POST['status'];

set_status($userid, $status);

if ($_FILES["avatar"]) upload_avatar($userid, $_FILES["avatar"]);

$telegram=$_POST['telegram'];
$instagram=$_POST['instagram'];
$vk=$_POST['vk'];

add_social_links($userid, $telegram, $instagram, $vk);

set_flash_message("success", "Пользователь успешно добавлен!");
redirect_to("index.php");

