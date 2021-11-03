<?php
/*
    Description:
        осуществляет подключение к БД
    Parameters:

    Return value:
        $connect - ссылка на подключение
 */
function db_connect()
{
    try {
        $link = new PDO('mysql:dbname=obuchenie;host=localhost', 'root', 'root');
    } catch (PDOException $e) {
        die($e->getMessage());
    }
    return $link;
}

/*
    Description:
        Поиск пользователя по email
    Parameters:
        string - $email
    Return value:
        array
 */
function get_user_by_email($email)
{
    $link = db_connect();
    $sth = $link->prepare("SELECT * FROM users where email=:email;");
    $sth->execute(array('email' => $email));
    $result = $sth->fetch(PDO::FETCH_ASSOC);
    if ($result) return $result; else return false;
}

/*
    Description:
        Добавление нового пользователя в БД
    Parameters:
        string - $email
                 $password
    Return value:
        int (user id)
 */
function add_user($email, $password)
{
    $link = db_connect();
    $sth = $link->prepare('INSERT INTO users (email, password) VALUES (:email, :password);');
    $sth->execute(array('email' => $email, 'password' => $password));
    $userid = $link->lastInsertId();
    if ($userid) return $userid; else return false;

}

/*
    Description:
        подготовить флеш сообщение
    Parameters:
        string - $name (ключ в сессии)
                 $message (текст сообщения)
    Return value:
        null
 */
function set_flash_message($name, $message)
{
    if ($name && $message) {
        $_SESSION['name'] = $name;
        $_SESSION['message'] = $message;
    }
}

/*
    Description:
        вывести флеш сообщение
    Parameters:
        string - $name (ключ в сессии)
    Return value:
        null
 */
function display_flash_message($name)
{
    if ($name=="success") {
        echo '<div class="alert alert-success">' . $_SESSION["message"] . '</div>';
    }
    if ($name=="alert") {
        echo '<div class="alert alert-danger text-dark" role="alert">' . $_SESSION["message"] . '</div>';
    }
    unset($_SESSION["name"]);
}

/*
    Description:
        перенаправляет на другую страницу
    Parameters:
        string - $path (адрес страницы)
    Return value:
        null
 */
function redirect_to($path)
{
    header('Location: ' . $path);
    exit();
}

/*
    Description:
        авторизует пользователя
    Parameters:
        string - $email, $password
    Return value:
        boolean
 */

function login($email, $password)
{
    $user = get_user_by_email($email);

    if (!$user) {
        set_flash_message("alert", "<strong>Уведомление!</strong><br> Такого пользователя не существует!");
        return false;
        exit();
    }

    if ($user["password"] <> $password) {
        set_flash_message("alert", "<strong>Уведомление!</strong><br> Ошибка ввода пароля!");
        return false;
        exit();
    }

    $_SESSION['user'] = $user;
    return true;

}