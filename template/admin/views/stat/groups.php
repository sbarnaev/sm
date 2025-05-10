<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('CHANNEL_GROUP');?></h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/channels/">Список каналов</a></li>
        <li>Группы каналов</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li><a class="button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/channels/addgroup/"><?php echo System::Lang('ADD_CHANNEL_GROUP');?></a></li>
            <li><a class="button-yellow-rounding" href="<?php echo $setting['script_url'];?>/admin/channels/"><?php echo System::Lang('LIST_CHANNELS');?></a></li>
        </ul>
    </div>

    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Не возможно удалить!</div>'?>
<div class="admin_form admin_form--margin-top">
<div class="overflow-container">
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th class="text-left">Название</th>
            <th class="text-left">Дата создания</th>
            <th class="td-last"></th>
        </tr>
        </thead>
        <tbody>
        <?php $group_list = Stat::getGroupList();
        if($group_list){
        foreach($group_list as $group):?>
        <tr>
            <td><?php echo $group['id'];?></td>
            <td class="text-left"><a href="<?php echo $setting['script_url'];?>/admin/channels/group/edit/<?php echo $group['id'];?>"><?php echo $group['name'];?></a></td>
            <td class="text-left"><?php echo $group['create_date'];?></td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="<?php echo $setting['script_url'];?>/admin/channels/group/del/<?php echo $group['id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach; } else echo '<p>Нет групп</p>';?>
        </tbody>
    </table>
</div>
</div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>