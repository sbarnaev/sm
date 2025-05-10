<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1>Создать уровень доступа</h1>
    <div class="logout">
        <a href="/" target="_blank">На сайт >></a>   | <a href="/admin/logout" class="red">Выход</a>
    </div>
    <form action="" method="POST" enctype="multipart/form-data">
    <div class="nav_gorizontal">
        <input type="submit" name="add" value="Сохранить" class="button save button-green-rounding">
        <a class="button button-red-rounding" href="/admin/membersubs">Закрыть</a>
    </div>
        
        <div class="admin_form">
            <!-- 1 вкладка -->

                <div class="box2">
                    <h4>Основное</h4>
                    <p><label>Название: </label><input type="text" name="name" placeholder="Название уровня" required="required"></p>
                    
                    <p><label>Описание:</label><textarea name="desc" cols="35" rows="3"></textarea></p>
                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>

        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>