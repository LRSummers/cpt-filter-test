jQuery(function($){
 
	/*
	 * Load More
	 */
	$('#fp_filter_loadmore').click(function(){
 
		$.ajax({
			url : fp_filter_loadmore_params.ajaxurl, // AJAX handler
			data : {
				'action': 'loadmore', // the parameter for admin-ajax.php
				'query': fp_filter_loadmore_params.posts, // loop parameters passed by wp_localize_script()
				'page' : fp_filter_loadmore_params.current_page // current page
			},
			type : 'POST',
			beforeSend : function ( xhr ) {
				$('#fp_filter_loadmore').text('Loading...'); // some type of preloader
			},
			success : function( data ){
				if( data ) {
 
					$('#fp_filter_loadmore').text( 'More posts' );
					$('#fp_filter_posts_wrap').append(data); // insert new posts
					fp_filter_loadmore_params.current_page++;
 
					if ( fp_filter_loadmore_params.current_page == fp_filter_loadmore_params.max_page ) 
						$('#fp_filter_loadmore').hide(); // if last page, HIDE the button
 
				} else {
					$('#fp_filter_loadmore').hide(); // if no data, HIDE the button as well
				}
			}
		});
		return false;
	});
 
	/*
	 * Filter
	 */
	$('#fp_filter_filters').submit(function(){
 
		$.ajax({
			url : fp_filter_loadmore_params.ajaxurl,
			data : $('#fp_filter_filters').serialize(), // form data
			dataType : 'json', // this data type allows us to receive objects from the server
			type : 'POST',
			beforeSend : function(xhr){
				$('#fp_filter_filters').find('button').text('...');
			},
			success:function(data){
 
				// when filter applied:
				// set the current page to 1
				fp_filter_loadmore_params.current_page = 1;
 
				// set the new query parameters
				fp_filter_loadmore_params.posts = data.posts;
 
				// set the new max page parameter
				fp_filter_loadmore_params.max_page = data.max_page;
 
				// change the button label back
				$('#fp_filter_filters').find('button').text('Suchen');
 
				// insert the posts to the container
				$('#fp_filter_posts_wrap').html(data.content);
 
				// hide load more button, if there are not enough posts for the second page
				if ( data.max_page < 2 ) {
					$('#fp_filter_loadmore').hide();
				} else {
					$('#fp_filter_loadmore').show();
				}
			}
		});
 
		// do not submit the form
		return false;
 
	});
 
});