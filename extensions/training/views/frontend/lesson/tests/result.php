<?php defined('BILLINGMASTER') or die;
$count_passing_test = TrainingTest::getCountPassingTest($this->test['test_id'], $this->user_id);
$count_more_test_try = $this->test['test_try'] - $count_passing_test;?>

<h3 id="test_go" class="test-go"><?=System::Lang('TEST');?></h3>
<?php if($this->homework['test'] == 0 && $this->test_expired):?>
    <p><?=System::Lang('TIME_OVER');?></p>
    <?php if($count_more_test_try > 0):?>
        <form class="start-test-form" action="/training/lesson/test/start" method="POST">
            <input type="hidden" name="lesson_id" value="<?=$this->lesson['lesson_id'];?>">
            <input type="hidden" name="test_id" value="<?=$this->test['test_id'];?>">
            <input type="submit" name="go_test" class="btn-green btn-green--big" value="Попробовать еще раз">
        </form>
    <?php endif;
else:
    $passing_time = $test_result['date'] - $this->homework['test_start'];
    $mk_passing_time = mktime( 0, 0, $passing_time);

    $passing_time_d = intval($passing_time / 86400);
    $passing_time_h = (int)date('G', $mk_passing_time);
    $passing_time_m = (int)date('i', $mk_passing_time);
    $passing_time_s = (int)date('s', $mk_passing_time);
    $show_passing_time = ($passing_time_d ? "$passing_time_d д." : '') . ($passing_time_h ? " $passing_time_h ч." : '') . ($passing_time_m ? " $passing_time_m мин." : '') . " $passing_time_s сек.";
    $test_status = $this->homework['test'];?>

    <div id="test_status" class="test-status <?=$test_status == 1 ? 'test-final' : 'test-not-done'.($count_more_test_try < 1 ? ' red' : '');?>">
        <i class="<?=$test_status == 1 ? 'icon-check' : 'icon-stop';?>"></i><?=TrainingTest::getStatusText($test_status);?>
    </div>

    <div class="modal-test-result">
        <p><strong><?=System::Lang('POINT_RESULTS');?> <?=$test_result['sum_points'];?></strong></p>
        <p><?=System::Lang('CORRECT_ANSWER');?> <?="{$test_result['sum_valid']} из {$this->show_questions_count}";?></p>
        <p><?=System::Lang('TIME');?> <?=$show_passing_time;?></p>
        <p><?=System::Lang('ATTEMPT');?> <?="$count_passing_test из {$this->test['test_try']}";?></p>
    </div>

    <?php if($test_status == 1 && $this->test['help_hint_success'] || $test_status == 2 && $this->test['help_hint_fail']):?>
        <div class="decryption-spoiler">
            <div class="decryption-spoiler-title">
                <span><?=System::Lang('SHOW_TRANSCRIPT');?></span><i class="icon-down"></i>
            </div>

            <div class="decryption-spoiler-content" style="display: none;">
                <table class="table-test-answers">
                    <thead>
                        <tr>
                            <td><?=System::Lang('QUESTION');?></td>
                            <td><?=System::Lang('CORRECT_ANSWERED');?></td>
                            <td><?=System::Lang('USER_ANSWER');?></td>
                            <td class="text-right"><?=System::Lang('POINTS');?></td>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $detail_test_result = TrainingTest::getDetailedTestResult($this->lesson_id, $this->user_id, true);
                        if ($detail_test_result):?>
                            <?php $prev_question_id = null;
                            foreach($detail_test_result as $result):?>
                                <tr>
                                    <td><?=!$prev_question_id || $prev_question_id != $result['quest_id'] ? $result['question'] : '';?>
                                    <?php if($result['image']):?>
                                        <img src="<?=$result['image']?>" alt="" />
                                    <?php endif;?>
                                    </td>

                                    <td>
                                        <div class="result-item">
                                            <span class="result-point result-green"></span>
                                            <?php if($result['cover_quest']):?>
                                                <span>
                                                    <img src="<?=$result['cover_quest']?>" alt="" />
                                                </span>
                                            <?php else:?>
                                                <div class="result-item-inner"><?=str_replace(',', ',<br>', $result['title']);?></div>
                                            <?php endif;?>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="result-item">
                                            <span class="result-point result-<?=$result['is_valid'] ? 'green' : 'red';?>"></span>
                                            <?php if($result['cover_answer']):?>
                                                <span>
                                                    <img src="<?=$result['cover_answer']?>" alt="" />
                                                </span>
                                            <?php else:?>
                                            <div class="result-item-wrap">
                                                <div class="result-item-inner"><?=str_replace(',', ',<br>', $result['result']);?></div>
                                                <span class="result-item-icon" data-uk-tooltip="" title="<?=$result['help'];?>">
                                                    <?if($result['help']):?>
                                                        <i class="icon-answer"></i>
                                                    <?php endif;?>
                                                </span>
                                            </div>
                                            <?php endif;?>
                                        </div>
                                    </td>

                                    <td class="text-right"><?=$result['user_points'];?></td>
                                </tr>
                                <?php $prev_question_id = $result['quest_id'];
                            endforeach;
                        endif;?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif;

    if($test_status == 2 && $count_more_test_try < 1):?>
        <p class="text-center test-try-info">
            <strong><?=System::Lang('ATTEMPTS_ENDED');?></strong><br>
            <strong><?=System::Lang('WAIT_RESPONSE');?></strong>
        </p>
    <?php endif;

    if($test_status == 2 && $count_passing_test < $this->test['test_try']):?>
        <form class="start-test-form" action="/training/lesson/test/start" method="POST">
            <input type="hidden" name="lesson_id" value="<?=$this->lesson['lesson_id'];?>">
            <input type="hidden" name="test_id" value="<?=$this->test['test_id'];?>">
            <input type="submit" name="go_test" class="btn-green btn-green--big" value="Попробовать еще раз">
        </form>
    <?php endif;
endif;?>

<?php if($test_status == 1 && TrainingLesson::isLessonComplete($this->lesson['lesson_id'], $this->user_id)):?>
    <script>
        $('.lesson-inner .next_less_next').removeClass('hidden');
    </script>
<?php endif;?>
