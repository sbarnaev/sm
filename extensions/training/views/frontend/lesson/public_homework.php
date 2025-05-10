<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/frontend/head.php');?>

<body id="page">
<?php require_once (ROOT . '/extensions/training/layouts/frontend/header.php');
require_once (ROOT . '/extensions/training/layouts/frontend/main_menu.php');?>
<script src="/template/<?=$this->setting['template'];?>/js/audio_play.js" type="text/javascript"></script>
<script src="/template/<?=$this->setting['template'];?>/js/player_bm.js" type="text/javascript"></script>

<div id="content">
    <div class="layout" id="courses">
        <div class="h1"><?=System::Lang('GAMEDEV');?></div>
        <ul class="breadcrumbs">
            <?php $breadcrumbs = Training::getBreadcrumbs($this->tr_settings, $category, $sub_category, $training, $section, $lesson);
            foreach ($breadcrumbs as $link => $name):?>
                <li><?=$link ? "<a href=\"$link\">$name</a>" : $name;?></li>
            <?php endforeach;?>
        </ul>

        <?php // Ссылка на след. урок для автотренингов
        $prev = $lesson['sort'] > 1 ? TrainingLesson::getLessonBySort($lesson['training_id'], $lesson['sort']-1) : null;
        $prev_link = $prev ? "/training/view/{$training['alias']}/lesson/{$prev['alias']}" : false;
        $next = TrainingLesson::getLessonBySort($lesson['training_id'], $lesson['sort'] + 1);
        $next_link = $next ? "/training/view/{$training['alias']}/lesson/{$next['alias']}" : false; ?>

        <div class="content-wrap">
            <div class="maincol<?php if($sidebar) echo '_min';?> content-with-sidebar">
                <div class="lesson-inner">
                    <div class="lesson-inner-top">
                        <h1 class="lesson-inner-h1 h2"><?=$lesson['name'];?></h1>
                    </div>

                    <?php if($playlist) {
                        require_once(__DIR__ . '/elements/playlist.php');
                    }

                    $elements = TrainingLesson::getElements2Lesson($lesson['lesson_id'], null, TrainingLesson::ELEMENT_TYPE_PLAYLIST);
                    if ($elements):
                        $prev_el = null;
                        foreach ($elements as $key => $element):?>
                            <?php if ($element['type'] == TrainingLesson::ELEMENT_TYPE_ATTACH && (!$prev_el || $prev_el != TrainingLesson::ELEMENT_TYPE_ATTACH)):?>
                                <div class="block-border-top">
                                <div class="group-files">
                            <?php endif;

                            if($element['type'] == TrainingLesson::ELEMENT_TYPE_ATTACH):
                                $link = $element['params']['type'] == 1 ? "/load/training/lessons/{$element['lesson_id']}/{$element['params']['attach']}" : $element['params']['link'];?>
                                <a href="<?=$link;?>" class="one-files<?php if(!$element['params']['line_up']) echo ' one-files__width-100';?>" <?=$element['params']['type'] == 1 ? 'download' : 'target="_blank"';?>>
                                    <div class="one-files-title"><?=$element['params']['name'];?></div>
                                    <div class="one-files-image"><img width="28" src="<?=$element['params']['cover'];?>" alt=""></div>
                                </a>
                            <?php elseif($element['type'] == TrainingLesson::ELEMENT_TYPE_HTML):
                                echo $element['params']['html'];
                            elseif($element['type'] == TrainingLesson::ELEMENT_TYPE_TEXT):?>
                                <div><?=$element['params']['text'];?></div>
                            <?php elseif($element['type'] == TrainingLesson::ELEMENT_TYPE_MEDIA):?>
                                <div class="block-border-top">
                                    <?php require(__DIR__ . '/elements/media_item.php');?>
                                </div>
                            <?php endif;

                            if($key == count($elements)-1 && $element['type'] == TrainingLesson::ELEMENT_TYPE_ATTACH || ($key < count($elements)-1 && $elements[$key+1]['type'] != TrainingLesson::ELEMENT_TYPE_ATTACH)):?>
                                </div>
                                </div>
                            <?php endif;
                            $prev_el = $element['type'];
                        endforeach;
                    endif;?>

                    <div class="lesson_content"><?=$lesson['content'];?></div>
                    <div class="custom_code"><?=$lesson['custom_code'];?></div>

                    <?php // ДОПМАТ
                    if($user_id):
                        if(!empty($lesson['dopmat'])):?>
                            <div class="dopmat_box">
                                <h2><?=System::Lang('ADDITIONAL_MATERIALS');?></h2>
                                <ol>
                                    <?php $dopmat_arr = unserialize($lesson['dopmat']);
                                    foreach($dopmat_arr as $dopmat):
                                        $dopmat_link = Course::getDopmatLink($dopmat); // Получить ссылку на допмат?>
                                        <li><a href="/load/dopmat/<?=$dopmat_link['file'];?>" target="_blank"><?=$dopmat_link['name'];?></a></li>
                                    <?php endforeach;?>
                                </ol>
                            </div>
                        <?php endif;?>

                        <?php if($task['task_type']  && $task['text']):?>
                            <div class="block-border-top">
                                <div class="home-work__with-sidebar">
                                    <div class="home-work-inner">
                                        <h4 class="home-work__title"><?=System::Lang('HOME_TASK');?></h4>
                                        <?=$task['text'];?>
                                    </div>
                                </div>
                            </div>
                        <?php endif;?>

                        <?php // ЗАДАНИЕ NEW
                        if($task['task_type'] != 0):
                            require_once(__DIR__ . '/../layouts/answers_list.php');

                            if($lesson['show_comments'] && $this->tr_settings['commentcode']):?>
                                <div class="block-border-top">
                                    <div class="comments" id="comments">
                                        <?=$this->tr_settings['commentcode'];?>
                                    </div>
                                </div>
                            <?php endif;
                        endif;
                    endif;?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once (ROOT . '/extensions/training/layouts/frontend/footer.php');
require_once (ROOT . '/extensions/training/layouts/frontend/tech-footer.php');

$watermark = false;
if(isset($user['email'])) $watermark = htmlentities($user['email']);
if(isset($user['phone'])) $watermark .= $user['phone'];

if($elements):
    foreach ($elements as $element):
        if($element['type'] == TrainingLesson::ELEMENT_TYPE_MEDIA && in_array($element['params']['element_type'], [2, 3])):?>
            <script>
              var player = new Playerjs({id:"player_<?=$element['id'];?>", file:window.atob("<?=base64_encode(trim($element['params']['url']));?>"), design:1, <?php if($watermark != false):?>wid:"<?php echo $watermark;?>",<?php endif;?> <?php if(isset($_GET['testmode'])):?>wid_test:1, <?php endif;?> poster:"<?=$element['params']['cover'];?>"});
            </script>
        <?php endif;
    endforeach;?>
<?php endif;?>

<script src="/template/<?=$this->setting['template'];?>/js/player_bm.js" type="text/javascript"></script>
</body>
</html>