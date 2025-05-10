<?php defined('BILLINGMASTER') or die;
$now = time();
require_once (ROOT . '/extensions/training/layouts/frontend/head.php');?>

<body id="page">
<?php require_once (ROOT . '/extensions/training/layouts/frontend/header.php');
require_once (ROOT . '/extensions/training/layouts/frontend/main_menu.php');?>

<div id="content">
    <div class="layout" id="courses">
        <div class="content-courses">
            <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                <?php // Вывод промо кода
                require_once (ROOT . "/template/{$this->setting['template']}/views/common/show_promo_code.php");

                // Вывод уведомления CallPassword
                if(CallPassword::isShowButton($user)):
                    require_once (ROOT . '/extensions/callpassword/views/show_notice.php');
                endif;

                // Вывод уведомления Telegram
                if (Telegram::isShowButton($user['user_id'], $user['nick_telegram'])):
                    require_once(ROOT . '/extensions/telegram/views/show_notice.php');
                endif; ?>

                <h1><?=$this->h1;?></h1>

                <?php if(!empty($h2) || (isset($this->tr_settings['show_section_button']) && $this->tr_settings['show_section_button'])):?>
                    <div class="widget-top">
                        <?php if(!empty($h2)):?>
                            <h2><?=$h2;?></h2>
                        <?php endif;

                        if(isset($this->tr_settings['show_section_button']) && $this->tr_settings['show_section_button']):?>
                            <div class="z-1" style="text-align: right">
                                <a class="btn-yellow btn-orange" href="/training"><?=System::Lang('GO_TO_SECTION');?></a>
                            </div>
                        <?php endif;?>
                    </div>
                <?php endif;?>

                <?php // вывод тренингов
                require_once (__DIR__ . "/../training/templates/list/{$this->tr_settings['template']}.php");?>
            </div>
            <?php require_once (ROOT . '/template/'.$this->setting['template'].'/layouts/sidebar.php');?>
        </div>
    </div>
</div>

<?php require_once (ROOT . '/extensions/training/layouts/frontend/footer.php');
require_once (ROOT . '/extensions/training/layouts/frontend/tech-footer.php');?>
</body>
</html>