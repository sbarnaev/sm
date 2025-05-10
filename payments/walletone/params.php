<?php defined('BILLINGMASTER') or die;

$params = unserialize(base64_decode($payment['params']));
$pt_options = array(
    'title1' => 'ЭЛЕКТРОННЫЕ ДЕНЬГИ',
    'WalletOne' => 'Единый кошелек',
    'WalletOneRUB' => 'W1 RUB',
    'WalletOneUAH' => 'W1 UAH',
    'WalletOneUSD' => 'W1 USD',
    'WalletOneEUR' => 'W1 EUR',
    'WalletOneZAR' => 'W1 ZAR',
    'WalletOneBYR' => 'W1 BYR',
    'WalletOneGEL' => 'W1 GEL',
    'WalletOneKZT' => 'W1 KZT',
    'WalletOnePLN' => 'W1 PLN',
    'WalletOneTJS' => 'W1 TJS',
    'YandexMoneyRUB' => 'Яндекс.Деньги',
    'QiwiWalletRUB' => 'QIWI Кошелек',
    'BPayMDL' => 'B-Pay',
    'CashUUSD' => 'CashU',
    'EasyPayBYR' => 'EasyPay',
    'LiqPayMoney' => 'LiqPay Money',
    'LiqPayMoneyUAH' => 'LiqPayMoneyUAH',
    'GoogleWalletUSD' => 'Google Wallet',
    'OkpayUSD' => 'OKPAY',
    'OkpayRUB' => 'OKPAY',
    'KvikuRUB' => 'Микрозайм Kviku',
    'title2' => 'МОБИЛЬНАЯ КОММЕРЦИЯ',
    'BeelineRUB' => 'Мобильный платеж «Билайн» (Россия)',
    'MtsRUB' => 'Мобильный платеж «МТС» (Россия)',
    'MegafonRUB' => 'Мобильный платеж «Мегафон» (Россия)',
    'Tele2RUB' => 'Мобильный платеж «Tele2» (Россия)',
    'YotaRUB' => 'Мобильный платеж «Yota» (Россия)',
    'KievStarUAH' => 'КиевСтар.Мобильные деньги (Украина)',
    'title3' => 'НАЛИЧНЫЕ',
    'CashTerminal' => 'Платежные терминалы',
    'CashTerminalBYR' => 'Платежные терминалы Беларуси',
    'CashTerminalGEL' => 'Платежные терминалы Грузии',
    'CashTerminalKZT' => 'Платежные терминалы Казахстана',
    'CashTerminalMDL' => 'Платежные терминалы Молдовы',
    'CashTerminalRUB' => 'Платежные терминалы России',
    'CashTerminalUAH' => 'Платежные терминалы Украины',
    'CashTerminalTJS' => 'Платежные терминалы Таджикистана',
    'CashTerminalZAR' => 'Платежные терминалы ЮАР',
    'ATM' => 'ATM',
    'AtmUAH' => 'Прием наличных (UAH)',
    'AtmZAR' => 'Automated Teller Machine',
    'MobileRetails' => 'Салоны связи',
    'EurosetRUB' => 'Салоны связи «Евросеть»',
    'SvyaznoyRUB' => 'Салоны связи «Связной»',
    'CifrogradRUB' => 'Салоны связи «Цифроград»',
    'CellularWorldRUB' => 'Салоны связи «Сотовый мир»',
    'BankOffice' => 'Отделения банков',
    'SberbankRUB' => 'Отделения Сбербанка России',
    'SberbankKZT' => 'Отделения Сбербанка в Казахстане',
    'PrivatbankUAH' => 'Отделения Приватбанка в Украине',
    'PravexBankUAH' => 'Отделения Правэкс-Банка в Украине',
    'UkrsibBankUAH' => 'Отделения УкрСиббанка в Украине',
    'KazkomBankKZT' => 'Отделения Казкоммерцбанка Казахстане',
    'LibertyBankGEL' => 'Отделения Liberty Bank в Грузии',
    'BranchZAR' => 'Bank Branch Deposit',
    'RussianPostRUB' => 'Отделения Почты России',
    'MoneyTransfer' => 'Денежные переводы',
    'LiderRUB' => 'Денежные переводы «ЛИДЕР»',
    'UnistreamRUB' => 'Денежные переводы Unistream (RUB)',
    'UnistreamUSD' => 'Денежные переводы Unistream (USD)',
    'title4' => 'БЕЗНАЛИЧНЫЕ',
    'OnlineBank' => 'Интернет-банки',
    'AlfaclickRUB' => 'Интернет-банк «Альфа-Клик» («Альфа-Банк»)',
    'TinkoffRUB' => 'Интернет-банк «Тинькофф»',
    'Privat24UAH' => 'Интернет-банк «Приват24»',
    'PsbRetailRUB' => 'Интернет-банк «PSB-Retail» («Промсвязьбанк»)',
    'SberOnlineRUB' => 'Сбербанк ОнЛ@йн',
    'FakturaruRUB' => 'Faktura.ru',
    'RsbRUB' => 'Интернет-банк Банка «Русский Стандарт»',
    'EripBYR' => 'Единое Расчетное Информационное Пространство (ЕРИП)',
    'EftZAR' => 'Electronic funds transfer',
    'BankTransfer' => 'Банковский перевод',
    'BankTransferCNY' => 'Банковский перевод в китайских юанях',
    'BankTransferEUR' => 'Банковский перевод в евро',
    'BankTransferGEL' => 'Банковский перевод в лари',
    'BankTransferKZT' => 'Банковский перевод в тенге',
    'BankTransferMDL' => 'Банковский перевод в леях',
    'BankTransferPLN' => 'Банковский перевод в польских злотах',
    'BankTransferRUB' => 'Банковский перевод в рублях',
    'BankTransferUAH' => 'Банковский перевод в гривнах',
    'BankTransferUSD' => 'Банковский перевод в долларах',
    'title5' => 'БАНКОВСКИЕ КАРТЫ',
    'SmartiviGEL' => 'Карты Smartivi',
    'VISA' => 'VISA',
    'CreditCardBYR' => 'MasterCard BYR',
    'CreditCardRUB' => 'МИР',
    'CreditCardUAH' => 'MasterCard UAH',
    'CreditCardUSD' => 'MasterCard USD',
    'CreditCardEUR' => 'MasterCard EUR',
    'MasterCard' => 'MasterCard',
    'Maestro' => 'Maestro',
    'title6' => 'ТЕСТОВЫЕ СПОСОБЫ ОПЛАТЫ',
    'TestCardEUR' => 'TestCardEUR',
    'TestCardRUB' => 'TestCardRUB',
    'TestCardUSD' => 'TestCardUSD',
);
?>
<div class="row-line">
    <div class="col-1-2">
        <h4 class="h4-border">Параметры</h4>
        <p><label>ID мерчанта:</label>
            <input type="text" name="params[merchant_id]" value="<? echo $params['merchant_id'];?>"></p>
        <p><label>Секретный ключ</label>
            <input type="text" name="params[secret_key]" value="<? echo $params['secret_key'];?>"></p>
        <p><label>Срок действия счета (дней, 1-30):</label>
            <input type="number" class="dop_cat" name="params[account_time]" min="1" max="30" value="<? echo $params['account_time'];?>" min="1" max="30"></p>
        <div class="width-100"><label>Валюта заказа:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="params[currency_id]" type="radio" value="643" <?php if($params['currency_id'] == '643') echo 'checked';?>><span>RUB</span></label>
                <label class="custom-radio"><input name="params[currency_id]" type="radio" value="840" <?php if($params['currency_id'] == '840') echo 'checked';?>><span>USD</span></label>
                <label class="custom-radio"><input name="params[currency_id]" type="radio" value="978" <?php if($params['currency_id'] == '978') echo 'checked';?>><span>EUR</span></label>
                <br>
                <label class="custom-radio"><input name="params[currency_id]" type="radio" value="980" <?php if($params['currency_id'] == '980') echo 'checked';?>><span>UAH</span></label>
                <label class="custom-radio"><input name="params[currency_id]" type="radio" value="974" <?php if($params['currency_id'] == '974') echo 'checked';?>><span>BYR</span></label>
                <label class="custom-radio"><input name="params[currency_id]" type="radio" value="398" <?php if($params['currency_id'] == '398') echo 'checked';?>><span>KZT</span></label>
                <br>
                <label class="custom-radio"><input name="params[currency_id]" type="radio" value="985" <?php if($params['currency_id'] == '985') echo 'checked';?>><span>PLN&nbsp;</span></label>
                <label class="custom-radio"><input name="params[currency_id]" type="radio" value="972" <?php if($params['currency_id'] == '972') echo 'checked';?>><span>TJS&nbsp;&nbsp;</span></label>
                <label class="custom-radio"><input name="params[currency_id]" type="radio" value="710" <?php if($params['currency_id'] == '710') echo 'checked';?>><span>ZAR</span></label>
            </span></div>
        <div class="width-100"><label>Ставка НДС:</label>
            <select name="params[tax_type]">
                <option value="without"<?php if($params['tax_type'] == 'without') echo ' selected="selected"';?>>без НДС</option>
                <option value="0"<?php if($params['tax_type'] == '0') echo ' selected="selected"';?>>НДС по ставке 0%</option>
                <option value="10"<?php if($params['tax_type'] == '10') echo ' selected="selected"';?>>НДС чека по ставке 10%</option>
                <option value="18"<?php if($params['tax_type'] == '18') echo ' selected="selected"';?>>НДС чека по ставке 18%</option>
                <option value="10/110"<?php if($params['tax_type'] == '10/110') echo ' selected="selected"';?>>НДС чека по расчетной ставке 10/110</option>
                <option value="18/118"<?php if($params['tax_type'] == '18/118') echo ' selected="selected"';?>>НДС чека по расчетной ставке 18/118</option>
                <option value="20"<?php if($params['tax_type'] == '20') echo ' selected="selected"';?>>НДС чека по ставке 20%</option>
                <option value="20/120"<?php if($params['tax_type'] == '20/120') echo ' selected="selected"';?>>НДС чека по расчетной ставке 20/120</option>
            </select>
        </div>
        <div class="width-100"><label>Разрешенные платежные системы: </label>
            <select class="multiple-select" name="params[pt_enabled][]" multiple="multiple">
                <?php foreach ($pt_options as $value => $name):?>
                    <?if(preg_match('#^title[0-9]+$#', $value)):?>
                        <option disabled="disabled" value=""><?php echo $name;?></option>
                    <?php else:?>
                        <option value="<?php echo $value;?>"<?php if(!empty($params['pt_enabled']) && array_search($value, $params['pt_enabled']) !== false) echo ' selected="selected"';?>>
                            <?php echo $name;?>
                        </option>
                    <?php endif;?>
                <?php endforeach;?>
            </select>
        </div>
        <div class="width-100"><label>Запрещенные платежные системы: </label>
            <select class="multiple-select" name="params[pt_disabled][]" multiple="multiple">
                <?php foreach ($pt_options as $value => $name):?>
                    <?if(preg_match('#^title[0-9]+$#', $value)):?>
                        <option disabled="disabled" value=""><?php echo $name;?></option>
                    <?php else:?>
                        <option value="<?php echo $value;?>"<?php if(!empty($params['pt_disabled']) && array_search($value, $params['pt_disabled']) !== false) echo ' selected="selected"';?>>
                            <?php echo $name;?>
                        </option>
                    <?php endif;?>
                <?php endforeach;?>
            </select>
        </div>
    </div>
</div>
<div class="reference-link">
    <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232778"><i class="icon-info"></i>Справка по расширению</a>
</div>