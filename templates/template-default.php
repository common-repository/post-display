<?php
$tzpost_version = tzpost_plugin_get_version();
if ( ! defined( 'ABSPATH' ) || ! current_user_can( 'manage_options' ) ) exit;
?>
<div class="tzpost-col-right">
  <h2>Custom Post Display<?php echo esc_attr($tzpost_version);?></h2>

    <ul class="info_author">
      <li><a href="http://www.templaza.com/forum/14-wordpress-plugin-discussion.html" target="_blank">Plugin Homepage</a></li>
      <li><a href="http://www.templaza.com/forum/14-wordpress-plugin-discussion.html" target="_blank">Help / Support</a></li>
      <li><a href="http://www.templaza.com" target="_blank">Document</a></li>
    </ul>
    <h3>Thanks for using Post Display Plugin</h3>

    <ul class="info_author">
      <li><a href="http://wordpress.org/support/view/plugin-reviews/post-display" target="_blank">Give it a good rating and review</a></li>
      <li><a href="http://wordpress.org/plugins/post-display/" target="_blank">Vote that it work</a></li>
    </ul>
</div>
<?php
$tcount = $wpdb->get_results("SHOW TABLE STATUS WHERE name = '".$wpdb->prefix."tzpost_optionset'");
foreach( $res1 as $dset){
	$plist = unserialize($dset->plist);
	$query = unserialize($dset->query);
	$container = unserialize($dset->container);
	$content = unserialize($dset->content);

	if( !isset($content['tzpost_link_rel'] )) $content['tzpost_link_rel'] = 'none';
?>


<div class="metabox-holder tzpost-display" style="margin-top:20px;">
  <div class="postbox-container" style="width:100%">
    <div class="postbox closed">
      <div class="handlediv down" title="Click to toggle"> <br>
      </div>
      <h3 style="cursor:pointer; text-align:center" class="tzpost-expand <?php if(isset($_POST['tzpost_submit']) && $_POST['tzpost_submit'] == 'Add new slideshow' && $_POST['nextoptid'] == $dset->id){echo 'tzpost-highlight';}?>" id="tzposttxt<?php echo $dset->id;?>">
        <?php if(get_option('tzpost'.$dset->id)){echo get_option('tzpost'.$dset->id);}else{echo 'Post Display '.$dset->id;}?>
      </h3>
      <div class="inside">
        <fieldset>
          <legend class="tzpost-legend" style="width:120px;"><strong>Label & Shortcode</strong></legend>
          <table class="form-table">
            <tr>
              <th scope="row">Label</th>
              <td><input type="text"  value="<?php if(get_option('tzpost'.$dset->id)){echo get_option('tzpost'.$dset->id);}else{echo 'Post Display '.$dset->id;}?>" name="tzpost<?php echo $dset->id;?>" class="tzpost-tzpost-label" onchange="tzpostUpdateLabel(this.name,this.value,<?php echo $dset->id;?>)" />
                <span id="tzpostbox<?php echo $dset->id;?>" style="padding-left:10px; display:none;"><img src="<?php echo tzpost_url;?>/images/ajax-loader.gif" /></span></td>
            </tr>
            <tr>
              <th scope="row">Shortcode</th>
              <td><input style="width:200px; font-size:12px; text-align:left;" type="text" value='[tzpost-display tzpost="<?php echo $dset->id;?>"]' readonly="readonly"  /></td>
            </tr>
          </table>
        </fieldset>
        <fieldset>
          <legend class="tzpost-legend tzpostsm" style="width:80px; background-position:79px 6px;"><strong>Select Post</strong></legend>
          <div id="tzpost-sel<?php echo $dset->id;?>">
            <table class="form-table">
              <tr>
                <th scope="row">Select post using</th>
                <td><select name="tzpostsmethod<?php echo $dset->id?>" onchange="tzupdateSm(this,<?php echo $dset->id;?>);">
                    <option value="plist" <?php if(get_option('tzpostsmethod'.$dset->id) == 'plist'){echo 'selected="selected"';}?>>Post list</option>
                    <option value="query" <?php if(get_option('tzpostsmethod'.$dset->id) == 'query'){echo 'selected="selected"';}?>>Query</option>
                  </select>
                  <span id="smudtsts<?php echo $dset->id;?>" style="padding-left:10px; display:none;"><img src="<?php echo tzpost_url;?>/images/ajax-loader.gif" /></span></td>
              </tr>
            </table>
            <form method="post" onsubmit="return false" id="plist<?php echo $dset->id;?>">
              <table class="form-table <?php if(get_option('tzpostsmethod'.$dset->id) == 'query'){echo 'tzpost-hide';}?>">
                <tr>
                  <th scope="row">Listing option</th>
                  <td><select title="Post type" name="tzpost_post_stypes">
                      <option value="post" <?php if($plist['tzpost_post_stypes'] == 'post'){echo 'selected="selected"';}?>>post</option>
                      <option value="page" <?php if($plist['tzpost_post_stypes'] == 'page'){echo 'selected="selected"';}?>>page</option>
                      <?php
                              foreach ($customPostTypes  as $post_type ) {
                          ?>
                      <option value="<?php echo $post_type;?>" <?php if($plist['tzpost_post_stypes'] == $post_type){echo 'selected="selected"';}?>><?php echo $post_type;?></option>
                      <?php		
                              }
                          ?>
                    </select>
                    <span style="padding-left:10px;">
                    <input type="text" name="tzpost_plistmax" value="<?php echo $plist['tzpost_plistmax'];?>" style="width:40px;" onkeypress="return onlyNum(event);" title="Max number of post to list" />
                    </span> <span style="padding-left:10px;">
                    <select name="tzpost_plistorder_by" title="Order by">
                      <option value="date" <?php if($plist['tzpost_plistorder_by'] == 'date'){echo 'selected="selected"';}?>>Date</option>
                      <option value="ID" <?php if($plist['tzpost_plistorder_by'] == 'ID'){echo 'selected="selected"';}?>>ID</option>
                      <option value="author" <?php if($plist['tzpost_plistorder_by'] == 'author'){echo 'selected="selected"';}?>>Author</option>
                      <option value="title" <?php if($plist['tzpost_plistorder_by'] == 'title'){echo 'selected="selected"';}?>>Title</option>
                      <option value="name" <?php if($plist['tzpost_plistorder_by'] == 'name'){echo 'selected="selected"';}?>>Name</option>
                      <option value="rand" <?php if($plist['tzpost_plistorder_by'] == 'rand'){echo 'selected="selected"';}?>>Random</option>
                      <option value="menu_order" <?php if($plist['tzpost_plistorder_by'] == 'menu_order'){echo 'selected="selected"';}?>>Menu order</option>
                      <option value="comment_count" <?php if($plist['tzpost_plistorder_by'] == 'comment_count'){echo 'selected="selected"';}?>>Comment count</option>
                    </select>
                    </span> <span style="padding-left:10px;">
                    <select name="tzpost_plistorder" title="Order">
                      <option value="ASC" <?php if($plist['tzpost_plistorder'] == 'ASC'){echo 'selected="selected"';}?>>Ascending</option>
                      <option value="DESC" <?php if($plist['tzpost_plistorder'] == 'DESC'){echo 'selected="selected"';}?>>Descending</option>
                    </select>
                    </span> <span style="padding-left:10px;">
                    <button class="button-secondary" value="" onclick="listPost(<?php echo $dset->id;?>)">List</button>
                    </span> <span class="ajx-loaderp" style="padding-left:12px; display:none;"><img src="<?php echo tzpost_url;?>/images/ajax-loader.gif" /></span></td>
                </tr>
                <tr>
                  <th scope="row">Select post from list</th>
                  <td><select name="tzpost_plist[]" multiple="multiple" style="min-height:250px; min-width:300px;" id="tzpost-plist-field<?php echo $dset->id;?>">
                      <?php 
						$lpargs = array(
								'post_type'      => ($plist['tzpost_post_stypes']) ? $plist['tzpost_post_stypes'] : 'post',
								'posts_per_page' => ($plist['tzpost_plistmax']) ? $plist['tzpost_plistmax'] : 99,
								'orderby'		 => ($plist['tzpost_plistorder_by']) ? $plist['tzpost_plistorder_by'] : 'date',
								'order'			 => ($plist['tzpost_plistorder']) ? $plist['tzpost_plistorder'] : 'DESC'
						);
					 	$pl_query = new WP_Query($lpargs); while ($pl_query->have_posts()) : $pl_query->the_post();?>
                      <option value="<?php the_id();?>" <?php if(isset($plist['tzpost_plist']) && in_array(get_the_id(),$plist['tzpost_plist'])){echo 'selected="selected"';}?>>
                      <?php the_title();?>
                      </option>
                      <?php endwhile;wp_reset_query();?>
                    </select>
                    <span style="padding-left:10px; font-size:10px; font-style:italic; vertical-align:top;">[ * You can select multiple ]</span></td>
                </tr>
                <tr>
                  <th scope="row">&nbsp;</th>
                  <td><input type="submit" name="tzpost_submit" value="Save changes" class="button-primary" onclick="tzpostupdateOptionSet('plist<?php echo $dset->id;?>')" />
                    <span class="ajx-loader" style="padding-left:15px; display:none;"><img src="<?php echo tzpost_url;?>/images/ajax-loader.gif" /></span><span class="ajx-sts"></span></td>
                </tr>
              </table>
              <input type="hidden" name="opt_field" value="plist" />
              <input type="hidden" value="<?php echo $dset->id;?>" name="opt_id" />
            </form>
            <form method="post" onsubmit="return false" id="query<?php echo $dset->id;?>">
              <table class="form-table <?php if(!get_option('tzpostsmethod'.$dset->id) || get_option('tzpostsmethod'.$dset->id) == 'plist'){echo 'tzpost-hide';}?>">
                <tr>
                  <th scope="row">Post Type</th>
                  <td><select name="tzpost_post_types" onchange="advpsCheckCat(this.value,<?php echo $dset->id;?>)">
                      <option value="post" <?php if($query['tzpost_post_types'] == 'post'){echo 'selected="selected"';}?>>post</option>
                      <option value="page" <?php if($query['tzpost_post_types'] == 'page'){echo 'selected="selected"';}?>>page</option>
                      <?php
                              foreach ($customPostTypes  as $post_type ) {
                          ?>
                      <option value="<?php echo $post_type;?>" <?php if($query['tzpost_post_types'] == $post_type){echo 'selected="selected"';}?>><?php echo $post_type;?></option>
                      <?php		
                              }
                          ?>
                    </select></td>
                </tr>
                <tr id="tzpost-cat-field<?php echo $dset->id;?>">
                  <?php  
					$posttypeobj = get_post_type_object($query['tzpost_post_types']);
					if($query['tzpost_post_types'] != "page" && ($query['tzpost_post_types'] == 'post' || in_array('category',$posttypeobj->taxonomies))){
				?>
                  <th scope="row">Category</th>
                  <td><select name="tzpost_category[]" multiple="multiple">
                      <?php 
					  	$catList = get_categories();
						foreach($catList as $scat){
					  ?>
                      <option value="<?php echo $scat->term_id;?>" <?php if(isset($query['tzpost_category']) && in_array($scat->term_id,$query['tzpost_category'])){echo 'selected="selected"';}?>><?php echo $scat->name;?></option>
                      <?php }?>
                    </select>
                    <span style="padding-left:10px; font-size:10px; font-style:italic; vertical-align:top;">[ * You can select multiple category ]</span></td>
                  <?php }?>
                </tr>
                <tr>
                  <th scope="row">Max. Number of post</th>
                  <td><input type="text" name="tzpost_maxpost" value="<?php echo $query['tzpost_maxpost'];?>" style="width:60px;" onkeypress="return onlyNum(event);" /></td>
                </tr>
                <tr>
                  <th scope="row">Post Format</th>
                  <td><select name="tzpost_post_format">
                      <option value="all" <?php if($query['tzpost_post_format'] == 'all'){echo 'selected="selected"';}?>>All</option>
                      <option value="aside" <?php if($query['tzpost_post_format'] == 'aside'){echo 'selected="selected"';}?>>Aside</option>
                      <option value="gallery" <?php if($query['tzpost_post_format'] == 'gallery'){echo 'selected="selected"';}?>>Gallery</option>
                      <option value="link" <?php if($query['tzpost_post_format'] == 'link'){echo 'selected="selected"';}?>>Link</option>
                      <option value="image" <?php if($query['tzpost_post_format'] == 'image'){echo 'selected="selected"';}?>>Image</option>
                      <option value="quote" <?php if($query['tzpost_post_format'] == 'quote'){echo 'selected="selected"';}?>>Quote</option>
                      <option value="status" <?php if($query['tzpost_post_format'] == 'status'){echo 'selected="selected"';}?>>Status</option>
                      <option value="video" <?php if($query['tzpost_post_format'] == 'video'){echo 'selected="selected"';}?>>Video</option>
                      <option value="audio" <?php if($query['tzpost_post_format'] == 'audio'){echo 'selected="selected"';}?>>Audio</option>
                      <option value="chat" <?php if($query['tzpost_post_format'] == 'chat'){echo 'selected="selected"';}?>>Chat</option>
                    </select></td>
                </tr>
                <tr>
                  <th scope="row">Order by</th>
                  <td><select name="tzpost_order_by">
                      <option value="date" <?php if($query['tzpost_order_by'] == 'date'){echo 'selected="selected"';}?>>Date</option>
                      <option value="ID" <?php if($query['tzpost_order_by'] == 'ID'){echo 'selected="selected"';}?>>ID</option>
                      <option value="author" <?php if($query['tzpost_order_by'] == 'author'){echo 'selected="selected"';}?>>Author</option>
                      <option value="title" <?php if($query['tzpost_order_by'] == 'title'){echo 'selected="selected"';}?>>Title</option>
                      <option value="name" <?php if($query['tzpost_order_by'] == 'name'){echo 'selected="selected"';}?>>Name</option>
                      <option value="rand" <?php if($query['tzpost_order_by'] == 'rand'){echo 'selected="selected"';}?>>Random</option>
                      <option value="menu_order" <?php if($query['tzpost_order_by'] == 'menu_order'){echo 'selected="selected"';}?>>Menu order</option>
                      <option value="comment_count" <?php if($query['tzpost_order_by'] == 'comment_count'){echo 'selected="selected"';}?>>Comment count</option>
                    </select></td>
                </tr>
                <tr>
                  <th scope="row">Order</th>
                  <td><select name="tzpost_order">
                      <option value="ASC" <?php if($query['tzpost_order'] == 'ASC'){echo 'selected="selected"';}?>>Ascending</option>
                      <option value="DESC" <?php if($query['tzpost_order'] == 'DESC'){echo 'selected="selected"';}?>>Descending</option>
                    </select></td>
                </tr>
                <tr>
                  <th scope="row">&nbsp;</th>
                  <td><input type="submit" name="tzpost_submit" value="Save changes" class="button-primary" onclick="tzpostupdateOptionSet('query<?php echo $dset->id;?>')" />
                    <span class="ajx-loader" style="padding-left:15px; display:none;"><img src="<?php echo tzpost_url;?>/images/ajax-loader.gif" /></span><span class="ajx-sts"></span></td>
                </tr>
              </table>
              <input type="hidden" name="opt_field" value="query" />
              <input type="hidden" value="<?php echo $dset->id;?>" name="opt_id" />
            </form>
          </div>
        </fieldset>

        <fieldset>
          <legend class="tzpost-legend" style="width:155px; background-position:154px 6px;"><strong>Container & Thumbnail</strong></legend>
          <form method="post" onsubmit="return false" id="container<?php echo $dset->id;?>">
            <table class="form-table">
              <tr>
                <th scope="row">Select Thumbnail</th>
                <td><select name="tzpost_thumbnail">
                    <option value="thumbnail" <?php if($container['tzpost_thumbnail'] == 'thumbnail'){echo 'selected="selected"';}?>>thumbnail</option>
                    <option value="medium" <?php if($container['tzpost_thumbnail'] == 'medium'){echo 'selected="selected"';}?>>medium</option>
                    <option value="large" <?php if($container['tzpost_thumbnail'] == 'large'){echo 'selected="selected"';}?>>large</option>
                    <option value="full" <?php if($container['tzpost_thumbnail'] == 'full'){echo 'selected="selected"';}?>>full</option>
                  </select></td>
              </tr>
              <tr>
                <th scope="row">Default image url</th>
                <td><input type="text" name="tzpost_default_image" value="<?php if(isset($container['tzpost_default_image'])){ echo $container['tzpost_default_image'];}?>" style="width:250px;" />
                  <span style="padding-left:10px; font-size:10px; font-style:italic;"> [ N.B. If any post doesn't have featured image then default image will be shown.]</span></td>
              </tr>

              <tr>
                <th scope="row">Post Display Layout</th>
                <?php
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
                ?>
                <td>
                  <select name="tzpost_layout">
                  <?php foreach($tzpost_layouts as $tzpost_layout  ){
                    $filename = substr( $tzpost_layout, ( strrpos( $tzpost_layout, "/" ) +1 ) );
                    $filenames = str_replace('.php','',$filename);
                    ?>
                    <option value="<?php echo esc_attr($filenames);?>" <?php if($container['tzpost_layout'] == $filenames){echo 'selected="selected"';}?>><?php echo esc_attr($filenames);?></option>
                    <?php } ?>
                  </select>
              </tr>

              <tr>
                <th scope="row">View All Text</th>
                <td><input type="text" name="tzpost_views_all_text" value="<?php if(isset($container['tzpost_views_all_text'])){ echo $container['tzpost_views_all_text'];}?>" style="width:250px;" />
                  </td>
              </tr>
              <tr>
                <th scope="row">View All Link</th>
                <td><input type="text" name="tzpost_views_all" value="<?php if(isset($container['tzpost_views_all'])){ echo $container['tzpost_views_all'];}?>" style="width:250px;" />
                  </td>
              </tr>

              <tr>
                <th scope="row">&nbsp;</th>
                <td><input type="submit" name="tzpost_submit" value="Save changes" class="button-primary" onclick="tzpostupdateOptionSet('container<?php echo $dset->id;?>')" />
                  <span class="ajx-loader" style="padding-left:15px; display:none;"><img src="<?php echo tzpost_url;?>/images/ajax-loader.gif" /></span><span class="ajx-sts"></span></td>
              </tr>
            </table>
            <input type="hidden" name="opt_field" value="container" />
            <input type="hidden" value="<?php echo $dset->id;?>" name="opt_id" />
          </form>
        </fieldset>
        <fieldset>
          <legend class="tzpost-legend" style="width:102px; background-position:101px 6px;"><strong>Title & Excerpt</strong></legend>
          <form method="post" onsubmit="return false" id="content<?php echo $dset->id;?>">
            <table class="form-table">
              <tr>
                <th scope="row">Show Label</th>
                <td><select name="tzpost_label_display">
                    <option value="yes" <?php if($content['tzpost_label_display'] == 'yes'){echo 'selected="selected"';}?>>Yes</option>
                    <option value="no" <?php if($content['tzpost_label_display'] == 'no'){echo 'selected="selected"';}?>>No</option>
                  </select></td>
              </tr>
              <tr>
                <th scope="row">Show image</th>
                <td><select name="tzpost_image_display">
                    <option value="yes" <?php if($content['tzpost_image_display'] == 'yes'){echo 'selected="selected"';}?>>Yes</option>
                    <option value="no" <?php if($content['tzpost_image_display'] == 'no'){echo 'selected="selected"';}?>>No</option>
                  </select></td>
              </tr>
              <tr>
                <th scope="row">Show Title</th>
                <td><select name="tzpost_title_display">
                    <option value="yes" <?php if($content['tzpost_title_display'] == 'yes'){echo 'selected="selected"';}?>>Yes</option>
                    <option value="no" <?php if($content['tzpost_title_display'] == 'no'){echo 'selected="selected"';}?>>No</option>
                  </select></td>
              </tr>
              <tr>
                <th scope="row">Show Excerpt</th>
                <td><select name="tzpost_excerpt_display">
                    <option value="yes" <?php if($content['tzpost_excerpt_display'] == 'yes'){echo 'selected="selected"';}?>>Yes</option>
                    <option value="no" <?php if($content['tzpost_excerpt_display'] == 'no'){echo 'selected="selected"';}?>>No</option>
                  </select></td>
              </tr>
              <tr>
                <th scope="row">Show Views All</th>
                <td><select name="tzpost_views_all_display">
                    <option value="yes" <?php if($content['tzpost_views_all_display'] == 'yes'){echo 'selected="selected"';}?>>Yes</option>
                    <option value="no" <?php if($content['tzpost_views_all_display'] == 'no'){echo 'selected="selected"';}?>>No</option>
                  </select></td>
              </tr>
              <tr>
                <th scope="row">Excerpt length</th>
                <td><input type="text" name="tzpost_excerptlen" value="<?php echo $content['tzpost_excerptlen'];?>" style="width:60px;" onkeypress="return onlyNum(event);" />
                  &nbsp;words</td>
              </tr>

              <tr>
                <th scope="row">link target</th>
                <td><select name="tzpost_link_target">
                    <option value="_self" <?php if($content['tzpost_link_target'] == '_self'){echo 'selected="selected"';}?>>_self</option>
                    <option value="_blank" <?php if($content['tzpost_link_target'] == '_blank'){echo 'selected="selected"';}?>>_blank</option>
                    <option value="_new" <?php if($content['tzpost_link_target'] == '_new'){echo 'selected="selected"';}?>>_new</option>
                    <option value="_top" <?php if($content['tzpost_link_target'] == '_top'){echo 'selected="selected"';}?>>_top</option>
                    <option value="_parent" <?php if($content['tzpost_link_target'] == '_parent'){echo 'selected="selected"';}?>>_parent</option>
                  </select></td>
              </tr>
              <tr>
                <th scope="row">&nbsp;</th>
                <td><input type="submit" name="tzpost_submit" value="Save changes" class="button-primary" onclick="tzpostupdateOptionSet('content<?php echo $dset->id;?>')" />
                  <span class="ajx-loader" style="padding-left:15px; display:none;"><img src="<?php echo tzpost_url;?>/images/ajax-loader.gif" /></span><span class="ajx-sts"></span></td>
              </tr>
            </table>
            <input type="hidden" name="opt_field" value="content" />
            <input type="hidden" value="<?php echo $dset->id;?>" name="opt_id" />
          </form>
        </fieldset>

        <!-- </form>-->
        <form method="post" id="frmOptDel<?php echo $dset->id;?>" onsubmit="return false">
          <input type="hidden" value="<?php echo $dset->id;?>" name="tzpost-id" />
          <input type="hidden" value="<?php echo $tcount[0]->Auto_increment;?>" name="nextoptid" />
          <p>
            <input type="submit" name="del-tzpost" value="Delete" class="button-secondary" onclick="tzpostdeleteTzPost(<?php echo $dset->id;?>)" style="width:12%;" />
           </p>
          <?php wp_nonce_field('tzpost-checkauthnonce','tzpost_wpnonce'); ?>
        </form>
      </div>
    </div>
  </div>
</div>
<?php 
}
?>
<div style="position:relative; float:left; width:72%">
  <form method="post">
    <input type="hidden" name="template" value="default" />
    <input type="hidden" name="nextoptid" id="nextoptid" value="<?php echo $tcount[0]->Auto_increment;?>" />
    <?php wp_nonce_field('tzpost-checkauthnonce','tzpost_wpnonce'); ?>
    <input type="submit" name="tzpost_submit" value="Add new" class="button-primary" style="font-weight:bold" />
  </form>
</div>
