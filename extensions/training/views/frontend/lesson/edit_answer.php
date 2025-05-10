<?php defined('BILLINGMASTER') or die;?>

<form enctype="multipart/form-data" class="form-complete" action="/training/answer/edit" method="POST" id="edit_answer_form">
    <input type="hidden" name="answer_id" value="<?=$answer['homework_id'];?>">
    <input type="hidden" name="current_attach" value="<?=$answer['attach'];?>">
    <input type="hidden" name="token" value="<?=isset($_SESSION['user_token']) ? $_SESSION['user_token'] : '';?>">

    <div class="block-border-top">
        <div class="add-home-work">
            <h4 class="add-home-work-title"><?=System::Lang('ANSWER');?></h4>
                <?php if($task && $task['show_work_link']):?>
                    <div class="add-home-work-line">
                        <div class="add-home-work-left"><?=System::Lang('LINK');?></div>
                        <div class="add-home-work-right">
                            <?php if(isset($answer['work_link'])):?>
                                <input name="work_link" type="text" placeholder="Вставьте ссылку" value="<?=$answer['work_link']?>">
                            <?php else:?>
                                <input name="work_link" type="text" placeholder="Вставьте ссылку">
                            <?php endif;?>
                        </div>
                    </div>
                <?php endif;?>
            <div class="add-home-work-line">
                <div class="add-home-work-left"><?=System::Lang('TEXT');?></div>
                <div class="add-home-work-right">
                    <textarea class="editor" name="answer" id="training-answer-edit" required="required"><?=base64_decode($answer['answer']);?></textarea>
                    <?php if($task['show_upload_file']):?>
                        <div class="attach home-work-attach">
                            <input type="file" data-browse="Загрузить файл" multiple name="lesson_attach[]">
                        </div>
                    <?php endif;?>
                </div>
            </div>

            <div class="add-home-work-submit z-1 add-home-work--simple">
                <button type="submit" name="edit_answer" class="button btn-orange btn-green btn-green--big"><?=System::Lang('SAVE');?></button>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
  $(function(){
    <?php if($settings['editor'] == 1):?>
      editor_transfiguration($("textarea.editor"));
    <?php else:?>
      editor_transfiguration('training-answer-edit');
    <?php endif;?>
  });
</script>