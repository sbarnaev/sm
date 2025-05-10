<?php defined('BILLINGMASTER') or die;?>

<form enctype="multipart/form-data" class="form-complete answer-client" action="/training/curator-comment/edit" method="POST" id="edit_comment_form">
    <input type="hidden" name="comment_id" value="<?=$comment['comment_id'];?>">
    <input type="hidden" name="homework_id" value="<?=$comment['homework_id'];?>">
    <input type="hidden" name="user_id" value="<?=$user_id;?>">
    <input type="hidden" name="lesson_id" value="<?=$lesson_id;?>">
    <input type="hidden" name="current_attach" value="<?=$comment['attach'];?>">
    <input type="hidden" name="token" value="<?=isset($_SESSION['user_token']) ? $_SESSION['user_token'] : '';?>">

    <div class="">
        <h4 class=""><?=System::Lang('ANSWER');?></h4>

        <div class="answer-client-middle">
            <div class="answer-client-middle__left">
                <div class="add-home-work-left"><?=System::Lang('TEXT');?></div>
            </div>

            <div class="answer-client-middle__right">
                <textarea class="editor" name="comment" id="training-comment-edit" required="required"><?=base64_decode($comment['comment_text']);?></textarea>
                <?php if($task['show_upload_file']):?>
                    <div class="attach home-work-attach">
                        <input type="file" data-browse="Загрузить файл" multiple name="lesson_attach[]">
                    </div>
                <?php endif;?>

                <div class="answer-client-bottom">
                    <div class="answer-client-submit z-1">
                        <button type="submit" name="edit_comment" class="button btn-orange btn-green btn-green--big"><?=System::Lang('SAVE');?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
  $(function(){
      <?php if($settings['editor'] == 1):?>
    editor_transfiguration($("textarea.editor"));
      <?php else:?>
    editor_transfiguration('training-comment-edit');
      <?php endif;?>
  });
</script>