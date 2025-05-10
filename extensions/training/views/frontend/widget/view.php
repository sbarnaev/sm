<?php defined('BILLINGMASTER') or die;?>

<div class="layout" id="widget-training">
    <div class="content-courses">
        <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
            <?php // вывод категорий
            if ($cat_list) {
                require_once (ROOT . "/extensions/training/views/frontend/category/templates/list/{$widget_params['params']['template']}.php");
            } else {
                require_once (ROOT . "/extensions/training/views/frontend/training/templates/list/{$widget_params['params']['template']}.php");
            }?>
        </div>
        <?php require_once (ROOT . "/template/{$setting['template']}/layouts/sidebar.php");?>
    </div>
</div>