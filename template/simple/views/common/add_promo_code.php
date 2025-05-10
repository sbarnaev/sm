<form style="margin-top:15px;" id="promo" action="" method="POST">
    <p><a class="promo-link" href="#"><?=System::Lang('IS_IT_PROMOCODE');?></a></p>
    <div class="promo-block" style="display: none;">
        <h6><?=System::Lang('PROMOCODE_ENTERING');?></h6>

        <div class="flex-row">
            <div class="modal-form-line max-width-200">
                <input class="small-input" type="text" name="promo">
            </div>
            <div class="modal-form-submit mb-0">
                <input type="submit" class="btn-yellow-fz-16 d-block small-button button" name="getpromo" value="Применить">
            </div>
        </div>
    </div>
</form>

<div id="promocode_msg"><?=System::Lang('PROMOCODE_APPLIED');?></div>

<script>
  window.onload = function() {
    $('#promo').submit(function(e) {
      e.preventDefault();
      let data = $(this).serialize();
      $.ajax({
        method: 'post',
        dataType: 'html',
        data: data,
        success: function (html) {
          if ($('.cart-item').length > 0) {
            let cart_html = $(html).find('.cart-item').html();
            $('.cart-item').html(cart_html);
          } else if($('.order_items').length > 0) {
            let order_items_html = $(html).find('.order_items').html();
            $('.order_items').html(order_items_html);
            
            let payment_itogo_html = $(html).find('.payment-itogo').html();
            $('.payment-itogo').html(payment_itogo_html);
          }
          
          if (html) {
            $("#promocode_msg").show('fast');
            setTimeout(function() {
              $('#promocode_msg').fadeOut('fast');
              $('.promo-block').slideToggle();
            },3000);
          }
        }
      });
    });
  };
</script>

<style>
  #promocode_msg {
    display: none;
    background: #359441;
    color: #fff;
    padding: 1em;
    margin: 0.5em 0;
    width: 56%;
    font-size: 12px;
    border-radius: 5px;
  }
</style>