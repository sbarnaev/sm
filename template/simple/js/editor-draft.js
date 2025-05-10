class EditorDraft {
  timer_id = null;
  on_save = false;
  draft_id = null;
  data = '';

  save(editor_id, data) {
    this.on_save = false;
    this.data = data;
    if (!this.timer_id) {
      this.draft_id = this.draft_get_id(editor_id);
      this.timer_id = setInterval(() => this.save_data(this), 600);
    }
  }

  draft_get_id(editor_id) {
    return document.location.pathname + '/' + editor_id;
  }

  save_data(obj) {
    if (obj.on_save) {
      localStorage.setItem(this.draft_id, this.data);
      console.log('editor text save');
      clearInterval(obj.timer_id);
      obj.timer_id = null;
    } else {
      obj.on_save = true;
    }
  }
}


document.addEventListener('DOMContentLoaded', function() {
  try {
    let $thmb = $('.lesson-inner .trumbowyg-textarea, #lk .trumbowyg-textarea');
    if ($thmb.length > 0) {
      let draft = new EditorDraft();

      $thmb.each(function () {
        let draft_id = draft.draft_get_id($(this).index('.trumbowyg-textarea'));
        let html = localStorage.getItem(draft_id);
        if (html) {
          $(this).trumbowyg('html', html);
        }
      });

      $('textarea.editor').trumbowyg().on('tbwchange', function () {
        let data = $(this).trumbowyg('html');
        draft.save($(this).index('.trumbowyg-textarea'), data);
      });

      $thmb.parents('form').submit(function (evt) {
        $(this).find('.trumbowyg-textarea').each(function () {
          let draft_id = draft.draft_get_id($(this).index('.trumbowyg-textarea'));
          if (localStorage.getItem(draft_id)) {
            localStorage.removeItem(draft_id);
            draft.data = '';
            console.log('trmb remove data');
          }
        });
      });
    } else if (editors.length > 0 && ($('.lesson-inner').length > 0 || $('#lk').length > 0)) {
      let draft = new EditorDraft();

      editors.forEach(function(editor) {
        let draft_id = draft.draft_get_id($(editor).attr('id'));
        let html = localStorage.getItem(draft_id);

        if (html) {
          editor.setData(html);
        }

        editor.on('change', function (evt) {
          setTimeout(
            () => {
              let data = evt.editor.getData();
              draft.save($(this).attr('id'), data);
            },
            300
          );
        });

        editor.on('afterPaste', function (evt) {
          let range = evt.editor.createRange();
          range.moveToElementEditablePosition(evt.editor.editable(), true);
          evt.editor.getSelection().selectRanges([range]);
        });

        $(document).on('submit', $(editor).parents('form'), function () {
          if (localStorage.getItem(draft_id)) {
            localStorage.removeItem(draft_id);
            draft.data = '';
            console.log('trmb remove data');
          }
        });
      });
    }
  } catch (e) {
    console.error(e.message);
  }
});