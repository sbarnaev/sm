<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Изменить рассрочку</h1>
        <div class="logout">
            <a href="<?=$setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?=$setting['script_url'];?>/admin/logout" class="red">Выход</a>
        </div>
    </div>
    
    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/installment/">Рассрочки</a></li>
        <li>Изменить рассрочку</li>
    </ul>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/new-categ.svg" alt="">
                </div>

                <div>
                    <h3 class="traning-title mb-0">Изменить рассрочку</h3>
                </div>
            </div>
            
            <ul class="nav_button">
                <li><input type="submit" name="edit" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/installment/">Закрыть</a></li>
            </ul>
        </div>

        <div class="tabs">
            <ul>
                <li>Основное</li>
                <li>Одобрение</li>
                <li>Напоминания</li>
                <li>Погашение</li>
            </ul>
            
            <div class="admin_form">
                <div>
                    <div class="row-line">
                        <div class="col-1-2">
                            <h4>Основное</h4>
                            <p class="width-100"><label>Название:</label>
                                <input type="text" name="name" value="<?=$installment['title'];?>" placeholder="Название" required="required">
                            </p>

                            <p class="width-100"><label>Кол-во платежей: <?=$installment['max_periods'];?></label>
                                <input type="hidden" value="<?=$installment['max_periods'];?>" name="max_periods">
                            </p>

                            <p class="width-100"><label>Периодичность платежей, в днях:</label>
                                <input type="text" value="<?=$installment['period_freq'];?>" name="period_freq" placeholder="Время периода">
                            </p>

                            <div class="width-100"><label>Дата второго платежа:</label>
                                <div class="datetimepicker-wrap">
                                    <input class="datetimepicker" type="text" autocomplete="off" name="date_second_payment" value="<?=isset($installment['date_second_payment']) ? date('d.m.Y', $installment['date_second_payment']) : '';?>">
                                </div>
                            </div>

                            <div class="width-100"><label>Статус:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($installment['enable'] == 1) echo 'checked=""'?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($installment['enable'] == 0) echo 'checked=""'?>><span>Откл</span></label>
                                </span>
                                <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                            </div>
							
							<div class="width-100" title="Это когда платёж состоит из 2-х частей"><label>Предоплата:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="prepayment" type="radio" value="1"<?php if($installment['prepayment'] == 1) echo ' checked=""'?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="prepayment" type="radio" value="0"<?php if($installment['prepayment'] == 0) echo ' checked=""'?>><span>Откл</span></label>
                                </span>
                            </div>
                        </div>


                        <div class="col-1-2">
                            <h4>Дополнительно</h4>
                            <p class="width-100"><label>Мин. сумма для рассрочки:</label>
                                <input type="text" value="<?=$installment['minimal'];?>" name="minimal">
                            </p>

                            <p class="width-100"><label>Увеличить сумму рассрочки:</label>
                                <input type="text" value="<?=$installment['increase'];?>" name="increase">
                            </p>

                            <p class="width-100"><label>Сортировка:</label>
                                <input type="text" value="<?=$installment['sort'];?>" name="sort">
                            </p>
                        </div>

                        <div class="col-1-1 mb-0">
                            <h4>Платежи</h4>
                        </div>

                        <div class="col-1-2">
                            <p class="width-100"><label>Первый платёж, %:</label>
                                <input type="text" value="<?=$installment['first_pay']?>" name="first_pay" placeholder="Первый платёж, %">
                            </p>

                            <p class="width-100"><label>Остальные платежи, %:</label>
                                <input type="text" name="other_pay" value="<?=$installment['other_pay'];?>" placeholder="Другие платежи, %">
                            </p>
                        </div>

                        <div class="col-1-2">
                            <p class="width-100"><label>Считать просроченным через X дней:</label>
                                <input type="text" name="expired" value="<?=$installment['expired']?>">
                            </p>

                            <p class="width-100"><label>При просрочке увеличить сумму на:</label>
                                <input type="text" name="sanctions" value="<?=$installment['sanctions']?>">
                            </p>
                        </div>

                        <div class="col-1-1">
                            <h4>Рассрочка (описание, условия)</h4>
                            <p class="width-100" title="Выводится под графиком платежей"><label>Краткое описание условий рассрочки:</label>
                                <textarea class="editor" name="installment_desc"><?=$installment['installment_desc'];?></textarea>
                            </p>

                            <p class="width-100"><label>Текст условий договора рассрочки:</label>
                                <textarea class="editor" name="installment_rules"><?=$installment['installment_rules'];?></textarea>
                            </p>
                        </div>

                        <div class="col-1-1">
                            <h4>Поля для заполнения</h4>
                            <p class="width-100"><label>Серия и номер паспорта:</label>
                                <select name="fields[passport]">
                                    <option value="0"<?php if($fields['passport'] == 0) echo ' selected="selected"';?>>Не показывать</option>
                                    <option value="1"<?php if($fields['passport'] == 1) echo ' selected="selected"';?>>Показывать</option>
                                    <option value="2"<?php if($fields['passport'] == 2) echo ' selected="selected"';?>>Показывать + обязательное</option>
                                </select>
                            </p>

                            <p class="width-100"><label>Город и адрес:</label>
                                <select name="fields[address]">
                                    <option value="0"<?php if($fields['address'] == 0) echo ' selected="selected"';?>>Не показывать</option>
                                    <option value="1"<?php if($fields['address'] == 1) echo ' selected="selected"';?>>Показывать</option>
                                    <option value="2"<?php if($fields['address'] == 2) echo ' selected="selected"';?>>Показывать + обязательное</option>
                                </select>
                            </p>

                            <p class="width-100"><label>Скан 1:</label>
                                <select name="fields[skan1]">
                                    <option value="0"<?php if($fields['skan1'] == 0) echo ' selected="selected"';?>>Не показывать</option>
                                    <option value="1"<?php if($fields['skan1'] == 1) echo ' selected="selected"';?>>Показывать</option>
                                    <option value="2"<?php if($fields['skan1'] == 2) echo ' selected="selected"';?>>Показывать + обязательное</option>
                                </select>
                            </p>

                            <p class="width-100"><label>Скан 2:</label>
                                <select name="fields[skan2]">
                                    <option value="0"<?php if($fields['skan2'] == 0) echo ' selected="selected"';?>>Не показывать</option>
                                    <option value="1"<?php if($fields['skan2'] == 1) echo ' selected="selected"';?>>Показывать</option>
                                    <option value="2"<?php if($fields['skan2'] == 2) echo ' selected="selected"';?>>Показывать + обязательное</option>
                                </select>
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="row-line">
                        <div class="col-1-1">
                            <h4>Автоматическое одобрение</h4>
                                <div class="width-100">

                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="approve" type="radio" value="1" <?php if($installment['approve'] == 1) echo 'checked=""'?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="approve" type="radio" value="0" <?php if($installment['approve'] == 0) echo 'checked=""'?>><span>Откл</span></label>
                                </span>
                            </div>
                        </div>


                        <div class="col-1-1">
                            <h4>Текст на сайте</h4>
                            <p class="width-100"><label>Текст после отправки заявки:</label>
                                <textarea class="editor" name="letters[waiting]"><?=$letters['waiting'];?></textarea>
                            </p>
                        </div>

                        <div class="col-1-1">
                            <h4>SMS-уведомления</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100">
                                <label>Отправлять SMS при одобрении:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="sms[send_good]" type="radio" value="1" <?php if($sms['send_good'] == 1) echo 'checked=""';?>><span>Отправить</span></label>
                                    <label class="custom-radio"><input name="sms[send_good]" type="radio" value="0" <?php if($sms['send_good'] == 0) echo 'checked=""';?>><span>Откл</span></label>
                                </span>
                            </div>

                            <div class="width-100">
                                <label>SMS сообщение :</label>
                                <textarea cols="50" rows="6" name="sms[text_good]"><?=$sms['text_good'];?></textarea>
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100">
                                <label>Отправлять SMS при отказе:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="sms[send_bad]" type="radio" value="1" <?php if($sms['send_bad'] == 1) echo 'checked=""';?>><span>Отправить</span></label>
                                    <label class="custom-radio"><input name="sms[send_bad]" type="radio" value="0" <?php if($sms['send_bad'] == 0) echo 'checked=""';?>><span>Откл</span></label>
                                </span>
                            </div>

                            <div class="width-100">
                                <label>SMS сообщение:</label>
                                <textarea cols="50" rows="6" name="sms[text_bad]"><?=$sms['text_bad'];?></textarea>
                            </div>
                        </div>

                        <div class="col-1-1">
                            <h4>Письма (email)</h4>
                            <p class="width-100"><label>Тема письма одобрения:</label>
                                <input type="text" name="letters[subject_good]" value="<?=$letters['subject_good'];?>">
                            </p>

                            <p class="width-100"><label>Письмо одобрения:</label>
                                <textarea class="editor" name="letters[letter_good]"><?=$letters['letter_good'];?></textarea>
                            </p>

                            <p class="width-100"><hr /></p>

                            <p class="width-100"><label>Тема письма отказа:</label>
                                <input type="text" name="letters[subject_bad]" value="<?=$letters['subject_bad'];?>">
                            </p>

                            <p class="width-100"><label>Письмо отказа:</label>
                                <textarea class="editor" name="letters[letter_bad]"><?=$letters['letter_bad'];?></textarea>
                            </p>
                        </div>


                        <div class="col-1-1">
                            <p class="width-100"><strong>Переменные для подстановки в Email и SMS:</strong><br />
                            <p>[CLIENT_NAME] - имя клиента<br />
                            [EMAIL] - email клиента<br />
                            [ORDER] - номер заказа<br />
                            [LINK] - ссылка на заказ</p>
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="row-line">
                        <div class="col-1-1 mb-40">
                            <h4>Напоминания о платежах</h4>
                        </div>
                    </div>

                    <div class="menu-apsell">
                        <ul>
                            <li>Напоминание №1</li>
                            <li>Напоминание №2</li>
                            <li>Напоминание №3</li>
                        </ul>

                        <div>
                            <div>
                                <div class="row-line">
                                    <div class="col-1-2">
                                        <div class="width-100">
                                            <label>Отправлять Email:</label>
                                            <span class="custom-radio-wrap">
                                                <label class="custom-radio"><input name="notif[send_1_email]" type="radio" value="1"<?php if($notif['send_1_email'] == 1) echo ' checked=""'?>><span>Отправить</span></label>
                                                <label class="custom-radio"><input name="notif[send_1_email]" type="radio" value="0"<?php if($notif['send_1_email'] == 0) echo ' checked=""'?>><span>Откл</span></label>
                                            </span>
                                        </div>

                                        <div class="width-100">
                                            <label>Отправлять SMS:</label>
                                            <span class="custom-radio-wrap">
                                                <label class="custom-radio">
                                                    <input name="notif[send_1_sms]" type="radio" value="1"<?php if($notif['send_1_sms'] == 1) echo ' checked=""'?>><span>Отправить</span>
                                                </label>

                                                <label class="custom-radio">
                                                    <input name="notif[send_1_sms]" type="radio" value="0"<?php if($notif['send_1_sms'] == 0) echo ' checked=""'?>><span>Откл</span>
                                                </label>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-1-2">
                                        <p class="width-100"><label>Отправить за, часов:</label>
                                            <input type="text" name="notif[send_1_time]" value="<?=$notif['send_1_time']?>">
                                        </p>

                                        <div class="width-100">
                                            <label>Текст SMS сообщения:</label>
                                            <textarea name="notif[send_1_smstext]"><?=$notif['send_1_smstext']?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-1-1">
                                        <p class="width-100"><label>Тема для Email сообщения:</label>
                                            <input type="text" name="notif[send_1_subject]" value="<?=$notif['send_1_subject']?>">
                                        </p>

                                        <p class="width-100"><label>Текст для Email сообщения:</label>
                                            <textarea class="editor" name="notif[send_1_text]"><?=$notif['send_1_text']?></textarea>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="row-line">
                                    <div class="col-1-2">
                                        <div class="width-100">
                                            <label>Отправлять Email:</label>
                                            <span class="custom-radio-wrap">
                                                <label class="custom-radio"><input name="notif[send_2_email]" type="radio" value="1"<?php if($notif['send_2_email'] == 1) echo ' checked=""'?>><span>Отправить</span></label>
                                                <label class="custom-radio"><input name="notif[send_2_email]" type="radio" value="0"<?php if($notif['send_2_email'] == 0) echo ' checked=""'?>><span>Откл</span></label>
                                            </span>
                                        </div>

                                        <div class="width-100">
                                            <label>Отправлять SMS:</label>
                                            <span class="custom-radio-wrap">
                                                <label class="custom-radio"><input name="notif[send_2_sms]" type="radio" value="1"<?php if($notif['send_2_sms'] == 1) echo ' checked=""'?>><span>Отправить</span></label>
                                                <label class="custom-radio"><input name="notif[send_2_sms]" type="radio" value="0"<?php if($notif['send_2_sms'] == 0) echo ' checked=""'?>><span>Откл</span></label>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-1-2">
                                        <p class="width-100"><label>Отправить за, часов:</label>
                                            <input type="text" name="notif[send_2_time]" value="<?=$notif['send_2_time']?>">
                                        </p>

                                        <div class="width-100">
                                            <label>Текст SMS сообщения:</label>
                                            <textarea name="notif[send_2_smstext]"><?=$notif['send_2_smstext']?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-1-1">
                                        <p class="width-100"><label>Тема для Email сообщения:</label>
                                            <input type="text" name="notif[send_2_subject]" value="<?=$notif['send_2_subject']?>">
                                        </p>

                                        <p class="width-100"><label>Текст для Email сообщения:</label>
                                            <textarea class="editor" name="notif[send_2_text]"><?=$notif['send_2_text']?></textarea>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="row-line">
                                    <div class="col-1-2">
                                        <div class="width-100">
                                            <label>Отправлять Email:</label>
                                            <span class="custom-radio-wrap">
                                                <label class="custom-radio"><input name="notif[send_3_email]" type="radio" value="1"<?php if($notif['send_3_email'] == 1) echo ' checked=""'?>><span>Отправить</span></label>
                                                <label class="custom-radio"><input name="notif[send_3_email]" type="radio" value="0"<?php if($notif['send_3_email'] == 0) echo ' checked=""'?>><span>Откл</span></label>
                                            </span>
                                        </div>

                                        <div class="width-100">
                                            <label>Отправлять SMS:</label>
                                            <span class="custom-radio-wrap">
                                                <label class="custom-radio"><input name="notif[send_3_sms]" type="radio" value="1"<?php if($notif['send_3_sms'] == 1) echo ' checked=""'?>><span>Отправить</span></label>
                                                <label class="custom-radio"><input name="notif[send_3_sms]" type="radio" value="0"<?php if($notif['send_3_sms'] == 0) echo ' checked=""'?>><span>Откл</span></label>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-1-2">
                                        <p class="width-100"><label>Отправить за, часов:</label>
                                            <input type="text" name="notif[send_3_time]" value="<?=$notif['send_3_time']?>">
                                        </p>

                                        <div class="width-100">
                                            <label>Текст SMS сообщения:</label>
                                            <textarea name="notif[send_3_smstext]"><?=$notif['send_3_smstext']?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-1-1">
                                        <p class="width-100"><label>Тема для Email сообщения:</label>
                                            <input type="text" name="notif[send_3_subject]" value="<?=$notif['send_3_subject']?>">
                                        </p>

                                        <p class="width-100"><label>Текст для Email сообщения:</label>
                                            <textarea class="editor" name="notif[send_3_text]"><?=$notif['send_3_text']?></textarea>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1">
                            <h4 style="color: #E04265">Письма после просрочки платежа</h4>
                        </div>

                        <div class="col-1-2">
                            <h4>1 письмо</h4>
                            <p class="width-100"><label>Отправить через, часов:</label>
                                <input type="text" name="notif[time_1_after]" value="<?=$notif['time_1_after']?>">
                            </p>
                        </div>

                        <div class="col-1-1">
                            <p class="width-100"><label>Тема для Email сообщения:</label><input type="text" name="notif[subject_1_after]" value="<?=$notif['subject_1_after']?>"></p>
                            <p class="width-100"><label>Текст для Email сообщения:</label><textarea class="editor" name="notif[text_1_after]"><?=$notif['text_1_after']?></textarea></p>
                        </div>

                        <div class="col-1-2">
                            <h4>2 письмо</h4>
                            <p class="width-100"><label>Отправить через, часов:</label>
                                <input type="text" name="notif[time_2_after]" value="<?=$notif['time_2_after']?>">
                            </p>
                        </div>

                        <div class="col-1-1">
                            <p class="width-100"><label>Тема для Email сообщения:</label>
                                <input type="text" name="notif[subject_2_after]" value="<?=$notif['subject_2_after']?>">
                            </p>

                            <p class="width-100"><label>Текст для Email сообщения:</label>
                                <textarea class="editor" name="notif[text_2_after]"><?=$notif['text_2_after']?></textarea>
                            </p>
                        </div>

                        <div class="col-1-1">
                            <p class="width-100"><strong>Переменные для подстановки в Email SMS:</strong><br />
                            <p>[CLIENT_NAME] - имя клиента<br />
                            [EMAIL] - email клиента<br />
                            [ORDER] - номер заказа<br />
                            [LINK] - ссылка на заказ</p>
                            </p>
                        </div>
                    </div>
                </div>


                <div>
                    <div class="row-line">
                        <div class="col-1-1">
                            <p class="width-100"><label>Тема письма клиенту после оплаты очередного платежа:</label>
                                <input type="text" name="letters[subject_pay]" value="<?php if(isset($letters['subject_pay'])) echo $letters['subject_pay'];?>">
                            </p>

                            <p class="width-100"><label>Письмо клиенту после оплаты очередного платежа:</label>
                                <textarea class="editor" name="letters[letter_pay]"><?=$letters['letter_pay'];?></textarea>
                            </p>


                            <p class="width-100"><label>Тема письма клиенту после погашения рассрочки:</label>
                                <input type="text" name="letters[subject_client_end]" value="<?=isset($letters['subject_client_end']) ? $letters['subject_client_end'] : '';?>">
                            </p>

                            <p class="width-100"><label>Письмо клиенту после погашения рассрочки:</label>
                                <textarea class="editor" name="letters[letter_client_end]"><?=isset($letters['letter_client_end']) ? $letters['letter_client_end'] : '';?></textarea>
                            </p>


                            <p class="width-100"><hr /></p>

                            <p class="width-100"><label>Тема дополнительного письма после погашения рассрочки:</label>
                                <input type="text" name="letters[subject_end]" value="<?=isset($letters['subject_end']) ? $letters['subject_end']: '';?>">
                            </p>

                            <p class="width-100"><label>Email куда отправить доп.письмо:</label>
                                <input type="text" name="letters[email_end]" value="<?=isset($letters['email_end']) ? $letters['email_end'] : '';?>">
                            </p>

                            <p class="width-100"><label>Текст доп.письма после после погашения рассрочки:</label>
                                <textarea class="editor" name="letters[letter_end]"><?=isset($letters['letter_end']) ? $letters['letter_end'] : '';?></textarea>
                            </p>


                            <p class="width-100"><strong>Переменные для подстановки в письма и SMS:</strong><br />
                            <p>[CLIENT_NAME] - имя клиента<br />
                            [EMAIL] - email клиента<br />
                            [ORDER] - номер заказа<br />
                            [LINK] - ссылка на заказ</p>
                            </p>
                        </div>
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
    format:'d.m.Y',
    lang:'ru'
  });
</script>
</body>
</html>