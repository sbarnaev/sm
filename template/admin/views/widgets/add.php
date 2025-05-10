<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1><?=System::Lang('ADD_WIDGET');?> <?=$type;?></h1>
        <div class="logout">
            <a href="/" target="_blank"><?=System::Lang('GO_SITE');?></a>
            <a href="<?=$setting['script_url'];?>/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/widgets/">Список виджетов</a></li>
        <li><?=System::Lang('ADD_WIDGET');?> <?=$type;?></li>
    </ul>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title"><?=System::Lang('ADD_WIDGET');?> <?=$type;?></h3>
            <ul class="nav_button">
                <li><input type="submit" name="addwidget" value="<?=System::Lang('SAVE');?>" class="button save button-white font-bold"></li>
                <li class="nav_button__last">
                    <a class="button red-link" href="/admin/widgets/"><?=System::Lang('CLOSE');?></a>
                </li>
            </ul>
        </div>

        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-1 mb-0">
                    <h4><?=System::Lang('BASIC');?></h4>
                </div>

                <div class="col-1-2">
                    <p class="width-100"><label><?=System::Lang('WIDGET_TITLE');?></label>
                        <input type="text" name="title" value="" placeholder="<?=System::Lang('WIDGET_TITLE');?>" required="required">
                    </p>

                    <div class="width-100"><label><?=System::Lang('STATUS');?></label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio">
                                <input name="status" type="radio" value="1" checked=""><span>Вкл</span>
                            </label>
                            <label class="custom-radio">
                                <input name="status" type="radio" value="0"><span>Откл</span>
                            </label>
                        </span>
                    </div>

                    <p class="width-100"><label><?=System::Lang('SORT');?></label>
                        <input type="text" size="3" value="" name="sort">
                    </p>

                    <?php $xml = simplexml_load_file(ROOT . '/template/'. $setting['template'].'/'.$setting['template'].'.xml');?>
                    <div class="width-100"><label>Позиция вывода</label>
                        <div class="select-wrap">
                            <select name="position">
                                <?php foreach($xml->positions->position as $position):?>
                                    <option value="<?=$position;?>"><?=$position;?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>

                    <div class="width-100"><label>Кто будет видеть</label>
                        <div class="select-wrap">
                            <select name="private">
                                <option value="0">Все пользователи</option>
                                <option value="1">Только зарегистрированные пользователи</option>
                            </select>
                        </div>
                    </div>

                    <p class="width-100"><label>CSS класс</label>
                        <input type="text" name="suffix" value="" placeholder="suffix">
                    </p>

                    <p class="width-100"><label>Заметки</label>
                        <textarea name="desc" rows="3" cols="40"></textarea>
                    </p>
                </div>

                <div class="col-1-2">
                    <div class="width-100"><label>Показывать на страницах</label>
                        <select class="multiple-select" name="page[]" multiple="multiple" size="4" required="required">
                            <option value="main"><?=System::Lang('MAIN_PAGE');?></option>
                            <option value="catalog"><?=System::Lang('CATALOG');?></option>
                            <option value="courses_index"><?=System::Lang('COURSES');?> главная</option>
                            <option value="courses">Мои курсы</option>
                            <option value="lessons_list"><?=System::Lang('LESSONS_LIST');?></option>
                            <option value="lesson_page"><?=System::Lang('LESSON_PAGE');?></option>
                            <option value="forum"><?=System::Lang('FORUM');?> главная</option>
                            <option value="forum-category"><?=System::Lang('FORUM');?> категории</option>
                            <option value="forum-branch"><?=System::Lang('FORUM');?> ветки</option>
                            <option value="forum-topic"><?=System::Lang('FORUM');?> темы</option>
                            
                            <option value="feedback"><?=System::Lang('ASK_QUESTION');?></option>
                            <option value="reviews"><?=System::Lang('REVIEWS');?></option>
                            <option value="lk"><?=System::Lang('LK');?></option>
                            <option value="aff"><?=System::Lang('PARTNERSHIP');?></option>
                            <option value="blog"><?=System::Lang('BLOG');?></option>
                            <option value="gallery"><?=System::Lang('GALLERY');?></option>
                            <option value="static"><?=System::Lang('STATIC_PAGES');?></option>
                            <option value="order">Страница заказа</option>
                            <option value="viewproduct"><?=System::Lang('VIEW_PRODUCT');?></option>
                            
                            <option value="training_index">Тренинги 2.0 главная</option>
                            <option value="training">Тренинги 2.0 тренинг</option>
                            <option value="lesson">Тренинги 2.0 уроки</option>
                            <option value="my_trainings">Тренинги 2.0 мои тренинги</option>
                        </select>
                    </div>
                    
                    <?php if($en_courses):?>
                    <div class="width-100"><label>Показывать на курсах</label>
                        <select class="multiple-select" name="show_for_course[]" multiple="multiple" size="4">
                            <?php $course_list = Course::getCourseList(0, 0);
                            if($course_list):
                                foreach($course_list as $course):?>
                                    <option value="<?=$course['course_id'];?>"><?=$course['name'];?></option>
                                <?php endforeach;
                            endif;?>
                        </select>
                    </div>
                    <?php endif;?>
                    
                    <?php if($en_training):?>
                    <div class="width-100"><label>Показывать на тренингах</label>
                        <select class="multiple-select" name="show_for_training[]" multiple="multiple" size="4">
                            <?php $trainings_list = Training::getTrainingList();
                            if($trainings_list):
                                foreach($trainings_list as $training):?>
                                    <option value="<?=$training['training_id'];?>"><?=$training['name'];?></option>
                                <?php endforeach;
                            endif;?>
                        </select>
                    </div>
                    <?php endif;?>

                    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                    <input type="hidden" name="type" value="<?=$type;?>">
                </div>
            </div>

            <div class="row-line">
                <div class="col-1-1 mb-0">
                    <h4>Настройки заголовоков</h4>
                </div>

                <div class="col-1-2">
                    <div class="width-100"><label>Показать Заголовок</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="show_header" type="radio" value="1"><span>Вкл</span></label>
                            <label class="custom-radio"><input name="show_header" type="radio" value="0" checked=""><span>Откл</span></label>
                        </span>
                    </div>

                    <div class="width-100"><label>Заголовок</label>
                        <input type="text" name="header" placeholder="Заголовок" value="">
                    </div>

                    <div class="width-100"><label>Показать подзаголовок</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="show_subheader" type="radio" value="1"><span>Вкл</span></label>
                            <label class="custom-radio"><input name="show_subheader" type="radio" value="0" checked=""><span>Откл</span></label>
                        </span>
                    </div>

                    <div class="width-100"><label>Подзаголовок</label>
                        <input type="text" name="subheader" placeholder="Подзаголовок" value="">
                    </div>
                </div>

                <div class="col-1-2">
                    <div class="width-100">
                        <label>Показать кнопку</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio">
                                <input name="show_right_button" type="radio" value="1"><span>Да</span>
                            </label>
                            <label class="custom-radio">
                                <input name="show_right_button" type="radio" value="0" checked=""><span>Нет</span>
                            </label>
                        </span>
                    </div>

                    <div class="width-100"><label>Название кнопки</label>
                        <input type="text" name="right_button_name" placeholder="Ссылка кнопки" value="">
                    </div>

                    <div class="width-100"><label>Ссылка кнопки</label>
                        <input type="text" name="right_button_link" placeholder="Ссылка кнопки" value="">
                    </div>
                </div>
            </div>

            <?php require_once(ROOT . "/template/admin/views/widgets/types/$type/add.php");?>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>