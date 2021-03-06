<?php
/**
 * Name: open layers plugin
 * Description: Sample Friendica plugin/addon. Set a marker on a map correlated with a textfield.
 * Version: 0.1
 * Author: thomas bierey ... based on Mike`s openlayers-addon
 * 
 * 
 * 
 *
 * Addons are registered with the system through the admin
 * panel.
 *
 * When registration is detected, the system calls the plugin
 * name_install() function, located in 'addon/name/name.php',
 * where 'name' is the name of the addon.
 * If the addon is removed from the configuration list, the 
 * system will call the name_uninstall() function.
 *
 */


function openlayers_install() {

	/**
	 * 
	 * Our demo plugin will attach in three places.
	 * The first is just prior to storing a local post.
	 *
	 */
	register_hook('app_menu', 'addon/openlayers/openlayers.php', 'openlayers_app_menu');
	register_hook('post_local', 'addon/openlayers/openlayers.php', 'openlayers_post_local');
	register_hook('post_remote', 'addon/openlayers/openlayers.php', 'openlayers_post_remote');

	/**
	 *
	 * Then we'll attach into the plugin settings page, and also the 
	 * settings post hook so that we can create and update
	 * user preferences.
	 *
	 */

	register_hook('plugin_settings', 'addon/openlayers/openlayers.php', 'openlayers_settings');
	register_hook('plugin_settings_post', 'addon/openlayers/openlayers.php', 'openlayers_settings_post');

	logger("installed openlayers");
}


function openlayers_uninstall() {

	/**
	 *
	 * uninstall unregisters any hooks created with register_hook
	 * during install. It may also delete configuration settings
	 * and any other cleanup.
	 *
	 */
	unregister_hook('app_menu', 'addon/openlayers/openlayers.php', 'openlayers_app_menu');
	unregister_hook('post_local',    'addon/openlayers/openlayers.php', 'openlayers_post_local');
	unregister_hook('post_remote', 'addon/openlayers/openlayers.php', 'openlayers_post_remote');
	unregister_hook('plugin_settings', 'addon/openlayers/openlayers.php', 'openlayers_settings');
	unregister_hook('plugin_settings_post', 'addon/openlayers/openlayers.php', 'openlayers_settings_post');


	logger("removed openlayers");
}


function openlayers_app_menu($a,&$b) {
$b['app_menu'][] = '<div class="app-title"><a href="openlayers">' . t('Openlayers') . '</a></div>';
}



function openlayers_post_local($a, &$item) {

	/**
	 *
	 * An item was posted on the local system.
	 * We are going to look for specific items:
	 *      - A status post by a profile owner
	 *      - The profile owner must have allowed our plugin
	 *
	 */

	logger('openlayers invoked');

	if(! local_user())   /* non-zero if this is a logged in user of this system */
		return;

	if(local_user() != $item['uid'])    /* Does this person own the post? */
		return;

	if($item['parent'])   /* If the item has a parent, this is a comment or something else, not a status post. */
		return;

	/* Retrieve our personal config setting */

	$active = get_pconfig(local_user(), 'openlayers', 'enable');

	if(! $active)
		return;

	/**
	 *
	 * OK, we're allowed to do our stuff.
	 * Here's what we are going to do:
	 * load the list of timezone names, and use that to generate a list of world cities.
	 * Then we'll pick one of those at random and put it in the "location" field for the post.
	 *
	 */

	$item['location'] = "AAA";

	return;
}

function openlayers_post_remote($a, &$item) {

	/**
	 *
	 * An item was posted on the local system.
	 * We are going to look for specific items:
	 *      - A status post by a profile owner
	 *      - The profile owner must have allowed our plugin
	 *
	 */

	logger('openlayers invoked');

	if(! local_user())   /* non-zero if this is a logged in user of this system */
		return;

	if(local_user() != $item['uid'])    /* Does this person own the post? */
		return;

	if($item['parent'])   /* If the item has a parent, this is a comment or something else, not a status post. */
		return;

	/* Retrieve our personal config setting */

	$active = get_pconfig(local_user(), 'openlayers', 'enable');

	if(! $active)
		return;

	/**
	 *
	 * OK, we're allowed to do our stuff.
	 * Here's what we are going to do:
	 * load the list of timezone names, and use that to generate a list of world cities.
	 * Then we'll pick one of those at random and put it in the "location" field for the post.
	 *
	 */

	
	$item['location'] = "AAA";

	return;
}



