<?php defined('BILLINGMASTER') or die;?>

<div class="structure_item_wrap">
    <input type="hidden" name="sort_items[]" value="<?=$lesson['lesson_id'];?>" data-type="lesson">

    <div class="structure_item lesson">
        <div class="button-drag"></div>

        <div class="structure_item_content">
            <div class="structure_link">
                <a href=/admin/training/editlesson/<?="$training_id/{$lesson['lesson_id']}";?>><?=$lesson['name'];?></a>
            </div>

            <div class="structure_desc">
                <div class="structure_desc-line">
                    <div style="overflow: visible;" class="structure_desc_data"><i class="icon-access-group" title="Сортировка <?=$lesson['sort'];?>"></i><?=$lesson['sort'];?></div>
                    <div class="structure_desc_data">
                        <i class="icon-key"></i>
                    <?php $repl = array("[", "]");
                    if ($lesson['access_type'] == Training::ACCESS_TO_SUBS):
                        $list_planes = str_replace($repl, '', $lesson['access_planes']);
                        $list_planes_name = empty($list_planes) ? 'Не выбранна подписка' : Training::getPlanesNameByList($list_planes);?>
                        <span <?=empty($list_planes) ? 'style="color:red"':'';?> title="<?=$list_planes_name;?>">Подписка: <?=$list_planes_name;?></span>
                    <?php elseif ($lesson['access_type'] == Training::ACCESS_TO_GROUP):
                        $list_group = str_replace($repl, '', $lesson['access_groups']);
                        $list_group_name = empty($list_group) ? 'Не выбранна группа' : Training::getGroupNameByList($list_group);?>
                        <span <?=empty($list_group) ? 'style="color:red"':'';?> title="<?=$list_group_name;?>">Группа доступа: <?=$list_group_name;?></span>
                    <?php elseif ($lesson['access_type'] == Training::ACCESS_FREE):?>
                        Тип доступа: Свободный
                    <?php elseif ($lesson['access_type'] == Training::ACCESS_TO_INHERIT):?>
                        Тип доступа: Наследовать
                    <?php endif; ?>
                    </div>
                </div>
                <?php $task = TrainingLesson::getTask2Lesson($lesson['lesson_id']); 
                if (isset($task['stop_lesson']) && $task['stop_lesson'] == 1):?>
                    <div class="structure_desc-line">
                    <div class="structure_desc_data overflow-visible"><i class="icon-stop color-red"></i>Стоп-урок</div>
                    <?php if ($lesson['shedule']==2):?>
                        <div class="structure_desc_data"><i class="icon-time"></i><span>Дата открытия: <?=Training::getOpenDateForStructureView(null, $lesson); ?></span></div>
                    <? endif;?>
                    </div>
                <?php else:?>
                    <?php if ($lesson['shedule']==2):?>
                        <div class="structure_desc-line">
                            <div class="structure_desc_data"><i class="icon-time"></i><span>Дата открытия: <?=Training::getOpenDateForStructureView(null, $lesson); ?></span></div>
                        </div>
                    <?php endif;?>
                <?php endif;?>
            </div>
        </div>

        <div class="structure_item_right">
            <form action="" method="POST">
                <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                <a class="link-delete__structure-item" onclick="return confirm('Вы уверены?')" href="/admin/training/dellesson/<?=$training_id;?>/<?=$lesson['lesson_id'];?>?token=<?=$_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a>
                <a class="link-copy__structure-item" href="/admin/training/copylesson/<?=$training_id;?>/<?=$lesson['lesson_id'];?>?token=<?=$_SESSION['admin_token'];?>" title="Копировать"><i class="icon-copy"></i></a>
            </form>
        </div>
    </div>
</div>