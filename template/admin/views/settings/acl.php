<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Права доступа менеджеров (уровни)</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
        <ul class="breadcrumb">
            <li>
                <a href="/admin">Дашбоард</a>
            </li>
            <li>Права доступа менеджеров</li>
        </ul>
    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/permissions/add/">Создать уровень доступа</a></li>
        </ul>
    </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
<div class="admin_form admin_form--margin-top">
<div class="overflow-container">
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th class="text-left">Пользователь</th>
            <th class="td-last"></th>
        </tr>
    </thead>
    <tbody>
        <?php if($levels){
        foreach($levels as $level):?>
        <tr>
            <td><?php echo $level['acl_id'];?></td>
            <td class="text-left"><a target="_blank" href="/admin/users/edit/<?php echo $level['user_id']?>"><?php $user_data = User::getUserNameByID($level['user_id']); echo $user_data['user_name'];?></a> - <a href="/admin/permissions/edit/<?php echo $level['acl_id'];?>">Изменить</a></td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('<?php echo System::Lang('YOU_SHURE');?>?')" href="<?php echo $setting['script_url'];?>/admin/permissions/del/<?php echo $level['acl_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="<?php echo System::Lang('DELETE');?>"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach;
        } else echo '<p>No levels</p>'; ?>
    </tbody>
    </table>
</div>
</div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>