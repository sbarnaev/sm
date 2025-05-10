<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php');?>

<div class="main">
    <div class="top-wrap">
        <h1>Структура тренинга</h1>
        <div class="logout">
            <a href="/" target="_blank"><?=System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>
    
    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/training/">Тренинги</a></li>
        <li><a href="/admin/training/structure/<?=$training_id;?>">Структура «<?=$training['name'];?>»</a></li>
    </ul>

    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;?>

    <?php if(Training::hasError()) Training::showError();?>

    <div class="admin_top admin_top-flex align-center flex-nowrap">
        <div class="admin_top-inner">
            <div>
                <img src="/extensions/training/web/admin/images/icons/training-2.svg" alt="">
            </div>

            <div>
                <h3 class="mb-0"><span class="traning-name-text"><?=$training['name'];?></span>
                    <a class="traning-name-link" href="/training/view/<?=$training['alias'];?>" target="_blank"><i class="icon-exit-top-right"></i></a>
                </h3>
                <a href="/admin/training/edit/<?=$training['training_id'];?>" class="edit-lesson"><i class="icon-pencil"></i> <span>Настройки тренинга</span></a>
            </div>
        </div>

        <ul class="nav_button">
            <li class="nav_button__last"><a class="button red-link" href="/admin/training/">Закрыть</a></li>
        </ul>
    </div>

    <div class="cource-list">
        <ul class="structure-nav">
            <li><a class="button-green" href="/admin/training/addsection/<?=$training_id;?>"><i class="icon-folder"></i>Добавить раздел</a></li>
            <li><a class="button-green" href="/admin/training/addblock/<?=$training_id;?>"><i class="icon-menu-buger"></i>Добавить блок</a></li>
            <li><a href="/admin/training/addlesson/<?=$training_id;?>" class="button-green"><i class="icon-player"></i>Добавить урок</a></li>
        </ul>

        <div class="filter"></div>

        <div>
            <h4>Структура тренинга</h4>
            <div class="overflow-container">
                <div class="structure_items sortable_box">
                    <input type="hidden" name="sort_upd_url" value="/admin/trainingajax/updsortstructure">

                    <?php if($sections):?>
                        <div class="section-list sortable">
                            <?php foreach ($sections as $section):
                                require(__DIR__ . '/section.php');
                            endforeach;?>
                        </div>
                    <?php endif;?>

                    <?php $blocks = TrainingBlock::getBlocks($training_id, 0, null);
                    if($blocks):?>
                        <div class="block-list sortable">
                            <?php foreach($blocks as $block):
                                require(__DIR__ . '/block.php');
                            endforeach;?>
                        </div>
                    <?php endif;

                    $lessons = TrainingLesson::getLessons($training_id, 0, 0, null);
                    if($lessons):?>
                        <div class="lesson-list sortable">
                            <?php foreach ($lessons as $lesson):
                                require(__DIR__ . '/lesson.php');
                            endforeach;?>
                        </div>
                    <?php endif;?>
                </div>

                <?php if (!$sections && !$blocks && !$lessons):?>
                    <div>Нет элементов</div>
                <?php endif;?>
            </div>
        </div>
    </div>

    <div class="buttons-under-form">
        <p class="button-delete">
            <a onclick="return confirm('Вы уверены?')" href="/admin/training/del/<?=$training_id;?>?token=<?=$_SESSION['admin_token'];?>" title="Удалить"><i class="icon-remove"></i>Удалить тренинг</a>
        </p>
    </div>

    <?php require_once (ROOT . '/extensions/training/layouts/admin/admin-footer.php');?>
</div>
</body>
</html>