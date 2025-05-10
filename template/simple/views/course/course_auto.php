<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>

    <style>
        .lesson_cover{width: <?php echo $params['params']['width_less_img'];?>px}
        <?php if(isset($params['params']['show_blocks']) && $params['params']['show_blocks'] == 0):?>
        .module-number {display:none}
        <?php endif;?>
    </style>
    
    <div id="content">
        <div class="layout" id="courses">
            <ul class="breadcrumbs">
                <li><a href="/"><?=System::Lang('MAIN');?></a></li>
                <li><a href="/courses"><?=System::Lang('ONLINE_COURSES');?></a></li>
                <li><?php echo $course['name'];?></li>
            </ul>
            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min';?> content-with-sidebar">
                    <div class="one-course-top">
                        <h1><?php echo $course['name'];?></h1>
                        <?php echo $course['course_desc'];?>
                    </div>
                
                <?php // Вычисление следующего доступного урока 
                // Считаем кол-во пройденых уркоов в автотренинге и +1 = получаем sort следующего
                $next = 0;
                /*if($map_items){
                    $i = 0;
                    foreach($map_items as $m_item){
                        $less[$i] = Course::getSortLessByID($m_item['lesson_id']);
                        $i++;
                    }
                    echo $next = max($less); // Номер следующего открытого урока
                    //echo $next = $i + 1;
                    
                }*/
                if(isset($count_map_items)) $next = $count_map_items + 1;?>

                <div class="lessons_list">
                <?php if($lesson_list):
                    foreach($lesson_list as $lesson):
                        // Формируем URL для покупки доступа к уроку
                        switch($lesson['type_access_buy']){

                            // если ссылка
                            case 3:
                                $link_access = $lesson['link_access'];
                                break;

                            // если лендинг продукта
                            case 2:
                                $product = Product::getProductData($lesson['product_access']);
                                $link_access = '/catalog/'.$product['product_alias'];
                                break;

                            // если страница заказа продукта
                            case 1:
                                $link_access = '/buy/'.$lesson['product_access'];
                                break;

                            // если нет данных, то берём их из настроек курса
                            case 0:
                            if($course['type_access_buy'] == 3) {
                                $link_access = $course['link_access'];
                            } elseif($course['type_access_buy'] == 2) {
                                $product = Product::getProductData($course['product_access']);
                                $link_access = '/catalog/'.$product['product_alias'];
                            } elseif($course['type_access_buy'] == 1) {
                                $link_access = '/buy/'.$course['product_access'];
                            } else {
                                $link_access = '';
                            }
                            break;
                        }

                        // Проверка доступа к уроку
                        $access = Course::checkAcсessLesson($course, $lesson, $user_groups, $user_planes);
                        $open = $lesson['sort'] <= $next ? true : false;

                        // НАЗВАНИЯ БЛОКОВ
    
                        if($lesson['block_id'] != null):
                            if ($block === null) {
                                $block_name = Course::getBlockLessonName($lesson['block_id']);
                                if ($block_name) {
                                    echo "<div class='cut old_cut'><div class='block-heading__click'><div class='module-number'>модуль</div><h4 id='block_".$lesson['block_id']."' class='block-heading'>$block_name</h4></div>
                                    <div style='' class='mini_cut old_mini_cut'>";
                                    $prev_block = $lesson['block_id'];
                                }
                            }?>
    
                            <?php if ($block !== null && $block != $lesson['block_id']) {
                                $block_name = Course::getBlockLessonName($lesson['block_id']);
                                if ($block_name) {
                                    echo "</div></div>
                                <div class='cut old_cut'><div class='block-heading__click'><div class='module-number'>модуль</div><h4 id='block_".$lesson['block_id']."' class='block-heading'>$block_name</h4></div>
                                    <div style='' class='mini_cut old_mini_cut'>";
                                    $prev_block = $lesson['block_id'];
                                }
                            } ?>
    
                        <?php $block = $lesson['block_id'];
                            endif; ?>
                    
                    <div class="old_lesson_item lesson_item<?php if($lesson['sort'] != 1 && !$open) echo ' no_access';?>">

                        <?php if(!empty($lesson['cover'])):
                        if(!$access && $lesson['sort'] == 1) $lesson_url = $link_access;
                        elseif(!$access && $open) $lesson_url = $link_access;
                        else $lesson_url = $course['alias'].'/'.$lesson['alias'];
                        
                        ?>
                        <a>
                        <div class="lesson_cover">
                            <img src="/images/lessons/<?php echo $lesson['cover'];?>" alt="<?php echo $lesson['img_alt'];?>"/>
                        </div>
                        <?php endif;?>
                        </a>
                        
                        <div class="lesson_desc old_lesson_desc">
                            <?php $complete_less = 0;
                                if($map_items){
                                    foreach($map_items as $item){
                                        
                                        if(in_array($lesson['lesson_id'], $item)) {
                                            if($item['status'] == 1) {
                                                $complete_less = 1;
                                                continue;   
                                            }
                                        }
                                    }
                                }
                            ?>
                            
                            <!--h3-->
                            <?php if(!$access && $lesson['sort'] == 1){ // Если доступа нет и это Первый урок
                                // то даём ссылку на покупку ?>
                                <script>
                                    function changeLink(url) {
                                      document.getElementById('accessLink').href=url;
                                      document.getElementById('accessLink').target="_blank";
                                    }
                                  </script>
                                <div class="lesson-title-yellow-circle"><a onclick="changeLink('<?php echo $link_access;?>')" data-uk-modal="{center:true}" href="#ModalAccess"><?php echo $lesson['name'];?></a></div>
                            
                            <?php } elseif (!$access && $open) {?>
                                <script>
                                    function changeLink(url) {
                                      document.getElementById('accessLink').href=url;
                                      document.getElementById('accessLink').target="_blank";
                                    }
                                  </script>
								<div class="lesson-title-yellow-circle"><a onclick="changeLink('<?php echo $link_access;?>')" data-uk-modal="{center:true}" href="#ModalAccess"><?php echo $lesson['name'];?></a></div>
                            
                            <?php }  elseif (!$open && $lesson['sort']!= 1){ // Если не следующий урок и это НЕ первый урок
                                // Ссылка не активна ?>
                                <div class="lesson-title-lock"><?php echo $lesson['name'];?></div><br>
                                <div class="lesson_desc__text old-lesson_desc__text"><span class="no_less_access"><?=System::Lang('TAKE_LAST_COURSE_FOR_ACCESS');?></span></div>
                            <?php } elseif($access && !$complete_less){ // если доступ есть и урок не пройден
                                // Даём ссылку на урок ?>
                                <div class="lesson-title-yellow-circle"><a href="/courses/<?php echo $course['alias'];?>/<?php echo $lesson['alias'];?>"><?php echo $lesson['name'];?></a></div>
                            <?php } elseif($access && $complete_less && $open) {?>
                                <div class="lesson-title-green-check"><a href="/courses/<?php echo $course['alias'];?>/<?php echo $lesson['alias'];?>"><?php echo $lesson['name'];?></a></div>
                            <?php }?>
                            <?php //echo 'open = '. $open .'<br />access = '.$access;?>
                            <!--/h3-->

                            <?php echo $lesson['less_desc'];?>
                        </div>

                        <? /*
                        <div class="lesson_info">
                            <ul>
                            <?php if($course['show_hits'] == 1):?>
                            <li class="less_hits" title="Просмотры"><i class="fa fa-eye" aria-hidden="true"></i> <?php echo $lesson['hits'];?></li>
                            <?php endif; ?>
                            
                            <?php if($course['show_comments'] == 1):?>
                            <li class="less_comments" title="Комментарии"><i class="fa fa-comments" aria-hidden="true"></i> 0</li>
                            <?php endif; ?>
                            
                            <?php if($map_items){
                                
                                foreach($map_items as $item){
                        
                                    if($lesson['lesson_id'] == $item['lesson_id']) {
                                        if($item['status'] == 1) echo '<li><span class="less_complete">Пройден</span></li>';
                                        else echo '<li><span class="less_waiting">На проверке</span></li>';
                                    }
                                }
                                
                                }?>
                            
                            </ul>
                        </div>
                        */ ?>
                    </div>
                <?php endforeach;
                    if(!empty($block)) echo '</div></div>';
                    endif;?>
                </div>
                
            </div>
            <aside class="sidebar">
                    <div class="current-course">
                        <div class="current-course-img">
                            <?php if(!empty($course['cover'])):?>
                            <img src="/images/course/<?php echo $course['cover'];?>" alt="<?php echo $course['img_alt'];?>" <?php if(!empty($course['padding'])):?>style="padding: <?php echo $course['padding']?>;"<?php endif;?>>
                            <?php endif; ?>
                        </div>
                        <div class="current-course-inner">
                            <div class="current-course-top">
                                <h4 class="current-course-title"><?php echo $course['name'];?></h4>
                                <?php if(!empty($course['author_id'])):?><p class="course_author"><?=System::Lang('AUTHOR');?> <?php $author_name = User::getUserNameByID($course['author_id']); echo $author_name['user_name'];?></p> <?php endif;?>
                            </div>

                            <?php $less_status = null; require_once (__DIR__.'/progressbar.php');?>
                        </div>
                    </div>
                    
                    <?php if($sidebar):
                    $widget_arr = $sidebar; 
                    require(ROOT . '/template/'.$setting['template'].'/widgets/widget_wrapper.php'); 
                    endif; ?>
                    <?php //require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
                </aside>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>

        <div id="ModalAccess" class="uk-modal">
            <div class="uk-modal-dialog">
                <div class="userbox modal-userbox-2">
                    <a href="#close" title="Закрыть" class="uk-modal-close uk-close modal-close"><span class="icon-close"></span></a>
                    <div class="box1">
                        <h3 class="modal-head-2"><?=System::Lang('COURSE_NOT_ACCESSED');?></h3>
                        <p><?=System::Lang('ACCESSED_COURSE');?><?php if(isset($is_auth) && !$is_auth ):?> <?=System::Lang('SITE_AUTHORIZE');?><?php endif;?>.</p>
                        <div class="group-button-modal">
                            <a class="button btn-yellow" id="accessLink" href="#"><?=System::Lang('GET_ACCESS');?></a>
                            <?php if(isset($is_auth) && !$is_auth ):?> <a class="btn-blue-border" href="#modal-login" data-uk-modal="{center:true}"> <?=System::Lang('SITE_LOGIN');?></a><?php endif;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</body>
</html>