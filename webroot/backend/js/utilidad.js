$('.js-manual').on('change', function(){
    var input_select  = $(this).siblings('.js-input-select');
    var input_manual  = $(this).siblings('.js-input-manual');
    console.log('js-manual');
    if ($(this).is(':checked')) {
        input_select.addClass('hidden');
        input_manual.removeClass('hidden');
    }else{
        input_manual.addClass('hidden');
        input_select.removeClass('hidden');
    }
});

$('.js-usar-pmp').on('click', function(){
    console.log('js-usar-pmp');
    var input  = $(this).siblings('.js-costo-pmp');
    input.val($(this).data('value'));
});

	
$(document).on('click', '.js-column', function(){

    $('.js-body').find('td').removeClass('success');
    $('.js-column').parents('table').eq(0).find('th').removeClass('success');

    if ($(this).is(':checked')) {
        var pos = $(this).parents('th').eq(0).index();
        
        $(this).parents('th').eq(0).addClass('success');

        $('.js-body tr').each(function(){
            $(this).find('td').eq(pos).addClass('success');
        });

    }
});

$('.clone-boton').on('click', function(){
    // var clone_tr  = document.getElementsByClassName("clone-tr");
    console.log('clone-boton');
    
    
});

$(document).on('click', '.clone-boton', function(){

    console.log('clone-boton');
});