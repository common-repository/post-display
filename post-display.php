<?php
/*
	Plugin Name: Post Display
	Plugin URI: http://templaza.com
	Description: Plugin Display Post with multiple layout order by date, title, random... Developer can override HTML or create new layout in your theme (Theme name/post-display/any layout.php).
	Version: 1.0.0
	Author: tuyennv, templaza
	Author URI: http://templaza.com
	License: GPL2
*/

define( 'TZ_CUSTOM_POST_DISPLAY_DIR', plugin_dir_path( __FILE__ ) );

	function tzpost_modify_menu(){
		$page_cat = add_menu_page('Theme page title', 'Post display', 'delete_pages', 'tzpost-display', 'tzpost_options', plugins_url('images/tzsidebar.png', __FILE__));
		add_submenu_page('tz_plusgallery', 'Post Display', 'Post Display', 'delete_pages', 'tzpost-display', 'tzpost_options');
	}
	
	add_action('admin_menu','tzpost_modify_menu');

	function tzpost_options(){
		require 'tzpost-admin.php';
	}
	function tzpost_plugin_get_version() {
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
		$plugin_file = basename( ( __FILE__ ) );
		return $plugin_folder[$plugin_file]['Version'];
	}

	define('tzpost_url',WP_PLUGIN_URL."/post-display/");

	require 'tzpost-db.php';
	
	/* ---------------------------------------------------------------------------------*/
	function tzpost_enqueue() {
		wp_enqueue_style("tzpostStyleSheet", plugins_url("tzpost-style.css", __FILE__), FALSE);
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'tzpost_front_script',tzpost_url.'js/tzpost.frnt.script.js' );
	}
	add_action( 'wp_enqueue_scripts', 'tzpost_enqueue' );
	
	function tzpost_custom_wp_admin_style() {
		global $wp_version;		
		if(isset($_GET['page'])){
		$pgslug = $_GET['page'];
		$menuslug = array('tzpost-display');
			if(!in_array($pgslug,$menuslug))
        		return;
       		 
			if($wp_version >= '3.5'){
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );
			}
			else
			{
				wp_enqueue_style( 'farbtastic' );
  				wp_enqueue_script( 'farbtastic' );
			}
			wp_enqueue_script( 'tzpost-js-script', tzpost_url . 'js/tzpost.script.js', array( 'jquery' ) );
			wp_enqueue_style("tzpost_admin", plugins_url("css/tzcustom_display_admin.css", __FILE__), FALSE);
			wp_localize_script( 'tzpost-js-script', 'tzpostajx', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),'tzpostAjaxReqChck' => wp_create_nonce( 'tzpostauthrequst' )));
			
		}
	
	}
	add_action( 'admin_enqueue_scripts', 'tzpost_custom_wp_admin_style' );
	
	/* ---------------------------------------------------------------------------------------*/
	
	register_activation_hook(WP_PLUGIN_DIR.'/post-display/post-display.php','set_tzpost_options');
	register_deactivation_hook(WP_PLUGIN_DIR.'/post-display/post-display.php','unset_tzpost_options');
	
	
	function unset_tzpost_options(){
	}

	/* ---------------------------------------------------------------------------------------*/
	function tzpost_image_sizes(){
	  if ( function_exists( 'add_image_size' ) ) { 
		  global $wpdb;
		  $rth = $wpdb->get_results( "select * from ".$wpdb->prefix."tzpost_thumbnail");
		  foreach($rth as $th){
			  add_image_size( $th->thumb_name,$th->width,$th->height, true); 
		  }
	  }
	  if(!function_exists('tzpost_resize') && !class_exists('TZcustom_Resize'))
		require_once( 'tzpost_resizer.php' );
	}
	add_action('wp_loaded', 'tzpost_image_sizes');
	
	function tzpost_excerpt_length_one( $length ) {
		
		return get_option('tzpost_excerptlen_one');
	}
	
	function tzpost_excerpt_length( $length ) {
		return get_option('tzpost_excerptlen');
	}	
	add_action( "wp_ajax_tzpostchkCategory", "tzpostchkCategory" );
	add_action( "wp_ajax_tzpostUpdateLabel", "tzpostUpdateLabel" );
	add_action( "wp_ajax_tzpostUpdateOpt", "tzpostUpdateOpt" );
	add_action( "wp_ajax_tzpostListPost", "tzpostListPost" );
	add_action( "wp_ajax_tzpostupdateSmethod", "tzpostupdateSmethod" );
	
	function tzpostUpdateLabel(){
		$nonce = $_POST['checkReq'];
		$fname = $_POST['f_name'];
		$fvalue = trim($_POST['f_value']);
		if(! defined( 'ABSPATH' ) || !wp_verify_nonce( $nonce, 'tzpostauthrequst' )){
			echo "Unauthorized request.";
			exit;
		}
		update_option($fname,$fvalue);
		exit;
	}
	
	function tzpostSanit($str){
		return sanitize_text_field($str);
	}

	function tzpostchkCategory(){
		$nonce = $_POST['checkReq'];
		$posttype = $_POST['post_type'];
		if(! defined( 'ABSPATH' ) || !wp_verify_nonce( $nonce, 'tzpostauthrequst' )){
			echo "Unauthorized request.";
			exit;
		}
		$catHtml = '';
		if($posttype == 'post'){
			$catHtml = '<th scope="row">Category</th><td><select name="tzpost_category[]" multiple="multiple">';
			$catList = get_categories();
			foreach($catList as $scat){	 
    			$catHtml .= '<option value="'.$scat->term_id.'">'.$scat->name.'</option>';
    		}
  		$catHtml .= '</select><span style="padding-left:10px; font-size:10px; font-style:italic; vertical-align:top;">[ * You can select multiple category ]</span></td>';			
		}
		else
		{
			$posttypeobj = get_post_type_object($posttype);
			if(in_array('category',$posttypeobj->taxonomies)){
				$catHtml = '<th scope="row">Category</th><td><select name="tzpost_category[]" multiple="multiple">';
				$catList = get_categories();
				foreach($catList as $scat){	 
					$catHtml .= '<option value="'.$scat->term_id.'">'.$scat->name.'</option>';
				}
			$catHtml .= '</select><span style="padding-left:10px; font-size:10px; font-style:italic; vertical-align:top;">[ * You can select multiple category ]</span></td>';
			}
		}
		echo $catHtml;
		exit;
	}
	function tzpostUpdateOpt(){
		$nonce = $_POST['checkReq'];
		$optdata = $_POST['optdata'];
		
		if(! defined( 'ABSPATH' ) || !wp_verify_nonce( $nonce, 'tzpostauthrequst' )){
			echo "Unauthorized request.";
			exit;
		}
		
		global $wpdb;
		$all_field = array();
		parse_str($optdata,$all_field);
		
		$optID = sanitize_text_field($all_field['opt_id']);
		$optfield = sanitize_text_field($all_field['opt_field']);
		
		unset($all_field['opt_id']);
		unset($all_field['opt_field']);
		
		$update_data = array();
		foreach($all_field as $fkey => $fval){
			if(is_array($fval)){
				$update_data[$fkey] = array_map('tzpostSanit',$fval);
			}
			else
			{
				$update_data[$fkey] = sanitize_text_field($fval);
			}
		}
		
		$update_data = serialize($update_data);
		
		$q_chk = $wpdb->prepare("select template from ".$wpdb->prefix."tzpost_optionset where ".$optfield." = '%s' and id = %d",$update_data,$optID);
		if(!$wpdb->get_results($q_chk)){
			$q_upd = $wpdb->prepare("update ".$wpdb->prefix."tzpost_optionset set ".$optfield." = '%s' where id = %d",$update_data,$optID);
			if($wpdb->query($q_upd)){
				echo "Updated successfully.";
			}	
		}
		else
		{
			echo 'No change.';
		}
		exit;
	}
	function tzpostListPost(){
		$nonce = $_POST['checkReq'];
		$ptype = $_POST['ptype'];
		$pmax = $_POST['pmax'];
		$porderBy = $_POST['porderBy'];
		$porder = $_POST['porder'];
		$plist = explode(',',$_POST['plist']);
		
		if(! defined( 'ABSPATH' ) || !wp_verify_nonce( $nonce, 'tzpostauthrequst' )){
			echo "Unauthorized request.";
			exit;
		}
		$plistHtml = '';
		$lpargs = array(
				'post_type'      => $ptype,
				'posts_per_page' => $pmax,
				'orderby'		 => $porderBy,
				'order'			 => $porder
		);
		$pl_query = new WP_Query($lpargs); while ($pl_query->have_posts()) : $pl_query->the_post();
		if($plist && in_array(get_the_id(),$plist)){
			$plistHtml .= '<option value="'.get_the_id().'" selected="selected">'.get_the_title().'</option>';
		}
		else{
			$plistHtml .= '<option value="'.get_the_id().'">'.get_the_title().'</option>';
		}
		endwhile;wp_reset_query();
		echo $plistHtml;
		exit;
	}
	function tzpostupdateSmethod(){
		$nonce = $_POST['checkReq'];
		$selnam = $_POST['selnam'];
		$selval = $_POST['selval'];
		
		if(! defined( 'ABSPATH' ) || !wp_verify_nonce( $nonce, 'tzpostauthrequst' )){
			echo "Unauthorized request.";
			exit;
		}
		update_option($selnam,$selval);
		exit;
	}
	/* ---------------------------------------------------------------------------------------*/


