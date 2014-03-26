<?php

add_action('init', 'add_editor_buttons');

function add_editor_buttons() {
    if (current_user_can('edit_posts') && current_user_can('edit_pages')) {
        add_filter('mce_external_plugins', 'add_admin_icon');
        add_filter('mce_buttons_3', 'addd_row3');
        wp_register_script('poupModal', get_template_directory_uri() . '/framework/js/jquery.simplemodal.js', 'jquery', '1.0', TRUE);
        wp_register_script('function.admin', get_template_directory_uri() . '/framework/js/function.admin.php', 'jquery', '1.0', TRUE);
        wp_enqueue_script('poupModal');
        wp_enqueue_script('function.admin');
        wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/font-awesome.css');
        wp_enqueue_style('modalstyle', get_template_directory_uri() . '/framework/css/modal.css');
    }
}

//add row 3 in editor
function addd_row3($buttons) {
    array_push($buttons,'icons', 'alerts', 'box',  'video', "map", "tabs",'number', "label");
    return $buttons;
}

function add_admin_icon($plugin_array) {
    $icon_array['icons'] = get_template_directory_uri() . '/framework/js/add-icons.js';
    $icon_array['alerts'] = get_template_directory_uri() . '/framework/js/add-icons.js';
    $icon_array['video'] = get_template_directory_uri() . '/framework/js/add-icons.js';
    $icon_array['map'] = get_template_directory_uri() . '/framework/js/add-icons.js';
    $icon_array['number'] = get_template_directory_uri() . '/framework/js/add-icons.js';
    $icon_array['box'] = get_template_directory_uri() . '/framework/js/add-icons.js';
    $icon_array['tabs'] = get_template_directory_uri() . '/framework/js/add-icons.js';
    $icon_array['label'] = get_template_directory_uri() . '/framework/js/add-icons.js';
    return $icon_array;
}

/* --------------Add icon ----------------- */

function add_icon($atts, $content = null) {
    extract(shortcode_atts(array(
                'icon' => 'camera-retro',
                'color' => '',
                'iconsize' => '1x'
                    ), $atts));

    $icon_element = '<i style="color:' . $color . ' "class="icon-' . $icon . ' icon-' . $iconsize . '"></i>';
    return $icon_element;
}

add_shortcode('icons', 'add_icon');

/* ---------------Add labels shortcodes-------------------- */


function xpert_shortcodes($content) {
    $block = join("|", array("box", "tabgroup", "tab",));

    //  remove p from opening tag
    $rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content);
    //  remove p from eding tag
    $rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)/", "[/$2]", $rep);

    return $rep;
}

add_filter('the_content', 'xpert_shortcodes');

function content_labels($atts, $content = null) {
    extract(shortcode_atts(array(
                'color' => 'red'
                    ), $atts));
    if ($color == 'red') {
        $class = 'label label-important';
    } elseif ($color == 'blue') {
        $class = 'label label-info';
    } elseif ($color == 'green') {
        $class = 'label label-success';
    } elseif ($color == 'gray') {
        $class = 'label';
    } elseif ($color == 'yellow') {
        $class = 'label label-warning';
    } elseif ($color == 'black') {
        $class = 'label label-inverse';
    }
    return '<span class="' . $class . '">' . do_shortcode($content) . '</span>';
}

add_shortcode('label', 'content_labels');

/* ---------------Add labels shortcodes-------------------- */

function phone_number($atts, $content = null) {
    global $data;
    extract(shortcode_atts(array(
                'type' => '1'
                    ), $atts));
    if ($type == '1') {
        $return = $data['phone1'];
    } elseif ($type == '2') {
        $return = $data['phone2'];
    }
    return $return;
}

add_shortcode('number', 'phone_number');

/* -------------------------- Add Google Map------------------------ */

add_action('wp_head', 'google_script');

function google_script() {
    ?>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <?php

}

