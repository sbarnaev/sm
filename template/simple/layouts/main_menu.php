<?php defined('BILLINGMASTER') or die; ?>

<?php $full_slide = $widget_arr = Widgets::RenderWidget($all_widgets, 'full_slide');
if($full_slide):?>
    <div class="full_layout">
        <?php require(ROOT . '/template/'.$setting['template'].'/widgets/user_menu_wrapper.php');?>
    </div>
<?php endif;?>

<?php $top = Widgets::RenderWidget($all_widgets, 'top');
$widget_arr = $top;
if($top):?>
    <div class="layout">
        <?php require(ROOT . '/template/'.$setting['template'].'/widgets/user_menu_wrapper.php');?>
    </div>
<?php endif;?>

<?php $slider = Widgets::RenderWidget($all_widgets, 'slider');
$widget_arr = $slider;
if($slider):?>
    <div class="user-menu">
        <div class="layout_small">
            <?php require(ROOT . '/template/'.$setting['template'].'/widgets/user_menu_wrapper.php');?>
        </div>
    </div>
<?php endif;?>