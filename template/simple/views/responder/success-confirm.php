<?php defined('BILLINGMASTER') or die;
$title = 'Подтверждение подписки';
$meta_desc = '';
$meta_keys = '';
$use_css = 1;
$is_page = 'responder'; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body class="invert-page" id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>
    
    
    <div id="content">
        <div class="layout" id="responder">
            <div class="content-wrap">
            <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                <div class="maincol-inner">
                <?php if(empty($delivery['after_confirm_text'])){?>
                    <?=System::Lang('YOUR_EMAIL_SUCCESSFULLY_CONFIRM');?>
                <?php } else echo $delivery['after_confirm_text'];?>
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