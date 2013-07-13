<?php
/*
Plugin Name: Mi Tienda MercadoLibre
Plugin URI: http://tematiza.com.ar/plugins/mi-tienda-mercadolibre
Description: Add your products catalog from MercadoLibre to your WordPress powered website without effort.
Author: Hernan Matias Gauna
Version: 0.1
Author URI: http://twitter.com/hmgauna
License: GPL2    

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2, 
    as published by the Free Software Foundation. 
    
    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    The license for this software can likely be found here: 
    http://www.gnu.org/licenses/gpl-2.0.html
    
*/

//Load text translation domains for plugin localization
add_action('init', 'mtmlplugin_init');

function mtmlplugin_init() {
	load_plugin_textdomain('mtml-domain','',plugin_basename( dirname( __FILE__ ) .'/languages' ));
}

//Main plugin function. Creates shortcut, gets data and build output
function do_tienda_ml( $atts ){

	//Standard attribute get from shortcode
	extract( shortcode_atts( array(
		'username' => '',
		'productos_por_pagina' => 6
		), $atts ));
	//Fix bug in attributes extraction, set default attribute
	$query = strtolower(stripslashes(strip_tags($atts['username']))); if ($query == null) {$query = '';}
	//Limit default fix. Limit is used for the api call to set number of items retrieved, and the items displayed per page
	$limit = $atts['productos_por_pagina']; if ($limit == null) {$limit = 6;}
	
	//Want to know if we are displaying paged content.
	global $wp_query; // We are going to need $wp_query
	if( is_paged() ) {
		//It is indeed paged, want to know which page
		$page = $wp_query->query_vars['paged'];
		$page = stripslashes($page);
		$offset = ($page-1)*$limit;
	} else {
		//Not paged, set everything to null
		$offset = null; $page = null;
	}
	
	//We got enough information to build the api call url
	$api_call = 'https://api.mercadolibre.com/sites/MLA/search?nickname='.$query.'&limit='.$limit;
		//Concatenate offset if displaying paged content
		if ( $offset ) { $api_call = $api_call.'&offset='.$offset; }
		
	//Open the API url that returns the results with a cURL call
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $api_call);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$returned = curl_exec($ch);
	curl_close($ch);
	//Decode returned data into something useful
	$data = json_decode($returned, true); 
	
	//Begin builiding the output (remember you can not 'echo' within shortcode functions like this one)
	$output = '<ul id="mi-tienda-ml" class="">';
	
	//Set an either/or class just in case you want to style it later with CSS
	$either_or = 'either';
	
	//Iterate through the results and append to the output
	foreach ($data['results'] as $item) {
		if ( $either_or == 'either' ) { $either_or = 'or'; } else { $either_or = 'either' ;};
		$output.=  '<li class="mtml-single '.$either_or.'">';
		$output.=  '<a rel="nofollow" href="http://pmstrk.mercadolibre.com.ar/jm/PmsTrk?tool=6107109&go='.$item['permalink'].'" title="'.$item['title'].'" rel="nofollow" class="img-shadow"><img src="'.str_replace('s_MLA_v_I_', 's_MLA_v_O_', $item['thumbnail']).'" alt="'.$item['title'].': '.$item['subtitle'].'"/></a>';
		$output.=  '<h3>'.$item['title'].'<span class="mtml-precio">$'.$item['price'].'</span></h3>';
		if ($item['subtitle']) { $output.=  '<p>'.$item['subtitle'].'</p>'; };
		$output.=  '<a href="http://pmstrk.mercadolibre.com.ar/jm/PmsTrk?tool=6107109&go='.$item['permalink'].'" class="boton">'.__('Ver detalles','mtml-domain').'</a>';
		$output.=  '</li>';
	}
	$output.= '</ul>';
	
	//Build the pagination if needed
	$total_results = $data['paging']['total'];
	if ( $total_results > $limit ) { //If there are more results than our limit, then pagination is needed
		
		$pages = ceil($total_results/$limit); //Calculate how many pages we need
		
		// We are going to modify the $wp_query to make it think this is something paged, and use the default nav functions and plugins. (Alterar el $wp_query para que actúe con el paginado nativo)
		$wp_query->query_vars['paged'] = $page; //Set paged
		$wp_query->max_num_pages = $pages; //Set pages
	}
	
	return $output;

}

//Create shortcode that returns do_tienda_ml() function
add_shortcode( 'tiendaml', 'do_tienda_ml' );

//Include related styles.
function mtml_styles() {
	wp_enqueue_style( 'fancybox', plugin_dir_url(__FILE__) . 'style.css' ); }

add_action( 'wp_enqueue_scripts', 'mtml_styles' );
?>