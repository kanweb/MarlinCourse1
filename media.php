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
if (isset($_FILES['image'])) {
    $image = $_FILES["image"];
//print_r($image);
    $userid = $_POST['id'];


    //обновляем данные и выходим на главную
    upload_avatar($userid, $image);
    set_flash_message("success", "Информация о пользователе успешно обновлена!");
    redirect_to("index.php");

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
    <div class="subheader">
        <h1 class="subheader-title">
            <i class='subheader-icon fal fa-image'></i> Загрузить аватар
        </h1>

    </div>
    <form method='post' action="media.php" enctype="multipart/form-data">
        <div class="row">
            <div class="col-xl-6">
                <div id="panel-1" class="panel">
                    <div class="panel-container">
                        <div class="panel-hdr">
                            <h2>Текущий аватар</h2>
                        </div>
                        <div class="panel-content">
                            <div class="form-group">
                                <img src="<?= ($user["avatar"]) ?: "/img/demo/avatars/avatar-m.png" ?>" alt=""
                                     class="img-responsive" width="200">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="example-fileinput">Выберите аватар</label>
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <input type="file" id="example-fileinput" name="image" class="form-control-file">
                            </div>


                            <div class="col-md-12 mt-3 d-flex flex-row-reverse">
                                <button class="btn btn-warning">Загрузить</button>
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