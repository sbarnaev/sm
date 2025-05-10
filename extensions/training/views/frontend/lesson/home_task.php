<?php defined('BILLINGMASTER') or die;?>
<div class="lesson-sidebar-inside">
    <div class="content-with-sidebar">
        <?php if($task['task_type'] > 0 && $levelAccessTypeHomeWork >= 0 && $levelAccessTypeHomeWork !== false):?>
            <div class="block-border-top">
                <div class="home-work__with-sidebar">
                    <div class="home-work-inner">
                        <?php if ($task['task_type'] >= 2):?>
                            <div class="block-border-top">
                                <?php require_once(__DIR__ . '/tests/form.php');?>
                            </div>
                        <?php endif;

                        if ($task['task_type'] < 3):?>
                            <div class="block-border-top homework-top">
                                <h4 class="home-work__title"><?=System::Lang('HOME_TASK');?>
                                    <?php if($task['check_type'] > $levelAccessTypeHomeWork):?>
                                        <span class="small-caption">(<?=TrainingLesson::getTaskTypeText($levelAccessTypeHomeWork, 1);?>)
                                            <a href="#modalAccessTask" data-uk-modal="{center:true}"><?=System::Lang('CHANGE');?></a>
                                        </span>
                                    <?php endif;

                                    require_once(__DIR__ . '/../layouts/homework_status.php'); ?>
                                </h4>
                                <?=$task['text'];?>
                            </div>
                        <?php endif;?>
                    </div>
                </div>
            </div>

            <?php if($task['task_type'] != 0):
                if ($task['check_type'] != 0) {
                    require_once(__DIR__ . '/../layouts/answers_list.php');
                }

                if ($task['task_type'] != 3) {
                    // тут делаем проверку если задание отклонено то форму редактирования
                    if ($lesson_homework_status == TrainingLesson::HOMEWORK_DECLINE) {
                        require_once(__DIR__ . '/edit_answer.php');
                    } else {
                        require_once(__DIR__ . '/../layouts/answer_form.php');
                    }
                }
            endif;
        elseif($task['task_type'] != 0):?>
            <div class="not-dz"><span class="h4"><?=System::Lang('HOME_TASK');?></span> <?=System::Lang('NO_HOME_TASK');?> <a href="#modalAccessTask" data-uk-modal="{center:true}"><?=System::Lang('IMPROVE');?></a></div>
        <?php elseif($task['task_type'] == 0 && $lesson['auto_access_lesson'] == 0 && $lesson_complete_status !=3):?> 
            <form enctype="multipart/form-data" class="form-complete" action="" method="POST" id="answer_form">
                <div class="add-home-work-submit z-1">
                    <button type="submit" name="complete" class="button btn-orange btn-green btn-green--big"><?=System::Lang('MARK_PASSED');?></button>
                </div>
            </form>
        <?php endif;?>
    </div>


    <?php if($training['lessons_tmpl'] == 2):?><!-- Если макет урока широкий, то сайдбар выводится тут в низу -->
        <aside class="sidebar">
            <section class="widget _instruction widget-sticky">
                <?php if($user_id && $training['show_widget_progress']):
                    if($training['cover'] && !$training['full_cover']):?>
                        <div class="sidebar-image">
                            <img src="/images/training/<?=$training['cover']?>">
                        </div>

                        <!-- ЗДЕСЬ выводим название тренинга, если обложка маленькая -->
                        <h4 class="traninig-name"><?=$name?></h4>
                    <?php endif;?>

                    <h3><?=System::Lang('YOUR_PROGRESS');?></h3>
                    <p class="progress-text"><?=System::Lang('TRACK_YOUR_TRAINING');?></p>

                    <?php require_once (__DIR__ . '/../layouts/progressbar.php');?>
                <?php else:
                    if($training['cover'] && !$training['full_cover']):?>
                        <div>
                            <div class="sidebar-image">
                                <img src="/images/training/<?=$training['cover']?>">
                            </div>
                        </div>

                        <!-- ЗДЕСЬ выводим название тренинга, если обложка маленькая -->
                        <h4 class="traninig-name"><?=$name?></h4>
                    <?php endif;?>

                    <h3><?=System::Lang('YOUR_PROGRESS');?></h3>
                    <p><?=System::Lang('PROGRESS_OF_THE_TRAINING_WILL_BE_DISPLAYED_HERE');?></p>
                <?php endif;?>
            </section>

            <?php if($sidebar):
                $widget_arr = $sidebar;
                require(ROOT . '/template/'.$this->setting['template'].'/widgets/widget_wrapper.php');
            endif;?>
        </aside>
    <?php endif;?>
</div>

<div id="modalAccessTask" class="uk-modal">
    <div class="uk-modal-dialog">
        <div class="userbox modal-userbox-2">
            <a href="#close" title="Закрыть" class="uk-modal-close uk-close modal-close"><span class="icon-close"></span></a>
            <div class="box1">
                <h3 class="modal-head-2"><?=System::Lang('CHANGE_ACCESS_HOME_TASK');?></h3>
                <div class="group-button-modal align-center">
                    <?php if ($task['check_type'] == 2):
                        $by_button = json_decode($training['by_button_curator_hw'], true);
                    elseif ($task['check_type'] == 1):
                        $by_button = json_decode($training['by_button_autocheck_hw'], true);
                    elseif ($task['check_type'] === 0):
                        $by_button = json_decode($training['by_button_self_hw'], true);
                    endif;
                    $link = Training::getLink2ByButton($by_button['type'], $by_button, $training);?>
                    <a class="button btn-yellow" id="accessLink" href="<?=$link?>"><?=$by_button['text']?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_comment_edit" class="uk-modal">
    <div class="uk-modal-dialog">
        <div class="userbox modal-userbox-2">
            <a href="#close" title="Закрыть" class="uk-modal-close uk-close modal-close"><span class="icon-close"></span></a>
            <div class="box1">
                <h3 class="modal-head-2"><?=System::Lang('CHANGE_COMMENT');?></h3>
                <div class="group-button-modal align-center">
                    <a class="button btn-yellow" id="accessLink" href="<?=$link?>"><?=$by_button['text']?></a>
                </div>
            </div>
        </div>
    </div>
</div>