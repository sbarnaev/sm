<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Список договоров на рассрочку</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/installment/">Рассрочки</a>
        </li>
        <li>Договора рассрочки</li>
    </ul>

    <div class="nav_gorizontal">
       
    </div>

    <?php $today = date("j");
    $days = date("t");
    $now = time();
    $hour = date("G");
    $end = $now + (($days - $today) * 86400);
    
    $install_pays = Product::getSummFromInstallmentCurrMonth($now, $end);
    if($install_pays):
    $summ = 0;?>
    <div class="filter admin_form">
    <?php foreach ($install_pays as $pay){
        $installment = Product::getInstallmentData($pay['installment_id']);
        $pay_item = ($pay['summ'] / 100) * $installment['other_pay'];
        $summ = $summ + $pay_item;
    }?>
    
        <p class="width-100">Ещё в этом месяце планируется получить платежей на: <?php echo round($summ)?> <?php echo $setting['currency'];?></p>
    </div>
    <?php endif;?>
	
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <div class="admin_form admin_form--margin-top">
    <div class="overflow-container">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th class="text-left">Просмотр</th>
                    <th class="text-left">Email</th>
                    <th class="text-left">Срок</th>
                    <th class="td-last">Сумма</th>
                    <th class="td-last">След. платёж</th>
                    <th class="td-last">Статус</th>
					<th class="td-last">Del</th>
                </tr>
                </thead>
                <tbody>
                <?php if($instalment_map):?>
                    <?php foreach($instalment_map as $item):?>
                    <tr>
                        <td><?php echo $item['id'];?><?php if(!empty($item['comment'])) echo '<span title="Есть комментарий">*</span> ';?></td>
                        <td><a href="/admin/installment/map/<?php echo $item['id'];?>">Просмотр</a></td>
                        <td class="text-left"><?php echo $item['email'];?></td>
                        <td class="text-left"><?php echo $item['max_periods'];?></td>
                        <td class=""><?php echo $item['summ'];?></td>
                        <td><?php if($item['next_pay'] > 0) echo date("d.m.Y", $item['next_pay']);?></td>
                        <td class=""><?php echo getStatus($item['status']);?></td>
						<td><a class="link-delete" onclick="return confirm('Вы уверены?')" href="<?php echo $setting['script_url'];?>/admin/installment/delmap/<?php echo $item['id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
					</tr>
                    <?php endforeach;?>
                <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
	
	<?php function getStatus($status)
    {
        if($status == 1) return 'Активна';
        if($status == 9) return '<span style="color:red">Просрочена</span>';
        if($status == 2) return 'Завершена';
    }
    ?>
</div>
</body>
</html>