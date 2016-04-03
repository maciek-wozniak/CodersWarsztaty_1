
$(function(){

    $('#messageReceiverSelect').on('change', function() {
        $('#messageReceiver').val($(this).val());
    });


    $('.disabledLink').on('click', function(e){
        e.preventDefault();
    });

    $('.stopPropagation').on('click', function(e) {
        e.stopPropagation();
    });

    $('.openMsg').on('click', function(){
        if ($(this).html() == 'Otwórz') {
            $(this).html('Zamknij');
        }
        else {
            $(this).html('Otwórz');
        }
    });

})