<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body class="blog-page" id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>
    
    
    <div id="content">
        <div class="layout" id="blog">
            <ul class="breadcrumbs">
                <li><a href="/"><?=System::Lang('MAIN');?></a></li>
                <li><a href="/blog"><?=System::Lang('BLOG');?></a></li>
                <li><a href="/blog/<?php echo $rubric_data['alias'];?>"><?php echo $rubric_data['name'];?></a></li>
                <li> <?php echo $post['name'];?> </li>
            </ul>
            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">

                    <?php if($params['params']['show_cover'] == 1 && !empty($post['post_img']) && $post['show_cover']):?>
                    <div class="blog_img full">
                        <img src="/images/post/cover/<?php echo $post['post_img'];?>" alt="<?php echo $post['img_alt'];?>">
                    </div>
                    <?php endif;?>

                    <div class="blog-full">
                    <h1 class="post_title"><?php echo $post['name'];?></h1>

                    <div class="post_info">
                        <?php if($params['params']['show_create_date'] == 1):?>
                        <span class="small"><?php echo date("d.m.Y", $post['create_date']);?>
                            <?php if($params['params']['show_cat'] == 1) echo ' | ';?></span>
                        <?php endif;?>
						
						<?php if($params['params']['show_start_date'] == 1):?>
                        <span class="small"><?php echo date("d.m.Y", $post['start_date']);?>
                            <?php if($params['params']['show_cat'] == 1) echo ' | ';?></span>
                        <?php endif;?>

                        <?php if($params['params']['show_cat'] == 1):?>
                        <span class="small"> <?php $rubr = Blog::getRubricDataByID($post['rubric_id']);?><?=System::Lang('CATEGORY');?><a href="/blog/<?php echo $rubr['alias'];?>"><?php echo $rubr['name'];?></a></span>
                        <?php endif;?>

                    </div>

                    <?php echo System::renderContent($post['text']);?>
                    <?php echo $post['custom_code'];?>

                    <?php if($comments): ?>
                    <div class="comment_wrapper">
                        <?php echo $params['params']['commentcode'];?>
                    </div>
                    <?php endif;?>
                    </div>
                </div>
                <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
            </div>

        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>