function tzpost_head_css(){
    wp_enqueue_style("tzpost_css", plugins_url("css/tzpost_css.css", __FILE__), FALSE);
    wp_enqueue_script( 'owljs', plugins_url("js/owl.carousel.js", __FILE__), array(), '1.0.0', true );
}
add_action('wp_head','tzpost_head_css');


	function tzpost_display($atts) {
		global $post;
		global $wpdb;
		$current = $post->ID;
		
		if(is_array($atts) && array_key_exists('tzpost',$atts)){
			$q1 = "select * from ".$wpdb->prefix."tzpost_optionset where id = ".intval($atts['tzpost']);
			$res1 = $wpdb->get_results($q1);
			if($res1){
				$plist = unserialize($res1[0]->plist);
				$query = unserialize($res1[0]->query);
				$container = unserialize($res1[0]->container);
				$content = unserialize($res1[0]->content);
			}
			else return;	
			$tzpostID = $atts['tzpost'];
		}
		else return;
		
		$qtype = get_option('tzpostsmethod'.$tzpostID);
		$tzpost_format = $query['tzpost_post_format'];

		if($qtype == 'query'){
			if($tzpost_format !='all') {
				$query_arg = array(
					'post_type' 	 => ($query['tzpost_post_types']) ? $query['tzpost_post_types'] : 'post',
					'posts_per_page' =>	($query['tzpost_maxpost']) ? $query['tzpost_maxpost'] : 10,
					'orderby'		 => ($query['tzpost_order_by']) ? $query['tzpost_order_by'] : 'date',
					'order'			 => ($query['tzpost_order']) ? $query['tzpost_order'] : 'DESC',
					'tax_query' => array(
						array(
							'taxonomy' => 'post_format',
							'field' => 'slug',
							'terms' => array(
								'' . $tzpost_format . ''
							),
						)
					)
				);
			} else{
				$query_arg = array(
					'post_type' 	 => ($query['tzpost_post_types']) ? $query['tzpost_post_types'] : 'post',
					'posts_per_page' =>	($query['tzpost_maxpost']) ? $query['tzpost_maxpost'] : 10,
					'orderby'		 => ($query['tzpost_order_by']) ? $query['tzpost_order_by'] : 'date',
					'order'			 => ($query['tzpost_order']) ? $query['tzpost_order'] : 'DESC'
				);
			}
			
			if($query['tzpost_post_types'] && $query['tzpost_post_types'] != "page"){
				if($query['tzpost_post_types'] == "post"){
					if(isset($query['tzpost_category'])){
						$query_arg['cat'] = implode(',',$query['tzpost_category']);
					}
				}
				else
				{
					$post_type_obj = get_post_type_object( $query['tzpost_post_types'] );
					if(in_array('category',$post_type_obj->taxonomies)){
						if(isset($query['tzpost_category'])){
							$query_arg['cat'] = implode(',',$query['tzpost_category']);
						}
					}
				}
			}
		}
		elseif($qtype == 'plist'){
			$query_arg = array(
				'post_type' 	 => ($plist['tzpost_post_stypes']) ? $plist['tzpost_post_stypes'] : 'post',
				'post__in'   	 => $plist['tzpost_plist'],
				'posts_per_page' =>	($plist['tzpost_plistmax']) ? $plist['tzpost_plistmax'] : 10,
				'orderby'		 => ($plist['tzpost_plistorder_by']) ? $plist['tzpost_plistorder_by'] : 'date',
				'order'			 => ($plist['tzpost_plistorder']) ? $plist['tzpost_plistorder'] : 'DESC'
			);
		}
		
		$template = $res1[0]->template;

		$theme_part = get_template_directory();
		$directory = "" . $theme_part . "/post-display/";
		$directory_plg = TZ_CUSTOM_POST_DISPLAY_DIR."/tpl_views/";
		$tzposts_theme_layout = glob($directory . "*.php");
		$tzposts_plg_layout = glob($directory_plg . "*.php");

		if(!empty($tzposts_theme_layout)){
		  $tzpost_layouts = $tzposts_theme_layout;
		}else{
		  $tzpost_layouts = $tzposts_plg_layout;
		}

		foreach($tzpost_layouts as $tzpost_layout  ){
			$filename = substr( $tzpost_layout, ( strrpos( $tzpost_layout, "/" ) +1 ) );
			$filenames = str_replace('.php','',$filename);

			 if($container['tzpost_layout'] == $filenames){
			 require_once($tzpost_layout);
			 $function_name = "tzpost_".$container['tzpost_layout']."";
			 return $function_name($tzpostID,$template, $plist,$query,$query_arg,$container,$content);
			 }
		}
}
	
	add_shortcode('tzpost-display', 'tzpost_display');