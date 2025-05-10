<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
  <div class="top-wrap">
    <h1>Статистика</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
  </div>
  <ul class="breadcrumb">
    <li>
      <a href="/admin">Дашбоард</a>
    </li>
    <li>Статистика</li>
  </ul>
    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li><a class="button-yellow-rounding" href="<?php echo $setting['script_url'];?>/admin/stat/product/">По продуктам</a></li>
            <li><a class="button-yellow-rounding" href="<?php echo $setting['script_url'];?>/admin/stat/channels/">По каналам</a></li>
        </ul>
    </div>
    <div class="filter admin_form">
            <form action="" method="POST">
                <div class="order-filter-row">

                    <div class="order-filter-1-4">
                        <div class="datetimepicker-wrap">
                            <input type="text" class="datetimepicker" name="start"<?php if($start) echo ' value="'.date('d.m.Y H:i', $start).'"';?> placeholder="От" autocomplete="off">
                        </div>
                    </div>

                    <div class="order-filter-1-4">
                        <div class="datetimepicker-wrap">
                            <input type="text" class="datetimepicker" name="finish"<?php if($finish) echo ' value="'.date('d.m.Y H:i', $finish).'"';?> placeholder="До" autocomplete="off">
                        </div>
                    </div>

                    <div class="order-filter-button">
                        <div class="order-filter-two-row">
                            <div>
                                <div class="order-filter-submit">
                                    <a class="red-link" href="">Сброс</a>
                                    <input class="button-blue-rounding" type="submit" name="filter" value="Фильтр">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
      </div>
        
    <div class="admin_form">
        <div class="row-line">
        <div class="col-1-1">
        <?php if($start&&$finish){?>
          <h4 class="course-list-item__name mb-20">Сводный отчёт за период</h4>
          <div class="overflow-container">
          <table class="table table-fixed">
               <thead>
                <tr>
                    <th class="text-left width-110">Период</th>
                    <th colspan="2" class="width-220">
                      <table class="table-inner">
                        <tr>
                          <td colspan="2">Оплаченные счета</td>
                        </tr>
                        <tr>
                          <td>Количество</td>
                          <td>На сумму</td>
                        </tr>
                      </table>
                    </th>
                  <th colspan="2" class="width-220">
                    <table class="table-inner">
                      <tr>
                        <td colspan="2">Не оплаченные счета</td>
                      </tr>
                      <tr>
                        <td>Количество</td>
                        <td>На сумму</td>
                      </tr>
                    </table>
                  </th>
                    <th class="width-110">Средний чек</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-left">Выбранный период</td>
                    <td>
                      <?php $period = Stat::CountOrders($start,$finish);
                      echo $period['pay'];?>
                    </td>
                  <td>
                    <?php echo number_format($period['summ'], 0, '.','.');?>
                  </td>
                    <td> <?php if($period['nopay'] != 0):?> <span class="small red" title="Не оплаченных"><?php echo $period['nopay'];?> </span> <?php endif;?></td>

                    <td><span class="small red"><?php echo number_format($period['nosumm'], 0, '.','.');?></span></td>
                    <td><?php if($period['pay'] > 0): echo number_format(round($period['summ'] / $period['pay']), 0, '.','.'); endif;?></td>
                </tr>
                </tbody>
            </table>
            </div>

        <?php } else { ?>
            <h4 class="course-list-item__name mb-20">Сводный отчёт</h4>
            <div class="overflow-container">
          <table class="table table-fixed">
               <thead>
                <tr>
                    <th class="text-left width-110">Период</th>
                    <th colspan="2" class="width-220">
                      <table class="table-inner">
                        <tr>
                          <td colspan="2">Оплаченные счета</td>
                        </tr>
                        <tr>
                          <td>Количество</td>
                          <td>На сумму</td>
                        </tr>
                      </table>
                    </th>
                  <th colspan="2" class="width-220">
                    <table class="table-inner">
                      <tr>
                        <td colspan="2">Не оплаченные счета</td>
                      </tr>
                      <tr>
                        <td>Количество</td>
                        <td>На сумму</td>
                      </tr>
                    </table>
                  </th>
                    <th class="width-110">Средний чек</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-left">Сегодня</td>
                    <td>
                      <?php $today = Stat::CountOrders($day);
                      echo $today['pay'];?>
                    </td>
                  <td>
                    <?php echo number_format($today['summ'], 0, '.','.');?>
                  </td>
                    <td> <?php if($today['nopay'] != 0):?> <span class="small red" title="Не оплаченных"><?php echo $today['nopay'];?> </span> <?php endif;?></td>

                    <td><span class="small red"><?php echo number_format($today['nosumm'], 0, '.','.');?></span></td>
                    <td><?php if($today['pay'] > 0): echo number_format(round($today['summ'] / $today['pay']), 0, '.','.'); endif;?></td>
                </tr>

                <tr>
                    <td class="text-left">Вчера</td>
                    <td>
                      <?php $yeday = Stat::CountOrders($yesterday, $day);
                    echo $yeday['pay'];?>
                    </td>
                  <td>
                    <?php echo number_format($yeday['summ'], 0, '.','.');?>
                  </td>
                    <td> <?php if($yeday['nopay'] != 0):?><span class="small red" title="Не оплаченных"><?php echo $yeday['nopay'];?> </span><?php endif;?></td>

                    <td><span class="small red" title="Не оплаченых"><?php echo number_format($yeday['nosumm'], 0, '.','.');?></span></td>
                    <td><?php if($yeday['pay'] > 0): echo number_format(round($yeday['summ'] / $yeday['pay']), 0, '.','.'); endif;?></td>

                </tr>

                <tr>
                    <td class="text-left">за 7 дней</td>
                    <td>
                      <?php $week = Stat::CountOrders($day7);
                    echo $week['pay'];?>
                    </td>
                  <td>
                    <?php echo number_format($week['summ'], 0, '.','.');?>
                  </td>
                    <td><?php if($week['nopay'] != 0):?><span class="small red" title="Не оплаченных"><?php echo $week['nopay'];?> </span><?php endif;?></td>

                    <td><span class="small red" title="Не оплаченых"><?php echo number_format($week['nosumm'], 0, '.','.');?></span></td>
                    <td><?php if($week['pay'] > 0): echo number_format(round($week['summ'] / $week['pay']), 0, '.','.'); endif;?></td>

                </tr>

                <tr>
                    <td class="text-left">за 30 дней</td>
                    <td>
                      <?php $month = Stat::CountOrders($day30);
                    echo $month['pay'];?>
                    </td>
                  <td>
                    <?php echo number_format($month['summ'], 0, '.','.');?>
                  </td>
                    <td><?php if($month['nopay'] != 0):?><span class="small red" title="Не оплаченных"><?php echo $month['nopay'];?> </span><?php endif;?></td>

                  <td><span class="small red" title="Не оплаченых"><?php echo number_format($month['nosumm'], 0, '.','.');?></span></td>
                    <td><?php if($month['pay'] > 0): echo number_format(round($month['summ'] / $month['pay']), 0, '.','.'); endif;?></td>

                </tr>

                <tr>
                    <td class="text-left">за всё время</td>
                    <td>
                      <?php $all = Stat::CountOrders();
                    echo $all['pay'];?>
                    </td>
                  <td>
                    <?php echo number_format($all['summ'], 0, '.','.');?>
                  </td>
                    <td><?php if($all['nopay'] != 0):?><span class="small red" title="Не оплаченных"><?php echo $all['nopay'];?> </span><?php endif;?></td>

                  <td><span class="small red" title="Не оплаченых"><?php echo number_format($all['nosumm'], 0, '.','.');?></span><?php echo $setting['currency']?></td>
                    <td><?php if($all['pay'] > 0): echo number_format(round($all['summ'] / $all['pay']), 0, '.','.'); endif;?> <?php echo $setting['currency']?></td></td>

                </tr>
                </tbody>
            </table>
            </div>
            <?php } ?>
        </div>
        </div>
    </div>
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