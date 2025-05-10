<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php');?>

<div class="main">
    <div class="top-wrap">
        <h1><?=System::Lang('MEMBER_USERS');?></h1>
        <div class="logout">
            <a href="/" target="_blank"><?=System::Lang('GO_SITE');?></a>
            <a href="/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/membersubs/">Планы подписок</a></li>
        <li>Участники</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="/admin/memberusers/add/"><?=System::Lang('CREATE_NEW_MEMBER');?></a></li>
            <li><a class="button-yellow-rounding" href="/admin/membersubs/">Перейти к планам</a></li>
            <li class="nav_gorizontal__parent-wrap">
                <div class="nav_gorizontal__parent nav_gorizontal__parent-yellow">
                    <a href="javascript:void(0);" class="nav-click button-yellow-rounding">Действие</a>
                    <span class="nav-click icon-arrow-down"></span>
                </div>

                <ul class="drop_down">
                    <li><a href="<?=$setting['script_url'];?>/admin/memberusers/export/">Экспорт →</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <div class="filter admin_form">
        <form action="/admin/memberusers" method="POST">
            <div class="filter-row filter-flex-end">
                <div class="filter-1-3">
                    <div class="select-wrap">
                        <select name="start">
                            <option value="">Когда началась</option>
                            <option value="1d"<?php if($filter['start'] == '1d') echo ' selected="selected"';?>>1 день</option>
                            <option value="7d"<?php if($filter['start'] == '7d') echo ' selected="selected"';?>>7 дней</option>
                            <option value="1m"<?php if($filter['start'] == '1m') echo ' selected="selected"';?>>30 дней</option>
                            <option value="period" data-show_on="start_dates"<?php if($filter['start'] == 'period') echo ' selected="selected"';?>>Произвольный период</option>
                        </select>
                    </div>

                    <div id="start_dates" class="hidden">
                        <div class="datetimepicker-wrap mt-10">
                            <input type="text" class="datetimepicker" name="start_from"<?php if(isset($filter['start_from']) && $filter['start_from']) echo ' value="'.date('d.m.Y H:i', $filter['start_from']).'"';?> placeholder="От" autocomplete="off" title="Выберите период начала подписки">
                        </div>

                        <div class="datetimepicker-wrap mt-10">
                            <input type="text" class="datetimepicker" name="start_to"<?php if(isset($filter['start_to']) && $filter['start_to']) echo ' value="'.date('d.m.Y H:i', $filter['start_to']).'"';?> placeholder="До" autocomplete="off" title="Выберите период окончания подписки">
                        </div>
                    </div>
                </div>

                <div class="filter-1-3">
                    <div class="select-wrap">
                        <select name="finish">
                            <option value="">Когда закончится</option>
                            <option value="1d"<?php if($filter['finish'] == '1d') echo ' selected="selected"';?>>1 день</option>
                            <option value="7d"<?php if($filter['finish'] == '7d') echo ' selected="selected"';?>>7 дней</option>
                            <option value="1m"<?php if($filter['finish'] == '1m') echo ' selected="selected"';?>>30 дней</option>
                            <option value="period" data-show_on="finish_dates"<?php if($filter['finish'] == 'period') echo ' selected="selected"';?>>Произвольный период</option>
                        </select>
                    </div>

                    <div id="finish_dates" class="hidden">
                        <div class="datetimepicker-wrap mt-10">
                            <input type="text" class="datetimepicker" name="finish_from"<?php if(isset($filter['finish_from']) && $filter['finish_from']) echo ' value="'.date('d.m.Y H:i', $filter['finish_from']).'"';?> placeholder="От" autocomplete="off" title="Выберите период начала подписки">
                        </div>

                        <div class="datetimepicker-wrap mt-10">
                            <input type="text" class="datetimepicker" name="finish_to"<?php if(isset($filter['finish_to']) && $filter['finish_to']) echo ' value="'.date('d.m.Y H:i', $filter['finish_to']).'"';?> placeholder="До" autocomplete="off" title="Выберите период окончания подписки">
                        </div>
                    </div>
                </div>

                <div class="filter-1-3">
                    <div class="select-wrap">
                        <select name="canceled">
                            <option value="">Недавно отменившие</option>
                            <option value="1d"<?php if($filter['canceled'] == '1d') echo ' selected="selected"';?>>1 день</option>
                            <option value="7d"<?php if($filter['canceled'] == '7d') echo ' selected="selected"';?>>7 дней</option>
                            <option value="1m"<?php if($filter['canceled'] == '1m') echo ' selected="selected"';?>>30 дней</option>
                            <option value="period" data-show_on="canceled_dates"<?php if($filter['canceled'] == 'period') echo ' selected="selected"';?>>Произвольный период</option>
                        </select>

                        <div id="canceled_dates" class="hidden">
                            <div class="datetimepicker-wrap mt-10">
                                <input type="text" class="datetimepicker" name="canceled_from"<?php if(isset($filter['canceled_from']) && $filter['canceled_from']) echo ' value="'.date('d.m.Y H:i', $filter['canceled_from']).'"';?> placeholder="От" autocomplete="off" title="Выберите период начала даты">
                            </div>

                            <div class="datetimepicker-wrap mt-10">
                                <input type="text" class="datetimepicker" name="canceled_to"<?php if(isset($filter['canceled_to']) && $filter['canceled_to']) echo ' value="'.date('d.m.Y H:i', $filter['canceled_to']).'"';?> placeholder="До" autocomplete="off" title="Выберите период окончания даты">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="filter-1-3">
                    <div class="select-wrap">
                        <select name="plane">
                            <option value="0">План подписки</option>
                            <?php $planes = Member::getPlanes();
                            if($planes):
                                foreach($planes as $plane):?>
                                    <option value="<?=$plane['id'];?>"<?php if($filter['plane'] == $plane['id']) echo ' selected="selected"';?>><?=$plane['name'];?></option>
                                    <?php if($plane['service_name']):?>
                                        <option disabled="disabled" class="service-name">(<?=$plane['service_name'];?>)</option>
                                    <?php endif;
                                endforeach;
                            endif;?>
                        </select>
                    </div>
                </div>

                <div class="filter-1-3">
                    <div class="select-wrap">
                        <select name="status">
                            <option value="">Статус действия</option>
                            <option value="1"<?php if($filter['status'] == 1) echo ' selected="selected"';?>>Включен</option>
                            <option value="0"<?php if($filter['status'] === 0) echo ' selected="selected"';?>>Отключен</option>
                        </select>
                    </div>
                </div>

                <div class="filter-1-3">
                    <div class="select-wrap">
                        <select name="pay_status">
                            <option value="">Статус платежей</option>
                            <option value="1"<?php if($filter['pay_status'] == 1) echo ' selected="selected"';?>>Активные</option>
                            <option value="0"<?php if($filter['pay_status'] === 0) echo ' selected="selected"';?>>Отменены</option>
                        </select>
                    </div>
                </div>

                <div class="filter-1-3">
                    <input type="text" name="email" value="<?=$filter['email'] ? $filter['email'] : '';?>" placeholder="E-mail участника">
                </div>

                <div class="filter-1-3">
                    <input type="text" name="name" value="<?=$filter['name'] ? $filter['name'] : '';?>" placeholder="Имя участника">
                </div>

                <div class="filter-1-3">
                    <input type="text" name="surname" value="<?=$filter['surname'] ? $filter['surname'] : '';?>" placeholder="Фамилия участника">
                </div>

                <div class="filter-bottom">
                    <div>
                        <div class="order-filter-result">
                            <?php if($filter && $filter['is_filter']):?>
                                <div><p>Отфильтровано: <?=$total_items;?> объекта</p></div>
                            <?php endif;?>
                            <?php if($members):?>
                                <input class="csv__link"  type="submit" name="load_csv" value="Выгрузить в csv">
                            <?php endif;?>
                        </div>
                    </div>

                    <div class="button-group">
                        <?php if($filter['is_filter']):?>
                            <a class="red-link" href="/admin/memberusers?reset">Сбросить</a>
                        <?php endif;?>

                        <input class="button-blue-rounding" type="submit" name="filter" value="Найти">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;

    if(isset($_GET['fail'])):?>
        <div class="admin_warning">Не возможно удалить!</div>
    <?php endif;?>

    <div class="admin_form admin_form--margin-top">
        <table class="table">
            <tr>
                <th>ID</th>
                <th class="text-left">Клиент</th>
                <th class="text-left">План</th>
                <th class="text-left">Дата создания</th>
                <th class="text-left">Дата окончания</th>
                <th></th>
            </tr>

            <?php if($members):
                foreach($members as $member):
                    $plane = Member::getPlaneByID($member['subs_id']);
                    $user = User::getUserNameByID($member['user_id']);?>

                    <tr<?php if($member['status'] == 0) echo ' class="off" style="color:#d0cdce"';?>>
                        <td>
                            <a href="/admin/memberusers/edit/<?=$member['id']; ?>"><?=$member['id']; ?></a>
                        </td>
                        <td class="text-left">
                            <a target="_blank" href="/admin/users/edit/<?=$member['user_id']; ?>"><?=$user['user_name'];?></a>
                            <br /><span class="small"><?=$user['email'];?></span>
                            <?php if($member['subscription_id'] != null):?>
                                <span title="ID подписки" style="color:#888"><?=$member['subscription_id'];?></span>
                            <?php endif ?>
                        </td>
                        <td class="text-left"><?=!empty($plane['service_name']) ? $plane['service_name'] :$plane['name'];?></td>
                        <td class="text-left"><?=date("d-m-Y", $member['create_date']);?></td>
                        <td class="text-left" title="<?=date("d.m.Y H:i:s", $member['end']);?>"><?=date("d.m.Y", $member['end']); ?><br>
                            Ост: <?=$member['end'] > time() ? strval(round(($member['end'] - time())/60/60/24)) : '0';?> дн.
                        </td>
                        <td>
                            <div class="one-line">
                                <a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/memberusers/delete/<?=$member['id'];?>?token=<?=$_SESSION['admin_token'];?>&action=delete" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach;
            else:?>
                <p>Участников пока нет</p>
            <?php endif;?>
        </table>
    </div>

    <?php if($is_pagination == true) echo $pagination->get();
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

<style>
    .filter-flex-end {
        align-items: baseline;
    }
</style>
</body>
</html>