<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php');
$group_list = User::getUserGroups();?>

<div class="main">
    <div class="top-wrap">
    <h1>Создать урок для <?=$training['name'];?></h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/training/">Тренинги</a></li>
        <li><a href="/admin/training/structure/<?=$training_id;?>">Структура</a></li>
        <li>Создать урок</li>
    </ul>

    <form action="" method="POST" enctype="multipart/form-data">
        <?php if(isset($_GET['success'])):?>
            <div class="admin_message">Успешно!</div>
        <?php endif;?>

        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">

        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Создать урок</h3>
            <ul class="nav_button">
                <li><input type="submit" name="addlesson" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last">
                    <a class="button red-link" href="/admin/training/structure/<?=$training_id;?>">Закрыть</a>
                </li>
            </ul>
        </div>

        <div class="tabs">
            <ul>
                <li>Урок</li>
                <li>Задание</li>
                <li>Тесты</li>
                <li>Доступ</li>
                <li>Внешний вид</li>
                <li>SEO</li>
            </ul>

            <div class="admin_form">
                <!-- 1 вкладка Урок-->
                <div>
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Общие настройки</h4>
                        </div>

                        <div class="col-1-2">
                            <p class="width-100"><label>Название</label>
                                <input type="text" value="" name="name" placeholder="Название урока" required="required">
                            </p>

                            <div class="width-100" id="section_box"><label>Раздел</label>
                                <div class="select-wrap">
                                    <select name="section_id">
                                        <option value="0">Без раздела</option>
                                        <?php $sections = TrainingSection::getSections($training_id);
                                        if($sections):
                                            foreach($sections as $section):?>
                                                <option value="<?=$section['section_id']?>"><?=$section['name'];?></option>
                                            <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100"><label>Блок</label>
                                <div class="select-wrap">
                                    <select name="block_id">
                                        <option value="">Без блока</option>
                                        <?php $block_list = TrainingBlock::getBlocks($training_id, null, null);
                                        if($block_list):
                                            foreach($block_list as $block):?>
                                                <option value="<?=$block['block_id']?>" data-show_off="section_box"><?=$block['name'];?></option>
                                            <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Статус</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="status" type="radio" value="1" checked>
                                        <span>Вкл</span>
                                    </label>
                                    <label class="custom-radio"><input name="status" type="radio" value="0">
                                        <span>Выкл</span>
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- 2 ВКЛАДКА ЗАДАНИЕ -->
                <div>
                    <div class="row-line">
                        <div class="col-1-1">
                            <p>Настройки будут доступны после сохранения урока</p>
                        </div>
                    </div>
                </div>


                <!-- 3 ВКЛАДКА ТЕСТЫ-->
                <div>
                    <div class="row-line">
                        <div class="col-1-1">
                            <p>Настройки будут доступны после сохранения урока</p>
                        </div>
                    </div>
                </div>


                <!-- 4 ВКЛАДКА ДОСТУП -->
                <div>
                    <div class="row-line">
                        <div class="col-1-1">
                            <p>Настройки будут доступны после сохранения урока</p>
                        </div>
                    </div>
                </div>

                <!-- 5 ВКЛАДКА ВНЕШНИЙ ВИД -->
                <div>
                    <div class="row-line">
                        <div class="col-1-1">
                            <p>Настройки будут доступны после сохранения урока</p>          
                        </div>
                    </div>
                </div>

                <!-- 6 вкладка SEO  -->
                <div>
                    <div class="row-line">
                        <div class="col-1-1">
                            <p>Настройки будут доступны после сохранения урока</p>          
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="sort" form="options" value="<?=!empty($sort_arr) ? max($sort_arr) : 0;?>">
    </form>

    <form action="" id="options" method="POST">
        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    </form>

    <!--Элементы урока-->
    <?php require_once(__DIR__ . '/elements/index.php');

    require_once (ROOT . '/extensions/training/layouts/admin/admin-footer.php');?>
</div>
</body>
</html>