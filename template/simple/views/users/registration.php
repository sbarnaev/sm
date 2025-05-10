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
                <div class="login-userbox">
                    <h1><?=System::Lang('REGISTRATION');?></h1>
                    <?php if(isset($_SESSION['reg_status'])):?>
                        <div class="userbox">
                            <?php if($_SESSION['reg_status'] == 1):?>
                                <p><?=System::Lang('APPROVAL_EMAIL');?>
                                </p>
                            <?php elseif($_SESSION['reg_status'] == 2):?>
                                <?=System::Lang('LINK_LOGED_SUCCESSFULL');?>
                            <?php endif;?>
                        </div>
                    <?php else:?>
                        <?php if(User::hasError()) User::showError('warning_message');?>

                        <form action="" method="POST">
                            <div class="form-line"><label><?=System::Lang('YOUR_NAME');?></label>
                                <div class="form-line-input">
                                    <input required type="text" name="name" value="<?=isset($name) ? $name : '';?>">
                                </div>
                            </div>

                            <?php if($setting['show_surname'] != 0):?>
                                <div class="form-line"><label><?=System::Lang('YOUR_SURNAME');?></label>
                                    <div class="form-line-input">
                                        <input required type="text" name="surname" value="<?=isset($surname) ? $surname : '';?>">
										<input type="hidden" name="fio">
                                    </div>
                                </div>
                            <?php endif;?>

                            <div class="form-line"><label><?=System::Lang('YOUR_EMAIL');?></label>
                                <div class="form-line-input">
                                    <?php if($setting['email_protection']):?>
										<script>document.write(window.atob("PGlucHV0IHR5cGU9ImVtYWlsIiBuYW1lPSJlbWFpbCI="));</script> required="required" pattern="^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,})+$">
									<?php else:?>
										<input type="email" name="email" required="required" pattern="^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,})+$">
									<?php endif;?>
                                </div>
                            </div>

                            <div class="form-line"><label><?=System::Lang('YOUR_PHONE');?></label>
                                <?php $is_show_cpbutton = System::CheckExtensension('callpassword', 1);?>
                                <div class="form-line-input <?=$is_show_cpbutton ? ' button_right_box' : '';?>">
                                    <input  required type="text" name="phone" value="<?=isset($phone) ? $phone : '';?>">
                                    <?php if ($is_show_cpbutton):?>
                                        <a id="cp_confirm" class="btn getlink btn-red button_right" href="javascript:void(0);"><?=System::Lang('CONFIRM');?></a>
                                    <?php endif;?>
                                </div>
                            </div>

                            <div class="form-line"><label><?=System::Lang('YOUR_PASSWORD');?></label>
                                <div class="form-line-input">
                                    <input required minlength="6" type="password" name="pass" value="<?=isset($pass) ? $pass : '';?>">
									<input type="hidden" name="time" value="<?php echo $timer;?>">
									<?php $cookie = $setting['cookie'];
									if(isset($_COOKIE["$cookie"])):?>
									<input type="hidden" name="sign" value="<?php echo md5($timer.'+'.$setting['secret_key']);?>">
									<?php endif;?>
                                </div>
                            </div>

                            <div class="form-line"><label><?=System::Lang('CONFIRM_PASSWORD');?></label>
                                <div class="form-line-input">
                                    <input required minlength="6" type="password" name="confirm_pass" value="<?=isset($confirm_pass) ? $confirm_pass : '';?>">
                                </div>
                            </div>

                            <div class="form-line-submit">
                                <input class="btn-yellow-fz-16 text-uppercase font-bold button" type="submit" name="save" value="Зарегистрироваться">
                            </div>
                        </form>
                    <?php endif;?>
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

<?php if (isset($is_show_cpbutton) && $is_show_cpbutton):?>
    <script type="text/javascript" src="/extensions/callpassword/web/js/main.js"></script>
<?php endif;?>
</body>
</html>