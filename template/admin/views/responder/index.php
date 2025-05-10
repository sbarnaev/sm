<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('DELIVERY_LIST');?></h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Массовые рассылки</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li>Время на сервере: <?= date("d.m.y H:i", time());?></li>
			<li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="/admin/responder/add_delivery?type=mass"><?php echo System::Lang('ADD_DELIVERY');?></a></li>
            <li><a class="button-yellow-rounding" href="/admin/responder/auto/"><?php echo System::Lang('AUTO_RESPONDERS');?></a></li>
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
            <th title="В скобках кол-во получаетелей c не корректным email, они не будут отправлены">Получатели</th>
            <th>Дата создания</th>
            <th>Дата отправки</th>
            <th>Отправлено</th>
            <th class="td-last"></th>
        </tr>
    </thead>
    <tbody>
        <?php if($delivery_list){
            foreach($delivery_list as $delivery):?>
        <tr>
            <td><?php echo $delivery['delivery_id'];?></td>
            <td class="text-left"><a href="/admin/responder/edit/<?php echo $delivery['delivery_id'];?>?type=mass"><?php echo $delivery['name'];?></a></td>
            <td><?php echo $delivery['count_letters']; 
                if ($delivery['count_bad']>0){
                    echo '&nbsp(<a href="/admin/subscribers/bad/'.$delivery['delivery_id'].'" title="В скобках кол-во получаетелей c не корректным email, они не будут отправлены" 
                    style="color:red">'.$delivery['count_bad'].'</a>)';}?>
                </td>
            <td>
                <?php echo date("d-m-Y", $delivery['create_date']);?><br>
                <?php echo date("H:i:s", $delivery['create_date']);?>
            </td>
            <td>
                <?php echo date("d-m-Y", $delivery['send_time']);?><br>
                <?php echo date("H:i:s", $delivery['send_time']);?>
            </td>
            <td><?php if($delivery['count_letters'] != 0) {$count = Responder::countSendLetters($delivery['delivery_id']); echo round(($count / $delivery['count_letters'] * 100), 2); } else echo '---';?> %</td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/responder/del/<?php echo $delivery['delivery_id'];?>?token=<?php echo $_SESSION['admin_token'];?>&type=<?php echo $delivery['type'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
       <?php endforeach;} else echo '<tr><td class="text-left" colspan="7">Вы пока не создали массовых рассылок</td></tr>';?>
    </tbody>
    </table>
</div>
</div>
    <?php if(isset($is_pagination) && $is_pagination == true) echo $pagination->get(); ?>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>