<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Настройки</h1>
            <div class="logout">
                <a href="/" target="_blank">Перейти на сайт</a>
                <a href="/admin/logout/" class="red">Выход</a>
            </div>
        </div>

    <form action="" id="main_set" method="POST" enctype="multipart/form-data">
        <ul class="breadcrumb">
            <li><a href="/admin">Дашбоард</a></li>
            <li>Настройки</li>
        </ul>

        <?php if(isset($_GET['success'])):?>
            <div class="admin_message">Успешно!</div>
        <?php endif;?>


        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div><img src="/template/admin/images/icons/nastr-icon.svg" alt=""></div>
                <div><h3 class="traning-title mb-0">Настройки</h3></div>
            </div>
            
            <ul class="nav_button">
                <li><input type="submit" name="save_main" value="Сохранить" class="button save button-white font-bold"></li>
            </ul>
        </div>
        
        <div class="tabs">
            <ul>
                <li>Общее</li>
                <li>Функции</li>
                <li>Аналитика</li>
                <li>Почта</li>
                <li>SMS</li>
            </ul>
            
            <div class="admin_form">
                <div><!-- 1 вкладка -->
                    <h4>Основное</h4>

                    <div class="row-line">
                        <div class="col-1-2">
                            <div class="width-100"><div class="label">Название сайта:</div>
                                    <input type="text" name="site_name" value="<?=$setting['site_name'];?>">
                                </div>
                                
                                <div class="width-100"><div class="label">E-mail админа (для уведомлений):</div>
                                    <input type="text" name="admin_email" value="<?=$setting['admin_email'];?>">
                                </div>
                                
                                <div class="width-100"><div class="label">E-mail техподдержки:</div>
                                    <input type="text" name="support_email" value="<?=$setting['support_email'];?>">
                                </div>
                           
                                <div class="width-100"><div class="label">Язык скрипта:</div>
                                    <div class="select-wrap">
                                        <select name="lang">
                                            <option value="ru"<?php if($setting['lang'] == 'ru') echo ' selected="selected"';?>>Русский</option>
                                            <option value="en"<?php if($setting['lang'] == 'en') echo ' selected="selected"';?>>English</option>
                                        </select>
                                    </div>
                                </div>
        
                                <div class="width-100"><div class="label">Валюта:</div>
                                    <input type="text" name="currency" value="<?=$setting['currency'];?>">
                                </div>
                            
                                <div class="width-100"><div class="label">Записей на страницу:</div>
                                   <div class="select-wrap">
                                       <select name="show_items">
                                            <option value="10"<?php if($setting['show_items'] == '10') echo ' selected="selected"';?>>10</option>
                                            <option value="20"<?php if($setting['show_items'] == '20') echo ' selected="selected"';?>>20</option>
                                            <option value="30"<?php if($setting['show_items'] == '30') echo ' selected="selected"';?>>30</option>
                                            <option value="50"<?php if($setting['show_items'] == '50') echo ' selected="selected"';?>>50</option>
                                            <option value="100"<?php if($setting['show_items'] == '100') echo ' selected="selected"';?>>100</option>
                                            <option value="200"<?php if($setting['show_items'] == '200') echo ' selected="selected"';?>>200</option>
                                        </select>
                                   </div>
                                </div>
                            
                               <div class="width-100"><div class="label">Шаблон:</div>
                                    <input type="text" name="template" value="<?=$setting['template'];?>">
                               </div>
                               
                               <input type="hidden" name="template_set" value="1">
                               <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                         </div>
                        
                        
                        <div class="col-1-2">
                            <div class="width-100"><div class="label">Аватар (в панели управления):</div>
                                <input type="file" name="cover">
                                <?php if(!empty($setting['cover'])):?>
                                    <div class="avatar-wrap">
                                       <img width="100" src="/images/<?=$setting['cover']?>">
                                    </div>
                                <?php endif;?>
                                <input type="hidden" name="current_img" value="<?=$setting['cover'];?>">
                            </div>

                            <div class="width-100">
                                <a href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=0&fldr=','okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="btn iframe-btn" type="button">Открыть файловый менеджер</a>
                            </div>

                            <div class="width-100"><div class="label">Favicon:</div>
                                <input type="file" name="favicon">
                                <div class="avatar-wrap">
                                    <img width="50" src="<?=$setting['script_url'];?>/favicon.ico">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-1-1 mb-0">
                            <h4>Служебные</h4>
                        </div>
                            
                        <div class="col-1-2">
                            <div class="width-100"><div class="label">Адрес скрипта:</div>
                                <input type="text" autocomplete="off" name="script_url" value="<?=$setting['script_url'];?>" required="required">
                            </div>
                            
                            <div class="width-100"><div class="label">Ключ админ панели:</div>
                                <input type="text" name="security_key" value="<?=$setting['security_key'];?>">
                            </div>
                            
                            <div class="width-100"><div class="label">Имя куки:</div>
                                <input type="text" name="cookie" value="<?=$setting['cookie'];?>" required="required">
                            </div>

                            <div class="width-100"><div class="label">Визуальный редактор для front-end:</div>
                                <div class="select-wrap">
                                    <select name="editor">
                                        <option value="1"<?php if($setting['editor'] == 1) echo ' selected="selected"';?>>Trumbowyg</option>
                                        <option value="2"<?php if($setting['editor'] == 2) echo ' selected="selected"';?>>CKEditor</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-1-2">
                            <div class="width-100"><div class="label">Макс. размер файла при загрузке, Мб:</div>
                                <input type="text" name="max_upload" value="<?=$setting['max_upload'];?>">
                            </div>

                            <div class="width-100"><div class="label">Время сессии, часы:</div>
                                <input type="text" name="session_time" value="<?=$setting['session_time'];?>">
                            </div>

                            <div class="width-100"><div class="label">Режим разработчика:</div>
                               <div class="select-wrap">
                                   <select name="debug_mode">
                                        <option value="1"<?php if($setting['debug_mode'] == '1') echo ' selected="selected"';?>>Включен</option>
                                        <option value="0"<?php if($setting['debug_mode'] == '0') echo ' selected="selected"';?>>Отключен</option>
                                    </select>
                               </div>
                           </div>
                        </div>
                        
                        <div class="col-1-1 mb-0">
                            <h4>API</h4>
                        </div>
                        
                        <div class="col-1-2">
                            <div class="width-100"><div class="label">Ключ API:</div>
                                <input type="text" name="secret_key" value="<?=$setting['secret_key'];?>" required="required">
                            </div>
							
                            <div class="width-100"><div class="label">Приватный ключ API:</div>
                                <input type="text" name="private_key" value="<?=$setting['private_key'];?>">
                            </div>
                        </div>
                        
                        <div class="col-1-2">
                            <div class="width-100"><div class="label">Если имя пустое, то подставляем:</div>
                                <input type="text" name="params[not_exist_name]" value="<?=$params['not_exist_name'];?>">
                                <p style="color:#777; font-size:13px; margin-top:10px">* [EMAIL] - подставить емейл пользователя</p>
                            </div>
                        </div>

                        <div class="col-1-1 mb-0">
                            <h4>Загрузка собственной конфигурации плеера (<a href="https://playerjs.com/">Player.js</a>)</h4>
                        </div>

                        <div class="width-100">
                            <?php if (isset($diffplayer) && $diffplayer):?>
                                <p>У вас загружена <a href="/template/<?=$setting['template'];?>/js/player_bm.js" download>собственная</a> конфигурация плеера</p>
                                <a onclick="return confirm('Вы уверены?')" href="?resetplayer=true&token=<?=$_SESSION['admin_token'];?>">Сбросить к стандартной</a>
                            <?php else:?>
                                <p>У вас стандартная конфигурация плеера, загрузить свою:</p>
                                <input type="file" name="playerjs" accept=".js">
                            <?php endif;?>
                        </div>
                    </div>
                </div>


                <div><!-- Вкладка Функции -->
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Включение функций</h4>
                        </div>
                        
                        <div class="col-1-2">
                            <div class="width-100"><label>Включить продажи:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="enable_sale" type="radio" value="1"<?php if($setting['enable_sale'] == '1') echo ' checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="enable_sale" type="radio" value="0"<?php if($setting['enable_sale'] == '0') echo ' checked';?>><span>Откл</span></label>
                                </span>
                            </div>
                            
                            <div class="width-100"><label>Включить каталог:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="enable_catalog" type="radio" value="1"<?php if($setting['enable_catalog'] == '1') echo ' checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="enable_catalog" type="radio" value="0"<?php if($setting['enable_catalog'] == '0') echo ' checked';?>><span>Откл</span></label>
                                </span>
                            </div>
                            
                            <div class="width-100"><div class="label">Корзина в каталоге:</div>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="use_cart" type="radio" value="1"<?php if($setting['use_cart'] == '1') echo ' checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="use_cart" type="radio" value="0"<?php if($setting['use_cart'] == '0') echo ' checked';?>><span>Откл</span></label>
                                </span>
                            </div>
                            
                            <div class="width-100"><label>Лендинги продуктов:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="enable_landing" type="radio" value="1"<?php if($setting['enable_landing'] == '1') echo ' checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="enable_landing" type="radio" value="0"<?php if($setting['enable_landing'] == '0') echo ' checked';?>><span>Откл</span></label>
                                </span>
                            </div>
                            
                            <div class="width-100"><label>Включить кабинет клиента:</label>
                                <span class="custom-radio-wrap">
                                <label class="custom-radio"><input name="enable_cabinet" type="radio" value="1"<?php if($setting['enable_cabinet'] == '1') echo ' checked';?>><span>Вкл</span></label>
                                <label class="custom-radio"><input name="enable_cabinet" type="radio" value="0"<?php if($setting['enable_cabinet'] == '0') echo ' checked';?>><span>Откл</span></label>
                                </span>
                            </div>

                            <div class="width-100"><label>Включить самостоятельную регистрацию пользователей:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="enable_registration" type="radio" value="1"<?php if($setting['enable_registration'] == '1') echo ' checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="enable_registration" type="radio" value="0"<?php if($setting['enable_registration'] == '0') echo ' checked';?>><span>Откл</span></label>
                                </span>
                            </div>

                            <input type="hidden" name="split_test_enable" value="0">
                            <!--div class="width-100"><label>A/B тестирование: </label>
                                <span class="custom-radio-wrap">
                                <label class="custom-radio"><input name="split_test_enable" type="radio" value="1"<?php if($setting['split_test_enable'] == '1') echo ' checked';?>><span>Вкл</span></label>
                                <label class="custom-radio"><input name="split_test_enable" type="radio" value="0"<?php if($setting['split_test_enable'] == '0') echo ' checked';?>><span>Откл</span></label>
                                </span>
                            </div-->
                        </div>
                        
                        <div class="col-1-2">
                            <div class="width-100"><label>Сбор отзывов:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="enable_reviews" type="radio" value="1"<?php if($setting['enable_reviews'] == '1') echo ' checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="enable_reviews" type="radio" value="0"<?php if($setting['enable_reviews'] == '0') echo ' checked';?>><span>Откл</span></label>
                                </span>
                            </div>
                        
                            <div class="width-100"><label>Включить обратную связь:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="enable_feedback" type="radio" value="1"<?php if($setting['enable_feedback'] == '1') echo ' checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="enable_feedback" type="radio" value="0"<?php if($setting['enable_feedback'] == '0') echo ' checked';?>><span>Откл</span></label>
                                </span>
                            </div>
        
                            <div class="width-100"><label>Записывать сообщения:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="write_feedback" type="radio" value="1"<?php if($setting['write_feedback'] == '1') echo ' checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="write_feedback" type="radio" value="0"<?php if($setting['write_feedback'] == '0') echo ' checked';?>><span>Откл</span></label>
                                </span>
                            </div>
                            
                            <div class="width-100"><div class="label">Перенаправление пользователя при входе на сайт:</div>
                               <div class="select-wrap">
                                   <select name="login_redirect">
                                        <option value="1"<?php if($setting['login_redirect'] == '1') echo ' selected="selected"';?>>Профиль</option>
                                        <option value="2"<?php if($setting['login_redirect'] == '2') echo ' selected="selected"';?>>Мои заказы</option>
                                        <option value="3"<?php if($setting['login_redirect'] == '3') echo ' selected="selected"';?>>Мои курсы</option>
                                        <option value="4"<?php if($setting['login_redirect'] == '4') echo ' selected="selected"';?>>Мои тренинги 2.0</option>
                                        <option value="5"<?php if($setting['login_redirect'] == '5') echo ' selected="selected"';?>>На главную</option>
                                        <option value="0"<?php if($setting['login_redirect'] == '0') echo ' selected="selected"';?>>Обратно на ту же страницу</option>
                                    </select>
                               </div>
                           </div>
                        </div>
                        
                        
                        <div class="col-1-1 mb-0">
                            <h4>Дополнительные поля</h4>
                        </div>
                        
                        <div class="col-1-2">
                            <div class="width-100"><div class="label">Показывать поле примечание:</div>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_order_note" type="radio" value="1"<?php if($setting['show_order_note'] == '1') echo ' checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="show_order_note" type="radio" value="0"<?php if($setting['show_order_note'] == '0') echo ' checked';?>><span>Откл</span></label>
                                </span>
                            </div>
        
                            <div class="width-100"><div class="label">Включать защиту email:</div>
                                <span class="custom-radio-wrap">
                                <label class="custom-radio"><input name="email_protection" type="radio" value="1"<?php if($setting['email_protection'] == '1') echo ' checked';?>><span>Вкл</span></label>
                                <label class="custom-radio"><input name="email_protection" type="radio" value="0"<?php if($setting['email_protection'] == '0') echo ' checked';?>><span>Откл</span></label>
                                </span>
                            </div>
                        </div>
                        
                        
                        <div class="col-1-2">
                            <div class="width-100"><label>Использовать Имя + Фамилия:</label>
                                <div class="select-wrap">
                                    <select name="show_surname">
                                        <option value="0">Нет</option>
                                        <option value="1"<?php if($setting['show_surname'] == 1) echo ' selected="selected"';?>>Только для платных продуктов</option>
                                        <option value="2"<?php if($setting['show_surname'] == 2) echo ' selected="selected"';?>>Использовать всегда</option>
                                    </select>
                                </div>
                             </div>
                            
                            <div class="width-100"><label>Запрашивать ник в Telegram: </label>
                                <div class="select-wrap">
                                    <select name="show_telegram_nick">
                                        <option value="0">Нет</option>
                                        <option value="1"<?php if($setting['show_telegram_nick'] == 1) echo ' selected="selected"';?>>Только для платных продуктов</option>
                                        <option value="2"<?php if($setting['show_telegram_nick'] == 2) echo ' selected="selected"';?>>Запрашивать всегда</option>
                                        <option value="3"<?php if($setting['show_telegram_nick'] == 3) echo ' selected="selected"';?>>Запрашивать обязательно</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="width-100"><label>Запрашивать ник в Instagram: </label>
                                <div class="select-wrap">
                                    <select name="show_instagram_nick">
                                        <option value="0">Нет</option>
                                        <option value="1"<?php if($setting['show_instagram_nick'] == 1) echo ' selected="selected"';?>>Только для платных продуктов</option>
                                        <option value="2"<?php if($setting['show_instagram_nick'] == 2) echo ' selected="selected"';?>>Запрашивать всегда</option>
                                        <option value="3"<?php if($setting['show_instagram_nick'] == 3) echo ' selected="selected"';?>>Запрашивать обязательно</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-1-1 mb-0">
                            <h4>Настройка поля телефон:</h4>
                        </div>
                        <div class="col-1-1">
                            <div class="width-100"><div class="label">Показывать поле телефон:</div>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="request_phone" type="radio" value="1"<?php if($setting['request_phone'] == '1') echo ' checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="request_phone" type="radio" value="0"<?php if($setting['request_phone'] == '0') echo ' checked';?>><span>Откл</span></label>
                                </span>
                            </div>
                            
                            <div class="label">Выберите страны, если нужно вывести маску телефона:</div>
                            <div>
                                <?php $countries = !empty($setting['countries_list']) ? json_decode($setting['countries_list']) : '';?>
                                <select class="multiple-select" name="countries_list[]" multiple="multiple">
                                    <?php foreach (System::getCountriesToPhone() as $code => $title):?>
                                        <option value="<?=$code;?>"<?=$countries && in_array($code, $countries) ? ' selected="selected"' : '';?>><?=$title;?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
        
                        <div class="col-1-1 mb-0">
                            <h4>Настройка заказа</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100" title="Будет отправлена квитанция об оплате заказа, это НЕ онлайн чек"><label>Отправлять квитанцию: </label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="strict_report" type="radio" value="1"<?php if($setting['strict_report'] == '1') echo ' checked';?>><span>Да</span></label>
                                    <label class="custom-radio"><input name="strict_report" type="radio" value="0"<?php if($setting['strict_report'] == '0') echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>
                            
                            <div class="width-100"><div class="label">Разрешить скачивать бесплатные продукты сразу:</div>
                                <span class="custom-radio-wrap">
                                <label class="custom-radio"><input name="simple_free_dwl" type="radio" value="1"<?php if($setting['simple_free_dwl'] == '1') echo ' checked';?>><span>Да</span></label>
                                <label class="custom-radio"><input name="simple_free_dwl" type="radio" value="0"<?php if($setting['simple_free_dwl'] == '0') echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>
    
                            <div class="width-100"><div class="label">Разрешить скачивать продукты из личного кабинета:</div>
                                <span class="custom-radio-wrap">
                                <label class="custom-radio"><input name="dwl_in_lk" type="radio" value="1"<?php if($setting['dwl_in_lk'] == '1') echo ' checked';?>><span>Да</span></label>
                                <label class="custom-radio"><input name="dwl_in_lk" type="radio" value="0"<?php if($setting['dwl_in_lk'] == '0') echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>

                            <div class="width-100"><div class="label">Разрешить отменять (удалять) заказы пользователям:</div>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="params[allow_user_to_delete_orders]" type="radio" value="1"<?php if(isset($params['allow_user_to_delete_orders']) && $params['allow_user_to_delete_orders'] == 1) echo ' checked';?>><span>Да</span></label>
                                    <label class="custom-radio"><input name="params[allow_user_to_delete_orders]" type="radio" value="0"<?php if(!isset($params['allow_user_to_delete_orders']) || $params['allow_user_to_delete_orders'] == 0) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>

                            <div class="width-100"><div class="label">Уведомлять администратора о бесплатных заказах:</div>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="params[disable_notify_admin_to_free_orders]" type="radio" value="0"<?php if(!isset($params['disable_notify_admin_to_free_orders']) || $params['disable_notify_admin_to_free_orders'] == 0) echo ' checked';?>><span>Да</span></label>
                                    <label class="custom-radio"><input name="params[disable_notify_admin_to_free_orders]" type="radio" value="1"<?php if(isset($params['disable_notify_admin_to_free_orders']) && $params['disable_notify_admin_to_free_orders'] == 1) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>
                        </div>
                        
                        
                        <div class="col-1-2">
                            <div class="width-100">
                                <div class="label">Время жизни заказа:</div>
                                <div class="input-meaning"><input type="text" size="2" name="order_life_time" value="<?=$setting['order_life_time'];?>" required="required"> <span>дней</span></div>
                            </div>
                            
                            <div class="width-100">
                                <div class="label">Время жизни ссылки для скачивания:</div>
                                <div class="input-meaning"><input type="text" size="2" name="dwl_time" value="<?=$setting['dwl_time'];?>" required="required"> <span>часов</span></div>
                            </div>
                            
                            <div class="width-100"><div class="label" title="Заметьте, в 1 заказе может быть несколько продуктов">Ограничение на кол-во скачиваний 1 заказа:</div>
                                <div class="input-meaning"><input type="text" size="2" name="dwl_count" value="<?=$setting['dwl_count'];?>" required="required"> <span>раз</span></div>
                            </div>
                        </div>

                        <div class="col-1-1 mb-0">
                            <h4>Дополнительно</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100">
                                <div class="label">Срок хранения логов:</div>
                                <div class="input-meaning">
                                    <input type="text" size="2" name="logs_life_time" value="<?=$setting['logs_life_time'];?>" required="required"> <span>дней</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                

                <div><!--  Вкладка Аналитика -->
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Веб-аналитика</h4>
                        </div>
                        
                        <div class="col-1-2">
                            <div class="width-100"><label>№ счётчика Я.Метрики (например: 5434873)</label>
                                <input type="text" name="yacounter" value="<?=$setting['yacounter'];?>">
                            </div>
                        </div>
    
                        <div class="col-1-2">
                            <div class="width-100"><label>Цели Google Analytics: </label>
                                <div class="select-wrap">
                                    <select name="ga_target">
                                        <option value="0"<?php if($setting['ga_target'] == '0') echo ' selected="selected"';?>>Не использовать</option>
                                        <option value="1"<?php if($setting['ga_target'] == '1') echo ' selected="selected"';?>>Использовать</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="col-1-1 mb-0">
                            <h4>Идентификаторы целей</h4>
                        </div>
                 
                        
                        <div class="col-1-2">
                            <p><strong>Событие "Клик заказать, добавить в корзину"</strong></p>
                            <p>Яндекс.Метрика: ADD_TO_BUY</p>
                            <p>GA: send, event, add_to_buy, click</p>
                        </div>
                        
                        <div class="col-1-2">
                            <p><strong>Событие "Создание заказа"</strong></p>
                            <p>Яндекс.Метрика: CREATE_ORDER</p>
                            <p>GA: send, event, create_order, submit</p>
                        </div>
                        
                        <div class="col-1-2">
                            <p><strong>Событие "Добавление апселла"</strong></p>
                            <p>Яндекс.Метрика: ADD_UPSELL</p>
                            <p>GA: send, event, add_upsell, submit</p>
                        </div>
                        
                        <div class="col-1-2">
                            <p><strong>Событие "Оплата Ручной способ | Счёт для ООО"</strong></p>
                            <p>Яндекс.Метрика: CUSTOM_PAY</p>
                            <p>GA: send, event, custom_pay, submit</p>
                        </div>
                        
                        <div class="col-1-2">
                            <p><strong>Событие "Переход на оплату в платёжную систему"</strong></p>
                            <p>Яндекс.Метрика: GO_PAY</p>
                            <p>GA: send, event, go_pay, submit</p>
                        </div>
                        
                        <div class="col-1-2">
                            <p><strong>Событие "Регистрация партнёра"</strong></p>
                            <p>Яндекс.Метрика: REG_PARTNER</p>
                            <p>GA: send, event, reg_partner, submit</p>
                        </div>
                    </div>
                </div>
                

                <div><!-- Вкладка SMTP  -->
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Режим отправки</h4>
                        </div>
                        
                        <div class="col-1-2">
                            <div class="width-100"><label>Отправлять почту через: </label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="use_smtp" type="radio" value="1"<?php if($setting['use_smtp'] == '1') echo ' checked';?>><span>Swift Mailer</span></label>
                                    <label class="custom-radio"><input name="use_smtp" type="radio" value="0"<?php if($setting['use_smtp'] == '0') echo ' checked';?>><span>PHP Mail</span></label>
                                </span>
                            </div>
    
                            <p class="width-100"><label>SMTP хост:</label>
                                <input type="text" name="smtp_host" value="<?=$setting['smtp_host'];?>">
                            </p>

                            <p class="width-100"><label>SMTP порт:</label>
                                <input size="4" type="text" name="smtp_port" value="<?=$setting['smtp_port'];?>">
                            </p>

                            <p class="width-100"><label>Пользователь:</label>
                                <input type="text" name="smtp_user" value="<?=$setting['smtp_user'];?>">
                            </p>

                            <p class="width-100"><label>Пароль SMTP:</label>
                                <input type="password" name="smtp_pass" value="<?=$setting['smtp_pass'];?>" autocomplete="off">
                            </p>

                            <div class="width-100"><label>Шифрование:</label>
                                <div class="select-wrap">
                                    <select name="smtp_ssl">
                                        <option value="0"<?php if($setting['smtp_ssl'] == '0') echo ' selected="selected"';?>>Нет</option>
                                        <option value="1"<?php if($setting['smtp_ssl'] == '1') echo ' selected="selected"';?>>SSL</option>
                                        <option value="2"<?php if($setting['smtp_ssl'] == '2') echo ' selected="selected"';?>>TLS</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="col-1-2">
                            <div class="width-100"><label>Домен для DKIM:</label>
                                <input type="text" name="smtp_domain" value="<?=$setting['smtp_domain'];?>">
                            </div>
                            
                            <div class="width-100"><label>Селектор DKIM:</label>
                                <input type="text" name="smtp_selector" value="<?=$setting['smtp_selector'];?>">
                            </div>
                            
                            <div class="width-100"><label>Приватный ключ:</label><br />
                                <textarea name="smtp_private_key" rows="6" cols="65"><?=$setting['smtp_private_key'];?></textarea>
                            </div>
                            
                            <p><a href="https://easydmarc.com/tools/dkim-record-generator" target="_blank">Онлайн генератор ключей DKIM</a></p>
                        </div>
                        
                        <div class="col-1-1 mb-0">
                            <h4>Отправитель</h4>
                        </div>
                        
                        <div class="col-1-2">
                            <div class="width-100"><label>E-mail отправителя: </label>
                                <input type="email" name="sender_email" pattern="^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,})+$" value="<?=$setting['sender_email'];?>">
                            </div>
                            
                            <div class="width-100"><label>Имя отправителя: </label>
                                <input type="text" name="sender_name" value="<?=$setting['sender_name'];?>">
                            </div>
                            
                            <div class="width-100"><label>Return Path (адрес возврата): </label>
                                <input type="text" name="return_path" value="<?=$setting['return_path'];?>">
                            </div>
                        </div>
                        
                        
                        <div class="col-1-1 mb-0">
                            <h4>Настройка DNS записей</h4>
                        </div>
                        
                        <div class="col-1-2">
                            <div class="width-100"><strong>DMARC запись: </strong>
                                <p>Имя: _dmarc.<?=$setting['script_url'];?>.</p>
                                <p>Тип: TXT</p>
                                <p>Значение: v=DMARC1; p=quarantine</p>
                            </div>
                        </div>
                        
                        <div class="col-1-2">
                            <div class="width-100"><strong>SPF запись: </strong>
                                <?php $script_url = explode(".", $setting['script_url']);
                                $count = count($script_url);
                                if ($count == 3) {
                                    $domain = $script_url[1].'.'.$script_url[2];
                                } else {
                                    $script_url = explode("//", $setting['script_url']);
                                    $domain = $script_url[1];
                                }?>
                                <p>Имя: <?=$domain;?>.</p>
                                <p>Тип: TXT</p>
                                <p>Значение: v=spf1 ip4:<?=$_SERVER['SERVER_ADDR'];?> ~all</p>
                            </div>
                        </div>
                        
                        
                        <div class="col-1-1 mb-0">
                            <h4>Тестировать отправку</h4>
                        </div>
                        
                        <div class="col-1-2">
                            <div class="width-100"><label>E-mail для теста: </label>
                                <input form="email_test" type="text" name="email_for_test" required="required" value="">
                            </div>
                            
                            <input form="email_test" type="submit" name="email_test" class="button-green-border-rounding" value="Тест отправки почты">
                            <p><a href="https://www.mail-tester.com/" target="_blank">Тестирование писем на СПАМ</a></p>
                        </div>
                    </div>
                </div>
                
                
                <div>
                    <div class="row-line">
                        <div class="col-1-2">
                            <h4>Настройки SMSC.ru</h4>
                            <div class="width-100"><label>Логин в SMSC.ru: </label>
                                <input type="text" name="smsc[login]" value="<?=$smsс['login'];?>">
                            </div>
                            
                            <div class="width-100"><label>Пароль в SMSC.ru: </label>
                                <input type="password" name="smsc[password]" value="<?=$smsс['password'];?>">
                            </div>
                            
                            <div class="width-100"><label>Имя отправителя (необязательно): </label>
                                <input type="text" name="smsc[sender]" value="<?=$smsс['sender'];?>">
                            </div>
                            
                            <div class="width-100"><label>Режим отладки:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio">
                                        <input name="smsc[debug]" type="radio" value="1" <?php if(isset($smsс['debug']) && $smsс['debug'] == 1) echo 'checked';?>><span>Вкл</span>
                                    </label>
                                    <label class="custom-radio">
                                        <input name="smsc[debug]" type="radio" value="0" <?php if(!isset($smsс['debug']) || $smsс['debug'] == 0) echo 'checked';?>><span>Откл</span>
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <form action="" id="email_test" method="POST">
        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

</body>
</html>