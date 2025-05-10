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
        <li>Статистика по уроку</li>
    </ul>
    <div class="admin_top admin_top-flex">
        <div class="admin_top-inner">
            <div>
                <img src="/template/admin/images/icons/training-1.svg" alt="">
            </div>
            <div>
                <div>Статистика курса</div>
                <h3 class="mb-0 mt-5"><?php echo $course['name'];?></h3>
            </div>
        </div>
        <ul class="nav_button">
            <li class="nav_button__last"><a class="button red-link" href="/admin/courses/stat/<?php echo $course_id;?>">Закрыть</a></li>
        </ul>
    </div>

    <form action="" method="POST">
        <div class="admin_form">
            <div>Статистика по уроку</div>
            <h4 class="h4-big mt-5"><?php echo $lesson['name'];?></h4>

            <!-- Сюда перенес фильтр. Он не работает.
            После того, как он будет раскомментирован, убрать у блока ниже style="padding-top: 0"
            <div class="order-filter-row">
                <div class="order-filter-1-4">
                    <div class="datetimepicker-wrap">
                        <input type="text" class="datetimepicker" name="start" <?php if(isset($_SESSION['filter']['start'])) { $start = $_SESSION['filter']['start']; echo " value='$start'";}?> placeholder="От" autocomplete="off">
                    </div>
                </div>
                <div class="order-filter-1-4">
                    <div class="datetimepicker-wrap">
                        <input type="text" class="datetimepicker" name="finish" <?php if(isset($_SESSION['filter']['finish'])) { $finish = $_SESSION['filter']['finish']; echo " value='$finish'";}?> placeholder="До" autocomplete="off">
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
            -->
        </div>
    </form>

    <div class="admin_result" style="padding-top: 0">
        <div class="overflow-container">
        <?php if($users){?>
        <table class="table">
            <thead>
                <tr>
                    <th class="text-left">ID</th>
                    <th class="text-right"><?php if($status == 0) echo 'Проходят урок: ('.count($users).')';
                    if($status == 1) echo 'Уже прошли: ('.count($users).')';?></th>
                    
                </tr>
            </thead>
            
            <tbody>
                <?php foreach($users as $user):?>
                <tr>
                    <td class="text-left"><?php echo $user['user_id'];?></td>
                    <td class="text-right"><a target="_blank" href="/admin/users/edit/<?php echo $user['user_id'];?>"><?php $user_data = User::getUserByID($user['user_id']); echo $user_data['user_name'];?></a></td>
                    
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <?php } else echo 'Уроков ещё нет';?>
        </div>
    </div>
    

    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>