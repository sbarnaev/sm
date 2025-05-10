<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Импорт тренинга</h1>
        <div class="logout">
            <a href="/" target="_blank"><?=System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/training/">Тренинги</a></li>
        <li>Импорт тренинга</li>
    </ul>

    <?php if(Training::hasSuccess()) Training::showSuccess();?>
    <?php if(Training::hasError()) Training::showError();?>
       
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Импортировать тренинг</h3>
            <ul class="nav_button">
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/training/">Закрыть</a></li>
            </ul>
        </div>

        <div class="tabs">
            <ul>
                <li>Основное</li>
                <li>Миграция учеников</li>
            </ul>

        <div class="admin_form admin_form">
            <div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="overflow-container">
                        <p class="width-100"><label>Файл с тренингом</label>
                            <input required type="file" name="training_file">
                        </p>
                        <p class="width-100"><label>Файл с уроками</label>
                            <input required type="file" name="training_lessons_file">
                        </p>
                    </div>
                    <p>
                        <input type="submit" name="import_training" value="Импортировать" class="button-yellow-rounding">
                    </p>
                    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                </form>
            </div>
            <div>
                <div class="row-line">
                    <div class="col-1-1">
                    <form onsubmit="alert('Загрузка учеников обновляет данные полностью. Вы уверены ?');" action="" method="POST" enctype="multipart/form-data">
                        <h4>Перенос из тренингов 1.0  в 2.0</h4>
                        <p> <label>Откуда:</label>
                            <select name="from_course">
                                <?php $course_list = Course::getCourseList(0, 0);
                                if($course_list):
                                    foreach($course_list as $course):?>
                                        <option value="<?php echo $course['course_id'];?>"><?=$course['name'];?></option>
                                    <?php endforeach;
                                endif;?>
                            </select>
                        </p>
                        <p><label>Куда:</label>
                            <select name="to_training">
                            <?php $trainings_list = Training::getTrainingList(null, null, null, null);
                            if($trainings_list):
                                foreach($trainings_list as $training):?>
                                    <option value="<?php echo $training['training_id'];?>"><?=$training['name'];?></option>
                                <?php endforeach;
                            endif;?>
                            </select>
                        </p>
                        <p><input type="submit" name="transfer_user" value="Перенести" class="button-yellow-rounding"></p>
                        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                    </form>
                    </div>
                    <div class="col-1-1">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <h4>Импорт пользователей из файла</h4>
                            <p class="width-100"><label>Файл с учениками тренинга</label>
                                <input required type="file" name="training_users_file">
                            </p>
                            <p><label>Куда импортировать:</label>
                                <select name="to_training_from_file">
                                <?php if($trainings_list):
                                    foreach($trainings_list as $training):?>
                                        <option value="<?php echo $training['training_id'];?>"><?=$training['name'];?></option>
                                    <?php endforeach;
                                endif;?>
                                </select>
                            </p>
                            <p><input type="submit" name="import_user" value="Импортировать" class="button-yellow-rounding"></p>
                            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>"> 
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php require_once (ROOT . '/extensions/training/layouts/admin/admin-footer.php');?>
</div>
</body>
</html>