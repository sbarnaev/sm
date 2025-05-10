<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>
    
    <div id="content">
        <div class="layout" id="author">
            <div class="content-wrap">
            <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                <h1><?=System::Lang('AUTHOR_CAB');?></h1>
                <?php if(isset($_GET['success_reg'])) echo '<div class="success_message">Вы зарегистрированы в партнёрской программе</div>';?>
                <?php if(isset($_GET['success'])) echo '<div class="success_message">Сохранено!</div>';?>
                <div class="tabs">
                    <ul>
                        <li><?=System::Lang('SUMMARY');?></li>
                        <li><?=System::Lang('ACCRUALS');?></li>                      
                        <li><?=System::Lang('REQUISITES');?></li>
                    </ul>
                    <div class="userbox usertabs">
                    
                        <!--  Основное  -->
                        <div>
                            <!--p>HTML таблица</p>
                            <div class="table-responsive">
                                <table class="usertable table-text-center">
                                    <tr>
                                        <th></th>
                                        <th>Сегодня</th>
                                        <th>Вчера</th>
                                        <th>Неделя</th>
                                        <th>Месяц</th>
                                        <th>Прошлый месяц</th>
                                        <th>Год</th>
                                    </tr>
                                    <tr>
                                        <td><strong>Заказов</strong></td>
                                        <td>1</td>
                                        <td>10</td>
                                        <td>10</td>
                                        <td>10</td>
                                        <td>10</td>
                                        <td>10</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Оплаченных заказов</strong></td>
                                        <td>0</td>
                                        <td>2</td>
                                        <td>2</td>
                                        <td>2</td>
                                        <td>2</td>
                                        <td>2</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Сумма комиссий</strong></td>
                                        <td>0 р.</td>
                                        <td>1100 р.</td>
                                        <td>1100 р.</td>
                                        <td>1100 р.</td>
                                        <td>1100 р.</td>
                                        <td>1100 р.</td>
                                    </tr>
                                </table>
                            </div-->
                            <div class="total-money">
                            <h4><?=System::Lang('TOTAL_EARNED');?> <?php if($total['SUM(summ)'] > 0) echo $total['SUM(summ)']; else echo 0;?> <?php echo $setting['currency'];?></h4>
                            <p><?=System::Lang('PAID_OUT');?> <?php if($total['SUM(pay)'] > 0) echo $total['SUM(pay)']; else echo 0;?> <?php echo $setting['currency'];?></p>
                            <p><?=System::Lang('OWNED');?> <?php if($total['SUM(summ)'] - $total['SUM(pay)'] > 0) echo $total['SUM(summ)'] - $total['SUM(pay)']; else echo 0;?> <?php echo $setting['currency'];?></p>
                            </div>
                        </div>
                        
                        <!--  Начисления  -->         
                        <div>
                            <p><?=System::Lang('LAST_FIFTY_ACCUALS');?></p>
                            <div class="table-responsive">
                                <table class="usertable">
                                    <tr>
                                        <th><?=System::Lang('DATE');?></th>
                                        <th><?=System::Lang('PRODUCT');?></th>
                                        <th><?=System::Lang('EMAIL');?></th>
                                        <th><?=System::Lang('ORDER_ID');?></th>
                                        <th><?=System::Lang('SUMM');?></th>
                                    </tr>

                                    <?php if($transacts){
                                        foreach($transacts as $action):?>
                                    <tr>
                                        <td><?php echo date("d.m.Y H:i:s", $action['date']);?></td>
                                        <td><?php $name = Product::getProductName($action['product_id']); echo $name['product_name']?></td>
                                        <td><?php echo Order::getEmailByOrder($action['order_id']);?></td>
                                        <td><?php echo $action['order_id'];?></td>
                                        <td><?php echo $action['summ'];?> <?php echo $setting['currency'];?></td>
                                    </tr>
                                    <?php endforeach;
                                    }?>
                                </table>
                            </div>
                        </div>                                                               
                        
                        <!--  Реквизиты -->
                        <div>
                           <div class="requisites">
                            <?php $req = unserialize($req['requsits']);?>
                            <form action="" method="POST">
                            <?php $req_arr = explode("\r\n", $params['params']['req']);
                            //print_r($req_arr);
                            foreach($req_arr as $req_item):
                            $req_item = explode("=", $req_item);
                            if($req_item[0] != 'rs'){?>
                            
                            <div class="h4 requisites__subtitle"><?php echo $req_item[1];?></div>
                                <div class="modal-form-line">
                                <input placeholder="Номер кошелька" type="text" name="req[<?php echo $req_item[0];?>]"
                            value="<?php if(!empty($req)){
                                if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"];
                                }?>">
                               </div>
                            <?php } else {?>
                                <div class="h4 requisites__subtitle"><?php echo $req_item[1];?></div>
                                <div class="modal-form-line">
                                <input placeholder="Номер счета" type="text" name="req[rs][rs]" value="<?php if(!empty($req)){
                                if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"]['rs'];}?>">
                               </div>
                                <div class="modal-form-line">
                                <input placeholder="Название организации" type="text" name="req[<?php echo $req_item[0];?>][name]" value="<?php if(!empty($req)){
                                if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"]['name'];}?>">
                               </div>
                                <div class="modal-form-line">
                                <input placeholder="БИК" type="text" name="req[<?php echo $req_item[0];?>][bik]" value="<?php if(!empty($req)){
                                if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"]['bik'];}?>">
                               </div>
                               <div class="modal-form-line">
                                <input placeholder="ИНН" type="text" name="req[<?php echo $req_item[0];?>][itn]" value="<?php if(!empty($req)){
                                if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"]['itn'];}?>">
                               </div>

                                <?php } 
                            endforeach; ?>
                            <div class="requisites__button"><input type="submit" class="button btn-blue" value="Сохранить" name="save_req"></div>
                            </form>
                           </div>
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
    
    <script type="text/javascript">
    	setTimeout(function(){$('.success_message').fadeOut('fast')},4000);
      $('input[name*="card"]').attr('placeholder', 'Номер карты');
    </script>
</body>
</html>