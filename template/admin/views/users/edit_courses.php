<?php defined('BILLINGMASTER') or die;?>

<div class="row-line">
    <div class="col-1-1">
        <?php foreach($uniq_courses as $course):?>
            <div class="block-collapse">
                <h4 class="block-collapse-head"><span class="section-name"><?=Course::getCourseNameByID($course['course_id']);?></span><span class="icon-down"></span></h4>

                <div class="block-collapse-inner" style="display: none;">
                    <div class="overflow-container">
                        <table class="table kurator-table">
                            <?php $order_by = 2;
                            $lessons = Course::getCompleteLessonsUser($id, $course['course_id'], $status = 0, $order_by);
                            if($lessons):
                                foreach($lessons as $lesson):
                                    $lesson_data = Course::getLessonMiniData($lesson['lesson_id']);?>
                                    <tr>
                                        <td class="text-left"><?=$lesson_data['name'];?><br />
                                            <span style="color:#888"><?='ID: '.$lesson['lesson_id'];?></span>
                                        </td>
                                        <td class="text-right"><?=getLessonTask($lesson_data['task_type']);?></td>
                                        <td class="text-right"><?=$lesson['status'] == 1 ? 'Выполнен' : 'Не проверен';?><br>
                                            <?=date("d.m.Y", $lesson['date']);?>
                                        </td>
                                        <td class="text-right">
                                            <a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/users/delcompletelesson/<?=$lesson['lesson_id'];?>?token=<?=$_SESSION['admin_token'];?>&user_id=<?=$user['user_id'];?>" title="Удалить прохождение урока">
                                                <i class="fas fa-times" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            endif;?>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach;?>
    </div>
</div>
