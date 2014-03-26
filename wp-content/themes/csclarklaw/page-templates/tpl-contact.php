<?php
/**
 * Template Name: Contact us
 *
 */
get_header();
global $data;
?>
<div class="clearfix inner-page"></div>
<div  class="container" id="mov-top">
    <div  class="inner-contents " style="padding-top: 30px;">
        <div id="inner-contact">
            <div class="span4 contact-page">
                <?php echo do_shortcode('[cForm]'); ?>
            </div>
        </div>
        <div class="address-div-outer">
            <div class="address-div-left">
                <div class="address-div-left-content">
                    <?php if ($data['address1']) { ?>
                        <p><?php echo $data['address1'] ?></p>
                    <?php } if ($data['phone1']) { ?>
                        <p><span>Phone :</span> <?php echo $data['phone1'] ?></p>
                    <?php } if ($data['phone2']) { ?>
                        <p><span>Phone :</span> <?php echo $data['phone2'] ?></p>
                    <?php } if ($data['fax_number']) { ?>
                        <p><span>Fax :</span> <?php echo $data['fax_number'] ?></p>
                    <?php } ?>
                </div>
            </div>
            <div class="address-div-right">
                <div id="map_canvas" style="width: 100%; height: 211px;" >

                </div>
                <script type="text/javascript">
                    function initialize() {
                        var address = "<?php echo $data['address1']; ?>";
                        var myLatlng = new google.maps.LatLng(<?php echo $data['latitude_address1']; ?>, <?php echo $data['longitude_address1']; ?>);
                        var myOptions = {
                            zoom: 18,
                            center: myLatlng,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        }
                        map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
                        var marker = new google.maps.Marker({
                            position: myLatlng,
                            map: map,
                            title: address
                        });
                        var contentwindow = '<b>' + address + '</b>'
                        infowindow = new google.maps.InfoWindow({
                            content: contentwindow
                        });
                        google.maps.event.addListener(marker, 'click', function() {
                            infowindow.open(map, marker);
                        });
                    }
                    google.maps.event.addDomListener(window, 'load', initialize);

                </script>
            </div>
        </div>
        <?php if ($data['address2']) { ?>
            <div class="address-div-outer">
                <div class="address-div-left">
                    <div class="address-div-left-content">
                        <p><?php echo $data['address2'] ?></p>
                    </div>
                </div>
                <div class="address-div-right">
                    <div id="map_canvas2" style="width: 100%; height: 211px;" >

                    </div>
                    <script type="text/javascript">
                        var geocoder;
                        var map;
                        var address = "<?php echo $data['address2']; ?>";
                        geocoder = new google.maps.Geocoder();
                        var latlng = new google.maps.LatLng(<?php echo $data['latitude_address2']; ?>, <?php echo $data['longitude_address2']; ?>);
                        var myOptions = {
                            zoom: 8,
                            center: latlng,
                            mapTypeControl: true,
                            mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
                            navigationControl: true,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        };
                        map = new google.maps.Map(document.getElementById("map_canvas2"), myOptions);
                        if (geocoder) {
                            geocoder.geocode({'address': address}, function(results, status) {
                                if (status == google.maps.GeocoderStatus.OK) {
                                    if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
                                        map.setCenter(results[0].geometry.location);

                                        var infowindow = new google.maps.InfoWindow(
                                                {content: '<b>' + address + '</b>',
                                                    size: new google.maps.Size(150, 50)
                                                });
                                        var marker = new google.maps.Marker({
                                            position: results[0].geometry.location,
                                            map: map,
                                            //title: address
                                        });
                                        google.maps.event.addListener(marker, 'click', function() {
                                            infowindow.open(map, marker);
                                        });

                                    } else {
                                        alert("No results found");
                                    }
                                } else {
                                    alert("Geocode was not successful for the following reason: " + status);
                                }
                            });
                        }
                    </script>
                </div>
            </div>
        <?php } ?>
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <?php the_content(); ?>
            </article><!-- #post -->
        <?php endwhile; ?>
    </div>
    <?php /* The loop */ ?>

</div><!-- .inner-contents -->
</div><!-- .container -->
<?php get_footer(); ?>
