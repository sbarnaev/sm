<?php defined('BILLINGMASTER') or die;
require_once(ROOT . "/template/{$this->setting['template']}/layouts/tech-footer.php");?>

<?php if(empty($training_filter_enabled)):?>
  <link rel="stylesheet" href="/extensions/training/web/frontend/style/style.css" type="text/css" />
  <script src="/extensions/training/web/frontend/js/main.js"></script>
<?php endif;?>

<?php if($is_page == 'lesson'):
    if(isset($task) && $task['task_type'] == 2):?>
        <script>
          $(function() {
            $('form.form-complete').submit(function() {
              if ($(this).children('input[name="is_allow_submit_homework"]').val() < 1) {
                alert('Сначала пройдите тест');
                return false;
              }
            });
          });
        </script>
    <?php endif;
endif;

if(in_array($is_page, ['lesson', 'lk'])):?>
    <link rel="stylesheet" type="text/css" href="/lib/fancybox/css/jquery.fancybox.min.css" media="screen" />
    <script type="text/javascript" src="/lib/fancybox/js/jquery.fancybox.min.js"></script>
    <script>
      $(function() {
        $('.lesson-inner .user_message img, .dialog_item .user_message img').each(function() {
          let src = $(this).attr('src');
          $(this).wrapAll('<a data-fancybox="" href="'+src+'">');
        });
      });
    </script>
<?php endif;?>