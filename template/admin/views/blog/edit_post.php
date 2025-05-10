<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo $post['name'];?></h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/blog/">Список записей блога</a></li>
        <li><?php echo $post['name'];?></li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>

<div class="admin_top admin_top-flex">
    <h3 class="traning-title"><a target="_blank" href="/blog/<?=Blog::getRubricAlias($post['rubric_id']);?>/<?=$post['alias'];?>"><?=$post['name'];?></a></h3>
    <ul class="nav_button">
        <li><input type="submit" name="editpost" value="Сохранить" class="button save button-white font-bold"></li>
        <li class="nav_button__last"><a class="button red-link" href="/admin/blog/">Закрыть</a></li>
    </ul>
</div>

    <div class="tabs">
        <ul>
            <li>Основное</li>
            <li>Дополнительно</li>
        </ul>
        <div class="admin_form">
            <!-- 1 вкладка -->
            <div>
                <div class="row-line">
                <div class="mb-0 col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" value="<?php echo $post['name'];?>" placeholder="Название" required="required"></p>

                    <div class="width-100"><label>Статус: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($post['status'] == 1) echo 'checked'; ?>><span>Вкл</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($post['status'] == 0) echo 'checked'; ?>><span>Откл</span></label>
                        </span>
                    </div>

                    <div class="width-100"><label>Категория: </label>
                       <div class="select-wrap">
                        <select name="rub_id">
                    <option value="">Выберите</option>
                    <?php if($rubric_list):
                    foreach($rubric_list as $rubric):?>
                    <option value="<?php echo $rubric['id'];?>"<?php if($post['rubric_id'] == $rubric['id']) echo ' selected="selected"';?>><?php echo $rubric['name'];?></option>
                    <?php endforeach;
                    endif; ?>
                    </select>
                    </div>
                    </div>

                    <div class="width-100"><label>Обложка: </label>
                        <div class="width-100">
                        <input type="file" name="cover">
                    </div>
                    <?php if(!empty($post['post_img'])):?>
                    <div class="del_img_wrap">
                        <img width="150" src="/images/post/cover/<?php echo $post['post_img'];?>" alt="">
                    
                    <input type="hidden" name="current_img" value="<?php echo $post['post_img'];?>">
                    <span class="del_img_link">
                        <button type="submit" form="del_img" title="Удалить изображение с сервера?" name="del_img"><span class="icon-remove"></span></button>
                        </span>
                    </div>
                    <?php endif; ?>
                    </div>

                    <p class="width-100"><label>Alt: </label><input type="text" size="35" value="<?php echo $post['img_alt'];?>" name="img_alt" placeholder="Альтернативный текст"></p>
					<p class="width-100"><label>Порядок: </label><input type="text" value="<?php echo $post['sort'];?>" name="sort"></p>
                    
                    
                    <p class="width-100"><label>Краткое описание (интро): </label><textarea rows="5" cols="65" name="short_desc"><?php echo $post['intro'];?></textarea></p>

                    <div class="width-100"><label>Автор</label>
                        <div class="select-wrap">
                            <select name="author_id">
                                <option value="">-- Выберите --</option>
                                <?php $admins = User::getAdministrationUser();
                                if($admins){
                                    foreach($admins as $admin):?>
                                <option value="<?php echo $admin['user_id'];?>"<?php if($post['author_id'] == $admin['user_id']) echo ' selected="selected"';?>><?php echo $admin['user_name'];?></option>
                                <?php endforeach;
                                }?>
                            </select>
                        </div>
                    </div>
                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>

                <div class="mb-0 col-1-2">
                    <h4>SEO</h4>
                    <p class="width-100"><label>Алиас: </label><input type="text" name="alias" value="<?php echo $post['alias'];?>" placeholder="Алиас записи"></p>
                    <p class="width-100"><label>Title: </label><input type="text" name="title" value="<?php echo $post['title'];?>" placeholder="Title записи"></p>
                    <p class="width-100"><label>Meta Description:</label><textarea name="meta_desc" rows="3" cols="40"><?php echo $post['meta_desc'];?></textarea></p>
                    <p class="width-100"><label>Meta Keys:</label><textarea name="meta_keys" rows="3" cols="40"><?php echo $post['meta_keys'];?></textarea></p>
                </div>

                <div class="col-1-1">
                    <h4>Описание:</h4>
                    <textarea class="editor" name="text"><?php echo $post['text'];?></textarea>
                </div>
                </div>
            </div>
            
            <!-- 2 вкладка -->
            <div>
                <div class="row-line">
                <div class="col-1-2">
                    <h4>Публикация</h4>
                    <p class="width-100"><label>Дата публикации</label><input type="text" class="datetimepicker" value="<?php echo date("d.m.Y H:i", $post['start_date']);?>" name="start" autocomplete="off"></p>
                    <p class="width-100"><label>Дата завершения</label><input type="text" class="datetimepicker" value="<?php echo date("d.m.Y H:i", $post['end_date']);?>" name="end" autocomplete="off"></p>
                </div>
                
                <div class="col-1-2">
                    <h4>Дополнительно</h4>
                    <div class="width-100"><label>Показывать обложку: </label>
                        <div class="select-wrap">
                        <select name="show_cover">
                            <option value="1"<?php if($post['show_cover'] == 1) echo ' selected="selected"'; ?>>Показать</option>
                            <option value="0"<?php if($post['show_cover'] == 0) echo ' selected="selected"'; ?>>Скрыть</option>
                        </select>
                        </div>
                    </div>
                    <p class="width-100"><label>Произвольный HTML:</label><textarea name="custom_code" rows="5" cols="40" style="font-size: 0.95rem;"><?php echo $post['custom_code'];?></textarea></p>
                </div>
                </div>
            </div>

        </div>
    </div>
    </form>
    
    <p class="button-delete">
        <a onclick="return confirm('Вы уверены?')" href="<?php echo $setting['script_url'];?>/admin/blog/del/<?php echo $post['post_id'];?>?token=<?php echo $_SESSION['admin_token'];?>"><i class="icon-remove"></i>Удалить запись</a>
    </p>
    
    <form action="/admin/delimg/<?php echo $post['post_id'];?>" id="del_img" method="POST">
        <input type="hidden" name="path" value="images/blog/cover/<?php echo $post['post_img'];?>">
        <input type="hidden" name="page" value="admin/blog/edit/<?php echo $post['post_id'];?>">
        <input type="hidden" name="table" value="blog_posts">
        <input type="hidden" name="name" value="post_img">
        <input type="hidden" name="where" value="post_id">
        <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
    </form>
    
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
<link rel="stylesheet" type="text/css" href="/template/admin/css/jquery.datetimepicker.min.css">
<script src="/template/admin/js/jquery.datetimepicker.full.min.js"></script>
<script>
jQuery('.datetimepicker').datetimepicker({
format:'d.m.Y H:i',
lang:'ru'
});
</script>
</body>
</html>