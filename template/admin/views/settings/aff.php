<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Настройки партнёрской программы</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/extensions/">Расширения</a></li>
        <li>Настройки партнёрской программы</li>
    </ul>
    <form action="" method="POST">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/nastr-tren.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Настройки партнёрской программы</h3>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="saveaff" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/extensions/">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">

            <div class="row-line">
        
            <div class="col-1-2">
                <h4 class="h4-border">Партнёрка</h4>
                <div class="width-100"><label>Партнёрская программа: </label>
                    <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($enable == 1) echo 'checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($enable == 0) echo 'checked';?>><span>Откл</span></label>
                    </span>
                </div>
                
                <p class="width-100"><label>Название для клиентов</label><input type="text" name="aff[params][title]" value="<?php if(isset($params['params']['title'])) echo $params['params']['title']; else echo 'Партнёрка';?>"></p>
                <p class="width-100"><label>Комиссия 1 уровень, %</label><input type="text" size="3" name="aff[params][aff_1_level]" value="<?php echo $params['params']['aff_1_level']?>"></p>
                <p class="width-100"><label>Комиссия 2-го ур.</label><input type="text" size="3" name="aff[params][aff_2_level]" value="<?php echo $params['params']['aff_2_level']?>"></p>
                <p class="width-100"><label>Комиссия 3-го ур.</label><input type="text" size="3" name="aff[params][aff_3_level]" value="<?php echo $params['params']['aff_3_level']?>"></p>
                <div class="width-100"><label>Группа для партнёра</label>
                    <div class="select-wrap">
                    <select name="aff[params][partner_group]">
                    <option value="">- Выберите -</option>
                    <?php $group_list = User::getUserGroups();
                    foreach($group_list as $group):?>
                    <option value="<?php echo $group['group_id'];?>"<?php if($params['params']['partner_group'] == $group['group_id']) echo 'selected="selected"';?>><?php echo $group['group_title'];?></option>
                    <?php endforeach;?>
                </select>
                </div>
                </div>
                <div class="width-100"><label>Засчитывать партнёра</label>
                   <div class="select-wrap">
                    <select name="aff[params][real_partner]">
                    <option value="1"<?php if($params['params']['real_partner'] == 1) echo ' selected="selected"';?>>Первого кто привёл клиента</option>
                    <option value="0"<?php if($params['params']['real_partner'] == 0) echo ' selected="selected"';?>>Последнего, кто сделал продажу</option>
                </select>
                </div>
                </div>
				
				<div class="width-100"><label>Скрывать емейлы клиентов от партнёра</label>
                   <div class="select-wrap">
                    <select name="aff[params][hidden_email]">
						<option value="1"<?php if(isset($params['params']['hidden_email'])) { if($params['params']['hidden_email'] == 1) echo ' selected="selected"';}?>>Скрывать</option>
						<option value="0"<?php if(isset($params['params']['hidden_email'])) { if($params['params']['hidden_email'] == 0) echo ' selected="selected"';}?>>Не скрывать</option>
					</select>
					</div>
                </div>
                
                <div class="width-100"><label title="На сколько времени ставить куку в браузер, а также сколько дней учитывать партнёра за клиентом">Время учёта партнёра и жизни куки, дни:</label>
                    <input type="text" size="5" name="aff[params][aff_life]" value="<?php if(isset($params['params']['aff_life'])) echo $params['params']['aff_life'];?>">
                </div>
                <?php // echo '<pre>'; print_r($params['params']); echo '</pre>';?>
                
                <div class="width-100"><label>При возврате удалять начисления у партнёра</label>
                    <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="aff[params][delpartnercomiss]" type="radio" value="1" <?php if($params['params']['delpartnercomiss'] == 1) echo 'checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="aff[params][delpartnercomiss]" type="radio" value="0" <?php if($params['params']['delpartnercomiss'] == 0) echo 'checked';?>><span>Откл</span></label>
                    </span>
                </div>
                
				<div class="width-100"><label title="Срок на который удерживается заработок партнёра">Срок возврата, дни:</label>
                    <input type="text" size="5" name="aff[params][return_period]" value="<?php if(isset($params['params']['return_period'])) echo $params['params']['return_period'];?>">
                </div>
				
				
				<div class="width-100"><label>Использовать спец.ссылки на сторонний лендинг</label>
                   <div class="select-wrap">
                       <select name="aff[params][speclinks]">
                           <option data-show_off="get_params" value="1"<?php if(isset($params['params']['speclinks']) && $params['params']['speclinks'] == 1) echo ' selected="selected"';?>>Использовать</option>
                           <option data-show_off="spec_get_params" value="0"<?php if(isset($params['params']['speclinks']) && $params['params']['speclinks'] == 0) echo ' selected="selected"';?>>Нет</option>
                       </select>
                    </div>
                </div>
                
                
				<div class="width-100" id="spec_get_params" title="Имена GET параметров для ссылки, вида: pid=[PID]&prod_id=[PROD_ID]"><label>Параметры для передачи по ссылке:</label>
                    <input type="text" size="5" name="aff[params][speclinks_url]" value="<?php if(isset($params['params']['speclinks_url'])) echo $params['params']['speclinks_url'];?>">
                    <p><span>[PID] - ID партнёра<br />[PROD_ID] - id продукта</span></p>
                </div>
                
                <div id="get_params" class="width-100" title="Используются в случае если на лендинге у вас встроен специальный скрипт проброса GET параметров"><label>Ссылки на лендинг с GET</label>
                    <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="aff[params][get_params]" type="radio" value="1" <?php if(isset($params['params']['get_params']) && $params['params']['get_params'] == 1) echo 'checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="aff[params][get_params]" type="radio" value="0" <?php if(isset($params['params']['get_params']) && $params['params']['get_params'] == 0) echo 'checked';?>><span>Откл</span></label>
                    </span>
                </div>
				
                <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
            </div>
            
            <div class="col-1-2">
                <h4 class="h4-border">Реквизиты</h4>
                <p class="width-100"><label>Разрешённые реквизиты</label><textarea class="vertical-overflow-container" name="aff[params][req]" cols="45" rows="5"><?php echo $params['params']['req'];?></textarea></p>
				<p class="width-100"><label>Примеры:</label><div>yandex=Яндекс.Деньги<br />
				card=Карта Сбербанка<br />
				wm=Webmoney<br />
				qiwi=QIWI Кошелёк<br />
				rs=Расчётный счёт</div>
				</p>
                
                <h4 class="h4-border">SEO</h4>
				<p class="width-100"><label>Meta Desc: </label><textarea class="vertical-overflow-container" name="aff[params][metadesc]" cols="45" rows="5"><?php if(isset($params['params']['metadesc'])) echo $params['params']['metadesc'];?></textarea></p>
                <p class="width-100"><label>Meta Keys: </label><textarea class="vertical-overflow-container" name="aff[params][metakeys]" cols="45" rows="5"><?php if(isset($params['params']['metakeys'])) echo $params['params']['metakeys'];?></textarea></p>
				
            </div>
            
            <!--div class="box2">
                <h4>Авторы</h4>
                <p><label>Расчёт комиссии</label><select name="aff[params][author_calculate]">
                    <option value="1"<?php //if($params['params']['author_calculate'] == 1) echo ' selected="selected"';?>>До партнёров</option>
                    <option value="0"<?php //if($params['params']['author_calculate'] == 0) echo ' selected="selected"';?>>После партнёров</option>
                </select></p>
            </div-->
            
            <div class="col-1-1">
                <h4 class="h4-border">Описание партнёрки</h4>
                <textarea name="aff[params][aff_desc]" class="editor"><?php echo $params['params']['aff_desc'];?></textarea>
            </div>

            </div>
        </div>
        <div class="reference-link">
            <a class="button-blue-rounding" target="_blank" href="https://lk.school-master.ru/rdr/44"><i class="icon-info"></i>Справка по расширению</a>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

</body>
</html>