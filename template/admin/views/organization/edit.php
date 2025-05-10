<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Изменить организацию</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/organizations/">Организации</a>
        </li>
        <li>Изменить организацию</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно</div>';?>
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/new-categ.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Изменить организацию</h3>
                    <p class="mt-0">для разделения оплаты</p>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="edit_org" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/organizations/">Закрыть</a></li>
            </ul>
        </div>
        <div class="tabs">
            <ul>
                <li>Основное</li>
                <li>Платёжные системы</li>
            </ul>
            
            <div class="admin_form">
                <div>
                    <div class="row-line">
                        <div class="col-1-2">
                            <h4>Основное</h4>
                            <p class="width-100"><label>Название: </label><input type="text" name="name" value="<?=$org['org_name'];?>" required="required"></p>
                            <p class="width-100"><label>Описание организации:</label><textarea rows="4" cols="45" name="org_desc"><?=$org['org_desc'];?></textarea></p>
                            
                            <p class="width-100"><label>Статус:</label>
                                <select name="status">
                                    <option value="1">Включена</option>
                                    <option value="0">Отключена</option>
                                </select>
                            </p>
                            <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                        </div>
                        <div class="col-1-2">
                            <h4>Реквизиты</h4>
                            <p class="width-100"><label>Наименование: </label><input type="text" name="requisits[company]" value="<?=$req['company'];?>"></p>
                            <p class="width-100"><label>ИНН: </label><input type="text" name="requisits[inn]" value="<?=$req['inn'];?>"></p>
                            <p class="width-100"><label>Система налогообложения:</label>
                                <select name="requisits[taxationsystem]">
                                    <option value="0"<?php if($req['taxationsystem'] == 0) echo ' selected="selected"';?>>ОСН</option>
                                    <option value="1"<?php if($req['taxationsystem'] == 1) echo ' selected="selected"';?>>УСН (Доход)</option>
                                    <option value="2"<?php if($req['taxationsystem'] == 2) echo ' selected="selected"';?>>УСН (Доход - расход)</option>
                                    <option value="3"<?php if($req['taxationsystem'] == 3) echo ' selected="selected"';?>>ЕНВД</option>
                                    <option value="5"<?php if($req['taxationsystem'] == 5) echo ' selected="selected"';?>>Патент</option>
                                </select>
                            </p>
                            <p class="width-100"><label>Ставка НДС:</label>
                                <select name="requisits[nds]">
                                <option value=""<?php if($req['nds'] === '') echo ' selected="selected"';?>>НДС не облагается</option>
                                <option value="0"<?php if($req['nds'] === 0) echo ' selected="selected"';?>>НДС по ставке 0%</option>
                                <option value="10"<?php if($req['nds'] == 10) echo ' selected="selected"';?>>НДС по ставке 10%</option>
                                <option value="20"<?php if($req['nds'] == 20) echo ' selected="selected"';?>>НДС чека по ставке 20%</option>
                                <option value="110"<?php if($req['nds'] == 110) echo ' selected="selected"';?>>НДС чека по расчетной ставке 10/110</option>
                                <option value="120"<?php if($req['nds'] == 120) echo ' selected="selected"';?>>НДС чека по расчетной ставке 20/120</option>
                                </select>
                            </p>
                            
                            <p class="width-100"><label>Онлайн касса:</label>
                                <select name="requisits[kassa]">
                                    <option value="1"<?php if($req['kassa'] == 1) echo ' selected="selected"';?>>Подключена</option>
                                    <option value="0"<?php if($req['kassa'] == 0) echo ' selected="selected"';?>>Нет</option>
                                </select>
                            </p>
                        </div>
                        
                        <div class="col-1-1">
                            <h4>Оферта</h4>
                            <p class="width-100"><textarea class="editor" name="oferta"><?=$org['oferta'];?></textarea></p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <div class="row-line">
            
                        <div class="col-1-2">
                            <h4 id="yookassa">Юкасса</h4>
                            <div class="width-100">
								<label>Использовать Я.Кассу</label>
                                <?php $data = Order::getPaymentSetting('yakassapi');?>
								<span class="custom-radio-wrap">
									<label class="custom-radio"><input name="payments[yookassa][enable]" type="radio" value="1" <?php if($payments['yookassa']['enable'] == 1) echo 'checked';?>><span>Да</span></label>
									<label class="custom-radio"><input name="payments[yookassa][enable]" type="radio" value="0" <?php if($payments['yookassa']['enable'] == 0) echo 'checked';?>><span>Нет</span></label>
								</span>
							</div>
                            <p class="width-100"><label>Shop ID: </label><input type="text" value="<?=$payments['yookassa']['shop_id'];?>" name="payments[yookassa][shop_id]"></p>
                            <p class="width-100"><label>Ключ API: </label><input type="text" value="<?=$payments['yookassa']['api_key'];?>" name="payments[yookassa][api_key]"></p>
                            <p class="width-100"><label>Валюта: </label><input type="text" value="<?=$payments['yookassa']['currency'];?>" name="payments[yookassa][currency]">
                            <input type="hidden" name="payments[yookassa][payment_id]" value="<?= $data['payment_id'];?>"></p>
                            
                        </div>
                        
                        
                        <div class="col-1-2">
                            <h4 id="cloud">Cloudpayments</h4>
                            <div class="width-100">
								<label>Использовать Cloudpayments</label>
                                <?php $data = Order::getPaymentSetting('cloudpayments');?>
								<span class="custom-radio-wrap">
									<label class="custom-radio"><input name="payments[cloud][enable]" type="radio" value="1" <?php if($payments['cloud']['enable'] == 1) echo 'checked';?>><span>Да</span></label>
									<label class="custom-radio"><input name="payments[cloud][enable]" type="radio" value="0" <?php if($payments['cloud']['enable'] == 0) echo 'checked';?>><span>Нет</span></label>
								</span>
							</div>
                            <p class="width-100"><label>Public ID: </label><input type="text" value="<?=$payments['cloud']['public_id'];?>" name="payments[cloud][public_id]"></p>
                            <p class="width-100"><label>Пароль API: </label><input type="text" value="<?=$payments['cloud']['api_pass'];?>" name="payments[cloud][api_pass]"></p>
                            <p class="width-100"><label>Валюта: </label><input type="text" value="<?=$payments['cloud']['currency'];?>" name="payments[cloud][currency]">
                            <input type="hidden" name="payments[cloud][payment_id]" value="<?= $data['payment_id'];?>"></p>
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