(function($) {
    jQuery.fn.lightTabs = function(options) {

        var createTabs = function() {
            let count = 1;
            let tabs = this;
            let i = 0;
            let data_page = "data-page-" + count;

            let showPage = function(i) {
                $(tabs).children("div").children("div").hide();
                $(tabs).children("div").children("div").eq(i).show();
                $(tabs).children("ul").children("li").removeClass("active");
                $(tabs).children("ul").children("li").eq(i).addClass("active");
            }

            showPage(0);

            $(tabs).children("ul").children("li").each(function(index, element) {
                $(element).attr("data-page", i++);
            });

            $(tabs).children("ul").children("li").click(function() {
                showPage(parseInt($(this).attr("data-page")));
            });
        };

        return this.each(createTabs);
    };
})(jQuery);

$(function() {
  $('.tabs > ul > li:first-child').addClass('active');
  $('.tabs > .admin_form > div:first-child').addClass('active');

  let tab_index = 'tab' + document.location.pathname.replace('/','-');

  $('.tabs > ul').each(function(i) {
    var storage = localStorage.getItem(tab_index + '-index-' + i);
    if (storage) {
      $(this).find('li').removeClass('active').eq(storage).addClass('active')
        .closest('div.tabs').find('.admin_form > div').removeClass('active').eq(storage).addClass('active');
    }
  });

  $('.tabs > ul').on('click', 'li:not(.active)', function() {
    $(this)
      .addClass('active').siblings().removeClass('active')
      .closest('div.tabs').find('.admin_form > div').removeClass('active').eq($(this).index()).addClass('active');
    var ulIndex = $('.tabs > ul').index($(this).parents('.tabs > ul'));

    localStorage.removeItem(tab_index + '-index-' + ulIndex);
    localStorage.setItem(tab_index + '-index-' + ulIndex, $(this).index());
  });
});

$(function() {

  $('.tabs-statistics-control > ul').each(function(i) {
    var storage = localStorage.getItem('tab' + i);
    if (storage) {
      $(this).find('li').removeClass('active').eq(storage).addClass('active')
        .closest('.tabs-statistics').find('.admin_form > div').removeClass('active').eq(storage).addClass('active');
    }
  });

  $('.tabs-statistics-control > ul').on('click', 'li:not(.active)', function() {
    $(this)
      .addClass('active').siblings().removeClass('active')
      .closest('.tabs-statistics').find('.admin_form > div').removeClass('active').eq($(this).index()).addClass('active');
    var ulIndex = $('.tabs-statistics-control > ul').index($(this).parents('.tabs-statistics-control > ul'));
    localStorage.removeItem('tab' + ulIndex);
    localStorage.setItem('tab' + ulIndex, $(this).index());
  });

});