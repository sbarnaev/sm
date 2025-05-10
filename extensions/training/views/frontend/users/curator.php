<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/frontend/head.php');?>
<body class="invert-page" id="page">
<?php require_once (ROOT . '/extensions/training/layouts/frontend/header.php');
require_once (ROOT . '/extensions/training/layouts/frontend/main_menu.php');?>

<div id="content" class="cabinet-lk">
    <div class="layout" id="lk">
        <h1><?=System::Lang('CURATOR_OFFICE');?></h1>

        <ul class="breadcrumbs">
            <li><a href="/"><?=System::Lang('MAIN');?></a></li>
            <li><a href="/lk"><?=System::Lang('PROFILE');?></a></li>
            <li><?=System::Lang('CURATOR_OFFICE');?></li>
        </ul>

        <div class="content-wrap rev-content-wrap">
            <div class="maincol<?php if($sidebar) echo '_min';?> content-with-sidebar">
                <div id="answers">
                    <?php if(isset($_GET['success'])):?>
                        <div class="success_message"><?=System::Lang('USER_SUCCESS_MESS');?></div>
                    <?php endif;

                    if($answer_list):?>
                        <?php foreach($answer_list as $answer):?>
                            <div class="list-questions">
                                <div class="list-questions__left">
                                    <?php $user_answer = User::getUserNameByID($answer['user_id']);?>
                                    <img src="<?=User::getAvatarUrl($user_answer, $setting);?>" alt="" />
                                </div>

                                <div class="list-questions__right">
                                    <div class="list-questions__top">
                                        <h4 class="list-questions__name">
                                            <a href="/lk/curator/answers/<?="{$answer['homework_id']}/{$answer['user_id']}/{$answer['lesson_id']}";?>"><?=$answer['user_name'];?><?=$answer['surname'] ? '&nbsp;'.$answer['surname'] : '';?></a>
                                        </h4>
                                        <?/*
                                        <?php if($answer['user_id'] != $curator_id):?>
                                        <div class="list-questions__email">
                                            <?=$answer['user_email'];?>
                                        </div>,
                                        <?php endif;?>
                                        */?>
                                        <div class="list-questions__time">
                                            <?php if($answer['type_items'] == 'answer' && isset($answer['date_user_send'])):?>
                                                <span><?=System::Lang('PASSED');?> <?=date("d.m.Y H:i:s", $answer['date_user_send']);?></span><span>#<?=$answer['homework_id'];?></span>
                                            <?php else:?>   
                                                <span><?=System::Lang('COMMENT_DATE');?> <?=date("d.m.Y H:i:s", $answer['create_date']);?></span><span>#<?=$answer['homework_id'];?></span>
                                            <?php endif;

                                            if ($answer['status'] == TrainingLesson::HOME_WORK_ACCEPTED && $answer['type_items'] == 'answer'):
                                                $data_accept = TrainingLesson::getLessonCompleteData($answer['lesson_id'], $answer['user_id']);?>
                                                <?php if(isset($data_accept)):?>
                                                    <span><?=System::Lang('CHECKED_OUT');?> <?=date("d.m.Y H:i:s", $data_accept['date']);?></span>
                                                <?php endif;
                                            endif;?>
                                        </div>
                                    </div>

                                    <ul class="list-questions__crumbs">
                                        <li><?=System::Lang('TRENNING');?> «<?=$answer['training_name'];?>»</li>
                                        <li><?=System::Lang('LESSON');?>: «<?=$answer['lesson_name'];?>»</li>
                                    </ul>

                                    <ul class="list-questions__status">
                                        <?php if ($answer['type_items'] == 'answer'):?>
                                            <?php if ($answer['status'] == TrainingLesson::HOME_WORK_ACCEPTED && $answer['type_items'] == 'answer'):?>
                                                <li class="status-prinyato"><i class="icon-check"></i></li>
                                            <?php elseif ($answer['status'] == TrainingLesson::HOME_WORK_DECLINE):?>    
                                                <li class="status-ne-sdan"><span><?=System::Lang('TEST_NOT_PASSED');?></span></li>
                                            <?php else:?>
                                                <li class="status-inoy"><span style="display: none"><?=System::Lang('ANOTHER_STATUS');?></span><span class="icon-dom-rab-asterisk"></span></li>
                                            <?php endif;?>
                                        <?/* else:?> Пока закомментируем тут комментарии они могут быть прочитаны или нет ...
                                            <li style="color: green;">Тут статус КОММЕНТАРИЯ НУЖЕН ли ?</li>*/?>
                                        <?php endif;?>
                                    </ul>

                                    <div class="list-questions__text">
                                        <?php if ($answer['type_items'] == 'answer'):?>
                                            <?=mb_substr(trim(strip_tags(html_entity_decode(base64_decode($answer['answer'])))), 0, 100);?>
                                        <?php else:?>
                                            <?=mb_substr(trim(strip_tags(html_entity_decode(base64_decode($answer['comment_text'])))), 0, 100);?>
                                        <?php endif;?>  
                                    </div>

                                    <?php if (!empty($answer['attach'])):?>
                                        <div class="list-questions__file">
                                            <?php foreach(json_decode($answer['attach'], true) as $attach):?>
                                                <a href="/load/hometask/?name=<?=urldecode($attach['name']);?>&history_id=<?=$answer['history_id']?>" target="_blank" download><i class="icon-attach-1"></i><?=$attach['name'];?></a>
                                            <?php endforeach?>
                                        </div>
                                    <?php endif?>

                                    <?php if (isset($answer['task_type']) && $answer['task_type'] > 1):?>
                                        <div class="test-result-show"> <?=System::Lang('TEST');?>:
                                            <?php if ($answer['test'] == '0'):?>
                                                <span style="color: #FFCA10;"><?=System::Lang('IN_PROCESS');?></span>
                                            <?php elseif ($answer['test'] == '1'):?>
                                                <span>
                                                    <span style="color: #5DCE59;"><?=System::Lang('DONE');?></span>
                                                    <a href="#ModalAccessTestAnswer" data-lesson_id="<?=$answer['lesson_id']?>" data-user_id="<?=$answer['user_id']?>"><?=System::Lang('LOOKED_ANSWERS');?></a>
                                                </span>
                                            <?php elseif ($answer['test'] == '2'):?>
                                                <span>
                                                    <span style="color: #E04265;"><?=System::Lang('NOT_PASSED');?></span> <a href="#ModalAccessTestAnswer" data-lesson_id="<?=$answer['lesson_id']?>" data-user_id="<?=$answer['user_id']?>"><?=System::Lang('LOOKED_ANSWERS');?></a>
                                                </span>

                                                <!-- Здесь кнопку на попытку даем только если тест не сдан (2-ой статус) -->
                                                <?php if($answer['test'] == '2'):?>
                                                    <a href="<?=$setting['script_url']?>/lk/curator/answers/<?="{$answer['homework_id']}/{$answer['user_id']}/{$answer['lesson_id']}?testtry";?>" class="test-result-show__button">
                                                        <i class="icon-repeat"></i><?=System::Lang('GAVE_ONE_MORE_TRY');?>
                                                    </a>
                                                <?php endif;?>

                                            <!-- Сейчас у нас есть просто принять ответ либо отклонить
                                            <a href="#" class="test-result-show__button"><i class="icon-skip"></i>Пропустить</a> -->
                                            <?php else:?>
                                                <span style="color: #E04265;"><?=System::Lang('NOT_START');?></span>
                                            <?php endif;?>
                                        </div>
                                    <?php endif;?>

                                    <div class="list-questions__file"></div>

                                    <div class="list-questions__bottom">
                                        <?php if ($answer['type_items'] == 'answer'):?>
                                            <div class="list-questions-type"><i class="icon-dom-rab"></i><?=System::Lang('HOME_WORK');?></div>
                                        <?php else:?>
                                            <div class="list-questions-type"><i class="icon-dom-rab-komment"></i><?=System::Lang('COMMENT');?></div>
                                        <?php endif;?>
                                    <div>

                                    <form id="accept" action="" method="POST" class="form-accept">
                                        <?php if($answer['type_items'] == 'answer'):
                                            if($answer['teacher']):?>
                                                 <?php if($answer['teacher'] == $answer['curator_id'] || $answer['curator_id'] == 0):?>
                                                    <div><?=System::Lang('CURATOR');?> <?=$answer['curator_name']?></div>
                                                <?php elseif($answer['curator_id']):?>
                                                    <div class="curator-name"><?=System::Lang('CHECKED_CURATOR');?> <?=User::getUserNameByID($answer['curator_id'])['user_name'];?></div>
                                                <?php endif;
                                            else:?>
                                                <div>
                                                    <?php if($answer['answer'] && $answer['status'] != TrainingLesson::HOME_WORK_ACCEPTED):?>
                                                        <label class="custom-checkbox assign-user">
                                                            <input type="checkbox" name="assign_user" value="1">
                                                            <span><?=System::Lang('CONNECT_TO_ME');?></span>
                                                        </label>
                                                    <?php endif;?>
                                                </div>
                                            <?php endif;

                                            if($answer['status'] == TrainingLesson::HOME_WORK_ACCEPTED):?>
                                                <?php if($answer['answer'] || $answer['attach'] || $answer['work_link']):?>
                                                    <a class="btn-green" href="<?=$setting['script_url']?>/lk/curator/answers/<?="{$answer['homework_id']}/{$answer['user_id']}/{$answer['lesson_id']}";?>"><?=System::Lang('LOOK');?></a>
                                                <?php endif;
                                            else:?>
                                                <ul class="accept-dz">
                                                    <li class="nav_gorizontal__parent-wrap">
                                                        <div class="nav_gorizontal__parent accept-dz-btn">
                                                            <input type="hidden" value="accept" name="accept">
                                                            <input type="hidden" value="<?=$answer['homework_id']?>" name="homework_id">
                                                            <input type="hidden" value="<?=$answer['user_id']?>" name="user_id">
                                                            <input type="hidden" value="<?=$answer['lesson_id']?>" name="lesson_id">
                                                            <input type="submit" name="answer_send" class="button-yellow-rounding accept-dz-submit" value="Принять">
                                                            <!-- <a href="javascript:;" onclick="document.getElementById('accept').submit();" class="button-yellow-rounding">Принять</a> -->
                                                            <span class="nav-click<?php if($answer['answer']) echo ' icon-down';?>"></span>
                                                        </div>

                                                        <?php if($answer['answer']):?>
                                                            <ul class="drop_down">
                                                                <?php if ($answer['auto_answer']):?>
                                                                    <li><a href="<?=$setting['script_url']?>/lk/curator/answers/<?="{$answer['homework_id']}/{$answer['user_id']}/{$answer['lesson_id']}?auto=1";?>"><?=System::Lang('ACCEPT_AND_ANSWER');?></a></li>
                                                                <?php endif;?>
                                                                <li><a href="<?=$setting['script_url']?>/lk/curator/answers/<?="{$answer['homework_id']}/{$answer['user_id']}/{$answer['lesson_id']}";?>"><?=System::Lang('GIVE_ANSWER');?></a></li>
                                                            </ul>
                                                        <?php endif;?>
                                                    </li>
                                                </ul>
                                            <?php endif;
                                        else:?>
                                            <a class="btn-green" href="<?=$setting['script_url']?>/lk/curator/answers/<?="{$answer['homework_id']}/{$answer['user_id']}/{$answer['lesson_id']}";?>"><?=System::Lang('GIVE_ANSWER');?></a>
                                        <?php endif;?>
                                    </form>
                                </div>
                            </div>
                                </div>
                            </div>
                        <?php endforeach;
                    endif;?>
                </div>
                <?php if(isset($pagination) && $pagination->amount>1) echo $pagination->get();?>
            </div>

            <aside class="sidebar">
                <div class="filter" id="filter">
                    <form action="/lk/curator#answers" method="POST">
                        <h4 class="filter-title"><?=System::Lang('RESULTS_FILTER');?> <?=$total;?></h4>

                        <div class="one-filter">
                            <div class="select-wrap">
                                <select name="training_id">
                                <option value=""<?php if(isset($filter) && $filter['training_id'] === null) echo ' selected="selected"';?>><?=System::Lang('ALL_TRENNINGS');?></option>
                                    <?php if($trainings_to_curator):
                                        foreach($trainings_to_curator as $training):?>
                                            <option value="<?=$training['training_id'];?>"<?php if(isset($filter) && $filter['training_id'] == $training['training_id']) echo ' selected="selected"';?>><?=$training['name'];?></option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
                        </div>

                        <?php if(isset($filter['training_id']) && $filter['training_id'] != 0):?>
                            <div class="one-filter">
                                <div class="select-wrap">
                                    <select name="lesson_id">
                                        <option value="0"><?=System::Lang('ALL_THE_LESSONS');?></option>
                                        <?php if(isset($lesson_list)):
                                            foreach($lesson_list as $lesson):?>
                                                <option value="<?=$lesson['lesson_id'];?>"<?php if(isset($filter) && $filter['lesson_id'] == $lesson['lesson_id']) echo ' selected="selected"';?>><?=$lesson['name'];?></option>
                                            <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>
                            </div>
                        <?php endif;?>

                        <div class="one-filter">
                            <div class="select-wrap">
                                <select name="curator_users">
                                    <option value="my_users"<?php if(isset($filter['curator_users']) && $filter['curator_users'] == 'my_users') echo ' selected="selected"';?>><?=System::Lang('MY_STUDENTS');?></option>
                                    <option value="all"<?php if(!isset($filter['curator_users']) || $filter['curator_users'] == 'all') echo ' selected="selected"';?>><?=System::Lang('ALL_STUDENTS');?></option>
                                    <?php if($user['role'] == 'admin' && $curators = User::getCurators()):?>
                                        <option value="choose_curator" data-show_on="filter_curator_id"<?php if(isset($filter['curator_users']) && $filter['curator_users'] == 'choose_curator') echo ' selected="selected"';?>><?=System::Lang('CHOOSE_THE_CURATOR');?></option>
                                    <?php endif;?>
                                </select>
                            </div>
                        </div>

                        <?php if($user['role'] == 'admin' && $curators):?>
                                <div class="one-filter hidden" id="filter_curator_id">
                                    <div class="select-wrap">
                                        <select name="curator_id">
                                            <?php foreach ($curators as $curator):?>
                                                <option value="<?=$curator['user_id']?>" <?php if(isset($filter['curator_id']) && $filter['curator_id'] == $curator['user_id']) echo ' selected="selected"';?>><?=trim("{$curator['surname']} {$curator['user_name']}")?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                </div>
                        <?php endif;?>

                        <div class="one-filter">
                            <div class="select-wrap">
                                <select name="answer_type">
                                    <option value="all"<?php if(!isset($filter['answer_type']) || !$filter['answer_type']) echo ' selected="selected"';?>><?=System::Lang('ANSWERS_AND_COMMENTS');?></option>
                                    <option value="only_answers"<?php if(isset($filter['answer_type']) && $filter['answer_type'] == "only_answers") echo ' selected="selected"';?> data-show_off="show_comments"><?=System::Lang('ANSWERS_ONLY');?></option>
                                    <option value="only_comments"<?php if(isset($filter['answer_type']) && $filter['answer_type'] == "only_comments") echo ' selected="selected"';?> data-show_off="show_lesson_status"><?=System::Lang('COMMENTS_ONLY');?></option>
                                </select>
                            </div>
                        </div>

                        <div id="show_lesson_status" class="one-filter">
                            <div class="select-wrap">
                                <select name="lesson_complete_status">
                                    <option value="unchecked"<?php if(!isset($filter['lesson_complete_status']) || $filter['lesson_complete_status'] == "unchecked") echo ' selected="selected"';?>><?=System::Lang('NEWS_ANSWERS');?></option>
                                    <option value="checked"<?php if(isset($filter['lesson_complete_status']) && $filter['lesson_complete_status'] == "checked") echo ' selected="selected"';?>><?=System::Lang('VERIFIED');?></option>
                                    <option value="all"<?php if(isset($filter['lesson_complete_status']) && $filter['lesson_complete_status'] == "all") echo ' selected="selected"';?>><?=System::Lang('ALL_ANSWERS');?></option>
                                </select>
                            </div>
                        </div>

                        <div id="show_comments" class="one-filter">
                            <div class="select-wrap">
                                <select name="comments_status">
                                    <option value="unread"<?php if(!isset($filter['comments_status']) || $filter['comments_status'] == "unread") echo ' selected="selected"';?>><?=System::Lang('UNREAD');?></option>
                                    <option value="read"<?php if(isset($filter['comments_status']) && $filter['comments_status'] == "read") echo ' selected="selected"';?>><?=System::Lang('READED');?></option>
                                    <option value="all"<?php if(isset($filter['comments_status']) && $filter['comments_status'] == "all") echo ' selected="selected"';?>><?=System::Lang('ALL_THE_COMMENTS');?></option>
                                </select>
                            </div>
                        </div>

                        <div class="one-filter">
                            <div class="datetimepicker-wrap">
                                <input type="text" name="start_date" class="datetimepicker" autocomplete="off" value="<?=isset($filter['start_date']) ? date('d.m.Y H:i', $filter['start_date']) : '';?>" placeholder="От">
                            </div>
                        </div>

                        <div class="one-filter">
                            <div class="datetimepicker-wrap">
                                <input type="text" name="finish_date" class="datetimepicker" autocomplete="off" value="<?=isset($filter['finish_date']) ? date('d.m.Y H:i', $filter['finish_date']) : '';?>" placeholder="До">
                            </div>
                        </div>

                        <div class="one-filter">
                            <input type="text" name="user_name" value="<?=isset($filter['user_name']) ? trim("{$filter['user_name']} {$filter['user_surname']}") : '';?>" placeholder="Имя">
                        </div>

                        <div class="one-filter">
                            <input type="text" name="user_email" value="<?=isset($filter['user_email']) ? $filter['user_email'] : '';?>" placeholder="E-mail">
                        </div>

                        <div class="filter-button">
                            <input class="btn-yellow filter-button-submit" type="submit" value="Выбрать" name="filter">
                            <?php if(isset($filter)):?>
                                <input class="link-blue" type="submit" value="Сбросить" name="reset">
                            <?php endif;?>
                        </div>
                    </form>
                </div>

                <?php require_once (ROOT . '/template/'.$this->setting['template'].'/layouts/sidebar2.php');?>
            </aside>
            
        </div>
    </div>
</div>

<div id="ModalAccessTestAnswer" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-2">
        <div class="userbox"></div>
    </div>
</div>

<?php require_once (ROOT . '/extensions/training/layouts/frontend/footer.php');
require_once (ROOT . '/extensions/training/layouts/frontend/tech-footer.php');?>

<link rel="stylesheet" type="text/css" href="/template/<?=$this->setting['template']?>/css/jquery.datetimepicker.min.css">
<script src="/template/<?=$this->setting['template']?>/js/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript">
  setTimeout(function(){$('.success_message').fadeOut('fast')},4000);
  $('.datetimepicker').datetimepicker({
    format: 'd.m.Y H:i'
  });
</script>
</body>
</html>