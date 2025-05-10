<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Список категорий</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/products/">Продукты</a>
        </li>
        <li>Категории</li>
    </ul>
    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
        <li><a class="button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/category/add/">Создать категорию</a></li>
        </ul>
    </div>
    <?php if(isset($_GET['delete_success'])) echo '<div class="admin_message">Категория успешно удалена</div>';?>
    <?php if(isset($_GET['delete_fail'])) echo '<div class="admin_warning">В категории есть продукты, удалить её не удалось</div>';?>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Категория сохранена</div>';?>
    <div class="admin_form">
<div class="overflow-container">
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th class="text-left">Название</th>
                <th class="td-last"></th>
            </tr>
            </thead>
            <tbody>
            <?php if($cat_list):
            foreach($cat_list as $cat):?>
            <tr>
                <td><?php echo $cat['cat_id'];?></td>
                <td class="text-left"><a href="<?php echo $setting['script_url'];?>/admin/category/edit/<?php echo $cat['cat_id'];?>"><?php echo $cat['cat_name'];?></a></td>
                <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="<?php echo $setting['script_url'];?>/admin/category/del/<?php echo $cat['cat_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
            </tr>
            <?php endforeach;
            endif;?>
            </tbody>
        </table>
    </div>
</div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>