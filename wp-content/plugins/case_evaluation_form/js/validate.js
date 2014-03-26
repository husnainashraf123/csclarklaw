jQuery(document).ready(function(){
    jQuery("#caseform").validate({
        rules: {
            caseformname: "required",
            caseformphone: "required",
            caseformmessage: "required",

            caseformemail: {
                required: true,
                email: true
            }
        },
        messages: {
            caseformname: " Required",
            caseformphone: " Required",
            caseformmessage: " Required",
            caseformemail: {
                required: " Required",
                email: " Invalid"
            }
        }
    });
    jQuery('#caseformsubmit').click(function(){
        var re = /((?:https?\:\/\/|www\.)(?:[-a-z0-9]+\.)*[-a-z0-9]+.*)/i;
        var  re1 = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
        var trimmed = jQuery.trim(document.getElementById('caseformmessage').value)
        if(trimmed.match(re) != null || re1.test(trimmed) == true){
            jQuery('input#caseformlink').val('1');
        }
    });   
    jQuery('input#caseformlink').val('0');
});
jQuery(function($){
    $("#caseformphone").mask("(999) 999-9999");
});