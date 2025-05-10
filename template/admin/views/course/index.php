<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1><?php echo System::Lang('COURSES_LIST');?></h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Тренинги</li>
    </ul>
    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li><a class="button-red-rounding" href="/admin/courses/add/"><?php echo System::Lang('CREATE_COURSE');?></a></li>

            <li class="nav_gorizontal__parent-wrap">
                <div class="nav_gorizontal__parent nav_gorizontal__parent-yellow">
                    <a href="<?php echo $setting['script_url']?>/admin/courses/cats/" class="button-yellow-rounding">Категории</a>
                    <span class="nav-click icon-arrow-down nav-click"></span>
                </div>
                <ul class="drop_down">
                    <li><a href="<?php echo $setting['script_url']?>/admin/courses/addcat/">Создать категорию</a></li>
                    <li><a href="<?php echo $setting['script_url']?>/admin/courses/cats/">Список категорий</a></li>
                </ul>
            </li>

            <li><a class="button-yellow-rounding" href="/admin/answers?get=check">Проверить задания</a></li>
            <li><a title="Общие настройки тренингов" class="settings-link" target="_blank" href="/admin/coursesetting/"><i class="icon-settings"></i></a></li>
        </ul>
    </div>
    
    <div class="filter admin_form">
        <div class="filter-row">
            <form action="" method="POST">
                <div style="float:left; margin:0 15px 0 0">
                    <div class="select-wrap">
                        <select name="cat_id">
                            <option value="0">Все категории</option>
                            <?php $cat_list = Course::getCourseCatFromList();
                            if($cat_list):
                            foreach($cat_list as $cat):?>
                            <option value="<?php echo $cat['cat_id'];?>"<?php if(isset($_SESSION['filter_cat_id'])){ if($cat['cat_id'] == $_SESSION['filter_cat_id']) echo ' selected="selected"';}?>><?php echo $cat['name'];?></option>
                            <?php endforeach;
                            endif;?>
                        </select>
                    </div>
                </div>
                
                <div style="float:left">
                    <input type="submit" class="button-blue-rounding" name="filter" value="Фильтр">
                </div>
            </form>
        </div>
    </div>

    <?php if(Course::hasSuccess()) Course::showSuccess();?>
    <?php if(Course::hasError()) Course::showError();?>
    
    <div class="course-list">
        <input type="hidden" name="sort_upd_url" value="/admin/courses/updatesort">
        <?php if($course_list){
        foreach($course_list as $course):?>
        <div class="course-list-item d-flex <?php if($course['status'] == 0) echo ' off';?>">
            <input type="hidden" name="sort[]" value="<?php echo $course['course_id'];?>">
            <div class="course-list-item__left button-drag"><img src="/template/admin/images/icons/training-icon.svg" alt=""></div>
            <div class="course-list-item__center">
            <h4 class="course-list-item__name"><a href="/admin/courses/edit/<?php echo $course['course_id'];?>"><?php echo $course['name'];?></a>
            </h4>
            <div class="course-list-item__data">
                <?php if(!empty($course['author_id'])) {$user_data = User::getUserNameByID($course['author_id']);
                echo '<div class="course-list-item__author"><i class="icon-user"></i>'.$user_data['user_name'].'</div>';
                }?>
                <?php if($course['cat_id'] != 0) {?>
                    <div><i class="icon-list"></i>
                        <?php $cat_name = Course::getCourseCatData($course['cat_id']);
                        if(!empty($cat_name)) echo $cat_name['name'];
                        else echo 'Без категории'; ?>
                    </div> <?
                    }?>
                <?php if($course['auto_train'] == 1) echo '<div><i class="icon-arrow-auto"></i>Авто-тренинг</div>';?>
            </div>
            <p class="course-list-item__descr"><a href="/admin/lessons?course=<?php echo $course['course_id'];?>/">Список уроков</a>&nbsp; &nbsp; &nbsp;<a href="/admin/courses/stat/<?php echo $course['course_id'];?>">Статистика</a></p>
            </div>
            <div class="course-list-item__right">
                <form action="" method="POST"><input type="hidden" name="course_id" value="<?php echo $course['course_id'];?>">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                    <button title="Копировать" class="button-copy" type="submit" name="copy"><i class="icon-copy"></i></button></form>
            </div>
        </div>
        <?php endforeach;
                } else echo '<div class="course-list-item">Вы пока не добавили ни одного курса</div>';?>
    </div>

    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>