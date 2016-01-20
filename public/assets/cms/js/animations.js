$(document).ready(function(){
    // inputs
    $('.input-animate input').attr('readonly', true);
    $(document).on('focus', '.input-animate input', function() {
        $(this).attr('readonly', false).parent().find('label').animate({
            'top': '-30px',
            'font-size': '0.8em'
        }, 200);
    }).on('blur', '.input-animate input', function() {

        if($(this).val() !== '') return;

        $(this).parent().find('label').animate({
            'top': '0px',
            'font-size': '1em'
        }, 100);
    });
});