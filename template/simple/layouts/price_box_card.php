<?php defined('BILLINGMASTER') or die; 

$metriks = null;
if(!empty($setting['yacounter'])) $ya_goal = "yaCounter".$setting['yacounter'].".reachGoal('ADD_TO_BUY');";
else $ya_goal = null;
if($setting['ga_target'] == 1) $ga_goal = "ga ('send', 'event', 'add_to_buy', 'click');";
else $ga_goal = null;
if(!empty($setting['yacounter']) || $setting['ga_target'] == 1) $metriks = ' onclick="'.$ya_goal.$ga_goal.' return true;"';

$complect_params = unserialize(base64_decode($product['complect_params']));
$complect_params = explode("|", $complect_params);
$style = ' '.$complect_params[2];

?>
<div class="product_price_box">
    <div class="product_box">
        <p class="product_box-title"><?=System::Lang('COAST');?></p>
        <!--h3><?php //echo $complect_params[0];?></h3-->
        <ul><?php echo $complect_params[1];?></ul>
        <?php $standart_price = Price::getFinalPrice($product['product_id']);?>
        
        <p class="price_str"><?php if($standart_price['real_price'] < $standart_price['price']){?>
        <span class="old_price"><?php echo $standart_price['price']?></span>&nbsp;<span class="red_price"><?php echo $standart_price['real_price'];?> <?php echo $setting['currency'];?></span>
        <?php } else {?>
        <?php echo $standart_price['real_price'];?> <?php echo $setting['currency'];?>
        <?php } ?>
        </p>
        
        <?php if($product['product_amt'] != 0):
        if($setting['use_cart'] == 1){?>
        <p><button data-id="<?php echo $product['product_id'];?>" class="add_to_cart order_link"<?php echo $metriks;?>><?=System::Lang('IN_CART');?></button></p>
        <?php } else {?>
        <p><a class="order_link" href="<?php echo $setting['script_url'];?>/buy/<?php echo $product['product_id']; ?>"<?php echo $metriks;?>><?php echo $product['button_text'];?></a></p>
        <?php } endif;?>
        <?php if($product['show_amt'] == 1):?>
            <?php echo prodCount($product['product_amt']);?>
        <?php endif; ?>
        
    </div>
        
<?php function prodCount($count){
    if($count == 0) $result = 'Товар закончился';
    elseif($count == -1) $result = '';
    else $result = "Всего осталось: $count";
    return $result;
}
?>
</div>