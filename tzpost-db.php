<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	global $tzpostPlist;
	global $tzpostQuery;
	global $tzpostContainer;
	global $tzpostContent;

	$tzpostPlist = array(
			"tzpost_post_stypes" => "post",
			"tzpost_plistmax" => "99",
			"tzpost_plistorder_by" => "name",
			"tzpost_plistorder" => "ASC",
			"tzpost_plist" => array()
	);
	
	$tzpostQuery = array(
			"tzpost_post_types" => "post",
			"tzpost_maxpost" => "10",
			"tzpost_order_by" => "date",
			"tzpost_post_format" => "all",
			"tzpost_order" => "DESC"
	);

	$tzpostContainer = array(
			"tzpost_thumbnail" => "thumbnail",
			"tzpost_default_image" => "",
			"tzpost_layout" => "default",
			"tzpost_views_all_text" => "",
			"tzpost_views_all" => "",
	);

	$tzpostContent = array(
			"tzpost_excerptlen" => "25",
			"tzpost_image_display" => "yes",
			"tzpost_title_display" => "yes",
			"tzpost_excerpt_display" => "no",
			"tzpost_views_all_display" => "yes",
			"tzpost_label_display" => "no",
			"tzpost_link_target" => "_self"
	);


	
	function set_tzpost_options(){
		global $wpdb;
		global $tzpostPlist;
		global $tzpostQuery;
		global $tzpostContainer;
		global $tzpostContent;

		$tzpost_opt_table = $wpdb->prefix.'tzpost_optionset';
		
		/*if($db_version){
			update_option('tzpost-update-notification','show');
		}*/
		
		if(!get_option('tzpostsmethod1')){
			add_option('tzpostsmethod1','plist');
		}
		if(!get_option('tzpostsmethod2')){
			add_option('tzpostsmethod2','plist');
		}
		if(!get_option('tzpostsmethod3')){
			add_option('tzpostsmethod3','plist');
		}
		$ins_q = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."tzpost_optionset (
  				`id` int(5) NOT NULL AUTO_INCREMENT,
  				`template` varchar(10) CHARACTER SET utf8 NOT NULL,
				`plist` text CHARACTER SET utf8 NOT NULL,
  				`query` text CHARACTER SET utf8 NOT NULL,
  				`slider` text NOT NULL,
  				`container` text NOT NULL,
  				`content` text NOT NULL,
  				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
		$wpdb->query($ins_q);
		
		$ins_q2 = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."tzpost_thumbnail (
				`id` int(2) NOT NULL AUTO_INCREMENT,
				`thumb_name` varchar(500) NOT NULL,
				`width` int(4) NOT NULL,
				`height` int(4) NOT NULL,
				`crop` varchar(5) NOT NULL,
				PRIMARY KEY (`id`)
			  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
		$wpdb->query($ins_q2);
		
		$q1 = "insert into ".$tzpost_opt_table." (template,plist,query,container,content) values('default','".serialize($tzpostPlist)."','".serialize($tzpostQuery)."','".serialize($tzpostContainer)."','".serialize($tzpostContent)."')";

		if(!$wpdb->get_results("select id from ".$tzpost_opt_table." where template = 'default'")){
			$wpdb->query($q1);
		}
		
		if(!$wpdb->get_results("select id from ".$wpdb->prefix."tzpost_thumbnail where thumb_name = 'tzpost-thumb-one'")){
			$wpdb->query( "insert into ".$wpdb->prefix."tzpost_thumbnail (thumb_name,width,height,crop) values('tzpost-thumb-one',600,220,'yes')");
		}
	}