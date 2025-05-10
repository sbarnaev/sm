<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body class="invert-page" id="page">
    <?php if($page['tmpl'] == 1){
    
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>
    
    
    <div id="content">
        <div class="layout" id="landing">
            <ul class="breadcrumbs">
                <li><a href="/"><?=System::Lang('MAIN');?></a></li>
                <li><?php echo $page['name'];?></li>
            </ul>
            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                    <div class="maincol-inner">
                        <h1><?php echo $page['name'];?></h1>
                        <?php echo System::renderContent($page['content']);?>
                        <?php echo $page['custom_code'];?>
                    </div>
                </div>
                <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php');
    } else {
        
        echo $page['content'];
        require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php');
        
    }?>
</body>
</html>