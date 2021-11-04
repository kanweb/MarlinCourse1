<?php
session_start();
include_once 'functions.php';

// Проверки на право редактировать юзера
if (not_authorize()) {
    set_flash_message('alert', '<strong>Уведомление!</strong><br> Пожалуйста, автризуйтесь');
    redirect_to("page_login.php");
}

logout();