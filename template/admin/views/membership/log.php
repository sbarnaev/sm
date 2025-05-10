<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1><?=System::Lang('MEMBER_SUBS');?></h1>
        <div class="logout">
            <a href="/" target="_blank"><?=System::Lang('GO_SITE');?></a>
            <a href="/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>
    
    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li>Журнал продлений подписок</li>
    </ul>

    <div class="filter admin_form">
        <form action="/admin/memberlog/">
            <div class="filter-row filter-flex-end">
                <div class="filter-1-3">
                    <input type="text" name="subs_map_id" value="<?=$filter['subs_map_id'] ? $filter['subs_map_id'] : '';?>" placeholder="ID подписки">
                </div>

                <div class="filter-1-3">
                    <div class="select-wrap">
                        <select name="plane_id">
                            <option value="">План подписки</option>
                            <?php $planes = Member::getPlanes();
                            if($planes):
                                foreach($planes as $plane):?>
                                    <option value="<?=$plane['id'];?>"<?php if($filter['plane_id'] == $plane['id']) echo ' selected="selected"';?>><?=$plane['name'];?></option>
                                    <?php if($plane['service_name']):?>
                                        <option disabled="disabled" class="service-name">(<?=$plane['service_name'];?>)</option>
                                    <?php endif;
                                endforeach;
                            endif;?>
                        </select>
                    </div>
                </div>

                <div class="filter-1-3">
                    <input type="text" name="user_id" value="<?=$filter['user_id'] ? $filter['user_id'] : '';?>" placeholder="ID пользователя">
                </div>

                <div class="filter-1-3">
                    <div class="datetimepicker-wrap mt-10">
                        <input type="text" class="datetimepicker" name="start_date"<?php if(isset($filter['start_date']) && $filter['start_date']) echo ' value="'.date('d.m.Y H:i', $filter['start_date']).'"';?> placeholder="От" autocomplete="off" title="Выберите период начала продения подписки">
                    </div>
                </div>

                <div class="filter-1-3">
                    <div class="datetimepicker-wrap mt-10">
                        <input type="text" class="datetimepicker" name="finish_date"<?php if(isset($filter['finish_date']) && $filter['finish_date']) echo ' value="'.date('d.m.Y H:i', $filter['finish_date']).'"';?> placeholder="До" autocomplete="off" title="Выберите период окончания продения подписки">
                    </div>
                </div>

                <div class="filter-bottom">
                    <div>
                        <div class="order-filter-result">
                            <?php if($filter && $filter['is_filter']):?>
                                <div><p>Отфильтровано: <?=$total_items;?> объекта</p></div>
                            <?php endif;?>
                        </div>
                    </div>

                    <div class="button-group">
                        <?php if($filter['is_filter']):?>
                            <a class="red-link" href="/admin/memberlog/?reset">Сбросить</a>
                        <?php endif;?>

                        <input class="button-blue-rounding" type="submit" name="filter" value="Найти">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="admin_form admin_form--margin-top">
        <table class="table">
            <tr>
                <th>ID</th>
                <th class="text-left">Юзер</th>
                <th class="text-left">План</th>
                <th class="text-left">Подписка</th>
                <th class="text-left">Было</th>
                <th class="text-left">Стало</th>
                <th class="text-left">Срок</th>
                <th class="text-left">Дата</th>
            </tr>
            
            <?php if($logs):
                foreach($logs as $item):?>
                    <tr>
                        <td><?=$item['id']; ?></td>
                        <td class="text-left"><a target="_blank" href="/admin/users/edit/<?=$item['user_id'];?>"><? $user = User::getUserNameByID($item['user_id']); echo $user['user_name'];?></a></td>
                        <td><a target="_blank" href="/admin/membersubs/edit/<?=$item['plane_id'];?>"><? $plane = Member::getPlaneByID($item['plane_id']); echo $plane['name'];?></a></td>
                        <td><?php if($item['subs_map_id'] != 0):?><a target="_blank" href="/admin/memberusers/edit/<?=$item['subs_map_id'];?>"><?=$item['subs_map_id']; ?></a><?php endif;?></td>
                        <td class="text-left"><?php if($item['end_before'] != 0) echo date("d.m.Y H:i:s", $item['end_before']);?></td>
                        <td class="text-left"><?=date("d.m.Y H:i:s", $item['end_after']); ?></td>
                        <td><?=$item['time_prolong'] / 86400;?> дн</td>
                        <td class="text-left"><?=date("d.m.Y H:i:s", $item['date']); ?></td>
                    </tr>
                <?php endforeach;
            else:?>
                <p>Продлений пока не было</p>
            <?php endif;?>
        </table>
    </div>

    <?=$pagination->get();
    require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

<link rel="stylesheet" type="text/css" href="/template/admin/css/jquery.datetimepicker.min.css">
<script src="/template/admin/js/jquery.datetimepicker.full.min.js"></script>
<script>
  jQuery('.datetimepicker').datetimepicker({
    format:'d.m.Y H:i',
    lang:'ru'
  });
</script>
</body>
</html>