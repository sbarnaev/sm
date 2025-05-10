<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php');?>

<div class="main">
    <div class="top-wrap">
        <h1>Статистика</h1>
        <div class="logout">
            <a href="<?=$setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?=$setting['script_url'];?>/admin/logout" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
    <li>Финансовая статистика</li>

    </ul>

    <div class="admin_form">
        <div class="row-line">
            <div class="col-1-1">
                <h4 class="course-list-item__name mb-20">Фин.отчёт</h4>
                <div class="overflow-container">
                    <table class="table" style="margin: 0.5em 0;">
                        <thead>
                            <tr>
                                <th class="text-left">Период</th>
                                <th>Счетов</th>
                                <th>На сумму</th>
                                <th>Продаж</th>
                                <th>На сумму</th>
                                <th>Не продано</th>
                                <th>Первых</th>
                                <th>Повторно</th>
                                <th>База</th>
                                <th>Клиенты</th>
                                <th>С активной<br>подпиской</th>
                                <?/*<th>LTV</th>*/?>
                                <th>Средний</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $stats = []; $prev_users_with_subs = null;
                            for ($i = $current_month - 13; $i <= $current_month; $i++) {
                                $year = $i < 1 ? date('Y') - 1 : date('Y');
                                $month = $i < 1 ? $i + 12 : $i;

                                $next_month = $month + 1 < 13 ? $month + 1 : 1;
                                $year2 = $i + 1 < 1 ? date('Y') - 1 : date('Y');
                                $year2 = $i < 12 ? $year2 : $year2 + 1;

                                $start_date = strtotime("1-$month-$year");
                                $end_date = strtotime("1-$next_month-$year2");

                                $stat = Stat::getSummaryStatistics($start_date, $end_date); // данные статистики в период месяца
                                $stat2 = Stat::getSummaryStatistics(null, $end_date); // общие данные статистики до конца месяца

                                if ($prev_users_with_subs !== null) {
                                    $stat['users_with_subs'] = $stat2['users_with_subs'] - $prev_users_with_subs;
                                }
                                $prev_users_with_subs = $stat2['users_with_subs'];

                                $stats[] = [
                                    'stat' => $stat,
                                    'stat2' => $stat2,
                                    'month' => $month,
                                    'year' => $year,
                                    //'start_date' => date('d-m-Y H-i:s', $start_date),
                                    //'end_date' => date('d-m-Y H-i:s', $end_date),
                                ];
                            }

                            for($i = 13; $i > 0; $i--):
                                $stat = $stats[$i]['stat'];
                                $stat2 = $stats[$i]['stat2'];
                                $month = $stats[$i]['month'];
                                $year = $stats[$i]['year'];?>

                                <tr>
                                    <td class="text-left">
                                        <nobr><?=$i == 13 ? 'Текущий месяц' : "{$months[$month]} $year";?></nobr>
                                    </td>

                                    <td class="text-right">
                                        <nobr><strong class="green-text">+<?=$stat['invoices'];?></strong></nobr><br>
                                        <small><?=$stat2['invoices'];?></small>
                                    </td>

                                    <td class="text-right">
                                        <nobr><strong class="green-text">+<?=number_format($stat['sum_invoices'], 0, '.','.');?> р.</strong></nobr><br>
                                        <small><?=number_format($stat2['sum_invoices'], 0, '.','.');?> р.</small>
                                    </td>

                                    <td class="text-right">
                                        <nobr><strong class="green-text">+<?=$stat['pay_invoices'];?></strong></nobr><br>
                                        <small><?=$stat2['pay_invoices'];?></small>
                                    </td>

                                    <td class="text-right">
                                        <nobr><strong class="green-text">+<?=number_format($stat['pay_sum_invoices'], 0, '.','.');?> р.</strong></nobr><br>
                                        <small><?=number_format($stat2['pay_sum_invoices'], 0, '.','.');?> р.</small>
                                    </td>

                                    <td class="text-right">
                                        <nobr><strong class="green-text">+<?=number_format($stat['sum_invoices'] - $stat['pay_sum_invoices'], 0, '.','.');?> р.</strong></nobr><br>
                                        <small><?=number_format($stat2['sum_invoices'] - $stat2['pay_sum_invoices'], 0, '.','.');?> р.</small>
                                    </td>

                                    <td class="text-right">
                                        <nobr><strong class="green-text">+<?=$stat['first_invoices'];?></strong></nobr><br>
                                        <small><?=$stat2['first_invoices'];?></small>
                                    </td>

                                    <td class="text-right">
                                        <nobr><strong class="green-text">+<?=$stat['repeat_invoices'];?></strong></nobr><br>
                                        <small><?=$stat2['repeat_invoices'];?></small>
                                    </td>

                                    <td class="text-right">
                                        <nobr><strong class="green-text">+<?=$stat['users'];?></strong></nobr><br>
                                        <small><?=$stat2['users'];?></small>
                                    </td>

                                    <td class="text-right">
                                        <nobr><strong class="green-text">+<?=$stat['pay_users'];?></strong></nobr><br>
                                        <small><?=$stat2['pay_users'];?></small>
                                    </td>

                                    <td class="text-right">
                                        <nobr><strong class="green-text"><?=($stat['users_with_subs'] >= 0 ? '+' : '').$stat['users_with_subs'];?></strong></nobr><br>
                                        <small><?=$stat2['users_with_subs'];?></small>
                                    </td>

                                    <?/*<td class="text-right">
                                        <small class="green-text"></small>
                                    </td>*/?>

                                    <td class="text-right">
                                        <nobr><strong class="green-text"><?=$stat['pay_invoices'] ? number_format($stat['pay_sum_invoices'] / $stat['pay_invoices'], 0, '.','.') : 0;?></strong></nobr><br>
                                        <small><?=$stat2['pay_invoices'] ? number_format($stat2['pay_sum_invoices'] / $stat2['pay_invoices'], 0, '.','.') : 0;?></small>
                                    </td>
                                </tr>
                            <?php endfor;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>