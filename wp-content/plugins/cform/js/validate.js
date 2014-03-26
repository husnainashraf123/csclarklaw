jQuery(document).ready(function(){
    jQuery("#cform").validate({
        rules: {
            cformname: "required",
            cformphone: "required",
            cformmessage: "required",

            cformemail: {
                required: true,
                email: true
            }
        },
        messages: {
            cformname: " Required",
            cformphone: " Required",
            cformmessage: " Required",
            cformemail: {
                required: " Required",
                email: " Invalid"
            }
        }
    });
    jQuery('#cformsubmit').click(function(){
        var re = /((?:https?\:\/\/|www\.)(?:[-a-z0-9]+\.)*[-a-z0-9]+.*)/i;
        var  re1 = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
        var trimmed = jQuery.trim(document.getElementById('cformmessage').value)
        if(trimmed.match(re) != null || re1.test(trimmed) == true){
            jQuery('input#cformlink').val('1');
        }
    });   
    jQuery('input#cformlink').val('0');
});
jQuery(function($){
    $("#cformphone").mask("(999) 999-9999");
});