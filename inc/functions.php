<?php

/**
 * setup default theme actions
 * @return void
 */
function basey_setup() {
	// tell the TinyMCE editor to use editor-style.css
	add_editor_style( 'editor-style.css' );

	// http://codex.wordpress.org/Post_Thumbnails
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 150, 150, false );

	add_theme_support( 'menus' );

	/**
	 * register menus
	 * http://codex.wordpress.org/Function_Reference/register_nav_menus
	 * http://codex.wordpress.org/Function_Reference/wp_nav_menu
	 */
	register_nav_menus(array(
		'top-bar-l' => 'Left Top Bar',
		'top-bar-r' => 'Right Top Bar'
	));

	if ( ! isset( $content_width ) ) $content_width = 1000;

}
add_action( 'after_setup_theme', 'basey_setup' );

/**
 * http://codex.wordpress.org/Function_Reference/wp_nav_menu
 */
function foundation_nav_bar() {
	wp_nav_menu( array(
		'container'       => false,                // remove menu container
		'container_class' => '',                   // class of container
		'menu'            => '',                   // menu name
		'menu_class'      => 'nav-bar',            // adding custom nav class
		'theme_location'  => 'main-menu',          // where it's located in the theme
		'before'          => '',                   // before each link <a>
		'after'           => '',                   // after each link </a>
		'link_before'     => '',                   // before each link text
		'link_after'      => '',                   // after each link text
		'depth'           => 2,                    // limit the depth of the nav
		'fallback_cb'     => 'main_nav_fb'        // fallback function (see below)
		//'walker'          => new nav_bar_walker()  // walker to customize menu (see foundation-nav-walker)
	) );
}

/**
 * http://codex.wordpress.org/Template_Tags/wp_list_pages
 */
function main_nav_fb() {
	echo '<ul class="nav-bar">';
	wp_list_pages( array(
		'depth'        => 0,
		'child_of'     => 0,
		'exclude'      => '',
		'include'      => '',
		'title_li'     => '',
		'echo'         => 1,
		'authors'      => '',
		'sort_column'  => 'menu_order, post_title',
		'link_before'  => '',
		'link_after'   => '',
		//'walker'       => new page_walker(),
		'post_type'    => 'page',
		'post_status'  => 'publish'
	) );
	echo '</ul>';
}

/**
 * returns 'nothing found' content
 * @return void
 */
function basey_no_results() { ?>

	<div class="alert">
		<?php _e('Sorry, no results were found.', 'basey'); ?>
	</div>
	<?php get_search_form(); ?>

<?php }

/**
 * provides basic query info and output for pagination
 * @return void
 */
function basey_pagination() {
	global $wp_query;
	$total_pages = $wp_query->max_num_pages;
	$big = 999999999; // need an unlikely integer
	if ( $total_pages > 1 ) {
		$current_page = max(1, get_query_var( 'paged' ) );
		echo '<nav class="page-nav">';
		echo paginate_links( array(
			'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'    => '?paged=%#%',
			'current'   => $current_page,
			'total'     => $total_pages,
			'prev_text' => 'Prev',
			'next_text' => 'Next'
		) );
		echo '</nav>';
	}
}

/**
 * Returns page titles based on context
 * @return void
 */
function basey_title() {
	if (is_home()) {
		if (get_option('page_for_posts', true)) {
			echo get_the_title(get_option('page_for_posts', true));
		} else {
			_e('Latest Posts', 'basey');
		}
	} elseif (is_archive()) {
		$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
		if ($term) {
			echo $term->name;
		} elseif (is_post_type_archive()) {
			echo get_queried_object()->labels->name;
		} elseif (is_day()) {
			printf(__('Daily Archives: %s', 'basey'), get_the_date());
		} elseif (is_month()) {
			printf(__('Monthly Archives: %s', 'basey'), get_the_date('F Y'));
		} elseif (is_year()) {
			printf(__('Yearly Archives: %s', 'basey'), get_the_date('Y'));
		} elseif (is_author()) {
			$author = get_queried_object();
			printf(__('Author Archives: %s', 'basey'), $author->display_name);
		} else {
			single_cat_title();
		}
	} elseif (is_search()) {
		printf(__('Search Results for %s', 'basey'), get_search_query());
	} elseif (is_404()) {
		_e('Not Found', 'basey');
	} else {
		the_title();
	}
}