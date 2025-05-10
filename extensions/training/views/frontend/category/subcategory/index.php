<?php defined('BILLINGMASTER') or die;
$now = time();
require_once (ROOT . '/extensions/training/layouts/frontend/head.php');?>

<body id="page">
<?php require_once (ROOT . '/extensions/training/layouts/frontend/header.php');
require_once (ROOT . '/extensions/training/layouts/frontend/main_menu.php');?>

<style>
    .hero-wrap{
        min-height: 300px;
        height: <?=$this->tr_settings['heroheigh'];?>px;
        background-position: <?=$this->tr_settings['position']?>;
        background-size: cover;
    }
    .hero-wrap:before{
        opacity:<?=$this->tr_settings['overlay']?>;
        background:<?=$this->tr_settings['overlaycolor']?>;
    }

    .hero_header.h1 {color: <?=$this->tr_settings['color']?>; font-size: <?=$this->tr_settings['fontsize']?>px; }

    @media screen and (max-width: 640px),
    only screen and (max-device-width:640px) {
        .hero-wrap {height: <?=$this->tr_settings['heromobileheigh'];?>px}
        .hero_header.h1 {font-size: <?=$this->tr_settings['fontsize_mobile'];?>px}
    }
</style>

<div id="content">
    <?php if(isset($this->tr_settings['hero']) && $this->tr_settings['hero'] != null ):?>
        <div id="hero" class="hero-wrap  hero-text-center" style="background-image: url(<?=$this->tr_settings['hero'];?>)">
            <h1 class="layout hero_header h1"><?=$this->tr_settings['heroheader'];?></h1>
        </div>
    <?php endif;?>

    <div class="layout" id="courses">
        <ul class="breadcrumbs mb-0">
            <li><a href="/"><?=System::Lang('MAIN');?></a></li>
            <li><a href="/training/"><?=System::Lang('ONLINE_COURSES');?></a></li>
            <li><a href="/training/category/<?=$category['alias'];?>"><?=$category['name'];?></a></li>
            <li><?=$sub_category['name'];?></li>
        </ul>

        <div class="content-courses">
            <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                <?php if(!empty($h1)):?>
                    <h1><?=$h1;?></h1>
                <?php endif;?>

                <?php if ($training_list) { // вывод тренингов
                    require_once (__DIR__ . "/../../training/templates/list/{$this->tr_settings['template']}.php");
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