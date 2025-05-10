<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('DELIVERY_VARIANTS');?></h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Варианты доставки</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li><a class="button-yellow-rounding" href="/admin/deliverysettings/add/">Создать новый способ</a></li>
        </ul>
    </div>

    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <div class="extension">

            <?php if($velivery_methods){
            foreach($velivery_methods as $method):?>
            <div class="extension-item <?php if($method['status'] == 0) echo ' off'?>">
                <div class="extension-img">
                    <!--img src="/template/admin/images/delivery.png"-->
                </div>
                <div class="extension-center">
                    <h4><a href="<?php echo $setting['script_url'];?>/admin/deliverysettings/edit/<?php echo $method['method_id'];?>"><?php echo $method['title'];?></a></h4>
                </div>
                <div class="extension-status">
                    <div class="ext-status <?php if($method['status'] == 1) echo 'on'; else echo 'off';?>"></div>
                </div>
            </div>
            <?php endforeach;
            } else echo 'Нет пунктов меню';?>

    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

</body>
</html>