<?php defined('BILLINGMASTER') or die;
$use_css = 1;
$title = 'Нет доступа';

$texts = [
    'training' => [
        'Тренинг начнется ', 'тренингу'
    ],
    'section' => [
        'Раздел откроется ', 'раделу'
    ],
    'lesson' => [
        'Урок откроется ', 'уроку'
    ]
];

if (($access['status'] == Training::NO_ACCESS_TO_DATE) || ($access['status'] == TrainingLesson::STATUS_LESSON_NOT_YET)) {
    $h3 = $texts[$this->is_page][0] . System::dateSpeller($access['start_date']);
} else {
    $h3 = 'К сожалению у вас пока нет доступа к этому ' . $texts[$this->is_page][1];
}

require_once (ROOT . '/extensions/training/layouts/frontend/head.php');?>

<body id="page">
    <?php require_once (ROOT . '/extensions/training/layouts/frontend/header.php');
    require_once (ROOT . '/extensions/training/layouts/frontend/main_menu.php');?>
    
    <div id="content">
        <div class="layout" id="no_access">
            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min';?> content-with-sidebar">
                    <h1><?=$this->h1;?></h1>
                    <h3><?=$h3;?></h3>

                    <?php if(($access['status'] != Training::NO_ACCESS_TO_DATE) && ($access['status'] != TrainingLesson::STATUS_LESSON_NOT_YET)):?>
                        <?php if(!$user_id):?>
                            <p><?=System::Lang('LOGIN_FAULT');?> <a href="#modal-login" data-uk-modal="{center:true}"><?=System::Lang('SITE_LOGIN');?></a>.</p>
                        <?php else:
                            $section = $this->is_page == 'section' || $this->is_page == 'lesson' ? $section : null;
                            $lesson = $this->is_page == 'lesson' ? $lesson : null;
                            $buttons = Training::renderByButtons(false, $training, $section, $lesson);

                            if ($buttons['big_button'] || $buttons['small_button']):?>
                                <div class="by_buttons-wrap">
                                    <?php if($buttons['big_button']):?>
                                        <div class="z-1 by_button">
                                            <a class="<?=Training::getCssClasses($this->setting, $buttons['big_button']['class-type']);?>" href="<?=$buttons['big_button']['url'];?>"><?=$buttons['big_button']['text'];?></a>
                                        </div>
                                    <?php endif;

                                    if($buttons['small_button']):?>
                                        <a class="<?=Training::getCssClasses($this->setting, $buttons['small_button']['class-type']);?>" href="<?=$buttons['small_button']['url'];?>"><?=$buttons['small_button']['text'];?></a>
                                    <?php endif;?>
                                </div>
                            <?php endif;
                        endif;
                    endif;?>
                </div>
                <?php require_once (ROOT . '/template/'.$this->setting['template'].'/layouts/sidebar.php');?>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/extensions/training/layouts/frontend/footer.php');
    require_once (ROOT . '/extensions/training/layouts/frontend/tech-footer.php');?>
</body>
</html>
<?php exit;?>