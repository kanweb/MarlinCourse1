<?php
session_start();
include_once 'functions.php';

$email = $_POST['email'];
$password = hash('md5', $_POST['password']);

if (!login($email, $password)) {
    redirect_to("page_login.php");
}
redirect_to("index.php");


