<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Настройки AmoCRM</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
        </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/extensions/">Расширения</a></li>
        <li>Настройки AmoCRM</li>
    </ul>
    
    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Сохранено!</div>
    <?php endif?>
    
    <form action="" method="POST">
        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/nastr-tren.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Настройки AmoCRM</h3>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="save" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/extensions/">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4 class="h4-border">Основное</h4>
                    <div class="width-100"><label>Статус:</label>
                        <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($enable == 1) echo 'checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($enable == 0) echo 'checked';?>><span>Откл</span></label>
                    </span>
                    </div>

                    <div class="width-100"><label>Id интеграции</label>
                        <input type="text" name="amocrm[params][integr_id]" value="<?=$params['params']['integr_id'];?>">
                    </div>
                    
                    <div class="width-100"><label>Секретный ключ приложения</label>
                        <input type="text" name="amocrm[params][secret_key]" value="<?=$params['params']['secret_key'];?>">
                    </div>

                    <?php if($params['params']['integr_id'] && $params['params']['secret_key']):?>
                        <div class="width-100">
                            <a href="/admin/amocrmsetting/oauth">Назначить права приложению</a>
                        </div>
                    <?php endif;?>
                </div>
            </div>

            <div class="row-line">
                <div class="col-1-2">
                    <div class="width-100"><label>Название поля для передачи ID партнеров</label>
                        <input type="text" name="amocrm[params][partners_ids_fname]" value="<?=isset($params['params']['partners_ids_fname']) ? $params['params']['partners_ids_fname'] : '';?>">
                    </div>

                    <div class="width-100"><label>Название поля для передачи партнерcких выплат</label>
                        <input type="text" name="amocrm[params][partners_payouts_fname]" value="<?=isset($params['params']['partners_payouts_fname']) ? $params['params']['partners_payouts_fname'] : '';?>">
                    </div>

                    <div class="width-100"><label>Название поля для передачи ID заказа</label>
                        <input type="text" name="amocrm[params][order_id_fname]" value="<?=isset($params['params']['order_id_fname']) ? $params['params']['order_id_fname'] : '';?>">
                    </div>
                </div>
            </div>
            <?php if(isset($pipelines) && !empty($pipelines)):?>
                <div class="row-line">
                    <div class="col-1-2">
                        <div class="width-100"><label>Воронка при выписке счета:</label>
                            <div class="select-wrap">
                                <select name="amocrm[params][pip_acc_stat]" data-pip_type="acc_stat">
                                    <option value=""></option>
                                    <?php foreach ($pipelines as $pipeline):?>
                                        <option value="<?=$pipeline->id;?>"<?php if($params['params']['pip_acc_stat'] == $pipeline->id) echo ' selected="selected"';?>><?=$pipeline->name;?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-1-2">
                        <div class="width-100" <?=!$params['params']['pip_acc_stat'] ? ' style="display:none"' : '';?>><label>Этап при выписке счета:</label>
                            <div class="select-wrap">
                                <select name="amocrm[params][stage_acc_stat]">
                                    <option value=""></option>
                                    <?php if (isset($stages_acc_stat) && !empty($stages_acc_stat)):
                                        foreach ($stages_acc_stat as $stage_acc_stat):?>
                                            <option value="<?=$stage_acc_stat->id;?>"<?php if($params['params']['stage_acc_stat'] == $stage_acc_stat->id) echo ' selected="selected"';?>><?=$stage_acc_stat->name;?></option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row-line">
                    <div class="col-1-2">
                        <div class="width-100"><label>Воронка при выписке счета для бесплатного продукта:</label>
                            <div class="select-wrap">
                                <select name="amocrm[params][pip_give_fp]" data-pip_type="give_fp">
                                    <option value=""></option>
                                    <?php foreach ($pipelines as $pipeline):?>
                                        <option value="<?=$pipeline->id;?>"<?php if(isset($params['params']['pip_give_fp']) && $params['params']['pip_give_fp'] == $pipeline->id) echo ' selected="selected"';?>><?=$pipeline->name;?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-1-2">
                        <div class="width-100" <?=!isset($params['params']['pip_give_fp']) || !$params['params']['pip_give_fp'] ? ' style="display:none"' : '';?>><label>Этап при выписке счета для бесплатного продукта:</label>
                            <div class="select-wrap">
                                <select name="amocrm[params][stage_give_fp]">
                                    <option value=""></option>
                                    <?php if (isset($stages_give_fp) && !empty($stages_give_fp)):
                                        foreach ($stages_give_fp as $stage_give_fp):?>
                                            <option value="<?=$stage_give_fp->id;?>"<?php if($params['params']['stage_give_fp'] == $stage_give_fp->id) echo ' selected="selected"';?>><?=$stage_give_fp->name;?></option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row-line">
                    <div class="col-1-2">
                        <div class="width-100"><label>Воронка при оплате счета:</label>
                            <div class="select-wrap">
                                <select name="amocrm[params][pip_acc_pay]" data-pip_type="acc_pay">
                                    <option value=""></option>
                                    <?php foreach ($pipelines as $pipeline):?>
                                        <option value="<?=$pipeline->id;?>"<?php if($params['params']['pip_acc_pay'] == $pipeline->id) echo ' selected="selected"';?>><?=$pipeline->name;?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                    </div>
    
                    <div class="col-1-2">
                        <div class="width-100" <?=!$params['params']['pip_acc_pay'] ? ' style="display:none"' : '';?>><label>Этап при оплате счета:</label>
                            <div class="select-wrap">
                                <select name="amocrm[params][stage_acc_pay]">
                                    <option value=""></option>
                                    <?php if (isset($stages_acc_pay) && !empty($stages_acc_pay)):
                                        foreach ($stages_acc_pay as $stage_acc_pay):?>
                                            <option value="<?=$stage_acc_pay->id;?>"<?php if($params['params']['stage_acc_pay'] == $stage_acc_pay->id) echo ' selected="selected"';?>><?=$stage_acc_pay->name;?></option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row-line">
                    <div class="col-1-2">
                        <div class="width-100"><label>Воронка для следующего платежа при оплате рассрочкой:</label>
                            <div class="select-wrap">
                                <select name="amocrm[params][pip_instlmnt_pay]" data-pip_type="instlmnt_pay">
                                    <option value=""></option>
                                    <?php foreach ($pipelines as $pipeline):?>
                                        <option value="<?=$pipeline->id;?>"<?php if(isset($params['params']['pip_instlmnt_pay']) && $params['params']['pip_instlmnt_pay'] == $pipeline->id) echo ' selected="selected"';?>><?=$pipeline->name;?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                    </div>
    
                    <div class="col-1-2">
                        <div class="width-100" <?=!isset($params['params']['pip_instlmnt_pay']) || !$params['params']['pip_instlmnt_pay'] ? ' style="display:none"' : '';?>><label>Этап для следующего платежа при оплате рассрочкой:</label>
                            <div class="select-wrap">
                                <select name="amocrm[params][stage_instlmnt_pay]">
                                    <option value=""></option>
                                    <?php if (isset($stages_instlmnt_pay) && !empty($stages_instlmnt_pay)):
                                        foreach ($stages_instlmnt_pay as $stages_instlmnt_pay):?>
                                            <option value="<?=$stages_instlmnt_pay->id;?>"<?php if(isset($params['params']['stage_instlmnt_pay']) && $params['params']['stage_instlmnt_pay'] == $stages_instlmnt_pay->id) echo ' selected="selected"';?>><?=$stages_instlmnt_pay->name;?></option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-1-2">
                        <div class="width-100"><label>Название поля для передачи даты следующего платежа при оплате рассрочкой</label>
                            <input type="text" name="amocrm[params][instlmnt_next_pay_date_fname]" value="<?=isset($params['params']['instlmnt_next_pay_date_fname']) ? $params['params']['instlmnt_next_pay_date_fname']  : '';?>">
                        </div>
                    </div>
                </div>
                
                <?php if($_SERVER['HTTP_HOST'] == 'lk.school-master.ru' || $_SERVER['HTTP_HOST'] == 'lk.school-master.local'):?>
                    <div class="row-line">
                        <div class="col-1-2">
                            <div class="width-100"><label>Воронка при генерации триала:</label>
                                <div class="select-wrap">
                                    <select name="amocrm[params][pip_gen_trial]" data-pip_type="gen_trial">
                                        <option value=""></option>
                                        <?php foreach ($pipelines as $pipeline):?>
                                            <option value="<?=$pipeline->id;?>"<?php if(isset($params['params']['pip_gen_trial']) && $params['params']['pip_gen_trial'] == $pipeline->id) echo ' selected="selected"';?>><?=$pipeline->name;?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100" <?=!isset($params['params']['pip_gen_trial']) || !$params['params']['pip_gen_trial'] ? ' style="display:none"' : '';?>><label>Этап при генерации триала:</label>
                                <div class="select-wrap">
                                    <select name="amocrm[params][stage_gen_trial]">
                                        <option value=""></option>
                                        <?php if (isset($stages_gen_trial) && !empty($stages_gen_trial)):
                                            foreach ($stages_gen_trial as $stage_gen_trial):?>
                                                <option value="<?=$stage_gen_trial->id;?>"<?php if($params['params']['stage_gen_trial'] == $stage_gen_trial->id) echo ' selected="selected"';?>><?=$stage_gen_trial->name;?></option>
                                            <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif;?>

                <div class="row-line">
                    <div class="col-1-2">
                        <div class="width-100"><label>Воронка для передачи списка должников по рассрочке:</label>
                            <div class="select-wrap">
                                <select name="amocrm[params][pip_debtors_instlmnt]" data-pip_type="debtors_instlmnt">
                                    <option value=""></option>
                                    <?php foreach ($pipelines as $pipeline):?>
                                        <option value="<?=$pipeline->id;?>"<?php if(isset($params['params']['pip_debtors_instlmnt']) && $params['params']['pip_debtors_instlmnt'] == $pipeline->id) echo ' selected="selected"';?>><?=$pipeline->name;?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-1-2">
                        <div class="width-100" <?=!isset($params['params']['pip_debtors_instlmnt']) || !$params['params']['pip_debtors_instlmnt'] ? ' style="display:none"' : '';?>><label>Этап для передачи списка дожников по рассрочке:</label>
                            <div class="select-wrap">
                                <select name="amocrm[params][stage_debtors_instlmnt]">
                                    <option value=""></option>
                                    <?php if (isset($stages_debtors_instlmntl) && !empty($stages_debtors_instlmntl)):
                                        foreach ($stages_debtors_instlmntl as $stage_debtors_instlmnt):?>
                                            <option value="<?=$stage_debtors_instlmnt->id;?>"<?php if($params['params']['stage_debtors_instlmnt'] == $stage_debtors_instlmnt->id) echo ' selected="selected"';?>><?=$stage_debtors_instlmnt->name;?></option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            
            
                <div class="row-line">
                    <div class="col-1-2">
                        <label class="custom-chekbox-wrap mb-20" for="send_partner">
                            <input type="checkbox" id="send_partner" name="amocrm[params][send_partner]" value="1" <?php if(isset($params['params']['send_partner']) && $params['params']['send_partner']) echo ' checked="checked"';?>>
                            <span class="custom-chekbox"></span>
                            Передавать партнерские данные при отправке данных в AmoCRM
                        </label>
                    </div>
                </div>
            <?php else:?>
                <input type="hidden" name="amocrm[params][pip_acc_stat]" value="">
                <input type="hidden" name="amocrm[params][stage_acc_stat]" value="">

                <input type="hidden" name="amocrm[params][pip_acc_pay]" value="">
                <input type="hidden" name="amocrm[params][stage_acc_pay]" value="">
            <?php endif;?>
        </div>
        <div class="reference-link">
            <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232109"><i class="icon-info"></i>Справка по расширению</a>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

