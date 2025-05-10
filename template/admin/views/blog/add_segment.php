<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1>Создать сегмент</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    
    <form action="" method="POST">
    <div class="nav_gorizontal">
        <ul>
            <li><input type="submit" name="add" value="Сохранить" class="button save button-green-rounding"></li>
            <li><a class="button button-red-rounding" href="/admin/segments">Закрыть</a></li>
        </ul>


    </div>
    
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" placeholder="Название сегмента" required="required"></p>

                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>

                <div class="col-1-2">
                    <h4>Описание</h4>
                    <p class="width-100">Описание:<br /><textarea name="desc" rows="3" cols="40"></textarea></p>
                </div>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>