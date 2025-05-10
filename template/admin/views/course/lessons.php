<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1><?php echo System::Lang('LESSONS_LIST');?></h1>
        <div class="logout">
            <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout/" class="red"><?php echo System::Lang('QUIT');?></a>
        </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/courses/">Тренинги</a>
        </li>
        <li>Список уроков</li>
    </ul>

    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <div class="admin_top admin_top-flex">
        <div class="admin_top-inner">
            <div>
                <img src="/template/admin/images/icons/training-1.svg" alt="">
            </div>
            <div>
                <h3 class="mb-0"><?php echo Course::getCourseNameByID($course);?></h3>
                <a href="/admin/courses/edit/<?php echo $course; ?>" class="edit-lesson"><i class="icon-pencil"></i> <span>Настройки курса</span></a>
            </div>
        </div>
        <ul class="nav_button">
            <!-- <li><input type="submit" name="save" value="Сохранить порядок" class="button save button-white font-bold"></li> -->
            <li class="nav_button__last"><a class="button red-link" href="/admin/courses/">Закрыть</a></li>
        </ul>
    </div>

    <div class="cource-list">
        <input type="hidden" name="sort_upd_url" value="/admin/lessons/updatesort">
        <?php if($lesson_list){
        foreach($lesson_list as $lesson):?>
        <div class="cource-list-item <?php if($lesson['status'] == 0) echo ' off';?>">
            <input type="hidden" name="sort[]" value="<?php echo $lesson['lesson_id'];?>">
            <div class="course-list-item__inner">
                <div class="button-drag style">
                    <img src="/template/admin/images/icons/button-drag.png">
                </div>
                <div class="course-list-item__order mr-15"><input value="<?php echo $lesson['sort'];?>" type="text"></div>
                <? /*
                <div class="course-list-item__img"><img src="/template/admin/images/icons/education.svg" alt=""></div>
                */ ?>
                <div class="course-list-item__text">
                    <div class="course-list-item__title"><a href="/admin/lessons/edit/<?php echo $lesson['lesson_id'];?>"><?php echo $lesson['name'];?></a></div>
                    <div class="course-list-item__time" style="color: #777;"><?php echo Course::getBlockLessonName($lesson['block_id']);?></div>
                    <div class="course-list-item__time"><?php if(isset($lesson['duration'])) echo $lesson['duration'].' мин.';?> <?php if($now < $lesson['public_date']) echo '<span style="color:red">Доступен с '.date("d.m.Y H:i", $lesson['public_date']).'</span>';?></div>
                </div>
                <a class="link-delete" onclick="return confirm('Вы уверены?')" href="<?php echo $setting['script_url'];?>/admin/lessons/del/<?php echo $lesson['lesson_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a>
            </div>
        </div>
        <?php endforeach;
        } else echo '<div class="course-list-item">Нет уроков</div>';?>
    <div class="add-lesson">
    <a class="button-green-rounding" href="/admin/lessons/add?filter_course=<?php echo $course;?>">+ Добавить урок</a>
    </div>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>