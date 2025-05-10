<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Изменить пункт меню</h1>
        <div class="logout">
            <a href="<?=$setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?=$setting['script_url'];?>/admin/logout" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/settings/">Настройки</a></li>
        <li><a href="/admin/menuitems/">Пункты меню</a></li>
        <li>Изменить пункт меню</li>
    </ul>

    <form action="" method="POST">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div><img src="/template/admin/images/icons/item-menu.svg" alt=""></div>
                <div><h3 class="traning-title mb-0">Изменить пункт меню</h3></div>
            </div>

            <ul class="nav_button">
                <li><input type="submit" name="savemenuitem" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?=$setting['script_url'];?>/admin/menuitems/">Закрыть</a></li>
            </ul>
        </div>

        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-1 mb-0">
                    <h4>Основное</h4>
                </div>

                <div class="col-1-2">
                    <p class="width-100"><label>Название:</label>
                        <input type="text" name="name" placeholder="Имя пункта меню" value="<?=$item['name']?>" required="required">
                    </p>

                    <div class="width-100"><label>Выводить в меню:</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="visible" type="radio" value="1" <?php if($item['visible'] == 1) echo 'checked'; ?>><span>Вкл</span></label>
                            <label class="custom-radio"><input name="visible" type="radio" value="0" <?php if($item['visible'] == 0) echo 'checked'; ?>><span>Откл</span></label>
                        </span>
                    </div>

                    <p class="width-100"><label>Статус:</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($item['status'] == 1) echo 'checked'; ?>><span>Вкл</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($item['status'] == 0) echo 'checked'; ?>><span>Откл</span></label>
                        </span>
                    </p>

                    <div class="width-100"><label>Открывать ссылку:</label>
                        <div class="select-wrap">
                            <select name="new_window">
                                <option value="0"<?php if($item['new_window'] == 0) echo ' selected="selected"';?>>В этой же вкладке</option>
                                <option value="1"<?php if($item['new_window'] == 1) echo ' selected="selected"';?>>В новой вкладке</option>
                            </select>
                        </div>
                    </div>

                    <?php if ($type == 'training') {
                        require_once(ROOT . '/extensions/training/views/admin/settings/page.php');
                    };?>

                    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                    <input type="hidden" name="menu_id" value="1">
                </div>

                <div class="col-1-2">
                    <?php if($item['type'] == 'custom'):?>
                        <p class="width-100"><label>Алиас:</label>
                            <input type="text" name="url" placeholder="Алиас адрес" value="<?=$item['link']?>" required="required">
                        </p>
                    <?php elseif($item['type'] == 'static'):?>
                        <p class="width-100"><label>Алиас:</label>
                            <input type="text" name="url" placeholder="Алиас адрес" disabled="disabled" value="<?=$item['link']?>" required="required">
                            <input type="hidden" name="url" value="<?=$item['link']?>">
                        </p>
                    <?php else:?>
                        <p class="width-100"><label>Алиас:</label>
                            <input type="text" name="url" disabled="disabled" placeholder="Алиас адрес" value="<?=$item['link']?>" required="required">
                            <input type="hidden" name="url"  value="<?=$item['link']?>">
                        </p>
                    <?php endif;?>

                    <div><label>Родительский пункт:</label>
                        <div class="select-wrap">
                            <select name="parent_id">
                                <option value="0">Нет</option>
                                <?php if($menu_items):
                                    foreach($menu_items as $mitem):?>
                                        <option value="<?=$mitem['item_id'];?>"<?php if($item['parent_id'] == $mitem['item_id']) echo ' selected="selected"';?>><?=$mitem['name'];?></option>
                                    <?php endforeach;
                                endif;?>
                            </select>
                        </div>
                    </div>

                    <p class="width-100"><label>Порядок:</label>
                        <input type="text" name="sort" value="<?=$item['sort']?>" size="3">
                    </p>
                </div>
            </div>

            <div class="row-line mt-0">
                <div class="col-1-1 mb-0">
                    <h4>SEO</h4>
                </div>

                <div class="col-1-2">
                    <p class="width-100"><label>Title ссылки:</label>
                        <input type="text" value="<?=$item['title']?>" name="title">
                    </p>

                    <div class="width-100"><label>Включить в карту сайта:</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="sitemap" type="radio" value="1" <?php if($item['sitemap'] == 1) echo 'checked'; ?>><span>Вкл</span></label>
                            <label class="custom-radio"><input name="sitemap" type="radio" value="0" <?php if($item['sitemap'] == 0) echo 'checked'; ?>><span>Откл</span></label>
                        </span>
                    </div>

                    <div class="width-100"><label>Частота обновления:</label>
                        <div class="select-wrap">
                            <select name="changefreq">
                                <option value="always"<?php if($item['changefreq'] == 'always') echo ' selected="selected"';?>>Постоянно</option>
                                <option value="hourly"<?php if($item['changefreq'] == 'hourly') echo ' selected="selected"';?>>Каждый час</option>
                                <option value="daily"<?php if($item['changefreq'] == 'daily') echo ' selected="selected"';?>>Каждый день</option>
                                <option value="weekly"<?php if($item['changefreq'] == 'weekly') echo ' selected="selected"';?>>Каждую неделю</option>
                                <option value="monthly"<?php if($item['changefreq'] == 'monthly') echo ' selected="selected"';?>>Каждый месяц</option>
                                <option value="yearly"<?php if($item['changefreq'] == 'yearly') echo ' selected="selected"';?>>Каждый год</option>
                                <option value="never"<?php if($item['changefreq'] == 'never') echo ' selected="selected"';?>>Никогда</option>
                            </select>
                        </div>
                    </div>

                    <div class="width-100"><label>Приоритет сканирования:</label>
                        <div class="select-wrap">
                            <select name="priority">
                                <option value="1.0"<?php if($item['priority'] == '1.0') echo ' selected="selected"';?>>1.0</option>
                                <option value="0.9"<?php if($item['priority'] == '0.9') echo ' selected="selected"';?>>0.9</option>
                                <option value="0.8"<?php if($item['priority'] == '0.8') echo ' selected="selected"';?>>0.8</option>
                                <option value="0.7"<?php if($item['priority'] == '0.7') echo ' selected="selected"';?>>0.7</option>
                                <option value="0.6"<?php if($item['priority'] == '0.6') echo ' selected="selected"';?>>0.6</option>
                                <option value="0.5"<?php if($item['priority'] == '0.5') echo ' selected="selected"';?>>0.5</option>
                                <option value="0.4"<?php if($item['priority'] == '0.4') echo ' selected="selected"';?>>0.4</option>
                                <option value="0.3"<?php if($item['priority'] == '0.3') echo ' selected="selected"';?>>0.3</option>
                                <option value="0.2"<?php if($item['priority'] == '0.2') echo ' selected="selected"';?>>0.2</option>
                                <option value="0.1"<?php if($item['priority'] == '0.1') echo ' selected="selected"';?>>0.1</option>
                            </select>
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