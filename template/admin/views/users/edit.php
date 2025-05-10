<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1><?=System::Lang('EDIT_USER');?> ID : <?=$user['user_id'];?></h1>
        <div class="logout">
            <a href="<?=$setting['script_url'];?>" target="_blank"><?=System::Lang('GO_SITE');?></a><a href="<?=$setting['script_url'];?>/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>
    
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/users/">Пользователи</a>
        </li>
        <li>Редактировать пользователя</li>
    </ul>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <?php if(isset($_GET['success'])):?>
            <div class="admin_message">Сохранено!</div>
        <?php endif;?>
        
        <?php if(isset($_GET['dublemail'])):?>
            <div class="admin_warning">Пользователь с таким эмейлом уже существует</div>
        <?php endif;?>

        <div class="traning-top">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/user-edit.svg" alt="">
                </div>
                
                <div>
                    <h3 class="traning-title mb-0">Редактировать пользователя</h3>
                </div>
            </div>
            
            <ul class="nav_button">
                <li><input type="submit" name="save" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?=$setting['script_url'];?>/admin/users"><?=System::Lang('CLOSE');?></a></li>
            </ul>
        </div>

        <div class="tabs">
            <ul>
                <li>Основное</li>
                <li>Заказы</li>
                <li>Группы</li>

                <?php if($responder):?>
                    <li>Рассылки</li>
                <?php endif;

                if($en_training && $uniq_trainings || $en_courses && $uniq_courses):?>
                    <li>Тренинги</li>
                <?php endif;?>
                <li>Письма</li>

                <?php if($user_planes):?>
                    <li>Подписки</li>
                <?php endif;

                if($user['is_curator'] && System::CheckExtensension('training', 1)):?>
                    <li>Кураторская</li>
                <?php endif;

                if($user_cerificates):?>
                    <li>Сертификаты</li>
                <?php endif;?>
            </ul>
    
            <div class="admin_form">
                <!-- 1 вкладка Основное -->
                <div>
                    <h4 class="h4-border"><?=System::Lang('BASIC');?></h4>
                    <div class="row-line">
                        <div class="col-1-2">
                            <p><label>Имя:</label><input type="text" name="name" value="<?=$user['user_name'];?>"></p>
                            
                            <?php if($setting['show_surname'] > 0):?>
                                <p><label>Фамилия:</label><input type="text" name="surname" value="<?=$user['surname'];?>"></p>
                            <?php endif;?>
                            
                            <p><label>E-mail:</label>
                                <input type="text" name="email" value="<?=$user['email'];?>">
                            </p>
                            <p><label>Логин (для админов):</label>
                                <input type="text" value="<?=$user['login']?>" name="login" autocomplete="off">
                            </p>
                            <p><label>Телефон<?if($user['confirm_phone'] == $user['phone']) echo ' (подтвержден)';?></label>
                                <input type="text" name="phone" value="<?=$user['phone'];?>">
                            </p>
        
                            <p><label>Новый пароль: </label><input type="text" name="pass"></p>
        
                            <p><label>Статус: </label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($user['status']== 1) echo 'checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($user['status']== 0) echo 'checked';?>><span>Откл</span></label>
                                </span>
                            </p>
                        </div>
                        
                        <div class="col-1-2">
                            <div class="round-block">
                                <p><img src="<?=User::getAvatarUrl($user, $setting);?>" /></p>
                                <p><b>Уровень:</b> <?=user::getRoleUser($user['role']);?></p>
                                <p title="Когда School Master в первый раз запомнил посетителя">Дата первого посещения сайта: <?=date("d.m.Y H:i:s", $user['enter_time']);?></p>
                                <p class="width-100 main-tooltyp-wrap">
                                    Дата регистрации: <?=date("d.m.Y H:i:s", $user['reg_date']);?>
                                    <span class="main-tooltyp">(через <?php $afterday = ($user['reg_date'] - $user['enter_time'])/ 86400; echo round($afterday, 2)?> дней)</span>
                                </p>
                                <p>Дата последнего входа: <?=$user['last_visit'] != null ? date("d.m.Y H:i:s", $user['last_visit']) : 'Никогда';?></p>
                                <p>Метод регистрации: <?=getRegMethod($user['enter_method']);?></p>
        
                                <?php if(!empty($user['channel_id'])):?>
                                    <p>Канал: <?php
                                        $channel = Stat::getChannelData($user['channel_id']);
                                        if ($channel && isset($channel['name'])):?>
                                            <a href="/admin/stat/channels/"><?=$channel['name'];?></a>
                                        <?php endif;?>
                                    </p>
                                <?php endif;?>
                                
                                <?php if(!empty($user['from_id'])):
                                    $user_data = User::getUserNameByID($user['from_id']);?>
                                    <p>Пришёл от партнёра:
                                        <a target="_blank" href="/admin/users/edit/<?=$user['from_id'];?>"><?=$user_data['user_name'];?></a>
                                    </p>
                                <?php endif;?>
                                
                                <p>ДР: <?="{$user['bith_day']}.{$user['bith_month']}.{$user['bith_year']}";?></p>
                                <div class="width-100">
                                    <input form="user_enter" type="hidden" value="<?=$user['user_id'];?>" name="user_id">
                                    <input type="hidden" form="user_enter" value="<?=$user['user_name'];?>" name="user_name">
                                    <input type="hidden" form="user_enter" name="token" value="<?=$_SESSION['admin_token'];?>">
                                </div>
                            </div>
                            
                            <div class="block-button">
                                <input type="submit" form="user_enter" value="Войти под пользователем" class="button-green" style="" name="user_enter">
                            </div>
                            <div class="block-button">
                                <a href="/admin/users/resetpass<?="?user_id={$user['user_id']}&user_name={$user['user_name']}&user_email={$user['email']}&token={$_SESSION['admin_token']}";?>">Сбросить и отправить пароль</a>
                            </div>
                        </div>
                    </div>
                    
                    <h4 class="mt-30">Служебное</h4>
                    <div class="row-line">
                        <div class="col-1-2">
                            <div class="width-100"><label>Уровень: </label>
                                <div class="select-wrap">
                                    <select name="role">
                                        <option value="user"<?php if($user['role'] == 'user') echo ' selected="selected"';?>>Пользователь</option>
                                        <option value="manager"<?php if($user['role'] == 'manager') echo ' selected="selected"';?>>Менеджер</option>
                                        <option value="admin"<?php if($user['role'] == 'admin') echo ' selected="selected"';?>>Админ</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="width-100"><label>Состояние / статус: </label>
                                <div class="select-wrap">
                                    <select name="level">
                                        <option value="">Не выбран</option>
                                        <option value="1"<?php if($user['level'] == 1) echo ' selected="selected"';?>>Новичок</option>
                                        <option value="2"<?php if($user['level'] == 2) echo ' selected="selected"';?>>Исследователь</option>
                                        <option value="3"<?php if($user['level'] == 3) echo ' selected="selected"';?>>Доцент</option>
                                        <option value="4"<?php if($user['level'] == 4) echo ' selected="selected"';?>>Магистр</option>
                                        <option value="5"<?php if($user['level'] == 5) echo ' selected="selected"';?>>Ракета</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-1-2">
                            <p><label>Добавить заметку</label>
                                <textarea name="note" cols="45" rows="3"><?=$user['note'];?></textarea>
                            </p>
                        </div>
                    </div>
                    
                    <h4 class="mt-30">Социальные сети</h4>
                    <div class="row-line">
                        <div class="col-1-2">
                            <p><label>Telegram: </label><input type="text" name="nick_telegram" value="<?=$user['nick_telegram'];?>"></p>
                            <p><label>Instagram: </label><input type="text" name="nick_instagram" value="<?=$user['nick_instagram'];?>"></p>
                        </div>
                        
                        <div class="col-1-2">
                            <p><label>ВКонтакте: </label><input type="text" name="vk_url" value="<?=$user['vk_url'];?>"></p>
                        </div>
                    </div>
                    
                    <h4 class="mt-30">Личное</h4>
                    <div class="row-line">
                        <div class="col-1-2">
                            <div class="width-100"><label>Пол: </label>
                                <div class="select-wrap">
                                    <select name="sex">
                                        <option value="">Не указан</option>
                                        <option value="male"<?php if($user['sex'] == 'male') echo ' selected="selected"';?>>Мужской</option>
                                        <option value="female"<?php if($user['sex'] == 'female') echo ' selected="selected"';?>>Женский</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h4 class="mt-30 h4-border">Дополнительные поля</h4>
                    <div class="row-line">
                        <div class="col-1-2">
                            <p><label>Город: </label><input type="text" name="city" value="<?=$user['city'];?>"></p>
                            <p><label>Индекс: </label><input type="text" name="zipcode" value="<?=$user['zipcode'];?>"></p>
                        </div>
                        <div class="col-1-2">
                            <p><label>Адрес: </label><textarea name="address" cols="45" rows="3"><?=$user['address'];?></textarea></p>
                        </div>
                    </div>

                    <?php if (System::CheckExtensension('polls', 1)) {
                        require_once(ROOT . '/extensions/polls/views/admin/polls/user_card.php');
                    }?>

                    <h4 class="mt-30 h4-border">Роли</h4>
                    <div class="row-line">
                        <div class="col-1-2">
                            <div class="roly-wrap">
                                <div class="roly-bottom">
                                    <?php $aff = System::CheckExtensension('partnership', 1);
                                    $enable_aff = $aff ? System::getExtensionStatus('partnership') : false;
                                    if($enable_aff != false && $user['is_partner'] == 1):?>
                                        <div class="width-100">
                                            <label class="custom-chekbox-wrap" for="is_partner">
                                                <input type="checkbox" id="is_partner" name="is_partner" value="1"<?php if($user['is_partner'] == 1) echo ' checked="checked"'; ?>>
                                                <span class="custom-chekbox"></span>Партнёр
                                            </label>
                                        </div>
                            
                                        <div class="width-100">
                                            <div class="ind-komis">
                                                <div class="relative" style="max-width: 100px;">
                                                    <input class="price-input-2" type="text" value="<?=$partner['custom_comiss'];?>" name="custom_comiss" title="Индивидуальная комиссия партнёра">
                                                    <div class="price-input-cur-2">%</div>
                                                </div>
                                                <span>Инд. комиссия</span>
                                            </div>
                                        </div>
										
										<div class="width-100">
                                            <a target="_blank" href="/admin/aff/userstat/<?php echo $id;?>">Начисления партнёра</a>
                                        </div>
                                    <?php endif;?>
                                </div>
                                
                                <div class="roly-row">
                                    <?php if($user['is_client'] == 1):?>
                                        <div><label class="custom-chekbox-wrap" for="is_client">
                                            <input type="checkbox" id="is_client" name="is_client" value="1" checked="checked" disabled="disabled">
                                            <span class="custom-chekbox"></span>Клиент
                                        </label></div>
                                    <?php endif;?>
    
                                    <?php if($enable_aff != false):?>
                                        <div><label class="custom-chekbox-wrap" for="is_author">
                                            <input type="checkbox" id="is_author" name="is_author" value="1" <?php if($user['is_author'] == 1) echo ' checked="checked"'; ?>>
                                            <span class="custom-chekbox"></span>Автор
                                        </label></div>
                                    <?php endif;?>
                                    
                                    <div><label class="custom-chekbox-wrap" for="is_subsc">
                                        <input type="checkbox" id="is_subsc" name="is_subsc" value="1"<?php if($user['is_subs'] == 1) echo ' checked="checked"'; ?>>
                                        <span class="custom-chekbox"></span>Получает рассылки
                                    </label></div>
                
                                    <div><label class="custom-chekbox-wrap" for="is_curator">
                                        <input type="checkbox" id="is_curator" name="is_curator" value="1"<?php if($user['is_curator'] == 1) echo ' checked="checked"'; ?>>
                                        <span class="custom-chekbox"></span> Куратор
                                    </label></div>
                                </div>
                            </div>
                            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                        </div>
                    </div>
                    
                    <!--  Особый режим -->
                    <?php if($enable_aff != false && $user['is_partner'] == 1):?>
                        <h4 class="mt-30 h4-border">Особый режим партнёрки</h4>
                        <div class="row-line">
                            <div class="col-1-1">
                                <p><label>Особый режим: </label>
                                    <span class="custom-radio-wrap">
                                        <label class="custom-radio"><input name="spec_aff" type="radio" value="1" <?php if($user['spec_aff']== 1) echo 'checked';?>><span>Вкл</span></label>
                                        <label class="custom-radio"><input name="spec_aff" type="radio" value="0" <?php if($user['spec_aff']== 0) echo 'checked';?>><span>Откл</span></label>
                                    </span>
                                </p>
                            </div>
                        
                            <div class="col-1-4">
                                <p><label>Добавить продукт:</label>
                                    <select form="spec_aff" name="specaff_params[products]">
                                        <?php $product_list = Product::getProductListOnlySelect();
                                        if ($product_list):
                                            foreach ($product_list as $product_item):?>
                                                <option <?php if($product_item['run_aff']!=1) echo 'style="color: gray;"';?> value="<?=$product_item['product_id'];?>"><?=$product_item['product_name'];?><?php if($product_item['run_aff']!=1) echo ' (выключено начисление)';?></option>
                                                <?php if ($product_item['service_name']):?>
                                                    <option <?php if($product_item['run_aff']!=1) echo 'style="color: gray;":';?> disabled="disabled" class="service-name">(<?=$product_item['service_name'];?>)</option>
                                                <?php endif;
                                            endforeach;
                                        endif;?>
                                    </select>
                                </p>
                            </div>
                        
                            <div class="col-1-4">
                                <p><label>Стратегия работы:</label>
                                    <select form="spec_aff" name="specaff_params[type]">
                                        <option value="1">Начислять только с 1 заказа</option>
                                        <option value="2">Начислять только со 2-го заказа</option>
                                        <option value="3" data-show_on="floatscheme">Плавающая схема</option>
                                    </select>
                                </p>
                            </div>
                        
                            <div class="col-1-4">
                                <p><label>Процент:</label><input type="text" form="spec_aff" name="specaff_params[comiss]"></p>
                            </div>
                            
                            <div class="col-1-4">
                                <input type="hidden" form="spec_aff" name="token" value="<?=$_SESSION['admin_token'];?>">
                                <label>&nbsp;</label><input type="submit" form="spec_aff" name="add_spec_aff" class="button save button-green-rounding add-prod-but" value="Добавить">
                            </div>
                            
                            <div id="floatscheme" class="col-1-2 hidden">
                                <p><label title="Вида № платежа=%комиссии, например: 1=20">Платежи и комиссии для плавающей схемы: </label>
                                    <textarea form="spec_aff" name="specaff_params[float]"></textarea>
                                </p>
                            </div>
                        
                            <?php if($aff_params):
                                foreach($aff_params as $item):
                                    $product = Product::getProductById($item['product_id']);?>
                                    <div class="col-1-1">
                                        <form id="edit_spec<?=$item['id'];?>" action="" method="POST">
                                            <p class="width-100">
                                                <a href="/admin/products/edit/<?=$product['product_id'];?>" target="_blank">
                                                    <?=$product['product_name'].($product['service_name'] ? " ({$product['service_name']})" : '');?>
                                                </a>
                                            </p>
                                            
                                            <div style="width:32%; float:left; margin:0 2% 0 0"><label>Стратегия работы:</label>
                                                <select name="specaff_params[type]">
                                                    <option value="1"<?php if($item['type'] == 1) echo ' selected="selected"';?>>Начислять только с 1 заказа</option>
                                                    <option value="2"<?php if($item['type'] == 2) echo ' selected="selected"';?>>Начислять только со 2-го заказа</option>
                                                    <option value="3"<?php if($item['type'] == 3) echo ' selected="selected"';?> data-show_on="floatscheme<?=$item['id'];?>">Плавающая схема</option>
                                                </select>
                                            </div>
                                            
                                            <div style="width:10%; float:left; margin:0 2% 0 0">
                                                <label>Комиссия:</label><input  type="text" name="specaff_params[comiss]" value="<?=$item['comiss'];?>">
                                            </div>
                                            
                                            <div id="floatscheme<?=$item['id'];?>" class="hidden" style="width:40%; float:left; margin:0 2% 0 0">
                                                <label>Плавающая схема:</label><textarea  name="specaff_params[float]"><?=$item['float_scheme'];?></textarea>
                                            </div>
                                            
                                            <div class="form-row-submit" style="padding: 1.5em 0 0 0">
                                                <input  type="hidden" name="spec_id" value="<?=$item['id'];?>">
                                                <input type="hidden"  name="token" value="<?=$_SESSION['admin_token'];?>">
                                                <button type="submit" title="Сохранить" name="save_spec" class="button save button-green-rounding button-lesson"><span class="icon-check"></span></button>
                                                <button type="submit" onclick="return confirm('Вы уверены?')" title="Удалить" name="del_spec" class="button save button-red-rounding button-lesson"><span class="icon-remove"></span></button>
                                            </div>
                                        </form>
                                    </div>
                                <?php endforeach;?>
                            <?php endif;?>
                        </div>
                    <?php endif;?>
                </div>
                
                <!-- 2 вкладка Заказы -->
                <div>
                    <div class="row-line">
                        <div class="col-1-1">
                            <div class="overflow-container">
                                <?php if($orders):?>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th class="text-left">Номер</th>
                                                <th class="text-left">Продукт</th>
                                                <th>Сумма, <?=$setting['currency'];?></th>
                                                <th>Статус</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
                                            <?php $all_summ = 0;
                                            foreach($orders as $order):
                                                $order_summ = 0?>
                                                <tr>
                                                    <td class="text-left">
                                                        <a class="order-link" target="_blank" href="/admin/orders/edit/<?=$order['order_id'];?>">
                                                            <?=$order['order_date'];?>
                                                        </a>
                                                        <br><?=date("d.m.Y", $order['order_date']);?>
                                                    </td>
                                                    
                                                    <td class="text-left">
                                                        <?php $order_items = Order::getOrderItems($order['order_id']);
                                                            if ($order_items):
                                                                foreach ($order_items as $order_item):
                                                                    $order_summ += $order_item['price'];
                                                                    $order_product = Product::getProductData($order_item['product_id'], false);?>
                                                                    <a target="_blank" href="/admin/products/edit/<?=$order_item['product_id'];?>">
                                                                        <?=$order_product['product_name'].($order_product['service_name'] ? " ({$order_product['service_name']})" : '')?>
                                                                    </a><br>
                                                                <?php endforeach;
                                                            endif;?>
                                                    </td>
                                                    
                                                    <td class="fz-16">
                                                    <?=$order_summ?></td>
                                                    <td><?php if($order['status'] != 1):?><span class="icon-stopwatch"></span><?endif?>
                                                        <?php if($order['status'] == 1):?><span class="checked-status"></span><?endif?>
                                                        <?php if($order['status'] == 9):?><span class="status-return"></span><?endif?>
                                                    </td>
                                                </tr>
                                                <?php if($order['status'] == 1):
                                                    $all_summ += $order_summ;
                                                    endif;?>
                                        <?php endforeach;?>
                                        </tbody>
                                    </table>
                                    <p><br /></p><hr /><p>Оплачено на сумму: <strong><?="$all_summ {$setting['currency']}";?></strong></p>
                                <?php else:?>
                                    <p>Заказы не найдены</p>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 3 вкладка Группы -->
                <div>
                    <div class="row-line">
                        <div class="col-2-3">
                            <?php $user_group_list = User::getUserGroups();
                            if($user_group_list):?>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="text-left">Группа</th>
                                            <th class="text-left">Дата назначения</th>
                                        </tr>
                                    </thead>

                                    <body>
                                        <?php foreach($user_group_list as $key => $user_group):?>
                                            <tr>
                                                <td class="text-left">
                                                    <label class="custom-chekbox-wrap" for="<?=$user_group['group_name'];?>">
                                                        <input type="checkbox" id="<?=$user_group['group_name'];?>" name="groups[ids][<?=$key;?>]" value="<?=$user_group['group_id'];?>"<?php if($user_groups && in_array($user_group['group_id'], $user_groups)) echo ' checked="checked"';?>>
                                                        <span class="custom-chekbox"></span>
                                                        <a href="/admin/usergroups/edit/<?=$user_group['group_id'];?>" target="_blank"><?=$user_group['group_title'];?></a>
                                                    </label>
                                                </td>

                                                <td class="text-left">
                                                    <?php $date = '';
                                                    if($user_groups && in_array($user_group['group_id'], $user_groups)):
                                                        $group = User::getGroupByUserAndGroup($user['user_id'], $user_group['group_id']);
                                                        $date = $group ? date("d.m.Y H:i:s", $group['date']) : '';
                                                    endif;?>
                                                    <input type="text" id="<?=$user_group['group_name'];?>_date" class="datetimepicker" name="groups[dates][<?=$key;?>]" value="<?=$date;?>">
                                                </td>
                                            </tr>
                                        <?php endforeach;?>
                                    </body>
                                </table>
                            <?php endif;?>
                        </div>
                    </div>
                </div>

                                <!-- Рассылки -->
                <?php if($responder):?>
                    <div>
                        <div class="row-line">
                            <div class="col-1-2">
                            <h4 class="h4-border">Подписан на рассылки</h4>
                            <div class="width-100">
                                <?php $delivery_list = Responder::getUserDelivery($user['email']);
                                if($delivery_list):?>
                                    <div class="col-1-1">
                                        <?php foreach($delivery_list as $delivery):
                                            $delivery_data = Responder::getDeliveryData($delivery['delivery_id']);?>
                                            <li><a href="/admin/membersubs/edit/<?=$delivery['delivery_id'];?>" target="_blank"><?=$delivery_data['name'];?></a></li>
                                        <?php endforeach;?>
                                    </div>
                                <?php else:?>
                                    <p><strong>Не подписан на рассылки</strong></p>
                                <?php endif;?>
                            </div>
                        </div>
                        
                        <div class="col-1-2">
                            <h4 class="h4-border">Отписки</h4>
                            <div class="width-100">
                            <?php $cancelled = Responder::getReasons($user['email']);
                            if($cancelled){
                                foreach($cancelled as $cancel):
                                    if($cancel['delivery_id'] != 0) {
                                        $delivery_data = Responder::getDeliveryData($cancel['delivery_id']);
                                        $title = $delivery_data['name'];
                                    } else $title = 'Отписался от всех';?>
                                <p><strong><?=$title;?></strong> | <?php echo  date("d.m.Y H:i:s", $cancel['time']);?><br /><?=$cancel['reason']?></p><br />
                            <?php endforeach;
                            }?>
                            </div>
                        </div>
                    </div>
                    </div>
                <?php endif; ?>

                <!-- Тренинги -->
                <?php if(($en_training && $uniq_trainings) || ($en_courses && $uniq_courses)):?>
                    <div>
                        <?php if ($uniq_trainings) {
                            require_once(__DIR__ . '/edit_trainings.php');
                        }
                        if ($uniq_courses) {
                            require_once(__DIR__ . '/edit_courses.php');
                        };?>
                    </div>
                <?php endif;?>


                <!-- Письма -->
                <div>
                    <h4>Написать письмо</h4>
                    <div class="row-line">
                        <div class="col-1-1">
                            <div class="width-100">
                                <input form="sender" type="text" name="subject">
                            </div>

                            <div class="width-100">
                                <textarea form="sender" name="letter" class="editor"></textarea>
                            </div>

                            <div class="width-100">
                                <input form="sender" type="submit" name="send" class="button-green">
                            </div>
                        </div>

                        <div class="col-1-1">
                            <h4>Отправленные письма</h4>

                            <div class="row-line">
                                <div class="col-1-1">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th class="text-left">ID</th>
                                            <th class="text-left">Email</th>
                                            <th class="text-left">Письмо</th>
                                            <th>Время</th>
                                            <!--th class="td-last"></th-->
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php if($log_letters):
                                            foreach($log_letters as $log):?>
                                                <tr>
                                                    <td><?=$log['id'];?></td>
                                                    <td class="text-left"><?=$log['email'];?></td>
                                                    <td class="text-left rdr_2"><a target="_blank" href="<?=$setting['script_url'];?>/admin/emailog/edit/<?=$log['id'];?>"><?=$log['type'];?></a></td>

                                                    <td><?=date("d.m.Y H:i:s", $log['datetime']);?></td>
                                                </tr>
                                            <?php endforeach;
                                        else:?>
                                            <tr>
                                                <td><p>No letters</p></td>
                                            </tr>
                                        <?php endif;?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Подписки -->
                <?php if ($user_planes):?>
                    <div>
                        <h4 class="h4-border">Основное</h4>
                        <div class="row-line">
                            <div class="col-1-1">
                                <div class="overflow-container">
                                    <table class="table">
                                        <tr>
                                            <th>ID</th>
                                            <th class="text-left">План</th>
                                            <th class="text-left">Дата создания</th>
                                            <th class="text-left">Дата окончания</th>
                                            <th class="td-last">Статус</th>
                                        </tr>
                                        <?php foreach($user_planes as $user_plane):
                                            $plane = Member::getPlaneByID($user_plane['subs_id']);?>
                                            <tr>
                                                <td><a href="/admin/memberusers/edit/<?=$user_plane['id'];?>"><?=$user_plane['id'];?></a></td>
                                                <td class="text-left">
                                                    <a href="/admin/membersubs/edit/<?=$user_plane['subs_id'];?>">
                                                        <?=!empty($plane['service_name']) ? $plane['service_name'] : $plane['name'];?>
                                                    </a>
                                                </td>
                                                <td class="text-left"><?=date("d-m-Y H:i:s", $user_plane['create_date']);?></td>
                                                <td class="text-left"><?=date("d-m-Y H:i:s", $user_plane['end']);?></td>
                                                <td class="td-last">
                                                    <?php if($user_plane['status']):?>
                                                        <span class="stat-yes"><i class="icon-stat-yes"></i></span>
                                                    <?php else:?>
                                                        <span class="stat-no"></span>
                                                    <?php endif;?>
                                                </td>
                                            </tr>
                                        <?php endforeach;?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif;?>

                <!-- Кураторская -->
                <?php if($user['is_curator'] && System::CheckExtensension('training', 1)):?>
                    <div>
                        <h4 class="h4-border">Кураторская</h4>
                        <div class="row-line">
                            <div class="col-1-1">
                                <div class="overflow-container">
                                   Тут список Ваших пользователей (в разработке)
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif;?>

                <!-- Сертификаты -->
                <?php if($user_cerificates):?>
                    <div>
                        <h4 class="h4-border">Сертификаты</h4>
                        <div class="row-line">
                            <div class="col-1-1">
                                <div class="overflow-container">
                                <table class="table">
                                        <thead>
                                            <tr>
                                                <th class="text-left">Номер</th>
                                                <th class="text-left">Тренинг</th>
                                                <th class="text-right">Дата выдачи</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        
                                     <?php foreach($user_cerificates as $user_cerificate):?>
                                        <tr>
                                        <td class="text-left"><?=$user_cerificate['id'];?></td>
                                        <td class="text-left"><a target="_blank" href="/admin/training/edit/<?=$user_cerificate['training_id'];?>"> <?=Training::getTrainingNameByID($user_cerificate['training_id']);?></a></td>
                                        <td class="text-right"><?=date("d.m.Y H:i:s", $user_cerificate['date']);?></td>
                                        <td><a target="_blank" href="<?=$setting['script_url'];?>/training/showcertificate/<?=$user_cerificate['url'];?>">Посмотреть</a></td>
                                        </tr>
                                    <?php endforeach;?>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif;?>

            </div>
        </div>
    </form>
    
    <form action="" method="POST" id="sender">
        <input type="hidden" name="email" value="<?=$user['email'];?>">
        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    </form>
    
    <form action="" target="_blank" method="POST" id="user_enter"></form>
        <div class="buttons-under-form">
            <p class="button-delete">
                <a onclick="return confirm('Вы уверены?')" href="<?=$setting['script_url'];?>/admin/users/del/<?=$user['user_id'];?>?token=<?=$_SESSION['admin_token'];?>" title="Удалить">
                    <i class="icon-remove"></i>Удалить юзера
                </a>
            </p>
            
            <div class="blacklist-but">
                <input type="hidden" form="black_list" name="email" value="<?=$user['email']?>">
                <input type="hidden" form="black_list" name="token" value="<?=$_SESSION['admin_token'];?>">
                <?php $check = User::searchEmailinBL($user['email']);
                if($check == 0):?>
                    <input type="hidden" form="black_list" name="act" value="add">
                    <input class="button-black-rounding" form="black_list" type="submit" value="В чёрный список" name="blacklist">
                <?php else:?>
                    <input type="hidden" form="black_list" name="act" value="delete">
                    <input class="button-green-rounding" form="black_list" type="submit" value="Убрать из чёрного списка" name="blacklist" style="background:#efd943; color:#444; padding:0.3em 10px; border:none; cursor:pointer">
                <?php endif;?>
            </div>
            
            <?php if(isset($enable_aff) && $enable_aff != false && $user['is_partner'] == 0):?>
                <div style="margin:0 0 0 30px">
                    <form action="" method="POST" id="partner">
                        <input type="hidden" name="id" value="<?=$user['user_id'];?>">
                        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                        <input class="button-yellow-rounding" type="submit" value="Сделать партнёром" name="make_partner" style="font-size:14px; cursor:pointer">
                    </form>
                </div>
            <?php endif;?>
        </div>

    <form action="" id="black_list" method="POST"></form>
	<form action="" id="spec_aff" method="POST"></form>
    
    <div id="ModalCurator" class="uk-modal">
    <div class="uk-modal-dialog">
        <div class="userbox  modal-userbox-2">
            <a href="#close" title="Закрыть" class="uk-modal-close uk-close modal-close"><span class="icon-close"></span></a>
            <div>
                <h3 class="modal-head">Выберите куратора</h3>
                <div class="">
                 <form action="" id="changecurator_id" method="POST" class="select-curator-row">
                    <div class="select-wrap">
                    <select class="select" name="newcurator">
                        <!-- TODO тут надо фильтрованый список кураторов конкретного раздела и тренинга -->
                        <?php $curators = User::getCurators();
                        foreach($curators as $curator):?>
                            <option value="<?php echo $curator['user_id']?>"><?php echo $curator['user_name'] .' '. $curator['surname']?></option>
                        <?php endforeach;?>
                    </select>
                    </div>
                    <div class="">
                        <input type="hidden" name="user_id" value="<?php echo $id?>">
                        <input type="hidden"  name="token" value="<?=$_SESSION['admin_token'];?>">
                        <div class="group-button-modal">
                            <button type="submit" name="changecurator" class="button button-green">Назначить</button>
                            <div><button type="submit" name="deletecurator" class="button btn-red-link">Сбросить</button></div>
                        </div>
                    </div>     
                 </form>
                </div>
            </div>
        </div>
    </div>

    <?php function getRegMethod($metod) {
        switch($metod){
            case 'paid':
            return 'покупка продукта';
            break;
            
            case 'free':
            return 'скачивание';
            break;
            
            case 'handmade':
            return 'админом вручную';
            break;

            case 'api':
            return 'добавлен через API';
            break;

            default:
            return 'наверное, это админ)';
            break;
        }
    }
    
    
    function getLessonTask($task) {
        switch($task){
            case 0:
            return 'Без задания';
            break;
            
            case 1:
            return 'Без проверки';
            break;
            
            case 2:
            return 'Автопроверка';
            break;
            
            case 3:
            return 'Ручная проверка';
            break;
        }
    }?>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
<link rel="stylesheet" type="text/css" href="/template/admin/css/jquery.datetimepicker.min.css">
<script src="/template/admin/js/jquery.datetimepicker.full.min.js"></script>
<script>
  function ChangeCurator(elm){
    
    var addform = document.getElementById('changecurator_id');
    
    var trainingid = document.createElement('input');
    trainingid.type = 'hidden';
    trainingid.name = 'training_id';
    trainingid.value = elm.dataset.setTrainingId;
    addform.appendChild(trainingid);
    
    var sectionid = document.createElement('input');
    sectionid.type = 'hidden';
    sectionid.name = 'section_id';
    sectionid.value = elm.dataset.setSectionId;
    addform.appendChild(sectionid);
    
    var curatorid = document.createElement('input');
    curatorid.type = 'hidden';
    curatorid.name = 'curator_id';
    curatorid.value = elm.dataset.setCuratorId;
    addform.appendChild(curatorid);
    
  };
  jQuery('.datetimepicker').datetimepicker({
    format:'d.m.Y H:i:s',
    lang:'ru'
  });
</script>
</body>
</html>