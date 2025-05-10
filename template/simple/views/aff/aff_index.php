<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>
    
    <div id="content">
        <div class="layout" id="lk">
            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                    <h1><?php if(isset($params['params']['title']) && !empty($params['params']['title'])) echo $params['params']['title']; else echo System::Lang('PARTNERSHIPLONG');?></h1>
                    <?php if(isset($_GET['success_reg'])):?>
                        <div class="success_message"><?=System::Lang('REG_IN_AFF_PROGRAM');?></div>
                    <?php endif;

                    if(isset($_GET['success'])):?>
                        <div class="success_message"><?=System::Lang('SAVED');?>!</div>
                    <?php endif;?>

                    <div class="tabs">
                        <ul>
                            <li id="tab1"><?=System::Lang('BASIC');?></li>
                            <li id="tab2"><?=System::Lang('PERTER_LINKS');?></li>
                            <li id="tab3"><?=System::Lang('SHORT_LINKS');?></li>
                            <li id="tab4"><?=System::Lang('PARTER_ORDERS');?></li> 
                            <li id="tab6"><?=System::Lang('ATTRACTED_PEOPLE');?></li>                       
                            <li id="tab5"><?=System::Lang('REQUISITES');?></li>
                            <li id="tab7"><?=System::Lang('INTEGRATION');?></li>
                        </ul>

                        <div class="userbox usertabs">
                            <?php require_once (__DIR__ . '/aff_cabinet/aff_main_tab.php');?>
                            <?php require_once (__DIR__ . '/aff_cabinet/aff_referral_tab.php');?>
                            <?php require_once (__DIR__ . '/aff_cabinet/aff_shortlink_tab.php');?>
                            <?php require_once (__DIR__ . '/aff_cabinet/aff_orders_tab.php');?>
                            <?php require_once (__DIR__ . '/aff_cabinet/aff_people_tab.php');?>
                            <?php require_once (__DIR__ . '/aff_cabinet/aff_requisites_tab.php');?>
                            <?php require_once (__DIR__ . '/aff_cabinet/aff_postbacks_tab.php');?>
                        </div>
                    </div>
                </div>
            
                <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
            </div>
        </div>
    </div>

    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
    
    <script type="text/javascript">
    	setTimeout(function(){$('.success_message').fadeOut('fast')},4000); 
    </script>
</body>
</html>