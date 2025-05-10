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
                <?php if($course['cat_id'] != 0):
                    $cat_data = Course::getCourseCatData($course['cat_id']);?>
                    <li><a href="/courses?category=<?php echo $cat_data['alias'];?>"><?php echo $cat_data['name'];?></a></li>
                <?php endif;?>
                <li><?php echo $course['name'];?></li>
            </ul>

            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min';?> content-with-sidebar">
                    <div class="one-course-top">
                        <h1><?php echo $course['name'];?></h1>
                        <?php echo $course['course_desc'];?>
                    </div>
        
                    <div class="lessons_list">
                    <?php if($lesson_list):
                        foreach($lesson_list as $lesson):

                            // Формируем URL для покупки доступа к уроку
                            switch ($lesson['type_access_buy']) {

                                case 3: // если ссылка
                                    $link_access = $lesson['link_access'];
                                    break;

                                case 2: // если лендинг продукта
                                    $product = Product::getProductData($lesson['product_access']);
                                    $link_access = '/catalog/'.$product['product_alias'];
                                    break;

                                case 1: // если страница заказа продукта
                                    $link_access = '/buy/'.$lesson['product_access'];
                                    break;

                                case 0: // если нет данных, то берём их из настроек курса
                                    if ($course['type_access_buy'] == 3) {
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
                            // НАЗВАНИЯ БЛОКОВ

                            if($lesson['block_id'] && ($block === null || $block != $lesson['block_id'])):
                                $block_name = Course::getBlockLessonName($lesson['block_id']);

                                if($block_name):
                                    if($block !== null && $block != $lesson['block_id']):?>
                                        </div></div>
                                    <?php endif;?>

                                    <div class='cut old_cut'>
                                        <div class='block-heading__click'>
                                            <div class='module-number'><?=System::Lang('MODULE');?></div>
                                            <h4 id="block_<?php echo $lesson['block_id'];?>" class='block-heading'><?php echo $block_name;?></h4>
                                        </div>
                                        <div style="" class="mini_cut old_mini_cut">
                                    <?php $prev_block = $lesson['block_id'];
                                endif;
                                $block = $lesson['block_id'];
                            elseif($block && $lesson['block_id'] == 0):
                                $block = null;?>
                                </div></div>
                            <?php endif;?>

                            <div class="lesson_item old_lesson_item">
                            <?php $access = Course::checkAcсessLesson($course, $lesson, $user_groups, $user_planes);
                                $complete_less = 0;
                                if ($map_items) {
                                    foreach($map_items as $item){
                                        if (in_array($lesson['lesson_id'], $item) && $item['status'] == 1) {
                                            $complete_less = 1;
                                        }
                                    }
                            }?>

                            <?php if(!empty($lesson['cover'])):
                                if (!$access && $lesson['sort'] == 1) {
                                    $lesson_url = $link_access;
                                } else {
                                    $lesson_url = $course['alias'].'/'.$lesson['alias'];
                                }?>
                                <a href="<?php echo $lesson_url;?>">
                                    <div class="lesson_cover">
                                        <img src="/images/lessons/<?php echo $lesson['cover'];?>" alt="<?php echo $lesson['img_alt'];?>"/>
                                    </div>
                                </a>
                            <?php endif;?>

                            
                            <div class="lesson_desc old_lesson_desc">
                                
                                <?php if($access && $complete_less == 1){?>
                                <div class="lesson-title-green-check">
                                    <a href="/courses/<?php echo $course['alias'];?>/<?php echo $lesson['alias'];?>"><?php echo $lesson['name'];?></a>
                                </div>
                                <?php } elseif($access && $complete_less == 0){ ?>
                                <div class="lesson-title-yellow-circle">
                                    <a href="/courses/<?php echo $course['alias'];?>/<?php echo $lesson['alias'];?>"><?php echo $lesson['name'];?></a>    
                                </div>
                                <?php } else { ?>
                                <script>
                                    function changeLink(url) {
                                      document.getElementById('accessLink').href=url;
                                      document.getElementById('accessLink').target="_blank";
                                    }
                                  </script>
                                <div class="lesson-title-lock">
                                    <a href="#ModalAccess" onclick="changeLink('<?php echo $link_access;?>')" data-uk-modal="{center:true}"><?php echo $lesson['name'];?></a>
                                </div>
                                
                                    <?php } ?>
                               <?php echo $lesson['less_desc'];?>
                            </div>
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

                            <?php $less_status = 1; require_once (__DIR__ . '/progressbar.php');?>
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
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
    
</body>
</html>