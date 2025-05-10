<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body id="page">
    <?php
    
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>
    
    
    <div id="content">
        <div class="layout" id="gallery">
            <div class="maincol<?php if($sidebar) echo '_min';?>">
            <div class="breadcrumbs">
                <ul>
                    <li><a href="/"><?=System::Lang('MAIN');?></a></li>
                    <li> > </li>
                    <li><?=System::Lang('GALLERY');?></li>
                </ul>
            </div>
            <h1><?php echo $params['params']['title'];?></h1>
            <style>
			.gallery_categories {display:flex; justify-content:space-around; flex-wrap: wrap}
			.gal_cat {width:30%}
			</style>
            <div class="gallery_categories">
                <?php if($cat_list):
                foreach($cat_list as $cat):
                if($cat['parent_id'] == 0){?>
                <div class="gal_cat">
                    <h2><a href="/gallery/<?php echo $cat['alias'];?>"><?php echo $cat['cat_name']?></a></h2>
                    <?php if(!empty($cat['cat_cover'])):?>
                        <a href="/gallery/<?php echo $cat['alias'];?>">
						<img src="/images/gallery/cats/<?php echo $cat['cat_cover'];?>" alt="<?php echo $cat['cat_name'];?>">
						</a>
                    <?php endif;?>
                </div>
                <?php } 
                endforeach;
                endif;?>
            </div>
            
            </div>
            <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php');
    ?>
</body>
</html>