/**
 *
 * Callback from the settings post function.
 * $post contains the $_POST array.
 * We will make sure we've got a valid user account
 * and if so set our configuration setting for this person.
 *
 */

function openlayers_settings_post($a,$post) {
	if(! local_user())
		return;
	if($_POST['openlayers-submit'])
		set_pconfig(local_user(),'openlayers','enable',intval($_POST['openlayers']));
}


/**
 *
 * Called from the Plugin Setting form. 
 * Add our own settings info to the page.
 *
 */



function openlayers_settings(&$a,&$s) {

	if(! local_user())
		return;

	/* Add our stylesheet to the page so we can make our settings look nice */

	$a->page['htmlhead'] .= '<link rel="stylesheet"  type="text/css" href="' . $a->get_baseurl() . '/addon/openlayers/openlayers.css' . '" media="all" />' . "\r\n";

	/* Get the current state of our config variable */

	$enabled = get_pconfig(local_user(),'openlayers','enable');

	$checked = (($enabled) ? ' checked="checked" ' : '');

	/* Add some HTML to the existing form */

	$s .= '<div class="settings-block">';
	$s .= '<h3>' . t('openlayers Settings') . '</h3>';
	$s .= '<div id="openlayers-enable-wrapper">';
	$s .= '<label id="openlayers-enable-label" for="openlayers-checkbox">' . t('Publish Data to Openlayers Plugin? ') . '</label>';
	$s .= '<input id="openlayers-checkbox" type="checkbox" name="openlayers" value="1" ' . $checked . '/>';
	$s .= '</div><div class="clear"></div>';

	/* provide a submit button */

	$s .= '<div class="settings-submit-wrapper" ><input type="submit" name="openlayers-submit" class="settings-submit" value="' . t('Submit') . '" /></div></div>';

}

function openlayers_module() {
return;
}

