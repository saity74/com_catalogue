jQuery( document ).ready(function( $ ) {

    $('#attrForm').on("change", 'input, select', function(){
        var
            name = $(this).attr('name').match(/\[image_([^\]]+)/)[1],
            value = $(this).val();
        if (name) {
            $('.selected input[data-attr="'+name+'"]').val(value);
        }
    });

    $('#attrForm input').prop('disabled', true);

    $('#imagesContainer').on('click', 'li', function(){
        $('#imagesContainer li.selected').removeClass('selected');
        $('#attrForm input').prop('disabled', false);
        $(this).addClass('selected');
        $(this).find('input.editable[type=hidden]').each(function(i, el){
            var
                $el         = $(el),
                $update_el  = $('#jform_image_'+$el.data('attr'));

            if ($update_el.prop("tagName") == 'SELECT')
            {
                $update_el.val($el.val().split(','));
                $update_el.trigger("liszt:updated");
            }
            else
            {
                $update_el.val($el.val())
            }
        })
    })
});