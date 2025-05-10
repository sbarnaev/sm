<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('USER_GROUP_LIST');?></h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/users/">Пользователи</a>
        </li>
        <li>Группы пользователей</li>
    </ul>

    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/usergroups/add/">Добавить группу</a></li>
        </ul>
    </div>
    
    <!--div class="filter">
    </div-->
<div class="admin_form admin_form--margin-top">
<div class="overflow-container">
        <table class="table">
            <thead>
                <tr>
                    <th class="text-left">ID</th>
					<th class="text-left"><?php echo System::Lang('USER_GROUP_TITLE');?></th>
                    <th class="text-left"><?php echo System::Lang('USER_GROUP_NAME');?></th>
                    <th class="text-left">Люди</th>
                    <th class="td-last"></th>
                </tr>
            </thead>
            <tbody>
                <?php if($groups):
                foreach($groups as $group):?>
                <tr>
                    <td class="text-left"><?php echo $group['group_id'];?></td>
					<td class="text-left"><a href="<?php echo $setting['script_url'];?>/admin/usergroups/edit/<?php echo $group['group_id'];?>"><?php echo $group['group_title'];?></a></td>
                    <td class="text-left"><?php echo $group['group_name'];?></td>
                    <td class="text-left"><?php $users = User::getUsersFromGroup($group['group_id']); if($users) echo count($users); else echo 0;?></td>
                    <td class="td-last"><a class="link-delete" onclick="return confirm('<?php echo System::Lang('YOU_SHURE');?>?')" href="<?php echo $setting['script_url'];?>/admin/usergroups/del/<?php echo $group['group_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="<?php echo System::Lang('DELETE');?>"><i class="fas fa-times" aria-hidden="true"></i></a></td>
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