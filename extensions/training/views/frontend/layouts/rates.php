<?php defined('BILLINGMASTER') or die;
$use_css = 1;
$title = 'Выберите вариант';
require_once (ROOT . '/extensions/training/layouts/frontend/head.php');
$metriks = null;
if(!empty($setting['yacounter'])) $ya_goal = "yaCounter".$setting['yacounter'].".reachGoal('ADD_TO_BUY');";
else $ya_goal = null;
if($setting['ga_target'] == 1) $ga_goal = "ga ('send', 'event', 'add_to_buy', 'click');";
else $ga_goal = null;
if(!empty($setting['yacounter']) || $setting['ga_target'] == 1) $metriks = ' onclick="'.$ya_goal.$ga_goal.' return true;"';?>


<body id="page">
    <?php require_once (ROOT . '/extensions/training/layouts/frontend/header.php');
    require_once (ROOT . '/extensions/training/layouts/frontend/main_menu.php');?>
    
    <?php if ($training['full_cover_param']):
        $full_cover_param = json_decode($training['full_cover_param'], true);
    endif;?>
    <style>
         .hero-wrap{
            height: <?php echo $full_cover_param['heroheigh'];?>px;
            background-position: <?php echo $full_cover_param['position']?>;
            background-size: cover;
        }
        .hero-wrap:before{
            opacity:<?php echo $full_cover_param['overlay']?>;
            background:<?php echo $full_cover_param['overlaycolor']?>;
        }

        @media screen and (max-width: 640px),
        only screen and (max-device-width:640px) {
            .hero-wrap {height: <?=$full_cover_param['heromobileheigh'];?>px}
        }

        .lesson_cover{width: <?=$this->tr_settings['width_less_img'];?>px}
        <?php if(isset($this->tr_settings['show_blocks']) && $this->tr_settings['show_blocks'] == 0):?>
        .module-number {display:none}
        <?php endif;?>
    </style>

    <div id="content">  
       <?php if ($training['full_cover']):?>
            <div id="hero" class="hero-wrap" style="background-image: url(/images/training/<?=$training['full_cover']?>)">
                <?php if(!empty($h1)) echo '<h1>'.$h1.'</h1>';?>
                <ul class="breadcrumbs">
                        <?php $breadcrumbs = Training::getBreadcrumbs($this->tr_settings, $category, $sub_category, $training, $section, $lesson);
                        foreach ($breadcrumbs as $link => $name):?>
                            <li><?=$link ? "<a href=\"$link\">$name</a>" : $name;?></li>
                        <?php endforeach;?>
                    </ul>
            </div>
            <div class="layout" id="courses">
    <?php else:?>
        <div class="layout" id="courses">
            <ul class="breadcrumbs">
                <?php $breadcrumbs = Training::getBreadcrumbs($this->tr_settings, $category, $sub_category, $training, $section, $lesson);
                foreach ($breadcrumbs as $link => $name):?>
                    <li><?=$link ? "<a href=\"$link\">$name</a>" : $name;?></li>
                <?php endforeach;?>
            </ul>
    <?php endif;?>
       
            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min';?> content-with-sidebar">
                    <div class="prod-course-top">
                    <?php if (empty($training['full_cover'])):?>
                      <?php if(!empty($h1)) echo '<h1>'.$h1.'</h1>';?>
                      <?php endif;?>
                        <h4><?=System::Lang('ACCESS_TARIFFS');?></h4>
                    </div>

                    <?php if($list_product):
                        foreach($list_product as $prod_id):
                        $product = Product::getProductById($prod_id);?>
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
                                                <div>
                                                    <a class="btn-green" href="<?=$setting['script_url'];?>/buy/<?=$product['product_id'];?>" target="_blank"<?=$metriks;?>><?=$product['button_text'];?></a>
                                                </div>
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
                <aside class="sidebar">
               

                    <!-- класс widget-sticky сделал на всякий случай, если надо, чтобы прогресс бар был плавающим. Если не нужен, можно убрать. -->
                    <?php if($user_id && $training['show_widget_progress']):?>
                    <section class="widget _instruction widget-sticky">
                        <?php if ($training['cover'] && !$training['full_cover']):?>
                        <div class="sidebar-image"><img src="/images/training/<?=$training['cover']?>"></div>
                         <!-- ЗДЕСЬ выводим название тренинга, если обложка маленькая -->
                        <h4 class="traninig-name"><?=$name?></h4>
                        <?php endif;?>

                        <h3><?=System::Lang('YOUR_PROGRESS');?></h3>
                        <p class="progress-text"><?=System::Lang('TRACK_YOUR_TRAINING');?></p>

                        <?php require_once (__DIR__ . '/../layouts/progressbar.php');?>
                    </section>
                    <?php else:?>
                        <section class="widget _instruction widget-sticky">
                        <?php if ($training['cover'] && !$training['full_cover']):?>
                        <div><div class="sidebar-image"><img src="/images/training/<?=$training['cover']?>"></div></div>
                        <!-- ЗДЕСЬ выводим название тренинга, если обложка маленькая -->
                            <h4 class="traninig-name"><?=$name?></h4>
                        <?php endif;?>
                        <h3><?=System::Lang('YOUR_PROGRESS');?></h3>
                        <p><?=System::Lang('PROGRESS_OF_THE_TRAINING_WILL_BE_DISPLAYED_HERE');?></p>
                        </section>
                    <?php endif;
                    if($sidebar):
                        $widget_arr = $sidebar;
                        require(ROOT . '/template/'.$this->setting['template'].'/widgets/widget_wrapper.php');
                    endif;?>
                </aside>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/extensions/training/layouts/frontend/footer.php');
    require_once (ROOT . '/extensions/training/layouts/frontend/tech-footer.php');?>
</body>
</html>
<?php exit;?>