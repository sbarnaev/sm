<?php // Вывод промо кода
$promo_data = System::getPromoByEmail($user['email'], time());
if($promo_data):
    $expire = date("d.m.Y H:i", $promo_data['finish']);?>
    <div class="coupon">
        <div class="coupon-left">
            <img src="/template/<?=$setting['template'];?>/images/coupon-img.svg" alt="">
        </div>
        <div class="coupon-text">
            <p><strong><?=System::Lang('PRESENT');?></strong></p>
            <p><?=System::Lang('YOUR_PROMOCODE');?>&nbsp;&nbsp;
                <b><?=$promo_data['promo_code'];?></b><br/>
                <?=System::Lang('VALID_UNTIL');?> <b><?=$expire;?></b><br/>
                <?=html_entity_decode($promo_data['sale_desc']);?>
            </p>
        </div>
    </div>
<?php endif;?>