<?php defined('BILLINGMASTER') or die;
$progress_data = Training::getLessonsProgressData($training, $user_id);?>

<div class="progress_course">
    <div class="progress_bar">
        <div class="completed_line" style="width:<?=$progress_data['progress'];?>%;<?php if($progress_data['progress'] == 100) echo ' background:#4BD96A';?>"></div>
    </div>
</div>

<div class="progress-row">
    <div class="progress-left"><?=$progress_data['progress'];?><?=System::Lang('PERCENTAGE_PASSED');?></div>
    <div class="progress-right"><?=$progress_data['count_less'].System::addTermination($progress_data['count_less'], ' урок[TRMNT]');?></div>
</div>