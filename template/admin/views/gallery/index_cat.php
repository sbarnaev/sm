<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Категории изображений</h1>
        <div class="logout">
            <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/gallery">Галерея</a>
        </li>
        <li>Категории изображений</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li><a class="button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/gallery/addcat">Создать категорию</a></li>

            <li class="nav_gorizontal__parent-wrap">
                <div class="nav_gorizontal__parent nav_gorizontal__parent-yellow">
                    <a href="<?php echo $setting['script_url'];?>/admin/gallery/add" class="button-yellow-rounding">Добавить изображение</a>
                    <span class="nav-click icon-arrow-down nav-click"></span>
                </div>
                <ul class="drop_down">
                    <li><a href="<?php echo $setting['script_url'];?>/admin/gallery/">Изображения</a></li>
                </ul>
            </li>
            <li><a title="Общие настройки тренингов" class="settings-link" target="_blank" href="/admin/gallerysettings"><i class="icon-settings"></i></a></li>
        </ul>
    </div>
    
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Категория не может буть удалена, в ней содержаться изображения!</div>'?>
<div class="overflow-container">
<table class="table table-striped">
    <thead>
        <tr>
            <th>Обложка</th>
            <th>Категория</th>
            <th class="td-last text-right">Действие</th>
        </tr>
    </thead>
    <tbody>
        <?php if($cat_list){
        foreach($cat_list as $cat):?>
        <?php if($cat['parent_id'] == 0){?>
        <tr<?php if($cat['status'] == 0) echo ' class="off"'; ?>>
            <td style="width: 25%;"><?php if(!empty($cat['cat_cover'])) echo '<img width="200" src="/images/gallery/cats/'.$cat['cat_cover'].'" alt="">';?></td>
            <td style="width: 30%;"><a href="/admin/gallery/editcat/<?php echo $cat['cat_id'];?>"><?php echo $cat['cat_name'];?></a></td>
            <td class="td-last text-right"><a class="link-delete" onclick="return confirm('<?php echo System::Lang('YOU_SHURE');?>?')" href="<?php echo $setting['script_url'];?>/admin/gallery/delcat/<?php echo $cat['cat_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="<?php echo System::Lang('DELETE');?>"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        
        <?php foreach($cat_list as $subcat):
        if($subcat['parent_id'] == $cat['cat_id']):?>
        
        <tr<?php if($subcat['status'] == 0) echo ' class="off"'; ?>>
            <td style="width: 25%;"><?php if(!empty($subcat['cat_cover'])) echo '<img width="100" src="/images/gallery/cats/'.$subcat['cat_cover'].'" alt="">';?></td>
            <td style="width: 30%;">L <a href="/admin/gallery/editcat/<?php echo $subcat['cat_id'];?>"><?php echo $subcat['cat_name'];?></a></td>
            <td class="td-last text-right"><a class="link-delete" onclick="return confirm('<?php echo System::Lang('YOU_SHURE');?>?')" href="<?php echo $setting['script_url'];?>/admin/gallery/delcat/<?php echo $subcat['cat_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="<?php echo System::Lang('DELETE');?>"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        
        <?php endif; 
        endforeach;?>
        
        <?php } ?>
        <?php endforeach;
        } else echo 'No categories'; ?>
    </tbody>
    </table>
</div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>