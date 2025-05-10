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
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>

    <div id="content">
        <div class="layout" id="catalog">
            <ul class="breadcrumbs mb-0">
                <li><a href="/"><?=System::Lang('MAIN');?></a></li>
                <li><?=System::Lang('CATALOG');?></li>
            </ul>

            <?php if(!empty($h1)):?>
                <h1 class="rev-h1"><?=$h1;?></h1>
            <?php endif;?>

            <div class="content-wrap rev-content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                    <?php if(!empty($category_data['cat_desc'])):?>
                        <p><?=$category_data['cat_desc'];?></p>
                    <?php endif;

                    if($list_product):
                        foreach($list_product as $product):?>
                            <div class="catalog_item">
                            
                                <?php if ($product['product_cover']):?>
                                    <div class="catalog_item_img">
                                        <?php if($product['external_landing'] == 1 && !empty($product['external_url'])):?>
                                            <a href="<?=$product['external_url'];?>">
                                        <?php else:?>
                                            <a href="<?=$setting['script_url'];?>/catalog/<?=$product['product_alias'];?>">
                                        <?php endif;?>
                                            <img src="<?=$setting['script_url'];?>/images/product/<?=$product['product_cover'];?>" alt="<?=$product['img_alt'];?>">
                                        </a>
                                    </div>
                                <?php endif;?>

                                <div class="catalog_item__right">
                                    <div class="catalog_desc intro">
                                        <h4 class="catalog_item__title"><?=$product['product_name'];?></h4>

                                        <?php if($product['product_desc'] != null):?>
                                            <div class="product_desc"><?=nl2br($product['product_desc']);?></div>
                                        <?php endif;?>
                                    </div>

                                        <?php if($product['show_price_box'] == 1):?>
                                            <?php if($product['hidden_price'] == 0):?>          
                                                <div class="catalog-item__price-box">
                                                    <div>
                                                        <span class="font-bold"><?=System::Lang('COAST');?></span>
                                                        <?php $price = Price::getPriceinCatalog($product['product_id']);?>
                                                        <?php if($price['real_price'] < $price['price']):?>
                                                            <span class="old_price"><?=$price['price'];?> <?=$setting['currency'];?></span>&nbsp;
                                                            <span class="red_price"><?=$price['real_price'];?> <?=$setting['currency'];?></span>
                                                        <?php else:?>
                                                            <strong><?=$price['real_price'];?> <?=$setting['currency'];?></strong>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                            <?php endif;?>
                                            <div class="catalog-item__button-box">
                                                <?php if($setting['use_cart'] == 1):
                                                    if($product['hidden_price'] == 0):?>
                                                        <div>
                                                            <button data-id="<?=$product['product_id'];?>" class="btn-green add_to_cart"<?=$metriks;?>><?=System::Lang('IN_CART');?></button>
                                                        </div>
                                                    <?php endif;
                                                elseif($product['hidden_price'] == 0):?>
                                                    <?php if(!empty($product['button_text'])):?>
                                                    <div>
                                                        <a class="btn-green" href="<?=$setting['script_url'];?>/buy/<?=$product['product_id'];?>" target="_blank"<?=$metriks;?>><?=$product['button_text'];?></a>
                                                    </div>
                                                    <?php endif;?>
                                                <?php endif;?>
                                                <?php if($setting['enable_landing'] == 1):
                                                            if($product['external_landing'] == 1 && !empty($product['external_url'])):?>
                                                                <a href="<?=$product['external_url'];?>"><?=System::Lang('MORE');?></a>
                                                            <?php else:?>
                                                                <a href="<?=$setting['script_url'];?>/catalog/<?=$product['product_alias'];?>"><?=System::Lang('MORE');?></a>
                                                            <?php endif;
                                                        elseif($setting['enable_landing'] == 0 && $product['external_landing'] == 1 && !empty($product['external_url'])):?>
                                                            <a href="<?=$product['external_url'];?>"><?=System::Lang('MORE');?></a>
                                                <?php endif;?>
                                            </div>
                                       <?php else:?>
                                            <div class="catalog-item__price-box">
                                                <?php if($product['hidden_price'] == 0):?>
                                                    <div>
                                                        <span class="font-bold"><?=System::Lang('COAST');?></span>
                                                        <?php $price = Price::getPriceinCatalog($product['product_id']);
                                                        if($price['real_price'] < $price['price']):?>
                                                            <span class="old_price"><?=$price['price'];?> <?=$setting['currency'];?></span>&nbsp;
                                                            <span class="red_price"><?=$price['real_price'];?> <?=$setting['currency'];?></span>
                                                        <?php else:?>
                                                            <strong><?=$price['real_price'];?> <?=$setting['currency'];?></strong>
                                                        <?php endif;?>
                                                    </div>
                                                <?php endif;?>
                                                <div class="catalog-item__button-box">  
                                                    <?php if(!empty($product['button_text'])):?>
                                                    <div>
                                                        <a class="btn-green" href="<?=$setting['script_url'];?>/buy/<?=$product['product_id'];?>" target="_blank"<?=$metriks;?>><?=$product['button_text'];?></a>
                                                    </div>
                                                    <?php endif;?>
                                                    <?php if($setting['enable_landing'] == 1):
                                                            if($product['external_landing'] == 1 && !empty($product['external_url'])):?>
                                                                <a href="<?=$product['external_url'];?>"><?=System::Lang('MORE');?></a>
                                                            <?php else:?>
                                                                <a href="<?=$setting['script_url'];?>/catalog/<?=$product['product_alias'];?>"><?=System::Lang('MORE');?></a>
                                                            <?php endif;
                                                        elseif($setting['enable_landing'] == 0 && $product['external_landing'] == 1 && !empty($product['external_url'])):?>
                                                            <a href="<?=$product['external_url'];?>"><?=System::Lang('MORE');?></a>
                                                    <?php endif;?>
                                                </div>
                                            </div>
                                        <?php endif;?>
                                </div>
                            </div>
                        <?php endforeach;
                    endif;?>
                </div>
                <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>