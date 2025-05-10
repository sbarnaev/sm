<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');

$metriks = null;
if(!empty($setting['yacounter'])) $ya_goal = "yaCounter".$setting['yacounter'].".reachGoal('FEEDBACK');";
else $ya_goal = null;
if($setting['ga_target'] == 1) $ga_goal = "ga ('send', 'event', 'feedback', 'submit');";
else $ga_goal = null;
if(!empty($setting['yacounter']) || $setting['ga_target'] == 1) $metriks = ' onsubmit="'.$ya_goal.$ga_goal.' return true;"';

?>
<body class="invert-page" id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>
    
    
    <div id="content">
        <div class="layout">
            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>" id="feedback">
                <?php //if(isset($_GET['success'])) echo '<div class="success_message">Сообщение успешно отправлено</div>';?>


                <?php if(!isset($_GET['success'])){?>

                <?php $_SESSION['feedback'] = 1;?>
                <?php if(!empty($params['params']['before'])):?>
                    <p><?php echo $params['params']['before'];?></p>
                <?php endif;?>

                <div class="login-userbox">

                    <h1 class="cource-head"><?php echo $params['params']['h1'];?></h1>
                    <form action="" method="POST"<?php echo $metriks;?>>

                    <?php if($params['params']['name'] > 0):?>
                    <div class="modal-form-line"><input type="text" name="name" placeholder="Имя" <?php if($params['params']['name'] == 2) echo ' required="required"';?>></div>
                    <?php endif; ?>

                    <?php if($params['params']['email'] == 1){?>
                        <div class="modal-form-line"><script>document.write(window.atob("PGlucHV0IHR5cGU9ImVtYWlsIiBuYW1lPSJlbWFpbCIgcGF0dGVybj0iXlx3KyhbLi1dP1x3KykqQFx3KyhbLi1dP1x3KykqKFwuXHd7Mix9KSskIiBwbGFjZWhvbGRlcj0iRS1tYWlsIj4="));</script></div>
                    <?php } elseif($params['params']['email'] == 2){ ?>
                        <div class="modal-form-line"><script>document.write(window.atob("PGlucHV0IHR5cGU9ImVtYWlsIiBuYW1lPSJlbWFpbCIgcGF0dGVybj0iXlx3KyhbLi1dP1x3KykqQFx3KyhbLi1dP1x3KykqKFwuXHd7Mix9KSskIiByZXF1aXJlZD0icmVxdWlyZWQiIHBsYWNlaG9sZGVyPSJFLW1haWwiPg=="));</script></div>
                    <?php }?>

                    <?php if($params['params']['phone'] > 0):?>
                    <div class="modal-form-line"><input type="text" name="phone" placeholder="Телефон" <?php if($params['params']['phone'] == 2) echo ' required="required"';?>></div>
                    <?php endif; ?>



                    <?php if($params['params']['field1'] != 'no'){

                        echo renderField($params['params']['field1'], 1, $params['params']['field1_name'], $params['params']['field1_data']);

                    }?>


                    <?php if($params['params']['field2'] != 'no'){

                        echo renderField($params['params']['field2'], 2, $params['params']['field2_name'], $params['params']['field2_data']);

                    }?>


                    <?php if($params['params']['message'] > 0):?>
                    <div class="modal-form-line"><textarea name="text" cols="55" rows="5" placeholder="Ваше сообщение"<?php if($params['params']['message'] == 2) echo ' required="required"';?>></textarea></div>
                    <?php endif; ?>

                    <?php if($params['params']['politika'] == 1):?>
                    <div class="modal-form-line"><label class="check_label" style="width: 100%;"><input type="checkbox" name="politika" required="required"> <span><?=System::Lang('LINK_CONFIRMED');?></span></label></div>
                    <?php endif; ?>

                    <div class="modal-form-submit text-right mb-0"><input type="hidden" name="time" value="<?php echo $now;?>"/>
						<input type="hidden" name="token_sm" value="<?php echo md5($now.'+'.$setting['secret_key']);?>"/>
                        <input type="submit" class="btn-yellow-fz-16 text-uppercase font-bold button" name="feedback" value="<?php echo $params['params']['button_text'];?>"></div>
                    </form>
                    <div><?php echo $params['params']['after'];?></div>
                </div>

                <?php } else echo '<div class=" login-userbox">'.$params['params']['text'].'</div>';?>

                </div>
                <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php');
    
function renderField($type, $num, $name, $data){
    
    switch($type){
        
        case 'text': 
        if($data == 'required') $attr = ' required="required"';
        $html = '<p><input type="text" name="field'.$num.'" placeholder="'.$name.'" '.$attr.'></p>';
        break;
        
        case 'radio':
        $options = explode(";", $data);
        $count = 1;
        $html = "<p><strong>$name</strong></p><ul>";
        foreach($options as $option){
            
            $data = explode('=', $option);
            $html .= '<li><input type="radio" id="field'.$num.$count.'" name="field'.$num.'" value="'.$data[1].'"> <label for="field'.$num.$count.'">'.$data[0].'</label></li>'; 
            $count++;
        }
        $html .= '</ul>';
        break;
        

        
        case 'select':
        $options = explode(";", $data);
        $html = '<p><strong>'.$name.'</strong></p><p><select name="field'.$num.'">';
        foreach($options as $option){
            
            $data = explode('=', $option);
            $html .= '<option value="'.$data[1].'">'.$data[0].'</option>'; 
        }
        $html .= '</select></p>';
        break;
        
        
        case 'chekbox':
        $options = explode(";", $data);
        $count = 1;
        $html = "<p><strong>$name</strong></p><ul>";
        foreach($options as $option){
            
            $data = explode('=', $option);
            $html .= '<li><input type="checkbox" id="field'.$num.$count.'" name="field'.$num.'[]" value="'.$data[1].'"> <label for="field'.$num.$count.'">'.$data[0].'</label></li>'; 
            $count++;
        }
        $html .= '</ul>';
        break;
        
    }
    
    return $html;
    
}

?>
</body>
</html>