<?php defined('BILLINGMASTER') or die;?>
<table class="table" style="margin: 0.5em 0;font-size:12px;">
    <thead>
        <tr>
            <th class="text-left">Период</th>
            <th class="text-right">Всего продаж</th>
            <th class="text-right">На сумму</th>
            <th class="text-right">Первых продаж</th>
            <th class="text-right">Повторных продаж</th>
            <th class="text-right">С активной<br>подпиской</th>
        </tr>
    </thead>

    <tbody>
        <?php $stats = [];
        for ($i = $current_month - 13; $i <= $current_month; $i++) {
            $year = $i < 1 ? date('Y') - 1 : date('Y');
            $month = $i < 1 ? $i + 12 : $i;

            $next_month = $month + 1 < 13 ? $month + 1 : 1;
            $year2 = $i + 1 < 1 ? date('Y') - 1 : date('Y');
            $year2 = $i < 12 ? $year2 : $year2 + 1;

            $start_date = strtotime("1-$month-$year");
            $end_date = strtotime("1-$next_month-$year2");

            $stat = SummaryStat::getMemberStatistics(null, $end_date); // общие данные статистики до конца месяца
            $stat2 = SummaryStat::getMemberStatistics($start_date, $end_date); // данные статистики в период месяца

            $stats[] = [
                'stat' => $stat,
                'stat2' => $stat2,
                'month' => $month,
                'year' => $year,
            ];
        }

        for($i = 13; $i > 0; $i--):
            $stat = $stats[$i]['stat'];
            $stat2 = $stats[$i]['stat2'];
            $month = $stats[$i]['month'];
            $year = $stats[$i]['year'];?>

            <tr>
                <td class="text-left"><!--Период-->
                    <nobr><?=$i == 13 ? 'Текущий месяц' : "{$months[$month]} $year";?></nobr>
                </td>

                <td class="text-right"><!--Всего продаж-->
                    <nobr><span class="green-text">+<?=$stat2['sales'];?></span></nobr><br>
                    <nobr><small><?=$stat['sales'];?></strong></small></nobr>
                </td>

                <td class="text-right"><!--На сумму-->
                    <nobr><span class="green-text">+<?=number_format($stat2['sum_sales'], 0, '.','.');?> р.</span></nobr><br>
                    <nobr><small><?=number_format($stat['sum_sales'], 0, '.','.');?> р.</small></nobr>
                </td>

                <td class="text-right"><!--Первых продаж-->
                    <nobr><span class="green-text">+<?=$stat2['first_sales'].' ('.number_format($stat2['first_sales_sum'], 0, '.','.').' р.)';?></span></nobr><br>
                    <nobr><small><?=$stat['first_sales'].' ('.number_format($stat['first_sales_sum'], 0, '.','.').' р.)';?></small></nobr>
                </td>

                <td class="text-right"><!--Повторных продаж-->
                    <nobr><span class="green-text">+<?=$stat2['repeat_sales'].' ('.number_format($stat2['repeat_sales_sum'], 0, '.','.').' р.)';?></span></nobr><br>
                    <nobr><small><?=$stat['repeat_sales'].' ('.number_format($stat['repeat_sales_sum'], 0, '.','.').' р.)';?></small></nobr>
                </td>

                <td class="text-right"><!--С активной подпиской-->
                    <span class="green-text">+<?=$stat2['users_with_active_subs'];?></span><br>
                    <nobr><small><?=$stat['users_with_active_subs'];?></small></nobr>
                </td>
            </tr>
        <?php endfor;?>
    </tbody>
</table>