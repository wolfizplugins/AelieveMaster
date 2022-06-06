<?php
echo get_post_field( 'post_name', get_post() );
if (!class_exists('view_eachpost')) {
	class view_eachpost {
		public function __construct() {
			get_header();
			get_sidebar();
			$this->MyTestimonial_eachpost();
			get_footer();
		}
		public function MyTestimonial_eachpost() {
			$args       = array(
			'post_type'   => 'my_testimonials',
			);
			$terms_Tags = wp_get_object_terms( get_the_ID(), 'testimonial_tags', $args );
			$terms_Cats = wp_get_object_terms( get_the_ID(), 'testimonial_cats', $args );
			if (has_post_thumbnail()) {
				?>
			  <img class="imgt" src="<?php echo get_the_post_thumbnail(get_the_ID()); ?>
				<?php
			} else {
				?>
			  <img class='imgt' src="<?php echo plugins_url('MyTestimonial/assets/img/placeholder.png'); ?>" />
			  <?php } ?>
		<p class="post_Title"><a href="<?php echo get_post_field( 'post_name', get_post() ); ?>" data-title="<?php echo get_the_ID(); ?>"><?php echo get_the_title(); ?></a></p>
			<?php if (get_the_content()!='') { ?>
		<p class="post_Content"><?php echo get_the_content(); ?></p>
		<?php } else { ?>
		<p class="post_Content">No description provided.</p>
		<?php } ?>
			<?php
				echo '<div class="ForCatTag" style="display: block !important;">';
				echo '<div class="Categories">';
			if (!$terms_Cats) {
				echo 'Uncategorized';}
			foreach ($terms_Cats as $key => $value) {
				if ($key==0) {
					echo 'Categories';} 
				echo '<p>' . $terms_Cats[$key]->name . '</p>';
			}
				echo '</div>';
				echo '<div class="Tags">';
			if (!$terms_Tags) {
				echo 'No tags';}
			foreach ($terms_Tags as $key => $value) {
				if ($key==0) {
					echo 'Tags';} 
				echo '<p>' . $terms_Tags[$key]->name . '</p>';
			}
				echo '</div>';
				echo '</div>';
			?>
			<br />
			 <div class="bottom">
									<p class="post_Dated"><?php echo get_the_date(); ?></p>
									<?php
									if (!empty(get_post_meta( get_the_ID(), 'client_name' ))) { 
										?>
									<p class="post_Dated">By: <?php echo ucfirst(get_post_meta( get_the_ID(), 'client_name' )[0]); ?></p>
										<?php 
									} else {
										echo 'Unknown Author';} 
									?>
			</div>
			<?php
		}
	}
	$view_eachpost = new view_eachpost();
}
