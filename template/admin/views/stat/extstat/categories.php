<?php defined('BILLINGMASTER') or die;
$stats = $categories = [];
for ($i = $current_month - 13; $i <= $current_month; $i++) {
    $year = $i < 1 ? date('Y') - 1 : date('Y');
    $month = $i < 1 ? $i + 12 : $i;

    $next_month = $month + 1 < 13 ? $month + 1 : 1;
    $year2 = $i + 1 < 1 ? date('Y') - 1 : date('Y');
    $year2 = $i < 12 ? $year2 : $year2 + 1;

    $start_date = strtotime("1-$month-$year");
    $end_date = strtotime("1-$next_month-$year2");

    $stat = SummaryStat::getCategoryStatistics(null, $end_date); // общие данные статистики до конца месяца
    $stat2 = SummaryStat::getCategoryStatistics($start_date, $end_date); // данные статистики в период месяца

    $stats[] = [
        'stat' => $stat,
        'stat2' => $stat2,
        'month' => $month,
        'year' => $year,
    ];

    if ($i == $current_month) {
        $last_stat = $stat;
    }
}?>

<table class="table" style="margin: 0.5em 0;font-size:12px;">
    <thead>
        <tr>
            <th class="text-left">Период</th>
            <th class="text-right">Всего продаж</th>
            <th class="text-right">На сумму</th>
            <?php foreach ($last_stat['cat_data'] as $category):?>
                <th class="text-right"><?=$category['cat_name'] ? $category['cat_name'] : 'Прочее';?></th>
            <?php endforeach;?>
        </tr>
    </thead>

    <tbody>
        <?php for($i = 13; $i > 0; $i--):
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

                <?php foreach ($last_stat['cat_data'] as $cat_id => $category):
                    $data1 = isset($stat2['cat_data'][$cat_id]) ? $stat2['cat_data'][$cat_id] : null;
                    $data2 = isset($stat['cat_data'][$cat_id]) ? $stat['cat_data'][$cat_id] : null;?>
                    <td class="text-right"><!--Категории-->
                        <nobr><span class="green-text"><?=$data1 ? $data1['sales'].' ('.number_format($data1['sum'], 0, '.','.').' р.)' : '--'?></span></nobr><br>
                        <nobr><small><?=$data2 ? $data2['sales'].' ('.number_format($data2['sum'], 0, '.','.').' р.)' : '--';?></strong></small></nobr>
                    </td>
                <?php endforeach;?>
            </tr>
        <?php endfor;?>
    </tbody>
</table>