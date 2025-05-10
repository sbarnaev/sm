<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>
    
    <div id="content">
        <div class="layout" id="lk">
            <div class="content-wrap">
            <div class="maincol<?php if($sidebar) echo '_min';?>">
                <h1><?=System::Lang('PASSWORD_RESTORED');?></h1>
                <div class="userbox">
                    <?=System::Lang('LINK_CHANGED_PASSWORD');?>
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