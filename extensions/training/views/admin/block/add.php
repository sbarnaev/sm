<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Создать блок для <?=$training['name'];?></h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a>
            <a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/training/">Тренинги</a></li>
        <li><a href="/admin/training/structure/<?=$training_id;?>">Структура</a></li>
        <li>Создать блок</li>
    </ul>

    <form action="" method="POST" enctype="multipart/form-data">

        <div class="admin_top admin_top-flex align-center">
            <div class="admin_top-inner">
                <div>
                    <img src="/extensions/training/web/admin/images/icons/block.svg" alt="">
                </div>
                <div style="position: relative; top: -3px;">
                    <h3 class="mb-0">Создать блок</h3>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="add_block" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last">
                    <a class="button red-link" href="/admin/training/structure/<?=$training['training_id']?>">Закрыть</a>
                </li>
            </ul>
        </div>
        
        <div class="admin_form">
            <!-- 1 вкладка -->

            <div class="row-line">
                <div class="col-1-1">
                    <h4 class="mb-0">Основное</h4>
                </div>
                <div class="col-1-2">
                    <p class="width-100"><label>Название</label>
                        <input type="text" name="name" placeholder="Название блока" required="required">
                    </p>
                    
                    <div class="width-100"><label>Тренинг</label>
                        <div class="select-wrap">
                            <select name="training_id">
                                <?php if($trainings_list):
                                    foreach($trainings_list as $training):?>
                                        <option value="<?=$training['training_id']?>"<?php if($training['training_id'] == $training_id) echo ' selected="selected"';?>><?=$training['name'];?></option>
                                    <?php endforeach;
                                endif;?>
                            </select>
                        </div>
                    </div>

                    <div class="width-100"><label>Раздел</label>
                        <div class="select-wrap">
                            <select name="section_id">
                                <option value="0">Не выбрано</option>
                                <?php if($sections):
                                    foreach($sections as $section):?>
                                        <option value="<?=$section['section_id']?>"><?=$section['name'];?></option>
                                    <?php endforeach;
                                endif;?>
                            </select>
                        </div>
                    </div>
                    
                    <p class="width-100"><label>Порядок</label>
                        <input type="text" name="sort" placeholder="Номер блока" required="required" value="<?=TrainingBlock::getFreeSort($training_id);?>">
                    </p>
                    
                    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                </div>
            </div>
        </div>
    </form>

    <?php require_once (ROOT . '/extensions/training/layouts/admin/admin-footer.php');?>
</div>
</body>
</html>