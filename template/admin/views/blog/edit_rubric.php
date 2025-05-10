<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Редактировать категорию</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <form action="" method="POST" enctype="multipart/form-data">

        <ul class="breadcrumb">
            <li>
                <a href="/admin">Дашбоард</a>
            </li>
            <li><a href="/admin/rubrics/">Список категорий</a></li>
            <li>Редактировать категорию</li>
        </ul>

    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>

<div class="admin_top admin_top-flex">
    <h3 class="traning-title">Редактировать категорию</h3>
    <ul class="nav_button">
        <li><input type="submit" name="edit" value="Сохранить" class="button save button-white font-bold"></li>
        <li class="nav_button__last"><a class="button red-link" href="/admin/rubrics/">Закрыть</a></li>
    </ul>
</div>

<div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" value="<?php echo $rubric['name'];?>" placeholder="Название категории" required="required"></p>

                    <div class="width-100"><label>Статус: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($rubric['status'] == 1) echo 'checked';?>><span>Вкл</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($rubric['status'] == 0) echo 'checked';?>><span>Откл</span></label>
                        </span>
                    </div>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
					
					<h4>Настройка доступа:</h4>
                    <div class="width-100"><label>Тип доступа:</label>
                        <div class="select-wrap">
                            <select id="type_access" name="access_type">
                                <option data-show_on="access2group_box" value="1"<?php if($rubric['access_type'] == 1) echo ' selected="selected"';?>>По группе</option>
                                <?php $membership = System::CheckExtensension('membership', 1);
                                if($membership):?>
                                    <option data-show_on="access2subs_box" value="2"<?php if($rubric['access_type'] == 2) echo ' selected="selected"';?>>По подписке</option>
                                <?php endif;?>
                                <option value="0"<?php if($rubric['access_type'] == 0) echo ' selected="selected"';?>>Свободный доступ</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="width-100 hidden" id="access2group_box"><label>Группа:</label>
                        <select name="groups[]" class="multiple-select" multiple="multiple">
                            <?php $group_list = User::getUserGroups();
                            $groups = json_decode($rubric['groups'], true);
                            foreach($group_list as $group):?>
                                <option value="<?php echo $group['group_id'];?>"<?php if($groups != null && in_array($group['group_id'], $groups)) echo ' selected="selected"';?>>
                                    <?=$group['group_title'];?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if($membership):?>
                    <div class="width-100 hidden" id="access2subs_box"><label>Подписка:</label>
                        <select name="planes[]" class="multiple-select" multiple="multiple">
                            <?php $planes = Member::getPlanes();
                            $access_arr = json_decode($rubric['access'], true);
                            if($planes):
							foreach($planes as $plane):?>
                                <option value="<?=$plane['id'];?>"<?php if($access_arr != null && in_array($plane['id'], $access_arr)) echo ' selected="selected"';?>>
                                    <?=empty($plane['service_name']) ? $plane['name'] : $plane['service_name'];?>
                                </option>
                            <?php endforeach;
							endif;?>
                        </select>
                    </div>
                    <?php endif;?>
					
					
                </div>

                <div class="col-1-2">
                    <h4>SEO</h4>
                    <p class="width-100"><label>Алиас: </label><input type="text" value="<?php echo $rubric['alias'];?>" name="alias" placeholder="Алиас категории"></p>
                    <p class="width-100"><label>Title: </label><input type="text" value="<?php echo $rubric['title'];?>" name="title" placeholder="Title категории"></p>
                    <p class="width-100"><label>Meta Description:</label><textarea name="meta_desc" rows="3" cols="40"><?php echo $rubric['meta_desc'];?></textarea></p>
                    <p class="width-100"><label>Meta Keys:</label><textarea name="meta_keys" rows="3" cols="40"><?php echo $rubric['meta_keys'];?></textarea></p>
                </div>

                <div class="col-1-1">
                    <h4>Описание:</h4>
                    <textarea class="editor" name="short_desc"><?php echo $rubric['short_desc'];?></textarea>
                </div>
            </div>

        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>