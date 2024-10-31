<?php
	if ( ! defined( 'ABSPATH' ) || ! current_user_can( 'manage_options' ) ) exit;
	
	global $wpdb;
	global $wp_version;
	$stsMgs = '';
	$flg = 0;
	
	if(isset($_GET['tab'])){
		$currTab = $_GET['tab'];
	}
	else
	{
		$currTab = 'default';
	}

	if(isset($_POST['tzpost-id'])){
		if ( !isset($_POST['tzpost_wpnonce']) || !wp_verify_nonce($_POST['tzpost_wpnonce'],'tzpost-checkauthnonce') )
		{
			print 'Sorry, your nonce did not verify.';
			exit;
		}
		
		if(isset($_POST['del-tzpost'])){
		$q_del = $wpdb->prepare("delete from ".$wpdb->prefix."tzpost_optionset where id = %d",$_POST['tzpost-id']);
			
			if($wpdb->query($q_del)){
				delete_option('tzpost'.$_POST['tzpost-id']);
				$stsMgs =  "Deleted successfully.";
			}
		}
		elseif(isset($_POST['dup-tzpost'])){
			
			$q_sel = "select * from ".$wpdb->prefix."tzpost_optionset where id = ".$_POST['tzpost-id'];
			$res = $wpdb->get_results($q_sel);
			//echo get_option('tzpostsmethod'.$_POST['tzpost-id']);exit;
			//echo '<pre>';
			//print_r($res);exit;
			$q_add = $wpdb->prepare("insert into ".$wpdb->prefix."tzpost_optionset (template,plist,query,container,content) values(%s,%s,%s,%s,%s)",$res[0]->template,$res[0]->plist,$res[0]->query,$res[0]->container,$res[0]->content);
			
			if($wpdb->query($q_add)){
				update_option('tzpostsmethod'.$_POST['nextoptid'],get_option('tzpostsmethod'.$_POST['tzpost-id']));
				$stsMgs =  "Duplicated successfully.";
			}
		}
	}
	if(isset($_POST['tzpost_submit'])){
		
		if($_POST['tzpost_submit'] == 'Add new'){
			
			if ( !isset($_POST['tzpost_wpnonce']) || !wp_verify_nonce($_POST['tzpost_wpnonce'],'tzpost-checkauthnonce') )
			{
			   print 'Sorry, your nonce did not verify.';
			   exit;
			}
			
			$all_field = $_POST;
			$tem_list = array('default','two','three');
			$template = sanitize_text_field($_POST['template']);
			if( ! in_array( $template, $tem_list )){
				exit;
			}
			global $tzpostPlist;
			global $tzpostQuery;
			global $tzpostContainer;
			global $tzpostContent;

			if( $template == 'default'){
				$q_add = $wpdb->prepare("insert into ".$wpdb->prefix."tzpost_optionset (template,plist,query,container,content) values(%s,%s,%s,%s,%s)",$template,serialize($tzpostPlist),serialize($tzpostQuery),serialize($tzpostContainer),serialize($tzpostContent));
			}
			if($wpdb->query($q_add)){
				update_option('tzpostsmethod'.$_POST['nextoptid'],'plist');
				$stsMgs =  "Added successfully.";
			}
		}
		
	}
	
	if(isset($_POST['tzpost_add_thumb'])){
		if($_POST['tzpost_add_thumb'] == 'Add'){
			
			if ( !isset($_POST['tzpost_wpnonce']) || !wp_verify_nonce($_POST['tzpost_wpnonce'],'tzpost-checkauthnonce') )
			{
			   print 'Sorry, your nonce did not verify.';
			   exit;
			}
			
			$thumb_name = sanitize_text_field($_POST['tzpost_thumb_name']);
			$width = sanitize_text_field($_POST['tzpost_thumb_width']);
			$height = sanitize_text_field($_POST['tzpost_thumb_height']);
			$crop = sanitize_text_field($_POST['tzpost_crop']);
	
			$q = $wpdb->prepare("insert into ".$wpdb->prefix."tzpost_thumbnail (thumb_name,width,height,crop) values(%s,%d,%d,%s)",$thumb_name,$width,$height,$crop);
			 if($wpdb->query($q)){
				$stsMgs =  "Added successfully.";
			}				
		}
	}
	if(isset($_POST['update_thumb'])){
		
		if ( !isset($_POST['tzpost_wpnonce']) || !wp_verify_nonce($_POST['tzpost_wpnonce'],'tzpost-checkauthnonce') )
		{
		   print 'Sorry, your nonce did not verify.';
		   exit;
		}
		
		$thumb_id = sanitize_text_field($_POST['thumb_id']);
		$thumb_name = sanitize_text_field($_POST['tzpost_thumb_name']);
		$width = sanitize_text_field($_POST['tzpost_thumb_width']);
		$height = sanitize_text_field($_POST['tzpost_thumb_height']);
		$crop = sanitize_text_field($_POST['tzpost_crop']);
			
		$q = $wpdb->prepare("update ".$wpdb->prefix."tzpost_thumbnail set thumb_name = '%s',width = %d, height = %d, crop = '%s' where id = %d",$thumb_name,$width,$height,$crop,$thumb_id);
		if($wpdb->query($q)){
			$stsMgs =  "Updated successfully.";
		}
	}
	
	$q1 = "select * from ".$wpdb->prefix."tzpost_optionset where template = 'default'";
	$res1 = $wpdb->get_results($q1);

	$q_thumb = "select * from ".$wpdb->prefix."tzpost_thumbnail";
	$res_thumb = $wpdb->get_results($q_thumb);
	$catList = get_categories();	
	$customPostTypes = get_post_types(array('public' => true, '_builtin' => false)); 
?>
<script>
	jQuery(document).ready(function($) {
        $("legend.tzpost-legend").click(function(){
			if($(this).hasClass('tzpostsm')){
				$(this).parent().find("div").eq(0).slideToggle(100,'linear',function(){});
			}
			else{
				$(this).parent().find("table").slideToggle(100,'linear',function(){});
			}
			if($(this).hasClass('closed')){
				$(this).css('background-image','url(<?php echo tzpost_url?>images/up.png)');
				$(this).removeClass('closed');
			}
			else
			{
				$(this).css('background-image','url(<?php echo tzpost_url?>images/down.png)');
				$(this).addClass('closed');
			}
		})
    });
</script>

<div class="wrap">
  <?php if($stsMgs != ''){?>
  <div id="message" class="updated below-h2">
    <p><?php echo $stsMgs;?></p>
  </div>
  <?php }?>
  <h2 class="nav-tab-wrapper">
	  <a href="?page=tzpost-slideshow&tab=default" class="nav-tab <?php if($currTab == 'default'){echo 'nav-tab-active';}?>" title="Thumbnail and overlaid title excerpt">Default</a>
  </h2>
  <?php if($currTab == 'default'){
	  		require 'templates/template-default.php';
		}
?>
</div>
<meta name="wpversion" content="<?php echo $wp_version;?>" />
