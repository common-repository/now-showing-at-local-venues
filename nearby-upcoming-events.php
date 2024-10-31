<?php
/*
Plugin Name: Now Showing at Local Venues - Nearby Upcoming Events
Plugin URI: http://lakeaustinrealty360.com/now-showing-at-local-venues
Description: Now Showing at Local Venues displays a slideshow of upcoming events w/ photos & details for any location. Just type in your location and show events based on keyword, radius, date, category, popularity and/or relevancy.
Version: 1.0.2
Author: Josh Davis
Author URI: http://josh.dvvvvvvvv.com/
*/

/*  Copyright 2012  Josh Davis

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function nue_scripts() {
	wp_enqueue_script('jquery');
    //wp_register_script('nue-script', plugins_url('nearby-upcoming-events.js', __FILE__));
    //wp_enqueue_script('nue-script', array('jquery'));
    wp_register_style('nue-style', plugins_url('style.css', __FILE__));
    wp_enqueue_style('nue-style');
}
add_action('wp_enqueue_scripts', 'nue_scripts');

function nue_settings() {
	$setting_vars = array(
		'nue_keywords',
		'nue_location',
		'nue_date',
		'nue_category',
		'nue_within',
		'nue_sort_order',
		'nue_page_size',
		'nue_visible',
		'nue_without_image',
		'nue_api_key',
		'nue_delay',
		'nue_city_zip',
		);
	foreach ( $setting_vars as $setting_var ){
		register_setting( 'nue_set', $setting_var );
		$cur_value = get_option( $setting_var );
		if ( $cur_value === false) {
			if ($setting_var == 'nue_visible'){
				update_option( $setting_var, '1' );
			}
			if ($setting_var == 'nue_without_image'){
				update_option( $setting_var, 'show' );
			}
			if ($setting_var == 'nue_delay'){
				update_option( $setting_var, '5' );
			}
			if ($setting_var == 'nue_city_zip'){
				update_option( $setting_var, 'exclude' );
			}
		}
	}
}
add_action('admin_init', 'nue_settings');

function nue_menu() {
	add_options_page( 'Now Showing at Local Venues Settings', 'Now Showing at Local Venues', 'manage_options', 'nue_uid', 'displetpop_options' );
}

function displetpop_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap"><h2>Now Showing at Local Venues Settings</h2><form method="post" action="options.php">';
	settings_fields('nue_set');
?>

<style>
.wrap{font-size: 13px; line-height: 17px;font-family: Arial, sans-serif; color: #000; padding-top: 10px;}
.wrap fieldset{margin:10px 0px; padding:15px; padding-top: 0px; border: 1px solid #ccc;}
.wrap fieldset legend{font-size: 13px; font-weight: bold; margin-left: -5px;}
.wrap fieldset span { font-size:11px; font-style:italic; color: #666;}
.wrap fieldset .red{color: #900;}
.wrap fieldset .entry{margin-top:10px; margin-bottom: 5px;}
.wrap fieldset .fieldleft{display: inline-block; width: 140px; text-align: right; vertical-align:top; margin: 3px 8px 0px 0px;}
.wrap fieldset .marleft{margin-left: 153px; margin-top: -5px;}
.wrap fieldset input{margin-bottom: 4px;}
.wrap fieldset textarea{width: 300px; height: 80px;}
</style>

<fieldset>
	<legend>Default Settings: <span>Leave any field blank to ignore that parameter in search. Omit quotes from examples.</span></legend>
	<div class="entry"><div class="fieldleft">Keywords</div><input name="nue_keywords" type="text" id="nue_keywords" size="30" value="<?php echo get_option('nue_keywords'); ?>"/><div class="marleft"><span>Ex. "books" or "jazz" - Overwrite this default using the shortcode, e.g. [nearby-events keyword="art"]</span></div></div>
	<div class="entry"><div class="fieldleft">Location</div><input name="nue_location" type="text" id="nue_location" size="30" value="<?php echo stripslashes(get_option('nue_location')); ?>"/><div class="marleft"><span>Ex. "Austin" or "Austin TX" or "78701" - Overwrite this default using the shortcode, e.g. [nearby-events location="San Diego"]</span></div></div>
	<div class="entry"><div class="fieldleft">Radius In Miles</div><input name="nue_within" type="text" id="nue_within" size="10" value="<?php echo get_option('nue_within'); ?>"/><div class="marleft"><span>Distance from the location to search within, in miles. Ex. "5" or "25" - Overwrite this default using the shortcode, e.g. [nearby-events within="15"]</span></div></div>
	<div class="entry"><div class="fieldleft">Date</div><input name="nue_date" type="text" id="nue_date" size="30" value="<?php echo get_option('nue_date'); ?>"/><div class="marleft"><span>Ex. "Future", "Past", "Today", "Last Week", "This Week", "Next week", and months by name, e.g. "October". Exact ranges can be specified the form "YYYYMMDD00-YYYYMMDD00", for example "2012042500-2012042700"; the last two digits of each date in this format are ignored. Overwrite this default using the shortcode, e.g. [nearby-events date="Future"]</span></div></div>
	<div class="entry"><div class="fieldleft">Category</div><input name="nue_category" type="text" id="nue_category" size="90" value="<?php echo get_option('nue_category'); ?>"/><div class="marleft"><span>Ex. "music", "conference", "learning_education", "family_fun_kids", "festivals_parades", "movies_film", "food", "fundraisers", "art", "support", "books", "attractions", "community", "business", "singles_social", "schools_alumni", "clubs_associations", "outdoors_recreation", "performing_arts", "animals", "politics_activism", "sales", "science", "religion_spirituality", "sports", "technology", and/or "other" only - Separate multiple entries with commas, and overwrite this default using the shortcode, e.g. [nearby-events category="music,food,movies_film,family_fun_kids,festivals_parades,art"]</span></div></div>
	<div class="entry"><div class="fieldleft">Sort By</div><input name="nue_sort_order" type="text" id="nue_sort_order" size="10" value="<?php echo get_option('nue_sort_order'); ?>"/><div class="marleft"><span>Ex. "popularity", "date", or "relevance" only - Overwrite this default using the shortcode, e.g. [nearby-events sort="date"]</span></div></div>
	<div class="entry"><div class="fieldleft">Number Of Results</div><input name="nue_page_size" type="text" id="nue_page_size" size="10" value="<?php echo get_option('nue_page_size'); ?>"/><div class="marleft"><span>Ex. "5" or "15"  - Overwrite this default using the shortcode, e.g. [nearby-events results="25"]</span></div></div>
</fieldset>
<fieldset>
	<legend>Slideshow Settings: <span>When the slideshow is in use, use these default settings unless otherwise specified in shortcode. Omit quotes from examples.</span></legend>
	<div class="entry"><div class="fieldleft">Visible</div><input name="nue_visible" type="text" id="nue_visible" size="10" value="<?php echo get_option('nue_visible'); ?>"/><div class="marleft"><span>How many slides are visible at a time. Should be limited based on available horizontal space. Ex. "1" or "5" - Overwrite this default using the shortcode, e.g. [nearby-events visible="3"]</span></div></div>
	<div class="entry"><div class="fieldleft">Results Without Images</div><input name="nue_without_image" type="text" id="nue_without_image" size="10" value="<?php echo get_option('nue_without_image'); ?>"/><div class="marleft"><span>Whether or not to show results that don't have an image. Ex. "show" or "hide" only - Overwrite this default using the shortcode, e.g. [nearby-events without_image="hide"]</span></div></div>
	<div class="entry"><div class="fieldleft">Delay Between Slides</div><input name="nue_delay" type="text" id="nue_delay" size="10" value="<?php echo get_option('nue_delay'); ?>"/><div class="marleft"><span>Time in seconds between each transition. Ex. "5" - Overwrite this default using the shortcode, e.g. [nearby-events delay="4"]</span></div></div>
	<div class="entry"><div class="fieldleft">City / Zip</div><input name="nue_city_zip" type="text" id="nue_city_zip" size="10" value="<?php echo get_option('nue_city_zip'); ?>"/><div class="marleft"><span>Whether to include the City, Zip below the name of the venue. Ex. "include" or "exclude" only - Overwrite this default using the shortcode, e.g. [nearby-events city_zip="exclude"]</span></div></div>
</fieldset>
<fieldset>
	<legend>API & TOS Settings: <span class="red">This section must be completed for Now Showing at Local Venues to function.</span></legend>
		<div class="entry">Signup for a key at <a href="http://api.eventful.com/keys" target="_blank">Eventful API Application Keys</a>. There is no charge and the key is frequently generated immediately upon registration.</div>
	<div class="entry"><div class="fieldleft">API Key</div><input name="nue_api_key" type="text" id="nue_api_key" size="30" value="<?php echo get_option('nue_api_key'); ?>"/> By entering a key, I agree to the following terms:<div class="marleft"><BR><span>This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.<BR><BR>This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.<BR><BR>You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.<BR><BR>In addition to the GNU General Public License v2 documented above, in return for using this plugin a source citation will be displayed below each set of results to Eventful, using the provided image without covering, removing, or otherwise obscuring it or rendering it ineffective.</span></div></div>
</fieldset>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

<?php
	echo '</form></div>';
}
add_action('admin_menu', 'nue_menu');

function nue_markup($nue_visible, $nue_keywords, $nue_location, $nue_date, $nue_category, $nue_within, $nue_sort_order, $nue_page_size, $nue_without_image, $nue_delay, $nue_city_zip) {
	
	// Make a UID for each slideshow
	$nue_str = str_replace(' ', '', $nue_visible . $nue_keywords . $nue_location . $nue_date . $nue_category . $nue_within . $nue_sort_order . $nue_page_size . $nue_without_image);
	$nue_uid = str_replace(',', '', $nue_str);

	// Query Eventful API
	$nue_xml_url='http://api.eventful.com/rest/events/search';
	$nue_query_args = array(
		'keywords' => $nue_keywords,
		'location' => $nue_location,
		'date' => $nue_date,
		'category' => $nue_category,
		'within' => $nue_within,
		'sort_order' => $nue_sort_order,
		'page_size' => $nue_page_size
		);
	$nue_api_key = get_option('nue_api_key');
	$arg_string = '?&app_key=' . $nue_api_key . '&';
	foreach ($nue_query_args as $arg => $val) {
			if (!empty($val)) {
				$arg_string .= $arg.'='.$val.'&';
			}
	}
	$arg_string = preg_replace('/\s+/', '+', $arg_string);
	$nue_xml_url .= rtrim($arg_string, '&');
	$returnstring = '<!-- XML: ' . $nue_xml_url . ' -->';
	$doc = new DOMDocument();
	$xml = get_transient($nue_xml_url);
	if (empty($xml)) {
		// create a new cURL resource
		$ch = curl_init();
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $nue_xml_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		// grab URL and pass it to the browser
		$xml = curl_exec($ch);
		// close cURL resource, and free up system resources
		curl_close($ch);
		set_transient($nue_xml_url, $xml, 43200); // 12 hour cache
	}
	$doc->loadXML($xml);	
	$events = $doc->getElementsByTagName('search')->item(0)->getElementsByTagName('events')->item(0)->getElementsByTagName('event');
	
	// JavaScript
	$returnstring .= "
	<script>
	// Start allowance of jQuery to $ shortcut
	jQuery(document).ready(function($){

		// Carousel
		$('#nue_events.slideshow" . $nue_uid . " .inner').jCarouselLite" . $nue_uid . "({
		  btnNext: '#nue_events.slideshow" . $nue_uid . " a.next',
		  btnPrev: '#nue_events.slideshow" . $nue_uid . " a.previous',
		  visible: " . $nue_visible . ",
		  auto: " . $nue_delay*1000 . ",
		});

	// Ends allowance of jQuery to $ shortcut
	});
	</script>
	<script>
	(function($) {
	    $.fn.jCarouselLite" . $nue_uid . " = function(o) {
	        o = $.extend({
	            btnPrev: null,
	            btnNext: null,
	            btnGo: null,
	            mouseWheel: false,
	            auto: null,
	            speed: 200,
	            easing: null,
	            vertical: false,
	            circular: true,
	            visible: 3,
	            start: 0,
	            scroll: 1,
	            beforeStart: null,
	            afterEnd: null
	        },
	        o || {});
	        return this.each(function() {
	            var running = false,
	            animCss = o.vertical ? 'top': 'left',
	            sizeCss = o.vertical ? 'height': 'width';
	            var div = $(this),
	            divouter = $(this).parent('.slideshow'),
	            a = $('a', div),
	            ul = $('ul', div),
	            tLi = $('li', ul),
	            tl = tLi.size(),
	            v = o.visible;
	            if (o.circular) {
	                ul.prepend(tLi.slice(tl - v - 1 + 1).clone()).append(tLi.slice(0, v).clone());
	                o.start += v;
	            }
	            var li = $('li', ul),
	            itemLength = li.size(),
	            curr = o.start;
	            div.css('visibility', 'visible');
	            li.css({
	                overflow: 'hidden',
	                float: o.vertical ? 'none': 'left'
	            });
	            ul.css({
	                margin: '0',
	                padding: '0',
	                position: 'relative',
	                'list-style-type': 'none',
	                'z-index': '1'
	            });
	            div.css({
	                overflow: 'hidden',
	                position: 'relative',
	                'z-index': '2',
	                left: '0px'
	            });
	            var liSize = o.vertical ? height(li) : width(li);
	            var ulSize = liSize * itemLength;
	            var divSize = liSize * v;
	            li.css({
	                width: li.width(),
	                height: li.height()
	            });
	            ul.css(sizeCss, ulSize + 'px').css(animCss, -(curr * liSize));
	            div.css(sizeCss, divSize-5 + 'px');
	            divouter.css(sizeCss, divSize-5 + 'px');
	            if (o.btnPrev) $(o.btnPrev).click(function() {
	                resetAuto();
	                autoScroll = setInterval(function() {
	                    go(curr + o.scroll);
	                },
	                o.auto + o.speed);
	                return go(curr - o.scroll);
	            });
	            if (o.btnNext) $(o.btnNext).click(function() {
	                resetAuto();
	                autoScroll = setInterval(function() {
	                    go(curr + o.scroll);
	                },
	                o.auto + o.speed);
	                return go(curr + o.scroll);
	            });
	            if (o.btnGo) $.each(o.btnGo,
	            function(i, val) {
	                $(val).click(function() {
	                    return go(o.circular ? o.visible + i: i);
	                });
	            });
	            if (o.mouseWheel && div.mousewheel) div.mousewheel(function(e, d) {
	                return d > 0 ? go(curr - o.scroll) : go(curr + o.scroll);
	            });
	            if (o.auto) {
	                autoScroll = setInterval(function() {
	                    go(curr + o.scroll);
	                },
	                o.auto + o.speed);
	                function resetAuto() {
	                    clearInterval(autoScroll);
	                };
	                div.hover(function() {
	                    clearInterval(autoScroll);
	                },
	                function() {
	                    autoScroll = setInterval(function() {
	                        go(curr + o.scroll);
	                    },
	                    o.auto + o.speed);
	                });
	            }
	            function vis() {
	                return li.slice(curr).slice(0, v);
	            };
	            function go(to) {
	                if (!running) {
	                    if (o.beforeStart) o.beforeStart.call(this, vis());
	                    if (o.circular) {
	                        if (to <= o.start - v - 1) {
	                            ul.css(animCss, -((itemLength - (v * 2)) * liSize) + 'px');
	                            curr = to == o.start - v - 1 ? itemLength - (v * 2) - 1: itemLength - (v * 2) - o.scroll;
	                        } else if (to >= itemLength - v + 1) {
	                            ul.css(animCss, -((v) * liSize) + 'px');
	                            curr = to == itemLength - v + 1 ? v + 1: v + o.scroll;
	                        } else curr = to;
	                    } else {
	                        if (to < 0 || to > itemLength - v) return;
	                        else curr = to;
	                    }
	                    running = true;
	                    ul.animate(animCss == 'left' ? {
	                        left: -(curr * liSize)
	                    }: {
	                        top: -(curr * liSize)
	                    },
	                    o.speed, o.easing,
	                    function() {
	                        if (o.afterEnd) o.afterEnd.call(this, vis());
	                        running = false;
	                    });
	                    if (!o.circular) {
	                        $(o.btnPrev + ',' + o.btnNext).removeClass('disabled');
	                        $((curr - o.scroll < 0 && o.btnPrev) || (curr + o.scroll > itemLength - v && o.btnNext) || []).addClass('disabled');
	                    }
	                }
	                return false;
	            };
	        });
	    };
	    function css(el, prop) {
	        return parseInt($.css(el[0], prop)) || 0;
	    };
	    function width(el) {
	        return el[0].offsetWidth + css(el, 'marginLeft') + css(el, 'marginRight');
	    };
	    function height(el) {
	        return el[0].offsetHeight + css(el, 'marginTop') + css(el, 'marginBottom');
	    };
	})(jQuery);
	</script>";

	$returnstring .= file_get_contents(plugins_url('nearby-upcoming-events.js', __FILE__));
	
	// HTML Markup
	$returnstring .= '<div id="nue_events" class="slideshow slideshow' . $nue_uid . '"><a href="javascript:void(0);" class="previous" title="Previous"><div>Previous<div class="nue_hovertrans"></div></div></a><a href="javascript:void(0);" class="next" title="Next"><div>Next<div class="nue_hovertrans"></div></div></a><div class="inner"><ul class="events">';
	foreach ($events as $event) {
	   $title = $event->getElementsByTagName('title')->item(0)->nodeValue;
	   $url = $event->getElementsByTagName('url')->item(0)->nodeValue;
	   $start_time = $event->getElementsByTagName('start_time')->item(0)->nodeValue;
	   $start_time = strftime("%a %h %e - %l:%M %p", strtotime($start_time));
	   $venue_name = $event->getElementsByTagName('venue_name')->item(0)->nodeValue;
	   $city_name = $event->getElementsByTagName('city_name')->item(0)->nodeValue;
	   $region_abbr = $event->getElementsByTagName('region_abbr')->item(0)->nodeValue;
	   $postal_code = $event->getElementsByTagName('postal_code')->item(0)->nodeValue;
	   $image = $event->getElementsByTagName('image')->item(0)->getElementsByTagName('medium')->item(0)->nodeValue;
	   if (!$image && $nue_without_image == 'hide') {} else {
	   	$returnstring .= '<li class="event"><a href="' . $url . '" target="_blank"><div class="inside">';
	   	if ($image) {
	   		$returnstring .= '<div class="img"><img src="' . $image . '" /></div>';
	   	}
	   	$returnstring .= '<h4 class="title">' . $title . '</h4>';
	   	if ($start_time) {
	   		$returnstring .= '<div class="detail">' . $start_time . '</div>';
	   	}
	   	if ($venue_name) {
	   		$returnstring .= '<div class="detail">@ ' . $venue_name . '</div>';
	   	}
		if ($city_name && $nue_city_zip == "include") {
			$returnstring .= '<div class="detail">' . $city_name . ', ' . $region_abbr . ' ' . $postal_code . '</div>';
		}
	   	$returnstring .= '</div></a><div class="nue_hovertrans"></div></li>';
	   }
	}
	$returnstring .= '</ul></div><!--// .inner --><div class="eventful-badge eventful-small"><img src="http://api.eventful.com/images/powered/eventful_58x20.gif" alt="Local Events, Concerts, Tickets"><p><a href="http://eventful.com/">Events</a> by Eventful</p></div><div class="clear"><!-- --></div><div class="eventful-badge eventful-small lla"><p>Plugin by <a href="http://lakeaustinrealty360.com" target="_blank">LakeAustinRealty360.com</a></p></div><div class="clear"><!-- --></div></div>';
	return $returnstring;
	
}

function nue_shortcode( $atts ){
	extract(shortcode_atts(array(
		'keyword' => stripslashes(get_option('nue_keywords')),
		'location' => stripslashes(get_option('nue_location')),
		'date' => stripslashes(get_option('nue_date')),
		'category' => stripslashes(get_option('nue_category')),
		'within' => stripslashes(get_option('nue_within')),
		'sort' => stripslashes(get_option('nue_sort_order')),
		'results' => stripslashes(get_option('nue_page_size')),
		'visible' => stripslashes(get_option('nue_visible')),
		'without_image' => stripslashes(get_option('nue_without_image')),
		'delay' => stripslashes(get_option('nue_delay')),
		'city_zip' => stripslashes(get_option('nue_city_zip')),
	), $atts));
	if (!get_option('nue_api_key')){
		return '<p>Please visit the <a href="' . get_bloginfo('home') . '/wp-admin/options-general.php?page=nue_uid">Now Showing at Local Venues Settings Page</a> and enter an API Key.</p>';
	}
	else{
		return nue_markup($visible, $keyword, $location, $date, $category, $within, $sort, $results, $without_image, $delay, $city_zip);
	}
}
add_shortcode('nearby-events', 'nue_shortcode');
add_filter('widget_text', 'do_shortcode');

?>
