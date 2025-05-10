<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Самые активные партнёры</h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
</div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/aff/">Партнерка</a>
        </li>
        <li>Самые активные партнёры</li>
    </ul>
    <div class="nav_gorizontal">
    <ul class="nav_gorizontal__ul flex-right">
        <li><a class="button-yellow-rounding" href="/admin/aff/">Назад</a></li>
    </ul>
    </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
<div class="admin_form admin_form--margin-top">
<div class="overflow-container">
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th class="text-left">Имя / Email</th>
            <th>Статус</th>
            <th>Заработано</th>
            <th>Переходов</th>
            <th>Заказов</th>
        </tr>
    </thead>
    <tbody>
        <?php if($users):
        foreach($users as $user):?>
        <tr>
            <td><?php echo $user['user_id'];?></td>
            <td class="text-left"><a href="/admin/users/edit/<?php echo $user['user_id'];?>" target="_blank"><?php $user_data = User::getUserById($user['user_id']); echo $user_data['user_name'];?></a>
            <br /><span class="small"><?php echo $user_data['email'];?></span></td>
            <td><?php if($user_data['status'] == 1) echo '<span class="stat-yes"><i class="icon-stat-yes"></i></span>'; else echo '<span class="stat-no"></span>'; ?></td>
            <td><?php echo $user['summ'];?></td>
            <td><?php echo $hits = Aff::contHitsToPartner($user['user_id']);?></td>
            <td><?php echo $count = Aff::CountOrdersToPartner($user['user_id']);?></td>
        </tr>
        <?php endforeach;
        endif;?>
    </tbody>
    </table>
    <p class="mt-30"><strong>Заработано партнёрами: </strong><?php echo Aff::getPartnerSummTotal(); echo ' '.$setting['currency'];?></p>
  </div>
</div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>