<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/frontend/head.php');?>

<body id="page">
    <?php require_once (ROOT . '/extensions/training/layouts/frontend/header.php');
    require_once (ROOT . '/extensions/training/layouts/frontend/main_menu.php');
    require_once (ROOT . '/extensions/training/web/frontend/style/training.php');?>

    <div id="content"> 
        <?php if($training['full_cover']):?>
            <div id="hero" class="hero-wrap" style="background-image: url(/images/training/<?=$training['full_cover']?>)">
                <h1><?=$training['name'];?></h1>
                <ul class="breadcrumbs">
                    <?php $breadcrumbs = Training::getBreadcrumbs($this->tr_settings, $category, $sub_category, $training);
                    foreach ($breadcrumbs as $link => $name):?>
                        <li><?=$link ? "<a href=\"$link\">$name</a>" : $name;?></li>
                    <?php endforeach;?>
                </ul>
            </div>
        <?php endif;?>

        <div class="layout" id="courses">
            <?php if(!$training['full_cover']):?>
                <ul class="breadcrumbs">
                    <?php $breadcrumbs = Training::getBreadcrumbs($this->tr_settings, $category, $sub_category, $training);
                    foreach ($breadcrumbs as $link => $name):?>
                        <li><?=$link ? "<a href=\"$link\">$name</a>" : $name;?></li>
                    <?php endforeach;?>
                </ul>
            <?php endif;?>

            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min';?> content-with-sidebar">
                    <div class="content-top one-course-top">
                        <?php if (empty($training['full_cover'])):?>
                        <h1><?=$training['name'];?></h1>
                        <?php endif;?>
                        <span class="z-1"><a class="btn-orange" href="/feedback"><?=System::Lang('WRITE_REVIEW');?></a></span>

                        <div class="one-course-desk">
                            <?=html_entity_decode($training['full_desc']);?>
                        </div>
                    </div>

                    <?php if ($section_list) {
                        require(__DIR__ . '/../section/list.php');
                    }

                    if ($block_list) {
                        require(__DIR__ . '/../block/list.php');
                    }

                    if ($lesson_list) {
                        require(__DIR__ . '/../lesson/list.php');
                    }?>
                </div>

                <aside class="sidebar">
                    <?php if($user_id && $training['show_widget_progress']):?>
                        <section class="widget _instruction traning-widget">
                            <?php if ($training['cover'] && !$training['full_cover']):?>
                                <div class="sidebar-image">
                                    <img src="/images/training/<?=$training['cover']?>">
                                </div>

                                <h4 class="traninig-name"><?=$name?></h4>
                            <?php endif;?>

                            <h3><?=System::Lang('YOUR_PROGRESS');?></h3>
                            <p class="progress-text"><?=System::Lang('TRACK_YOUR_TRAINING');?></p>

                            <?php require_once (__DIR__ . '/../layouts/progressbar.php');?>
                        </section>
                    <?php else:?>
                        <section class="widget _instruction traning-widget">
                            <?php if($training['cover'] && !$training['full_cover']):?>
                                <div>
                                    <div class="sidebar-image">
                                        <img src="/images/training/<?=$training['cover']?>">
                                    </div>
                                </div>
                                <h4 class="traninig-name"><?=$name?></h4>
                            <?php endif;?>
                                <h3><?=System::Lang('YOUR_PROGRESS');?></h3>
                                <p><?=System::Lang('PROGRESS_OF_THE_TRAINING_WILL_BE_DISPLAYED_HERE');?></p>
                        </section>
                    <?php endif;
                    if($have_certificate):?>
                        <section class="widget traning-widget">
                            <?php if(!empty($sertificate['header'])):?>
                                <h3 class="elephant-title"><i class="icon-sertifikat"></i><?=$sertificate['header'];?></h3>
                            <?php endif;?>
                                <a target="_blank" href="<?=$setting['script_url'];?>/training/showcertificate/<?=$have_certificate['url'];?>"> 
                            <img src="<?=$setting['script_url'];?>/training/showcertificate/<?=$have_certificate['url'];?>" alt=""></a>
                            <p class="text-center">
                                <a href="<?=$setting['script_url'];?>/training/showcertificate/<?=$have_certificate['url'];?>" class="btn-green" download>Скачать</a>
                            </p>
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
                        require(ROOT . "/template/{$this->setting['template']}/widgets/widget_wrapper.php");
                    endif;?> 
                </aside>   
            </div><hr>
        </div>
    </div>

    <?require_once (ROOT . '/extensions/training/layouts/frontend/footer.php');
    require_once (ROOT . '/extensions/training/layouts/frontend/tech-footer.php');?>
</body>
</html>