<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Список рассрочек</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/products/">Продукты</a>
        </li>
        <li>Рассрочки</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><a class="button-yellow-rounding" href="<?php echo $setting['script_url'];?>/admin/installment/map/">Список договоров</a></li>
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/installment/add/">Создать рассрочку</a></li>
        </ul>
    </div>

    <div class="filter">
    </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Рассрочка используется для оплаты</div>'?>
    <div class="admin_form admin_form--margin-top">
    <div class="overflow-container">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th class="text-left">Название</th>
                    <th class="text-left">Срок</th>
                    <th class="td-last"></th>
                </tr>
                </thead>
                <tbody>
                <?php if($instalment_list):?>
                    <?php foreach($instalment_list as $item):?>
                    <tr>
                        <td><?php echo $item['id'];?></td>
                        <td class="text-left"><a href="/admin/installment/edit/<?php echo $item['id'];?>"><?php echo $item['title'];?></a></td>
                        <td class="td-last"><?php echo $item['max_periods'];?></td>
                        <td><a class="link-delete" onclick="return confirm('Вы уверены?')" href="<?php echo $setting['script_url'];?>/admin/installment/del/<?php echo $item['id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
                    </tr>
                    <?php endforeach;?>
                <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>
	<p>&nbsp;* Для работы рассрочек не забудьте создать задание планировщика CRON. <a href="/admin/cronjobs/">Пример задания</a></p>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>