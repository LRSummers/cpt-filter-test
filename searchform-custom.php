<?php ?>
<div id="search">
	<div class="wrap">
		<div class="data-column-wrap">
			<form id="fp_filter_filters" action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" method="POST">
				<!-- Region Select field -->
				<div class="data-column data-column-4">
					<?php $regionargs = array(
						'show_option_all'    => '',
						'show_option_none'   => '',
						'option_none_value'  => '',
						'orderby'            => 'ID',
						'order'              => 'ASC',
						'show_count'         => 0,
						'hide_empty'         => 1,
						'child_of'           => 0,
						'exclude'            => '',
						'include'            => '',
						'echo'               => 1,
						'selected'           => isset( $_GET['region'] ) ? $_GET['region'] : '',
						'hierarchical'       => 1,
						'name'               => 'regionfilter',
						'id'                 => 'regionselect',
						'class'              => 'chosen-select',
						'depth'              => 0,
						'tab_index'          => 0,
						'taxonomy'           => 'region',
						'hide_if_empty'      => false,
						'value_field'	     => 'id',
						'multiple'           => true
					); ?>
					<?php wp_dropdown_categories( $regionargs ); ?>
				</div>		

				<!-- // Region Select field -->

				<!-- Branche Select field -->
				<div class="data-column data-column-4">

					<?php $brancheargs = array(
						'show_option_all'    => '',
						'show_option_none'   => '',
						'option_none_value'  => '',
						'orderby'            => 'ID',
						'order'              => 'ASC',
						'show_count'         => 0,
						'hide_empty'         => 1,
						'child_of'           => 0,
						'exclude'            => '',
						'include'            => '',
						'echo'               => 1,
						'selected'           => isset( $_GET['branche'] ) ? $_GET['branche'] : '',
						'hierarchical'       => 1,
						'name'               => 'branchefilter',
						'id'                 => 'brancheselect',
						'class'              => 'chosen-select',
						'depth'              => 0,
						'tab_index'          => 0,
						'taxonomy'           => 'branche',
						'hide_if_empty'      => false,
						'value_field'	     => 'id',
						'multiple'           => true
					); ?>
					<?php wp_dropdown_categories( $brancheargs ); ?>
				</div>	

				<!-- // Branche Select field -->

				<!-- Advanced Custom Field date fields -->
				<div class="data-column data-column-4">

					<?php // get custom field from post type
					$field = get_field_object('field_59bfd40499af7');
					$choices = $field['choices'];
					?>
					<select name="kampagne-typ" id="kampagne-typ" class="postform">
						<option value="" selected="selected"><?php _e('Typ', 'fptheme'); ?></option>
							<?php if( $choices ): ?>
								<?php foreach ($choices as $key => $value): ?>
									<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
									<?php endforeach; ?>
							<?php endif; ?>				
					</select>

					<input type="hidden" name="fp_filter_posts_per_page" id="fp_filter_posts_per_page" value="-1"><!-- or: echo get_option( 'posts_per_page' ) default value from Settings->Reading -->
				</div>
				<!-- // Advanced Custom Field date fields -->
				<div class="data-column data-column-4">
					<input type="hidden" name="action" value="fp_filter_filter" />
 					<button class="search"><span class="icon-lupe"></span></button>
				</div>
			</form>
		</div>
	</div>
	<div class="search-graphic"><img src="<?php echo plugins_url('includes/images/search-graphic.png', dirname(dirname( __FILE__ ) )); ?>" /></div>
</div> <!-- #search -->
<div class="dreieck-blue"></div>

<div class="wrap">
	<div id="mapwrap"></div>
	<div id="fp_filter_posts_wrap">
		<?php kampagnen_liste();?> <!-- this is not a main query but a custom query calling cpt 'kampagne' -->
	</div>
</div>

<?php ?>