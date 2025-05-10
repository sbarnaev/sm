<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>
    
    
    <div id="content">
        <div class="layout">
            <div class="maincol<?php if($sidebar) echo '_min';?>">
                <div class="userbox">
                    <h1><?=System::Lang('EMAIL_CONFIRMED');?>!</h1>
                    <?=System::Lang('AUTHOR_ACCEPT_MESS');?>
                </div>
            </div>
            <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
            
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>