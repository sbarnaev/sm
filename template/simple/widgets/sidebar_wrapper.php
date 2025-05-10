<?php defined('BILLINGMASTER') or die;
// ЭТОТ ФАЙЛ не используется!!!!

$suffix = $widget['suffix'];
if(isset($sidebar) && $sidebar == true):
    foreach($sidebar as $widget):
        $params = unserialize($widget['params']);
        if(!$widget['private'] || $is_auth):?>
            <section class="widget<?=$widget['private'] ? $suffix : '';?>">
                <h3><?=$widget['widget_title'];?></h3>
                <?php require ($widget['widget_type'].'.php');?>
            </section>
        <?php endif;
    endforeach;
endif;?>
