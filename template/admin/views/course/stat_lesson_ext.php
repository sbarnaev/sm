<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Сводная статистика по уроку</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/courses/">Тренинги</a>
        </li>
        <li>
            <a href="/admin/courses/stat/<?php echo $course_id;?>">Статистика по тренингу</a>
        </li>
        <li>Сводная статистика по уроку</li>
    </ul>

    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>

    <div class="admin_top admin_top-flex">
        <div class="admin_top-inner">
            <div>
                <img src="/template/admin/images/icons/training-1.svg" alt="">
            </div>
            <div>
                <div>Статистика по уроку</div>
                <h3 class="mb-0 mt-5"><?php echo $lesson['name'];?></h3>
            </div>
        </div>
        <ul class="nav_button">
            <li class="nav_button__last"><a class="button red-link" href="/admin/courses/stat/<?php echo $course_id;?>">Закрыть</a></li>
        </ul>
    </div>
    <form action="" method="POST">
            <div class="admin_form">
                <h4 class="h4-big">Сводная статистика по уроку</h4>
                <p>Дата покупки:</p>

                <div class="order-filter-row">
                    <div class="order-filter-1-4">
                        <div class="datetimepicker-wrap">
                            <input type="text" class="datetimepicker" name="start" <?php if(isset($_SESSION['filter']['start'])) { echo " value='".$_SESSION['filter']['start']."'";} else echo " value='".date("d-m-Y H:i", $start)."'";?> placeholder="От" autocomplete="off">
                        </div>
                    </div>
                    <div class="order-filter-1-4">
                        <div class="datetimepicker-wrap">
                            <input type="text" class="datetimepicker" name="finish" <?php if(isset($_SESSION['filter']['finish'])) {echo " value='".$_SESSION['filter']['finish']."'";} else echo " value='".date("d-m-Y H:i", $finish)."'"?> placeholder="До" autocomplete="off">
                        </div>
                    </div>

                    <div class="order-filter-button">
                        <div class="order-filter-two-row">
                            <div>
                                <?php if( isset($_SESSION['filter']) && !empty($_SESSION['filter']['paid'])):?>
                                <div class="order-filter-result">
                                    <div><p>Отфильтровано: <?php if($order_list) echo count($order_list);?> объекта</p></div>
                                </div>
                                <?php endif;?>
                            </div>
                            <div>
                                <div class="order-filter-submit">
                                    <?php if( isset($_SESSION['filter'])):?>
                                    <a class="order-filter-reset" href="/admin/orders?reset">Сбросить</a>
                                    <?php endif;?>
                                    <input class="button-blue-rounding" type="submit" name="filter" value="Найти">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    
    
    <div class="admin_result">
        <div class="overflow-container">
        <?php if($users){?>
        <table class="table">
            <thead>
                <tr>
                    <th class="text-left">Пользователь</th>
                    <th class="text-right">Покупка</th>
                    <th class="text-right">Текущий урок</th>
                    <th class="text-right">Пройдено уроков</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($users as $user):
                $user_data = User::getUserDataForAdmin($user['user_id']);?>
                <tr>
                    <td class="text-left"><a target="_blank" href="/admin/users/edit/<?php echo $user_data['user_id'];?>"><?php echo $user_data['user_name'];?></a><br />
                    <span class="small"><?php echo $user_data['email'];?></span></td>
                    <td class="text-right">
                        <?php // Получение даты покупки курса взависмости от типа доступа к нему: группа/подписка
                        $buy_date = Course::getPaidUserDate($user_data['user_id'], $groups, $planes);
                        echo date("d-m-Y H:i:s", $buy_date);
                        ?>
                    </td>
                    <td class="text-right"><?php $less_time = $user['date']; 
                                            if($user['status'] == 1) echo 'Пройден<br />'.date("d.m.Y H:i:s", $less_time); 
                                            else {
                                                $open_time = round(($now - $less_time) / 3600); // открыт часов
                                                echo 'Открыт ('. $open_time .'ч )<br />'.date("d.m.Y H:i:s", $less_time);
                                            }?></td>
                    <td class="text-right"><?php $complete_less = Course::getCompleteLessonsUser($user['user_id'], $course_id, 1);
                    if($complete_less) echo count($complete_less);?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <?php } else echo 'В указанный период никто не найден';?>
        </div>
    </div>
    

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