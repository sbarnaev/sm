<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Настройки Telegram</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/extensions/">Расширения</a></li>
        <li>Настройки Telegram</li>
    </ul>

    <?php if(Telegram::hasSuccess()) Telegram::showSuccess();?>
    <?php if(Telegram::hasError()) Telegram::showError();?>

    <form action="" method="POST">
        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/nastr-tren.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Настройки Telegram</h3>
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

                    <div class="width-100"><label title="API токен, выданный при создании бота">API Токен</label>
                        <input type="text" required name="telegram[params][token]" value="<?=$params['params']['token'];?>">
                    </div>
    
                    <div class="width-100"><label title="Имя бота (без @ в начале)">Имя бота</label>
                        <input type="text" required name="telegram[params][bot_name]" value="<?=$params['params']['bot_name'];?>">
                    </div>

                    <?php if($params['params']['is_set_webhook'] == 0):?>
                        <div class="width-100">
                            <a href="/admin/telegramsetting/setwebhook?token=<?=$_SESSION['admin_token'];?>">Установить вебхуки (для приема данных из чата telegram)</a>
                        </div>
                    <?php endif;?>
    
                    <?php if($params['params']['is_set_webhook'] == 1):?>
                        <div class="width-100">
                            <a href="/admin/telegramsetting/delwebhook?token=<?=$_SESSION['admin_token'];?>">Удалить вебхуки</a>
                        </div>
                    <?php endif;?>

                    <div class="width-100">
                        <a href="javascript:void(0)" name="del_stowaways">Удалить пользоваталей из чатов, у которых недолжно быть к ним доступа</a>
                    </div>

                    <input type="hidden" name="telegram[params][is_set_webhook]" value="<?=$params['params']['is_set_webhook'];?>">
                </div>
                
                <div class="col-1-2">
                    <h4 class="h4-border">Участники</h4>
                    <div class="width-100"><label>Группы пользователей, для которых выводить ссылку для привязки telegram</label>
                        <select class="multiple-select" size="7" multiple="multiple" name="telegram[params][tg_user_groups][]">
                            <?php if($group_list):
                                foreach($group_list as $user_group):?>
                                    <option value="<?=$user_group['group_id'];?>"<?php if (!empty($params['params']['tg_user_groups']) && in_array($user_group['group_id'], $params['params']['tg_user_groups'])) echo ' selected="selected"';?>><?=$user_group['group_title'];?></option>
                                <?php endforeach;
                            endif;?>
                        </select>
                    </div>

                    <div class="width-100">
                        <a href="/admin/telegramsetting/memberslist">Cписок участников</a>
                    </div>

                    <div class="width-100">
                        <a href="/admin/telegramsetting/log">Cписок событий</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="reference-link">
            <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232110"><i class="icon-info"></i>Справка по расширению</a>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
<script src="/extensions/telegram/web/admin/js/main.js"></script>
<?php $title = 'Удаление пользоваталей из чатов, у которых недолжно быть к ним доступа';require_once(ROOT . '/lib/progressbar/html.php');?>
</body>
</html>