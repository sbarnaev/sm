<?php defined('BILLINGMASTER') or die;
// task_type = 3 это только ТЕСТ статус ДЗ Урока не нужен
if ($task['task_type'] != 3):
    if ($lesson_complete_status === TrainingLesson::HOMEWORK_AUTOCHECK):?>
        <span class="less_waiting on-check">
            <span class="icon-stopwatch"></span><?=System::Lang('ON_CHECK');?>
        </span>
    <?php else:?>
        <?php if ($lesson_homework_status === TrainingLesson::HOME_WORK_IN_VERIFICATION):?>
            <span class="less_waiting">
                <span class="icon-stopwatch"></span><?=System::Lang('ON_CHECK');?>
            </span>
        <?php elseif($lesson_homework_status == TrainingLesson::HOMEWORK_DECLINE):?>
            <span class="less_complete less_rejected">
                <span class="icon-rejected"></span><?=System::Lang('REJECTED');?>
            </span>
        <?php elseif($lesson_homework_status == TrainingLesson::HOME_WORK_ACCEPTED):?>
            <span class="less_complete">
                <span class="icon-check"></span><?=System::Lang('ACCEPTED');?>
            </span>
        <?php elseif($lesson_homework_status == TrainingLesson::HOME_WORK_SEND):?>
            <span class="less_waiting">
                <span class="icon-stopwatch"></span><?=System::Lang('ON_WAITING_CHECK');?>
            </span>
        <?php else:?>
            <span class="less_waiting">
                <span class="icon-stop"></span><?=System::Lang('NOT_DONE');?>
            </span>
        <?php endif;?>
    <?php endif;?>
<?php endif;?>
<? // широкий макет уроков
// Как я понял формат такого виджета статуса урока будет только в КАЙНе и он никак не зависит от ширины макета...
// Пока закоментирую все это 
/*
    if (!$lesson_complete_status) {
        $progress_val = 25;
        $status_line = 1;
        $progress_text = 'Нет работ на проверке';
    } elseif(in_array($lesson_complete_status, [TrainingLesson::HOMEWORK_SUBMITTED, TrainingLesson::HOMEWORK_AUTOCHECK])) {
        $progress_val = 50;
        $status_line = 2;
        $progress_text = 'Работа проверяется';
    } elseif($lesson_complete_status == TrainingLesson::HOMEWORK_DECLINE) {
        $progress_val = 80;
        $status_line = 3;
        $progress_text = 'Работа отклонена';
    } elseif($lesson_complete_status == TrainingLesson::HOMEWORK_ACCEPTED) {
        $progress_val = 100;
        $status_line = 4;
        $progress_text = 'Работа принята';
    }?>
    <div class="home-work-status">
        <div class="home-work-widget _instruction">
            <h3 class="widget-header">Статус проверки</h3>
            <p>Ответ по вашей работе появится снизу под вашим сообщением.</p>

            <div class="progress_course">
                <div class="progress_bar">
                    <div class="completed_line status-line-<?=$status_line;?>" style="width:<?=$progress_val;?>%"> </div>
                </div>
            </div>

            <div class="progress-row">
                <div class="progress-left"><?=$progress_text;?></div>
                <div class="progress-right">
                    <?php if($lesson_complete_status) {
                        $task_data = TrainingLesson::getLessonCompleteData($lesson['lesson_id'], $user_id);
                        echo date('d.m.Y', $task_data['open']);
                    }?>
                </div>
            </div>
        </div>
    </div>
*/?>
