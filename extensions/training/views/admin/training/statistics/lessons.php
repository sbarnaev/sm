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
    <input type="hidden" name="stat_type" value="lessons">
</form>

<div class="admin_result">
    <?php if($stats):?>
        <div class="overflow-container">
            <table class="table fz-12">
                    <thead>
                        <tr>
                            <th class="text-left">Урок</th>
                            <th class="text-right">Не выслали</th>
                            <th class="text-right">На проверке</th>
                            <th class="text-right">Не сдали тест</th>
                            <th class="text-right">Незачет</th>
                            <th class="text-right">Прошли</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($stats as $stat):?>
                            <tr>
                                <td class="text-left"><?="{$stat['name']}"?></td>
                                <td class="text-right"><?="{$stat['no_send']}"?></td>
                                <td class="text-right" style="color:#FFCA10;font-weight:700"><?="{$stat['on_check']}"?></td>
                                <td class="text-right" style="color:#E04265;font-weight:700"><?="{$stat['fail_test']}"?></td>
                                <td class="text-right" style="color:#E04265;font-weight:700"><?="{$stat['fail']}"?></td>
                                <td class="text-right" style="color:#5DCE59;font-weight:700"><?="{$stat['passed']}"?></td>
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