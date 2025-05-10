<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1><?=System::Lang('ANSWER_LIST');?></h1>
        <div class="logout">
            <a href="/" target="_blank"><?=System::Lang('GO_SITE');?></a>
            <a href="/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/training/">Тренинги</a></li>
        <li>Список ответов</li>
    </ul>

    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;?>

    <?php if(isset($_GET['fail'])):?>
        <div class="admin_warning"></div>
    <?php endif;
    
    if ($total > 0):?>
        <div class="filter admin_form" style="margin:0 0 1em 0;">
            <form action="" method="POST">
                <div class="filter-row">
                    <div class="select-wrap" style="width:22%;">
                        <select name="lesson_complete_status">
                            <option value="unchecked"<?php if(isset($_SESSION['admin']['training']['answers_filter']) && $_SESSION['admin']['training']['answers_filter']['lesson_complete_status'] == 1) echo ' selected="selected"';?>>На проверку</option>
                            <option value="checked"<?php if(isset($_SESSION['admin']['training']['answers_filter']) && in_array($_SESSION['admin']['training']['answers_filter']['lesson_complete_status'], [2,3])) echo ' selected="selected"';?>>Проверенные</option>
                            <option value="all"<?php if(isset($_SESSION['admin']['training']['answers_filter']) && $_SESSION['admin']['training']['answers_filter']['lesson_complete_status'] === null) echo ' selected="selected"';?>>Все ответы</option>
                        </select>
                    </div>
        
                    <div class="select-wrap" style="width:28%;">
                        <select name="answer_status">
                            <option value="0"<?php if(isset($_SESSION['admin']['training']['answers_filter']) && $_SESSION['admin']['training']['answers_filter']['answer_status'] === 0) echo ' selected="selected"';?>>Непрочитанные</option>
                            <option value="1"<?php if(isset($_SESSION['admin']['training']['answers_filter']) && $_SESSION['admin']['training']['answers_filter']['answer_status'] === 1) echo ' selected="selected"';?>>Прочитанные</option>
                            <option value="all"<?php if(isset($_SESSION['admin']['training']['answers_filter']) && $_SESSION['admin']['training']['answers_filter']['answer_status'] === null) echo ' selected="selected"';?>>Все ответы</option>
                        </select>
                    </div>
    
                    <div class="select-wrap" style="width:28%;">
                        <select name="answer_type">
                            <option value="0"<?php if(isset($_SESSION['admin']['training']['answers_filter']) && $_SESSION['admin']['training']['answers_filter']['answer_type']) echo ' selected="selected"';?>>Ответы и комментарии</option>
                            <option value="<?=TrainingLesson::MSG_TYPE_ANSWER;?>"<?php if(isset($_SESSION['admin']['training']['answer_type']) && $_SESSION['admin']['training']['answers_filter']['answer_type'] === TrainingLesson::MSG_TYPE_ANSWER) echo ' selected="selected"';?>>Только ответы</option>
                            <option value="<?=TrainingLesson::MSG_TYPE_COMMENT;?>"<?php if(isset($_SESSION['admin']['training']['answer_type']) && $_SESSION['admin']['training']['answers_filter']['answer_type'] == TrainingLesson::MSG_TYPE_COMMENT) echo ' selected="selected"';?>>Только комментарии</option>
                        </select>
                    </div>
    
                    <div class="select-wrap" style="width:34%;">
                        <select name="training_id">
                            <option value="0">Все тренинги</option>
                            <?php $training_list = Training::getTrainingList();
                            if($training_list):
                                foreach($training_list as $training):?>
                                    <option value="<?=$training['training_id'];?>"<?php if(isset($_SESSION['admin']['training']['answers_filter']) && $_SESSION['admin']['training']['answers_filter']['training_id'] == $training['training_id']) echo ' selected="selected"';?>><?=$training['name'];?></option>
                                <?php endforeach;
                            endif;?>
                        </select>
                    </div>
    
                    <div class="select-wrap" style="width:34%;">
                        <select name="lesson_id">
                            <option value="0">Все уроки</option>
                        </select>
                    </div>
    
                    <div>
                        <input type="text" name="user_email" value="<?=isset($_SESSION['admin']['training']['answers_filter']['user_email']) ? $_SESSION['admin']['training']['answers_filter']['user_email'] : '';?>" placeholder="E-mail">
                    </div>
    
                    <div>
                        <div class="button-group">
                            <?php if(isset($_SESSION['admin']['training']['answers_filter'])):?>
                                <input class="red-link" style="border: none; background: none; text-decoration:underline; cursor:pointer" type="submit" value="Сбросить" name="reset">
                            <?php endif;?>
                            <input class="button-blue-rounding" type="submit" value="Выбрать" name="filter">
                        </div>
                    </div>
                </div>
                <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
            </form>
        </div>
    <?php endif;?>


    <div class="admin_form">
        <?php if($answer_list):?>
            <?php foreach($answer_list as $answer):
                $user_answer = User::getUserNameByID($answer['user_id']);?>
                <div class="list-questions relative">
                    <div class="list-questions__left">
                        <img src="<?=User::getAvatarUrl($user_answer, $setting);?>" alt="" />
                    </div>

                    <div class="list-questions__right">
                        <ul class="list-questions__crumbs">
                            <li><?=$answer['lesson_name']?></li>
                        </ul>

                        <div class="list-questions__top">
                            <p class="list-questions__name">
                                <a href="/admin/users/edit/<?=$answer['user_id'];?>"><?=$answer['user_name']?></a>
                            </p>,
                            <div class="list-questions__email">
                                <?=$answer['user_email'];?>
                            </div>,
                            <div class="list-questions__time">
                                <span style="display: none;"><?=$answer['id'];?>,</span> <?=date("d.m.Y H:i:s", $answer['create_date']);?>
                            </div>

                            <div class="list-questions__type">
                                <?=$answer['type'] == Traininglesson::MSG_TYPE_ANSWER ? 'Ответ' : 'Комментарий';?>
                            </div>
                        </div>

                        <div class="list-questions__text">
                            <?=mb_substr(trim(strip_tags(base64_decode($answer['answer']))), 0, 100);?>
                        </div>

                        <div class="list-questions__bottom">
                            <a class="button-yellow-rounding" href="/admin/training/answers/<?="{$answer['lesson_id']}/{$answer['user_id']}";?>">Перейти к рассмотрению</a>
                            <a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/answers/deldialog/<?=$answer['user_id'];?>?token=<?=$_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach;?>
        <?php else:
            echo 'Будет доступно в следующей версии.';
        endif;?>
    </div>

    <?php if($pagination) echo $pagination->get();?>

    <?php require_once (ROOT . '/extensions/training/layouts/admin/admin-footer.php');?>
</div>
</body>

<script type="text/javascript">
    $(function() {
      $('select[name="training_id"]').on('change', '', function (e) {
        let training_id = this.value;
        let token = $(this).parents('form').find('input[name="token"]').val();
        $.ajax({
          url: '/admin/trainingajax/lessonlist',
          method: 'post',
          dataType: 'json',
          data: {training_id: training_id, admin_token: token},
          success: function($resp) {
            if ($resp['status']) {
              let html = '<option value="">Все уроки</option>';
              if (Object.keys($resp['list']).length > 0) {
                for (let key of Object.keys($resp['list'])) {
                  html += '<option value="' + key + '">' + $resp['list'][key] + '</option>';
                }
              }
              $('select[name="lesson_id"]').html(html);
            } else {
              console.error($resp['error']);
            }
          }
        });
      });
    });
</script>
</html>