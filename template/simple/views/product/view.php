<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body class="invert-page" id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>
    
    
    <div id="content">
        <div class="layout" id="landing">
            <ul class="breadcrumbs">
                <li><a href="/"><?=System::Lang('MAIN');?></a></li>
                <li><a href="/catalog"><?=System::Lang('CATALOG');?></a></li>
                <li><?=$product['product_name'];?></li>
            </ul>
            
            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                    <div class="maincol-inner">
                        <?php if($product["$text_heading"] == 1):?>
                            <h1><?=$product['product_name'];?></h1>
                        <?php endif;?>
                        
                        <?=System::renderContent($text_lp);?>
                        <?=$product['custom_code'];?>
                        
                        <?php if($product['show_price_box'] == 1) {
                            require_once(ROOT . '/template/'.$setting['template'].'/layouts/price_box.php');
                        }
                        
                        if(!empty($product['code_price_box'])) {
                            echo $product['code_price_box'];
                        }?>
                    </div>
                
                    <?php if($product['show_reviews'] == 1):
                        $reviews = Product::getReviewsByProductID($product['product_id']);
                        if($reviews):?>
                            <div class="reviews_list">
                                <h3><?=System::Lang('REVIEWS');?></h3>
                                <?php foreach($reviews as $review):?>
                                    <div class="review_item">
                                        <div class="review_item-inner">
                                            <div class="review_img">
                                                <?php if(!empty($review['attach'])):?>
                                                    <img src="/images/reviews/<?=$review['attach'];?>" alt="<?=$review['name'];?>">
                                                <?php endif;?>
                                                
                                                <ul class="rev-soc">
                                                    <?php if(!empty($review['site_url'])):?>
                                                        <li><a href="<?=$review['site_url'];?>" target="_blank" rel="nofollow"><i class="icon-site"></i></a></li>
                                                    <?php endif;?>
                
                                                    <?php if(!empty($review['vk_url'])):?>
                                                        <li><a href="<?=$review['vk_url'];?>" target="_blank" rel="nofollow"><i class="icon-vk-i"></i></a></li>
                                                    <?php endif;?>
                
                                                    <?php if(!empty($review['fb_url'])):?>
                                                        <li><a href="<?=$review['fb_url'];?>" target="_blank" rel="nofollow"><i class="icon-facebook"></i></a></li>
                                                    <?php endif;?>
                                                </ul>
                                            </div>
        
                                            <div class="review_desc">
                                                <p><strong><?=$review['name'];?></strong></p>
                                                <?=$review['text'];
                                                
                                                $reviews_tune = unserialize(base64_decode($setting['reviews_tune']));
                                                if(!isset($reviews_tune['show_date']) || $reviews_tune['show_date']):?>
                                                    <p><?=$review['create_date'];?></p>
                                                <?php endif;?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach;?>
                            </div>
                        <?php endif;
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