<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1>Изменить профессию</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    <form action="" method="POST" enctype="multipart/form-data">
    <div class="nav_gorizontal">
        <input type="submit" name="editprof" value="Сохранить" class="button save button-green-rounding">
        <a class="button button-red-rounding" href="/admin/courses/profs">Закрыть</a>
    </div>
        
        <div class="admin_form">

                <div class="box2">
                    <h4>Основное</h4>
                    <p><label>Название: </label><input type="text" name="name" placeholder="Название профессии" value="<?php echo $prof['name'];?>" required="required"></p>
                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                
                <div class="box2">
                    <h4>SEO</h4>
                    <p><label>Алиас: </label><input type="text" value="<?php echo $prof['alias'];?>" name="alias" placeholder="Алиас профессии"></p>
                    <p><label>Title: </label><input type="text" value="<?php echo $prof['title'];?>" name="title" placeholder="Title профессии"></p>
                    <p>Meta Description:<br /><textarea name="meta_desc" rows="3" cols="40"><?php echo $prof['meta_desc'];?></textarea></p>
                    <p>Meta Keys:<br /><textarea name="meta_keys" rows="3" cols="40"><?php echo $prof['meta_keys'];?></textarea></p>
                </div>
                
                <div class="box1">
                    <h4>Описание:</h4>
                    <textarea class="editor" name="prof_desc"><?php echo $prof['prof_desc'];?></textarea>
                </div>
                

        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>