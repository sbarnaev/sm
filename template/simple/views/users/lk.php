<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
$is_show_cpbutton = CallPassword::isShowButton($user);?>

<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>

    <div id="content">
        <div class="layout" id="lk">
            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                    <div class="login-userbox" style="position:relative;">
                        <?php if ($is_show_cpbutton):?>
                            <div class="loader_box">
                                <div class="loader"></div>
                            </div>
                        <?php endif;?>

                        <h1 class="cource-head"><?=System::Lang('MY_PROFILE');?></h1>
                        <?php if(isset($_GET['success'])):?>
                            <div class="success_message">
                                <span class="icon-check"></span><?=System::Lang('USER_SUCCESS_MESS');?>
                            </div>
                        <?php endif;?>

                        <form action="" method="POST">
                            <div class="form-line"><label><?=System::Lang('YOUR_NAME');?></label>
                                <div class="form-line-input">
                                    <input type="text" name="name" value="<?=$user['user_name'];?>">
                                </div>
                            </div>

                            <?php if($setting['show_surname'] != 0):?>
                                <div class="form-line"><label><?=System::Lang('YOUR_SURNAME');?></label>
                                    <div class="form-line-input">
                                        <input type="text" name="surname" value="<?=$user['surname'];?>">
                                    </div>
                                </div>
                            <?php endif;?>

                            <div class="form-line"><label><?=System::Lang('YOUR_EMAIL');?></label>
                                <div class="form-line-input">
                                    <input type="email" disabled value="<?=$user['email'];?>">
                                </div>
                            </div>

                            <div class="form-line"><label><?=System::Lang('YOUR_PHONE');?></label>
                                <div class="form-line-input <?=$is_show_cpbutton ? ' button_right_box' : '';?>">
                                    <input type="text" name="phone" value="<?=$user['phone'];?>">
                                    <?php if ($is_show_cpbutton):?>
                                        <a id="cp_confirm" class="btn getlink btn-red button_right" href="javascript:void(0);"><?=System::Lang('CONFIRM');?></a>
                                    <?php endif;?>
                                </div>
                            </div>

                            <div class="form-line"><label><?=System::Lang('YOUR_TELEGRAM');?></label>
                                <?php $tg_link = Telegram::getLinkToBindAccount($user['user_id'], $user['nick_telegram']);?>
                                <div class="form-line-input<?=$tg_link ? ' button_right_box' : '';?>">
                                    <input type="text" name="nick_telegram" value="<?=$user['nick_telegram'];?>">
                                    <?php if($tg_link):?>
                                        <a id="tg_bind_account" data-link="<?=$tg_link;?>" class="btn getlink btn-red button_right" href="javascript:void(0);"><?=System::Lang('BIND');?></a>
                                    <?php endif;?>
                                </div>
                            </div>

                            <div class="form-line"><label><?=System::Lang('INSTAGRAM_NIK');?></label>
                                <div class="form-line-input">
                                    <input type="text" name="nick_instagram" value="<?=$user['nick_instagram'];?>">
                                </div>
                            </div>

                            <div class="form-line"><label><?=System::Lang('ADRESS_VK');?></label>
                                <div class="form-line-input">
                                    <input type="text" name="vk_url" value="<?=$user['vk_url'];?>">
                                </div>
                            </div>

                            <div class="form-line"><label><?=System::Lang('POL');?></label>
                                <div class="form-line-input">
                                    <div class="select-wrap">
                                        <select name="sex">
                                            <option value=""><?=System::Lang('NOT_SELECTED');?></option>
                                            <option value="male"<?php if($user['sex'] == 'male') echo ' selected="selected"';?>><?=System::Lang('MAN');?></option>
                                            <option value="female"<?php if($user['sex'] == 'female') echo ' selected="selected"';?>><?=System::Lang('WOMAN');?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-line"><label><?=System::Lang('BERTHDAY_DATE');?></label>
                                <div class="form-line-input">
                                    <div class="form-line-inner">
                                        <div class="form-line-inner-col">
                                            <div class="select-wrap">
                                                <select name="bith_day">
                                                    <option value=""><?=System::Lang('DAY');?></option>
                                                    <?php $day = 1;
                                                    while($day <= 31):?>
                                                        <option value="<?=$day;?>"<?php if($user['bith_day'] == $day) echo ' selected="selected"';?>>
                                                            <?=$day++;?>
                                                        </option>
                                                    <?php endwhile;?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-line-inner-col">
                                            <div class="select-wrap">
                                                <select name="bith_month">
                                                    <option value=""><?=System::Lang('MONTH');?></option>
                                                    <option value="1"<?php if($user['bith_month'] == 1) echo ' selected="selected"';?>><?=System::Lang('JAN');?></option>
                                                    <option value="2"<?php if($user['bith_month'] == 2) echo ' selected="selected"';?>><?=System::Lang('FEB');?></option>
                                                    <option value="3"<?php if($user['bith_month'] == 3) echo ' selected="selected"';?>><?=System::Lang('MAR');?></option>
                                                    <option value="4"<?php if($user['bith_month'] == 4) echo ' selected="selected"';?>><?=System::Lang('APR');?></option>
                                                    <option value="5"<?php if($user['bith_month'] == 5) echo ' selected="selected"';?>><?=System::Lang('MAY');?></option>
                                                    <option value="6"<?php if($user['bith_month'] == 6) echo ' selected="selected"';?>><?=System::Lang('JUN');?></option>
                                                    <option value="7"<?php if($user['bith_month'] == 7) echo ' selected="selected"';?>><?=System::Lang('JUL');?></option>
                                                    <option value="8"<?php if($user['bith_month'] == 8) echo ' selected="selected"';?>><?=System::Lang('AUG');?></option>
                                                    <option value="9"<?php if($user['bith_month'] == 9) echo ' selected="selected"';?>><?=System::Lang('SEP');?></option>
                                                    <option value="10"<?php if($user['bith_month'] == 10) echo ' selected="selected"';?>><?=System::Lang('OKT');?></option>
                                                    <option value="11"<?php if($user['bith_month'] == 11) echo ' selected="selected"';?>><?=System::Lang('NOV');?></option>
                                                    <option value="12"<?php if($user['bith_month'] == 12) echo ' selected="selected"';?>><?=System::Lang('DEC');?></option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-line-inner-col">
                                            <div class="select-wrap">
                                                <select name="bith_year">
                                                    <option value=""><?=System::Lang('YEAR');?></option>
                                                    <?php $year = 2012;
                                                    while($year > 1940):?>
                                                        <option value="<?=$year;?>" <?php if($user['bith_year'] == $year) echo ' selected="selected"';?>>
                                                            <?=$year--;?>
                                                        </option>
                                                    <?php endwhile;?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!--div class="form-line"><label>Индекс:</label><div class="form-line-input"><input type="text" name="zipcode" value="<?=$user['zipcode'];?>"></div></div>
                            <div class="form-line"><label>Город:</label><div class="form-line-input"><input type="text" name="city" value="<?=$user['city'];?>"></div></div>
                            <div class="form-line"><label>Адрес:</label><div class="form-line-input"><textarea cols="45" rows="3" name="address"><?=$user['address'];?></textarea></div></div-->
                            <div class="form-line-submit">
                                <input class="btn-yellow-fz-16 text-uppercase font-bold button" type="submit" name="update" value="<?=System::Lang('UPDATE_MYDATA');?>">
                            </div>
                        </form>

                        <div class="text-right" style="margin-top: 20px;">
                            <a href="/lk/changepass"><?=System::Lang('CHANGE_PASSWORD');?></a>
                        </div>
                    </div>
                </div>

                <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
            </div>
        </div>
    </div>

    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php');?>

    <script type="text/javascript">
    	setTimeout(function(){$('.success_message').fadeOut('fast')},4000);
    </script>

    <?php // Подключение скрипта для расширения Telegram
    if ($tg_link):?>
        <script type="text/javascript" src="/extensions/telegram/web/js/main.js"></script>
    <?php endif;

    if ($is_show_cpbutton):?>
        <script type="text/javascript" src="/extensions/callpassword/web/js/main.js"></script>
    <?php endif;?>
</body>
</html>