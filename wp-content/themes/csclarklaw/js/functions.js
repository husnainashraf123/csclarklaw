jQuery(document).ready(function(){
    jQuery("#book").submit(function( )
    {
        jQuery("ErrorMsg").hide( );
        var objFV = new FormValidator("book", "ErrorMsg");
        if (!objFV.validate("name1", "B", "Please enter your name."))
            return false;
        if (!objFV.validate("email1", "B,E", "Please enter valid email."))
            return false;
        if (!objFV.validate("phone1", "B", "Please enter client phone#."))
            return false;
        jQuery("#btn-download").attr('disabled', true);
        
        var url = jQuery(this).attr('action');
        jQuery.post(url, 
            jQuery("#book").serialize( ),
            function (sResponse)
            {	
                var sParams = sResponse.split("|-|");
                showMessage("#ErrorMsg", sParams[0], sParams[1]);		
                if (sParams[0] == "alert alert-success")
                {
                    jQuery('#book-title-1').html('Thank you for downloading the guide')
                    jQuery('.popup-form').html("<p>If your download doesn't start automatically: <a href='"+sParams[2]+"'> CLICK HERE </a> </p>")
                    jQuery.fileDownload(sParams[2], {
                    
                        });

                }
            },

            "text");
    });
    jQuery('#ErrorMsg').click(function() {
        jQuery(this).hide(350);
    });
   jQuery("#book-2").submit(function( )
    {
        jQuery("ErrorMsg-2").hide( );
        var objFV = new FormValidator("book-2", "ErrorMsg-2");
        if (!objFV.validate("name2", "B", "Please enter your name."))
            return false;
        if (!objFV.validate("email2", "B,E", "Please enter valid email."))
            return false;
        if (!objFV.validate("phone2", "B", "Please enter client phone#."))
            return false;
        jQuery("#btn-download-2").attr('disabled', true);
        
        var url = jQuery(this).attr('action');
        jQuery.post(url, 
            jQuery("#book-2").serialize( ),
            function (sResponse)
            {	
                var sParams = sResponse.split("|-|");
                showMessage("#ErrorMsg-2", sParams[0], sParams[1]);		
                if (sParams[0] == "alert alert-success")
                {
                    jQuery('#book-title-2').html('Thank you for downloading the guide')
                    jQuery('.popup-form-2').html("<p>If your download doesn't start automatically: <a href='"+sParams[2]+"'> CLICK HERE </a> </p>")
                    jQuery.fileDownload(sParams[2], {
                    
                        });

                }
            },

            "text");
    });
    /*---------|||||| Validation |||||--------------*/
    
    /* jQuery("#book").validate({
        rules: {
            name: "required",
            phone: "required",
            email: {
                required: true,
                email: true
            }
        },
        messages: {
            name: " Required",
            phone: " Required",
            email: {
                required: " Required",
                email: " Invalid"
            }
        }
    });*/
    // Phone formate to .phone class
    jQuery(function(jQuery){
        jQuery(".phone-formate").mask("(999) 999-9999");
    });
    /*---------|||||| Responsive Main Menu |||||--------------*/
    
    // Create the dropdown base
    //    jQuery('<select />').appendTo('.navbar-top .container');

    // Create default option 'Go to...'
    //    jQuery('<option />', {
    //        'selected': 'selected',
    //        'value'   : '',
    //        'text'    : 'Main Navigation'
    //    }).appendTo('.navbar-top select');

    // Populate dropdown with menu items
    //    jQuery('.navbar-top a').each(function() {
    //        var el = jQuery(this);
    //
    //        if(jQuery(el).parents('.sub-menu .sub-menu').length >= 1) {
    //            jQuery('<option />', {
    //                'value'   : el.attr('href'),
    //                'text'    : '--- ' + el.text()
    //            }).appendTo('.navbar-top select');
    //        }
    //        else if(jQuery(el).parents('.sub-menu').length >= 1) {
    //            jQuery('<option />', {
    //                'value'   : el.attr('href'),
    //                'text'    : '- ' + el.text()
    //            }).appendTo('.navbar-top select');
    //        }
    //        else {
    //            jQuery('<option />', {
    //                'value'   : el.attr('href'),
    //                'text'    : el.text()
    //            }).appendTo('.navbar-top select');
    //        }
    //    });
//    var jQuerymenu_select = jQuery("<select />");	
//    jQuery("<option />", {
//        "selected": "selected", 
//        "value": "", 
//        "text": "Main Navigation"
//    }).appendTo(jQuerymenu_select);
//    jQuerymenu_select.appendTo(".navbar-top .container");
//    jQuery(".navbar-top ul li a").each(function(){
//        var menu_url = jQuery(this).attr("href");
//        var menu_text = jQuery(this).text();
//        if (jQuery(this).parents("li").length == 2) {
//            menu_text = '- ' + menu_text;
//        }
//        if (jQuery(this).parents("li").length == 3) {
//            menu_text = "-- " + menu_text;
//        }
//        if (jQuery(this).parents("li").length > 3) {
//            menu_text = "--- " + menu_text;
//        }
//        if (jQuery(this).parents("li").length > 4) {
//            menu_text = "---- " + menu_text;
//        }
//        jQuery("<option />", {
//            "value": menu_url, 
//            "text": menu_text
//        }).appendTo(jQuerymenu_select)
//    })
//    
//    jQuery('.navbar-top select').ddslick({
//        width: '100%',
//        onSelected: function(selectedData){
//            if(selectedData.selectedData.value != '') {
//                window.location = selectedData.selectedData.value;
//            }
//        }   
//    });
  
   	
});	