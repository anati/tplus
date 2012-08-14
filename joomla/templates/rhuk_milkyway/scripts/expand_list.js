jQuery(document).ready(function(){
    jQuery('.list_expanded').hide();
    jQuery('.list_title').find('a').attr('href', 'javascript:void(0);');
    jQuery('.list_title').find('a').click(function(event)
    {
        jQuery(this).parent().parent().find('.list_expanded').toggle();
    });
});
