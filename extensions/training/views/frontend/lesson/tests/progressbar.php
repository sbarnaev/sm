<?php defined('BILLINGMASTER') or die;
$time_left = $this->test_finish - $this->time;
$mktime_left = mktime( 0, 0, $time_left);

$expired_d = intval($time_left / 86400);
$expired_h = (int)date('G', $mktime_left);
$expired_m = (int)date('i', $mktime_left);
$expired_s = (int)date('s', $mktime_left);
$show_expired = ($expired_d ? "$expired_d д." : '') . ($expired_h ? " $expired_h ч." : '') . ($expired_m ? " $expired_m мин." : '') . " $expired_s сек.";

$progress = intval(($number_question / $this->show_questions_count) * 100);?>

<div class="progress-row test-progress-row">
    <div class="progress-left"><?=System::Lang('PROGRESS');?></div>
    <?php if($time_left > 0): // Показываем сколько осталось секунд?>
        <div class="progress-right"><?=System::Lang('TIME_LEFT');?> <span class="time-left" data-time_left="<?=$time_left;?>"><?=$show_expired;?></span></div>
    <?php endif;?>
</div>

<div class="progress_bar">
    <div class="completed_line" style="width:<?=$progress;?>%"></div>
</div>
