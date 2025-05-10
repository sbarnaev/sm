<?php defined('BILLINGMASTER') or die;
$params = $widget_params;
if(empty($params['params']['countpost'])) $params['params']['countpost'] = 5;
if(!empty($params['params']['from_id'])){
    $post_list = Blog::getPostListByID($params['params']['countpost'], $params['params']['from_id']);
} else $post_list = Blog::getPublicPostList($params['params']['countpost']);
if($post_list){?>
<ul class="last_posts">
<?php foreach($post_list as $post):
    $rubric = Blog::getRubricDataByID($post['rubric_id'])?>
    <li><a href="<?php echo $setting['script_url'];?>/blog/<?php echo $rubric['alias'];?>/<?php echo $post['alias'];?>"><?php echo $post['name'];?></a></li>
<?php endforeach;?>
</ul>
<?php }?>
