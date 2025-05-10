$(document).ready(function() {
  $(".menu-apsell").lightTabs();
  //Accordion Nav
  $('.mainNav').navAccordion({
      expandButtonText: '<i class="icon-angle-down"></i>',  //Text inside of buttons can be HTML
      collapseButtonText: '<i class="icon-angle-down"></i>'
    },function(){
      console.log('Callback');
    });

  $('input[type="file"], select[multiple="multiple"]').styler({});


  $('.nav-click').click(function () {
    $(this).closest('.nav_gorizontal__parent-wrap').toggleClass('active');
  });

  $(document).on('click', function(e) {
    if (!$(e.target).closest(".nav-click").length) {
      $('.nav_gorizontal__parent-wrap').removeClass('active');
    }
    e.stopPropagation();
  });

  $(document).mouseup(function(e) {
    var container = $(".nav_gorizontal__parent-wrap.active");
    if (!container.is(e.target) &&
      container.has(e.target).length === 0 &&
      !$(e.target).hasClass("nav_gorizontal__parent-wrap")) {
      container.removeClass("active");
    }
  });

  $('#inner-descr').on('click', function() {
    $(this).children('input').prop('checked', true);
    $('#external-descr').children('input').prop('checked', false);
    $('.external-descr-i').css('display', 'none');
    $('.inner-descr-i').css('display', 'block');
    $('.big-descr').css('display', 'block');
    $('.short-desct').css('display', 'none');
  });

  $('#external-descr').on('click', function() {
    $(this).children('input').prop('checked', true);
    $('#inner-descr').children('input').prop('checked', false);
    $('.external-descr-i').css('display', 'block');
    $('.inner-descr-i').css('display', 'none');
    $('.big-descr').css('display', 'none');
    $('.short-desct').css('display', 'block');
  });

  $('.filter .list > li > a').click(function () {
    $(this).parent('.filter .list li').toggleClass('active');
  });

  $(document).on('click', function(e) {
    if (!$(e.target).closest(".filter .list > li > a").length) {
      $('.filter .list li').removeClass('active');
    }
    e.stopPropagation();
  });

  $('.custom-radio:nth-child(2) ~ *').parent('.custom-radio-wrap').addClass('custom-radio-lot-of');

  $('.table-sort').DataTable({
    "paging":   false,
    "sDom": '<"top"i>rt<"bottom"lp><"clear">',
    "info":     false,
    "order": [[ 0, "desc" ]]
  });

  $('#checkbox-change').change(function() {
    $('.special-treatment').addClass('visible');
  });

  $('#checkbox-change-2').change(function() {
    $('.special-treatment').removeClass('visible');
  });

  if ($('#table-receipt').html() === '') {
    $('#table-receipt').html('<p>Здравствуйте, [NAME]!</p>\n' +
        '<p>В соответствии с положениями п. 2.1. ст. 2 ФЗ "О применении контрольно-кассовой<br />техники при осуществлении наличных денежных расчетов и (или) расчетов с<br />использованием электронных средств платежа" от 22.05.2003 N 54-ФЗ<br />направляем Вам документ, подтверждающий факт произведения расчета между<br />индивидуальным предпринимателем и покупателем.</p>\n' +
        '<table style="width: 100%; max-width: 100%; border-collapse: collapse; border-spacing: 0; font-size: 14px; color: #373a4c;">\n' +
        '<tbody>\n' +
        '<tr>\n' +
        '<td style="padding-right: 15px; padding-top: 4px; padding-bottom: 4px;">Квитанция об оплате №:</td>\n' +
        '<td style="text-align: right;">[ORDER]</td>\n' +
        '</tr>\n' +
        '<tr>\n' +
        '<td style="padding-right: 15px; padding-top: 4px; padding-bottom: 4px;">Дата:</td>\n' +
        '<td style="text-align: right;">[DATE]</td>\n' +
        '</tr>\n' +
        '<tr>\n' +
        '<td style="padding-right: 15px; padding-top: 4px; padding-bottom: 4px;">Наименование</td>\n' +
        '<td style="text-align: right;">[ORG_NAME]</td>\n' +
        '</tr>\n' +
        '<tr>\n' +
        '<td style="padding-right: 15px; padding-top: 4px; padding-bottom: 4px;">ИНН:</td>\n' +
        '<td style="text-align: right;">[INN]</td>\n' +
        '</tr>\n' +
        '<tr>\n' +
        '<td style="padding-right: 15px; padding-top: 4px; padding-bottom: 4px;">Система налогообложения:</td>\n' +
        '<td style="text-align: right;">Патент</td>\n' +
        '</tr>\n' +
        '<tr>\n' +
        '<td style="padding-right: 15px; padding-top: 4px; padding-bottom: 4px;">Признак расчета (приход, возврат прихода):</td>\n' +
        '<td style="text-align: right;">приход</td>\n' +
        '</tr>\n' +
        '<tr>\n' +
        '<td style="padding-right: 15px; padding-top: 4px; padding-bottom: 4px;">Форма расчета (электронные деньги, безналичный расчет, наличные):</td>\n' +
        '<td style="text-align: right;">электронные деньги</td>\n' +
        '</tr>\n' +
        '<tr>\n' +
        '<td style="padding-right: 15px; padding-top: 4px; padding-bottom: 4px;">Пользователь не является плательщиком НДС</td>\n' +
        '<td style="text-align: right;">&nbsp;</td>\n' +
        '</tr>\n' +
        '<tr>\n' +
        '<td style="padding-right: 15px; padding-top: 4px; padding-bottom: 4px;">Адрес электронной почты поставщика:</td>\n' +
        '<td style="text-align: right;">[EMAIL]</td>\n' +
        '</tr>\n' +
        '<tr>\n' +
        '<td style="padding-right: 15px; padding-top: 4px; padding-bottom: 4px;">Адрес электронной почты покупателя:</td>\n' +
        '<td style="text-align: right;">[CLIENT_EMAIL]</td>\n' +
        '</tr>\n' +
        '<tr>\n' +
        '<td style="padding-right: 15px; padding-top: 4px; padding-bottom: 4px;">Место расчета (наименование сайта):</td>\n' +
        '<td style="text-align: right;">[SITE]</td>\n' +
        '</tr>\n' +
        '</tbody>\n' +
        '</table>\n' +
        '<p><strong>Предмет расчёта:</strong></p>\n' +
        '<p>[ORDER_ITEMS]</p>');
  }
  
  $('#minmaxprice').on('click', function() {  
    if ($(this).is(':checked')){
      $('#customprice').css("display", "block");
    } else {
      $('#customprice').css("display", "none");
    }  
  });

});

