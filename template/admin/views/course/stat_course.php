<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Сводная статистика по тренингу</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/courses/">Тренинги</a></li>
        <li>Статистика по тренингу</li>
    </ul>

    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;?>

    <div class="admin_top admin_top-flex">
        <div class="admin_top-inner">
            <div>
                <img src="/template/admin/images/icons/training-1.svg" alt="">
            </div>
            <div>
                <div>Статистика курса</div>
                <h3 class="mb-0 mt-5"><?=$course['name'];?></h3>
            </div>
        </div>
        <ul class="nav_button">
            <li class="nav_button__last"><a class="button red-link" href="/admin/courses/">Закрыть</a></li>
        </ul>
    </div>
    <form action="" method="POST">
            <div class="admin_form">
                <h4 class="h4-big">Сводная статистика по курсу</h4>
                <p title="Указывается дата присвоения ученику группы для доступа к курсу, если курс без групп, то будет использоваться дата регистрации пользователя">Фильтр по дате покупки курса:</p>

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
                                <div class="order-filter-submit">
                                    <?php if( isset($_SESSION['filter'])):?>
                                    <a class="order-filter-reset" href="/admin/courses/stat/<?=$course_id;?>?reset">Сбросить</a>
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
        <?php if($lesson_list):?>
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-left">№ </th>
                        <th class="text-left">Урок</th>
                        <th class="text-right">Проходят</th>
                        <th class="text-right">Прошли</th>
                        <!--th class="text-right">Закрыт</th-->
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($lesson_list as $lesson):?>
                        <tr>
                            <td><?=$lesson['sort']?></td>
                            <td class="text-left">
                                <a target="_blank" href="/admin/courses/statlessext/<?=$course_id;?>/<?=$lesson['lesson_id'];?>"><?=$lesson['name'];?></a><br />
                                <div class="course-list-item__time" style="color: #777;"><?=Course::getBlockLessonName($lesson['block_id']);?></div>
                            </td>
                            <td class="text-right">
                                <?php $open = Course::getLessonStat($lesson['lesson_id'], 0, $start, $finish, $groups, $planes);
                                if($open):?>
                                    <a href="/admin/courses/statless/<?=$course_id;?>/<?=$lesson['lesson_id'];?>?type=0"><?=count($open);?></a>
                                <?php else:
                                    echo 0;
                                endif;?>
                            </td>
                            <td class="text-right">
                                <?php $open = Course::getLessonStat($lesson['lesson_id'], 1, $start, $finish, $groups, $planes);
                                if($open):?>
                                    <a href="/admin/courses/statless/<?=$course_id;?>/<?=$lesson['lesson_id'];?>?type=1"><?=count($open);?></a>
                                <?php else:
                                    echo 0;
                                endif;?>
                            </td>
                            <!--td class="text-right"></td-->
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        <?php else:
            echo 'Уроков ещё нет';
        endif;?>
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