function google_map($atts) {
    $atts = shortcode_atts(array(
        'lat' => '0',
        'long' => '0',
        'map_id' => 'map',
        'zoom' => '1',
        'width' => '400',
        'height' => '300',
        'maptype' => 'ROADMAP',
        'address' => '',
        'kml' => '',
        'kmlautofit' => 'yes',
        'marker' => '',
        'markerimage' => '',
        'traffic' => 'no',
        'bike' => 'no',
        'fusion' => '',
        'start' => '',
        'end' => '',
        'infowindow' => '',
        'infowindowdefault' => 'yes',
        'directions' => '',
        'hidecontrols' => 'false',
        'scale' => 'false',
        'scrollwheel' => 'true',
        'style' => ''
            ), $atts);

    $returnme = '<div id="' . $atts['map_id'] . '" style="width:' . $atts['width'] . 'px;height:' . $atts['height'] . 'px;" class="google_map ' . $atts['style'] . '"></div>';

    //directions panel
    if ($atts['start'] != '' && $atts['end'] != '') {
        $panelwidth = $atts['width'] - 20;
        $returnme .= '<div id="directionsPanel" style="width:' . $panelwidth . 'px;height:' . $atts['height'] . 'px;border:1px solid gray;padding:10px;overflow:auto;"></div><br>';
    }

    $returnme .= '
	<script type="text/javascript">
		var latlng = new google.maps.LatLng(' . $atts['lat'] . ', ' . $atts['long'] . ');
		var myOptions = {
			zoom: ' . $atts['zoom'] . ',
			center: latlng,
			scrollwheel: ' . $atts['scrollwheel'] . ',
			scaleControl: ' . $atts['scale'] . ',
			disableDefaultUI: ' . $atts['hidecontrols'] . ',
			mapTypeId: google.maps.MapTypeId.' . $atts['maptype'] . '
		};
		var ' . $atts['map_id'] . ' = new google.maps.Map(document.getElementById("' . $atts['map_id'] . '"),
		myOptions);
		';

    //kml
    if ($atts['kml'] != '') {
        if ($atts['kmlautofit'] == 'no') {
            $returnme .= '
				var kmlLayerOptions = {preserveViewport:true};
				';
        } else {
            $returnme .= '
				var kmlLayerOptions = {preserveViewport:false};
				';
        }
        $returnme .= '
			var kmllayer = new google.maps.KmlLayer(\'' . html_entity_decode($atts['kml']) . '\',kmlLayerOptions);
			kmllayer.setMap(' . $atts['map_id'] . ');
			';
    }

    //directions
    if ($atts['start'] != '' && $atts['end'] != '') {
        $returnme .= '
			var directionDisplay;
			var directionsService = new google.maps.DirectionsService();
		    directionsDisplay = new google.maps.DirectionsRenderer();
		    directionsDisplay.setMap(' . $atts['map_id'] . ');
    		directionsDisplay.setPanel(document.getElementById("directionsPanel"));

				var start = \'' . $atts['start'] . '\';
				var end = \'' . $atts['end'] . '\';
				var request = {
					origin:start, 
					destination:end,
					travelMode: google.maps.DirectionsTravelMode.DRIVING
				};
				directionsService.route(request, function(response, status) {
					if (status == google.maps.DirectionsStatus.OK) {
						directionsDisplay.setDirections(response);
					}
				});


			';
    }

    //traffic
    if ($atts['traffic'] == 'yes') {
        $returnme .= '
			var trafficLayer = new google.maps.TrafficLayer();
			trafficLayer.setMap(' . $atts['map_id'] . ');
			';
    }

    //bike
    if ($atts['bike'] == 'yes') {
        $returnme .= '			
			var bikeLayer = new google.maps.BicyclingLayer();
			bikeLayer.setMap(' . $atts['map_id'] . ');
			';
    }

    //fusion tables
    if ($atts['fusion'] != '') {
        $returnme .= '			
			var fusionLayer = new google.maps.FusionTablesLayer(' . $atts['fusion'] . ');
			fusionLayer.setMap(' . $atts['map_id'] . ');
			';
    }

    //address
    if ($atts['address'] != '') {
        $returnme .= '
		    var geocoder_' . $atts['map_id'] . ' = new google.maps.Geocoder();
			var address = \'' . $atts['address'] . '\';
			geocoder_' . $atts['map_id'] . '.geocode( { \'address\': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					' . $atts['map_id'] . '.setCenter(results[0].geometry.location);
					';

        if ($atts['marker'] != '') {
            //add custom image
            if ($atts['markerimage'] != '') {
                $returnme .= 'var image = "' . $atts['markerimage'] . '";';
            }
            $returnme .= '
						var marker = new google.maps.Marker({
							map: ' . $atts['map_id'] . ', 
							';
            if ($atts['markerimage'] != '') {
                $returnme .= 'icon: image,';
            }
            $returnme .= '
							position: ' . $atts['map_id'] . '.getCenter()
						});
						';

            //infowindow
            if ($atts['infowindow'] != '') {
                //first convert and decode html chars
                $thiscontent = htmlspecialchars_decode($atts['infowindow']);
                $returnme .= '
							var contentString = \'' . $thiscontent . '\';
							var infowindow = new google.maps.InfoWindow({
								content: contentString
							});
										
							google.maps.event.addListener(marker, \'click\', function() {
							  infowindow.open(' . $atts['map_id'] . ',marker);
							});
							';

                //infowindow default
                if ($atts['infowindowdefault'] == 'yes') {
                    $returnme .= '
									infowindow.open(' . $atts['map_id'] . ',marker);
								';
                }
            }
        }
        $returnme .= '
				} else {
				alert("Geocode was not successful for the following reason: " + status);
			}
			});
			';
    }

    //marker: show if address is not specified
    if ($atts['marker'] != '' && $atts['address'] == '') {
        //add custom image
        if ($atts['markerimage'] != '') {
            $returnme .= 'var image = "' . $atts['markerimage'] . '";';
        }

        $returnme .= '
				var marker = new google.maps.Marker({
				map: ' . $atts['map_id'] . ', 
				';
        if ($atts['markerimage'] != '') {
            $returnme .= 'icon: image,';
        }
        $returnme .= '
				position: ' . $atts['map_id'] . '.getCenter()
			});
			';

        //infowindow
        if ($atts['infowindow'] != '') {
            $returnme .= '
				var contentString = \'' . $atts['infowindow'] . '\';
				var infowindow = new google.maps.InfoWindow({
					content: contentString
				});
							
				google.maps.event.addListener(marker, \'click\', function() {
				  infowindow.open(' . $atts['map_id'] . ',marker);
				});
				';
            //infowindow default
            if ($atts['infowindowdefault'] == 'yes') {
                $returnme .= '
						infowindow.open(' . $atts['map_id'] . ',marker);
					';
            }
        }
    }

    $returnme .= '</script>';


    return $returnme;
}

