<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
  <div class="top-wrap">
    <h1>Просмотр действия</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
  </div>
  <ul class="breadcrumb">
    <li>
      <a href="/admin">Дашбоард</a>
    </li>
    <li><a href="/admin/actionlog/">Лог действий</a></li>
    <li>Просмотр действия</li>
  </ul>

    <div class="admin_form">
       <div class="row-line">
            <div class="col-1-2">
                <h4>Основное</h4>
                <p class="width-100"><label>Расширение: <?=$log['extension'];?></label></p>
                <p class="width-100"><label>Метод: <?=$log['method'];?></label></p>
                <p class="width-100"><label>Элемент: <?=$log['item'];?> (<?=$log['item_id'];?>)</label></p>
                <p class="width-100"><label>Пользователь: <a target="_blank" href="/admin/users/edit/<?=$log['user_id'];?>"><?=$log['user_id'];?></a></label></p>

            </div>

            <div class="col-1-2">
                <h4>Дата</h4>
                <p class="width-100"><label>Расширение: <?= date("d.m.Y H:i:s", $log['date']);?></label></p>
            </div>

            <div class="box1">
                <h4>Данные:</h4>
                <?php echo '<pre>';
                print_r(json_decode($log['data'], 1));
                echo '</pre>';?>
            </div>
    </div>
    </div>

    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>