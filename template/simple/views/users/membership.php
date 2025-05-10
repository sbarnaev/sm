<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>
    
    <div id="content">
        <div class="layout" id="lk">
            <div class="content-wrap">
                <div class="minirow <?php if($sidebar) echo '_min content-with-sidebar';?>">
                    <?php  // Вывод уведомления CallPassword
                    if(CallPassword::isShowButton($user)):
                        require_once (ROOT . '/extensions/callpassword/views/show_notice.php');
                    endif;?>

                    <?php  // Вывод уведомления Telegram
                    if (Telegram::isShowButton($user['user_id'], $user['nick_telegram'])):
                        require_once (ROOT . '/extensions/telegram/views/show_notice.php');
                    endif;?>

                    <h1><?=System::Lang('MY_SUBSCRIPTIONS');?></h1>
                    
                    <?php if(isset($_GET['success'])):?>
                        <div class="success_message"><?=System::Lang('USER_SUCCESS_MESS');?></div>
                    <?php endif;?>
                    
                    <?php if(isset($_GET['fail'])):?>
                        <div class="warning_message"><?=System::Lang('ERROR_HAPPENED');?></div>
                    <?php endif;?>
                    
                    <?php if($myplanes):?>
                    <div class="pay_orders">
                        <div class="table-responsive">
                            <table class="pay-table pay-table-padding">
                                <tr>
                                    <th class="text-left"><?=System::Lang('PLAN_NAME');?></th>
                                    <th><?=System::Lang('VALID_UNTIL');?></th>
                                    <th><?=System::Lang('ACTION');?></th>
                                    <th><?=System::Lang('STATUS');?></th>
                                </tr>
                                <?php $recurrents = false;
								foreach($myplanes as $myplane):
									if($myplane['subscription_id'] != null) $recurrents = true;
                                    if ($myplane['status'] == 1) {
                                        $last_update = $myplane['last_update'];
                                    }
                                    $plane_data = Member::getPlaneByID($myplane['subs_id']);?>
                                    <tr>
                                        <td class="text-left" title="<?=$plane_data['subs_desc'];?>"><?=$plane_data['name'];?></td>

                                        <td><?=date("d.m.Y H:i:s", $myplane['end']);?></td>
                                        <td><?php if($myplane['status'] == 1 && $myplane['subscription_id'] != null && $myplane['recurrent_cancelled'] != 1):?>
                                                <a  onclick="return confirm('Вы уверены что хотите отменить подписку?')" href="<?=$setting['script_url'];?>/lk/membership?action=pause&id=<?=$myplane['id'];?>"><?=System::Lang('SUBSCRIPTION_CANCEL');?></a>
                                            <?php else:?>
                                                <span class="small"> </span>
                                            <?php endif;?>
                                        </td>
                                        
                                        <td><?php if($myplane['status'] == 1):?>
                                                <span class="status-act"><?=System::Lang('ACTIVE');?></span>
                                            <?php else:?>
                                                <span class="status-remove"><?=System::Lang('OFF');?></span>
                                            <?php endif;?>
                                        </td>
                                    </tr>
                                <?php endforeach;?>
                            </table>
                        </div>
                    </div>
                    
					<?php if($recurrents):?>
                    <div class="my-payments-section">
                        <h2><?=System::Lang('PAYMENTS');?></h2>
                        <div class="my-payments-date">
                            <div class="my-payments-date__left">
                                <?php if(!empty($last_update)):?>
                                    <p><?=System::Lang('LAST_PAYMENTS');?>  <?=date("d.m.Y H:i:s", $last_update);?></p>
                                <?php endif; ?>
                                    <?=System::Lang('BANK_CARD_SAVING');?>
                            </div>
                            <div class="my-payments-date__right">
                                <a href="/lk/orders" class="btn-blue-history"><?=System::Lang('PAYMENT_HISTORY');?></a>
                            </div>
                        </div>
                    </div>
					<?php endif;?>
                    <?php else:
                        echo 'Нет подписок';
                    endif;?>
                    <? /*
                    <h2>Отмененные подписки</h2>
                    <div class="pay_orders">
                        <div class="table-responsive">
                            <table class="pay-table pay-table-padding">
                                <tr>
                                    <th class="text-left">Название плана</th>
                                    <th>Действует до:</th>
                                    <th>Действие</th>
                                    <th>Статус</th>
                                </tr>
                                <?php foreach($myplanes as $myplane):
                                    $plane_data = Member::getPlaneByID($myplane['subs_id']);?>
                                <tr>
                                    <td class="text-left" title="<?=$plane_data['subs_desc'];?>"><?=$plane_data['name'];?></td>
    
                                    <td><?=date("d.m.Y H:i:s", $myplane['end']);?></td>
                                    <td><?php if($myplane['status'] == 1){?>
                                        <a  onclick="return confirm('Вы уверены что хотите отменить подписку?')" href="<?=$setting['script_url'];?>/lk/membership?action=pause&id=<?=$myplane['id'];?>">Отменить подписку</a>
                                        <?php } else {?>
    
                                        <?php } ?>
                                    </td>
                                    <td><?php if($myplane['status'] == 1) echo '<span class="status-act">активен</span>'; else echo '<span class="status-remove">Отключен</span>';?></td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
            */ ?>
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