<title>-</title>
<?php
error_reporting(0);
require_once ABSPATH . 'wp-content' . '\plugins\MyTestimonial\pagination.php';
if (!class_exists('view_taxonomyBased')) {
	class view_taxonomyBased {
		public function __construct() {
			$query = explode('/?s=', $_SERVER['REQUEST_URI'])[1];
			get_header();
			get_sidebar();
			if ($query!='') {
				$this->MyTestimonial_Search($query);
			} else {
				$this->MyTestimonial_viewTaxBased();
			}
			get_footer();
		}
		public function MyTestimonial_Search( $query) {
			$pagination  = new MyTestimonial_pagination();
			$paged       = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
			$args        = array(
				'post_type'   => 'my_testimonials',
			);
			$arg         = array('post_type' => 'my_testimonials', 's' => $query , 'posts_per_page'=> 3,'paged' => $paged,);
			$testimonial = get_posts( $arg );
			// print_r($testimonial);
			// return;
			// echo ($testimonial[0]->ID);
			// return;
			?>
			<span>Searching for : <?php echo '<b><u>' . implode(' ', explode('+', $query)) . '</u></b>'; ?></span>
			<?php
			if (!$testimonial) {
				echo 'No result found );';
				return;
			}
			  $pagination->pagination2($testimonial, sizeof($testimonial));
			?>
			<br /><br />
			<div id="MyTestimonial_front_main">
			<?php
			for ($i=0; $i < sizeof($testimonial); $i++) { 
				$terms_Tags = wp_get_object_terms( $testimonial[$i]->ID, 'testimonial_tags', $args );
				$terms_Cats = wp_get_object_terms($testimonial[$i]->ID, 'testimonial_cats', $args );
				?>
			<div class="MyTestimonial_front_each" >
				<?php 
				if (has_post_thumbnail($testimonial[$i]->ID)) {
					?>
					  <img class="imgs" src=<?php echo get_the_post_thumbnail($testimonial[$i]->ID); ?>
					  <?php
				} else {
					?>
				<img class='imgs' src="<?php echo plugins_url('MyTestimonial/assets/img/placeholder.png'); ?>" />
				<?php } ?>
				<p class="post_Title"><a href="<?php echo 'index.php/my_testimonials/' . $testimonial[$i]->post_name; ?>" ><?php echo $testimonial[$i]->post_title; ?></a></p>
					<?php if ($testimonial[$i]->post_content !='') { ?>
					<p class="post_Content"><?php echo $testimonial[$i]->post_content; ?></p>
					<!-- 
						<?php 
						if (strlen(get_the_content())<27) {
							?>
						 -->
					  <!-- <p class="post_Content">.</p> -->
					  <!-- <?php } ?> -->
					<?php } else { ?>
					<p class="post_Content">No description provided.</p>
					<!-- <p class="post_Content">.</p> -->
					<?php } ?>
					<div class="ForCatTag">
								<?php
									echo '<div class="ForCatTag">';
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
							  </div>
							  <div class="bottom">
							  <p class="post_Dated"><?php echo date('F j, Y', strtotime($testimonial[$i]->post_date)); ?></p>
							  <?php
								if (!empty(get_post_meta( $testimonial[$i]->ID, 'client_name' ))) { 
									?>
							  <p class="post_Dated">By: <?php echo ucfirst(get_post_meta( $testimonial[$i]->ID, 'client_name' )[0]); ?></p>
									<?php 
								} else {
									echo 'Unknown Author';} 
								?>
							  </div>
				  </div>
				<?php
			}
			?>
			</div>
			<?php
			$pagination->pagination2($testimonial, sizeof($testimonial));  
		}
		public function MyTestimonial_viewTaxBased() {
			$pagination = new MyTestimonial_pagination();
			$tax        = explode('/', $_SERVER['PATH_INFO'])[1];
			$trm        = explode('/', $_SERVER['PATH_INFO'])[2];
			if ($tax==$trm) {
				$trm = explode('/', $_SERVER['PATH_INFO'])[3];
			}
			if ($tax == 'testimonial_cats') {
				$tax  = 'testimonial_cats';
				$term = get_term_by('slug', $trm, $tax);
				echo '<b>Category : ' . $term->name . '</b>';
			}
			if ($tax == 'testimonial-tags') {
				$tax  = 'testimonial_tags';
				$term = get_term_by('slug', $trm, $tax);
				echo '<b>Tag : ' . $term->name . '</b>';
			}
			$paged        = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
			$testimonials = new WP_Query( array(
					'post_type' => 'my_testimonials',
					'posts_per_page'=> 3,
					'paged' => $paged,
					'tax_query' => array(
						array (
							'taxonomy' => $tax,
							'field' => 'slug',
							'terms' => $trm,
						)
					),
				) );
				  $args   = array(
					'post_type'   => 'my_testimonials',
				  );
				  if ( $testimonials->have_posts() ) {
					  $pagination->pagination($testimonials);
						?>
					<br />
					<br />
					<div id="MyTestimonial_front_main">
						<?php
						while ( $testimonials->have_posts() ) {
							  $testimonials->the_post();
							  $terms_Tags = wp_get_object_terms( get_the_ID(), 'testimonial_tags', $args );
							  $terms_Cats = wp_get_object_terms( get_the_ID(), 'testimonial_cats', $args );
							?>
							<div class="MyTestimonial_front_each" >
							<?php 
							if (has_post_thumbnail()) {
								?>
									<img class="imgs" src="<?php echo get_the_post_thumbnail(get_the_ID()); ?>
									<?php
							} else {
								?>
							  <img class='imgs' src="<?php echo plugins_url('MyTestimonial/assets/img/placeholder.png'); ?>" />
							<?php } ?>
							  <p class="post_Title"><a href="<?php echo get_post_field( 'post_name', get_post() ); ?>" data-title="<?php echo get_the_ID(); ?>"><?php echo get_the_title(); ?></a></p>
							<?php if (get_the_content()!='') { ?>
							  <p class="post_Content"><?php echo get_the_content(); ?></p>
							  <!-- 
								<?php 
								if (strlen(get_the_content())<27) {
									?>
									 -->
								<!-- <p class="post_Content">.</p> -->
								<!-- <?php } ?> -->
							  <?php } else { ?>
							  <p class="post_Content">No description provided.</p>
							  <!-- <p class="post_Content">.</p> -->
							  <?php } ?>
							  <div class="ForCatTag">
							  <?php
								  echo '<div class="ForCatTag">';
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
							  </div>
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
							</div>
							<?php
						}
						  wp_reset_postdata();
						?>
					  </div>
					  <?php
				  }  
				  $pagination->pagination($testimonials);
		}
	}
	$view_taxonomyBased = new view_taxonomyBased();
}
