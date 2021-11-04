<?php
session_start();
include_once 'functions.php';

// Проверки на право редактировать юзера
if (not_authorize()) {
    set_flash_message('alert', '<strong>Уведомление!</strong><br> Пожалуйста, автризуйтесь');
    redirect_to("page_login.php");
}

//если не админ и если не автор - на выход
if (!(is_admin() || $_SESSION['user']['id'] == $_GET['id'])) {
    set_flash_message('alert', '<strong>Уведомление!</strong><br> Не хватает прав доступа');
    redirect_to("page_login.php");
}

delete($_GET['id']);

if ($_SESSION['user']['id']<>$_GET['id']) {
    set_flash_message("success", "Пользователь успешно удален!");
    redirect_to("index.php");
}

logout();