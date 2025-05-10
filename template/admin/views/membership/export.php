<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Экспорт участников</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/memberusers/">Участники</a>
        </li>
        <li>Экспорт участников</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/export.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Экспорт</h3>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="export" value="Экспортировать" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a href="<?php echo $setting['script_url'];?>/admin/memberusers/">Закрыть</a></li>
            </ul>
        </div>

    <div class="admin_form">
        <h4 class="h4-border"><?php echo System::Lang('BASIC');?></h4>
        <div class="row-line">
        <div class="col-1-2">
            <div class="width-100"><label>Кого эскпортируем?</label>
                <div class="select-wrap">
                    <select name="type">
                    <option value="all">Всех участников</option>
                </select>
                </div>
            </div>
            <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
        </div>
        </div>
    </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>