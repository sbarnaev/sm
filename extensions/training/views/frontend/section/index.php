<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/frontend/head.php');?>

<body id="page">
    <?php require_once (ROOT . '/extensions/training/layouts/frontend/header.php');
    require_once (ROOT . '/extensions/training/layouts/frontend/main_menu.php');
    require_once (ROOT . '/extensions/training/web/frontend/style/section.php');?>

    <div id="content">
        <?php if($training['full_cover']):?><!-- большая картинка с оверлеем если есть  -->
            <div id="hero" class="hero-wrap" style="background-image: url(/images/training/<?=$training['full_cover']?>)">
                <h1><?=$training['name'];?></h1>
                <ul class="breadcrumbs">
                    <?php $breadcrumbs = Training::getBreadcrumbs($this->tr_settings, $category, $sub_category, $training, $section);
                    foreach ($breadcrumbs as $link => $name):?>
                        <li><?=$link ? "<a href=\"$link\">$name</a>" : $name;?></li>
                    <?php endforeach;?>
                </ul>
            </div>
        <?php endif;?>

        <div class="layout" id="courses">
            <?php if(!$training['full_cover']):?><!-- А тут нет большой обложки(картинки) -->
                <h1><?=$training['name'];?></h1>

                <ul class="breadcrumbs">
                    <?php $breadcrumbs = Training::getBreadcrumbs($this->tr_settings, $category, $sub_category, $training, $section);
                    foreach ($breadcrumbs as $link => $name):?>
                        <li><?=$link ? "<a href=\"$link\">$name</a>" : $name;?></li>
                    <?php endforeach;?>
                </ul>
            <?php endif;?>

            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min';?> content-with-sidebar">
                    <div class="content-top">
                        <h2><?=System::Lang('CONTENT');?></h2>
                        <span class="z-1"><a class="btn-orange" href="/feedback"><?=System::Lang('WRITE_REVIEW');?></a></span>
                    </div>

                    <div class="one-course-top">
                        <?=html_entity_decode($section['section_desc']);?>
                    </div>

                    <?php if($block_list) {
                        require(__DIR__ . '/../block/list.php');
                    }

                    if ($lesson_list) {
                        require(__DIR__ . '/../lesson/list.php');
                    }?>
                </div>

                <aside class="sidebar">
                    <?php if($user_id && $training['show_widget_progress']):?>
                        <section class="widget _instruction">
                            <?php if ($training['cover'] && !$training['full_cover']):?>
                            <div class="sidebar-image"><img src="/images/training/<?=$training['cover']?>"></div>
                            <!-- ЗДЕСЬ выводим название тренинга, если обложка маленькая -->
                            <h4 class="traninig-name"><?=$name?></h4>
                            <?php endif;?>

                            <h3><?=System::Lang('YOUR_PROGRESS');?></h3>
                            <p class="progress-text"><?=System::Lang('TRACK_YOUR_TRAINING');?></p>

                            <?php require_once (__DIR__ . '/../layouts/progressbar.php');?>
                        </section>
                    <?php else:?>
                        <section class="widget _instruction">
                            <?php if ($training['cover'] && !$training['full_cover']):?>
                                <div><div class="sidebar-image"><img src="/images/training/<?=$training['cover']?>"></div></div>
                                <!-- ЗДЕСЬ выводим название тренинга, если обложка маленькая -->
                                <h4 class="traninig-name"><?=$name?></h4>
                            <?php endif;?>
                            <h3><?=System::Lang('YOUR_PROGRESS');?></h3>
                            <p><?=System::Lang('PROGRESS_OF_THE_TRAINING_WILL_BE_DISPLAYED_HERE');?></p>
                        </section>
                    <?php endif;

                    if($user_is_curator):?>
                        <section class="widget elephant-widget">
                            <h3 class="elephant-title"><i class="icon-elephant"></i><?=System::Lang('LOGIN_AS_CURATOR');?></h3>
                            <p><?=System::Lang('ALL_LESSONS_AVAILABLE');?></p>
                        </section>
                    <?php endif;

                    if($sidebar):
                        $widget_arr = $sidebar;
                        require(ROOT . '/template/'.$this->setting['template'].'/widgets/widget_wrapper.php');
                    endif;?>
                </aside>
            </div>
        </div>
    </div>

    <?php require_once (ROOT . '/extensions/training/layouts/frontend/footer.php');
    require_once (ROOT . '/extensions/training/layouts/frontend/tech-footer.php');?>
</body>
</html>