<script>
  $(document).ready(function() {
    $('select[data-pip_type]').change(function () {
      let pip_type = $(this).data('pip_type');
      let pip_id = $(this).val();
      let select_name = 'amocrm[params][stage_' + pip_type + ']';
      if ($('select[name="' + select_name + '"]').length == 0) {
        select_name = 'amocrm[params][stages_' + pip_type + '][]';
      }
      
      if (pip_id) {
        $('select[name="' + select_name + '"]').parents('div.width-100').show();
      } else {
        $('select[name="' + select_name + '"]').parents('div.width-100').hide();
      }
      
      $.ajax({
        url: '/admin/amocrmsetting/ajax',
        method: 'post',
        dataType: 'json',
        data: {pip_type:pip_type, pip_id:pip_id, token: '<?=$_SESSION["admin_token"];?>'},
        success: function (resp) {
          if (resp.status) {
            if (Object.keys(resp.data).length > 0) {
              let html = '<option value=""></option>';
              for (key in resp.data) {
                html += '<option value="' + resp.data[key].id + '">' + resp.data[key].name + '</option>';
              }
              $('select[name="' + select_name + '"]').html(html);
            }
          } else {
            console.error(resp.error);
          }
        },
        error: function (err) {
          console.error(err);
        }
      });
    });
  });
</script>
</body>
</html>