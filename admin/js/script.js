(function ($) {
  Notify_me={
    init:function(){
      $(document).on('change','#timee',function(){
        var value=$('#timee').val();
        $('#timeeLabel').text(value);
      });

    }
  }
Notify_me.init();
})(jQuery);