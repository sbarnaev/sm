<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Настройки тренингов</h1>
        <div class="logout">
            <a href="/" target="_blank"><?=System::Lang('GO_SITE');?></a>
            <a href="/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/extensions/">Расширения</a></li>
        <li>Настройки тренингов</li>
    </ul>

    <form action="" method="POST">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div><img src="/template/admin/images/icons/nastr-tren.svg" alt=""></div>
                <div><h3 class="traning-title mb-0">Настройки тренингов</h3></div>
            </div>

            <ul class="nav_button">
                <li><input type="submit" name="save" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/extensions/">Закрыть</a></li>
            </ul>
        </div>

        <div class="admin_form">
            <h4 class="h4-border">Основное</h4>
            <div class="row-line">
                <div class="col-1-2">
                    <p class="width-100"><label>Заголовок H1:</label>
                        <input type="text" name="training[params][h1]" value="<?=$this->tr_settings['h1']?>">
                    </p>

                    <p class="width-100"><label>Подзаголовок H2:</label>
                        <input type="text" name="training[params][h2]" value="<?=isset($this->tr_settings['h2']) ? $this->tr_settings['h2'] : ''?>">
                    </p>
        
                    <div class="width-100">
                        <label>Статус:</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($enable == 1) echo 'checked';?>><span>Вкл</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($enable == 0) echo 'checked';?>><span>Откл</span></label>
                        </span>
                    </div>

                    <p class="width-100"><label>Задание планировщика:</label>
                        <textarea class="vertical-overflow-container" cols="65" rows="3">php <?=ROOT ?>/task/trainig_cron.php</textarea>
                    </p>

                    <div class="width-100"><label>Разрешить удаление домашних заданий кураторам:</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="training[params][allow_del_homework]" type="radio" value="1" <?php if($this->tr_settings['allow_del_homework']) echo 'checked';?>><span>Да</span></label>
                            <label class="custom-radio"><input name="training[params][allow_del_homework]" type="radio" value="0" <?php if(!$this->tr_settings['allow_del_homework']) echo 'checked';?>><span>Нет</span></label>
                        </span>
                    </div>

                    <div class="width-100">
                        <label>Использовать прокрутку до формы при редактировании комментариев:</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="training[params][scroll2comments]" type="radio" value="1" <?php if($this->tr_settings['scroll2comments']) echo 'checked';?>><span>Да</span></label>
                            <label class="custom-radio"><input name="training[params][scroll2comments]" type="radio" value="0" <?php if(!$this->tr_settings['scroll2comments']) echo 'checked';?>><span>Нет</span></label>
                        </span>
                    </div>
                 </div>
                 
                 <div class="col-1-2">
                    <p class="width-100"><label>Код комментариев (в head):</label>
                        <textarea class="vertical-overflow-container" rows="10" cols="45" name="training[params][commenthead]"><?=$this->tr_settings['commenthead']?></textarea>
                    </p>

                    <div><label>Код комментариев (в body):</label>
                        <textarea class="vertical-overflow-container" rows="10" cols="45" name="training[params][commentcode]"><?=$this->tr_settings['commentcode']?></textarea>
                    </div>

                    <p class="px-label-wrap"><label>Ширина изображений уроков:<span class="px-label">px</span></label>
                        <input type="text" name="training[params][width_less_img]" value="<?=isset($this->tr_settings['width_less_img']) ? $this->tr_settings['width_less_img'] : 160;?>">
                    </p>
        			
                    <div class="width-100"><label>Нумерация блоков:</label>
                        <div class="select-wrap">
                            <select name="training[params][show_blocks]">
                                <option value="1"<?php if(isset($this->tr_settings['show_blocks']) && $this->tr_settings['show_blocks'] == 1) echo ' selected="selected"';?>>Показать</option>
                                <option value="0"<?php if(isset($this->tr_settings['show_blocks']) && $this->tr_settings['show_blocks'] == 0) echo ' selected="selected"';?>>Не показывать</option>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                </div>
            </div>


            <h4 class="h4-border mt-20">Блок HERO</h4>
            <div class="row-line">
                <div class="col-1-2">
                    <div class="width-100"><label>Фоновое изображение:</label>
               	        <div class="fon-wrap2">
                            <input id="fieldID" type="text" name="training[params][hero]" value="<?php if(isset($this->tr_settings['hero'])) echo $this->tr_settings['hero'];?>" >
                    	    <a href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=1&popup=1&field_id=fieldID&relative_url=0', 'okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="btn iframe-btn" type="button">Выбрать изображение</a>
                        </div>
                    </div>
        
                    <div class="width-100"><label>Позиция фона:</label>
                        <div class="select-wrap">
                            <select name="training[params][position]">
                                <option value="center center"<?php if(isset($this->tr_settings['position']) && $this->tr_settings['position'] == 'center center') echo ' selected="selected"';?>>По центру</option>
                                <option value="center top"<?php if(isset($this->tr_settings['position']) && $this->tr_settings['position'] == 'center top') echo ' selected="selected"';?>>Сверху и по центру </option>
                            </select>
                        </div>
                    </div>

                    <p class="px-label-wrap"><label>Высота блока:<span class="px-label">px</span></label>
                        <input type="text" name="training[params][heroheigh]" value="<?=isset($this->tr_settings['heroheigh'])? $this->tr_settings['heroheigh'] : '';?>">
                    </p>

                    <p class="px-label-wrap"><label>Высота блока на мобильных:<span class="px-label">px</span></label>
                        <input type="text" name="training[params][heromobileheigh]" value="<?=isset($this->tr_settings['heromobileheigh'])? $this->tr_settings['heromobileheigh'] : '';?>">
                    </p>
                </div>
                
                <div class="col-1-2">
                    <p class="width-100"><label>Оверлей цвет:</label><input type="color" name="training[params][overlaycolor]" value="<?php if(isset($this->tr_settings['overlaycolor'])) echo $this->tr_settings['overlaycolor']; else echo '#000000'?>"></p>
                    <div class="width-100"><label>Оверлей прозрачность:</label>
                        <div class="select-wrap">
                            <select name="training[params][overlay]">
                                <option value="1.0"<?php if(isset($this->tr_settings['overlay']) && $this->tr_settings['overlay'] == '1.0') echo ' selected="selected"';?>>1.0</option>
                                <option value="0.9"<?php if(isset($this->tr_settings['overlay']) && $this->tr_settings['overlay'] == '0.9') echo ' selected="selected"';?>>0.9</option>
                                <option value="0.8"<?php if(isset($this->tr_settings['overlay']) && $this->tr_settings['overlay'] == '0.8') echo ' selected="selected"';?>>0.8</option>
                                <option value="0.7"<?php if(isset($this->tr_settings['overlay']) && $this->tr_settings['overlay'] == '0.7') echo ' selected="selected"';?>>0.7</option>
                                <option value="0.6"<?php if(isset($this->tr_settings['overlay']) && $this->tr_settings['overlay'] == '0.6') echo ' selected="selected"';?>>0.6</option>
                                <option value="0.5"<?php if(isset($this->tr_settings['overlay']) && $this->tr_settings['overlay'] == '0.5') echo ' selected="selected"';?>>0.5</option>
                                <option value="0.4"<?php if(isset($this->tr_settings['overlay']) && $this->tr_settings['overlay'] == '0.4') echo ' selected="selected"';?>>0.4</option>
                                <option value="0.3"<?php if(isset($this->tr_settings['overlay']) && $this->tr_settings['overlay'] == '0.3') echo ' selected="selected"';?>>0.3</option>
                                <option value="0.2"<?php if(isset($this->tr_settings['overlay']) && $this->tr_settings['overlay'] == '0.2') echo ' selected="selected"';?>>0.2</option>
                                <option value="0.1"<?php if(isset($this->tr_settings['overlay']) && $this->tr_settings['overlay'] == '0.1') echo ' selected="selected"';?>>0.1</option>
                                <option value="0.0"<?php if(isset($this->tr_settings['overlay']) && $this->tr_settings['overlay'] == '0.0') echo ' selected="selected"';?>>0.0</option>
                            </select>
                        </div>
                    </div>
                    
                    <p class="width-100"><label>Заголовок блока:</label>
                        <input type="text" name="training[params][heroheader]" value="<?=isset($this->tr_settings['heroheader']) ? $this->tr_settings['heroheader'] : '';?>">
                    </p>

                    <p class="width-100"><label>Цвет заголовка:</label>
                        <input type="color" name="training[params][color]" value="<?=isset($this->tr_settings['color']) ? $this->tr_settings['color'] : '#000000';?>">
                    </p>

                    <p class="px-label-wrap">
                        <label>Размер заголовка:<span class="px-label">px</span></label>
                        <input type="text" name="training[params][fontsize]" value="<?=isset($this->tr_settings['fontsize']) ? $this->tr_settings['fontsize'] : '20';?>">
                    </p>

                    <p class="px-label-wrap">
                        <label>Размер заголовка для мобильных:<span class="px-label">px</span></label>
                        <input type="text" name="training[params][fontsize_mobile]" value="<?=isset($this->tr_settings['fontsize_mobile']) ? $this->tr_settings['fontsize_mobile'] : '20';?>">
                    </p>
                 </div>
            </div>
            <div class="row-line">
                <div class="col-1-1 mb-0">
                    <h4>SEO</h4>
                </div>
                <div class="col-1-2">
                    <p class="width-100"><label>Title</label><input type="text" name="training[params][title]" value="<?=$this->tr_settings['title']?>"></p>
                </div>
            </div>

            <div class="row-line mt-20">
                <div class="col-1-2">
                    <p class="width-100"><label>Description</label><textarea name="training[params][meta_desc]"><?=isset($this->tr_settings['meta_desc']) ? $this->tr_settings['meta_desc'] : ''?></textarea></p>
                </div>
                <div class="col-1-2">
                    <p class="width-100"><label>Keyword</label><textarea name="training[params][meta_keys]"><?=isset($this->tr_settings['meta_keys']) ? $this->tr_settings['meta_keys'] : '';?></textarea></p>
                </div>
            </div>
        </div>
        <div class="reference-link">
            <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/category/50576"><i class="icon-info"></i>Справка по расширению</a>
        </div>
    </form>

    <?php require_once (ROOT . '/extensions/training/layouts/admin/admin-footer.php');?>
</div>
</body>
</html>