function openlayers_content(&$a) {
	if(! local_user())
		return;

	$baseurl = $a->get_baseurl() . '/addon/openlayers';
	$o = '';
	
	$openlayersJS = $baseurl."/OpenLayers.js";
	$a->page['htmlhead'] .= sprintf('<script type="text/javascript" src="%s" ></script>', $openlayersJS);
	
	
	$OLBounds = get_pconfig(local_user(), "openlayers", "OLBounds");
	$OLNewpost = get_pconfig(local_user(), "openlayers", "OLNewpost");
	$OLNewpost_coord_x = get_pconfig(local_user(), "openlayers", "OLNewpost_coord_x");
	$OLNewpost_coord_y = get_pconfig(local_user(), "openlayers", "OLNewpost_coord_y");
	
		if ($OLBounds==false) $OLBounds="1249162.0791318,6788625.7476497,1340886.5130612,6865062.7759243";
		if ($OLNewpost==false) $OLNewpost="Hello friendicans ;)";	
		if ($OLNewpost_coord_x==false) $OLNewpost_coord_x="1293342.6814745";	
		if ($OLNewpost_coord_y==false) $OLNewpost_coord_y="6819659.1811292";	
			
	$a->page['htmlhead'] .= '
	<script>
      
	$(document).ready(function(){
				var iconSize = new OpenLayers.Size(21, 25);
            var iconOffset = new OpenLayers.Pixel(-(iconSize.w / 2), -iconSize.h);
            var icon = new OpenLayers.Icon("http://www.openstreetmap.org/openlayers/img/marker.png",
                           iconSize, iconOffset);
            var zoom, center, currentPopup, map, lyrMarkers;
            var popupClass = OpenLayers.Class(OpenLayers.Popup.FramedCloud, {
                "autoSize": true,
                "minSize": new OpenLayers.Size(100, 50),
                "maxSize": new OpenLayers.Size(500, 300),
                "keepInMap": true,
            });
            var bounds = new OpenLayers.Bounds();
            function addMarker(lng, lat, info) {
                var pt = new OpenLayers.LonLat('.$OLNewpost_coord_x.','.$OLNewpost_coord_y.');
                bounds.extend(pt);
                var feature = new OpenLayers.Feature(lyrMarkers, pt);
                feature.closeBox = true;
                feature.popupClass = popupClass;
                feature.data.popupContentHTML = "'.$OLNewpost.'";
                feature.data.overflow = "auto";
                var marker = new OpenLayers.Marker(pt, icon.clone());
                var markerClick = function(evt) {
                    if (currentPopup != null && currentPopup.visible()) {
                        currentPopup.hide();
                    }
                    if (this.popup == null) {
                        this.popup = this.createPopup(this.closeBox);
                        map.addPopup(this.popup);
                        this.popup.show();
                    } else {
                        this.popup.toggle();
                    }
                    currentPopup = this.popup;
                    OpenLayers.Event.stop(evt);
                };
                marker.events.register("mousedown", feature, markerClick);
                lyrMarkers.addMarker(marker);
                map.events.register("mousemove", map, function(e) {
               
                $("#openlayers_OLBounds", document.element).val(map.getExtent());
                
            		});
            	map.events.register("click", map , function(e){
   					var opx = map.getLonLatFromPixel(e.xy) ;
   					var marker = new OpenLayers.Marker(opx, icon.clone());
   					lyrMarkers.addMarker(marker);
   					$("#openlayers_OLNewpost_coord_x", document.element).val(marker.lonlat.lon);
   					$("#openlayers_OLNewpost_coord_y", document.element).val(marker.lonlat.lat);
   					popup = new OpenLayers.Popup.FramedCloud("chicken",
                         marker.lonlat,
                         new OpenLayers.Size(200, 200),
                         $("#openlayers_OLNewpost", document.element).val(),
                         null, true);
                   map.addPopup(popup); 
						});
            }

	
	var options = {
                    projection: new OpenLayers.Projection("EPSG:900913"),
                    displayProjection: new OpenLayers.Projection("EPSG:4326"),
                    units: "m",
                    numZoomLevels: 19,
                    maxResolution: 156543.0339,
                    maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34, 20037508.34, 20037508.34)
                };
 
					 map = new OpenLayers.Map("map", options);
                map.addControl(new OpenLayers.Control.DragPan());
                var lyrOsm = new OpenLayers.Layer.OSM();
                map.addLayer(lyrOsm);
                lyrMarkers = new OpenLayers.Layer.Markers("Markers");
                map.addLayer(lyrMarkers);
					 lyrMarkers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat('.$OLNewpost_coord_x.','.$OLNewpost_coord_y.'),icon.clone()));
                 //add marker on given coordinates
                addMarker(121.06573, 14.65194, "<b>University of the Philippines</b><br/>Philippines");
                addMarker(121.04931, 14.65105, "<b>Quezon Memorial Circle</b><br/>Philippines");
                view_init = new OpenLayers.Bounds('.$OLBounds.');
                center = view_init.getCenterLonLat();
                map.setCenter(center, map.getZoomForExtent(view_init));
                zoom = map.getZoom();
                
    	})
  </script>';
    



	
  $sub= "(III) Save current post/view";
  if (isset($_POST['openlayers-settings-sub']) && $_POST['openlayers-settings-sub']!=''){	
		set_pconfig(local_user(), 'openlayers', 'OLBounds', $_POST['openlayers_OLBounds']);
		set_pconfig(local_user(), 'openlayers', 'OLNewpost', $_POST['openlayers_OLNewpost']);		
		set_pconfig(local_user(), 'openlayers', 'OLNewpost_coord_x', $_POST['openlayers_OLNewpost_coord_x']);	
		set_pconfig(local_user(), 'openlayers', 'OLNewpost_coord_y', $_POST['openlayers_OLNewpost_coord_y']);	
		header("Location: openlayers");
		}
  $o .= <<< EOT
<h2>Openlayers</h2>
<form id="openlayersform" action="openlayers" method="post" >
<div class='field input'>
		<label for='openlayers_OLNewpost'>(I)write new post:</label>
		<input name='openlayers_OLNewpost' id='openlayers_OLNewpost' value="">
</div>
<div class='field input'>
(II) set marker on map!
</div>
<div style="display:none;" class='field input'>
		<label for='openlayers_OLNewpost_coord_x'>new post coordinates x:</label>
		<input name='openlayers_OLNewpost_coord_x' id='openlayers_OLNewpost_coord_x' value="">
</div>
<div style="display:none;" class='field input'>
		<label for='openlayers_OLNewpost_coord_y'>new post coordinates y:</label>
		<input name='openlayers_OLNewpost_coord_y' id='openlayers_OLNewpost_coord_y' value="">
</div>
<div style="display:none;" class='field input'>
		<label for='openlayers_OLBounds'>current bounds</label>
		<input name='openlayers_OLBounds' id='openlayers_OLBounds' value="$OLBounds">
</div>
<div class="openlayers-submit-wrapper">
<input id="OLsub" type="submit" value="$sub" class="settings-submit" name="openlayers-settings-sub"></input>
</div>
</form>
<div id="map" style="width: 600px; height: 500px;">
</div>
EOT;

return $o;
    
}