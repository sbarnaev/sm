<?php defined('BILLINGMASTER') or die;?>

<form class="filter-form" action="/admin/training/statistics/<?=$training_id;?>/" method="POST">
    <p><strong>Фильтровать</strong></p>

    <div class="filter-row filter-flex-end mb-20">
        <div class="filter-1-3">
            <input type="text" name="email" placeholder="E-mail" value="<?=$filter['email'] ? $filter['email'] : '';?>">
        </div>

        <div class="filter-1-3">
            <div class="select-wrap">
                <select name="curator">
                    <option value="">Куратор</option>
                    <?php $curators = User::getCurators();
                    if ($curators):
                        foreach($curators as $curator):
                            if($tr_curators['datacurators'] && in_array($curator['user_id'], $tr_curators['datacurators']) ||
                            $tr_curators['datamaster'] && in_array($curator['user_id'], $tr_curators['datamaster'])):?>
                                <option value="<?=$curator['user_id']?>"<?php if($filter['curator'] && $filter['curator'] == $curator['user_id']) echo ' selected="selected"';?>><?=$curator['user_name'] .' '. $curator['surname']?></option>
                        <?php endif;
                    endforeach;
                    endif;?>
                </select>
            </div>
        </div>

        <div class="filter-1-3">
            <div class="select-wrap">
                <select name="pass_status">
                    <option value="">Статус прохождения</option>
                    <option value="1"<?php if($filter['pass_status'] && $filter['pass_status'] == 1) echo ' selected="selected"';?>>Ни разу не входили в курс</option>
                    <option value="2"<?php if($filter['pass_status'] && $filter['pass_status'] == 2) echo ' selected="selected"';?>>Заходили, но не проходили уроки</option>
                    <option value="3"<?php if($filter['pass_status'] && $filter['pass_status'] == 3) echo ' selected="selected"';?>>Остановились в процессе обучения</option>
                    <option value="4"<?php if($filter['pass_status'] && $filter['pass_status'] == 4) echo ' selected="selected"';?>>Закончил</option>
                </select>
            </div>
        </div>

        <div class="filter-1-5">
            <div class="datetimepicker-wrap">
                <input type="text" class="datetimepicker" name="start_date" value="<?=$filter['start_date'] ? date("d.m.Y", $filter['start_date']) : '';?>" placeholder="От" autocomplete="off" data-format="d.m.Y">
            </div>
        </div>

        <div class="filter-1-5">
            <div class="datetimepicker-wrap">
                <input type="text" class="datetimepicker" name="finish_date" value="<?=$filter['finish_date'] ? date("d.m.Y", $filter['finish_date']) : '';?>" placeholder="До" autocomplete="off" data-format="d.m.Y">
            </div>
        </div>

        <div class="filter-1-4">
            <input type="text" name="completed_lessons" placeholder="Прошли уроков" value="<?=$filter['completed_lessons'] ? $filter['completed_lessons'] : '';?>">
        </div>

        <div class="filter-1-3 flex-grow-1">
            <div class="select-wrap">
                <select name="last_lesson_complete">
                    <option value="">Последний пройденный</option>
                    <?php $lessons = TrainingLesson::getLessons($training_id);
                    if ($lessons):
                        foreach($lessons as $lesson):?>
                            <option value="<?=$lesson['lesson_id']?>"<?php if($filter['last_lesson_complete'] && $filter['last_lesson_complete'] == $lesson['lesson_id']) echo ' selected="selected"';?>><?=$lesson['name'];?></option>
                        <?php endforeach;
                    endif;?>
                </select>
            </div>
        </div>

        <div class="filter-1-3">
            <div class="select-wrap">
                <select name="lesson_id">
                    <option value="" data-show_off="filter-lesson">Урок</option>
                    <?php $lessons = TrainingLesson::getLessons($training_id);
                    if ($lessons):
                        foreach($lessons as $lesson):?>
                            <option value="<?=$lesson['lesson_id']?>"<?php if($filter['lesson_id'] && $filter['lesson_id'] == $lesson['lesson_id']) echo ' selected="selected"';?>><?=$lesson['name'];?></option>
                        <?php endforeach;
                    endif;?>
                </select>
            </div>
        </div>

        <div class="filter-1-3" id="filter-lesson">
            <div class="select-wrap">
                <select name="lesson_status">
                    <option value="">Состояние урока</option>
                    <option value="1"<?php if($filter['lesson_status'] && $filter['lesson_status'] == 1) echo ' selected="selected"';?>>Прошли урок</option>
                    <option value="2"<?php if($filter['lesson_status'] && $filter['lesson_status'] == 2) echo ' selected="selected"';?>>Не выслали дз</option>
                    <option value="3"<?php if($filter['lesson_status'] && $filter['lesson_status'] == 3) echo ' selected="selected"';?>>Отправили дз на проверку</option>
                    <option value="4"<?php if($filter['lesson_status'] && $filter['lesson_status'] == 4) echo ' selected="selected"';?>>Не проходили тест</option>
                    <option value="5"<?php if($filter['lesson_status'] && $filter['lesson_status'] == 5) echo ' selected="selected"';?>>Не сдали тест</option>
                    <option value="6"<?php if($filter['lesson_status'] && $filter['lesson_status'] == 6) echo ' selected="selected"';?>>Получили незачет</option>
                </select>
            </div>
        </div>

        <div class="filter-bottom">
            <div>
                <div class="order-filter-result">
                    <?php if($stats):?>
                        <input class="csv__link"  type="submit" name="load_csv" value="Выгрузить в csv">
                    <?php endif;?>
                </div>
            </div>

            <div class="button-group">
                <?php if($filter['is_filter']):?>
                    <a class="red-link" href="/admin/training/statistics/<?=$training_id;?>/?reset">Сбросить</a>
                <?php endif;?>

                <input class="button-blue-rounding" type="submit" name="filter" value="Найти">
            </div>
        </div>
    </div>
    <hr>
    <input type="hidden" name="stat_type" value="users">
