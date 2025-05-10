<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Создать канал</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/channels/">Список каналов</a></li>
        <li>Создать канал</li>
    </ul>

    <form action="" method="POST">

        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Создать канал</h3>
            <ul class="nav_button">
                <li><input type="submit" name="add" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/channels/">Закрыть</a></li>
            </ul>
        </div>
        
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" placeholder="Имя канала" required="required"></p>
                    <div class="width-100"><label>Группа:</label>
                        <div class="select-wrap">
                        <select name="group">
                        <option value="0">Без группы</option>
                        <?php $group_list = Stat::getGroupList();
                        if($group_list):
                        foreach($group_list as $group):?>
                        <option value="<?php echo $group['id'];?>"><?php echo $group['name'];?></option>
                        <?php endforeach;
                        endif;?>
                    </select>
                    </div>
                    </div>
                    <div class="width-100"><label>Вложено: </label>
                        <div class="relative"><input class="price-input-2" type="text" name="summ" placeholder="Сумма"><span class="price-input-cur-2"><?php echo $setting['currency'];?></span></div>
                    </div>
                    <p><label>Описание: </label><textarea name="channel_desc" cols="55" rows="3"></textarea></p>
                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                
                <div class="col-1-2">
                    <h4>UTM</h4> 
                    <p class="width-100"><label>Источник <span class="small yell">utm_source</span>: </label><input type="text" name="source" placeholder="utm_source" required="required"></p>
                    <p class="width-100"><label>Тип трафика <span class="small yell">utm_medium</span>: </label><input type="text" name="medium" placeholder="utm_medium"></p>
                    
                    <p class="width-100"><label>Кампания <span class="small yell">utm_campaign</span>: </label><input type="text" name="campaign" placeholder="utm_campaign"></p>
                    <p class="width-100"><label>Объявление <span class="small yell">utm_content</span>: </label><input type="text" name="content" placeholder="utm_content"></p>
                    <p class="width-100"><label>Ключ <span class="small yell">utm_term</span>: </label><input type="text" name="term" placeholder="utm_term"></p>
                    
                </div>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>