<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Партнерские к выплате</h1>
        <div class="logout">
            <a href="/" target="_blank"><?=System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>
    
    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li>Партнерские к выплате</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap">
                <div class="nav_gorizontal__parent nav_gorizontal__parent-yellow">
                    <a href="javascript:void(0);" class="nav-click button-yellow-rounding">Список партнеров</a>
                    <span class="nav-click icon-arrow-down"></span>
                </div>

                <ul class="drop_down">
                    <li><a href="/admin/users?role=is_partner">Все партнёры</a></li>
                    <li><a href="/admin/aff/top/">ТОП партнёров</a></li>
                </ul>
            </li>
            <li><a class="button-yellow-rounding" href="/admin/aff/paystat/">Все выплаты</a></li>
            <li><a title="Общие настройки тренингов" class="settings-link" target="_blank" href="/admin/affsetting"><i class="icon-settings"></i></a></li>
        </ul>
    </div>

    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;?>

<script>
    $(document).ready(function(){
        $('.button_req').click(function () {
            $(this).next('.div_req').toggleClass('open');
        });
    });
</script>
<style>
    .button_req {text-decoration:none; border-bottom: 1px dashed #555; overflow: hidden;}
    .div_req {height:0; visibility:hidden; transition: 0.5s;}
    .div_req.open {height:250px; visibility:visible; padding:0.5em 0 0 0; transition: 0.5s}
</style>
	<form action="" method="POST">
        <div class="admin_form">
            <div class="order-filter-row">
                <div class="order-filter-1-4">
                    <input type="text" name="pid" value="" placeholder="ID партнёра">
                </div>
                <div class="order-filter-1-4">
                    <input type="text" name="order_id" value="" placeholder="ID заказа">
                </div>
                <div class="order-filter-1-4">
                    <input type="text" name="summ" value="" placeholder="Сумма">
                </div>
                <div class="order-filter-1-4">
                    <div class="datetimepicker-wrap">
                        <input type="text" class="datetimepicker" name="date" placeholder="Дата" value="<?= date('d.m.Y', time());?>" autocomplete="off">
                        <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                    </div>
                </div>
                <div class="order-filter-1-4">
                    <input class="button-blue-rounding" type="submit" name="add_transaction" value="Начислить">
                </div>
            </div>
        </div>
    </form>
    <div class="admin_form admin_form--margin-top">
        <div class="overflow-container">
            <table class="table">
                <thead>
                    <tr>
                        <? /* <th>ID</th> */ ?>
                        <th class="text-left">Партнёр</th>
                        <th>Заработано</th>
                        <th>Выплачено</th>
                        <th>К выплате</th>
                        <th>Выплатить</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php if($partners):
						$all = 0;
						$all_now = 0;
						$real_pay = 0;
                        foreach($partners as $partner):
                            $user = User::getUserNameByID($partner['user_id']);?>
                            <tr<?php if($user['status'] == 0) echo ' class="off"';?>>
                                <td class="text-left">
                                    <div>
                                        <a target="_blank" href="/admin/users/edit/<?=$partner['user_id'];?>"><?=$user['user_name'];?></a>
                                        <br />
                                        <span class="small"><?=$user['email']?></span>
                                        <br />
                                        <?php $req = Aff::getPartnerReq($partner['user_id']);
                                            $req = unserialize($req['requsits']);
                                        if($req):?>
                                        <a class="button_req" href="javascript:void(0);">Реквизиты</a>
                                        <div class="div_req">
                                        <?php $req_arr = explode("\r\n", $params['params']['req']);

                                            foreach($req_arr as $req_item):
                                                $req_item = explode("=", $req_item);
                                                if(!empty($req["$req_item[0]"])):?>
                                                    <div class="mb-10"><?=$req_item[1];?>
                                                        <?php if($req_item[0] != 'rs'):?>
                                                            <input type="text" value="<?php if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"];?>">
                                                        <?php else:?>
                                                            <div class="mb-5">Счёт: <input type="text" value="<?php if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"]['rs'];?>"></div>
                                                            <div class="mb-5">Банк: <input type="text" value="<?php if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"]['name'];?>"></div>
                                                            <div class="mb-5">БИК : <input type="text" value="<?php if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"]['bik'];?>"></div>
                                                            <div class="mb-5">ИНН : <input type="text" value="<?php if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"]['itn'];?>"></div>
                                                        <?php endif;?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php else:?>
                                            <span class="small">Реквизиты не указаны</span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td><a href="/admin/aff/userstat/<?=$partner['user_id'];?>" target="_blank"><?=$partner['summ'];?></a></td>
                                <td><?=$partner['pay'];?></td>
								
                                <td class="fz-16"><?= $itog = $partner['summ'] - $partner['pay'];?>
                                <?php if(!empty($params['params']['return_period'])){
                                    $date = time() - ($params['params']['return_period'] * 86400);
                                    $total2 = Aff::getUserTransactData($partner['user_id'], 'aff', $date);                                                                        
                                
                                    if(($total2['SUM(summ)'] - $partner['pay']) > 0) $real_pay = $total2['SUM(summ)'] - $partner['pay'];
                                    else $real_pay = 0;
                                    if($real_pay > 0) echo '<br /><span style="font-size:80%; color:green" title="К выплате на сегодня">'.$real_pay.'</span>';
                                }?></td>
								<?php $all = $all + $itog;
								$all_now = $all_now + $real_pay;?>
                                <td>
                                    <form id="pay<?=$partner['user_id'];?>" action="" method="POST">
                                        <input type="text" name="summ" required="required">
                                        <input type="hidden" name="partner" value="<?=$partner['user_id'];?>">
                                        <input class="button-green-rounding mt-5 d-block" type="submit" name="pay" value="Выплатить">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach;?>
							<tr><td colspan="2">Всего к выплате: <?php echo $all;?> <?php echo $setting['currency']?>
							<?php if(!empty($params['params']['return_period'])) echo '<br />на сегодня '.$all_now.' '.$setting['currency'];?></td>
							</tr>
                    <?php else:
                        echo 'Нет выплат';
                    endif;?>
                </tbody>
            </table>
        </div>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
<link rel="stylesheet" type="text/css" href="/template/admin/css/jquery.datetimepicker.min.css">
<script src="/template/admin/js/jquery.datetimepicker.full.min.js"></script>
<script>
  jQuery('.datetimepicker').datetimepicker({
    format:'d.m.Y H:i',
    lang:'ru'
  });
</script>
</body>
</html>