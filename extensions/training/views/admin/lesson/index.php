<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1><?=System::Lang('LESSONS_LIST');?></h1>
        <div class="logout">
            <a href="/" target="_blank"><?=System::Lang('GO_SITE');?></a><a href="/admin/logout/" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li></li>
        <li>Список уроков</li>
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
                <h3 class="mb-0"><?=Training::getTrainingNameByID($training_id);?></h3>
                <a href="/admin/training/edit/<?=$training_id; ?>" class="edit-lesson"><i class="icon-pencil"></i> <span>Настройки тренинга</span></a>
            </div>
        </div>

        <ul class="nav_button">
            <li><a href="/admin/training/addlesson/<?=$training_id;?>" class="button save button-white font-bold">Добавить урок</a></li>
            <li class="nav_button__last"><a class="button red-link" href="/admin/training/">Закрыть</a></li>
        </ul>
    </div>

    <div class="cource-list">
        <input type="hidden" name="sort_upd_url" value="/admin/lessons/updatesort">
        <?php if($lesson_list):
            foreach($lesson_list as $lesson):
                $task = TrainingLesson::getTask2Lesson($lesson['lesson_id']);;?>
                <div class="cource-list-item <?php if($lesson['status'] == 0) echo ' off';?>">
                    <input type="hidden" name="sort[]" value="<?=$lesson['lesson_id'];?>">
                    <div class="course-list-item__inner">
                        <div class="button-drag style"></div>
                        <div class="course-list-item__order mr-15"><input value="<?=$lesson['sort'];?>" type="text"></div>
                        <div class="course-list-item__text">
                            <div class="course-list-item__title">
                                <a href="/admin/training/editlesson/<?=$training_id;?>/<?=$lesson['lesson_id'];?>"><?=$lesson['name'];?></a>
                            </div>
                            <div class="course-list-item__time" style="color: #777;"><?=Course::getBlockLessonName($lesson['block_id']);?></div>
                            <div class="course-list-item__time"><?php if(isset($lesson['duration'])) echo $lesson['duration'].' мин.'; if($task) echo ' '.TrainingLesson::getTaskTypeText($task['check_type'], $task['task_type']);?></div>
                        </div>
                        <a class="link-delete" onclick="return confirm('Вы уверены?')" href="<?=$setting['script_url'];?>/admin/training/dellesson/<?=$training_id;?>/<?=$lesson['lesson_id'];?>?token=<?=$_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a>
                    </div>
                </div>
            <?php endforeach;
        else:?>
            <div class="course-list-item">Нет уроков</div>
        <?php endif;?>

        <div class="add-lesson">
            <a class="button-green-rounding" href="/admin/training/addlesson/<?=$training_id;?>">+ Добавить урок</a>
        </div>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>