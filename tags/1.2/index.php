<?php
/*
 Plugin Name:  Special Folder
 Plugin URI: http://www.islam.com.kw
 Description: This plugin will help editors create special folders on specific topics or events. You need just to insert the IDs of posts.
 Version: 1.2
 Author: EDC Team (E-Da`wah Committee)
 Author URI: http://www.islam.com.kw/
 Text Domain: special-folder
 License: Free
*/

function special_folder_load_textdomain() {
	load_plugin_textdomain( 'special-folder', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}
add_action('plugins_loaded', 'special_folder_load_textdomain');

add_action( 'wp_enqueue_scripts', 'special_folder_css' );
function special_folder_css() {
	wp_register_style( 'special-folder-style', plugins_url( 'style.css', __FILE__ ) );
	wp_enqueue_style( 'special-folder-style' );
}

function special_folder_admin_css() {
	wp_register_style( 'special-folder-style', plugins_url( 'style.css', __FILE__ ) );
	wp_enqueue_style( 'special-folder-style' );
}
add_action( 'admin_enqueue_scripts', 'special_folder_admin_css' );

function special_folder_install(){
	add_option( 'special_folder_ids', '', null );
	add_option( 'special_folder_view', 'list', null );
	add_option( 'special_folder_show_image', 1, null );
	add_option( 'special_folder_show_excerpt', 1, null );
}
register_activation_hook(__FILE__, 'special_folder_install'); 

function special_folder_replace($t){
	$text = preg_replace_callback("/posts\[([0-9,]*?)\]/s", "get_special_folder", $t);
	return $text;
}
add_filter('the_content','special_folder_replace');

function special_folder_menu() {
	add_menu_page( __('Special Folder', 'special-folder'), __('Special Folder', 'special-folder'), 'manage_options', 'special-folder-edit', 'special_folder_options', plugins_url( 'images/special_folder.png', __FILE__ ) );
}
add_action( 'admin_menu', 'special_folder_menu' );

function get_special_folder_last($IDs=''){
	if(empty($IDs)){
		$data = '';
	}else{
		if(is_single()){
			$arr_IDs = explode( ',', $IDs[1] );
		}else{
			$arr_IDs = explode( ',', $IDs );
		}
		
		$args = array(
			'posts_per_page' => 20,
			'offset' => 0,
			'orderby' => 'date',
			'order' => 'DESC',
			'post_type' => 'post',
			'post_status' => 'publish',
			'post__in' => $arr_IDs
		);

		$posts = get_posts($args);
		$li_list = '';
		$tpl = '';
		foreach($posts as $post){
			$ID = $post->ID;
			$post_author = esc_html($post->post_author);
			$post_date = esc_html($post->post_date);
			$post_date_gmt = esc_html($post->post_date_gmt);
			$post_title = esc_attr($post->post_title);
			$post_excerpt = esc_attr($post->post_excerpt);
			$post_name = esc_html($post->post_name);
			$guid = $post->guid;
			$permalink = esc_url( get_permalink($ID) );
			
			if(has_post_thumbnail( $ID ) ){
				$get_image = wp_get_attachment_image_src( get_post_thumbnail_id( $ID ), 'thumbnail' ); 
				$image = $get_image[0];
			}else{
				$iamge = '';
			}

			$li_list .= '<li><a href="'.$guid.'">'.$post_title.'</a></li>';
			
			$tpl .= sprintf( '<div class="block">
			<section class="section_box">
				<div class="blog_style1">
					<div class="content_inner">
						<div class="post_thumb"><a href="%1$s"><img src="%2$s" alt="%3$s"></a></div>
						<h2><a href="%4$s">%5$s</a></h2>
						<p>%6$s</p>
					</div>
				</div>
			</section>
		<div style="clear:both;"></div>
		</div>', $permalink, $image, $post_title, $permalink, $post_title, $post_excerpt);
		}
		
		if(get_option('special_folder_view') == "list"){
			$data = '<ul>';
			$data .= $li_list;
			$data .= '</ul>';
		}else{
			$data = $tpl;
		}
	}
	return $data;
}

function get_special_folder($IDs=''){
	if(empty($IDs)){
		$data = '';
	}else{
		if(is_single()){
			$arr_IDs = explode( ',', $IDs[1] );
		}else{
			$arr_IDs = explode( ',', $IDs );
		}

		$posts = '';
		foreach($arr_IDs as $ID) {
		    $posts .= get_special_folder_post($ID);
		}
		
		if(get_option('special_folder_view') == "list"){
			$data = '<ul>';
			$data .= $posts;
			$data .= '</ul>';
		}else{
			$data = $posts;
		}
	}
	return $data;
}

function get_special_folder_post($ID=''){
	if(empty($ID)){
		$data = '';
	}else{
		$post = get_post( $ID ); 

		$post_author = esc_html($post->post_author);
		$post_date = esc_html($post->post_date);
		$post_date_gmt = esc_html($post->post_date_gmt);
		$post_title = esc_attr($post->post_title);
		$post_excerpt = esc_attr($post->post_excerpt);
		$post_name = esc_html($post->post_name);
		$guid = $post->guid;
		$permalink = esc_url( get_permalink($ID) );
		
		if(has_post_thumbnail( $ID ) ){
			$get_image = wp_get_attachment_image_src( get_post_thumbnail_id( $ID ), 'thumbnail' ); 
			$image = $get_image[0];
		}else{
			$iamge = '';
		}

		$li_list = '<li><a href="'.$guid.'">'.$post_title.'</a></li>';
			
		$tpl = sprintf( '<div class="block">
			<section class="section_box">
				<div class="blog_style1">
					<div class="content_inner">
						<div class="post_thumb"><a href="%1$s"><img src="%2$s" alt="%3$s"></a></div>
						<h2><a href="%4$s">%5$s</a></h2>
						<p>%6$s</p>
					</div>
				</div>
			</section>
		<div style="clear:both;"></div>
		</div>', $permalink, $image, $post_title, $permalink, $post_title, $post_excerpt);
		
		if(get_option('special_folder_view') == "list"){
			$data = $li_list;
		}else{
			$data = $tpl;
		}
	}
	return $data;
}

function special_folder_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'special-folder' ) );
	}

	if ( isset( $_POST['save_settings'] ) && wp_verify_nonce( $_POST['save_settings'], 'special_folder_nonce' ) ) {
			if( get_option( 'special_folder_ids' ) !== false ){
				$special_folder_show_image = ( isset($_POST['special_folder_show_image']) ? 1 : 0 );
				$special_folder_show_excerpt = ( isset($_POST['special_folder_show_excerpt']) ? 1 : 0 );
				if(preg_match('/^[0-9,]+$/i', sanitize_text_field($_POST['special_folder_ids']))) {
					update_option( 'special_folder_ids', sanitize_text_field($_POST['special_folder_ids']) );
				}
				update_option( 'special_folder_view', sanitize_text_field($_POST['special_folder_view']) );
				update_option( 'special_folder_show_image', $special_folder_show_image );
				update_option( 'special_folder_show_excerpt', $special_folder_show_excerpt );
				echo '<meta http-equiv="refresh" content="0; URL='.esc_url( admin_url( 'admin.php?page=special-folder-edit' ) ).'">';
				exit;
			}else{
				add_option( 'special_folder_ids', '', null );
				add_option( 'special_folder_show_image', 1, null );
				add_option( 'special_folder_show_excerpt', 1, null );
				add_option( 'special_folder_view', 'list', null );
			}
	}else{
		print '';
	}

	$IDs = esc_html( get_option('special_folder_ids') );
	?>
	<div class="wrap nosubsub">
		<h1><?php _e('Special Folder Setting', 'special-folder'); ?></h1>
		<div id="col-container">

			<div id="col-right">
				<div class="col-wrap">
					<div class="form-wrap">
						<?php echo get_special_folder($IDs); ?>
					</div>
				</div>
			</div>
			
			<div id="col-left">
				<div class="col-wrap">
					<div class="form-wrap">
						<form name="sytform" action="" method="post">
							<?php wp_nonce_field( 'special_folder_nonce', 'save_settings' ); ?>

							<div class="form-field">
								<label for="special_folder_ids"><?php _e('IDs', 'special-folder'); ?></label>
								<input id="special_folder_ids" type="text" name="special_folder_ids" value="<?php echo $IDs; ?>">
								<p><?php _e('Separate IDs with comma', 'special-folder'); ?></p>
								<?php if( $IDs != "" ){ echo '<p>'.__('Copy code and paste in page/post.', 'special-folder').'<br /><em><strong>posts['.$IDs.']</strong></em></p>'; } ?>
							</div>

							<div class="form-field">
								<p><label for="special_folder_view"><?php _e('View by', 'special-folder'); ?></label>
								<select id="special_folder_view" name="special_folder_view">
								<option value="list"<?php echo ( get_option('special_folder_view') == 'list' ) ? ' selected="selected"' : ''; ?>><?php _e('List', 'special-folder'); ?></option>
								<option value="template"<?php echo ( get_option('special_folder_view') == 'template' ) ? ' selected="selected"' : ''; ?>><?php _e('Template', 'special-folder'); ?></option>
								</select></p>
								<!--
								<p><input id="special_folder_show_image" type="checkbox" name="special_folder_show_image" <?php echo (get_option('special_folder_show_image') == 1 ? 'checked="checked"' : '' ); ?>> <?php _e('Show Image', 'special-folder'); ?></p>
								<p><input id="special_folder_show_excerpt" type="checkbox" name="special_folder_show_excerpt" <?php echo (get_option('special_folder_show_excerpt') == 1 ? 'checked="checked"' : '' ); ?>> <?php _e('Show Excerpt', 'special-folder'); ?></p>
								-->
							</div>
								
							<p class="submit"><input type="submit" name="Submit" id="submit" class="button button-primary" value="<?php _e('Update options', 'special-folder'); ?>"></p>
						</form>
					</div>

				</div>
			</div>

		</div>
	</div>
<?php
}
