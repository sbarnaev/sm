<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Расширения</h1>
        <div class="logout">
            <a href="<?=$setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?=$setting['script_url'];?>/admin/logout" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/settings/">Настройки</a></li>
        <li>Расширения</li>
    </ul>

    <div class="admin_form">
        <form action="" method="POST" enctype="multipart/form-data">
            <ul>
                <li class="search-row">
                    <span class="search-row mr-auto">Установить расширение (макс. <?=System::getPostMaxSize('mb');?> Мб)
                        <input type="file" name="extens" value="Выбрать">
                    </span>
                    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                    <input type="submit" class="button save button-green-rounding" name="install_ext" value="Установить">
                </li>
            </ul>
        </form>
    </div>

    <?php if(System::hasSuccess()) System::showSuccess();?>
    <?php if(System::hasError()) System::showError();?>

    <div class="extension">
        <?php if(isset($exts) && !empty($exts)):
            foreach($exts as $ext):?>
            <div class="extension-item">
                <div class="extension-img">
                    <img src="/template/admin/images/ext/<?=$ext['name'];?>.svg">
                </div>

                <div class="extension-center">
                    <h4><a href="<?=$setting['script_url'];?>/admin/<?=$ext['link'];?>"><?php $ext_name = $ext['title']; echo System::Lang("$ext_name");?></a></h4>
                </div>

                <div class="extension-status">
                    <?php $status = $ext['enable'] == 1 ? 'on' : 'off';?>
                    <div class="ext-status <?=$status;?>"></div>
                </div>
            </div>
            <?php endforeach;
        endif;?>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>