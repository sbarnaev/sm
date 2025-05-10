$(function() {
  /*Элементы урока begin*/
  var show_modal_form = function(modal_id, url) {
    $.ajax({
      url: url,
      type: "POST",
      dataType: "html",
      success: function (html) {
        if (html !== '') {
          $(modal_id + ' .userbox').html(html);
          UIkit.modal(modal_id, {center: true}).show();
          editor_init();
          sorting();
          dependent_blocks();
        }
      }
    });
  };

  $(document).on('click', '.el-edit, .pl-item-edit, .test-edit', function(e) {
    let url = $(this).data('url');
    if (typeof(url) !== 'undefined') {
      e.preventDefault();
      let modal_id = typeof($(this).data('modal_id')) !== 'undefined' ? $(this).data('modal_id') : 'modal_edit_element';
      show_modal_form('#' + modal_id, url);
    }
  });

  $(document).on('click', '.icon-remove.ajax, a.link_delete.ajax', function(e) {
    e.stopPropagation();
    e.preventDefault();

    let $el = $(this);
    let id = typeof($el.data('id')) !== 'undefined' ? $el.data('id') : null;
    let href = typeof($el.attr('href')) !== 'undefined' ? $el.attr('href') : null;
    let url = typeof($el.data('url')) !== 'undefined' ? $el.data('url') : null;
    let replace_block = typeof($el.data('replace_block')) !== 'undefined' ? $el.data('replace_block') : null;

    if (id && (url || href)) {
      let res = confirm('Вы уверены?');
      if (res) {
        let token = $el.parents('form').find('[name="token"]').val();
        $.ajax({
          url: url ? url : href,
          type: "POST",
          data: {id: id, token: token},
          success: function (data) {
            if (data && typeof(data) === 'object') {
              if (data.status && data.redirect) {
                document.location = data.redirect;
              } else if(!data.status) {
                alert('Ошибка данных');
              }
            } else if(replace_block) {
              $el.parents(replace_block).html(data);
            }
          }
        });
      }
    }
  });
  /*Элементы урока end*/

  var sorting = function() {
    /*Sortable begin*/
    let $sort_list = $('.sortable');

    if ($sort_list.length > 0) {
      $sort_list.sortable({
        cursor: "move",
        handle: ".button-drag",
        stop: function (event, ui) {
          let $item = $(ui.item[0]);
          let $sortable_box = $item.parents('.sortable_box');
          if ($sortable_box.length == 0 || $sort_list.find('input[name="sort_items[]"]').length == 0) {
            return false;
          }

          let sort_upd_url = $sortable_box.children('input[name="sort_upd_url"]').val();
          let item_type = $item.find('input[name="sort_items[]"]').data('type');
          let sort = $sortable_box.find('input[name="sort_items[]"][data-type="' + item_type + '"]').serialize();

          $.ajax({
            url: sort_upd_url + '?item_type=' + item_type,
            method: 'post',
            dataType: 'json',
            data: sort,
            success: function (resp) {
              if (!resp.status) {
                alert('Произошла ошибка при сохранении данных, обратитесь к разработчику');
                console.error(resp.error);
              }
            },
            error: function (err) {
              alert("Произошла ошибка при сохранении данных, обратитесь к разработчику");
              console.error(err);
            }
          });
        }
      });
    }
  };

  sorting();
  /*Sortable end*/
  $('.datetimepicker').datetimepicker({
    format:typeof($('.datetimepicker').data('format')) !== 'undefined' ? $('.datetimepicker').data('format') : 'd.m.Y H:i',
    lang:'ru'
  });


  $(document).on('submit', '.uk-modal form.ajax',function(e) { // обновление ajax-формы модального окна и его открытие
    e.preventDefault();
    let $form = $(this);
    let data = $form.serializeArray();
    let $submit = $form.find('input[type="submit"]');
    data.push({name: $submit.attr('name'), value: $submit.val()});

    $.ajax({
      url: $form.attr('action'),
      type: "POST",
      dataType: "json",
      data: data,
      success: function (resp) {
        if (resp.status) {
          if (resp.show_modal_form && resp.modal_form_url) {
            show_modal_form(resp.show_modal_form, resp.modal_form_url);
          } else {
            $form.find('.admin_message').show();
            setTimeout(function(){$form.find('.admin_message').fadeOut('fast')},5000);
          }
        }
      }
    });
  });

  $('.open_add-elements').click(function () {
    $(this).parent('.add-elements__with-open').toggleClass('active');
  });

  $('.uk-modal[data-show_modal]').on({ // открытие модального окна, при закрытии текущего
    'show.uk.modal': function() {},
    'hide.uk.modal': function() {
      let show_modal_form = '#' + $(this).data('show_modal');
      setTimeout(function() {
        let is_open = $(show_modal_form).hasClass('uk-open');
        if (!is_open) {
          UIkit.modal(show_modal_form, {center: true}).show();
        }
      },30);
    }
  });
});