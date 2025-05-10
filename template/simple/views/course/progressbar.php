<?php if($user_id && $course['show_progress'] == 1):
    $amount = Course::countLessonByCourse($course['course_id'], $less_status);
    $completed = $map_items_progress > 0 ? count($map_items_progress) : 0;
    $progress = $amount != 0 ? ($completed / $amount) * 100 : 0;?>

    <div class="progress_course">
        <div class="progress_bar">
            <div class="completed_line" style="width:<?php echo ceil($progress);?>%;<?php if(ceil($progress) == 100) echo ' background:#4BD96A';?>"> </div>
        </div>
    </div>

    <div class="progress-row">
        <div class="progress-left"><?php echo ceil($progress);?><?=System::Lang('PERCENTAGE_PASSED');?></div>
        <div class="progress-right"><?php echo $amount.System::addTermination($amount, ' урок[TRMNT]');?></div>
    </div>

    <?php if(ceil($progress) != 100):?>
    <div class="current-course-buttom">
        <span class="btn-yellow-border"><?=System::Lang('PASSING_PROCESS');?></span>
    </div>
    <?php endif;?>
<?php endif; ?>