<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Код формы подписки</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout/" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/responder/auto">Автосерии</a></li>
        <li>Код формы подписки</li>
    </ul>

    <div class="admin_top admin_top-flex">
        <h3 class="traning-title mb-0">Код формы подписки</h3>
        <ul class="nav_button">
            <li class="nav_button__last"><a class="button red-link" href="/admin/responder/auto/">Закрыть</a></li>
        </ul>
    </div>
        <div class="admin_form">
        <textarea style="min-height: 310px;" cols="80" rows="15"><?php echo $html; ?></textarea>


        </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
<link rel="stylesheet" type="text/css" href="/template/admin/css/jquery.datetimepicker.min.css">
<script src="/template/admin/js/jquery.datetimepicker.full.min.js"></script>
<script>
jQuery('.datetimepicker').datetimepicker({
format:'d.m.Y H:i',
lang:'ru'
});
</script>
</body>
</html>