add_shortcode('map', 'google_map');

/* -------------------------- Add Tabs------------------------ */

function xpert_tabgroup($atts, $content = null) {
    extract(shortcode_atts(array(
                'layout' => 'horizontal'
                    ), $atts));
    $GLOBALS['tab_count'] = 0;
    $i = 1;
    $randomid = rand();
    do_shortcode($content);
    if (is_array($GLOBALS['tabs'])) {
        foreach ($GLOBALS['tabs'] as $tab) {
            if ($tab['icon'] != '') {
                $icon = '<i class="icon-' . $tab['icon'] . '"></i>';
            } else {
                $icon = '';
            }
            if ($tab['active'] == 'yes') {
                $active_tab = 'active';
            } else {
                $active_tab = '';
            }
            $tabs[] = '<li class = "' . $active_tab . '"><a  data-toggle="tab" href="#tab' . $randomid . $i . '">' . $icon . $tab['title'] . '</a></li>';
            $panes[] = '<div class="tab-pane ' . $active_tab . '" id="tab' . $randomid . $i . '"><p>' . do_shortcode($tab['content']) . '</p></div>';
            $i++;
            $icon = '';
        }
        $return = '<div class="tabset layout-' . $layout . '"><ul class="tabs">' . implode("\n", $tabs) . '</ul><div class="tab-content">' . implode("\n", $panes) . '</div></div>';
    }
    return $return;
}

add_shortcode('tabgroup', 'xpert_tabgroup');

function xpert_tab($atts, $content = null) {
    extract(shortcode_atts(array(
                'title' => '',
                'icon' => '',
                'active' => ''
                    ), $atts));
    $x = $GLOBALS['tab_count'];
    $GLOBALS['tabs'][$x] = array('title' => sprintf($title, $GLOBALS['tab_count']), 'icon' => $icon, 'active' => $active, 'content' => $content);
    $GLOBALS['tab_count']++;
}

add_shortcode('tab', 'xpert_tab');

/* --------------Container Box  ----------------- */

function container_box($atts, $content = null) {
    extract(shortcode_atts(array(
                'background_color' => 'F5F5F5'
                    ), $atts));
    $html = '<div class="box" style="background-color:#' . $background_color . ';">';
    $html .= do_shortcode($content);
    $html .= '</div>';
    return $html;
}

add_shortcode('box', 'container_box');

/* -------------------------- Add Alert------------------------ */

function xpert_alert($atts, $content = null) {
    extract(shortcode_atts(array(
                'type' => 'warning',
                'close' => 'yes'
                    ), $atts));
    if ($close == 'no') {
        $close_btn = '';
    } else {
        $close_btn = '<button class="close" type="button" data-dismiss="alert">×</button>';
    }
    return '<div class="alert alert-' . $type . '">' . $close_btn . ' ' . do_shortcode($content) . '</div>';
}

add_shortcode('alert', 'xpert_alert');

/* ------------EOF Shortcodes--------------- */
?>
