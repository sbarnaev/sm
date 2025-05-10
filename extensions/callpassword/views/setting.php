<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Настройки CallPassword</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
        </div>
    </div>
    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/extensions/">Расширения</a></li>
        <li>Настройки CallPassword</li>
    </ul>

    <?php if(callpassword::hasSuccess()) CallPassword::showSuccess();?>
    <?php if(callpassword::hasError()) CallPassword::showError();?>

    <form action="" method="POST">
        <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/nastr-tren.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Настройки CallPassword</h3>
                </div>
            </div>
            
            <ul class="nav_button">
                <li><input type="submit" name="save" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/extensions/">Закрыть</a></li>
            </ul>
        </div>

        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4 class="h4-border">Основное</h4>
                    <div class="width-100"><label>Статус:</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($enable == 1) echo 'checked';?>><span>Вкл</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($enable == 0) echo 'checked';?>><span>Откл</span></label>
                        </span>
                    </div>

                    <div class="width-100"><label title="Секретный ключ API">Ключ API для авторизации запросов</label>
                        <input type="text" required name="callpassword[params][api_key]" value="<?=$params['params']['api_key'];?>">
                    </div>

                    <div class="width-100"><label title="Секретный ключ подписи">Ключ API для подписи запросов</label>
                        <input type="text" required name="callpassword[params][sign_key]" value="<?=$params['params']['sign_key'];?>">
                    </div>

                    <div class="width-100">
                        <label title="При влюченной опцией API-сервер сформирует ответ только после получения статуса вызова,&#10;определение которого может продолжаться не более времени ожидания ответа, указанного&#10;в настройках. При выключенной – ответ будет сформирован сразу после обработки запроса,&#10;при этом статус вызова будет неопределён.">
                            Засчитывать подтверждение телефона только после получения статуса вызова
                        </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input required data-show_on="get_call_timeout_box" name="callpassword[params][get_call_status]" type="radio" value="1" <?php if($params['params']['get_call_status'] == 1) echo 'checked';?>><span>Вкл</span></label>
                            <label class="custom-radio"><input required data-show_off="get_call_timeout_box" name="callpassword[params][get_call_status]" type="radio" value="0" <?php if($params['params']['get_call_status'] == 0) echo 'checked';?>><span>Откл</span></label>
                        </span>
                    </div>

                    <div class="width-100 hidden" id="get_call_timeout_box"><label title="Время ожидания ответа в секундах (от 20 до 90 секунд)">Время ожидания ответа в секундах</label>
                        <input type="number" required min="20" max="90" name="callpassword[params][get_call_timeout]" value="<?=$params['params']['get_call_timeout'];?>">
                    </div>

                    <div class="width-100">
                        <a href="https://my.new-tel.net" target="_blank">Перейти в личный кабинет сервиса</a>
                    </div>
                </div>

                <div class="col-1-2">
                    <h4 class="h4-border">Участники</h4>
                    <div class="width-100"><label>Группы пользователей, для которых выводить ссылку для подтверждения телефона</label>
                        <select class="multiple-select" size="7" multiple="multiple" name="callpassword[params][cp_user_groups][]">
                            <?php if($group_list):
                                foreach($group_list as $user_group):?>
                                    <option value="<?=$user_group['group_id'];?>"<?php if (!empty($params['params']['cp_user_groups']) && in_array($user_group['group_id'], $params['params']['cp_user_groups'])) echo ' selected="selected"';?>><?=$user_group['group_title'];?></option>
                                <?php endforeach;
                            endif;?>
                        </select>
                    </div>

                    <div class="width-100"><label>Запретить пользователям доступ к урокам, если номер не подтвержден:</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="callpassword[params][access_users_to_lessons]" type="radio" value="1" <?php if($params['params']['access_users_to_lessons'] == 1) echo 'checked';?>><span>Вкл</span></label>
                            <label class="custom-radio"><input name="callpassword[params][access_users_to_lessons]" type="radio" value="0" <?php if($params['params']['access_users_to_lessons'] == 0) echo 'checked';?>><span>Откл</span></label>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="reference-link">
            <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232128"><i class="icon-info"></i>Справка по расширению</a>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>