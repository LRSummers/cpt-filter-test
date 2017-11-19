<?php
add_action( 'wp_enqueue_scripts', 'fp_filter_script_and_styles', 1 );

function fp_filter_script_and_styles() {
	global $wp_query;

	// register our main script but do not enqueue it yet
	wp_register_script( 'fp_filter_scripts', plugin_dir_url( __FILE__ ) .  '/filter-script.js', array('jquery'), time() );

	// now the most interesting part
	// we have to pass parameters to myloadmore.js script but we can get the parameters values only in PHP
	// you can define variables directly in your HTML but I decided that the most proper way is wp_localize_script()
	wp_localize_script( 'fp_filter_scripts', 'fp_filter_loadmore_params', array(
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
		'posts' => json_encode( $wp_query->query_vars ), // everything about your loop is here
		'current_page' => $wp_query->query_vars['paged'] ? $wp_query->query_vars['paged'] : 1,
		'max_page' => $wp_query->max_num_pages
	) );

 	wp_enqueue_script( 'fp_filter_scripts' );
}


add_action('wp_ajax_loadmore', 'fp_filter_loadmore_ajax_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_loadmore', 'fp_filter_loadmore_ajax_handler'); // wp_ajax_nopriv_{action}

function fp_filter_loadmore_ajax_handler(){

	// prepare our arguments for the query
	$args = json_decode( stripslashes( $_POST['query'] ), true );
	$args['paged'] = $_POST['page'] + 1; // we need next page to be loaded
	$args['post_status'] = 'publish';
	$args['post_type'] = 'kampagne';

	$query = new WP_Query( $args );
 
	if( $query->have_posts() ) :

 		ob_start(); // start buffering because we do not need to print the posts now
		while( $query->have_posts() ): $query->the_post();
			kampagnen_marker();
		endwhile;
		rewind_posts();
	?>
	<table width="100%" border="0" cellpadding="0">
		<tbody>
			<tr>
			    <th>&nbsp;</th>
			    <th><?php _e('Systemname und Adresse', 'franchiseportal');?></th>
			    <th><?php _e('Branchen', 'franchiseportal');?></th>
			    <th colspan="2"><?php _e('Standortgründung<br> oder Nachfolgesuche', 'franchiseportal');?></th>
			 </tr>

			<?php while ( $query->have_posts()) : $query->the_post(); 
					kampagnen_liste_content();
				endwhile;
					ob_end_clean(); // clear the buffer
				endif;
			?>
		</tbody>
	</table>
	<?php
	wp_reset_query();

	die; // here we exit the script and even no wp_reset_query() required!
}



add_action('wp_ajax_fp_filterfilter', 'fp_filter_filter_function');
add_action('wp_ajax_nopriv_fp_filterfilter', 'fp_filter_filter_function');

function fp_filter_filter_function(){

	$args = array(
		'post_type' => 'kampagne',
		'posts_per_page' => $_POST['fp_filter_posts_per_page'], // -1 to show all posts
	);

	$_region = $_POST['regionfilter'] != '' ? $_POST['regionfilter'] : '';
    $_branche = $_POST['branchefilter'] != '' ? $_POST['branchefilter'] : '';
    $_ktyp = $_POST['kampagne-typ'] != '' ? $_POST['kampagne-typ'] : '';
 
	// for taxonomies / categories
	if( $_region != '' ) {
		$args = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'region',
					'terms' => $_region,
				),
			),
		);
	}

	if( $_branche != '' ) {
		$args['meta_query'] = array(
			array(
				'key'     => 'rel_branche',
				'value'   => '"'.$_branche.'"',
				'compare' => 'LIKE',
			)
		);
	}

	if( $_ktyp != '' ) {
		$args['meta_query'] = array(
			'relation'	=> 'OR',
			array(
				'key'	=> 'kampagne_typ',
				'value' => $_ktyp,
			)
		);
	}

	$query = new WP_Query( $args );
 
	if( $query->have_posts() ) :

 		ob_start(); // start buffering because we do not need to print the posts now
		while( $query->have_posts() ): $query->the_post();
			kampagnen_marker();
		endwhile;
		rewind_posts();
	?>
	<table width="100%" border="0" cellpadding="0">
		<tbody>
			<tr>
			    <th>&nbsp;</th>
			    <th><?php _e('Systemname und Adresse', 'franchiseportal');?></th>
			    <th><?php _e('Branchen', 'franchiseportal');?></th>
			    <th colspan="2"><?php _e('Standortgründung<br> oder Nachfolgesuche', 'franchiseportal');?></th>
			 </tr>

			<?php while ( $query->have_posts()) : $query->the_post(); 
					kampagnen_liste_content();
				endwhile;
		 		$content = ob_get_contents(); // we pass the posts to variable
					ob_end_clean(); // clear the buffer
				endif;
			?>
		</tbody>
	</table>
	<?php

	wp_reset_query();

 	echo json_encode( array(
		'posts' => serialize( $wp_query->query_vars ),
		'max_page' => $wp_query->max_num_pages,
		'found_posts' => $wp_query->found_posts,
		'content' => $content
	) );

	die();
}
