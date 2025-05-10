<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
$metriks = null;
if(!empty($setting['yacounter'])) $ya_goal = "yaCounter".$setting['yacounter'].".reachGoal('REG_PARTNER');";
else $ya_goal = null;
if($setting['ga_target'] == 1) $ga_goal = "ga ('send', 'event', 'reg_partner', 'submit');";
else $ga_goal = null;
if(!empty($setting['yacounter']) || $setting['ga_target'] == 1) $metriks = ' onsubmit="'.$ya_goal.$ga_goal.' return true;"';
?>
<body class="invert-page" id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>
    
    
    <div id="content">
        <div class="layout">
            <div class="content-wrap">
            <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                <div class="maincol-inner">
                    <h1><?=System::Lang('BECOME_PARTER');?></h1>
                    <div class="userbox">
                    <?php if(isset($message)){?>
                    
                    <p><?php echo $message; ?></p>
                    <?php } else {?>
                    <form action="" method="POST"<?php echo $metriks;?>>
                        <p class="userbox-max-width"><label><?=System::Lang('YOUR_NAME');?></label><input type="text" name="name"></p>
                        <p class="userbox-max-width"><label><?=System::Lang('YOUR_EMAIL');?></label><script>document.write(window.atob("PGlucHV0IHR5cGU9ImVtYWlsIiBuYW1lPSJlbWFpbCIgcmVxdWlyZWQ9InJlcXVpcmVkIj4="));</script></p>
                        <p class="userbox-max-width"><label><?=System::Lang('DEVISE_PASSWORD');?></label><input type="text" name="pass" required="required"></p>
                        <p class="textarea-big textarea-big-max-width"><label><?=System::Lang('SMALL_ABOUT_YOU');?></label><textarea name="about" cols="53" rows="4"></textarea></p>
                        <p><label></label><input type="hidden" name="tm" value="<?php echo time();?>"><input type="submit" name="affreg" class="button btn-blue" value="Готово"></p>
                    </form>
                    
                    <?php } ?>
                </div>
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