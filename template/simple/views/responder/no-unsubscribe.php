<?php defined('BILLINGMASTER') or die;
$title = 'Отписка от рассылки';
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
                <?=System::Lang('THANKS_FOR_STAING');?>
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