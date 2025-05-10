<?php defined('BILLINGMASTER') or die;?>
<table class="table" style="margin: 0.5em 0;font-size:12px;">
    <thead>
        <tr>
            <th class="text-left">Период</th>
            <th class="text-right">Всего<br>счетов</th>
            <th class="text-right">На сумму</th>
            <th class="text-right">Всего<br>продаж</th>
            <th class="text-right">На сумму</th>
            <th class="text-right">Принесли<br>партнеры</th>
            <th class="text-right color-red">Не продано</th>
            <th class="text-right">Средний<br>чек</th>
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

            $stat = SummaryStat::getCommonStatistics(null, $end_date); // общие данные статистики до конца месяца
            $stat2 = SummaryStat::getCommonStatistics($start_date, $end_date); // данные статистики в период месяца

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

                <td class="text-right"><!--Всего счетов-->
                    <nobr><span class="green-text">+<?=$stat2['invoices'];?></span></nobr><br>
                    <nobr><small><?=$stat['invoices'];?></strong></small></nobr>
                </td>

                <td class="text-right"><!--На сумму-->
                    <nobr><span class="green-text">+<?=number_format($stat2['sum_invoices'], 0, '.','.');?> р.</span></nobr><br>
                    <nobr><small><?=number_format($stat['sum_invoices'], 0, '.','.');?> р.</small></nobr>
                </td>

                <td class="text-right"><!--Всего продаж-->
                    <nobr><span class="green-text">+<?=$stat2['sales'];?></span></nobr><br>
                    <nobr><small><?=$stat['sales'];?></small></nobr>
                </td>

                <td class="text-right"><!--На сумму-->
                    <nobr><span class="green-text">+<?=number_format($stat2['sum_sales'], 0, '.','.');?> р.</span></nobr><br>
                    <nobr><small><?=number_format($stat['sum_sales'], 0, '.','.');?> р.</small></nobr>
                </td>

                <td class="text-right"><!--Принесли партнеры (new)-->
                    <nobr><span class="green-text">+<?=number_format($stat2['sum_sales_from_partners'], 0, '.','.');?> р.</span></nobr><br>
                    <nobr><small><?=number_format($stat['sum_sales_from_partners'], 0, '.','.');?> р.</small></nobr>
                </td>

                <td class="text-right"><!--Не продано-->
                    <nobr><span class="green-text">+<?=number_format($stat2['sum_invoices'] - $stat2['sum_sales'], 0, '.','.');?> р.</span></nobr><br>
                    <nobr><small><?=number_format($stat['sum_invoices'] - $stat['sum_sales'], 0, '.','.');?> р.</small></nobr>
                </td>

                <td class="text-right"><!--Средний чек-->
                    <nobr><span class="green-text">+<?=$stat2['sales'] ? number_format($stat2['sum_sales'] / $stat2['sales'], 0, '.','.') : 0;?> р.</span></nobr><br>
                    <nobr><small><?=$stat['sales'] ? number_format($stat['sum_sales'] / $stat['sales'], 0, '.','.') : 0;?> р.</small></nobr>
                </td>
            </tr>
        <?php endfor;?>
    </tbody>
</table>