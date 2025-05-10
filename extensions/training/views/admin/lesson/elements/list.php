<?php defined('BILLINGMASTER') or die;
$playlist_exists = TrainingLesson::getCountElements2Lesson($lesson_id, TrainingLesson::ELEMENT_TYPE_PLAYLIST) ? true : false;?>

<div class="row-line">
    <div class="col-1-1 mb-0">
        <h4>Конструктор урока</h4>
    </div>

    <div class="col-1-1">
        <div class="add-elements-wrap">
            <div class="add-elements add-elements__with-open">
                <?php if(System::CheckExtensension('polls', 1) || System::CheckExtensension('forum2', 1)):?>
                    <div class="open_add-elements"><i class="icon-down"></i></div>
                <?php endif;?>

                <a href="#modal_add_media" class="add-element media" data-uk-modal="{center:true}"><i class="el-icon"></i>Видео/аудио</a>
                <div class="add-element playlist<?php if($playlist_exists) echo ' disabled';?>"><a href="#modal_playlist" data-uk-modal="{center:true}" class="add-element-inner"><i class="el-icon"></i>Плейлист</a></div>
                <a href="#modal_add_text" class="add-element text" data-uk-modal="{center:true}"><i class="el-icon"></i>Текст</a>
                <a href="#modal_add_attach" class="add-element attach" data-uk-modal="{center:true}"><i class="el-icon"></i>Файлы</a>
                <a href="#modal_add_html" class="add-element html" data-uk-modal="{center:true}"><i class="el-icon"></i>HTML-код</a>
                <?php if(System::CheckExtensension('polls', 1)):?>
                    <a href="#modal_add_poll" class="add-element poll" data-uk-modal="{center:true}"><i class="el-icon"></i>Опрос</a>
                <?php endif;
                if(System::CheckExtensension('forum2', 1)):
                    $forum_exists = TrainingLesson::getCountElements2Lesson($lesson_id, TrainingLesson::ELEMENT_TYPE_FORUM) ? true : false;?>
                    <a href="#modal_add_forum<?php if($forum_exists) echo ' disabled';?>" class="add-element forum" data-uk-modal="{center:true}"><i class="el-icon"></i>Форум</a>
                <?php endif;?>
            </div>

            <div class="el-edit-list">
                <?php if($elements):?>
                    <div class="sortable sortable_box">
                        <input type="hidden" name="sort_upd_url" value="/admin/trainingajax/updsortelements">
                        <?php foreach($elements as $element):?>
                            <a href="javascript:void(0)" class="el-edit <?=TrainingLesson::getElementName($element['type']);?>" data-url="/admin/trainingajax/elementform?id=<?="{$element['id']}&token={$_SESSION['admin_token']}";?>">
                                <input type="hidden" name="sort_items[]" data-type="el" value="<?=$element['id'];?>">
                                <i class="button-drag el-icon"></i>
                                <span class="el-title"><?=$element['params']['name'];?></span>
                                <span class="ajax icon-remove" data-id="<?=$element['id'];?>" data-url="/admin/trainingajax/dellessonelement"></span>
                            </a>
                        <?php endforeach;?>
                    </div>
                <?php else:?>
                    <div class="not-elements">Добавьте первый элемент</div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
