<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body class="blog-page" id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');

    if(isset($params['params']['hero']) && $params['params']['hero'] != null ):?>
        <style>
            .hero-wrap{
                min-height: 300px;
                height: <?=$params['params']['heroheigh'];?>px;
                background-position: <?=$params['params']['position']?>;
                background-size: cover;
            }
            .hero-wrap:before{
                opacity:<?=$params['params']['overlay']?>;
                background:<?=$params['params']['overlaycolor']?>;
            }

            .hero_header.h1 {color: <?=$params['params']['color']?>; font-size: <?=$params['params']['fontsize']?>px; }

            @media screen and (max-width: 640px),
            only screen and (max-device-width:640px) {
                .hero-wrap {height: <?=$params['params']['heromobileheigh'];?>px}
                .hero_header.h1 {font-size: <?=$params['params']['fontsize_mobile'];?>px}
            }
        </style>

        <div id="hero" class="hero-wrap old-hero-wrap" style="background-image: url(<?=$params['params']['hero'];?>)">
            <h1 class="layout hero_header h1"><?=$rubric['name'];?></h1>
        </div>
	<?php endif;?>
    
    <div id="content">
        <div class="layout" id="blog">
            <ul class="breadcrumbs">
                <li><a href="/"><?=System::Lang('MAIN');?></a></li>
                <li><a href="/blog"><?=System::Lang('BLOG');?></a></li>
                <li><?=$rubric['name'];?></li>
            </ul>

            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                    <?php if(!empty($rubric['short_desc'])) echo $rubric['short_desc'];?>

                    <?php if($post_list):
                        foreach($post_list as $post):
                            $rubric = Blog::getRubricAlias($post['rubric_id']);?>
                            <div class="blog_item">
                                <?php if(!empty($post['post_img'])):?>
                                <div class="blog_img">
                                <a href="/blog/<?=$rubric?>/<?=$post['alias'];?>">
                                    <img src="/images/post/cover/<?=$post['post_img'];?>" alt="<?=$post['img_alt'];?>"></a>
                                </div>
                                <?php endif;?>
                                <div class="intro">
                                    <h2 class="blog_item__title"><a href="/blog/<?=$rubric?>/<?=$post['alias'];?>"><?=$post['name'];?></a></h2>
                                    <div class="post_info">
                                    <?php if($params['params']['show_create_date'] == 1):?>
                                        <span class="small"><?=date("d.m.Y", $post['create_date']);?>
                                        <?php if($params['params']['show_cat'] == 1) echo ' | ';?></span>
                                    <?php endif;?>
									
									<?php if(isset($params['params']['show_start_date']) && $params['params']['show_start_date'] == 1):?>
                                        <span class="small"><?=date("d.m.Y", $post['start_date']).($params['params']['show_cat'] ? ' | ' : '');?></span>
									<?php endif;?>

                                    <?php if($params['params']['show_cat'] == 1):?>
                                        <span class="small"> <?php $rubr = Blog::getRubricDataByID($post['rubric_id']);?><?=System::Lang('CATEGORY');?> <a href="/blog/<?=$rubr['alias'];?>"><?=$rubr['name'];?></a></span>
                                    <?php endif;?>

                                    </div>
                                    <?=$post['intro'];?>
                                </div>
                                <div class="read_more__wrap">
                                    <a class="read_more btn-blue-thin" href="/blog/<?=$rubric?>/<?=$post['alias'];?>"><?=System::Lang('MORE');?></a>
                                </div>
                            </div>
                        <?php endforeach;
                    else:
                        echo 'Пока здесь нет записей';
                    endif;?>

                    <?php if($is_pagination == true) echo $pagination->get();?>
                </div>
                <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>