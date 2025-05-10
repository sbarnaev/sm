<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Журнал запросов платежей</h1>
        <div class="logout">
            <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
        </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Лог платёжных запросов</li>
    </ul>

    <div class="filter admin_form">
        <form action="" method="POST">
            <div class="order-filter-row">

                <div class="order-filter-1-4">
                    <div class="datetimepicker-wrap">
                        <input type="text" class="datetimepicker" name="start" placeholder="От" autocomplete="off">
                    </div>
                </div>

                <div class="order-filter-1-4">
                    <div class="datetimepicker-wrap">
                        <input type="text" class="datetimepicker" name="finish" placeholder="До" autocomplete="off">
                    </div>
                </div>
				
				<div class="order-filter-1-4">
                    <div class="">
                        <input type="text" name="subscriptionID" placeholder="SubscriptionID">
                    </div>
                </div>
				
				<div class="order-filter-1-4">
                    <div class="">
                        <input type="text" name="email" placeholder="Email">
                    </div>
                </div>

                <div class="order-filter-button">
                    <div class="order-filter-two-row">
                        <div>
                            <div class="order-filter-submit">
                                <a class="red-link" href="<?php echo $setting['script_url'];?>/admin/paylog?reset">Сброс</a>
                                <input class="button-blue-rounding" type="submit" name="filter" value="Фильтр">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
<div class="admin_form admin_form--margin-top">
    <div class="overflow-container">
        <table class="table">
            <thead>
            <tr>
                <th class="text-left">ID</th>
                <th class="text-left">№ заказа</th>
                <th class="text-left"></th>
                <th>Время</th>
                <th>Система</th>
            </tr>
            </thead>
            <tbody>
            <?php if($log_List){
        foreach($log_List as $log):?>
            <tr>
                <td><?php echo $log['id'];?></td>
                <td class="text-left"><?php echo $log['order_date'];?></td>
                <td><a target="_blank" href="/admin/paylog/view/<?php echo $log['id'];?>">Подробнее</a></td>
                <td><?php echo date("d.m.Y H:i:s", $log['transaction_date']);?></td>
                <td><?php if($log['payment_id'] != null) {
                                $payment_data = Order::getPaymentDataForAdmin($log['payment_id']);
                                echo $payment_data['title'];
                            }?></td>
            </tr>
            <?php endforeach;
        } else echo '<p>No list</p>'; ?>
            </tbody>
        </table>
    </div>
</div>
<?php if(isset($is_pagination) && $is_pagination == true) echo $pagination->get(); ?>
<?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

<link rel="stylesheet" type="text/css" href="/template/admin/css/jquery.datetimepicker.min.css">
<script src="/template/admin/js/jquery.datetimepicker.full.min.js"></script>
<script>
  jQuery('.datetimepicker').datetimepicker({
    format:'d.m.Y H:i',
    lang:'ru'
  });
</script>
</body>
</html>