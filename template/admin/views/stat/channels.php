<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('LIST_CHANNELS');?></h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><?php echo System::Lang('LIST_CHANNELS');?></li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/channels/add/"><?php echo System::Lang('ADD_CHANNEL');?></a></li>
            <li class="nav_gorizontal__parent-wrap">
                <div class="nav_gorizontal__parent nav_gorizontal__parent-yellow">
                    <a href="javascript:void(0);" class="nav-click button-yellow-rounding"><?php echo System::Lang('CHANNEL_GROUP');?></a>
                    <span class="nav-click icon-arrow-down"></span>
                </div>
                <ul class="drop_down">
                    <li><a href="<?php echo $setting['script_url'];?>/admin/channels/group/">Все группы каналов</a></li>
                    <li><a href="<?php echo $setting['script_url'];?>/admin/channels/addgroup/"><?php echo System::Lang('ADD_CHANNEL_GROUP');?></a></li>
                </ul>
            </li>

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
            <th class="text-left">Группа</th>
            <th class="text-left">Сумма</th>
            <th class="text-left">Дата создания</th>
            <th class="text-left">Хиты</th>
            <th class="td-last"></th>
        </tr>
    </thead>
    <tbody>
        <?php if($channel_list){
            foreach ($channel_list as $channel):?>
        <tr>
            <td><?php echo $channel['channel_id'];?></td>
            <td class="text-left"><a href="<?php echo $setting['script_url'];?>/admin/channels/edit/<?php echo $channel['channel_id'];?>"><?php echo $channel['name'];?></a></td>
            <td class="text-left"><?php $group = Stat::getGroupData($channel['group_id']); echo $group['name'];?></td>
            <td class="text-left"><?php echo $channel['summ'];?> <?php echo $setting['currency'];?></td>
            <td class="text-left"><?php echo date("d-m-Y", $channel['create_date']);?></td>
            <td class="text-left"><?php echo $channel['hits'];?></td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="<?php echo $setting['script_url'];?>/admin/channels/del/<?php echo $channel['channel_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach; } else echo '<p>Нет каналов</p>';?>
    </tbody>
    </table>
</div>
</div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>