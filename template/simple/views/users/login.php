<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');?>

<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>
    
    <div id="content" class="<?php if(!$sidebar) echo 'content-lk';?>">
        <div class="layout" id="lk">
            <div class="<?php if($sidebar) echo 'content-wrap';?>">
                <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                    <div class="content-userbox">
                        <h1 class="text-center"><?=System::Lang('AUTHORIZATION');?></h1>
                        <?php if(isset($errors) && is_array($errors)):?>
                            <ul style="color:#9F6000;">
                                <?php foreach($errors as $error): ?>
                                    <li><?=$error;?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <form action="/login" method="POST">
                            <?php // РАСШИРЕНИЕ AUTOPILOT
                            if (System::CheckExtensension('autopilot', 1)) {
                                require_once (ROOT . '/extensions/autopilot/views/simple/vk-auth.php');
                            }?>

                            <div class="modal-form-line">
                                <script>document.write(window.atob("PGlucHV0IHR5cGU9ImVtYWlsIiBuYW1lPSJlbWFpbCIgcGxhY2Vob2xkZXI9IkUtbWFpbCIgcmVxdWlyZWQ9InJlcXVyZWQiPg=="));</script>
                            </div>

                            <div class="modal-form-line">
                                <input placeholder="Password" type="password" name="pass" required="requred">
                            </div>

                            <div class="modal-form-submit">
                                <input type="submit" value="<?=System::Lang('LOGIN');?>" class="btn-yellow-fz-16 d-block button" name="enter">
                            </div>
                        </form>

                        <div class="modal-form-forgot-wrap">
                            <?php if ($setting['enable_registration']):?>
                                <div class="modal-form-reg">
                                    <a href="/lk/registration"><?=System::Lang('REGISTRATION');?></a>
                                </div>
                            <?php endif;?>

                            <div class="modal-form-forgot">
                                <a href="/forgot"><?=System::Lang('FORGOT_PASSWORD');?></a>
                            </div>
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