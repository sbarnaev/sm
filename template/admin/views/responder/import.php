<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<div id="page-preloader"><span class="spinner"></span></div>
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Импорт подписчиков</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/responder/mass/">Массовые рассылки</a></li>
        <li>Импорт подписчиков</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data" class="superform">
    <?php if(isset($_GET['success'])):?><div class="admin_message">Успешно добавлено подписчиков: <?php echo intval($_GET['success']);?></div>
    
    <?php endif;?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Не выбран файл или рассылка.</div>'?>

<div class="admin_top admin_top-flex">
    <h3 class="traning-title mb-0">Импорт подписчиков</h3>
    <ul class="nav_button">
        <li><input type="submit" name="import" value="Сохранить" class="button save button-white font-bold"></li>
        <li class="nav_button__last"><a class="button red-link" href="/admin/subscribers/">Закрыть</a></li>
    </ul>
</div>

        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Общее</h4>
                    <p class="width-100"><label>Источник (txt, csv):</label><input type="file" name="file" required="required"></p>
                    
                    <div class="width-100"><label>Поле 1:</label>
                        <div class="select-wrap">
                        <select name="field_one">
                        <option value="email">E-mail</option>
                        <option value="name">Имя</option>
                        <option value="phone">Телефон</option>
                    </select>
                    </div>
                    </div>
                    
                    <div class="width-100"><label>Поле 2:</label>
                        <div class="select-wrap">
                        <select name="field_two">
                        <option value="none">Нет</option>
                        <option value="email">E-mail</option>
                        <option value="name">Имя</option>
                        <option value="phone">Телефон</option>
                    </select>
                        </div>
                    </div>
                    
                    <div class="width-100"><label>Поле 2:</label>
                        <div class="select-wrap">
                        <select name="field_three">
                        <option value="none">Нет</option>
                        <option value="email">E-mail</option>
                        <option value="name">Имя</option>
                        <option value="phone">Телефон</option>
                    </select>
                        </div>
                    </div>
                    
                    <p class="width-100"><label>Разделитель:</label><input type="text" size="2" name="separator" value=";"></p>
                    
                     <div class="width-100"><label>Требовать подтверждения:</label>

                         <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="confirm" type="radio" value="1"><span>Вкл</span></label>
                        <label class="custom-radio"><input name="confirm" type="radio" value="0" checked=""><span>Откл</span></label>
                        </span>
                     </div>

                    <div class="width-100">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                    <span class="small">* Импорт 1 подписчика за ~ 0,07 сек</span>
                    </div>
                </div>
                
                <div class="col-1-2">
                    <h4>Подписать на рассылку</h4>
                    <div class="width-100"><label>Рассылка: </label>
                        <div class="select-wrap">
                        <select name="delivery">
                    <option value="0">Выберите</option>
                    <?php $delivery_list = Responder::getDeliveryList(2);
                    foreach($delivery_list as $delivery):?>
                    <option value="<?php echo $delivery['delivery_id'];?>"><?php echo $delivery['name'];?></option>
                    <?php endforeach;?>
                    </select>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
<style>
#page-preloader {
position: fixed;
left: 0;
top: 0;
right: 0;
bottom: 0;
background: #fff;
z-index: 100500;
display:none;
}

#page-preloader.open {
display:block;
}

#page-preloader .spinner {
width: 128px;
height: 128px;
position: absolute;
left: 50%;
top: 50%;
background: url('/template/admin/images/spinner.gif') no-repeat 50% 50%;
margin: -64px 0 0 -64px;
}
</style>
<script>
 jQuery(document).ready(function(){
	jQuery('.superform').submit(function () { // на что кликать
		jQuery('#page-preloader').toggleClass('open'); // у кого менять
		});
	});
</script>
</body>
</html>