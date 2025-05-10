<?php defined('BILLINGMASTER') or die;?>
    <!-- 1 Основное -->
    <div>
    <div class="table-responsive">
        <table class="usertable fz-14">
            <tr>
                <th><?=System::Lang('PERIOD');?></th>
                <th class="text-right"><?=System::Lang('CLIKS');?></th>
                <th class="text-right"><?=System::Lang('INVOISES_ISSUED');?></th>
                <th class="text-right"><?=System::Lang('INVOISES_PAID');?></th>
                <th class="text-right"><?=System::Lang('EARNED');?></th>
            </tr>
            
            <?php
               
               $cur_period = new Datetime(date('Ym',time()));
               $count_month_has_date = $count_month_has_date > 12 ? 12 : $count_month_has_date; 
               for($i = 0; $i <= $count_month_has_date; $i++):
                    $kol1 = $kol2 = $kol3 = $kol4 = '-';
                    $cur_period_str = date('Ym', date_timestamp_get($cur_period));
                    $a = array_search($cur_period_str, array_column($main_table,'period'));
                    if ($a !== false){
                        $kol1 = $main_table[$a]['hits'];
                        $kol2 = $main_table[$a]['invoice'];
                        $kol3 = $main_table[$a]['pay_invoice'];
                        $kol4 = $main_table[$a]['payments'].'&nbsp'.$setting['currency'];
                    }?>
                    <tr>
                        <?php if (date('Ym',time()) === $cur_period_str) {
                            $period_prn = 'Текущий период';
                        } else {
                            $month_num = substr($cur_period_str, 4);
                            $month_lang = $months[(int)$month_num];
                            $period_prn = $month_lang . ' ' .substr($cur_period_str, 0, 4);
                        };?>
                        <td><?=$period_prn;?></td>
                        <td class="text-right"><?=$kol1;?></td>
                        <td class="text-right"><?=$kol2;?></td>
                        <td class="text-right"><?=$kol3;?></td>
                        <td class="text-right"><?=$kol4;?></td>
                    </tr>
            <?php $cur_period = $cur_period->modify("first day of last month");
             endfor;?>
        </table>
    </div>


    <div class="total-money">
        <?php $now = time();
        $past_month = date("m" ,strtotime("first day of last month")); // номер прошлого месяца

        // кол-во дней в прошлом месяце
        $days = 30;
        if (in_array($past_month, [1, 3, 5, 7, 10, 10, 12])) {
            $days = 31;
        } elseif($past_month == 2) {
            $days = date("L") == 1 ? 29 : 28;
        }

        $curr_day = date('j')-1;
        $curr_hour = date('G')+1;

        $past_time = $curr_day * 86400 + $curr_hour * 3600; // прошло времени с конца прошлого месяца

        $last_day_pay_month = $now - $past_time;// последний день прошлого месяца
        $first_day_past_month = $now - (($curr_hour - 1) * 3600) - ($days + $curr_day + 1) * 86400;// первый день прошлого месяца

        $last_month_pay = Aff::getLastMonthPay($userId, 'aff', $first_day_past_month, $last_day_pay_month); // заработано за последий месяц?>

        <h4><?=System::Lang('YOUR_ID');?> <?=$userId;?></h4>
        <h4><?=System::Lang('TOTAL_EARNED');?> <?php if($total['SUM(summ)'] > 0) echo $total['SUM(summ)']; else echo 0;?> <?=$setting['currency'];?></h4>
        <p title="с <?=date("d.m.Y H:i:s", $first_day_past_month);?> по <?=date("d.m.Y H:i:s", $last_day_pay_month);?>"><?=System::Lang('EANED_IN_LAST_YEAR');?> <?php if(!empty($last_month_pay['SUM(summ)'])) echo $last_month_pay['SUM(summ)']; else echo 0;?> <?=$setting['currency'];?></p>

        <p><?=System::Lang('TOTAL_PAID');?> <?php if($total['SUM(pay)'] > 0) echo $total['SUM(pay)']; else echo 0;?> <?=$setting['currency'];?></p>

        <?php if(!empty($last_pay)):?>
            <p><?=System::Lang('LAST_PAIMENT');?> <?=date("d.m.Y", $last_pay['date']);?> <?=System::Lang('ON');?> <?=$last_pay['pay']; ?> <?=$setting['currency'];?></p>
        <?php endif;?>

        <?php if(isset($total2)) {
            $all_pay = $total['SUM(summ)'];
        } else {
            $all_pay = $total['SUM(summ)'];
            $total2['SUM(summ)'] = $total['SUM(summ)'];
        }

        $rezerv = $all_pay - $total2['SUM(summ)'];
        $ostatok = $total['SUM(summ)'] > $total['SUM(pay)'] ? $total['SUM(summ)'] - $total['SUM(pay)'] : 0;?>

        <p style="margin:1em 0"><?=System::Lang('TOTAL_OWNED');?> <?="$ostatok {$setting['currency']}";?><br />
            <?php if(isset($params['params']['return_period']) && $params['params']['return_period'] > 0 && $ostatok > 0):?>
                <span style="border-bottom:1px dashed #999; cursor: help" title="с учётом резервирования выплат на <?=$params['params']['return_period'];?> дней"><?=System::Lang('AVAILABLE_PAIMENT_TODAY');?></span>
                <?=$ostatok - $rezerv;?> <?=$setting['currency'];?>
            <?php endif;?>
        </p>

        <h4><?=System::Lang('STATISTICS');?></h4>
        <p><?=System::Lang('TOTAL_CLIKS');?> <?=$hits; ?></p>
        <p><?=System::Lang('ORDERS_PAID');?> <?=$total_orders;?></p>
            <h4><?=System::Lang('EFFECTIVENES');?></h4>
        <p><?=System::Lang('CONVERTION');?> <?php if($hits > 0) {
            $conv = ($total_orders / $hits) *100; echo  round($conv, 2) . ' %';
        } else echo 'нет данных';?></p>
        <p><?=System::Lang('EACH_CLICK_BRINGS');?> <?php if($hits > 0) {
            $click = $total['SUM(summ)'] / $hits; echo round($click, 2). ' ' .$setting['currency'];
            } else {
                echo 'нет данных';
            }?>
        </p>
    </div>
</div>