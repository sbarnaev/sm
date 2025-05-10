<div class="sale-paysystem-wrapper">
	<span class="tablebodytext">
		<?php use esas\hutkigrosh\lang\TranslatorBM;
        $translator = new TranslatorBM();
        echo $completion_text;?>
	</span>

    <?php if($configurationWrapper->isWebpayButtonEnabled()):
        if($webpay_status && $webpay_status == 'payed'):?>
            <script>alert("<?=$translator->translate('hutkigrosh_webpay_msg_success')?>");</script>
        <?php elseif($webpay_status && $webpay_status == 'failed'):?>
            <script>alert("<?=$translator->translate('hutkigrosh_webpay_msg_unsuccess')?>");</script>
        <?php endif;?>

        <hr>
        <div id="webpay">
            <?=$webpay_form?>
        </div>

        <script>
          $(function() {
            $('#webpay input[type="submit"]').addClass('payment_btn');
          });
        </script>
    <?php endif;?>

        <?php if($configurationWrapper->isAlfaclickButtonEnabled()):?>
            <hr>
            <form action="<?=$alfaclick_url?>" id="alfaclick_form">
                <div>
                    <input type="hidden" id="alfaclick_bill_id" value="<?=$alfaclick_billID?>"/>
                    <p><label>Номер телефона, по которому будет выставлен счет в системе AlfaClick</label>
                        <input type="tel" id="alfaclick_phone" required="required" minlength="6" maxlength="18" value="<?=$alfaclick_phone?>"/>
                    </p>
                    <input type="submit" class="order_button btn-green-small mt-5" style="max-width: inherit;" value="Выставить счет в Alfaclick" id="alfaclick_btn">
                </div>
            </form>

            <script>
                $(function() {
                    $('#alfaclick_btn').click(function () {
                        if ($('#alfaclick_phone').val().length > 5) {
                            $.post('<?=$alfaclick_url?>', {
                                phone: $('#alfaclick_phone').val(),
                                billid: $('#alfaclick_bill_id').val()
                            }).done(function (result) {
                                if (result.trim() == 'ok') {
                                    alert('<?=$translator->translate("hutkigrosh_alfaclick_msg_success")?>');
                                } else {
                                    alert('<?=$translator->translate("hutkigrosh_alfaclick_msg_unsuccess")?>');
                                }
                            });
                            return false;
                        }
                    });
                });
            </script>
        <?php endif?>
    </div>
</div>