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

//Проверяем был ли заполнен email в форме обновляем данные
if ($_POST['email']) {

    $userid=$_POST['id'];
    $email=$_POST['email'];
    $password = hash('md5', $_POST['password']);

    //обновляем данные и выходим на главную
    if(edit_credential($userid, $email, $password)) {
        set_flash_message("success", "Информация о пользователе успешно обновлена!");
        redirect_to("index.php");
    }
}

// Ищем редактируемого пользователя в БД, если есть выводим форму с текущей информацией
$user = get_user_by_id($_GET['id']);

if (!$user) {
    set_flash_message('alert', '<strong>Уведомление!</strong><br> Пользователь не найден!');
    redirect_to("page_login.php");
}

include_once "header.php";
?>
<main id="js-page-content" role="main" class="page-content mt-3">
    <? if ($_SESSION["name"]) display_flash_message($_SESSION["name"]) ?>
    <div class="subheader">
        <h1 class="subheader-title">
            <i class='subheader-icon fal fa-lock'></i> Безопасность
        </h1>

    </div>
    <form action="" method="POST">
        <div class="row">
            <div class="col-xl-6">
                <div id="panel-1" class="panel">
                    <div class="panel-container">
                        <div class="panel-hdr">
                            <h2>Обновление эл. адреса и пароля</h2>
                        </div>
                        <div class="panel-content">
                            <!-- email -->
                            <div class="form-group">
                                <label class="form-label" for="simpleinput">Email</label>
                                <input type="text" id="simpleinput" name="email" class="form-control"
                                       value="<?= $user['email'] ?>">
                                <input type="hidden"  name="id" value="<?= $user['id'] ?>">
                            </div>

                            <!-- password -->
                            <div class="form-group">
                                <label class="form-label" for="simpleinput">Пароль</label>
                                <input type="password" id="simpleinput" name="password" class="form-control">
                            </div>

                            <!-- password confirmation-->
                            <div class="form-group">
                                <label class="form-label" for="simpleinput">Подтверждение пароля</label>
                                <input type="password" id="simpleinput" name="password2" class="form-control">
                            </div>


                            <div class="col-md-12 mt-3 d-flex flex-row-reverse">
                                <button class="btn btn-warning">Изменить</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</main>

<script src="js/vendors.bundle.js"></script>
<script src="js/app.bundle.js"></script>
<script>

    $(document).ready(function () {

        $('input[type=radio][name=contactview]').change(function () {
            if (this.value == 'grid') {
                $('#js-contacts .card').removeClassPrefix('mb-').addClass('mb-g');
                $('#js-contacts .col-xl-12').removeClassPrefix('col-xl-').addClass('col-xl-4');
                $('#js-contacts .js-expand-btn').addClass('d-none');
                $('#js-contacts .card-body + .card-body').addClass('show');

            } else if (this.value == 'table') {
                $('#js-contacts .card').removeClassPrefix('mb-').addClass('mb-1');
                $('#js-contacts .col-xl-4').removeClassPrefix('col-xl-').addClass('col-xl-12');
                $('#js-contacts .js-expand-btn').removeClass('d-none');
                $('#js-contacts .card-body + .card-body').removeClass('show');
            }

        });

        //initialize filter
        initApp.listFilter($('#js-contacts'), $('#js-filter-contacts'));
    });

</script>
</body>
</html>