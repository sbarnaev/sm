<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Список платёжных модулей</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Список платёжных модулей</li>
    </ul>
    <div class="admin_form">

        <form action="" method="POST" enctype="multipart/form-data">
            <ul>
                <li class="search-row"><span class="search-row mr-auto">Установить новый модуль: <input type="file" name="payment" value="Установить"></span>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                    <input type="submit" value="Установить" class="button save button-green-rounding" name="install_payment">
                </li>
            </ul>
        </form>
    </div>
    
    <?php if($_SERVER['HTTP_HOST'] == 'shishonin-doc.ru'):?>
    <p><a href="/admin/organizations">Организации</a></p>
    <?php endif;?>
    
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
<div class="extension">

        <?php if(isset($payments)){
        foreach($payments as $payment):?>
        <div class="extension-item">
            <div class="extension-img">
                <img src="/payments/<?php echo $payment['name'];?>/<?php echo $payment['name'];?>.png">
            </div>
            <div class="extension-center">
                <h4 class="mb-0 pb-0"><a href="<?php echo $setting['script_url'];?>/admin/paysettings/<?php echo $payment['payment_id'];?>"><?php echo $payment['title'];?></a></h4>
            </div>
            <div class="extension-status">
                <?php if($payment['status'] == 1) $status = 'on'; else $status = 'off';?><div class="ext-status <?php echo $status; ?>"></div>
            </div>
        </div>
        <?php endforeach;
        }?>

</div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</div>
</body>
</html>