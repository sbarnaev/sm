<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body class="invert-page" id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>
    
    
    <div id="content">
        <div class="layout" id="landing">
            <ul class="breadcrumbs mb-0">
                <li><a href="/"><?=System::Lang('MAIN');?></a></li>
                <li><?=System::Lang('REVIEWS');?></li>
            </ul>

            <?php if(!empty($h1)):?>
                <h1 class="rev-h1"><?=$h1;?></h1>
            <?php endif;?>

            <div class="content-wrap rev-content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                    <p class="flex-right"><a class="button btn-add-rev" href="/reviews/add"><?=System::Lang('WRITE_REVIEW');?></a></p>
                    
                    <div class="reviews_list">
                        <?php if($list_reviews):
                            foreach($list_reviews as $review):?>
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
                                            <?php if(!empty($review['product_id'])):
                                                $pr_arr = Product::getProductName($review['product_id']);
                                                if($pr_arr['product_name'] != '- - '):?>
                                                    <p><strong><?=$pr_arr['product_name'];?></strong></p>
                                                <?php endif;
                                            endif;?>
                                            
                                            <p><strong><?=$review['name'];?></strong>
                                                <?php $reviews_tune = unserialize(base64_decode($setting['reviews_tune']));
                                                if(!isset($reviews_tune['show_date']) || $reviews_tune['show_date']):?>
                                                    , <span class="small"><?=$review['create_date']?></span>
                                                <?php endif;?>
                                            </p>
                                            <div><?=$review['text'];?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach;
                        else:?>
                            <p><?=System::Lang('NO_REVIEW');?></p>
                        <?php endif;?>
                    </div>
                
                    <?php if(isset($is_pagination) && $is_pagination == true) echo $pagination->get();?>
                </div>
                <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>