<?php defined('BILLINGMASTER') or die;
$setting = System::getSetting();?>

<a href="#close" title="Закрыть" class="uk-modal-close uk-close modal-close">
    <span class="icon-close"></span>
</a>

<div class="box1">
    <h3 class="modal-head-2"><?=System::Lang('COURSE_NOT_ACCESSED');?> <?=$lesson_id ? 'уроку': 'разделу';?></h3>
    <p class="text-center"><?=System::Lang('ACCESSED_COURSE');?><?php if(!$user_id ):?> <?=System::Lang('SITE_AUTHORIZE');?><?php endif;?>.</p>

    <div class="group-button-modal align-center">
        <?php if(!$user_id):?>
            <a class="btn-yellow" href="#modal-login" data-uk-modal="{center:true}"><?=System::Lang('LOGIN');?></a>
            <?php if($buttons['big_button']):?>
                <div class="z-1">
                    <?php if(isset($buttons['big_button']['over_button_text'])):?>
                        <p class="small"><?=$buttons['big_button']['over_button_text'];?></p>
                    <?php endif;?>
                    <?php if(mb_stripos($buttons['big_button']['url'], "?viewmodal")):?>
                        <a data-uk-lightbox="" data-lightbox-type="iframe" class="btn-blue" href="<?=$buttons['big_button']['url'];?>">
                            <?=$buttons['big_button']['text'];?>
                        </a>
                    <?php else:?>
                        <a class="btn-blue" href="<?=$buttons['big_button']['url'];?>">
                            <?=$buttons['big_button']['text'];?>
                        </a>
                    <?php endif;?>
                </div>
            <?php endif;?>
        <?php elseif ($buttons['big_button']):?>
            <div class="z-1">
                <?php if(isset($buttons['big_button']['over_button_text'])):?>
                    <p class="small"><?=$buttons['big_button']['over_button_text'];?></p>
                <?php endif;?>
                <?php if(mb_stripos($buttons['big_button']['url'], "?viewmodal")):?>
                    <a data-uk-lightbox="" data-lightbox-type="iframe" class="<?=Training::getCssClasses($setting, $buttons['big_button']['class-type']);?>" href="<?=$buttons['big_button']['url'];?>">
                        <?=$buttons['big_button']['text'];?>
                    </a>
                <?php else:?>
                    <a class="<?=Training::getCssClasses($setting, $buttons['big_button']['class-type']);?>" href="<?=$buttons['big_button']['url'];?>">
                        <?=$buttons['big_button']['text'];?>
                    </a>
                <?php endif;?>
            </div>
        <?php endif;?>
    </div>
</div>
