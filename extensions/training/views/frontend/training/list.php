<?php defined('BILLINGMASTER') or die;
$now = time();
require_once (ROOT . '/extensions/training/layouts/frontend/head.php');?>

<body id="page">
    <?php require_once (ROOT . '/extensions/training/layouts/frontend/header.php');
    require_once (ROOT . '/extensions/training/layouts/frontend/main_menu.php');
    require_once (ROOT . '/extensions/training/web/frontend/style/training-list.php');?>

    <div id="content">
        <?php if(isset($this->tr_settings['hero']) && $this->tr_settings['hero'] != null ):?>
            <div id="hero" class="hero-wrap  hero-text-center" style="background-image: url(<?=$this->tr_settings['hero'];?>)">
                <h1 class="layout hero_header h1"><?=$this->tr_settings['heroheader'];?></h1>
            </div>
        <?php endif;?>

        <div class="layout" id="courses">
            <div class="content-courses">
                <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                    <?php if(!empty($h1)):?>
                        <h1><?=$h1;?></h1>
                    <?php endif;?>

                    <?php if(!empty($h2) || (isset($this->tr_settings['show_section_button']) && $this->tr_settings['show_section_button'])):?>
                        <div class="widget-top">
                            <?php if(!empty($h2)):?>
                                <h2><?=$h2;?></h2>
                            <?php endif;?>

                            <?php if(isset($this->tr_settings['show_section_button']) && $this->tr_settings['show_section_button']):?>
                                <div class="z-1" style="text-align: right">
                                    <a class="btn-yellow btn-orange" href="/training"><?=System::Lang('GO_TO_SECTION');?></a>
                                </div>
                            <?php endif;?>
                        </div>
                    <?php endif;?>

                    <?php // вывод категорий
                    if ($cat_list) {
                        require_once (__DIR__ . "/../category/templates/list/{$this->tr_settings['template']}.php");
                    } else {
                        require_once (__DIR__ . "/../training/templates/list/{$this->tr_settings['template']}.php");
                    }?>
                </div>
                <?php require_once (ROOT . '/template/'.$this->setting['template'].'/layouts/sidebar.php');?>
            </div>
        </div>
    </div>

    <?php require_once (ROOT . '/extensions/training/layouts/frontend/footer.php');
    require_once (ROOT . '/extensions/training/layouts/frontend/tech-footer.php');?>
</body>
</html>