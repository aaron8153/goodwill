<?php
/**
 * Boilerplate functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, boilerplate_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'boilerplate_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Boilerplate
 * @since Boilerplate 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640;

/** Tell WordPress to run boilerplate_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'boilerplate_setup' );

if ( ! function_exists( 'boilerplate_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override boilerplate_setup() in a child theme, add your own boilerplate_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Ten 1.0
 */
function boilerplate_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Uncomment if you choose to use post thumbnails; add the_post_thumbnail() wherever thumbnail should appear
	//add_theme_support( 'post-thumbnails' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'boilerplate', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'boilerplate' ),
		'footer_nav1' => __( 'First Footer', 'boilerplate' ),
		'footer_nav2' => __( 'Second Footer', 'boilerplate' ),
		'footer_nav3' => __( 'Third Footer', 'boilerplate' ),
	) );

	// This theme allows users to set a custom background
	add_custom_background();

	// Your changeable header business starts here
	define( 'HEADER_TEXTCOLOR', '' );
	// No CSS, just IMG call. The %s is a placeholder for the theme template directory URI.
	define( 'HEADER_IMAGE', '%s/images/headers/path.jpg' );

	// The height and width of your custom header. You can hook into the theme's own filters to change these values.
	// Add a filter to boilerplate_header_image_width and boilerplate_header_image_height to change these values.
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'boilerplate_header_image_width', 940 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'boilerplate_header_image_height', 198 ) );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be 940 pixels wide by 198 pixels tall.
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

	// Don't support text inside the header image.
	define( 'NO_HEADER_TEXT', true );

	// Add a way for the custom header to be styled in the admin panel that controls
	// custom headers. See boilerplate_admin_header_style(), below.
	add_custom_image_header( '', 'boilerplate_admin_header_style' );

	// ... and thus ends the changeable header business.

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'berries' => array(
			'url' => '%s/images/headers/starkers.png',
			'thumbnail_url' => '%s/images/headers/starkers-thumbnail.png',
			/* translators: header image description */
			'description' => __( 'Boilerplate', 'boilerplate' )
		)
	) );
}
endif;

if ( ! function_exists( 'boilerplate_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in boilerplate_setup().
 *
 * @since Twenty Ten 1.0
 */
function boilerplate_admin_header_style() {
?>
<style type="text/css">
/* Shows the same border as on front end */
#headimg {
	border-bottom: 1px solid #000;
	border-top: 4px solid #000;
}
/* If NO_HEADER_TEXT is false, you would style the text with these selectors:
	#headimg #name { }
	#headimg #desc { }
*/
</style>
<?php
}
endif;

/**
 * Makes some changes to the <title> tag, by filtering the output of wp_title().
 *
 * If we have a site description and we're viewing the home page or a blog posts
 * page (when using a static front page), then we will add the site description.
 *
 * If we're viewing a search result, then we're going to recreate the title entirely.
 * We're going to add page numbers to all titles as well, to the middle of a search
 * result title and the end of all other titles.
 *
 * The site title also gets added to all titles.
 *
 * @since Twenty Ten 1.0
 *
 * @param string $title Title generated by wp_title()
 * @param string $separator The separator passed to wp_title(). Twenty Ten uses a
 * 	vertical bar, "|", as a separator in header.php.
 * @return string The new title, ready for the <title> tag.
 */
function boilerplate_filter_wp_title( $title, $separator ) {
	// Don't affect wp_title() calls in feeds.
	if ( is_feed() )
		return $title;

	// The $paged global variable contains the page number of a listing of posts.
	// The $page global variable contains the page number of a single post that is paged.
	// We'll display whichever one applies, if we're not looking at the first page.
	global $paged, $page;

	if ( is_search() ) {
		// If we're a search, let's start over:
		$title = sprintf( __( 'Search results for %s', 'boilerplate' ), '"' . get_search_query() . '"' );
		// Add a page number if we're on page 2 or more:
		if ( $paged >= 2 )
			$title .= " $separator " . sprintf( __( 'Page %s', 'boilerplate' ), $paged );
		// Add the site name to the end:
		$title .= " $separator " . get_bloginfo( 'name', 'display' );
		// We're done. Let's send the new title back to wp_title():
		return $title;
	}

	// Otherwise, let's start by adding the site name to the end:
	return $title;

	// If we have a site description and we're on the home/front page, add the description:
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $separator " . $site_description;

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $separator " . sprintf( __( 'Page %s', 'boilerplate' ), max( $paged, $page ) );

	// Return the new title to wp_title():
	return $title;
}
add_filter( 'wp_title', 'boilerplate_filter_wp_title', 10, 2 );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * To override this in a child theme, remove the filter and optionally add
 * your own function tied to the wp_page_menu_args filter hook.
 *
 * @since Twenty Ten 1.0
 */
function boilerplate_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'boilerplate_page_menu_args' );

