<?php defined('BILLINGMASTER') or die;

// Список изображений категории
define('BM_GALLERY', $widget_params['params']['style']);
if ($widget_params['params']['source'] == 'category') {
    $img_list = Gallery::getImagesByCat($widget_params['params']['category']);
}

if ($widget_params['params']['source'] == 'folder') {
    $img_list = scandir($widget_params['params']['folder']);
};?>

<div id="gallery">
    <?php if ($widget_params['params']['source'] == 'category'):?>
        <?php if (isset($img_list) && $img_list):
            foreach($img_list as $img):
                if (!empty($img['link'])):?>
                    <a href="<?=$img['link'];?>">
                <?php endif;?>
                <img alt="<?=$img['alt'];?>" src="/images/gallery/thumb/<?=$img['file'];?>" style="display:none"
                     data-image="/images/gallery/<?=$img['file'];?>" data-description="<?=$img['item_desc'];?>"
                >
                <?php if (!empty($img['link'])) echo '</a>';
            endforeach;
        endif;?>
    <?php elseif (isset($img_list) && $img_list):
        foreach($img_list as $img):
            if ($img != '.' && $img != '..' && is_file($widget_params['params']['folder'].'/'. $img)):
                $file = pathinfo($widget_params['params']['folder'].'/'. $img);
                if ($file['extension'] == 'jpeg' || $file['extension'] == 'jpg' || $file['extension'] == 'png' || $file['extension'] == 'gif'):?>
                    <img alt="" src="/<?=$widget_params['params']['folder'] . '/'.$img;?>"
                         data-image="/<?=$widget_params['params']['folder'] . '/'.$img;?>"
                         data-description="" style="display:none"
                    >
                <?php endif;
            endif;
        endforeach;
    endif;?>
</div>