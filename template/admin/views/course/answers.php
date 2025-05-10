<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
  <div class="top-wrap">
    <h1><?php echo System::Lang('ANSWER_LIST');?></h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
  </div>
  <ul class="breadcrumb">
    <li>
      <a href="/admin">Дашбоард</a>
    </li>
    <li>
      <a href="/admin/courses/">Тренинги</a>
    </li>
    <li>Список ответов</li>
  </ul>
    <div class="nav_gorizontal">
        <ul>
            <li><?php if(!isset($_GET['get'])){?><a class="button-yellow-rounding" href="/admin/answers?get=check"><?php echo System::Lang('NEWS_ANSWERS');?></a><?php } else echo System::Lang('<span class="button-yellow-border">На проверку</span>');?></li>
            <li><?php if(isset($_GET['get'])){?><a class="button-yellow-rounding" href="/admin/answers"><?php echo System::Lang('ALL_ANSWERS');?></a><?php } else echo System::Lang('<span class="button-yellow-border">Все ответы</span>');?></li>
        </ul>
    </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning"></div>'?>
    
    <div class="filter admin_form" style="margin:0 0 1em 0;">
        <div class="filter-row">
        <div>
            <form action="" method="POST">
                <div class="one-filter">
                    <div class="select-wrap" style="width:45%; float:left; margin-right:20px">
                        <select name="course_id">
                            <option value="">Выбрать курс</option>
                            <?php $course_list = Course::getCourseListFromSitemap();
                            if($course_list):
                                foreach($course_list as $course):?>
                                    <option value="<?php echo $course['course_id'];?>"<?php if(isset($_SESSION['course_id']) && $_SESSION['course_id'] == $course['course_id']) echo ' selected="selected"';?>><?php echo $course['name'];?></option>
                                <?php endforeach;
                            endif;?>
                        </select>
                        
                    </div>
                    
                    <div class="select-wrap" style="width:45%; float:left">
                        <select name="lesson_id">
                            <option value="">Выбрать урок</option>
                        </select>
                    </div>
                </div>
        </div>
        <div>      
                <div class="button-group">
                    <input class="button-blue-rounding" type="submit" value="Выбрать" name="filter">
                    <?php if(isset($_SESSION['course_id']) || isset($_SESSION['lesson_id'])):?>
                    <input class="red-link" style="border: none; background: none; text-decoration:underline; cursor:pointer" type="submit" value="Сбросить" name="reset">
                    <?php endif;?>
                </div>
            </form>
        </div>
        </div>
    </div>
    
    <div class="admin_form">

        <?php if($answer_list){?>
    <?php foreach($answer_list as $answer):?>

      <div class="list-questions relative">
        <!--div class="task-accepted">
            <label>
                <input type="checkbox" name="success" value="1">
                    <?php if($answer['status'] == 0) echo '<span class="noaccepted">Принять</span>';
                    else echo '<span class="accepted"><i class="icon-check"></i> Принято</span>';?>
                    
            </label>
        </div-->
        <div class="list-questions__left">
          <?php $user_answer = User::getUserById($answer['user_id']);?>
            <img src="<?=User::getAvatarUrl($user_answer, $setting);?>" alt="" />
        </div>
        <div class="list-questions__right">
          <ul class="list-questions__crumbs">
            <li><?php $lesson = Course::getLessonDataByID($answer['lesson_id']); echo $lesson['name']?></li>
          </ul>
          <div class="list-questions__top">
            <p class="list-questions__name"><?php echo $answer['user_name']?></p>,
            <div class="list-questions__email"><?php echo $answer['user_email'];?></div>,
            <div class="list-questions__time"><span style="display: none;"><?php echo $answer['id'];?>,</span> <?php echo date("d.m.Y H:i:s", $answer['date']);?></div>
          </div>
          <div class="list-questions__text">
            <?php $text = mb_substr($answer['body'], 0, 100); echo strip_tags($text);?>
          </div>
          <div class="list-questions__bottom">
            <a class="button-yellow-rounding" href="/admin/answers/<?php echo $answer['id'];?>?user=<?php echo $answer['user_id'];?>&lesson=<?php echo $answer['lesson_id'];?>">Перейти к рассмотрению</a>
            <a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/answers/deldialog/<?php echo $answer['user_id'];?>/<?php echo $answer['lesson_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a>
          </div>
        </div>
      </div>

        <?php endforeach; ?>
        <?php } else echo 'Нет ответов';?>

    </div>
    <?php if($is_pagination == true) echo $pagination->get(); ?>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
<script type="text/javascript">
    	setTimeout(function(){$('.success_message').fadeOut('fast')},4000);
    	let lessonsFilter = function (course_id, lesson__id) {
    	  if (course_id != '') {
            $.ajax({
              url: '/admin/lessons/listfilter',
              method: 'post',
              dataType: 'json',
              data: {course_id: course_id},
              success: function(list) {
                if (Object.keys(list).length > 0) {
                  let html = '<option value="">Выбрать урок</option>';
                  $.each(list, function(sort, name) {
                    let lesson = Object.keys(name);
                    let lesson_id = Number(lesson[0]);
                    html += '<option value="' + lesson_id + '"';
                    html += lesson__id != '' && lesson_id == lesson__id ? ' selected="selected"' : '';
                    html += '>' + name[lesson_id] + '</option>';
                  });
                  $('select[name="lesson_id"]').html(html);
                  $('.filter-lessons').show();
                  $('.filter-button input[name="filter"]').css('opacity', '1');
                  $('.filter-button input[name="filter"]').removeAttr('disabled');
                } else {
                  $('.filter-lessons').hide();
                  $('.filter-button input[name="filter"]').css('opacity', '0.4');
                  $('.filter-button input[name="filter"]').attr('disabled','disabled');
                }
              }
            });
          } else {
            $('select[name="lesson_id"]').html('<option value="">Выбрать урок</option>');
          }
        }
        $(function() {
          let course_id = $('select[name="course_id"]').val();
          let lesson_id = '<? echo isset($_SESSION['lesson_id']) ? $_SESSION['lesson_id'] : '';?>';
          lessonsFilter(course_id, lesson_id);
          
          $('select[name="course_id"]').change(function() {
            lessonsFilter(this.value, '');
          });
        });
    </script>
</body>
</html>