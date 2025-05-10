<?php defined('BILLINGMASTER') or die; 
$title = 'Спасибо!';
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');?>
<body class="cart-page" id="page">
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    ?>
    <div id="order_form">
        <div class="container-cart">
            <h1><?php echo System::Lang('CUSTOM_SUCCESS_THANK');?></h1>
            
<?php

$time_limit = $order_date + ($payment_params['pay_time'] * 86400);



// Сумма прописью.
function str_price($value)
{
    $value = explode('.', number_format($value, 2, '.', ''));

    $f = new NumberFormatter('ru', NumberFormatter::SPELLOUT);
    $str = $f->format($value[0]);

    // Первую букву в верхний регистр.
    $str = mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1, mb_strlen($str));

    // Склонение слова "рубль".
    $num = $value[0] % 100;
    if ($num > 19) { 
        $num = $num % 10; 
    }    
    switch ($num) {
        case 1: $rub = 'рубль'; break;
        case 2: 
        case 3: 
        case 4: $rub = 'рубля'; break;
        default: $rub = 'рублей';
    }    
    
    return $str . ' ' . $rub . ' ' . $value[1] . ' копеек.';
}

$html = '
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
* {font-family: arial;font-size: 16px;line-height: 16px}
table {margin: 0 0 15px 0; width: 100%; border-collapse: collapse; border-spacing:0}  
table td {padding: 5px; border:1px solid #333}    
table th {padding: 5px;font-weight: bold}

.header {margin: 0 0 0 0;padding: 0 0 15px 0;font-size: 12px;line-height: 12px;text-align: center}

.details td {padding: 3px 2px;border: 1px solid #000000;font-size: 12px;line-height: 12px;vertical-align: top}
h1 {margin: 0 0 10px 0;padding: 10px 0 10px 0;border-bottom: 2px solid #000;font-weight: bold;font-size: 20px}

.list thead, .list tbody  {border: 2px solid #000}
.list thead th {padding: 4px 0;border: 1px solid #000;vertical-align: middle;text-align: center}    
.list tbody td {padding: 5px;border: 1px solid #000; vertical-align: middle; text-align: center } 
.list tfoot th {padding: 5px;border: none;text-align: right} 

.total {margin: 0 0 20px 0;padding: 0 0 10px 0;border-bottom: 2px solid #000} 
.total p {margin: 0;padding: 0}

.sign {position: relative}
.sign table {width: 60%}
.sign th {padding: 40px 0 0 0;text-align: left}
.sign td {padding: 40px 0 0 0;border:none; border-bottom: 1px solid #000;text-align: right;font-size: 12px}

.sign-1 {position: absolute;left: 200px;top:24px} 
.sign-2 {position: absolute;left: 200px;top:80px} 
.printing {position: absolute;left: 271px;top: -15px}

'.$payment_params['sign_css'].'

.footer {padding:4em 0; text-align:center}
</style>
</head>


<body>
<p style="text-align: right;">Оплату необходимо произвести до '.date("d.m.Y", $time_limit).' г.</p><p><br /></p>
<table>
<tbody>
    <tr>
        <td colspan="2" style="border-bottom: none;">'. $payment_params['bank_name'] .'</td>
        <td>БИК</td>
        <td style="border-bottom: none;">'. $payment_params['bik'].'</td>
    </tr>
    <tr>
        <td colspan="2" style="border-top: none; font-size: 12px;">Банк получателя</td>
        <td>Сч. №</td>
        <td style="border-top: none;">'.$payment_params['bank_schet'].'</td>
    </tr>
    <tr>
        <td width="25%">ИНН '.$payment_params['inn'].'</td>
        <td width="30%">КПП '.$payment_params['kpp'].'</td>
        <td width="10%" rowspan="3">Сч. №</td>
        <td width="35%" rowspan="3">'.$payment_params['your_rs'].'</td>
    </tr>
    <tr>
        <td colspan="2" style="border-bottom: none;">'.$payment_params['org_name'].'</td>
    </tr>
    <tr>
        <td colspan="2" style="border-top: none; font-size: 12px;">Получатель</td>
    </tr>
</tbody>
</table>

<h1>Счёт на оплату № '.$order_date.' от '. date("d.m.Y", $order_date) .' г.</h1>

<p>Поставщик: <strong>'.$payment_params['org_name'].', ИНН '.$payment_params['inn'];

if($payment_params['type_org'] == 2) $html .= 'КПП '.$payment_params['kpp'];

$html .= ', '.$payment_params['address'].'</strong></p>
<p>Покупатель: <strong>'.$organization.', ИНН '.$inn.'</strong></p>

<table class="list">
    <thead>
        <tr>
            <th width="5%">№</th>
            <th width="45%">Наименование товара, работ, услуг</th>
            <th width="5%">Коли-<br>чество</th>
            <th width="7%">Ед.<br>изм.</th>
            <th width="7%">НДС %<br></th>
            <th width="15%">Цена, '.$setting['currency'].'</th>
            <th width="15%">Сумма, '.$setting['currency'].'</th>
        </tr>
    </thead>
    <tbody>';
    
    $total = 0; // всего за товары  + НДС
    $total_nds = 0; // всего ндс
    $count = 0;
    foreach($order_items as $item){
        $count++;
        
        if($payment_params['nds'] != 0){
            $nds = ($item['price'] * $payment_params['nds'] / 100);
            
        } else {
            $nds = 0;
        }
        
        $html .= '
        <tr>
            <td>'.$count.'</td>
            <td>'.$item['product_name'].'</td>
            <td>1</td>
            <td>шт</td>
            <td>'.$payment_params['nds'].'</td>
            <td>'.$item['price'].'</td>
            <td>'.$item['price'].'</td>
        </tr>
        ';
        
        $total = $total + $item['price'];
        $total_nds = $total_nds + $nds;
    }
    
    
    
    $html .= '
    </tbody>
    
    <tfoot>
        <tr>
            <th colspan="5">Итого:</th>
            <th>'.$total.' '.$setting['currency'].'</th>
        </tr>
        <tr>
            <th colspan="5">В том числе НДС:</th>
            <th>'.$total_nds.' '.$setting['currency'].'</th>
        </tr>
        <tr>
            <th colspan="5">Всего к оплате:</th>
            <th>'.$total.' '.$setting['currency'].'</th>
        </tr>
        
    </tfoot>
</table>

<div class="total">
    <p>Всего наименований '.$count.', на сумму '.$total.' '.$setting['currency'].'</p>
    <p><strong>'.str_price($total).'</strong></p>
</div>


<div class="sign">
    <img class="sign-1" src="'.ROOT.'/images/'.$payment_params['sign_boss'].'">';
    
    
    if($payment_params['type_org'] == 2 && !empty($payment_params['sign_buh'])) {$html .= '
    <img class="sign-2" src="'.ROOT.'/images/'.$payment_params['sign_buh'].'">';}
    
    $html .= '<img class="printing" style="max-width:250px!important" src="'.ROOT.'/images/'.$payment_params['print'].'">

    <table>
        <tbody>
            <tr>
                <th width="30%">Руководитель</th>
                <td width="70%"> </td>
            </tr>';
            
            if($payment_params['type_org'] == 2) {$html .= '
            <tr>
                <th>Бухгалтер</th>
                <td> </td>
            </tr>
            ';}
            
       $html .= '</tbody>
    </table>
</div>

<div class="footer">
</div>


</body>
</html>
';



?>
            
            
            <div class="order_data">
                
                <?php echo $payment_params['thanks'];?>
                
                    <?php //echo $html;


                    require_once (ROOT . '/vendor/dompdf/autoload.inc.php');
                        
                    // reference the Dompdf namespace
                    use Dompdf\Dompdf;
                    
                    // instantiate and use the dompdf class
                    $dompdf = new Dompdf();
                    //$dompdf->set_option('defaultFont', 'Arial');
                    $dompdf->loadHtml($html, 'UTF-8');
                    
                    // (Optional) Setup the paper size and orientation
                    $dompdf->setPaper('A4', 'portrait');
                    
                    // Render the HTML as PDF
                    $dompdf->render();
                    
                    // Output the generated PDF to Browser
                    //$dompdf->stream();
                    
                    $pdf_file = $dompdf->output(); 
                    $name_schet = 'schet-'.$order_date.'.pdf';
                    file_put_contents("tmp/$name_schet", $pdf_file);
                    
                    ?>
                    
                    <a href="/tmp/<?php echo $name_schet;?>" class="order_button" target="_blank">Скачать счёт</a>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>