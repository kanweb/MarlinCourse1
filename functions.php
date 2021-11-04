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
    if ($name == "success") {
        echo '<div class="alert alert-success">' . $_SESSION["message"] . '</div>';
    }
    if ($name == "alert") {
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

function not_authorize()
{
    if ($_SESSION['user'] && !empty($_SESSION['user'])) return false; else return true;
}

function is_admin()
{
    if ($_SESSION['user']['role'] == 'admin') return true; else return false;
}

function get_all_user()
{
    $link = db_connect();
    $sth = $link->prepare("SELECT * FROM users ORDER BY id DESC;");
    $sth->execute();
    $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    if ($result) return $result; else return false;
}

/*
    Description:
        Поиск пользователя по email
    Parameters:
        string - $email
    Return value:
        array
 */
function get_user_by_id($id)
{
    $link = db_connect();
    $sth = $link->prepare("SELECT * FROM users where id=:id;");
    $sth->execute(array('id' => $id));
    $result = $sth->fetch(PDO::FETCH_ASSOC);
    if ($result) return $result; else return false;
}


/*
    Description:
        Добавление нового пользователя через админку
    Parameters:
        string - $email
                 $password
                 ........
    Return value:
        int (user id), false в случае ошибки
 */
function edit_user_information($userid, $username, $job, $phone, $address)
{
    $link = db_connect();
    $sth = $link->prepare('UPDATE users SET username=:username,phone=:phone,address=:address,job=:job WHERE id=:id;');
    $sth->execute(array('id' => $userid, 'username' => $username, 'phone' => $phone, 'address' => $address, 'job' => $job));
}


function set_status($userid, $status)
{
    $link = db_connect();
    $sth = $link->prepare('UPDATE users SET status=:status WHERE id=:id;');
    $sth->execute(array('id' => $userid, 'status' => $status));

}

function upload_avatar($userid, $image)
{
    $target_dir = 'img/avatars/';
    $target_file = $target_dir . $image["name"];
    move_uploaded_file($image["tmp_name"], $target_file);

    $link = db_connect();

    $sth = $link->prepare("SELECT avatar FROM users where id=:id;");
    $sth->execute(array('id' => $userid));
    $result = $sth->fetch(PDO::FETCH_ASSOC);
    if ($result["avatar"] && file_exists($result["avatar"])) {
        unlink($result["avatar"]);
    }

    $sth = $link->prepare('UPDATE users SET avatar=:avatar WHERE id=:id;');
    $sth->execute(array('id' => $userid, 'avatar' => $target_file));

}

function add_social_links($userid, $telegram, $instagram, $vk)
{
    $link = db_connect();
    $sth = $link->prepare('UPDATE users SET telegram=:telegram,instagram=:instagram,vk=:vk WHERE id=:id;');
    $sth->execute(array('id' => $userid, 'telegram' => $telegram, 'instagram' => $instagram, 'vk' => $vk));
}

function edit_credential($userid, $email, $password)
{
    $user = get_user_by_email($email);

    //Проверяем занят ли этот емайл игнорируем совпадения email с редактируемым пользователем
    if ($user['email'] && $user['id'] <> $userid) {
        set_flash_message("alert", "<strong>Уведомление!</strong><br> Этот эл. адрес уже занят другим пользователем!");
        redirect_to("security.php?id=" . $userid);
    }

    $link = db_connect();
    $sth = $link->prepare('UPDATE users SET email=:email,password=:password WHERE id=:id;');
    $sth->execute(array('id' => $userid, 'email' => $email, 'password' => $password));

    return true;

}

function delete($userid)
{
    $user = get_user_by_id($userid);
    if (file_exists($user['avatar'])) {
        unlink($user['avatar']);
    }

    $link = db_connect();
    $sth = $link->prepare("DELETE FROM users WHERE id = :id");
    $sth->execute(array('id' => $userid));
}

function logout()
{
    session_unset();
    session_destroy();
    redirect_to("page_login.php");
    exit();
}