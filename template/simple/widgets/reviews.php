<?php defined('BILLINGMASTER') or die;

if(empty($widget_params['params']['countreviews'])) $widget_params['params']['countreviews'] = 5;
$reviews_list = Product::getReviews(1, $widget_params['params']['category'], $widget_params['params']['label'], $widget_params['params']['countreviews']);
if($reviews_list){?>
<div class="reviews_list<?php if($widget_params['params']['orient'] == 'vertical') echo ' vertical';?>">
<?php foreach($reviews_list as $review):?>
    <div class="review_item">
        
        <div class="review_img">
            <?php if(!empty($review['attach'])):?>
            <img src="/images/reviews/<?php echo $review['attach'];?>" alt="<?php echo $review['name'];?>">
            <?php endif;?>
        </div>
        
        <div class="review_desc">
            <p><strong><?php echo $review['name'];?></strong></p>
            <p><?php echo $review['text'];?></p>
        </div>
    </div>
    
<?php endforeach;?>
</div>

<?php }?>
