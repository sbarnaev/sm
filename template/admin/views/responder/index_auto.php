<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('AUTO_RESPONDERS');?> (auto)</h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/responder/mass/">Массовые рассылки</a></li>
        <li><?php echo System::Lang('AUTO_RESPONDERS');?> (auto)</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="/admin/responder/add_delivery/"><?php echo System::Lang('ADD_RESPONDER');?></a></li>
            <li><a class="button-yellow-rounding" href="/admin/responder/mass/">Массовые письма</a></li>
        </ul>
    </div>

    <div class="filter">
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
            <th class="text-left">Письма</th>
            <th class="text-left">Дата создания</th>
            <th class="text-left">Подтверждение</th>
            <th class="text-left">Форма</th>
            <th class="td-last"></th>
        </tr>
    </thead>
    <tbody>
        <?php if($delivery_list){
            foreach($delivery_list as $delivery):?>
        <tr>
            <td><?php echo $delivery['delivery_id'];?></td>
            <td class="text-left"><a href="/admin/responder/edit/<?php echo $delivery['delivery_id'];?>"><?php echo $delivery['name'];?></a></td>
            <td class="text-left"><a href="/admin/responder/autoletters/<?php echo $delivery['delivery_id'];?>"><?php echo Responder::countAutoLetters($delivery['delivery_id']);?></a></td>
            <td class="text-left"><?php echo date("d.m.Y", $delivery['create_date']);?></td>
            <td class="text-left"><?php if($delivery['confirmation'] == 1) echo 'Всегда'; elseif($delivery['confirmation'] == 2) echo 'Только через форму'; else echo 'Нет';?></td>
            <td class="text-left"><a href="/admin/subsforms/<?php echo $delivery['delivery_id'];?>">Создать</a></td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/responder/del/<?php echo $delivery['delivery_id'];?>?token=<?php echo $_SESSION['admin_token'];?>&type=<?php echo $delivery['type'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
       <?php endforeach;} else echo 'Вы пока не создали автосерий';?>
    </tbody>
    </table>
</div>
</div>
    <?php if(isset($is_pagination) && $is_pagination == true) echo $pagination->get(); ?>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>