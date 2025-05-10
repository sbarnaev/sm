<?php defined('BILLINGMASTER') or die;?>

<h3 id="test_go" class="test-go"><?=System::Lang('TEST');?></h3>

<div id="test_status" class="test-status test-process">
    <i class="icon-stopwatch"></i><?=System::Lang('IN_PROCESS');?>
</div>

<form class="test-form" action="/training/lesson/test/complete" method="POST">
    <input type="hidden" name="lesson_id" value="<?=$this->lesson_id;?>">
    <input type="hidden" name="question_id" value="<?=$question['quest_id'];?>">

    <?php require (__DIR__.'/progressbar.php');?>

    <p class="question-name">
        <?="$number_question из {$this->show_questions_count}. {$question['question']}"?>
    </p>

    <?php if($question['image'] != null):?>
        <img class="test-one-image" src="<?=$question['image'];?>" alt="">
    <?php endif;?>

    <div class="test-answer-row-wrap">
        <?php $options = TrainingTest::getOptionsByQuest($question['quest_id']);
        if($options):
            $input_type = $question['true_answer'] == 1 ? 'radio' : 'checkbox';

            foreach($options as $key => $option):?>
                <div class="test-answer-row<?php if($option['cover'] != null) echo ' test-answer-item-image';?>">
                    <label class="custom-<?=$input_type;?>">
                        <?php $is_checked = isset($_SESSION['test_questions']['answers'][$question['quest_id']]) && in_array($option['option_id'], $_SESSION['test_questions']['answers'][$question['quest_id']]) ? true : false;
                        if ($input_type == 'checkbox'):?>
                            <input type="<?=$input_type;?>" data-id="<?=$option['option_id'];?>" name="option[<?=$question['quest_id'];?>][]" value="<?=$option['value'];?>"<?if ($is_checked) echo ' checked="checked"';?>>
                        <?php else:?>
                            <input type="<?=$input_type;?>" data-id="<?=$option['option_id'];?>" name="option[<?=$question['quest_id'];?>]" value="<?=$option['value'];?>"<?if ($is_checked) echo ' checked="checked"'; if($key == 0) echo ' required="required"';?>>
                        <?php endif;?>

                        <span><b><?=$option['title'];?></b></span>
                    </label>

                    <?php if($option['cover'] != null):?>
                        <img src="<?=$option['cover'];?>" class="test-list-image" alt="">
                    <?php endif;?>
                </div>
            <?php endforeach;
        endif;?>
    </div>

    <div>
        <div class="test-btn-row">
            <?php if ($number_question > 1):?>
                <button class="btn-green btn-test-prev" type="button" id="prevBtn"><i class="icon-prev"></i><?=System::Lang('PREVIOUS');?></button>
            <?php endif;

            if ($number_question < $this->show_questions_count):?>
                <button class="btn-green btn-test-next" type="button" id="nextBtn"><?=System::Lang('FURTHER');?><i class="icon-next"></i></button>
            <?php endif;?>
        </div>
    </div>

    <?php if ($number_question == $this->show_questions_count):?>
        <div class="test-submit">
            <input type="submit" name="test_complete" id="test_complete" class="button btn-blue-small" value="Завершить тест">
        </div>
    <?php endif;?>
</form>
