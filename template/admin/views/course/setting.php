<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Настройки тренингов</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
</div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/extensions/">Расширения</a></li>
        <li>Настройки тренингов</li>
    </ul>
    <form action="" method="POST">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/nastr-tren.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Настройки тренингов</h3>
                </div>
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
              <p class="width-100"><label>Заголовок H1</label><input type="text" name="course[params][h1]" value="<?php echo $params['params']['h1']?>"></p>

              <div class="width-100">
                    <label>Статус</label>
                    <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($enable == 1) echo 'checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($enable == 0) echo 'checked';?>><span>Откл</span></label>
                    </span>
                </div>

                <p class="width-100"><label>Ключ лицензии</label><input type="text" size="40" name="course[params][license]" value="<?php echo $params['params']['license']?>"></p>


                <p class="width-100"><label>Задание планировщика</label><textarea class="vertical-overflow-container" cols="65" rows="3">php <?php echo ROOT ?>/task/course_cron.php</textarea></p>
            </div>
             <div class="col-1-2">
                <p class="width-100"><label>Код комментариев (в head):</label><textarea class="vertical-overflow-container" rows="10" cols="45" name="course[params][commenthead]"><?php echo $params['params']['commenthead']?></textarea></p>
                <div><label>Код комментариев (в body):</label><textarea class="vertical-overflow-container" rows="10" cols="45" name="course[params][commentcode]"><?php echo $params['params']['commentcode']?></textarea></div>
                <p class="width-100"><label>Ширина изображений уроков, px: </label><input type="text" name="course[params][width_less_img]" value="<?php if(isset($params['params']['width_less_img'])) echo $params['params']['width_less_img']; else echo 160;?>"></p>
				
                <div class="width-100"><label>Нумерация блоков:</label>
                <div class="select-wrap">
                <select name="course[params][show_blocks]">
                    <option value="1"<?php if(isset($params['params']['show_blocks']) && $params['params']['show_blocks'] == 1) echo ' selected="selected"';?>>Показать</option>
                    <option value="0"<?php if(isset($params['params']['show_blocks']) && $params['params']['show_blocks'] == 0) echo ' selected="selected"';?>>Не показывать</option>
                </select>
                </div>
                </div>
                <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
            </div>

            </div>
            <h4 class="h4-border mt-20">Блок HERO</h4>
          <div class="row-line">
            <div class="col-1-2">
                <div class="width-100"><label>Фоновое изображение:</label>
            	    <div class="fon-wrap2">
                  <input id="fieldID" type="text" name="course[params][hero]" value="<?php if(isset($params['params']['hero'])) echo $params['params']['hero'];?>" >
            	    <a href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=1&popup=1&field_id=fieldID&relative_url=0', 'okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="btn iframe-btn" type="button">Выбрать изображение</a>
                  </div>
                </div>

                <div class="width-100"><label>Позиция фона:</label>
               <div class="select-wrap">
                <select name="course[params][position]">
                    <option value="center center"<?php if(isset($params['params']['position']) && $params['params']['position'] == 'center center') echo ' selected="selected"';?>>По центру</option>
                    <option value="center top"<?php if(isset($params['params']['position']) && $params['params']['position'] == 'center top') echo ' selected="selected"';?>>Сверху и по центру </option>
                </select>
                    </div>
                </div>
                <p class="width-100"><label>Высота блока, px:</label><input type="text" name="course[params][heroheigh]" value="<?php if(isset($params['params']['heroheigh'])) echo $params['params']['heroheigh']; else echo '';?>"></p>
                <p class="width-100"><label>Высота блока на мобильных, px:</label><input type="text" name="course[params][heromobileheigh]" value="<?php if(isset($params['params']['heromobileheigh'])) echo $params['params']['heromobileheigh']; else echo '';?>"></p>

            </div>
            <div class="col-1-2">
                <p class="width-100"><label>Оверлей цвет:</label><input type="color" name="course[params][overlaycolor]" value="<?php if(isset($params['params']['overlaycolor'])) echo $params['params']['overlaycolor']; else echo '#000000'?>"></p>
                <div class="width-100"><label>Оверлей прозрачность:</label>
                    <div class="select-wrap">
                    <select name="course[params][overlay]">
                    <option value="1.0"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '1.0') echo ' selected="selected"';?>>1.0</option>
                    <option value="0.9"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.9') echo ' selected="selected"';?>>0.9</option>
                    <option value="0.8"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.8') echo ' selected="selected"';?>>0.8</option>
                    <option value="0.7"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.7') echo ' selected="selected"';?>>0.7</option>
                    <option value="0.6"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.6') echo ' selected="selected"';?>>0.6</option>
                    <option value="0.5"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.5') echo ' selected="selected"';?>>0.5</option>
                    <option value="0.4"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.4') echo ' selected="selected"';?>>0.4</option>
                    <option value="0.3"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.3') echo ' selected="selected"';?>>0.3</option>
                    <option value="0.2"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.2') echo ' selected="selected"';?>>0.2</option>
                    <option value="0.1"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.1') echo ' selected="selected"';?>>0.1</option>
                    <option value="0.0"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.0') echo ' selected="selected"';?>>0.0</option>
                </select>
                </div>
                </div>
                
                
                <p class="width-100"><label>Заголовок блока:</label><input type="text" name="course[params][heroheader]" value="<?php if(isset($params['params']['heroheader'])) echo $params['params']['heroheader']; else echo ''?>"></p>
                <p class="width-100"><label>Цвет заголовка:</label><input type="color" name="course[params][color]" value="<?php if(isset($params['params']['color'])) echo $params['params']['color']; else echo '#000000'?>"></p>
                <p class="width-100"><label>Размер заголовка, px:</label><input type="text" name="course[params][fontsize]" value="<?php if(isset($params['params']['fontsize'])) echo $params['params']['fontsize']; else echo '20';?>"></p>
                <p class="width-100"><label>Размер заголовка для мобильных, px:</label><input type="text" name="course[params][fontsize_mobile]" value="<?php if(isset($params['params']['fontsize_mobile'])) echo $params['params']['fontsize_mobile']; else echo '20';?>"></p>

             </div>

            </div>
          <h4 class="h4-border mt-20">SEO</h4>
          <div class="row-line">
            <div class="col-1-2">
                <p class="width-100"><label>Title</label><input type="text" name="course[params][title]" value="<?php echo $params['params']['title']?>"></p>
                <p class="width-100"><label>Description</label><textarea name="course[params][desc]"><?php echo $params['params']['desc']?></textarea></p>
                <p class="width-100"><label>Keyword</label><textarea name="course[params][keys]"><?php echo $params['params']['keys']?></textarea></p>
            </div>
          </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

</body>
</html>