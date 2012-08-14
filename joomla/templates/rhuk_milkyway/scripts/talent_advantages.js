
// create object that we can call with the qS PARAM:w
var TalentAdvantages = TalentAdvantages || (function(){
    return {
        clicked: false,
        initialize: function( talent_advantage_identifier )
        {
            if( this.clicked )
            {
                return false;
            }
            var element = jQuery('#talent_advantage_'+talent_advantage_identifier);
            if( element )
            {
                jQuery(element).find('.list_expanded').show();
            }
            this.clicked = true;
        }
    };
})();
