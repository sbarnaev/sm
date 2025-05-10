<?php defined('BILLINGMASTER') or die;?>

<form class="filter-form" action="/admin/training/statistics/<?=$training_id;?>/" method="POST">
    <p><strong>Фильтровать</strong></p>

    <div class="filter-row filter-flex-end mb-20">
        <div class="filter-1-4">
            <div class="datetimepicker-wrap">
                <input type="text" class="datetimepicker" name="start_date" value="<?=$filter['start_date'] ? date("d.m.Y H:i", $filter['start_date']) : '';?>" placeholder="От" autocomplete="off" data-format="d.m.Y H:i">
            </div>
        </div>

        <div class="filter-1-4">
            <div class="datetimepicker-wrap">
                <input type="text" class="datetimepicker" name="finish_date" value="<?=$filter['finish_date'] ? date("d.m.Y H:i", $filter['finish_date']) : '';?>" placeholder="До" autocomplete="off" data-format="d.m.Y H:i">
            </div>
        </div>

        <div class="filter-bottom">
            <div>
                <div class="order-filter-result">
                    <?php if($stat):?>
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
    <input type="hidden" name="stat_type" value="common">
</form>

<div class="admin_result">
    <?php if($stat):?>
    <div class="overflow-container">
        <table class="table fz-12">
            <thead>
                <tr>
                    <th class="text-left">Прохождение курса</th>
                    <th class="text-right">Учеников</th>
                    <th class="text-right">Начали</th>
                    <th class="text-right">Закончили</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td class="text-left">Прогресс курса</td>
                    <td class="text-right"><?=$stat['users'];?></td>
                    <td class="text-right">
                        <div class="progress">
                            <div class="completed_line" style="width:<?=$stat['progress_started'];?>"></div>
                        </div>
                        <nobr class="number-people"><?=$stat['progress_started'];?> (<?=$stat['started'];?> чел.)</nobr>
                    </td>
                    <td class="text-right">
                        <div class="progress">
                            <div class="completed_line" style="width:<?=$stat['progress_finished'];?>"></div>
                        </div>
                        <nobr class="number-people"><?=$stat['progress_finished'];?> (<?=$stat['finished'];?> чел.)</nobr>
                    </td>
                </tr>

                <?php if($sections):
                    foreach($sections as $section):
                        $stat = TrainingStatistics::getCommonStatistics($training, $section, $filter);?>
                        <tr>
                            <td class="text-left"><?=$section['name'];?></td>
                            <td class="text-right"><?=$stat['users'];?></td>
                            <td class="text-right">
                                <div class="progress">
                                    <div class="completed_line" style="width:<?=$stat['progress_started'];?>"></div>
                                </div>
                                <nobr class="number-people"><?=$stat['progress_started'];?> (<?=$stat['started'];?> чел.)</nobr>
                            </td>
                            <td class="text-right">
                                <div class="progress">
                                    <div class="completed_line" style="width:<?=$stat['progress_finished'];?>"></div>
                                </div>
                                <nobr class="number-people"><?=$stat['progress_finished'];?> (<?=$stat['finished'];?> чел.)</nobr>
                            </td>
                        </tr>
                    <?php endforeach;
                endif;?>
            </tbody>
        </table>
    </div>
    <?php else:?>
        <p><?=$filter['is_filter'] ? 'Ничего не найдено' : 'Пользователей ещё нет';?></p>
    <?php endif;?>
</div>

<script>
  $('.datetimepicker').datetimepicker({
    format:'d.m.Y H:i',
    lang:'ru'
  });
</script>