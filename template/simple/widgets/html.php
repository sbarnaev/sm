<?php defined('BILLINGMASTER') or die; 

foreach ($widget_params as $param) {
    if (strpos($param['code'], '<?') !== false) {
        System::parsePHPviaFile($param['code']);
    } else {
        echo $param['code'];
    }
}?>