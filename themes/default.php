<?php
if(!empty($list)):
	if(isset($list[0]))
	{
		$main_item = $list[0];
		unset($list[0]);
	}
$theme = $instance['theme'];
$image_size = $instance['image_size'];
$image_size_custom = $instance['image_size_custom'];
$thumb_size = $instance['thumb_size'];
$thumb_size_custom = $instance['thumb_size_custom'];
$show_readmore = $instance['show_readmore'];
$readmore_label = $instance['readmore_label'];
$show_paging = $instance['show_paging'];
$show_desc = $instance['show_desc'];
$show_thumb = $instance['show_thumb'];
$show_desc_small = $instance['show_desc_small'];

if($image_size_custom!='')
{
	$image_size_custom = explode(",",$image_size_custom);
	isset($image_size_custom[0])? $image_size_custom[0] = intval($image_size_custom[0]):'';
	isset($image_size_custom[1])? $image_size_custom[1] = intval($image_size_custom[1]):'';
	/* add custom style */
		wp_enqueue_style(
			'custom-styles',
			plugins_url('/assets/css/custom_styles.css', str_replace("themes","",__FILE__))
		);
		$custom_css = "
				#bm_articles_top_".$widget_id." .bm_articles_top_all .bm_top_first .bm_top_img{width:".$image_size_custom[0]."px; height:".$image_size_custom[1]."px}
				#bm_articles_top_".$widget_id." .bm_articles_top_all .bm_top_first .bm_top_img img{width:".$image_size_custom[0]."px; height:".$image_size_custom[1]."px}";
		wp_add_inline_style( 'custom-styles', $custom_css );
		
	/* End add custom style */
}
if($thumb_size_custom!='')
{
	$thumb_size_custom = explode(",",$thumb_size_custom);
	isset($thumb_size_custom[0])? $thumb_size_custom[0] = intval($thumb_size_custom[0]):'';
	isset($thumb_size_custom[1])? $thumb_size_custom[1] = intval($thumb_size_custom[1]):'';

	/* add custom style */
		wp_enqueue_style(
			'custom-styles',
			plugins_url('/assets/css/custom_styles.css', str_replace("themes","",__FILE__))
		);
		$custom_css = "
				#bm_articles_top_".$widget_id." .bm_articles_top_all .bm_top_second .bm_top_item .bm_top_item_img img{width:".$thumb_size_custom[0]."px; height:".$thumb_size_custom[1]."px}";
		wp_add_inline_style( 'custom-styles', $custom_css );
		
	/* End add custom style */
}
?>
<div id="bm_articles_top_<?php echo $widget_id;?>" class="bm_articles_top bm_aticled_top_<?php echo $theme;?> <?php echo $moduleclass_sfx;?>">
	<div class="bm_articles_top_all">
		<div class="bm_top_first">
			<div class="bm_top_img">
				<a href="<?php echo get_permalink( $main_item->ID ); ?>" >
					<span class="bm_rollover" ><i class="fa fa-link"></i></span>
                    <?php echo get_the_post_thumbnail ($main_item->ID, $image_size); ?>
                </a>
			</div>
			<div class="bm_top_content">
				<div class="bm_top_title">
					<a href="<?php echo get_permalink( $main_item->ID ); ?>"><?php echo $main_item->post_title; ?></a>
				</div>
				
				<?php if($show_desc):?>
					<div class="bm_top_desc">
						<div>
							 <?php
								if ( preg_match('/<!--more(.*?)?-->/', $main_item->post_content, $matches) ) {
									$content = explode($matches[0], $main_item->post_content, 2);
									$content = $content[0];
								} else {
									$text = strip_shortcodes( $main_item->post_content );
									$text = apply_filters('the_content', $text);
									$text = str_replace(']]>', ']]&gt;', $text);
									$content = wp_trim_words($text, $length);
								}
								echo $content;
							?>
							<?php if($show_readmore):?>
								<a href="<?php echo get_permalink( $main_item->ID ); ?>"><?php echo $readmore_label; ?></a>
							<?php endif;?>
						</div>
					</div>
				<?php endif;?>
				
			</div>
		</div>
		<?php
			$style="";
			//var_dump($thumb_size);die;
			if($theme != 'theme1' && $theme != 'theme5')
			{
				$width = 100;
				if($count = count($list))
				{
					$width = round(100/$count);
				}
				$style = "style='width:".$width."%'";
			}
		?>
		<div class="bm_top_second">
			<?php foreach ($list as $post) : ?> 
				<div class="bm_top_item" <?php echo $style;?>>
                	<?php if($show_thumb):?>
					<div class="bm_top_item_img">
						<a href="<?php echo get_permalink( $post->ID ); ?>" >
							<?php echo get_the_post_thumbnail ($post->ID, $thumb_size); ?>
						</a>
					</div>
                    <?php endif;?>
					<div class="bm_top_item_content">
						<div class="bm_item_top_title">
							<a href="<?php echo get_permalink( $post->ID ); ?>"><?php echo $post->post_title; ?></a>
						</div>				
						<?php if($show_desc_small):?>
							<div class="bm_item_top_desc">
								<div>
									 <?php
										if ( preg_match('/<!--more(.*?)?-->/', $post->post_content, $matches) ) {
											$content = explode($matches[0], $post->post_content, 2);
											$content = $content[0];
										} else {
											$text = strip_shortcodes( $post->post_content );
											$text = apply_filters('the_content', $text);
											$text = str_replace(']]>', ']]&gt;', $text);
											$content = wp_trim_words($text, $small_length);
										}
										echo $content;
									?>
									<?php if($show_readmore):?>
										<a href="<?php echo get_permalink( $post->ID ); ?>"><?php echo $readmore_label; ?></a>
									<?php endif;?>
								</div>
							</div>
						<?php endif;?>				
					</div>
				</div>
			<?php endforeach; ?>		
		</div>
	</div>
</div>
<?php else: ?>
	<div class="bm-nodata"><?php echo 'Found no Post!';?></div>
<?php endif;?>