/**
 * Sets the post excerpt length to 40 characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 * @since Twenty Ten 1.0
 * @return int
 */
function boilerplate_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'boilerplate_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @since Twenty Ten 1.0
 * @return string "Continue Reading" link
 */
function boilerplate_continue_reading_link() {
	return '<br /><a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'boilerplate' ) . '</a><br />';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and boilerplate_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string An ellipsis
 */
function boilerplate_auto_excerpt_more( $more ) {
	return ' &hellip;' . boilerplate_continue_reading_link();
}
add_filter( 'excerpt_more', 'boilerplate_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function boilerplate_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= boilerplate_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'boilerplate_custom_excerpt_more' );

/**
 * Remove inline styles printed when the gallery shortcode is used.
 *
 * Galleries are styled by the theme in Twenty Ten's style.css.
 *
 * @since Twenty Ten 1.0
 * @return string The gallery style filter, with the styles themselves removed.
 */
function boilerplate_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'boilerplate_remove_gallery_css' );

if ( ! function_exists( 'boilerplate_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own boilerplate_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Ten 1.0
 */
function boilerplate_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>">
			<div class="comment-author vcard">
				<?php echo get_avatar( $comment, 40 ); ?>
				<?php printf( __( '%s <span class="says">says:</span>', 'boilerplate' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
			</div><!-- .comment-author .vcard -->
			<?php if ( $comment->comment_approved == '0' ) : ?>
				<em><?php _e( 'Your comment is awaiting moderation.', 'boilerplate' ); ?></em>
				<br />
			<?php endif; ?>
			<footer class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
				<?php
					/* translators: 1: date, 2: time */
					printf( __( '%1$s at %2$s', 'boilerplate' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'boilerplate' ), ' ' );
				?>
			</footer><!-- .comment-meta .commentmetadata -->
			<div class="comment-body"><?php comment_text(); ?></div>
			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-##  -->
	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'boilerplate' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'boilerplate'), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 * To override boilerplate_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @since Twenty Ten 1.0
 * @uses register_sidebar
 */
function boilerplate_widgets_init() {
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'boilerplate' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', 'boilerplate' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name' => __( 'Secondary Widget Area', 'boilerplate' ),
		'id' => 'secondary-widget-area',
		'description' => __( 'The secondary widget area', 'boilerplate' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 3, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'First Footer Widget Area', 'boilerplate' ),
		'id' => 'first-footer-widget-area',
		'description' => __( 'The first footer widget area', 'boilerplate' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 4, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Second Footer Widget Area', 'boilerplate' ),
		'id' => 'second-footer-widget-area',
		'description' => __( 'The second footer widget area', 'boilerplate' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 5, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Third Footer Widget Area', 'boilerplate' ),
		'id' => 'third-footer-widget-area',
		'description' => __( 'The third footer widget area', 'boilerplate' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 6, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Fourth Footer Widget Area', 'boilerplate' ),
		'id' => 'fourth-footer-widget-area',
		'description' => __( 'The fourth footer widget area', 'boilerplate' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
/** Register sidebars by running boilerplate_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'boilerplate_widgets_init' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 *
 * To override this in a child theme, remove the filter and optionally add your own
 * function tied to the widgets_init action hook.
 *
 * @since Twenty Ten 1.0
 */
function boilerplate_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'boilerplate_remove_recent_comments_style' );

if ( ! function_exists( 'boilerplate_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post—date/time and author.
 *
 * @since Twenty Ten 1.0
 */
function boilerplate_posted_on() {
	printf( __( '<span class="%1$s">Posted on</span> %2$s', 'boilerplate' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		)
	);
}
endif;

if ( ! function_exists( 'boilerplate_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since Twenty Ten 1.0
 */
function boilerplate_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'boilerplate' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'boilerplate' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'boilerplate' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;

/*	Begin Boilerplate */
	// Add Admin
		require_once(TEMPLATEPATH . '/boilerplate-admin/admin-menu.php');

	// remove version info from head and feeds (http://digwp.com/2009/07/remove-wordpress-version-number/)
		function boilerplate_complete_version_removal() {
			return '';
		}
		add_filter('the_generator', 'boilerplate_complete_version_removal');
/*	End Boilerplate */

// add category nicenames in body and post class
	function boilerplate_category_id_class($classes) {
	    global $post;
	    foreach((get_the_category($post->ID)) as $category)
	        $classes[] = $category->category_nicename;
	        return $classes;
	}
	add_filter('post_class', 'boilerplate_category_id_class');
	add_filter('body_class', 'boilerplate_category_id_class');

// change Search Form input type from "text" to "search" and add placeholder text
	function boilerplate_search_form ( $form ) {
		$form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
		<div><label class="screen-reader-text" for="s">' . __('Search for:') . '</label>
		<input type="search" placeholder="Search for..." value="' . get_search_query() . '" name="s" id="s" />
		<input type="submit" id="searchsubmit" value="'. esc_attr__('Search') .'" />
		</div>
		</form>';
		return $form;
	}
	add_filter( 'get_search_form', 'boilerplate_search_form' );

// added per WP upload process request
if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
}

// [JC_04/23/2012]:  the following function is a hook which fires
//					 after a Formdidable form is submitted.  If the form
//					 is the Job Application, it takes the values, creates
//					 a text file in the given format, and emails it is both a 
//					 message and an attachment to the given email address.
add_filter('frm_after_create_entry', 'after_entry_created', 30, 2);
function after_entry_created($entry_id, $form_id){
	if ($form_id == 22) {
	
		// Form Values
		$appVal179 = $_POST['item_meta'][179]; // position applied for
		$appVal180 = $_POST['item_meta'][180]; // how did you find out about us
		$appVal181 = $_POST['item_meta'][181]; // last name
		$appVal182 = $_POST['item_meta'][182]; // first name
		$appVal183 = $_POST['item_meta'][183]; // middle name
		$appVal184 = $_POST['item_meta'][184]; // address
		$appVal185 = $_POST['item_meta'][185]; // city
		$appVal323 = $_POST['item_meta'][323]; // state
		$appVal324 = $_POST['item_meta'][324]; // zip
		$appVal330 = $_POST['item_meta'][330]; // email
		$appVal186 = $_POST['item_meta'][186]; // daytime phone
		$appVal423 = $_POST['item_meta'][423]; // daytime phone area code
		$appVal187 = $_POST['item_meta'][187]; // evening phone
		$appVal188 = $_POST['item_meta'][188]; // can provide proof to work in US
		$appVal189 = $_POST['item_meta'][189]; // ever filled out app with MERS
		$appVal191 = $_POST['item_meta'][191]; // if yes, date
		$appVal190 = $_POST['item_meta'][190]; // ever been employed by MERS
		$appVal192 = $_POST['item_meta'][192]; // if yes, date
		$appVal193 = $_POST['item_meta'][193]; // may we contact present employer
		$appVal194 = $_POST['item_meta'][194]; // days, hours, etc. not available for work
		$appVal195 = $_POST['item_meta'][195]; // are you available to work		
		$appVal196 = $_POST['item_meta'][196]; // can perform essential function of job
		$appVal197 = $_POST['item_meta'][197]; // can travel between locations
		$appVal200 = $_POST['item_meta'][200]; // out of town travel
		$appVal201 = $_POST['item_meta'][201]; // ever been convicted of felony
		$appVal206 = $_POST['item_meta'][206]; // if yes, date
		$appVal199 = $_POST['item_meta'][199]; // had an SIS sentence
		$appVal203 = $_POST['item_meta'][203]; // if yes, please explain
		$appVal205 = $_POST['item_meta'][205]; // have a pending felony case
		$appVal204 = $_POST['item_meta'][204]; // if yes, please explain
		$appVal202 = $_POST['item_meta'][202]; // list relatives, friends at MERS
		$appVal211 = $_POST['item_meta'][211]; // employer 1 name
		$appVal215 = $_POST['item_meta'][215]; // employer 1 phone
		$appVal216 = $_POST['item_meta'][216]; // employer 1 work dates
		$appVal217 = $_POST['item_meta'][217]; // employer 1 job title
		$appVal218 = $_POST['item_meta'][218]; // employer 1 supervisor
		$appVal219 = $_POST['item_meta'][219]; // employer 1 hourly rate/salary
		$appVal220 = $_POST['item_meta'][220]; // employer 1 job duties
		$appVal221 = $_POST['item_meta'][221]; // employer 1 reason leaving
		$appVal222 = $_POST['item_meta'][222]; // employer 2 name
		$appVal224 = $_POST['item_meta'][224]; // employer 2 work dates
		$appVal226 = $_POST['item_meta'][226]; // employer 2 job title
		$appVal227 = $_POST['item_meta'][227]; // employer 2 supervisor
		$appVal228 = $_POST['item_meta'][228]; // employer 2 hourly rate/salary
		$appVal229 = $_POST['item_meta'][229]; // employer 2 job duties
		$appVal231 = $_POST['item_meta'][231]; // employer 2 reason leaving
		$appVal232 = $_POST['item_meta'][232]; // employer 3 name
		$appVal233 = $_POST['item_meta'][233]; // employer 3 work dates
		$appVal235 = $_POST['item_meta'][235]; // employer 3 job title
		$appVal237 = $_POST['item_meta'][237]; // employer 3 supervisor
		$appVal238 = $_POST['item_meta'][238]; // employer 3 hourly rate/salary
		$appVal239 = $_POST['item_meta'][239]; // employer 3 job duties
		$appVal241 = $_POST['item_meta'][241]; // employer 3 reason leaving
		$appVal246 = $_POST['item_meta'][246]; // employer 4 name
		$appVal247 = $_POST['item_meta'][247]; // employer 4 work dates
		$appVal249 = $_POST['item_meta'][249]; // employer 4 job title
		$appVal250 = $_POST['item_meta'][250]; // employer 4 supervisor
		$appVal251 = $_POST['item_meta'][251]; // employer 4 hourly rate/salary
		$appVal252 = $_POST['item_meta'][252]; // employer 4 job duties
		$appVal253 = $_POST['item_meta'][253]; // employer 4 reason leaving
		$appVal254 = $_POST['item_meta'][254]; // employer 5 name
		$appVal255 = $_POST['item_meta'][255]; // employer 5 work dates
		$appVal256 = $_POST['item_meta'][256]; // employer 5 job title
		$appVal257 = $_POST['item_meta'][257]; // employer 5 supervisor
		$appVal258 = $_POST['item_meta'][258]; // employer 5 hourly rate/salary
		$appVal259 = $_POST['item_meta'][259]; // employer 5 job duties
		$appVal260 = $_POST['item_meta'][260]; // employer 5 reason leaving
		$appVal325 = $_POST['item_meta'][325]; // education drop down
		$appVal265 = $_POST['item_meta'][265]; // have graduated high school or GED
		$appVal266 = $_POST['item_meta'][266]; // high school or GED diploma		
		$appVal267 = $_POST['item_meta'][267]; // high school courses studied
		$appVal269 = $_POST['item_meta'][269]; // have graduated with assoc. degree
		$appVal270 = $_POST['item_meta'][270]; // assoc. degree diploma
		$appVal271 = $_POST['item_meta'][271]; // assoc. degree courses studied
		$appVal273 = $_POST['item_meta'][273]; // have graduated with undergrad degree
		$appVal274 = $_POST['item_meta'][274]; // undergrad diploma
		$appVal275 = $_POST['item_meta'][275]; // undergrad courses studied
		$appVal277 = $_POST['item_meta'][277]; // have graduated with graduate degree
		$appVal278 = $_POST['item_meta'][278]; // graduate diploma
		$appVal279 = $_POST['item_meta'][279]; // graduate courses studied
		$appVal281 = $_POST['item_meta'][281]; // any other school, training, etc.
		$appVal282 = $_POST['item_meta'][282]; // any additional work related
		$appVal285 = $_POST['item_meta'][285]; // reference 1 name
		$appVal286 = $_POST['item_meta'][286]; // reference 1 address
		$appVal287 = $_POST['item_meta'][287]; // reference 1 phone
		$appVal289 = $_POST['item_meta'][289]; // reference 2 name
		$appVal290 = $_POST['item_meta'][290]; // reference 2 address
		$appVal291 = $_POST['item_meta'][291]; // reference 2 phone
		$appVal292 = $_POST['item_meta'][292]; // reference 3 name
		$appVal293 = $_POST['item_meta'][293]; // reference 3 address
		$appVal294 = $_POST['item_meta'][294]; // reference 3 phone
		$appVal300 = $_POST['item_meta'][300]; // agree with employment conditions
		$appVal417 = $_POST['item_meta'][417]; // employer 2 phone number
		$appVal418 = $_POST['item_meta'][418]; // employer 3 phone number
		$appVal421 = $_POST['item_meta'][421]; // employer 4 phone number
		$appVal422 = $_POST['item_meta'][422]; // employer 5 phone number		
				
		$chrRtrn = "\r\n";
		$dblChrRtrn = "\r\n\r\n";
		
		// Basic Information
		$theMsg .= "Education Level:" . $dblChrRtrn . $appVal325 . $dblChrRtrn;
		$theMsg .= "Position(s) Applied For:" . $dblChrRtrn . $appVal179 . $dblChrRtrn;
		$theMsg .= "Last Name:" . $dblChrRtrn . $appVal181 . $dblChrRtrn;
		$theMsg .= "First Name:" . $dblChrRtrn . $appVal182 . $dblChrRtrn;		
		$theMsg .= "Daytime Phone Area Code:" . $dblChrRtrn . $appVal423 . $dblChrRtrn;		
		$theMsg .= "5 digit Zip Code:" . $dblChrRtrn . $appVal324 . $chrRtrn;
		$theMsg .= $dblChrRtrn;		
		$theMsg .= "Position(s) Applied for:  " . $appVal179 . $chrRtrn;							
		$theMsg .= "Date of Application:  " . date("F j, Y") . $chrRtrn;	
								
		$findOutAboutUs = "";
		for ($i=0; $i<count($appVal180); $i++){ 
			$findOutAboutUs = ($findOutAboutUs == "") ? $appVal180[$i] : ($findOutAboutUs .= ", " . $appVal180[$i]);
		}
		$theMsg .= "How did you find out about us?  " . $findOutAboutUs . $chrRtrn;
		
		$theMsg .= "Last Name:  " . $appVal181 . $chrRtrn;							
		$theMsg .= "First Name:  " . $appVal182 . $chrRtrn;							
		$theMsg .= "Middle Name:  " . $appVal183 . $chrRtrn;
		$theMsg .= "Address:  " . $appVal184 . $chrRtrn;
		$theMsg .= "City/ State/ Zip:  " . $appVal185 . "/ " . $appVal323 . "/ " . $appVal324 . $chrRtrn;
		$theMsg .= "Email:  " . $appVal330 . $chrRtrn;				
		$theMsg .= "Daytime Phone:  " . $appVal423 . " " . $appVal186 . $chrRtrn;				
		$theMsg .= "Evening Phone:  " . $appVal187 . $chrRtrn;				
		$theMsg .= "Have you ever filled out an application with us before?  " . $appVal189 . $chrRtrn;				
		$theMsg .= "If yes, date:  " .  $appVal191 . $chrRtrn;
		$theMsg .= "Have you ever been employed with us before?  " . $appVal190 . $chrRtrn;				
		$theMsg .= "If yes, date:  " . $appVal192 . $chrRtrn;											
		$theMsg .= "May we contact your present employer?  " . $appVal193 . $chrRtrn;
		$theMsg .= "Are there any days or hours you are not available to work?  " . $appVal194 . $chrRtrn;
		
		
		
		$avail = "";
		for ($i=0; $i<count($appVal195); $i++){ 
			$avail = ($avail == "") ? $appVal195[$i] : ($avail .= ", " . $appVal195[$i]);
		}
		
		$theMsg .= "Are you available to work:  " . $avail . $chrRtrn;
		$theMsg .= "Can you perform the essential functions of the job you are applying for?  " . $appVal196 . $chrRtrn;
		$theMsg .= "Can you travel between locations?  " . $appVal197 . $chrRtrn;
		$theMsg .= "Out-of-town?" . $appVal200 . $chrRtrn;
		$theMsg .= "Have you ever been convicted of a felony?  " . $appVal201 . $chrRtrn;
		$theMsg .= "If yes, date:  " . $appVal206 . $chrRtrn;
		$theMsg .= "Had an SIS Sentence?  " . $appVal199 . $chrRtrn;
		$theMsg .= "If yes, please explain:  " . $appVal203 . $chrRtrn;
		$theMsg .= "Have a pending felony case(s)?  " . $appVal205 . $chrRtrn;
		$theMsg .= "If yes, please explain:  " . $appVal204 . $chrRtrn;		
		$theMsg .= "Please list and current relatives of friends employed with MERS Goodwill:  " . $appVal202 . $chrRtrn;		
		
		// Employer 1
		$theMsg .= "Employer 1 ..." . $chrRtrn;		
		$theMsg .= "Employer:  " . $appVal211. $chrRtrn;		
		$theMsg .= "Employer's Phone Number:  " . $appVal215 . $chrRtrn;
		$theMsg .= "Work Dates From:  " . $appVal216 . $chrRtrn;	
		$theMsg .= "Job Title:  " . $appVal217 . $chrRtrn;
		$theMsg .= "Supervisor:  " . $appVal218 . $chrRtrn;		
		$theMsg .= "Hourly Rate/Salary Starting:  " . $appVal219 . $chrRtrn;			
		$theMsg .= "Job Duties:  " . $appVal220 . $chrRtrn;		
		$theMsg .= "Reason for leaving:  " . $appVal221 . $chrRtrn;		
		
		// Employer 2
		$theMsg .= "Employer 2 ..." . $chrRtrn;		
		$theMsg .= "Employer:  " . $appVal222 . $chrRtrn;		
		$theMsg .= "Employer's Phone Number:  " . $appVal417 . $chrRtrn;
		$theMsg .= "Work Dates From:  " . $appVal224 . $chrRtrn;
		$theMsg .= "Job Title:  " . $appVal226 . $chrRtrn;
		$theMsg .= "Supervisor:  " . $appVal227 . $chrRtrn;		
		$theMsg .= "Hourly Rate/Salary Starting:  " . $appVal228 . $chrRtrn;		
		$theMsg .= "Job Duties:  " . $appVal229 . $chrRtrn;		
		$theMsg .= "Reason for leaving:  " . $appVal231 . $chrRtrn;
		
		// Employer 3
		$theMsg .= "Employer 3 ..." . $chrRtrn;		
		$theMsg .= "Employer:  " . $appVal232 . $chrRtrn;		
		$theMsg .= "Employer's Phone Number:  " . $appVal418 . $chrRtrn;
		$theMsg .= "Work Dates From:  " . $appVal233 . $chrRtrn;
		$theMsg .= "Job Title:  " . $appVal235 . $chrRtrn;
		$theMsg .= "Supervisor:  " . $appVal237 . $chrRtrn;		
		$theMsg .= "Hourly Rate/Salary Starting:  " . $appVal238 . $chrRtrn;			
		$theMsg .= "Job Duties:  " . $appVal239 . $chrRtrn;		
		$theMsg .= "Reason for leaving:  " . $appVal241 . $chrRtrn;
		
		// Employer 4
		$theMsg .= "Employer 4 ..." . $chrRtrn;		
		$theMsg .= "Employer:  " . $appVal246 . $chrRtrn;		
		$theMsg .= "Employer's Phone Number:  " . $appVal421 . $chrRtrn;
		$theMsg .= "Work Dates From:  " . $appVal247 . $chrRtrn;
		$theMsg .= "Job Title:  " . $appVal249 . $chrRtrn;
		$theMsg .= "Supervisor:  " . $appVal250 . $chrRtrn;		
		$theMsg .= "Hourly Rate/Salary Starting:  " . $appVal251 . $chrRtrn;			
		$theMsg .= "Job Duties:  " . $appVal252 . $chrRtrn;		
		$theMsg .= "Reason for leaving:  " . $appVal253 . $chrRtrn;
		
		// Employer 5
		$theMsg .= "Employer 5 ..." . $chrRtrn;		
		$theMsg .= "Employer:  " . $appVal254 . $chrRtrn;		
		$theMsg .= "Employer's Phone Number:  " . $appVal422 . $chrRtrn;
		$theMsg .= "Work Dates From:  " . $appVal255 . $chrRtrn;
		$theMsg .= "Job Title:  " . $appVal256 . $chrRtrn;
		$theMsg .= "Supervisor:  " . $appVal257 . $chrRtrn;		
		$theMsg .= "Hourly Rate/Salary Starting:  " . $appVal258 . $chrRtrn;			
		$theMsg .= "Job Duties:  " . $appVal259 . $chrRtrn;		
		$theMsg .= "Reason for leaving:  " . $appVal260 . $chrRtrn;

		// Graduate Highschool Degree
		$theMsg .= "Did you Graduate from Highschool?  " . $appVal265 . $chrRtrn;		
		$theMsg .= "If No, did you get your GED or equivalent Diploma?  " . $appVal266 . $chrRtrn;
		$theMsg .= "Courses Studied:  " . $appVal267 . $chrRtrn;

		// Graduate with Associates Degree
		$theMsg .= "Did you Graduate with an Associates Degree?  " . $appVal269 . $chrRtrn;		
		$theMsg .= "Did you obtain your Diploma?  " . $appVal270 . $chrRtrn;
		$theMsg .= "Courses Studied:  " . $appVal271 . $chrRtrn;

		// Graduate with Undergraduate Degree
		$theMsg .= "Did you Graduate with an Undergraduate Degree?  " . $appVal273 . $chrRtrn;		
		$theMsg .= "Did you obtain your Diploma?  " . $appVal274 . $chrRtrn;
		$theMsg .= "Courses Studied:  " . $appVal275 . $chrRtrn;

		// Graduate with Graduate Degree
		$theMsg .= "Did you Graduate with an Graduate Degree?  " . $appVal277 . $chrRtrn;		
		$theMsg .= "Did you obtain your Diploma?  " . $appVal278 . $chrRtrn;
		$theMsg .= "Courses Studied:  " . $appVal279 . $chrRtrn;

		$theMsg .= "List any other school, training, etc:  " . $appVal281 . $chrRtrn;
		$theMsg .= "State any additional work related info which would be helpful:  " . $appVal282 . $chrRtrn;
		
		// Reference 1
		$theMsg .= "Reference 1 ..." . $chrRtrn;
		$theMsg .= "Name:  " . $appVal285 . $chrRtrn;
		$theMsg .= "Address:  " . $appVal286 . $chrRtrn;
		$theMsg .= "Phone:  " . $appVal287 . $chrRtrn;		

		// Reference 2
		$theMsg .= "Reference 2 ..." . $chrRtrn;
		$theMsg .= "Name:  " . $appVal289 . $chrRtrn;
		$theMsg .= "Address:  " . $appVal290 . $chrRtrn;
		$theMsg .= "Phone:  " . $appVal291 . $chrRtrn;		

		// Reference 3
		$theMsg .= "Reference 3 ..." . $chrRtrn;
		$theMsg .= "Name:  " . $appVal292 . $chrRtrn;
		$theMsg .= "Address:  " . $appVal293 . $chrRtrn;
		$theMsg .= "Phone:  " . $appVal294 . $chrRtrn;		
		
		$theMsg .= "Agree with Conditions of Employment?  " . $appVal300 . $chrRtrn;
									
		$newFileName = date("Ymd_s");	
			
		$ourFileName = WP_CONTENT_DIR . "/uploads/formidable/applications/" . $newFileName . ".txt";		
		$ourFileHandle = fopen($ourFileName, 'w') or die("Error:  Cannot Open File");
		$stringData = $theMsg . "\n";
		fwrite($ourFileHandle, $stringData);		
		fclose($ourFileHandle);
		
		$attachment = $ourFileName;
		
		$theHeaders = "From: isupport@mersgoodwill.org\r\n";  		
		wp_mail('VCAROTHERS@MERSGOODWILL.ORG', "New Job Application", $theMsg, $theHeaders, $attachment);
		//wp_mail('aj@goelastic.com', "New Job Application", $theMsg, $theHeaders, $attachment);  --Elastic Testing

	}
}

?>
