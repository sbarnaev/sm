<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');?>
<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>

    <div id="content">
        <div class="layout" id="lk">
            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min';?> content-with-sidebar">
                    <h1><?=System::Lang('CURATOR_OFFICE');?></h1>
                    <div class="tabs2">
                        <ul>
                            <li<?php if(isset($_GET['get']) && $_GET['get'] == 'all') echo ''; else echo ' class="active"'?>><a href="<?php echo $setting['script_url'];?>/lk/answers"><?php echo System::Lang('NEWS_ANSWERS');?></a></li>
                            <li<?php if(isset($_GET['get']) && $_GET['get'] == 'all') echo ' class="active"';?>><a href="<?php echo $setting['script_url'];?>/lk/answers?get=all"><?php echo System::Lang('ALL_ANSWERS');?></a></li>
                        </ul>
                    </div>
                    <div class="userbox">
                    <?php if(isset($_GET['success'])) echo '<div class="success_message">Успешно</div>';?>

                    <?php if($answer_list){?>
                        <?php foreach($answer_list as $answer):
                            $user_answer = User::getUserNameByID($answer['user_id']);?>
                            <div class="list-questions">
                                <div class="list-questions__left">
                                    <img src="<?=User::getAvatarUrl($user_answer, $setting);?>" alt="" />
                                </div>
                                <div class="list-questions__right">
                                    <ul class="list-questions__crumbs">
                                        <li><?php if($answer['course_id'] != null) {
                                            $course_data = Course::getCourseByID($answer['course_id']);
                                            echo $course_data['name'];
                                        }?>
                                        </li>
                                        <li><?php $lesson = Course::getLessonDataByID($answer['lesson_id']); echo $lesson['name']?></li>
                                    </ul>
                                    <div class="list-questions__top">
                                        <h4 class="list-questions__name">
                                        <a href="<?php echo $setting['script_url'];?>/lk/answers/<?php echo $answer['id'];?>?user=<?php echo $answer['user_id'];?>&lesson=<?php echo $answer['lesson_id'];?>"><?php echo $answer['user_name'];?></a>
                                        </h4>,
                                        <?php if($answer['user_id'] != $userId):?>
                                        <div class="list-questions__email"><?php echo $answer['user_email'];?></div>,
                                        <?php endif;?>
                                        <div class="list-questions__time">#<?php echo $answer['id'];?>, <?php echo date("d.m.Y H:i:s", $answer['date']);?></div>
                                    </div>
                                    <div class="list-questions__text">
                                        <?php $text = mb_substr(strip_tags(trim($answer['body'])), 0, 100); echo $text;?>
                                    </div>
                                    <div class="list-questions__bottom">
                                        <a class="btn-yellow-border fz-14" href="<?php echo $setting['script_url'];?>/lk/answers/<?php echo $answer['id'];?>?user=<?php echo $answer['user_id'];?>&lesson=<?php echo $answer['lesson_id'];?>">Ответить</a>
                                        <a class="list-questions__remove" onclick="return confirm('Вы уверены?')" href="<?php echo $setting['script_url'];?>/lk/answers/deldialog/<?php echo $answer['user_id'];?>/<?php echo $answer['lesson_id'];?>" title="Удалить"><span class="icon-remove"></span></a>
                                    </div>
                                </div>

                                <!--div class="task-accepted">
                                    <?php if($answer['status'] == 0){?>
                                    <form action="" method="POST">
                                        <input type="hidden" name="lesson_id" value="<?php echo $answer['lesson_id'];?>">
                                        <input type="hidden" name="user_id" value="<?php echo $answer['user_id'];?>">
                                        <input type="submit" name="accept" class="noaccepted d-flex" value="Принять">
                                    </form>
                                    <?php } else echo '<span class="accepted d-flex"><i class="icon-check"></i> Принято</span>';?>
                                </div-->
                            </div>
                        <?php endforeach; ?>

                    <?php } else echo 'Нет ответов';?>

                    </div>
                    <?php if(isset($is_pagination) && $is_pagination == true) echo $pagination->get(); ?>
                </div>
                <aside class="sidebar">

                    <div class="filter">
                        <form action="" method="POST">
                            <h4><?=System::Lang('FILTER');?></h4>
                            <div class="one-filter">
                                <h5 class="one-filter__title"><?=System::Lang('COURSE');?></h5>
                                <div class="select-wrap">
                                    <select name="course_id">
                                        <option value=""><?=System::Lang('CHOOSE_COURSE');?></option>
                                        <?php $course_list = Course::getCourseListFromSitemap();
                                        if($course_list):
                                            foreach($course_list as $course):?>
                                                <option value="<?php echo $course['course_id'];?>"<?php if(isset($_SESSION['course_id']) && $_SESSION['course_id'] == $course['course_id']) echo ' selected="selected"';?>><?php echo $course['name'];?></option>
                                            <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>
                            </div>
                            <div class="one-filter filter-lessons">
                                <h5 class="one-filter__title"><?=System::Lang('LESSON');?></h5>
                                <div class="select-wrap">
                                    <select name="lesson_id">
                                        <option value=""><?=System::Lang('CHOOSE_THE_LESSON');?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="filter-button">
                                <?php if(isset($_SESSION['course_id']) || isset($_SESSION['lesson_id'])):?>
                                <input class="link-blue" type="submit" value="Сбросить" name="reset">
                                <?php endif;?>
                                <input class="btn-blue-thin" type="submit" value="Выбрать" name="filter" disabled>
                            </div>
                        </form>
                    </div>

                    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar2.php');?>
                </aside>
               
            </div>
        </div>
    </div>
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>

    <script type="text/javascript">
    	setTimeout(function(){$('.success_message').fadeOut('fast')},4000);
    	
    	let lessonsFilter = function (course_id, lesson__id) {
    	  if (course_id != '') {
            $.ajax({
              url: '/courses/lessons/listfilter',
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
                    html += lesson__id != '' && lesson__id == lesson_id ? ' selected="selected"' : '';
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