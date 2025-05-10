<?php defined('BILLINGMASTER') or die;

$elements = TrainingLesson::getElements2Lesson($lesson['lesson_id'], null, TrainingLesson::ELEMENT_TYPE_PLAYLIST);
if ($elements):
    $prev_el = null;
    foreach ($elements as $key => $element):
        if($element['type'] == TrainingLesson::ELEMENT_TYPE_PLAYLIST):
            require(__DIR__ . '/playlist.php');
        elseif($element['type'] == TrainingLesson::ELEMENT_TYPE_ATTACH):
            if (!$prev_el || $prev_el != TrainingLesson::ELEMENT_TYPE_ATTACH):?>
                <div class="block-border-top">
                    <div class="media_item_wrap">
                <div class="group-files">
            <?php endif;

            $link = $element['params']['type'] == 1 ? "/training/lesson/attach/{$element['id']}" : $element['params']['link'];?>
            <a href="<?=$link;?>" class="one-files<?php if(!$element['params']['line_up']) echo ' one-files__width-100';?>" <?=$element['params']['type'] == 1 ? 'download' : 'target="_blank"';?>>
                <div class="one-files-title"><?=$element['params']['title'];?></div>
                <div class="one-files-image"><img width="28" src="<?=$element['params']['cover'];?>" alt=""></div>
            </a>
        <?php elseif($element['type'] == TrainingLesson::ELEMENT_TYPE_HTML):
            echo $element['params']['html'];
        elseif($element['type'] == TrainingLesson::ELEMENT_TYPE_TEXT):
            if($element['params']['type'] == TrainingLesson::ELEMENT_TEXT_TYPE_TEXT):
                echo $element['params']['text'];
            elseif($element['params']['type'] == TrainingLesson::ELEMENT_TEXT_TYPE_ACCORDEON):?>
                <div class="block-border-top">
                    <div class="media_item_wrap cut un-login-cut">
                        <div class="block-heading__click">
                            <h4 id="el_text_<?=$element['id'];?>" class="block-heading"><?=$element['params']['title'];?></h4>
                        </div>

                        <div class="mini_cut hidden">
                            <?=$element['params']['text'];?>
                        </div>
                    </div>
                </div>
            <?php elseif($element['params']['type'] == TrainingLesson::ELEMENT_TEXT_TYPE_POPAP):?>
                <p><a href="#modal_el_text_<?=$element['id'];?>" data-uk-modal="{center:true}"><?=$element['params']['title'];?></a></p>
                <div id="modal_el_text_<?=$element['id'];?>" class="uk-modal">
                    <div class="uk-modal-dialog">
                        <div class="userbox modal-userbox-3">
                            <a href="#close" title="Закрыть" class="uk-modal-close uk-close modal-close">
                                <span class="icon-close"></span>
                            </a>
                            <div class="box">
                                <?=$element['params']['text'];?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif;?>
        <?php elseif($element['type'] == TrainingLesson::ELEMENT_TYPE_MEDIA):?>
            <div class="block-border-top media_item_wrap">
                <?php if(isset($element['params']['title']) && $element['params']['title']):?>
                    <h3><?=$element['params']['title'];?></h3>
                <?php endif;
                require(__DIR__ . '/media_item.php');?>
            </div>
        <?php endif;

        if($element['type'] == TrainingLesson::ELEMENT_TYPE_ATTACH && ($key == count($elements)-1 || ($key < count($elements)-1 && $elements[$key+1]['type'] != TrainingLesson::ELEMENT_TYPE_ATTACH))):?>
            </div>
            </div>
                </div>
        <?php endif;
        $prev_el = $element['type'];
    endforeach;
endif;?>
