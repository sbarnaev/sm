<?php if($sidebar):
$widget_arr = $sidebar; ?>
<aside class="sidebar">
<?php require(ROOT . '/template/'.$setting['template'].'/widgets/widget_wrapper.php');?>
</aside>
<?php endif;?>