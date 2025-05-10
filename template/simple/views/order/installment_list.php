<?php defined('BILLINGMASTER') or die;

$s = 0;
foreach($list as $install_item):
    if ($total < $install_item['minimal']) {
        continue;
    }

    $pays = Installment::getPays($install_item, $total);
    $p = 2;
    $m = 1;
    $increase_pay = $install_item['increase'] > 0 ? round($install_item['increase'] / $install_item['max_periods']) : 0;?>

    <div class="install_item install_item-mt-50">
        <label class="install_item-radio">
            <input class="install_item-radio-check" type="radio" <?php if($s++ == 0) echo 'checked=""';?> name="installment_id" id="instal_<?=$install_item['id'];?>" value="<?=$install_item['id'];?>">
            <span class="install_item-title"><?=$install_item['title'];?></span>

            <div class="install_item-inner">
                <table class="install_item-table">
                    <tr>
                        <th><?=System::Lang('PAYMENT_NUMBER');?></th>
                        <th><?=System::Lang('PAYMENT_DATE');?></th>
                        <th class="install_item-table__last"><?=System::Lang('SUMM');?></th>
                    </tr>

                    <tr>
                        <td><?=System::Lang('FIRST_PAYMENT');?></td>
                        <td><?=System::Lang('TODAY');?></td>
                        <td><?=$pays['first_pay'];?> <?=$setting['currency'];?></td>
                    </tr>

                    <?php while($install_item['max_periods'] >= $p):
                        $pay_date = Installment::getNextPayDate($install_item, $now, $install_item['date_second_payment'], $m++);?>
                        <tr>
                            <td><?=$p++?> <?=System::Lang('PAYMENT');?></td>
                            <td><?=date("d.m.Y", $pay_date);?></td>
                            <td class="install_item-table__last"><?=$pays['other_pay'];?> <?=$setting['currency'];?></td>
                        </tr>
                    <?php endwhile;?>
                </table>

                <?php if($install_item['increase'] > 0):?>
                    <p class="install_item__last-block"><?=System::Lang('INSTALLMENT_COAST');?> <?="{$install_item['increase']} {$setting['currency']}";?></p>
                <?php endif;?>

                <p class="install_item__last-block"><strong><?=System::Lang('INSTALLMENT_COAST_SUMM');?> <?=($total + $install_item['increase']) . " {$setting['currency']}";?></strong></p>
            </div>
        </label>
    </div>
<?php endforeach;?>

<div class="payment-deskr">
    <!--p>При оплате в рассрочку доступ к курсу предоставляется сразу, но уроки открываются постепенно, по мере оплаты. Счета на оставшиеся платежи будут в вашем личном кабинете.
        <br>Оплатить счета можно в любое время, но не позднее даты платежа.</p-->
</div>

<input type="hidden" name="order_date" value="<?=$order_date;?>">
<div class="payment-submir-wrap">
    <button class="btn-green-small" type="submit" name="installment"><?=System::Lang('INSTALLMENT_PLAN');?></button>
</div>

