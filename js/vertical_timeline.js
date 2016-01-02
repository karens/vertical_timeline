(function ($) {
  Drupal.behaviors.VerticalTimelineBehavior = {
    attach: function (context, settings) {

    var container = $('.grid');

    container.masonry({
      // use outer width of grid-sizer for columnWidth
      columnWidth: '.grid-sizer',
      gutter: '.gutter-sizer',
      // do not use .grid-sizer in layout
      itemSelector: '.grid-item',
      percentPosition: true
    });

    container.bind('change', function() {
      container.masonry('reloadItems');
      container.masonry();
    });

    var s = container.find('.grid-item');
    $.each(s, function(i, obj) {
      var posLeft = $(obj).css("left");
      if (posLeft == "0px") {
        $(obj).addClass('grid-item-left');
      }
      else {
        $(obj).addClass('grid-item-right');
      }
    });

  }};

  Drupal.behaviors.infiniteMasonry = {
    attach: function(context, settings){
      $('.view .masonry').change(function(e){
        $(this).masonry('reload');
      });
    }
  };

})(jQuery);