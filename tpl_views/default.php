<?php
function tzpost_default($tzpostID,$template, $plist,$query,$query_arg,$container,$content){
    ob_start(); ?>
    <?php if($content['tzpost_label_display'] == 'yes' || $content['tzpost_views_all_display'] == 'yes' ){ ?>
        <div class="tzpost-heading">
        <?php
        if ($content['tzpost_label_display'] == 'yes') {?>
            <h3 class="tzpost-label"><?php echo esc_attr(get_option('optset'.$tzpostID.''));?></h3>
        <?php } ?>
        <?php
        if ($content['tzpost_views_all_display'] == 'yes') {?>
            <a class="tzpost-all" target="<?php echo $content['tzpost_link_target'];?>" href="<?php echo $container['tzpost_views_all']; ?>"><?php echo $container['tzpost_views_all_text']; ?></a>
        <?php } ?>
        </div>
    <?php } ?>
    <ul id="<?php echo "tzpost" . $tzpostID; ?>" class="tzpost-default">
        <?php $count = 1;
        $the_query = new WP_Query($query_arg);
        while ($the_query->have_posts()) :
        $the_query->the_post();
        if ($template == 'default'):
        ?>
        <li class="tzpost_item">
            <?php
            if ($content['tzpost_image_display'] == 'yes') {
                if (has_post_thumbnail()) { ?>
                    <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail($container['tzpost_thumbnail']);
                    ?>
                        </a>
                    <?php
                } elseif (isset($container['tzpost_default_image']) && $container['tzpost_default_image'] != '') {
                    ?>
                    <a href="<?php the_permalink(); ?>">
                    <img src="<?php echo $container['tzpost_default_image']; ?>" class="wp-post-image"
                         alt="<?php the_title(); ?>"/>
                    </a>
                    <?php
                }
            }
            ?>
            <?php if($content['tzpost_title_display'] == 'yes' || $content['tzpost_excerpt_display'] == 'yes' ){ ?>
            <div class="tzpost-excerpt-<?php echo $template?>">
                <?php
                if ($content['tzpost_title_display'] == 'yes') { ?>
                    <h4 class="tzpost_title"><a href="<?php the_permalink(); ?>"> <?php the_title(); ?></a></h4>
                <?php }
                ?>
                <?php
                if ($content['tzpost_excerpt_display'] == 'yes') {
                    if ( ! has_excerpt() ) {
                    the_content( sprintf('<a href="%s" class="readmore">%s</a>',esc_url(get_permalink()), esc_html__('Read More'),false ));
                      wp_link_pages();
                      } else {
                      ?>
                    <p><?php the_excerpt()?></p>
                    <?php
                    }
                 }
                ?>
            </div>
            <?php } ?>
            <div class="clr"></div>
        </li>
<?php endif;$count++;endwhile; wp_reset_query(); ?>
    </ul>
<?php
    return ob_get_clean();
}