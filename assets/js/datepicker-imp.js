jQuery(document).ready(function(){
    
    jQuery( ".date-time-pick-zs-util" ).datepicker(
        {
            onSelect: function(dateText, inst)
            {
                console.log(this);
                var dateObject = jQuery(this).datepicker('getDate');
                console.log(dateObject);
                //var unixDate = jQuery(this).datepicker.formatDate("@", new Date(dateObject));
                //console.log(unixDate);
                jQuery(this).attr('value', dateObject);
            },
            attributeBindings: ["value"]
        }
    );
    //console.log(datetime_pick_obj.datepicker("getDate"));
    //jQuery( ".date-time-pick-zs-util" ).val(datetime_pick_obj.datepicker("getDate").formatDate('@'));
    
});