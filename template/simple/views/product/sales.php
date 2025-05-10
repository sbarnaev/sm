<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
$metriks = null;
if(!empty($setting['yacounter'])) $ya_goal = "yaCounter".$setting['yacounter'].".reachGoal('ADD_TO_BUY');";
else $ya_goal = null;
if($setting['ga_target'] == 1) $ga_goal = "ga ('send', 'event', 'add_to_buy', 'click');";
else $ga_goal = null;
if(!empty($setting['yacounter']) || $setting['ga_target'] == 1) $metriks = ' onclick="'.$ya_goal.$ga_goal.' return true;"';

?>
<body class="invert-page" id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>
    <style>
        .hero-wrap{
            min-height: 300px;
            height: <?php echo $param['params']['heroheigh'];?>px;
            background-position: <?php echo $param['params']['position']?>;
            background-size: cover;
        }
        .hero-wrap:before{
            opacity:<?php echo $param['params']['overlay']?>;
            background:<?php echo $param['params']['overlaycolor']?>;
        }
        
        .hero_header.h1 {color: <?php echo $param['params']['color']?>; font-size: <?php echo $param['params']['fontsize']?>px; }
        
        @media screen and (max-width: 640px),
        only screen and (max-device-width:640px) {
            .hero-wrap {height: <?php echo $param['params']['heromobileheigh'];?>px}
            .hero_header.h1 {font-size: <?php echo $param['params']['fontsize_mobile'];?>px}
        }
        
    </style>
    
    <?php if(isset($param['params']['hero']) && $param['params']['hero'] != null ):?>
    <div id="hero" class="hero-wrap old-hero-wrap" style="background-image: url('<?php echo $param['params']['hero'];?>')">
        <h1 class="layout hero_header h1"><?php echo $h1;?></h1>
    </div>
    <?php endif;?>

    
    <div id="content">
        <div class="layout" id="catalog">
            <ul class="breadcrumbs mb-30">
                <li><a href="/"><?=System::Lang('MAIN');?></a></li>
                <li><?=System::Lang('DISCOUNTS');?></li>
            </ul>
            <div class="content-wrap rev-content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">

                    <div class="maincol-inner mb-30">
                    <?php echo $page['page_text'];?>

                    <?php if(!empty($page['page_code'])):?>
                    <div class="sale_custom_code"><?php echo $page['page_code'];?></div>
                    <?php endif; ?>
                    
                    <?php if($list_product){
                foreach($list_product as $product):?>
                    <div class="catalog_item">
                        <div class="catalog_item_img">
                            <a href="<?php echo $setting['script_url'];?>/catalog/<?php echo $product['product_alias'];?>" target="_blank"<?php echo $metriks;?>><img src="<?php echo $setting['script_url'];?>/images/product/<?php echo $product['product_cover'];?>" alt="<?php echo $product['img_alt'];?>"></a>
                        </div>
                        <div class="catalog_item__right">
                            <div class="catalog_desc intro">
                                <h4 class="catalog_item__title"><?php echo $product['product_name'];?></h4>
                                <div class="product_desc"><?php echo $product['product_desc'];?></div>
                            </div>
                            <div class="catalog-item__price-box">
                                <div>
                                    <?php if($product['show_price_box'] == 1){?>
                                    <span class="font-bold"><?=System::Lang('COAST');?></span>
                                    <?php $price = Price::getPriceinCatalog($product['product_id']);
                        if($price['real_price'] < $price['price']):?>
                                        <span class="old_price"><?php echo $price['price'];?> <?php echo $setting['currency'];?></span>
                                        <strong><?php endif;
                        echo $price['real_price']; ?> <?php echo $setting['currency'];?></strong>

                                    <?php if($setting['use_cart'] == 1){?>
                                    <p><button data-id="<?php echo $product['product_id'];?>" class="add_to_cart btn-green"<?php echo $metriks;?>><?=System::Lang('IN_CART');?></button></p>
                                    <?php } else {?>
                                    <p><a class="btn-green" href="<?php echo $setting['script_url'];?>/buy/<?php echo $product['product_id'];?>" target="_blank"<?php echo $metriks;?>><?php echo $product['button_text'];?></a></p>
                                    <?php } ?>

                                    <?php } else { ?>

                                    <p><strong><?php $price = Price::getPriceinCatalog($product['product_id']);
                        if($price['real_price'] < $price['price']):?>
                                        <span class="old_price"><?php echo $price['price'];?></span>
                                        <?php endif;
                        echo $price['real_price']; ?></strong> <?php echo $setting['currency'];?></p>
                                    <p><a href="<?php echo $setting['script_url'];?>/catalog/<?php echo $product['product_alias'];?>" target="_blank"<?php echo $metriks;?>><?php echo $product['button_text'];?></a></p>

                                    <?php } ?>
                                </div>
                            </div>
                            <div class="catalog-item__button-box">
                                <!-- <div>
                                    Сюда нужно переместить кнопку заказать или в корзину
                                </div> -->
                                <?php if($setting['enable_landing'] == 1):?>
                                <a href="<?php echo $setting['script_url'];?>/catalog/<?php echo $product['product_alias'];?>" target="_blank"><?=System::Lang('MORE');?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                        </div>

                    <?php endforeach;
                } else { ?>
                    <p><?=System::Lang('DISCOUNTS_OVER');?></p>
                    <?php } ?>

                </div>
                <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
                </div>
            </div>

        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>