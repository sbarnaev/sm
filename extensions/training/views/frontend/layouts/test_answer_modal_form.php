<?php defined('BILLINGMASTER') or die;

function filterArray($value){
    return ($value['is_valid'] == 1);
}
$filterValid = array_filter($data, 'filterArray');


$passing_time = $check_test['date'] - $start_test['test_start'];
$mk_passing_time = mktime( 0, 0, $passing_time);

$passing_time_d = intval($passing_time / 86400);
$passing_time_h = (int)date('G', $mk_passing_time);
$passing_time_m = (int)date('i', $mk_passing_time);
$passing_time_s = (int)date('s', $mk_passing_time);
$show_passing_time = ($passing_time_d ? "$passing_time_d д." : '') . ($passing_time_h ? " $passing_time_h ч." : '') . ($passing_time_m ? " $passing_time_m мин." : '') . " $passing_time_s секунд";

$count_passing_test = TrainingTest::getCountPassingTest($test['test_id'], $user_id);?>

<a href="#close" title="Закрыть" class="uk-modal-close uk-close modal-close modal-close-2">
    <span class="icon-close"></span>
</a>

<div class="modal-top">
    <strong><?=System::Lang('CLIENT_RESULTS');?></strong>
    <div class="modal-top-status">
        <?php if($check_test['success']):?>
            <div class="less_complete static"><span class="icon-check"></span><?=System::Lang('DONE');?></div>
        <?php else:?>
            <div class="test-status test-final-error static"><span class="icon-check"></span><?=System::Lang('NOT_PASSED');?></div>
        <?php endif;?>
    </div>
</div>

<div class="modal-test-result">
    <?php if($test['finish']>0):?>
        <p><?=System::Lang('OUTCOME');?> <?=$check_test['sum_points']?> <?=System::Lang('FROM');?> <?=$test['finish']?> <?=System::Lang('TO_BE_CONTINUE');?></p>
    <?php endif;?>
    <p><?=System::Lang('CORRECT_ANSWER');?> <?=$check_test['sum_valid']?> <?=System::Lang('FROM');?> <?=count($data)?></p>
    <p><?=System::Lang('TIME');?> <?=$show_passing_time;?></p>
    <p><?=System::Lang('ATTEMPT');?> <?="$count_passing_test из {$test['test_try']}";?></p>
</div>

<div class="box1">
    <div class="table-answers-responsive">
        <?php if($data):?>
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
                    <?php $prev_question_id = null;
                    foreach($data as $result):?>
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
                    endforeach;?>
                </tbody>
            </table>
        <?php endif;?>
    </div>
</div>
