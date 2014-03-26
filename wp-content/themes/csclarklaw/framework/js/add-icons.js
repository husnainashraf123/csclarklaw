/*
 * @package WordPress
 * @subpackage Xpert
 * @since Xpert 1.0
 * 
 * Define action for icons
 * 
 */

/* --------------Add icon ----------------- */
(function() {  
    tinymce.create('tinymce.plugins.icons', {  
        init : function(ed, url) {  
            ed.addButton('icons', {  
                title : 'Add Icons',  
                image : url+'/icons/add-icons.png',  
                onclick : function() { 
                    jQuery('#add-icons').modal({
                        onShow: function (dialog) {
                            jQuery("a", dialog.data).click(function(event) {
                                event.preventDefault();
                                color = jQuery("#icolor").attr('value');
                                isize = jQuery("#icon-size").attr('value');
                                icon = jQuery(this).attr("href");
                                if(color!= ''&& isize!= '' && icon!= '' ){
                                    ed.selection.setContent("[icons color='#"+color+"' iconsize='"+isize+"' icon='"+icon+"']");
                                }
                                jQuery.modal.close();
                                return false;
                            });
                        }
                    });
            
  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('icons', tinymce.plugins.icons);  
})();

/* --------------Add Phone Number ----------------- */
(function() {  
    tinymce.create('tinymce.plugins.number', {  
        init : function(ed, url) {  
            ed.addButton('number', {  
                title : 'Add Phone Number',  
                image : url+'/icons/add-number.png',  
                onclick : function() {  
                    ed.selection.setContent('[number type="1"]');  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('number', tinymce.plugins.number);  
})();
/* --------------Add Labels ----------------- */
(function() {  
    tinymce.create('tinymce.plugins.label', {  
        init : function(ed, url) {  
            ed.addButton('label', {  
                title : 'Add Label',  
                image : url+'/icons/add-label.png',  
                onclick : function() {  
                    ed.selection.setContent('[label color="red"]Tag Text Here[/label]');  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('label', tinymce.plugins.label);  
})();
/* --------------Add Tabs ----------------- */
(function() {  
    tinymce.create('tinymce.plugins.tabs', {  
        init : function(ed, url) {  
            ed.addButton('tabs', {  
                title : 'Add Tabs',  
                image : url+'/icons/add-tabs.png',  
                onclick : function() {  
                    ed.selection.setContent('[tabgroup layout="horizontal"]<br />[tab title="Tab 1"] <br />Tab 1 contents goes here...<br />[/tab]<br />[tab active="yes" title="Tab 2" icon="tasks"]<br /> Tab 2 contents goes here...<br />[/tab]<br />[tab  icon="legal"]<br />Tab 3 contents goes here...<br />[/tab]<br />[/tabgroup]');  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('tabs', tinymce.plugins.tabs);  
})();

/* --------------Add Alert box ----------------- */
(function(){  
    tinymce.create('tinymce.plugins.alerts', {  
        init : function(ed, url) {  
            ed.addButton('alerts', {  
                title : 'Add Alert Box',  
                image : url+'/icons/add-alerts.png',  
                onclick : function() { 
                    jQuery('#add-alerts').modal({
                        onShow: function (dialog) {
                            jQuery("a", dialog.data).click(function(event){
                                event.preventDefault();
                                alert_type = jQuery("#alert_type").attr('value');
                                alertcontent = jQuery("#alertcontent").attr('value');
                                alert_close = jQuery("#alert_close").attr('value');
                                if(alert_type!= ''&& alertcontent!= ''){
                                    ed.selection.setContent("[alert type='"+alert_type+"' close='"+alert_close+"']"+alertcontent+"[/alert]");
                                }
                                jQuery.modal.close();
                                return false;
                            });
                        }
                    });
                    
  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('alerts', tinymce.plugins.alerts);  
})();

/* --------------Add Container box ----------------- */
(function(){  
    tinymce.create('tinymce.plugins.box', {  
        init : function(ed, url) {  
            ed.addButton('box', {  
                title : 'Add Container Box ',  
                image : url+'/icons/add-well.png',  
                onclick : function() { 
                    jQuery('#add-box').modal({
                        onShow: function (dialog) {
                            jQuery("a", dialog.data).click(function(event) {
                                event.preventDefault();
                                background_color = jQuery("#background_color").attr('value');
                                boxcontent = jQuery("#boxcontent").attr('value');
                                if(background_color!= ''&& boxcontent!= ''){
                                    ed.selection.setContent("[box background_color='"+background_color+"']"+boxcontent+"[/box]");
                                }
                                jQuery.modal.close();
                                return false;
                            });
                        }
                    });
                    
  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('box', tinymce.plugins.box);  
})();
/* --------------Add Video ----------------- */
(function(){  
    tinymce.create('tinymce.plugins.video', {  
        init : function(ed, url) {  
            ed.addButton('video', {  
                title : 'Add Video ',  
                image : url+'/icons/add-video.png',  
                onclick : function() { 
                    jQuery('#add-video').modal({
                        onShow: function (dialog) {
                            jQuery("a", dialog.data).click(function(event) {
                                event.preventDefault();
                                type = jQuery("#video_type").attr('value');
                                video_id = jQuery("#video_id").attr('value');
                                auto_play = jQuery("#auto_play").attr('value');
                                video_width = jQuery("#video_width").attr('value');
                                video_height = jQuery("#video_height").attr('value');
                                if(video_id!= ''&& type!= ''){
                                    ed.selection.setContent("[video type='"+type+"' id='"+video_id+"' width='"+video_width+"' height='"+video_height+"' autoplay='"+auto_play+"']");
                                }
                                jQuery.modal.close();
                                return false;
                            });
                        }
                    });
                    
  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('video', tinymce.plugins.video);  
})();
/* --------------Add Map ----------------- */
(function(){  
    tinymce.create('tinymce.plugins.map', {  
        init : function(ed, url) {  
            ed.addButton('map', {  
                title : 'Add Google Map ',  
                image : url+'/icons/add-map.png',  
                onclick : function() { 
                    jQuery('#add-map').modal({
                        onShow: function (dialog) {
                            jQuery("a", dialog.data).click(function(event) {
                                event.preventDefault();
                                randomNum = Math.ceil(Math.random()*50);
                                map_address = jQuery("#address").attr('value');
                                map_width = jQuery("#map_width").attr('value');
                                map_height = jQuery("#map_height").attr('value');
                                map_type = jQuery("#map_type").attr('value');
                                map_marker = jQuery("#map_marker").attr('value');
                                map_traffice = jQuery("#map_traffice").attr('value');
                                map_style = jQuery("#map_style").attr('value');
                                map_controls = jQuery("#map_controls").attr('value');
                                map_infowin = jQuery("#map_infowin").attr('value');
                                map_infowintext = jQuery("#map_infowintext").attr('value');
                                map_lat = jQuery("#map_lat").attr('value');
                                map_long = jQuery("#map_long").attr('value');
                                map_mark_img = jQuery("#map_mark_img").attr('value');
                                if(map_address!= ''&& map_type!= ''){
                                    ed.selection.setContent('[map address="'+map_address+'" width="'+map_width+'" height="'+map_height+'"  traffic="'+map_traffice+'" style="'+map_style+'" zoom="16" marker="'+map_marker+'" infowindow="'+map_infowintext+'" infowindowdefault="'+map_infowin+'" maptype="'+map_type+'" hidecontrols="'+map_controls+'" map_id="map'+randomNum+'"]');
                                }
                                jQuery.modal.close();
                                return false;
                            });
                        }
                    });
                    
  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('map', tinymce.plugins.map);  
})();