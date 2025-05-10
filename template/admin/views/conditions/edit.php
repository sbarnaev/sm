<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Изменить условие</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/conditions/">Условия</a></li>
        <li>Изменить условие</li>
    </ul>
<?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <form action="" method="POST">
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Изменить условие</h3>
            <ul class="nav_button">
                <li><input type="submit" name="edit" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/conditions/">Закрыть</a></li>
            </ul>
        </div>
    
        <div class="tabs">
        <ul>
            <li>Основное</li>
            <li>Действия</li>
        </ul>
        
        <div class="admin_form">
            
            <div>
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название:</label><input type="text" name="name" value="<?php echo $condition['name'];?>" placeholder="Название" required="required"></p>

                    <div class="width-100"><label>Тип условия:</label>
                        <div class="select-wrap">
                            <select name="type" required="required">
                                <option value="">- Выбрать тип -</option>
                                <option value="1"<?php if($condition['type'] == 1) echo ' selected="selected"';?>>Последний вход более XX часов</option>
                                <option value="2"<?php if($condition['type'] == 2) echo ' selected="selected"';?>>Последний пройденный урок более XX часов</option>
                                <option value="3"<?php if($condition['type'] == 3) echo ' selected="selected"';?>>До дня рождения XX дней</option>
                                <option value="4"<?php if($condition['type'] == 4) echo ' selected="selected"';?>>Принадлежит группе XX</option>
                                <option value="6"<?php if($condition['type'] == 6) echo ' selected="selected"';?>>Не принадлежит никакой группе</option>
                                <option value="5"<?php if($condition['type'] == 5) echo ' selected="selected"';?>>Подписка мембершипа заканчивается через XX дней</option>
                                <option value="100"<?php if($condition['type'] == 100) echo ' selected="selected"';?>>Тест на юзерах с ID </option>
                                <!--option value="99"<?php //if($condition['type'] == 99) echo ' selected="selected"';?>>Свой SQL запрос</option-->
                            </select>
                        </div>
                    </div>
                    
                    <p class="width-100"><label>Значение XX:</label><input type="text" value="<?php echo $condition['value_xx'];?>" name="value_xx"></p>
                    
                    <div class="width-100"><label>Статус:</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1"<?php if($condition['status'] == 1) echo ' checked="checked"';?>><span>Вкл</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0"<?php if($condition['status'] == 0) echo ' checked="checked"';?>><span>Откл</span></label>
                        </span>
                    </div>
                    
                    <p class="width-100">Описание:<br />
                        <textarea name="desc" rows="3" cols="40"><?=$condition['cond_desc'];?></textarea>
                    </p>

                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>

                <div class="col-1-2">
                    <h4>Планировщик</h4>
                    
                    <div class="width-100">
                        <div class="select-wrap">
                            <select name="use_cron">
                                <option value="1"<?php if($condition['use_cron'] == 1) echo ' selected="selected"';?>>Использовать планировщик</option>
                                <option value="0"<?php if($condition['use_cron'] == 0) echo ' selected="selected"';?>>Выполнить сразу, без планировщика</option>
                            </select>
                        </div>
                    </div>
                    
                    <p class="width-100" title="Интервал выполнения для планировщика">
                        <label>Интервал выполнения, мин:</label>
                        <input type="text" value="<?php echo $condition['period'];?>" name="period">
                    </p>
                </div>
            </div>
            <input type="hidden" name="sql" value="">
            <!--div class="col-1-1">
                <p class="width-100"><label>SQL запрос:</label><textarea name="sql" rows="3" cols="40"><?php //echo $condition['sql_data'];?></textarea></p>
            </div-->
            
            </div>

            
            <div>
                <div class="row-line">
                    <div class="col-1-2">
                        <h4>Добавить группы пользователю</h4>
                        
                        <div class="width-100"><label>Выберите группы:</label>
                            <select class="multiple-select" size="7" multiple="multiple" name="add_groups[]">
                                <?php $group_list = User::getUserGroups();
                                if($group_list):
                                    $add_groups = unserialize(base64_decode($condition['add_groups']));
                                    foreach($group_list as $user_group):?>
                                        <option value="<?=$user_group['group_id'];?>"<?php if(!empty($add_groups) && in_array($user_group['group_id'], $add_groups)) echo ' selected="selected"';?>>
                                            <?=$user_group['group_title'];?>
                                        </option>
                                    <?php endforeach;
                                endif;?>
                            </select>
                        </div>
                    </div>
    
                    <div class="col-1-2">
                        <h4>Удалить группы пользователя</h4>
    
                        <div class="width-100"><label>Выберите группы:</label>
                            <select class="multiple-select" size="7" multiple="multiple" name="del_groups[]">
                                <?php if($group_list):
                                    $del_groups = unserialize(base64_decode($condition['del_groups']));
                                    foreach($group_list as $user_group):?>
                                        <option value="<?=$user_group['group_id'];?>"<?php if(!empty($del_groups) && in_array($user_group['group_id'], $del_groups)) echo ' selected="selected"';?>>
                                            <?=$user_group['group_title'];?>
                                        </option>
                                    <?php endforeach;
                                endif;?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-1-2">
                        <h4>Рассылки</h4>

                        <?php $responder = System::CheckExtensension('responder',1);
                        if($responder):?>
                            <div class="width-100"><label>Подписать на рассылку</label>
                                <div class="select-wrap">
                                    <select name="delivery">
                                        <option value="0">Выберите</option>
                                        <?php $delivery_list = Responder::getDeliveryList(2,1,100);
                                        if($delivery_list):
                                            foreach($delivery_list as $delivery):?>
                                                <option value="<?=$delivery['delivery_id'];?>"<?php if($condition['delivery_id'] == $delivery['delivery_id']) echo ' selected="selected"';?>>
                                                    <?=$delivery['name'];?>
                                                </option>
                                            <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100"><label>Отписать от рассылок</label>
                                <select class="multiple-select" size="5" multiple="multiple" name="delivery_unsub[]">
                                    <?php $unsubscribe = unserialize(base64_decode($condition['unsubscribe']));
                                    if($delivery_list):
                                        foreach($delivery_list as $delivery):?>
                                            <option value="<?=$delivery['delivery_id'];?>"<?php if(!empty($unsubscribe) && in_array($delivery['delivery_id'], $unsubscribe)) echo ' selected="selected"';?>>
                                                <?=$delivery['name'];?>
                                            </option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
                        <?php endif;?>
                    </div>
                    
                    <div class="col-1-1">
                        <h4>Отправить письмо</h4>
                        <div class="width-100">
                            <span class="custom-radio-wrap">
                                <label class="custom-radio"><input name="send_letter" type="radio"<?php if($condition['send_letter'] == 1) echo ' checked="checked"';?> value="1">
                                    <span>Отправить</span>
                                </label>
                                
                                <label class="custom-radio"><input name="send_letter" type="radio"<?php if($condition['send_letter'] == 0) echo ' checked="checked"';?> value="0">
                                    <span>Не отправлять</span>
                                </label>
                            </span>
                        </div>
                        
                        <p class="width-100"><label>Тема письма:</label>
                            <input type="text" value="<?=$condition['subject'];?>" name="subject">
                        </p>
                        
                        <p class="width-100">
                            <textarea class="editor" name="letter"><?=$condition['letter'];?></textarea>
                        </p>
                        <p>[NAME] - имя юзера<br />[SURNAME] - фамилия<br />[EMAIL] - email юзера</p>
                    </div>
    
                    <div class="col-1-1">
                        <h4>Отправить sms</h4>
                        <div class="width-100">
                            <span class="custom-radio-wrap">
                                <label class="custom-radio">
                                    <input name="send_sms" type="radio"<?php if($condition['send_sms'] == 1) echo ' checked="checked"';?> value="1">
                                    <span>Отправить</span>
                                </label>
                                
                                <label class="custom-radio">
                                    <input name="send_sms" type="radio"<?php if($condition['send_sms'] == 0) echo ' checked="checked"';?> value="0">
                                    <span>Не отправлять</span>
                                </label>
                            </span>
                        </div>
                        
                        <p class="width-100">
                            <textarea name="message"><?=$condition['message'];?></textarea>
                        </p>
                        <p>[NAME] - имя юзера<br />[SURNAME] - фамилия<br />[EMAIL] - email юзера</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
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