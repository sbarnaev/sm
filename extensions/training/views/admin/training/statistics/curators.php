<?php defined('BILLINGMASTER') or die;?>

<form class="filter-form" action="/admin/training/statistics/<?=$training_id;?>/" method="POST">
    <p><strong>Фильтровать</strong></p>

    <div class="filter-row filter-flex-end mb-20">
        <div class="filter-1-4">
            <label>Анализируем период:</label>
            <div class="datetimepicker-wrap">
                <input type="text" class="datetimepicker" name="start_date" value="<?=$filter['start_date'] ? date("d.m.Y", $filter['start_date']) : '';?>" placeholder="От" autocomplete="off" data-format="d.m.Y H:i">
            </div>
        </div>

        <div class="filter-1-4">
            <div class="datetimepicker-wrap">
                <input type="text" class="datetimepicker" name="finish_date" value="<?=$filter['finish_date'] ? date("d.m.Y", $filter['finish_date']) : '';?>" placeholder="До" autocomplete="off" data-format="d.m.Y H:i">
            </div>
        </div>

        <div class="filter-1-2 px-label-wrap">
            <label>Считать бросившим, если не активен:<span class="px-label">дн.</span></label>
            <input type="text" name="stop_out_day" placeholder="" value="<?=$filter['stop_out_day'] ? $filter['stop_out_day'] : '30';?>">
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
    <input type="hidden" name="stat_type" value="curators">
</form>

<div class="admin_result">
    <?php if($stats):?>
        <div class="overflow-container">
            <table class="table fz-12">
                <thead>
                    <tr>
                        <th class="text-left">Куратор</th>
                        <th class="text-right">Учеников</th>
                        <th class="text-right">Бросили</th>
                        <th class="text-right">В процессе</th>
                        <th class="text-right">Прошли</th>
                        <th class="text-right">Проверено заданий</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($stats as $stat):
                        $user_name = $stat['sur_name'] ? "{$stat['user_name']}<br>{$stat['sur_name']}" : $stat['user_name'];
                        $in_progress = $stat['students']-$stat['passed']-$stat['countday'];?>
                        <tr>
                            <td class="text-left">
                                <a href="/admin/users/edit/<?=$stat['curator_id'];?>" target="_blank"><?=$user_name;?></a>
                            </td>
                            <td class="text-right"><?=$stat['students'];?></td>
                            <td class="text-right" style="color:#E04265;font-weight:700"><?=$stat['countday'] .' ('. round($stat['countday']/$stat['students']*100) . ' %)';?></td>
                            <td class="text-right" style="color:#FFCA10;font-weight:700"><?=$in_progress .' ('. round($in_progress/$stat['students']*100) . ' %)';?></td>
                            <td class="text-right" style="color:#5DCE59;font-weight:700"><?=$stat['passed'] . ' ('. round($stat['passed']/$stat['students']*100) . ' %)';?></td>
                            <td class="text-right"><?=$stat['checked'];?></td>
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
    format:'d.m.Y H:i',
    lang:'ru'
  });
</script>