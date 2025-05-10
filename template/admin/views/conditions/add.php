<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Создать условие</h1>
    
        <div class="logout">
            <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
        </div>
    </div>
  
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/conditions/">Условия</a></li>
        <li>Создать условие</li>
    </ul>

    <form action="" method="POST">
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Создать новое условие</h3>
            <ul class="nav_button">
                <li><input type="submit" name="add" value="Создать" class="button save button-white font-bold"></li>
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
                        
                        <p class="width-100"><label>Название:</label>
                            <input type="text" name="name" placeholder="Название" required="required">
                        </p>
    
                        <div class="width-100"><label>Тип условия:</label>
                            <div class="select-wrap">
                                <select name="type" required="required">
                                    <option value="">- Выбрать тип -</option>
                                    <option value="1">Последний вход более XX часов</option>
                                    <option value="2">Последний пройденный урок более XX часов</option>
                                    <option value="3">До дня рождения XX дней</option>
                                    <option value="4">Принадлежит группе XX</option>
                                    <option value="6">Не принадлежит никакой группе</option>
                                    <option value="5">Подписка мембершипа заканчивается через XX дней</option>
                                    <option value="100">Тест на юзерах с ID </option>
                                    <!--option value="99">Свой SQL запрос</option-->
                                </select>
                            </div>
                        </div>
                    
                        <p class="width-100">
                            <label>Значение XX:</label>
                            <input type="text" name="value_xx">
                        </p>
                        
                        <div class="width-100"><label>Статус:</label>
                            <span class="custom-radio-wrap">
                                <label class="custom-radio"><input name="status" type="radio" value="1" checked=""><span>Вкл</span></label>
                                <label class="custom-radio"><input name="status" type="radio" value="0"><span>Откл</span></label>
                            </span>
                        </div>
                    
                        <p class="width-100">Описание:<br />
                            <textarea name="desc" rows="3" cols="40"></textarea>
                        </p>
                        
                        <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                    </div>

                    <div class="col-1-2">
                        <h4>Планировщик</h4>
                        
                        <div class="width-100">
                            <div class="select-wrap">
                                <select name="use_cron">
                                    <option value="1">Использовать планировщик</option>
                                    <option value="0">Выполнить сразу, без планировщика</option>
                                </select>
                            </div>
                        </div>
                        
                        <p class="width-100" title="Интервал выполнения для планировщика">
                            <label>Интервал выполнения, мин: </label>
                            <input type="text" name="period">
                        </p>
                    </div>
                    
                    <input type="hidden" name="sql" value="">
                </div>
            </div>
            
            <div>
                <div class="row-line">
                    <div class="col-1-2">
                        <h4>Добавить группы пользователю</h4>
                        
                        <div class="width-100"><label>Выберите группы:</label>
                            <select class="multiple-select" size="7" multiple="multiple" name="add_groups[]">
                                <?php $group_list = User::getUserGroups();
                                if($group_list):
                                    foreach($group_list as $user_group):?>
                                        <option value="<?=$user_group['group_id'];?>">
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
                                    foreach($group_list as $user_group):?>
                                        <option value="<?=$user_group['group_id'];?>">
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
                                                <option value="<?=$delivery['delivery_id'];?>"><?=$delivery['name'];?></option>
                                            <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100"><label>Отписать от рассылок</label>
                                <select class="multiple-select" size="5" multiple="multiple" name="delivery_unsub[]">
                                    <?php if($delivery_list):
                                        foreach($delivery_list as $delivery):?>
                                            <option value="<?php echo $delivery['delivery_id'];?>"><?=$delivery['name'];?></option>
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
                                <label class="custom-radio"><input name="send_letter" type="radio" value="1"><span>Отправить</span></label>
                                <label class="custom-radio"><input name="send_letter" type="radio" value="0" checked="checked"><span>Не отправлять</span></label>
                            </span>
                        </div>
                        
                        <p class="width-100"><label>Тема письма:</label>
                            <input type="text" name="subject">
                        </p>
                        <p class="width-100">
                            <textarea class="editor" name="letter"></textarea>
                        </p>
                        <p>[NAME] - имя юзера<br />[SURNAME] - фамилия<br />[EMAIL] - email юзера</p>
                    </div>
    
                    <div class="col-1-1">
                        <h4>Отправить sms</h4>
                        <div class="width-100">
                            <span class="custom-radio-wrap">
                                <label class="custom-radio">
                                    <input name="send_sms" type="radio" value="1">
                                    <span>Отправить</span>
                                </label>
                                
                                <label class="custom-radio">
                                    <input name="send_sms" type="radio" value="0" checked="checked">
                                    <span>Не отправлять</span>
                                </label>
                            </span>
                        </div>
                        
                        <p class="width-100">
                            <textarea name="message"></textarea>
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