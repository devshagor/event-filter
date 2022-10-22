jQuery(function($){
   $('.cat-list-item').on('click', function() {
      $('.cat-list-item').removeClass('active');
      $(this).addClass('active');
  
      $.ajax({
         type: 'POST',
         url: wpAjax.ajax_url,
         dataType: 'html',
         data: {
            action: 'filter_posts',
            category: $(this).data('slug'),
            location: $(this).data('slug'),
         },
            success: function(res) {
            $('.event-row').html(res);
         }
      })
   });
});