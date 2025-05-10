<?php defined('BILLINGMASTER') or die;
$test = $task ? TrainingTest::getTestByTaskID($task['task_id']) : null;?>

<?php if ($test && TrainingTest::getCountQuestions2Test($test['test_id'])):?>
    <div class="test">
        <input type="hidden" name="lesson_id" value="<?=$lesson['lesson_id'];?>">
        <div class="test-wrap"></div>
    </div>

    <script src="/extensions/training/web/frontend/js/testform.js"></script>
<?php endif;?>