var dependent_blocks = function() {
  if ($('[data-show_on]').length > 0) {
    $('[data-show_on]').each(function () {
      let $block = $('#' + $(this).data('show_on'));
      if ($block.length > 0 && ($(this).is(':selected') || $(this).is(':checked'))) {
        $block.removeClass('hidden');
      }
    });
  }

  $(document).on('change', 'select', function (e) {
    let $el = $("option:selected", this);
    let block_id = '';
    if (typeof($el.data('show_on')) !== 'undefined') {
      block_id = '#' + $el.data('show_on');
      $(block_id).removeClass('hidden');
    }

    let $select = $el.parent('select');
    $els = $select.find('option[data-show_on]');
    $els.each(function () {
      let $bloc__id = '#' + $(this).data('show_on');
      if (block_id != $bloc__id && !$(this).is(':selected') && $($bloc__id).is(':visible')) {
        $($bloc__id).addClass('hidden');
      }
    });
  });

  $(document).on('change', 'input[type="radio"], input[type="checkbox"][data-show_on]', function () {
    if ($(this).attr('type') === 'checkbox' || $(this).parents('.custom-radio-wrap').find('input[type="radio"][data-show_on]').length > 0) {
      let $block = $(this).attr('type') === 'checkbox' ? $('#' + $(this).data('show_on')) : $('#' + $(this).parents('.custom-radio-wrap').find('input[type="radio"][data-show_on]').data('show_on'));

      if ($block.length > 0) {
        if (($(this).attr('type') === 'CHECKBOX' || typeof($(this).data('show_on')) !== 'undefined') && $(this).is(':checked')) {
          $block.removeClass('hidden');
        } else {
          $block.addClass('hidden');
        }
      }
    }
  });


  if ($('[data-show_off]').length > 0) {
    $('[data-show_off]').each(function () {
      let $el = $(this);
      let blocks = $(this).data('show_off').split(',');
      blocks.forEach(function (val) {
        let $block = $('#' + val);
        if ($block.length > 0 && ($el.is(':selected') || $el.is(':checked'))) {
          $block.addClass('hidden');
        }
      });
    });
  }

  $(document).on('change', 'select', function () {
    let $els = $(this).find('option:not(:selected)[data-show_off]');
    let $el_selected = $(this).find('option:selected[data-show_off]');
    let els_hidden = $el_selected.length > 0 ? $el_selected.data('show_off').split(',') : [];
    let selected_index = -1;

    if (els_hidden) {
      els_hidden.forEach(function (val) {
        let $block = $('#' + val);
        if ($block.length > 0) {
          $block.addClass('hidden');
        }
      });
    }

    if ($els.length > 0) {
      $els.each(function () {
        let blocks = $(this).data('show_off').split(',');

        blocks.forEach(function (val) {
          let $block = $('#' + val);

          if ($block.length > 0 && els_hidden.indexOf(val) === -1) {
            $block.removeClass('hidden');

            if ($block.parent('select').length > 0) { // если блок является опцией селекта и сброс выбранного секлета не производился
              selected_index = $block.index();
              $block.parent('select').prop('selectedIndex', selected_index);
            }
          }
        });
      });
    }
  });

  $(document).on('click', 'input[data-show_off]', function () {
    let $el = $(this);
    let blocks = $(this).data('show_off').split(',');
    blocks.forEach(function (val) {
      let $block = $('#' + val);
      if ($block.length > 0 && $el.is(':checked')) {
        $block.addClass('hidden');
      }
    });
  });
};

$(function () {
  var accordeon = $('.block-collapse-head');
  accordeon.click(function (e) {
    $(this).toggleClass('active').siblings('.block-collapse-inner').slideToggle(300).parent('.block-collapse').toggleClass('show');
  });
  
  if ($('.datetimepicker').length > 0) {
    $.datetimepicker.setLocale('ru');
    $('.datetimepicker').datetimepicker({
      dayOfWeekStart : 1
    });
  }
  dependent_blocks();
});