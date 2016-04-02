
$(function(){

    $('#messageReceiverSelect').on('change', function() {
        $('#messageReceiver').val($(this).val());
    });


    $('.disabledLink').on('click', function(e){
        e.preventDefault();
    });

})