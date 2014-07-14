jQuery(document).ready(function(){
    
    jQuery( ".date-time-pick-zs-util" ).datepicker(
        {
            dateFormat: 'mm/dd/yy',
            onSelect: function(dateText, inst)
            {
                //console.log(this);
                //var dateObject = jQuery(this).datepicker('getDate');
                //console.log(dateText);
                //var unixDate = jQuery(this).datepicker.formatDate("@", new Date(dateObject));
                //console.log(unixDate);
                //dateObject = jQuery(this).datepicker('getDate');
                jQuery(this).attr('value', dateText);
            },
            attributeBindings: ["value"]
        }
    );
    //console.log(datetime_pick_obj.datepicker("getDate"));
    //jQuery( ".date-time-pick-zs-util" ).val(datetime_pick_obj.datepicker("getDate").formatDate('@'));
    
});