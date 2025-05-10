<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('REDIRECT_CATS');?></h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/redirect/">Редиректы</a></li>
        <li><?php echo System::Lang('REDIRECT_CATS');?></li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/redirect/addcat/"><?php echo System::Lang('ADD_REDIRECT_CAT');?></a></li>
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/redirect/add/"><?php echo System::Lang('ADD_REDIRECT');?></a></li>
            <li><a class="button-yellow-rounding" href="<?php echo $setting['script_url'];?>/admin/redirect/"><?php echo System::Lang('REDIRECTS_LIST');?></a></li>
        </ul>
    </div>
    
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Содержит редиректы, удалить нельзя!</div>'?>
<div class="admin_form admin_form--margin-top">
<div class="overflow-container">
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th class="text-left">Название</th>
            <th class="text-left">Описание</th>
            <th class="td-last">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if($cat_list){
        foreach($cat_list as $cat):?>
        <tr>
            <td><?php echo $cat['cat_id'];?></td>
            <td class="text-left"><a href="<?php echo $setting['script_url'];?>/admin/redirect/editcat/<?php echo $cat['cat_id'];?>"><?php echo $cat['name'];?></a></td>
            <td class="text-left"><?php echo $cat['cat_desc'];?></td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('<?php echo System::Lang('YOU_SHURE');?>?')" href="<?php echo $setting['script_url'];?>/admin/redirect/delcat/<?php echo $cat['cat_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="<?php echo System::Lang('DELETE');?>"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach;
        } else echo 'No categories'; ?>
    </tbody>
    </table>
</div>
</div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>