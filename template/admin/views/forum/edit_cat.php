<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1>Изменить категорию форума</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    <form action="" method="POST">
    <div class="nav_gorizontal">
        <ul>
            <li><input type="submit" name="edit_cat" value="Сохранить" class="button save button-green-rounding"></li>
            <li><a class="button button-red-rounding" href="/admin/forum/cats">Закрыть</a></li>
        </ul>
    </div>
    
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" value="<?php echo $cat['name'];?>" placeholder="Название категории" required="required"></p>

                    <div class="width-100"><label>Раздел: </label>
                        <div class="select-wrap">
                        <select name="section">
                    <option value="">-- Выберите --</option>
                    <?php $section_list = Forum::getForumSections();
                    if($section_list):
                    foreach($section_list as $section):?>
                    <option value="<?php echo $section['section_id'];?>"<?php if($section['section_id'] == $cat['section_id']) echo ' selected="selected"';?>><?php echo $section['name'];?></option>
                    <?php endforeach;
                    endif; ?>
                    </select>
                    </div>
                    </div>

                    <div class="width-100"><label>Тип доступа: </label>
                     <div class="select-wrap">
                        <select name="type_access">
                    <option value="1"<?php if($cat['access_type'] == 1) echo ' selected="selected"'; ?>>Группа</option>
                    <?php $membership = System::CheckExtensension('membership', 1);
                    if($membership):?>
                    <option value="2"<?php if($cat['access_type'] == 2) echo ' selected="selected"'; ?>>Подписка</option>
                    <?php endif;?>
                    </select>
                    </div>
                    </div>

                    <div class="width-100"><label>Группа: </label>
                        <select class="multiple-select" name="groups[]" multiple="multiple" size="7">
                    <?php $group_list = User::getUserGroups();
                    $groups = unserialize($cat['groups']);
                    foreach($group_list as $group):?>
                        <option value="<?php echo $group['group_id'];?>"<?php if($groups != null) {if(in_array($group['group_id'], $groups)) echo ' selected="selected"';}?>><?php echo $group['group_title'];?></option>
                    <?php endforeach; ?>
                    </select>
                    </div>

                    <?php if($membership):?>
                    <div class="width-100"><label>Подписка: </label>

                        <select class="multiple-select" name="subs[]" multiple="multiple">
                        <?php $planes = Member::getPlanes();
                        $access_arr = null;
                        if($cat['subs'] != null) $access_arr = unserialize($cat['subs']);
                        if($planes):
                        foreach($planes as $plane):?>
                        <option value="<?php echo $plane['id'];?>"<?php if($access_arr != null) {if(in_array($plane['id'], $access_arr)) echo ' selected="selected"';}?>><?php echo $plane['name'];?></option>
                        <?php endforeach;
                        endif;?>
                    </select>

                    </div>
                    <?php endif; ?>

                    <div class="width-100"><label>Статус: </label>
                        <div class="select-wrap">
                        <select name="status">
                    <option value="1"<?php if($cat['status'] == 1) echo ' selected="selected"';?>>Включен</option>
                    <option value="0"<?php if($cat['status'] == 0) echo ' selected="selected"';?>>Отключен</option>
                    </select>
                    </div>
                    </div>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>

                <div class="col-1-2">
                    <h4>SEO</h4>
                    <p class="width-100"><label>Алиас: </label><input type="text" name="alias" value="<?php echo $cat['alias'];?>" placeholder="Алиас раздела"></p>
                    <p class="width-100"><label>Title: </label><input type="text" name="title" value="<?php echo $cat['title'];?>" placeholder="Title раздела"></p>
                    <p class="width-100"><label>Meta Description:</label><textarea name="meta_desc" rows="3" cols="40"><?php echo $cat['metadesc'];?></textarea></p>
                    <p class="width-100"><label>Meta Keys:</label><textarea name="meta_keys" rows="3" cols="40"><?php echo $cat['metakeys'];?></textarea></p>
                </div>

                <div class="col-1-1">
                    <h4>Описание:</h4>
                    <textarea class="editor" name="cat_desc"><?php echo $cat['cat_desc'];?></textarea>
                </div>
</div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>