<?php defined('BILLINGMASTER') or die;?>

<div class="structure_item_wrap">
    <input type="hidden" name="sort_items[]" value="<?=$block['block_id'];?>" data-type="block">

    <div class="structure_item block">
        <div class="button-drag"></div>

        <div class="structure_item_content">
            <div class="structure_link">
                <a href="/admin/training/editblock/<?="$training_id/{$block['block_id']}";?>"><?=$block['name'];?></a>
            </div>

            <div class="structure_desc">
                <div class="structure_desc-line">
                    <div class="structure_desc_data"><i class="icon-access-group" title="Сортировка"></i><span><?=$block['sort'];?></span></div>
                </div>
            </div>
        </div>

        <div class="structure_item_right">
            <form action="" method="POST">
                <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                <a class="link-delete__structure-item" onclick="return confirm('Вы уверены?')" href="/admin/training/delblock/<?=$block['training_id'];?>/<?=$block['block_id'];?>?token=<?=$_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a>
                <button title="Копировать" class="button-copy" type="submit" name="copy">
                    <i class="icon-copy"></i>
                </button>
            </form>
        </div>
    </div>

    <?php $lessons = TrainingLesson::getLessons($training_id, $block['section_id'], $block['block_id'], null);
    if($lessons):?>
        <div class="lesson-list sortable" style="margin-left:30px;">
            <?php foreach ($lessons as $lesson):?>
                <?php require(__DIR__ . '/lesson.php');?>
            <?php endforeach;?>
        </div>
    <?php endif;?>
</div>


