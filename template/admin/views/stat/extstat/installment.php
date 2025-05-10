<?php defined('BILLINGMASTER') or die;
$stats = [];
for ($i = $current_month - 13; $i <= $current_month; $i++) {
    $year = $i < 1 ? date('Y') - 1 : date('Y');
    $month = $i < 1 ? $i + 12 : $i;

    $next_month = $month + 1 < 13 ? $month + 1 : 1;
    $year2 = $i + 1 < 1 ? date('Y') - 1 : date('Y');
    $year2 = $i < 12 ? $year2 : $year2 + 1;

    $start_date = strtotime("1-$month-$year");
    $end_date = strtotime("1-$next_month-$year2");

    $stat = SummaryStat::getInstallmentStatistics(null, $end_date); // общие данные статистики до конца месяца
    $stat2 = SummaryStat::getInstallmentStatistics($start_date, $end_date); // данные статистики в период месяца

    $stats[] = [
        'stat' => $stat,
        'stat2' => $stat2,
        'month' => $month,
        'year' => $year,
    ];
}?>

<table class="table" style="margin: 0.5em 0;font-size:12px;">
    <thead>
        <tr>
            <th class="text-left">Период</th>
            <th class="text-right">Всего<br>продаж</th>
            <th class="text-right">На сумму</th>
            <th class="text-right">Новых<br>рассрочек</th>
            <th class="text-right">Создано<br>обязательств</th>
            <th class="text-right">Должны<br>оплатить</th>
            <th class="text-right">Фактически<br>оплачено</th>
            <th class="text-right">Просрочили</th>
        </tr>
    </thead>

    <tbody>
        <?php  for($i = 13; $i > 0; $i--):
            $stat = $stats[$i]['stat'];
            $stat2 = $stats[$i]['stat2'];
            $month = $stats[$i]['month'];
            $year = $stats[$i]['year'];?>

            <tr>
                <td class="text-left"><!--Период-->
                    <nobr><?=$i == 13 ? 'Текущий месяц' : "{$months[$month]} $year";?></nobr>
                </td>

                <td class="text-right"><!--Всего продаж-->
                    <nobr><span class="green-text">+<?=$stat2['sales']['count'];?></span></nobr><br>
                    <nobr><small><?=$stat['sales']['count'];?></strong></small></nobr>
                </td>

                <td class="text-right"><!--На сумму-->
                    <nobr><span class="green-text">+<?=number_format($stat2['sales']['sum'], 0, '.','.');?> р.</span></nobr><br>
                    <nobr><small><?=number_format($stat['sales']['sum'], 0, '.','.');?> р.</small></nobr>
                </td>

                <td class="text-right"><!--Новых рассрочек-->
                    <nobr><span class="green-text">+<?="{$stat2['new_sales']['count']} (".number_format($stat2['new_sales']['sum'], 0, '.','.').' р.)';?></span></nobr><br>
                    <nobr><small><?="{$stat['new_sales']['count']} (".number_format($stat['new_sales']['sum'], 0, '.','.').' р.)';?></strong></small></nobr>
                </td>

                <td class="text-right"><!--Создано обязательств-->
                    <nobr><span class="green-text">+<?="{$stat2['total_obligations']['count']} (".number_format($stat2['total_obligations']['sum'], 0, '.','.').' р.)';?></span></nobr><br>
                    <nobr><small><?="{$stat['total_obligations']['count']} (".number_format($stat['total_obligations']['sum'], 0, '.','.').' р.)';?></strong></small></nobr>
                </td>

                <td class="text-right"><!--Должны оплатить-->
                    <nobr><span class="green-text"><?=$stat2['sum_sales_not_paid'] ? '+'.number_format($stat2['sum_sales_not_paid'], 0, '.','.').' р.': '--';?></span></nobr><br>
                    <nobr><small><?=$stat['sum_sales_not_paid'] ? number_format($stat['sum_sales_not_paid'], 0, '.','.').' р.' : '--';?></small></nobr>
                </td>

                <td class="text-right"><!--Фактически оплачено-->
                    <nobr><span class="green-text">+<?=number_format($stat2['sum_sales_paid'], 0, '.','.');?> р.</span></nobr><br>
                    <nobr><small><?=number_format($stat['sum_sales_paid'], 0, '.','.');?> р.</small></nobr>
                </td>

                <td class="text-right"><!--Просрочили-->
                    <nobr><span class="green-text"><?=$stat2['expired'] ? "+{$stat2['expired']['count']} (".number_format($stat2['expired']['sum'], 0, '.','.').' р.)' : '--';?></span></nobr><br>
                    <nobr><small><?=$stat['expired'] ? "{$stat['expired']['count']} (".number_format($stat['expired']['sum'], 0, '.','.').' р.)' : '--';?></strong></small></nobr>
                </td>
            </tr>
        <?php endfor;?>
    </tbody>
</table>