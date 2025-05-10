<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php');?>

<div class="main">
    <div class="top-wrap">
        <h1>Изменить план подписки</h1>
            <div class="logout">
                <a href="/" target="_blank">Перейти на сайт</a>
                <a href="/admin/logout" class="red">Выход</a>
          </div>
    </div>
  
    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/membersubs/">Подписки</a></li>
        <li>Изменить план подписки</li>
    </ul>

    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;?>
  
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div><img src="/template/admin/images/ext/membership.svg" alt=""></div>
                <div><h3 class="traning-title mb-0">Изменить план подписки</h3></div>
            </div>
            
            <ul class="nav_button">
                <li><input type="submit" name="save" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last">
                    <a class="button red-link" href="/admin/membersubs/">Закрыть</a>
                </li>
            </ul>
        </div>
        
        
        <div class="tabs">
            <ul>
                <li>Основное</li>
                <li>Действие</li>
                <li>Уведомления</li>
            </ul>
            
            <div class="admin_form">
                <!-- ОСНОВНОЕ  -->
                <div>
                    <div class="row-line">
                        <div class="col-1-2">
                            <h4>Основное</h4>
                            <p><label>Название плана подписки:</label>
                                <input type="text" name="name" value="<?=$plane['name'];?>"placeholder="Название плана" required="required">
                            </p>
                            
                            <p><label>Служебное имя:</label>
                                <input type="text" name="service_name" value="<?=$plane['service_name'];?>">
                            </p>
                            
                            <p><label>Статус:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($plane['status'] == 1) echo 'checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($plane['status'] == 0) echo 'checked';?>><span>Откл</span></label>
                                </span>
                            </p>
                            
                            <p><label>Разрешить создание реккурентных платежей:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="recurrent_enable" type="radio" value="1" <?php if($plane['recurrent_enable'] == 1) echo 'checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="recurrent_enable" type="radio" value="0" <?php if($plane['recurrent_enable'] == 0) echo 'checked';?>><span>Откл</span></label>
                                </span>
                            </p>
                            
                            <p><label>Описание:</label>
                                <textarea name="subs_desc" cols="35" rows="3"><?=$plane['subs_desc'];?></textarea>
                            </p>
                            
                            <div class="width-100"><label>Тип периода:</label>
                                <div class="select-wrap">
                                    <select name="period_type">
                                        <option value="Month"<?php if($plane['period_type'] == 'Month') echo ' selected="selected"';?>>Месяц</option>
                                        <option value="Week"<?php if($plane['period_type'] == 'Week') echo ' selected="selected"';?>>Неделя</option>
                                        <option value="Day"<?php if($plane['period_type'] == 'Day') echo ' selected="selected"';?>>День</option>
                                    </select>
                                </div>
                            </div>
                            
                            <p><label title="Здесь указываем кол-во (дней, недель или месяцев)">Период (значение):</label>
                                <input type="text" value="<?=$plane['lifetime'];?>" name="lifetime">
                            </p>
                            
                            <p><label title="Сколько раз списывать оплату">Максимальное кол-во периодов:</label>
                                <input type="text" value="<?=$plane['max_periods'];?>" name="max_periods">
                            </p>
    
                            <p><label title="Это время, когда нужно начинать регулярные платежи (первый платёж был не регулярный, а установочный)">Отсрочка 1-го регулярного платежа, дни:</label>
                                <input type="text" value="<?=$plane['delay'];?>" name="delay">
                            </p>
                            
                            <p><label title="Сумма для регулярного списания, по умолчанию совпадает с суммой первого (установочного) платежа">Сумма регулярного платежа</label>
                                <input type="text" value="<?=$plane['amount'];?>" name="amount">
                            </p>

                            <p id="recurrent_label"><label title="Галочка при рекуррентах">Подпись чекбокса при создании регулярных платежей</label>
                                <input type="text"  value="<?=$plane['recurrent_label'];?>" name="recurrent_label">
                            </p>
                            
                            <div class="width-100"><label>При оплате использовать только эти платёжные системы: </label>
                                <div class="">
                                    <select class="multiple-select" name="select_payments[]" multiple="multiple">
                                        <?php $payments = Order::getPayments();
                                        if($payments):
                                            foreach($payments as $payment):?>
                                                <option value="<?=$payment['payment_id'];?>"<?php if($selected != null) {if(in_array($payment['payment_id'], $selected)) echo ' selected="selected"';}?>><?=$payment['title'];?></option>
                                            <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>
                            </div>

                            <input type="hidden" name="access_id" value="0">
                            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                        </div>
						
						
                        <div class="col-1-2">
                            <h4>Связанные подписки</h4>
                            <div class="width-100"><label title="Это подписки, которые дают доступ к одному и тому же тренингу, но отличаются периодом">При покупке продлять эти планы:</label>
                                <select class="multiple-select" name="related_planes[]" multiple="multiple">
                                    <?php $planes = Member::getPlanes();
                                    if($planes):
                                        foreach($planes as $_plane):?>
                                            <option value="<?=$_plane['id'];?>"<?php if($related_plane_arr != null && in_array($_plane['id'], $related_plane_arr)) echo ' selected="selected"';?>><?=!empty($_plane['service_name']) ? $_plane['service_name'] : $_plane['name'];?></option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>

                            <div class="width-100"><label>При повторной покупке:</label>
                                <div class="select-wrap">
                                    <select name="create_new">
                                        <option value="0"<?php if($plane['create_new'] == 0) echo ' selected="selected"';?>>Продлевать существующую подписку</option>
                                        <option value="1"<?php if($plane['create_new'] == 1) echo ' selected="selected"';?>>Создавать новую подписку</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ДОСТУП  -->
                <div>
                    <h4>Продление плана</h4>
                    <div class="row-line">
                        
                        <div class="col-1-2">
                            <div class="width-100"><label>Куда направить клиента для продления плана:</label>
                                <div class="select-wrap">
                                    <select name="renewal_type">
                                        <option value="0">Не выбрано</option>
                                        <option value="1"<?php if($plane['renewal_type'] == 1) echo ' selected="selected"';?>>Страница заказа продукта</option>
                                        <option value="2"<?php if($plane['renewal_type'] == 2) echo ' selected="selected"';?>>Страница описания продукта</option>
                                        <option value="3"<?php if($plane['renewal_type'] == 3) echo ' selected="selected"';?>>Свой лендинг</option>
                                    </select>
                                </div>
                            </div>
    
                            <div class="width-100"><label>если продукт, выберите:</label>
                                <div class="select-wrap">
                                    <select name="renewal_product">
                                        <option value="0">Не выбран</option>
                                        <?php $product_list = Product::getProductListOnlySelect();
                                        foreach($product_list as $product):?>
                                            <option value="<?=$product['product_id'];?>"<?php if($plane['renewal_product'] == $product['product_id']) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                                            <?php if($product['service_name']):?>
                                                <option disabled="disabled" class="service-name">(<?=$product['service_name'];?>)</option>
                                            <?php endif;
                                        endforeach;?>
                                    </select>
                                </div>
                            </div>

                            <p><label>если ссылка, укажите:</label>
                                <input type="text" value="<?=$plane['renewal_link']?>" name="renewal_link" placeholder="http://">
                            </p>
                            
                            <div class="width-100"><label>Продлевать:</label>
                                <div class="select-wrap">
                                    <select name="prolong_active">
                                        <option value="0"<?php if($plane['prolong_active'] == 0) echo ' selected="selected"';?>>Любую, даже законченную подписку</option>
                                        <option value="1"<?php if($plane['prolong_active'] == 1) echo ' selected="selected"';?> data-show_on="prolong_link">Только активную подписку</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="width-100 hidden" id="prolong_link"><label>Ссылка для продления, если подписка закончилась: </label>
                                <input type="text" value="<?=$plane['prolong_link']?>" name="prolong_link" placeholder="http://">
                            </div>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Окончание действия</h4>
                        </div>
                        
                        <div class="col-1-2">
                            <?php $del_groups = $plane['del_groups'] ? unserialize($plane['del_groups']) : null;
                            $add_groups = $plane['add_groups'] ? explode(',', $plane['add_groups']) : null;
                            $group_list = User::getUserGroups();?>

                            <div class="width-100"><label>При завершении действия плана удалить группы пользователя:</label>
                                <select class="multiple-select" size="7" multiple="multiple" name="del_groups[]">
                                    <?php if($group_list):
                                        foreach($group_list as $user_group):?>
                                            <option value="<?=$user_group['group_id'];?>"<?php if($del_groups && in_array($user_group['group_id'], $del_groups)) echo ' selected="selected"';?>>
                                                <?=$user_group['group_title'];?>
                                            </option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-1-2">
                            <div class="width-100"><label>При завершении действия плана добавить группы пользователю:</label>
                                <select class="multiple-select" size="7" multiple="multiple" name="add_groups[]">
                                    <?php if($group_list):
                                        foreach($group_list as $user_group):?>
                                            <option value="<?=$user_group['group_id'];?>"<?php if($add_groups && in_array($user_group['group_id'], $add_groups)) echo ' selected="selected"';?>>
                                                <?=$user_group['group_title'];?>
                                            </option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>

                            <div class="width-100"><label>При завершении действия плана добавить план подписки пользователю:</label>
                                <select class="multiple-select" size="7" multiple="multiple" name="add_planes[]">
                                <?php $add_planes = $plane['add_planes'] ? explode(',', $plane['add_planes']) : null;
                                $plane_list = Member::getPlanes();
                                    if($plane_list):
                                        foreach($plane_list as $plane_one):?>
                                            <option value="<?=$plane_one['id'];?>"<?php if($add_planes && in_array($plane_one['id'], $add_planes)) echo ' selected="selected"';?>>
                                                <?=$plane_one['name'];?>
                                            </option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
                        </div>

                        <!--  РАСШИРЕНИЕ TELEGRAM-->
                        <?php if (System::CheckExtensension('telegram', 1)) {
                            require_once(ROOT . '/extensions/telegram/views/membership/edit_plane.php');
                        };?>
                        
                        <div class="col-1-1 mb-0">
                            <h4>Письмо куратору</h4>
                        </div>
    
                        <?php if($plane['manager_letter'] != null) {
                            $manager_letter = unserialize(base64_decode($plane['manager_letter']));
                        }?>
                        
                        <div class="col-1-2">
                            <p class="width-100"><label>Тема письма: </label>
                                <input type="text" name="subj_manager" value="<?php if(isset($manager_letter['subj_manager'])) echo $manager_letter['subj_manager'];?>">
                            </p>
                        </div>
                        
                        <div class="col-1-2">
                            <p class="width-100" title="Если оставить поле пустым, то письмо НЕ отправится"><label>Email:</label>
                                <input type="email" name="email_manager" pattern="^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,})+$" value="<?php if(isset($manager_letter['email_manager'])) echo $manager_letter['email_manager'];?>">
                            </p>
                        </div>
                        
                        <div class="col-1-1">
                            <div class="label">Содержание письма:</div>
                            <div class="width-100">
                                <textarea name="letter_manager" class="editor" rows="6"><?php if(isset($manager_letter['letter_manager'])) echo $manager_letter['letter_manager']?></textarea>
                            </div>

                            <div class="width-100">
                                <p>Переменные для подстановки: <br />
                                    [NAME] - имя клиента<br />
                                    [SURNAME] - фамилия клиента<br />
                                    [EMAIL] - Email клиента<br />
                                    [NICK_TG] - ник в Telegram<br />
                                    [NICK_IG] - ник в Instagram
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Уведомления об окончании плана -->
                <div>
                    <h4 class="mb-30">Уведомления об окончании плана</h4>
                    <div class="row-line">
                        <div class="col-1-1">
                            <div class="menu-apsell">
                                <ul>
                                    <li>Уведомление 1</li>
                                    <li>Уведомление 2</li>
                                    <li>Уведомление 3</li>
                                </ul>

                                <div>
                                    <div>
                                        <div class="row-line">
                                            <div class="col-1-2">
                                                <p><label>Отправить за (n часов)</label>
                                                    <input type="text" size="3" value="<?=$plane['letter_1_time']?>" name="letter_1_time">
                                                </p>
                                            </div>
                                        </div>

                                        <div class="row-line">
                                            <div class="col-1-2">
                                                <div class="width-100"><label>Отправлять SMS:</label>
                                                    <span class="custom-radio-wrap">
                                                        <label class="custom-radio"><input data-show_on="sms1_text" name="sms1_status" type="radio" value="1"<?php if($plane['sms1_status']) echo ' checked="checked"';?>><span>Вкл</span></label>
                                                        <label class="custom-radio"><input name="sms1_status" type="radio" value="0" <?php if(!$plane['sms1_status']) echo ' checked="checked"';?>><span>Откл</span></label>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-1-1 hidden" id="sms1_text">
                                                <div class="width-100" title="1 SMS = 70 сиволов в кириллице или 160 в латинице"><label>Текст SMS сообщения:</label>
                                                    <textarea name="sms1_text"><?=$plane['sms1_text'];?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row-line">
                                            <div class="col-1-2">
                                                <p><label>Тема письма:</label>
                                                    <input type="text" size="45" value="<?=$plane['letter_1_subj']?>" name="letter_1_subj" placeholder="">
                                                </p>
                                            </div>

                                            <div class="col-1-1">
                                                <h4>Текст письма:</h4>
                                                <textarea class="editor" name="letter_1"><?=$plane['letter_1']?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div>
                                        <div class="row-line">
                                            <div class="col-1-2">
                                                <p><label>Отправить за (n часов)</label>
                                                    <input type="text" size="3" value="<?=$plane['letter_2_time']?>" name="letter_2_time" placeholder="">
                                                </p>
                                            </div>
                                        </div>

                                        <div class="row-line">
                                            <div class="col-1-2">
                                                <div class="width-100"><label>Отправлять SMS:</label>
                                                    <span class="custom-radio-wrap">
                                                        <label class="custom-radio"><input data-show_on="sms2_text" name="sms2_status" type="radio" value="1"<?php if($plane['sms2_status']) echo ' checked="checked"';?>><span>Вкл</span></label>
                                                        <label class="custom-radio"><input name="sms2_status" type="radio" value="0" <?php if(!$plane['sms2_status']) echo ' checked="checked"';?>><span>Откл</span></label>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-1-1 hidden" id="sms2_text">
                                                <div class="width-100" title="1 SMS = 70 сиволов в кириллице или 160 в латинице"><label>Текст SMS сообщения:</label>
                                                    <textarea name="sms2_text"><?=$plane['sms2_text'];?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row-line">
                                            <div class="col-1-2">
                                                <p><label>Тема письма:</label>
                                                    <input type="text" size="45" value="<?=$plane['letter_2_subj']?>" name="letter_2_subj" placeholder="">
                                                </p>
                                            </div>

                                            <div class="col-1-1">
                                                <h4>Текст письма:</h4>
                                                <textarea class="editor" name="letter_2"><?=$plane['letter_2']?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div>
                                        <div class="col-1-2">
                                            <p><label>Отправить за (n часов)</label>
                                                <input type="text" size="3" value="<?=$plane['letter_3_time']?>" name="letter_3_time" placeholder="">
                                            </p>
                                        </div>

                                        <div class="row-line">
                                            <div class="col-1-2">
                                                <div class="width-100"><label>Отправлять SMS:</label>
                                                    <span class="custom-radio-wrap">
                                                        <label class="custom-radio"><input data-show_on="sms3_text" name="sms3_status" type="radio" value="1"<?php if($plane['sms3_status']) echo ' checked="checked"';?>><span>Вкл</span></label>
                                                        <label class="custom-radio"><input name="sms3_status" type="radio" value="0" <?php if(!$plane['sms3_status']) echo ' checked="checked"';?>><span>Откл</span></label>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-1-1 hidden" id="sms3_text">
                                                <div class="width-100" title="1 SMS = 70 сиволов в кириллице или 160 в латинице"><label>Текст SMS сообщения:</label>
                                                    <textarea name="sms3_text"><?=$plane['sms3_text'];?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row-line">
                                            <div class="col-1-2">
                                                <p><label>Тема письма:</label>
                                                    <input type="text" size="45" value="<?=$plane['letter_3_subj']?>" name="letter_3_subj" placeholder="">
                                                </p>
                                            </div>

                                            <div class="col-1-1">
                                                <h4>Текст письма:</h4>
                                                <textarea class="editor" name="letter_3"><?=$plane['letter_3']?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-1-1">
                            <p>Переменные для подстановки:</p>
                            <p>[NAME] - имя клиента<br />
                            [LINK] - ссылка на продление доступа.</p>
                        </div>

                        <div class="col-1-1">
                            <h4 title="Запускает отправку писем уведомлений">Команда для планировщика 1 раз в 10 минут</h4>
                            <textarea>php <?=ROOT . '/task/member_cron.php'?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>