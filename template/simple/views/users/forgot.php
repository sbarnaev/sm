<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body class="invert-page" id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>
    
    <div id="content">
        <div class="layout" id="lk">
            <div class="content-wrap">
            <div class="maincol<?php if($sidebar) echo '_min';?>">
                <div class="login-userbox">
                    <h1><?=System::Lang('REMMEMBER_PASSWORD');?></h1>
                    <div class="userbox">
                        <?php if(isset($_GET['mess'])):?>
                            <p><?=$_GET['mess'];?></p>
                        <?php else:?>
                            <form action="" method="POST">
                                <div class="modal-form-line">
                                    <label><?=System::Lang('YOUR_EMAIL');?></label><script>document.write(window.atob("PGlucHV0IHR5cGU9ImVtYWlsIiBuYW1lPSJlbWFpbCIgcmVxdWlyZWQ9InJlcXVyZWQiPg=="));</script>
                                </div>

                                <div class="modal-form-submit text-right mb-0">
                                    <input type="submit" value="Вспомнить пароль" class="btn-yellow-fz-16 text-uppercase font-bold button" name="forgot">
                                </div>
                            </form>
                        <?php endif;?>
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