<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php'); 
$metriks = null;
if(!empty($setting['yacounter'])) $ya_goal = "yaCounter".$setting['yacounter'].".reachGoal('ADD_UPSELL');";
else $ya_goal = null;
if($setting['ga_target'] == 1) $ga_goal = "ga ('send', 'event', 'add_upsell', 'submit');";
else $ga_goal = null;
if(!empty($setting['yacounter']) || $setting['ga_target'] == 1) $metriks = ' onsubmit="'.$ya_goal.$ga_goal.' return true;"';?>
<body class="cart-page" id="page">
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    ?>
    <div id="order_form">
        <div class="container-cart">
          <ul class="container-crumbs">
            <li class="crumbs-no-active"><?=System::Lang('FIRST_YOUR_DATAS');?></li>
            <li class="two-active"><?=System::Lang('SECOND_CART');?></li>
            <li><?=System::Lang('THIRD_PAYMENT_VARIANT');?></li>
          </ul>
          <h2><?=System::Lang('SPECIAL_OFFER');?></h2>
           <?php echo $intro;?>

          <div class="offer main">
            <div class="order_item">
              <div class="order_item-left">
                <img class="upsell_cover" src="<?php echo $setting['script_url'];?>/images/product/<?php echo $upsell['product_cover'];?>" alt="<?php echo $upsell['product_name'];?>">
              </div>
              <div class="order_item-desc">
                <h4><?php echo $upsell['product_name'];?></h4>
              </div>
              <div class="order_item-price_box-right">
                <div><?php if($old_price) echo '<span class="old_price">'.$old_price.'</span>' . $setting['currency'].'<br> '; echo '<span class="real_price">'.$price .'</span>' . $setting['currency'];?></div>
              </div>
            </div>
            <div class="upsell_box-bottom">
              <form action="" id="top_yes" method="POST"<?php echo $metriks;?>>
              <input type="submit" name="upsell" value="Добавить к заказу" class="upsell_button btn-green">
              <input type="hidden" name="result" value="1">
              <input type="hidden" name="step" value="<?php echo $step;?>">
              </form>
              <form action="" id="top_no" method="POST">
                <input type="submit" name="upsell" value="Спасибо, не надо" class="upsell_cancel link-red">
                <input type="hidden" name="result" value="0">
                <input type="hidden" name="step" value="<?php echo $step;?>">
              </form>
            </div>
          </div>

           <?php if(!empty($text)): 
           echo $text;?>

          <div class="offer main">
            <div class="order_item">
              <div class="order_item-left">
                <img class="upsell_cover" src="<?php echo $setting['script_url'];?>/images/product/<?php echo $upsell['product_cover'];?>" alt="<?php echo $upsell['product_name'];?>">
              </div>
              <div class="order_item-desc">
                <h4><?php echo $upsell['product_name'];?></h4>
              </div>
              <div class="order_item-price_box-right">
                <div><?php if($old_price) echo '<span class="old_price">'.$old_price.'</span>' . $setting['currency'].'<br> '; echo '<span class="real_price">'.$price .'</span>' . $setting['currency'];?></div>
              </div>
            </div>
            <div class="upsell_box-bottom">
              <form action="" id="bottom_yes" method="POST"<?php echo $metriks;?>>
              <input type="submit" name="upsell" value="Добавить к заказу" class="upsell_button btn-green">
              <input type="hidden" name="result" value="1">
              <input type="hidden" name="step" value="<?php echo $step;?>">
              </form>
              <form action="" id="bottom_no" method="POST">
                <input type="submit" name="upsell" value="Спасибо, не надо" class="upsell_cancel link-red">
                <input type="hidden" name="result" value="0">
                <input type="hidden" name="step" value="<?php echo $step;?>">
              </form>
            </div>
          </div>
           
           <?php endif; ?>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>