<?php defined('BILLINGMASTER') or die;?>

<div class="structure_item_wrap">
    <input type="hidden" name="sort_items[]" value="<?=$section['section_id'];?>" data-type="section">

    <div class="structure_item section">
        <div class="button-drag"></div>

        <div class="structure_item_content">
            <div class="structure_link">
                <a href="/admin/training/editsection/<?="$training_id/{$section['section_id']}";?>"><?=$section['name'];?></a>
            </div>

            <div class="structure_desc">
                <div class="structure_desc-line">
                <div style="overflow: visible;" class="structure_desc_data"><i class="icon-access-group" title="Сортировка <?=$section['sort'];?>"></i><span><?=$section['sort'];?></span></div>
                    <div class="structure_desc_data">
                            <i class="icon-key"></i>
                        <?php $repl = array("[", "]");
                            if ($section['access_type'] == Training::ACCESS_TO_SUBS):?>
                            <?php  
                            $list_planes = str_replace($repl, '', $section['access_planes']);
                            $list_planes_name = empty($list_planes) ? 'Не выбранна подписка' : Training::getPlanesNameByList($list_planes);?>
                            <span <?=empty($list_planes) ? 'style="color:red"':'';?> title="<?=$list_planes_name?>">Подписка: <?=$list_planes_name?></span>
                        <?php elseif ($section['access_type'] == Training::ACCESS_TO_GROUP):?>
                            <?php 
                            $list_group = str_replace($repl, '', $section['access_groups']);
                            $list_group_name = empty($list_group) ? 'Не выбранна группа' : Training::getGroupNameByList($list_group);?>
                            <span <?=empty($list_group) ? 'style="color:red"':'';?> title="<?=$list_group_name?>">Группа доступа: <?=$list_group_name?></span>
                        <?php elseif ($section['access_type'] == Training::ACCESS_FREE):?>
                            Тип доступа: Свободный
                        <?php elseif ($section['access_type'] == Training::ACCESS_TO_INHERIT):?>
                            Тип доступа: Наследовать
                        <?php endif;?>  
                    </div>
                </div>
                <?php 
                $text_data = Training::getOpenDateForStructureView($section, null);
                if ($text_data):?>
                    <div class="structure_desc-line">
                        <div class="structure_desc_data"><i class="icon-time"></i><span>Дата открытия: <?=$text_data?></span></div>
                    </div>
                <?php endif;?>          
            </div>
        </div>

        <div class="structure_item_right">
            <form action="" method="POST">
                <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                <a class="link-delete__structure-item" onclick="return confirm('Вы уверены?')" href="/admin/training/delsection/<?=$section['training_id'];?>/<?=$section['section_id'];?>?token=<?=$_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a>
<!--                <button title="Копировать" class="button-copy" type="submit" name="copy">-->
<!--                    <i class="icon-copy"></i>-->
<!--                </button>-->
            </form>
        </div>
    </div>

    <?php $blocks = TrainingBlock::getBlocks($training_id, $section['section_id'], null);
    if($blocks):?>
        <div class="block-list sortable" style="margin-left:30px;">
            <?php foreach($blocks as $block):
                require(__DIR__ . '/block.php');
            endforeach;?>
        </div>
    <?php endif;?>

    <?php $lessons = TrainingLesson::getLessons($training_id, $section['section_id'], 0, null);
    if($lessons):?>
        <div class="lesson-list sortable" style="margin-left:30px;">
            <?php foreach ($lessons as $lesson):?>
                <?php require(__DIR__ . '/lesson.php');?>
            <?php endforeach;?>
        </div>
    <?php endif;?>
</div>