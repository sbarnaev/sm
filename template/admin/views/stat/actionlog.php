<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Журнал действий</h1>
        <div class="logout">
            <a href="<?=$setting['script_url'];?>" target="_blank"><?=System::Lang('GO_SITE');?></a><a href="<?=$setting['script_url'];?>/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Лог действий</li>
    </ul>

    <div class="filter admin_form">
        <form action="/admin/actionlog/">
            <div class="filter-row filter-flex-end">
               <div class="filter-1-4">
                    <select name="extension">
                        <option value="">- Выберите -</option>
                        <option value="users"<?php if($filter['extension'] == 'users') echo ' selected="selected"';?>>Пользователи</option>
                        <option value="membership"<?php if($filter['extension'] == 'membership') echo ' selected="selected"';?>>Мембершип</option>
                        <option value="installments"<?php if($filter['extension'] == 'installments') echo ' selected="selected"';?>>Рассрочки</option>
                        <option value="orders"<?php if($filter['extension'] == 'orders') echo ' selected="selected"';?>>Заказы</option>
                        <option value="blog"<?php if($filter['extension'] == 'blog') echo ' selected="selected"';?>>Блог</option>
                        <option value="products"<?php if($filter['extension'] == 'products') echo ' selected="selected"';?>>Продукты</option>
                    </select>
               </div>

               <div class="filter-1-4">
                    <div class="datetimepicker-wrap">
                        <input type="text" class="datetimepicker" name="start_date" value="<?php if(isset($filter['start_date'])) echo date('d.m.Y H:i', $filter['start_date'])?>" placeholder="От" autocomplete="off">
                    </div>
               </div>

               <div class="filter-1-4">
                    <div class="datetimepicker-wrap">
                        <input type="text" class="datetimepicker" name="finish_date" value="<?php if(isset($filter['finish_date'])) echo date('d.m.Y H:i', $filter['finish_date'])?>" placeholder="До" autocomplete="off">
                    </div>
               </div>

                <div class="filter-bottom">
                    <div>
                        <div class="order-filter-result">
                            <?php if($filter['is_filter']):?>
                                <div><p>Результатов: <?=$total;?></p></div>
                            <?php endif;?>
                        </div>
                    </div>

                    <div class="button-group">
                        <?php if($filter['is_filter']):?>
                            <a class="red-link" href="/admin/actionlog">Сбросить</a>
                        <?php endif;?>

                        <button class="button-blue-rounding" type="submit" name="filter">Фильтр</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif?>

    <div class="admin_form admin_form--margin-top">
        <div class="overflow-container">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-left">ID</th>
                        <th class="text-left">Расширение</th>
                        <th class="text-left">Действие</th>
                        <th class="text-left">Элемент</th>
                        <th class="text-left">Юзер</th>
                        <th>Время</th>
                        <!--th class="td-last"></th-->
                    </tr>
                </thead>

                <tbody>
                    <?php if($action_log):
                        foreach($action_log as $log):?>
                            <tr>
                                <td><?=$log['id'];?></td>
                                <td class="text-left"><?=$log['extension'];?></td>
                                <td class="text-left"><a href="/admin/actionlog/view/<?=$log['id'];?>"><?=$log['method'];?></a></td>
                                <td class="text-left"><?=$log['item'];?></td>
                                <td class="text-left"><?=$log['user_id'];?></td>
                                <td><?=date("d.m.Y H:i:s", $log['date']);?></td>
                            </tr>
                        <?php endforeach;
                    else:?>
                        <p>No action</p>
                    <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>

    <?=$pagination->get(); ?>
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