<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('STAT_CHANNELS');?></h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/stat/">Статистика</a></li>
        <li><?php echo System::Lang('STAT_CHANNELS');?></li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li><a class="button-yellow-rounding" href="<?php echo $setting['script_url'];?>/admin/stat/"><?php echo System::Lang('BACK');?></a></li>
        </ul>
    </div>

    <div class="admin_form">
    <h3>По каналам</h3>
    <div class="overflow-container">
    <table class="table text-left">
        <thead>
        <tr>
            <th class="text-center">ID</th>
            <th>Название</th>
            <th>Вложено</th>
            <th>Переходы</th>
            <th>Пользователей</th>
            <th>Заказы</th>
            <th>На сумму, <?php echo $setting['currency'];?></th>
            <th>ROI</th>
        </tr>
        </thead>
        <tbody>
        <?php if($channel_list){
            foreach ($channel_list as $channel):?>
        <tr>
            <td class="text-center"><?php echo $channel['channel_id'];?></td>
            <td><a target="_blank" href="<?php echo $setting['script_url'];?>/admin/channels/edit/<?php echo $channel['channel_id'];?>"><?php echo $channel['name'];?></a></td>
            <td><?php echo $channel['summ'];?> <?php echo $setting['currency'];?></td>
            <td><?php echo $channel['hits'];?></td>
            <td><?php $users = Stat::getUserStatByChannel($channel['channel_id']); echo $users;?>
            <?php if($users > 0): echo '<span class="small">( Лид = '.$channel['summ'] / $users .' </span>';?> <?php echo $setting['currency'] . ' )';?><?php endif;?></td>
            <td><?php $orders = Stat::getOrderByChannel($channel['channel_id']); echo '<span title="Оплачено">'.$orders['pay'].'</span>';?> ( <span class="red" title="Не оплачено"><?php echo $orders['nopay'];?> </span> )</td>
            <td><?php echo $orders['summ'];?></td>
            <td><?php if($channel['summ'] > 0): echo round($roi = ($orders['summ'] / $channel['summ'])*100).' %'; endif;?></td>
        </tr>
        <?php endforeach; } else echo '<p>Нет каналов</p>';?>
        </tbody>
    </table>
    </div>
    </div>

    <div class="admin_form admin_form--margin-top">
    <h3>По группам каналов</h3>
        <div class="overflow-container">
    <table class="table text-left">
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Вложено</th>
            <th>Хиты</th>
            <th>Пользователей</th>
            <th>Заказы</th>
            <th>На сумму, <?php echo $setting['currency'];?></th>
            <th>ROI</th>
        </tr>
        
        <?php if($group_list):
        foreach($group_list as $group):?>
        <tr>
            <td><?php echo $group['id'];?></td>
            <td><?php echo $group['name'];?></td>
            <td><?php $data = Stat::getGroupStat($group['id']); if($data['summ'] > 0): echo $data['summ']; echo ' '.$setting['currency']; endif;?> </td>
            <td><?php echo $data['hits'];?></td>
            <td><?php echo $data['users'];?>
            <?php if($data['users'] > 0): echo '<span class="small">( Лид = '.$data['summ'] / $data['users'] .' </span>';?> <?php echo $setting['currency'] . ' )';?><?php endif;?></td>
            <td><span title="Оплачено"><?php echo $data['pay'];?></span> ( <span class="red" title="Не оплачено"><?php echo $data['nopay'];?></span> )</td>
            <td><?php echo $data['amount'];?></td>
            <td><?php if($data['summ'] > 0): echo round($roi = ($data['amount'] / $data['summ'])*100) . ' %'; endif;?></td>
        </tr>
        <?php endforeach;
        endif;?>
    </table>
    </div>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>