<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>
    
    
    <div id="content">
        <div class="layout" id="blog">
            <div class="maincol<?php if($sidebar) echo '_min';?>">
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="/"><?=System::Lang('MAIN');?></a></li>
                        <li> > </li>
                        <li><a href="/gallery"><?=System::Lang('GALLERY');?></a></li>
                        <li> > </li>
                        <li><?php echo $cat['cat_name'];?></li>
                    </ul>
                </div>
                
                <h1><?php echo $cat['cat_name'];?></h1>
                
                <?php if($subcat_list):?>
                <div class="gallery_categories">
                    <?php
                    foreach($subcat_list as $subcat):?>
                    
                    <div class="gal_cat">
                        <h2><a href="/gallery/<?php echo $alias;?>/<?php echo $subcat['alias'];?>"><?php echo $subcat['cat_name']?></a></h2>
                        <?php if(!empty($cat['cat_cover'])):?>
                            <a href="/gallery/<?php echo $alias;?>/<?php echo $subcat['alias'];?>">
							<img src="/images/gallery/cats/<?php echo $subcat['cat_cover'];?>" alt="<?php echo $subcat['cat_name'];?>">
							</a>
                        <?php endif;?>
                    </div>
                    <?php endforeach;?>
                </div>
                <?php endif; ?>
                
                <?php if($img_list):?>
                <div id="gallery">
                    <?php if($img_list):
                    foreach($img_list as $img):?>
                    
                    <?php if(!empty($img['link'])):?>
                    <a href="<?php echo $img['link'];?>">
                    <?php endif;?>
            		<img alt="<?php echo $img['alt'];?>"
            		     src="/images/gallery/thumb/<?php echo $img['file'];?>"
            		     data-image="/images/gallery/<?php echo $img['file'];?>"
            		     data-description="<?php echo $img['item_desc'];?>"
            		     style="display:none">
            		<?php if(!empty($img['link'])) echo '</a>'?>
                    <?php endforeach;
                    endif;?>
                </div>
                <?php endif;?>
                
                
            </div>
            <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
    
</body>
</html>