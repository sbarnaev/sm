<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Журнал действий</h1>
        <div class="logout">
            <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
        </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Лог действий в админке</li>
    </ul>

    <div class="filter admin_form">
        <form action="" method="POST">
            <div class="order-filter-row">
                <div class="order-filter-1-4">
                    <input type="text" name="phone" placeholder="Телефон" <?php if(isset($_SESSION['sms_filter']['phone'])):?> value="<?php echo $_SESSION['sms_filter']['phone'];?>" <?php endif;?>>
                </div>

                <div class="order-filter-1-4">
                    <div class="datetimepicker-wrap">
                        <input type="text" class="datetimepicker" name="start" <?php if(isset($_SESSION['sms_filter']['start'])) { $start = $_SESSION['sms_filter']['start']; echo " value='$start'";}?> placeholder="От" autocomplete="off">
                    </div>
                </div>

                <div class="order-filter-1-4">
                    <div class="datetimepicker-wrap">
                        <input type="text" class="datetimepicker" name="finish" <?php if(isset($_SESSION['sms_filter']['finish'])) { $finish = $_SESSION['sms_filter']['finish']; echo " value='$finish'";}?> placeholder="До" autocomplete="off">
                    </div>
                </div>

                <div class="order-filter-button">
                    <div class="order-filter-two-row">
                        <div>
                            <?php if(isset($count)):?><div class="order-filter-result"><span>Найдено: <?php echo $count;?></span></div><?php endif;?>
                        </div>
                        <div>
                            <div class="order-filter-submit">
                                <a class="red-link" href="<?php echo $setting['script_url'];?>/admin/smslog?reset">Сброс</a>
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
                <th class="text-left">Телефон</th>
                <th class="text-left">Текст</th>
                <th>Время</th>
                <!--th class="td-last"></th-->
            </tr>
            </thead>
            <tbody>
            <?php if($sms_list){
        foreach($sms_list as $sms):?>
            <tr>
                <td><?php echo $sms['id'];?></td>
                <td class="text-left"><a href="/admin/"><?php echo $sms['phone'];?></a></td>
                <td class="text-left rdr_2"><?php echo $sms['message']?></td>

                <td><?php echo date("d.m.Y H:i:s", $sms['datetime']);?></td>
            </tr>
            <?php endforeach;
        } else echo '<p>No sms</p>'; ?>
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