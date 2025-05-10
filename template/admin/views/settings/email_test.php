<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Тестирование отправки почты</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>
    
    
    <div class="admin_form admin_form--margin-top">
        <div class="row-line">
        <div class="col-1-2">
            
            <?php if($error == null) echo '<p><strong>Письмо успешно отправлено!</strong><br />Осталось дождаться его в ящике. Это примерно 1-3 минуты<br />Папку спам тоже проверьте на всякий случай.</p>';
            else echo '<p style="color:#ec5f5f">'.$error.'</p>';?>
            
            <p><a href="/admin/settings">Вернуться назад</a></p>
            
            
        </div>
        </div>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>