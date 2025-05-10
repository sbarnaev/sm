<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body class="invert-page" id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>
    
    
    <div id="content">
        <div class="layout">
            <div class="content-wrap">
            <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                <div class="maincol-inner">
                    <?=$params['params']['aff_desc'];
                
                    if($show_aff):?>
                        <p><a class="button btn-yellow" href="/aff/reg"><?=System::Lang('BECOME_PARTER');?></a></p>
                    <?php endif;?>
                    
                    <?php if($show_cabinet):?>
                        <p><a class="btn-blue-thin" href="/lk"><?=System::Lang('LOG_IN_LK');?></a>
                            <br><span style="font-size: 12px"><?=System::Lang('ALREADY_REG');?></span>
                        </p>
                    <?php endif; ?>
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