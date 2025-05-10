<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Настройки внешнего вида</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/settings/">Настройки</a></li>
        <li>Настройки внешнего вида</li>
    </ul>

    <form action="" method="POST" enctype="multipart/form-data">

        <?php if(isset($_GET['success'])):?>
            <div class="admin_message">Успешно!</div>
        <?php endif;?>
    
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/nastr-icon.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Настройки внешнего вида</h3>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="save_vid" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/settings/">Закрыть</a></li>
            </ul>
        </div>
        
        <div class="tabs">
            <ul>
                <li>Основное</li>
                <li>Настройки главной</li>
                <li>Каталог</li>
                <li>Отзывы</li>
                <li>Оферта</li>
				<li>Свой код</li>
            </ul>
        
            <div class="admin_form">
                <!-- 1 вкладка -->
                <div>
                    <div class="row-line">
                        <div class="col-1-2">
                            <h4>Основное</h4>
                            
                            <div class="width-100"><label>Логотип сайта:</label>
                                <input id="fieldID" type="text" name="logotype" value="<?php if(!empty($setting['logotype'])) echo $setting['logotype'];?>" >
                                <a href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=1&popup=1&field_id=fieldID&relative_url=0', 'okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="btn iframe-btn" type="button">Выбрать изображение</a>
    
                                <?php if(!empty($setting['logotype'])):?>
                                    <div style="margin:1em 0">
                                        <img src="<?=$setting['logotype'];?>" alt="">
                                    </div>
                                <?php endif;?>
                            </div>
                            
                            <div class="width-100"><label>Слоган</label>
                                <input type="text" name="slogan" size="25" value="<?=$setting_main['slogan'];?>">
                            </div>

                            <div class="width-100"><label>Телефон</label>
                                <input type="text" name="phone" placeholder="8(495) 123-34-34" value="<?=$setting['phone'];?>">
                            </div>

                            <div class="width-100"><label>Телефон (ссылка)</label>
                                <input type="text" name="phone_link" placeholder="+74951233443" value="<?=$setting['phone_link'];?>">
                            </div>
                        </div>


                        <div class="col-1-2">
                            <div class="width-100"><label>Зафиксировать шапку:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="fix_head" type="radio" value="1"<?php if($setting['fix_head'] == '1') echo ' checked';?>><span>Да</span></label>
                                    <label class="custom-radio"><input name="fix_head" type="radio" value="0"<?php if($setting['fix_head'] == '0') echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>


							<div class="width-100">
                                <div class="label">Боковая колонка (сайдбар)</div>
                                <div class="select-wrap">
                                    <select name="sidebar">
                                        <option value="left"<?php if(isset($setting_main['sidebar']) && $setting_main['sidebar'] == 'left') echo ' selected="selected"';?>>Слева</option>
                                        <option value="right"<?php if(isset($setting_main['sidebar']) && $setting_main['sidebar'] == 'right') echo ' selected="selected"';?>>Справа</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-1-1 mb-0">
                            <h4>Информация в футере</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Instagram</label>
                                <input type="text" name="socbut[instagram]" value="<?php if(isset($socbut['instagram'])) echo $socbut['instagram']?>">
                            </div>

                            <div class="width-100"><label>Telegram</label>
                                <input type="text" name="socbut[tg]" value="<?php if(isset($socbut['tg'])) echo $socbut['tg']?>">
                            </div>

                            <div class="width-100"><label>Youtube</label>
                                <input type="text" name="socbut[youtube]" value="<?php if(isset($socbut['youtube'])) echo $socbut['youtube']?>">
                            </div>

                            <div class="width-100"><label>Вконтакте</label>
                                <input type="text" name="socbut[vk]" value="<?php if(isset($socbut['vk'])) echo $socbut['vk']?>">
                            </div>

                            <div class="width-100"><label>Facebook</label>
                                <input type="text" name="socbut[fb]" value="<?php if(isset($socbut['fb'])) echo $socbut['fb']?>">
                            </div>

                            <div class="width-100"><label>Одноклассники</label>
                                <input type="text" name="socbut[ok]" value="<?php if(isset($socbut['ok'])) echo $socbut['ok']?>">
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><div class="label">Копирайты</div>
                                <textarea name="copyright" rows="3" cols="55"><?=$setting_main['copyright'];?></textarea>
                            </div>

                            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                        </div>
                    </div>
                </div>


                <!--  Настройки главной страницы -->
                <div>
                    <h4>Главная страница</h4>
                    <div class="row-line">
                        <div class="col-1-2">
                            <div class="width-100">
                                <div class="label">Что выводим на главной</div>
                                <div class="select-wrap">
                                    <select name="main_page_content">
                                        <option value="1"<?php if($setting_main['main_page_content'] == '1') echo ' selected="selected"';?>>Собственный текст</option>
                                        <?php $en_ext_courses = System::CheckExtensension('courses', 1);
                                        if($en_ext_courses):?>
                                            <option value="2"<?php if($setting_main['main_page_content'] == '2') echo ' selected="selected"';?>>Категории тренингов</option>
                                            <option value="4"<?php if($setting_main['main_page_content'] == '4') echo ' selected="selected"';?>>Только список тренингов</option>
                                        <?php endif;

                                        $en_trainings = System::CheckExtensension('training', 1);
                                        if ($en_trainings):?>
                                            <option value="7"<?php if($setting_main['main_page_content'] == '7') echo ' selected="selected"';?>>Тренинги (new)</option>
                                        <?php endif;?>
										<option value="6"<?php if($setting_main['main_page_content'] == '6') echo ' selected="selected"';?>>Блог</option>
                                        <option value="3"<?php if($setting_main['main_page_content'] == '3') echo ' selected="selected"';?>>Форма входа</option>
										<option value="5"<?php if($setting_main['main_page_content'] == '5') echo ' selected="selected"';?>>Загрузить внешний URL</option>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100"><label>Внешний URL</label>
                                <input type="text" name="external_url" value="<?=$setting_main['external_url'];?>">
                            </div>

                            <div class="width-100"><label>Заголовок (title)</label>
                                <input type="text" name="main_page_title" value="<?=$setting_main['main_page_title'];?>" required="required">
                            </div>

                            <div class="width-100"><label>Description</label><br />
                                <textarea name="main_page_desc" rows="3" cols="55"><?=$setting_main['main_page_desc'];?></textarea>
                            </div>

                            <div class="width-100"><label>Keyword</label><br />
                                <textarea name="main_page_keys" rows="3" cols="55"><?=$setting_main['main_page_keys'];?></textarea>
                            </div>
                        </div>

                        <?php if($setting_main['main_page_content'] == '1'):?>
                            <div class="col-1-2">
                                <div class="width-100">
                                    <div class="label">Убрать разметку</div>
                                    <span class="custom-radio-wrap">
                                        <label class="custom-radio"><input name="main_page_tmpl" type="radio" value="1"<?php if($setting_main['main_page_tmpl'] == '1') echo ' checked';?>><span>Нет</span></label>
                                        <label class="custom-radio"><input name="main_page_tmpl" type="radio" value="0"<?php if($setting_main['main_page_tmpl'] == '0') echo ' checked';?>><span>Да</span></label>
                                    </span>
                                </div>
                            </div>
                        <?php endif;?>


                        <div class="col-1-1 mb-0">
                            <h4>Содержание главной страницы</h4>
                        </div>

                        <div class="col-1-1">
                            <div class="width-100"><textarea class="editor" name="main_page_text" rows="3" cols="55"><?=$setting_main['main_page_text'];?></textarea></div>
                        </div>

                        <div class="col-1-2 mb-15">
                            <label>CSS и JS в head</label>
                            <div class="width-100"><textarea name="in_head"><?=$setting['in_head'];?></textarea></div>
                        </div>

                        <div class="col-1-2 mb-15">
                            <label>CSS и JS перед /body</label>
                            <div class="width-100"><textarea name="in_body" rows="6"><?=$setting['in_body'];?></textarea></div>
                        </div>

                        <div class="col-1-1">
                            <p>* CSS: <code>&lt;link rel="stylesheet" href="/template/css/style.css" type="text/css" /&gt;</code><br />
                                * JS: <code>&lt;script src="jquery-1.12.4.min.js"&gt;&lt;/script&gt;</code>
                            </p>
                        </div>
                    </div>
                </div>


                <!--  КАТАЛОГ  -->
                <div>
                    <h4>Настройки страницы каталога продуктов</h4>
                    <div class="row-line">
                        <div class="col-1-2">
                            <div class="width-100"><label>Title для каталога</label>
                                <input type="text" name="catalog_title" value="<?=$setting['catalog_title'];?>">
                            </div>

                            <div class="width-100"><label>Основной заголовок для каталога</label>
                                <input type="text" name="catalog_h1" value="<?=$setting['catalog_h1'];?>">
                            </div>

                            <div class="width-100"><label>Meta desc для каталога</label><br />
                                <textarea name="catalog_desc" cols="55" rows="6"><?=$setting['catalog_desc'];?></textarea>
                            </div>

                            <div class="width-100"><label>Meta keys для каталога</label>
                                <textarea name="catalog_keys" cols="55" rows="6"><?=$setting['catalog_keys'];?></textarea>
                            </div>
                        </div>
                    </div>
                </div>



                <!--  ОТЗЫВЫ   -->
                <div>
                    <h4>Настройки страницы отзывов</h4>
                    <div class="row-line">
                        <?php $reviews_tune = unserialize(base64_decode($setting['reviews_tune']));?>

                        <div class="col-1-2">
                            <div class="width-100"><label>Title для отзывов</label>
                                <input type="text" name="reviews_tune[title]" value="<?=$reviews_tune['title'];?>">
                            </div>

                            <div class="width-100"><label>Основной заголовок для отзывов</label>
                                <input type="text" name="reviews_tune[h1]" value="<?=$reviews_tune['h1'];?>">
                            </div>

                            <div class="width-100"><label>Meta desc для отзывов</label><br />
                                <textarea name="reviews_tune[meta_desc]" cols="55" rows="6"><?=$reviews_tune['meta_desc'];?></textarea>
                            </div>

                            <div class="width-100"><label>Meta keys для отзывов</label><br />
                                <textarea name="reviews_tune[meta_keys]" cols="55" rows="6"><?=$reviews_tune['meta_keys'];?></textarea>
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100">
                                <div class="label">Показать дату отзывов:</div>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="reviews_tune[show_date]" type="radio" value="1" <?php if(!isset($reviews_tune['show_date']) || $reviews_tune['show_date'] == 1) echo 'checked';?>><span>Да</span></label>
                                    <label class="custom-radio"><input name="reviews_tune[show_date]" type="radio" value="0" <?php if(isset($reviews_tune['show_date']) && $reviews_tune['show_date'] == 0) echo 'checked';?>><span>Нет</span></label>
                                </span>
                            </div>
                        </div>

                        <div class="col-1-1 mb-0">
                            <h4>Данные пользователя</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100">
                                <div class="label">Фото</div>
                                <div class="select-wrap">
                                    <select name="reviews_tune[photo]">
                                        <option value="0"<?php if($reviews_tune['photo'] == 0) echo ' selected="selected"';?>>Не запрашивать</option>
                                        <option value="1"<?php if($reviews_tune['photo'] == 1) echo ' selected="selected"';?>>Запрашивать</option>
                                        <option value="2"<?php if($reviews_tune['photo'] == 2) echo ' selected="selected"';?>>Запрашивать обязательно</option>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100">
                                <div class="label">Поле email</div>
                                <div class="select-wrap">
                                    <select name="reviews_tune[email]">
                                        <option value="0"<?php if($reviews_tune['email'] == 0) echo ' selected="selected"';?>>Не запрашивать</option>
                                        <option value="1"<?php if($reviews_tune['email'] == 1) echo ' selected="selected"';?>>Запрашивать</option>
                                        <option value="2"<?php if($reviews_tune['email'] == 2) echo ' selected="selected"';?>>Запрашивать обязательно</option>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100">
                                <div class="label">Ссылка на сайт</div>
                                <div class="select-wrap">
                                    <select name="reviews_tune[site_url]">
                                        <option value="0"<?php if($reviews_tune['site_url'] == 0) echo ' selected="selected"';?>>Не запрашивать</option>
                                        <option value="1"<?php if($reviews_tune['site_url'] == 1) echo ' selected="selected"';?>>Запрашивать</option>
                                        <option value="2"<?php if($reviews_tune['site_url'] == 2) echo ' selected="selected"';?>>Запрашивать обязательно</option>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100">
                                <div class="label">Включить оценку тренинга</div>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="reviews_tune[rate]" type="radio" value="1" <?php if($reviews_tune['rate'] == 1) echo 'checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="reviews_tune[rate]" type="radio" value="0" <?php if($reviews_tune['rate'] == 0) echo 'checked';?>><span>Откл</span></label>
                                </span>
                            </div>
                        </div>


                        <div class="col-1-2">
                            <div class="width-100">
                                <div class="label">Ссылка на профиль ВК</div>
                                <div class="select-wrap">
                                    <select name="reviews_tune[vk_url]">
                                        <option value="0"<?php if($reviews_tune['vk_url'] == 0) echo ' selected="selected"';?>>Не запрашивать</option>
                                        <option value="1"<?php if($reviews_tune['vk_url'] == 1) echo ' selected="selected"';?>>Запрашивать</option>
                                        <option value="2"<?php if($reviews_tune['vk_url'] == 2) echo ' selected="selected"';?>>Запрашивать обязательно</option>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100">
                                <div class="label">Ссылка на профиль Facebook</div>
                                <div class="select-wrap">
                                    <select name="reviews_tune[fb_url]">
                                        <option value="0"<?php if($reviews_tune['fb_url'] == 0) echo ' selected="selected"';?>>Не запрашивать</option>
                                        <option value="1"<?php if($reviews_tune['fb_url'] == 1) echo ' selected="selected"';?>>Запрашивать</option>
                                        <option value="2"<?php if($reviews_tune['fb_url'] == 2) echo ' selected="selected"';?>>Запрашивать обязательно</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-1-1">
                            <p class="width-100"><label>Текст после отправки отзыва</label><br />
                                <textarea class="editor" name="reviews_tune[after_text]" rows="3" cols="55"><?=$reviews_tune['after_text'];?></textarea>
                            </p>
                        </div>
                    </div>
                </div>



                <!--  ОФЕРТА   -->
                <div>
                    <h4>Политика обработки персональных данных</h4>
                    <div class="row-line">
                        <div class="col-1-1">
                            <p class="width-100"><label>Текст для ссылки</label>
                                <input type="text" name="politika_link" placeholder="" value="<?=$setting_main['politika_link'];?>">
                            </p>

                            <div class="width-100">
                                <textarea class="editor" name="politika_text" rows="3" cols="55"><?=$setting_main['politika_text'];?></textarea>
                            </div>
                        </div>


                        <div class="col-1-1 mb-0">
                            <h4>Договор оферты</h4>
                        </div>

                        <div class="col-1-1">
                            <p class="width-100"><label>Текст для ссылки</label>
                                <input type="text" name="oferta_link" placeholder="" value="<?=$setting_main['oferta_link'];?>">
                            </p>

                            <div class="width-100">
                                <textarea class="editor" name="oferta_text" rows="3" cols="55"><?=$setting_main['oferta_text'];?></textarea>
                            </div>
                        </div>
                    </div>
                </div>


				<!--  СВОЙ CSS    -->
                <div>
                    <div class="row-line">
                        <div class="col-1-1">
                            <div class="width-100">
                                <h4>Код в head </h4>
                                <textarea name="counters_head" cols="55" rows="6"><?=$setting['counters_head'];?></textarea>
                                <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                            </div>
                        </div>

                        <div class="col-1-1">
                            <div class="width-100">
                                <h4>Счётчики (перед /body)</h4>
                                <textarea name="counters" cols="55" rows="6"><?=$setting['counters'];?></textarea>
                                <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                            </div>
                        </div>


                        <div class="col-1-1">
                           <h4>Свои CSS стили</h4>
                           <div class="width-100">
                                <textarea name="custom_css" style="height:550px"><?=$setting_main['custom_css'];?></textarea>
                            </div>
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