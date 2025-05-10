<?php defined('BILLINGMASTER') or die;?>

<h3 id="test_go" class="test-go"><?=System::Lang('TEST');?></h3>

<div class="test-start">
    <div id="test_status" class="test-status test-not-done">
        <i class="icon-stop"></i><?=System::Lang('NOT_DONE');?>
    </div>

    <p class="text-center">
        <?php $start_text = $this->test['test_desc'] ? $this->test['test_desc'] : 'Для проверки знаний пройдите тест';
        $count_attempts = $this->test['test_try'];?>
        <?="$start_text<br>На прохождение теста предоставляется попыток: $count_attempts".($this->test['test_time'] > 0 ? "<br>Время на прохождение: {$this->test['test_time']} мин." : '')?>
    </p>

    <form class="start-test-form" action="/training/lesson/test/start" method="POST">
        <input type="hidden" name="lesson_id" value="<?=$this->lesson['lesson_id'];?>">
        <input type="hidden" name="test_id" value="<?=$this->test['test_id'];?>">
        <input type="submit" name="go_test" class="btn-green btn-green--big" value="Начать">
    </form>
</div>
