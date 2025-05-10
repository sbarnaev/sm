<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Тренинги 2.0</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a>
            <a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li>Тренинги 2.0</li>
    </ul>

    
    <?php if(Training::hasSuccess()): Training::showSuccess();
    elseif(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;
    if(isset($_GET['fail'])):?>
        <div class="admin_warning">Не удалось удалить, тренинг содержит уроки! <a onclick="return confirm('Вы уверены?')" href="/admin/training/delall/<?=$_SESSION['del_trainig_id'];?>?token=<?=$_SESSION['admin_token'];?>">Удалить включая все уроки ?</a></div>
    <?php endif;?>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">

            <li class="nav_gorizontal__parent-wrap">
                <div class="nav_gorizontal__parent nav_gorizontal__parent-red">
                    <a href="/admin/training/add/" class="button-red-rounding"><?=System::Lang('CREATE_COURSE');?></a>
                    <span class="nav-click icon-arrow-down nav-click"></span>
                </div>

                <ul class="drop_down">
                    <li><a href="<?=$setting['script_url']?>/admin/training/addcat/">Создать категорию</a></li>
                    <li><a href="<?=$setting['script_url']?>/admin/training/cats/">Список категорий</a></li>
                    <li><a href="<?=$setting['script_url']?>/admin/training/import/">Импорт тренинга</a></li>
                </ul>
            </li>

            <li><a class="button-yellow-rounding" href="/admin/training/answers">Проверить задания</a></li>
            <li><a title="Общие настройки тренингов" class="settings-link" target="_blank" href="/admin/trainingsetting/"><i class="icon-settings-bold"></i></a></li>
        </ul>
    </div>

    <div class="filter admin_form">
        <form action="" method="POST">
            <div class="filter-row">
                <div class="max-width-147">
                    <div class="select-wrap">
                        <select name="cat_id">
                            <option value="">Категория</option>
                            <?php $cat_list = TrainingCategory::getCatList(false);
                            if($cat_list):
                                foreach($cat_list as $cat):?>
                                    <option value="<?=$cat['cat_id']?>" <?php if(isset($filter) && $filter['category'][0] == $cat['cat_id']) echo 'selected="selected"'; ?>><?=$cat['name']?></option>
                                    <?php $subcategories = TrainingCategory::getSubCategories($cat['cat_id'], null);
                                    if($subcategories):
                                        foreach ($subcategories as $subcategory):?>
                                            <option value="<?=$subcategory['cat_id']?>" <?php if(isset($filter) && $filter['category'][0] == $subcategory['cat_id']) echo 'selected="selected"'; ?>> - <?=$subcategory['name']?></option>
                                        <?php endforeach;
                                    endif;
                                endforeach;
                            endif;?>
                        </select>
                    </div>
                </div>
                
                <div class="max-width-120 mr-auto">
                    <div class="select-wrap">
                        <select name="status">
                            <option value="all"<?php if($status === null) echo 'selected="selected"';?>>Статус</option>
                            <option value="1"<?php if($status == 1) echo ' selected="selected"';?> >Включен</option>
                            <option value="0"<?php if($status === 0) echo ' selected="selected"';?>>Отключен</option>
                        </select>
                    </div>
                </div>

                <div>
                    <div class="button-group">
                        <input class="button-blue-rounding" type="submit" name="filter" value="Найти">
                        <?php if(isset($filter)):?>
                            <a class="red-link" href="/admin/training">Сбросить</a>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <div class="course-list sortable sortable_box">
        <input type="hidden" name="sort_upd_url" value="/admin/trainingajax/updsortrainings">
        <?php if($trainings_list):
            foreach($trainings_list as $training):?>
                <div class="course-list-item d-flex">
                    <input type="hidden" name="sort_items[]" value="<?=$training['training_id'];?>" data-type="training">

                    <div class="button-drag course-list-item__left">
                        <img <?php if($training['status'] == 0) echo 'style="opacity: 0.5;"';?>src="/template/admin/images/icons/training-icon.svg" alt="">
                    </div>
    
                    <div class="course-list-item__center">
                        <h4 class="course-list-item__name">
                            <a href="/admin/training/edit/<?=$training['training_id'];?>"><?=$training['name'];?></a>
                        </h4>
    
                        <div class="course-list-item__data">
                            <?php if(!empty($training['authors'])):
                                $user_data = User::getUserNameByListID($training['authors']);?>
                                <div class="course-list-item__author"><i class="icon-user"></i>
                                <?php 
                                $i = 1;
                                foreach($user_data as $user):
                                    if($i>1):?><?=','?><?php endif;?>
                                    <?php echo $user['user_name'].' '.$user['surname'];?>
                                    <?php $i++;
                                 endforeach;?>
                                </div>
                            <?php endif;
                            if($training['cat_id'] != 0):?>
                                <div><i class="icon-list"></i>
                                    <?php $cat_name = TrainingCategory::getCategory($training['cat_id']);
                                    if(!empty($cat_name)) echo $cat_name['name'];?>
                                </div>
                            <?php else:?>
                                <div><i class="icon-list"></i>Без категории</div>
                            <?php endif;?>
                            
                            <?php /* TODO Удалить после релиза
                            if($training['auto_train'] == 1):?>
                                <div><i class="icon-arrow-auto"></i>Авто-тренинг</div>
                            <?php endif;*/ ?>
                        </div>
    
                        <p class="course-list-item__descr">
                            <a href="/admin/training/structure/<?=$training['training_id'];?>">Структура</a>&nbsp; &nbsp; &nbsp;
                            <a href="/admin/training/statistics/<?=$training['training_id'];?>">Статистика</a>&nbsp; &nbsp; &nbsp;
                            <a class="disabled" title="Будет доступно в следующей версии">Расписание</a>
                        </p>
                    </div>
    
                    <div class="course-list-item__right">
                        <form onsubmit="alert('Скопировать тренинг. Вы уверены ?');" action="" method="POST">
                            <input type="hidden" name="training_id" value="<?=$training['training_id'];?>">
                            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                            <button title="Копировать" class="button-copy" type="submit" name="copy">
                                <i class="icon-copy"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach;
        else:?>
            <div class="course-list-item">Вы пока не добавили ни одного тренинга</div>
        <?php endif;?>
    </div>
    <?php require_once (ROOT . '/extensions/training/layouts/admin/admin-footer.php');?>
</div>
</body>
</html>