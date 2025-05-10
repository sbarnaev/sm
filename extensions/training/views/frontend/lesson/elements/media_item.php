<?php defined('BILLINGMASTER') or die;
if($element['params']['element_type'] == 1): //infoprotector?>
    <a href="<?=$element['params']['url'];?>" target="_blank">
        <img src="<?=$element['params']['cover'];?>" alt="">
    </a>
<?php elseif($element['params']['element_type'] == 2 ): //video?>
    <div id="player_<?=$element['id'];?>"></div>

<?php elseif($element['params']['element_type'] == 3): // audio ?>
    <div id="a_player_<?=$element['id'];?>"></div>

<?php elseif($element['params']['element_type'] == 4): //youtube or vimeo
    if(strpos($element['params']['url'], 'vimeo.com') !== false): //vimeo
        if (strpos($element['params']['url'], 'https://vimeo.com') === 0) {
            $element['params']['url'] = 'https://player.vimeo.com/video'.str_replace('https://vimeo.com', '', $element['params']['url']);
        }?>
        <a href="javascript:void(0)">
            <div class="video-responsive">
                <iframe src="<?=$element['params']['url'];?>" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
            </div>
        </a>
    <?php else: //youtube?>
        <a href="javascript:void(0)">
            <div class="video-responsive">
                <iframe src="<?=System::getYoutubeUrl2Iframe($element['params']['url']);?>" frameborder="0" modestbranding="1" showinfo="0" rel="0" enablejsapi="1" allowfullscreen></iframe>
            </div>
        </a>
    <?php endif;?>
<?php elseif($element['params']['element_type'] == 5): //изображение?>
    <a href="<?=$element['params']['cover'];?>" data-uk-lightbox="{group:'group2'}" data-lightbox-width="900">
        <img src="<?=$element['params']['cover'];?>" alt="">
    </a>
<?php endif;?>