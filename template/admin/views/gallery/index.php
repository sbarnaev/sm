<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Галерея</h1>
        <div class="logout">
            <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
        </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Галерея</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li><a class="button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/gallery/add">Добавить изображение</a></li>

            <li class="nav_gorizontal__parent-wrap">
                <div class="nav_gorizontal__parent nav_gorizontal__parent-yellow">
                    <a href="<?php echo $setting['script_url'];?>/admin/gallery/addcat" class="button-yellow-rounding">Добавить категорию</a>
                    <span class="nav-click icon-arrow-down nav-click"></span>
                </div>
                <ul class="drop_down">
                    <li><a href="<?php echo $setting['script_url'];?>/admin/gallery/cats">Список категорий</a></li>
                </ul>
            </li>
            <li><a title="Общие настройки тренингов" class="settings-link" target="_blank" href="/admin/gallerysettings"><i class="icon-settings"></i></a></li>
        </ul>
    </div>

    <div class="filter admin_form">
        <form action="" method="POST">
            <div class="filter-row filter-gallery">
                <div class="oblogka">
                    <div class="select-wrap">
                        <select name="cat_id">
                            <?php $cat_list = Gallery::getCatList();
                            if($cat_list):
                            foreach($cat_list as $cat):?>
                            <option value="<?php echo $cat['cat_id'];?>"><?php echo $cat['cat_name'];?></option>
                            <?php endforeach;
                            endif;?>
                        </select>
                    </div>
                </div>
                <div>
                    <div class="button-group">
                        <a class="red-link" href="<?php echo $setting['script_url'];?>/admin/gallery">Сбросить</a>
                        <input class="button-blue-rounding" type="submit" name="filter" value="Найти">
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
<div class="overflow-container">
<table class="table table-striped">
    <thead>
        <tr>
            <th>Изображение</th>
            <th>Название</th>
            <th>Категория</th>
            <th class="td-last text-right">Действие</th>
        </tr>
    </thead>
    <tbody>
        <?php if($img_list){
        foreach($img_list as $img):?>
        <tr<?php if($img['status'] == 0) echo ' class="off"'; ?>>
            <td style="width: 20%;"><img style="width:150px" src="/images/gallery/<?php echo $img['file'];?>" alt=""></td>
            <td style="width: 20%;"><a href="/admin/gallery/edit/<?php echo $img['id'];?>"><?php echo $img['title'];?></a></td>
            <td><?php $cat_name = Gallery::getCatName($img['cat_id']); echo $cat_name;?></td>
            <td class="td-last text-right"><a class="link-delete" onclick="return confirm('<?php echo System::Lang('YOU_SHURE');?>?')" href="<?php echo $setting['script_url'];?>/admin/gallery/del/<?php echo $img['id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="<?php echo System::Lang('DELETE');?>"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach;
        } else echo 'No images'; ?>
    </tbody>
    </table>
</div>
    <?php if(isset($is_pagination) && $is_pagination == true) echo $pagination->get(); ?>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>