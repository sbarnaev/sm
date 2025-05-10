<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Настройки писем</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>
    
    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/settings/">Настройки</a></li>
        <li>Настройки писем</li>
    </ul>

    <form action="" method="POST" enctype="multipart/form-data">
        <?php if(isset($_GET['success'])):?>
            <div class="admin_message">Успешно!</div>
        <?php endif;?>
    
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div><img src="/template/admin/images/icons/nastr-icon.svg" alt=""></div>
                <div><h3 class="traning-title mb-0">Настройки писем</h3></div>
            </div>
            
            <ul class="nav_button">
                <li><input type="submit" name="save_letters" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/settings/">Закрыть</a></li>
            </ul>
        </div>
        
        <div class="tabs">
            <ul>
                <li>Регистрация и смена пароля</li>
                <li>Счёт</li>
                <li>Оплата</li>
                <li>Квитанция</li>
                <li>SMS</li>
            </ul>
        
            <div class="admin_form">
                <!-- 1 вкладка -->
                <div>
                    <h4>Шаблон письма при подтверждении регистрации:</h4>
                    <div class="row-line">
                        <div class="col-1-1">
                            <textarea class="editor" name="reg_confirm_letter" rows="5" cols="55"><?=$setting['reg_confirm_letter'];?></textarea>
                            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                        </div>

                        <div class="col-1-1">
                            <p><strong>Переменные для подстановки в письма:</strong></p>
                            <p class="small">[CLIENT_NAME] - имя клиента<br />
                                [LINK] - ссылка на подтверждение регистрации<br />
                                [LINK2] - ссылка на личный кабинет<br />
                                [EMAIL] - емейл при регистрации<br />
                                [PASS] - пароль для входа<br />
                                [SUPPORT] - емейл службы поддержки
                            </p>
                        </div>
                    </div>

                    <h4>Шаблон письма при регистрации:</h4>
                    <div class="row-line">
                        <div class="col-1-1">
                            <textarea class="editor" name="register_letter" rows="5" cols="55"><?=$setting['register_letter'];?></textarea>
                            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                        </div>

                        <div class="col-1-1">
                            <p><strong>Переменные для подстановки в письма:</strong></p>
                            <p class="small">[CLIENT_NAME] - имя клиента<br />
                                [LINK] - ссылка на личный кабинет<br />
                                [EMAIL] - емейл при регистрации<br />
                                [PASS] - пароль для входа<br />
                                [SUPPORT] - емейл службы поддержки
                            </p>
                        </div>
                    </div>

                    <h4>Шаблон письма при смене пароля:</h4>
                    <div class="row-line">
                        <div class="col-1-1">
                            <textarea class="editor" name="pass_reset_letter" rows="5" cols="55"><?=$setting['pass_reset_letter'];?></textarea>
                            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                        </div>

                        <div class="col-1-1">
                            <p><strong>Переменные для подстановки в письма:</strong></p>
                            <p class="small">[CLIENT_NAME] - имя клиента<br />
                                [LINK] - ссылка на личный кабинет<br />
                                [EMAIL] - емейл при регистрации<br />
                                [PASS] - пароль для входа<br />
                                [SUPPORT] - емейл службы поддержки
                            </p>
                        </div>
                    </div>
                </div>
            
                <!-- 2 вкладка -->
                <?php $remind_letter1 = unserialize(base64_decode($setting['remind_letter1']));
                $remind_letter2 = unserialize(base64_decode($setting['remind_letter2']));
                $remind_letter3 = unserialize(base64_decode($setting['remind_letter3']));?>
                <div>
                    <div class="row-line">
                        <div class="col-1-1">
                            <h4 class="mb-30">Письмо о выписанном счёте: </h4>
                            <div class="menu-apsell mb-20">
                                <ul>
                                    <li>Напоминание 1</li>
                                    <li>Напоминание 2</li>
                                    <li>Напоминание 3</li>
                                </ul>
                                
                                <div>
                                    <div>
                                        <div class="width-100"><label>Отправлять</label>
                                            <span class="custom-radio-wrap">
                                                <label class="custom-radio"><input name="remind_letter1[status]" type="radio" value="1"<?php if($remind_letter1['status'] == 1) echo ' checked';?>><span>Вкл</span></label>
                                                <label class="custom-radio"><input name="remind_letter1[status]" type="radio" value="0"<?php if($remind_letter1['status'] == 0) echo ' checked';?>><span>Откл</span></label>
                                            </span>
                                        </div>

                                        <div class="width-100"><label>Отправить через</label>
                                            <div class="time-wrap">
                                            <input class="time-input" size="4" type="text" name="remind_letter1[time]" value="<?=$remind_letter1['time']?>">
                                                <div class="time-value">мин.</div>
                                            </div>
                                        </div>
    
                                        <div class="width-100"><label>Тема письма: </label>
                                            <input size="35" type="text" name="remind_letter1[subject]" value="<?=$remind_letter1['subject']?>">
                                        </div>
                                        
                                        <div class="width-100"><label>Текст письма: </label><br />
                                            <textarea class="editor" name="remind_letter1[text]" rows="5" cols="55"><?=$remind_letter1['text']?></textarea>
                                        </div>

                                        <div class="width-100">
                                            <p><strong>Переменные для подстановки в письма:</strong></p>
                                            <p class="small">[NAME] - имя клиента<br />
                                                [ORDER] - номер заказа<br />
                                                [LINK] - ссылка на неоплаченный заказ<br />
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <div class="width-100">
                                            <label>Отправлять</label>
                                            <span class="custom-radio-wrap">
                                                <label class="custom-radio"><input name="remind_letter2[status]" type="radio" value="1" <?php if($remind_letter2['status'] == 1) echo 'checked';?>><span>Да</span></label>
                                                <label class="custom-radio"><input name="remind_letter2[status]" type="radio" value="0" <?php if($remind_letter2['status'] == 0) echo 'checked';?>><span>Нет</span></label>
                                            </span>
                                        </div>
                                        
                                        <div class="width-100"><label>Отправить через</label>
                                            <div class="time-wrap">
                                                <input class="time-input" size="4" type="text" name="remind_letter2[time]" value="<?=$remind_letter2['time']?>">
                                                <div class="time-value">мин.</div>
                                            </div>
                                        </div>
                                        
                                        <div class="width-100"><label>Тема письма: </label>
                                            <input size="35" type="text" name="remind_letter2[subject]" value="<?=$remind_letter2['subject']?>">
                                        </div>
    
                                        <div class="width-100"><label>Текст письма: </label><br />
                                            <textarea class="editor" name="remind_letter2[text]" rows="5" cols="55"><?=$remind_letter2['text']?></textarea>
                                        </div>

                                        <div class="width-100">
                                            <p><strong>Переменные для подстановки в письма:</strong></p>
                                            <p class="small">[NAME] - имя клиента<br />
                                                [ORDER] - номер заказа<br />
                                                [LINK] - ссылка на неоплаченный заказ<br />
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <div class="width-100">
                                            <label>Отправлять</label>
                                            <span class="custom-radio-wrap">
                                                <label class="custom-radio"><input name="remind_letter3[status]" type="radio" value="1" <?php if($remind_letter3['status'] == 1) echo 'checked';?>><span>Да</span></label>
                                                <label class="custom-radio"><input name="remind_letter3[status]" type="radio" value="0" <?php if($remind_letter3['status'] == 0) echo 'checked';?>><span>Нет</span></label>
                                            </span>
                                        </div>
                                        
                                        <div class="width-100">
                                            <label>Отправить через</label>
                                            <div class="time-wrap">
                                                <input class="time-input" size="4" type="text" name="remind_letter3[time]" value="<?=$remind_letter3['time']?>">
                                                <div class="time-value">мин.</div>
                                            </div>
                                        </div>
                                        
                                        <div class="width-100"><label>Тема письма: </label>
                                            <input size="35" type="text" name="remind_letter3[subject]" value="<?=$remind_letter3['subject']?>">
                                        </div>
                                        
                                        <div class="width-100"><label>Текст письма: </label>
                                            <textarea class="editor" name="remind_letter3[text]" rows="5" cols="55"><?=$remind_letter3['text']?></textarea>
                                        </div>

                                        <div class="width-100">
                                            <p><strong>Переменные для подстановки в письма:</strong></p>
                                            <p class="small">[NAME] - имя клиента<br />
                                                [ORDER] - номер заказа<br />
                                                [LINK] - ссылка на неоплаченный заказ<br />
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-1-1">
                            <div class="width-100">
                                <h4 title="Чтобы эти письма отправлялись, вам нужно создать на хостинге задание для планировщика">Задание для планировщика CRON (одно для всех писем), 1 раз в 10 минут</h4>
                                <textarea disabled cols="65" rows="3">php <?=ROOT ?>/task/order_cron.php</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            
            
                <!-- 3 вкладка -->
                <div>
                    <h4 title="Этот шаблон подставляется в инфопродукты, при их создании, а здесь вы можете его изменить для всех продуктов сразу">Шаблон письма после оплаты заказа: </h4>
                    <div class="row-line">
                        <div class="col-1-1">
                            <div class="width-100"><label>Тема письма о заказе: </label>
                                <input size="35" type="text" name="client_letter_subj" value="<?=$setting['client_letter_subj']?>">
                            </div>
                            
                            <textarea class="editor" name="client_letter" rows="5" cols="55"><?=$setting['client_letter'];?></textarea>
                        </div>
                        
                        <div class="col-1-1">
                            <p class="small">[CLIENT_NAME] - имя клиента<br />
                                [ORDER] - номер заказа<br />
                                [PRODUCT_NAME] - название продукта<br />
                                [SUMM] - сумма<br />
                                [LINK] - ссылка на скачивание<br />
                                [SUPPORT] - емейл службы поддержки<br />
                                [PINCODE] - пин код (лицензионный ключ)
                            </p>
                        </div>
                    </div>
                </div>
            
                <div>
                    <h4>Данные для квитанции об оплате</h4>
                    <div class="row-line">
                        <div class="col-1-2">
                            <div class="width-100"><label>Наименование организации: </label>
                                <textarea name="ticket[org_name]"><?=$ticket['org_name']?></textarea>
                            </div>
                            
                            <div class="width-100"><label>ИНН: </label>
                                <input type="text" name="ticket[inn]" value="<?=$ticket['inn']?>">
                            </div>
                            
                            <div class="width-100"><label>ОГРН: </label>
                                <input type="text" name="ticket[ogrn]" value="<?=$ticket['ogrn']?>">
                            </div>
                        </div>
                    
                        <div class="col-1-2">
                            <div class="width-100"><label>Юридический адрес: </label>
                                <textarea name="ticket[address]"><?=$ticket['address']?></textarea>
                            </div>
                            
                            <div class="width-100"><label>Телефон: </label>
                                <input type="text" name="ticket[phone]" value="<?=$ticket['phone']?>">
                            </div>
                        </div>
                    
                        <div class="col-1-1">
                            <h4>Текст квитанции об оплате</h4>
                            
                            <div class="width-100">
                                <textarea <?php if(empty($ticket['text'])) echo 'id="table-receipt"';?> class="editor" name="ticket[text]"><?=$ticket['text']?></textarea>
                            </div>
                        <div>
                            <p><strong>Данные для подстановки:</strong></p>
                            <p>[DATE] - дата и время заказа<br />
                            [ORDER] - номер заказа/квитанции<br />
                            [CLIENT_EMAIL] - емейл клиента<br />
                            [EMAIL] - емейл продавца<br />
                            [SITE] - адрес сайта<br />
                            [SUMM] - сумма <br />
                            [NAME] - имя клиента<br />
                            [ORG_NAME] - наименование продавца / ИП<br />
                            [INN] - ИНН <br />
                            [YR_ADDRESS] - юр.адрес<br />
                            [OGRN] - ОГРН<br />
                            [PHONE] - телефон продавца<br />
                            [ORDER_ITEMS] - список продуктов</p>
                        </div>
                    </div>
                </div>
            </div>
            
            
            <!-- SMS -->
            <div>
                <div class="row-line">
                    <div class="col-1-1">
                        <h4 class="mb-30">SMS о неоплаченном заказе</h4>
                        <div class="menu-apsell mb-20">
                            <ul>
                                <li>Напоминание 1</li>
                                <li>Напоминание 2</li>
                            </ul>

                            <div>
                                <div>
                                    <div class="width-100"><label>Отправлять SMS:</label>
                                        <span class="custom-radio-wrap">
                                            <label class="custom-radio"><input name="remind_sms1[send]" type="radio" value="1"<?php if($remind_sms1['send'] == 1) echo ' checked';?>><span>Вкл</span></label>
                                            <label class="custom-radio"><input name="remind_sms1[send]" type="radio" value="0"<?php if($remind_sms1['send'] == 0) echo ' checked';?>><span>Откл</span></label>
                                        </span>
                                    </div>

                                    <div class="width-100"><label>Отправить sms через</label>
                                        <div class="time-wrap">
                                        <input class="time-input" size="4" type="text" name="remind_sms1[delay]" value="<?=$remind_sms1['delay'];?>">
                                            <div class="time-value">мин.</div>
                                        </div>
                                    </div>

                                    <div class="width-100" title="1 SMS = 70 сиволов в кириллице или 160 в латинице"><label>Текст SMS сообщения:</label>
                                        <textarea name="remind_sms1[text]"><?=$remind_sms1['text'];?></textarea><br />
                                    </div>

                                    <div>
                                        <p>Переменные для подстановки:</p>
                                        <p>[NAME] - имя клиента<br />
                                           [LINK] - ссылка на оплату заказа
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div>
                                    <div class="width-100"><label>Отправлять SMS:</label>
                                        <span class="custom-radio-wrap">
                                            <label class="custom-radio"><input name="remind_sms2[send]" type="radio" value="1"<?php if($remind_sms2['send'] == 1) echo ' checked';?>><span>Вкл</span></label>
                                            <label class="custom-radio"><input name="remind_sms2[send]" type="radio" value="0"<?php if($remind_sms2['send'] == 0) echo ' checked';?>><span>Откл</span></label>
                                        </span>
                                    </div>

                                    <div class="width-100"><label>Отправить sms через</label>
                                        <div class="time-wrap">
                                            <input class="time-input" size="4" type="text" name="remind_sms2[delay]" value="<?=$remind_sms2['delay'];?>">
                                            <div class="time-value">мин.</div>
                                        </div>
                                    </div>

                                    <div class="width-100" title="1 SMS = 70 сиволов в кириллице или 160 в латинице"><label>Текст SMS сообщения:</label>
                                        <textarea name="remind_sms2[text]"><?=$remind_sms2['text'];?></textarea><br />
                                    </div>

                                    <div>
                                        <p>Переменные для подстановки:</p>
                                        <p>[NAME] - имя клиента<br />
                                            [LINK] - ссылка на оплату заказа
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row-line">
                    <div class="col-1-1 mb-0">
                        <h4>SMS при регистрации пользователя</h4>
                    </div>

                    <div class="col-1-2">
                        <div class="width-100"><label>Отправлять SMS:</label>
                            <span class="custom-radio-wrap">
                                <label class="custom-radio"><input name="reg_sms[enable]" type="radio" value="1"<?php if($setting['reg_sms']['enable']) echo ' checked';?>><span>Вкл</span></label>
                                <label class="custom-radio"><input name="reg_sms[enable]" type="radio" value="0"<?php if(!$setting['reg_sms']['enable']) echo ' checked';?>><span>Откл</span></label>
                            </span>
                        </div>
                    </div>

                    <div class="col-1-1">
                        <div class="width-100" title="1 SMS = 70 сиволов в кириллице или 160 в латинице"><label>Текст SMS сообщения:</label>
                            <textarea name="reg_sms[text]"><?=$setting['reg_sms']['text'];?></textarea><br />
                        </div>

                        <div>
                            <p>Переменные для подстановки:</p>
                            <p>[NAME] - имя клиента<br />
                               [EMAIL] - e-mail клиента<br />
                               [LINK] - ссылка на личный кабинет<br />
                               [PASS] - пароль<br />
                            </p>
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