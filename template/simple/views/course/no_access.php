<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>
    
    <?php // Формируем URL для покупки доступа к уроку
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
            if($course['type_access_buy'] == 3) $link_access = $course['link_access'];
            elseif($course['type_access_buy'] == 2){
                $product = Product::getProductData($course['product_access']);
                $link_access = '/catalog/'.$product['product_alias'];
            }
            elseif($course['type_access_buy'] == 1){
                $link_access = '/buy/'.$course['product_access'];
            }
            else $link_access = '';
            break;
                
        }?>
    
    <div id="content">
        <div class="layout" id="courses">
            <ul class="breadcrumbs">
                <li><a href="/"><?=System::Lang('MAIN');?></a></li>
                <li><a href="/courses"><?=System::Lang('ONLINE_TRAINING');?></a></li>
                <li><a href="/courses/<?php echo $course['alias'];?>"><?php echo $course['name'];?></a></li>
                <li><?php echo $lesson['name'];?></li>
            </ul>
            <div class="content-wrap">

                <div class="maincol<?php if($sidebar) echo '_min';?> content-with-sidebar">
                    <h1><?php echo $course['name'];?> : <?php echo $lesson['name'];?></h1>
                    <h3><?=System::Lang('NO_ACCESS_LESSON');?></h3>
                    <?php if($access && $open_time > 0):?>
                    <p><?=System::Lang('HOURS_ACCESS');?> <?php echo round(($open_time - $date)/3600);?></p>
                    <?php endif;?>
					
					<?php if(!$user):?>
                    <p><?=System::Lang('LOGIN_FAULT');?> <a href="#modal-login" data-uk-modal="{center:true}"><?=System::Lang('SITE_LOGIN');?></a>.</p>
					<?php endif;?>
					
					<?php if(!$access):?>
                    <p><?=System::Lang('GET_LINK_ACCESS');?> <a href="<?php echo $link_access;?>" target="_blank"><?=System::Lang('GET_SITE_ACCESS');?></a></p>
					<?php endif;?>
					
                    
                </div>
            <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>

            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>