</form>

<div class="admin_result">
    <p><strong>Список учеников</strong></p>
    <?php if($stats):?>
    <div class="overflow-container">
        <table class="table fz-12">
            <thead>
                <tr>
                    <th class="text-left">Клиент</th>
                    <th class="text-right">Прогресс</th>
                    <th class="text-right">Начал</th>
                    <th class="text-left last-people">Последний пройденный</th>
                    <th class="text-right">Не был</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($stats as $stat):
                    $stat['user_name'] = $stat['user_name'] ? str_replace(' ', '<br>', $stat['user_name']) : '--';?>
                    <tr>
                        <td class="text-left">
                            <a class="user-link" href="/admin/users/edit/<?=$stat['user_id'];?>" target="_blank"><?=$stat['user_name']?></a>
                        </td>
                        <td class="text-right">
                            <div class="progress list-users-progress" title="Пройдено уроков: <?="{$stat['completed']} из {$count_lessons}";?>">
                                <div class="completed_line" style="width:<?=$stat['progress'];?>"></div>
                            </div>
                            <nobr class="number-people"><?=$stat['progress'];?> (уроков: <?=$stat['completed'];?>)</nobr>
                        </td>
                        <td class="text-right"><?=date("d.m.Y", $stat['open']).'<br>'.date("H:i:s", $stat['open']);?></td>
                        <td class="text-left last-people"><?=$stat['last_lesson_complete_name'] ? $stat['last_lesson_complete_name'] : '--';?></td>
                        <td class="text-right"><?=$stat['was_not'] ? "{$stat['was_not']} дн." : '--';?></td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <?php else:?>
        <p><?=$filter['is_filter'] ? 'Ничего не найдено' : 'Пользователей ещё нет';?></p>
    <?php endif;?>
</div>

<script>
  $('.datetimepicker').datetimepicker({
    format:'d.m.Y',
    lang:'ru'
  });
</script>