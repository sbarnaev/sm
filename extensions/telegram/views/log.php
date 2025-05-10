<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Журнал событий</h1>
        <div class="logout">
            <a href="/" target="_blank"><?=System::Lang('GO_SITE');?></a>
            <a href="<?=$setting['script_url'];?>/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/extensions/">Расширения</a></li>
        <li><a href="/admin/telegramsetting/">Настройки Telegram</a></li>
        <li>Журнал событий</li>
    </ul>

    <div class="filter admin_form">
        <form action="" method="POST">
            <div class="filter-row filter-flex-end">
                <div class="filter-1-3">
                    <input type="text" name="email" value="<?=$filter['email'] ? $filter['email'] : '';?>" placeholder="E-mail пользователя">
                </div>

                <div class="filter-1-3">
                    <input type="text" name="username" value="<?=$filter['username'] ? $filter['username'] : '';?>" placeholder="имя пользователя">
                </div>

                <div class="filter-1-3">
                    <input type="text" name="sm_user_id" value="<?=$filter['sm_user_id'] ? $filter['sm_user_id'] : '';?>" placeholder="ID пользователя в SM">
                </div>

                <div class="filter-1-3">
                    <input type="text" name="chat_id" value="<?=$filter['chat_id'] ? $filter['chat_id'] : '';?>" placeholder="ID чата/канала">
                </div>

                <div class="filter-1-3">
                    <div class="datetimepicker-wrap">
                        <input type="text" class="datetimepicker" name="start"<?php if($filter['start']) echo ' value="'.date('d.m.Y H:i', $filter['start']).'"';?> placeholder="От" autocomplete="off" title="Выберите период начала событий">
                    </div>
                </div>

                <div class="filter-1-3">
                    <div class="datetimepicker-wrap">
                        <input type="text" class="datetimepicker" name="finish"<?php if($filter['finish']) echo ' value="'.date('d.m.Y H:i', $filter['finish']).'"';?> placeholder="До" autocomplete="off" title="Выберите период окончания событий">
                    </div>
                </div>

                <div class="filter-bottom">
                    <div class="button-group">
                        <?php if($filter['is_filter']):?>
                            <a class="red-link" href="/admin/telegramsetting/log?reset">Сбросить</a>
                        <?php endif;?>

                        <input class="button-blue-rounding" type="submit" name="filter" value="Найти">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="admin_form admin_form--margin-top">
        <div class="overflow-container">
            <?php if($log_list):?>
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-left">
                                ID пользователя в SM<br>
                                ID пользователя в TG
                            </th>
                            <th class="text-left">Имя<br>пользователя</th>
                            <th class="text-left">Тип события</th>
                            <th class="td-last">Время события</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($log_list as $log):
                            $user = Telegram::getUserBySmUserId($log['sm_user_id']);?>
                            <tr>
                                <td class="text-left">
                                    <?php if($log['sm_user_id']):?>
                                        <a href="/admin/users/edit/<?=$log['sm_user_id'];?>" target="_blank"><?=$log['sm_user_id'];?></a>
                                    <?php else:?>
                                        <span>--</span>
                                    <?php endif;?>
                                    <br>
                                    <span><?=$log['user_id'] ? $log['user_id'] : '--'?></span>
                                </td>
                                <td><?=$user && $user['user_name'] ? $user['user_name'] : '--';?></td>
                                <td class="text-left"><?=Telegram::getMessageToEvent($log);?></td>
                                <td class="td-last"><?=date("d.m.Y H:i:s", $log['date']);?></td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            <?php else:?>
                <p>Событий пока небыло</p>
            <?php endif;?>
        </div>
    </div>
    <?php if($is_pagination) echo $pagination->get();?>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
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