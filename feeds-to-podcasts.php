<?php
/**
 * Plugin Name: Feeds To Podcasts
 * Plugin URI:  https://github.com/yknivag/WPFeedsToPodcasts
 * Description: WordPress plugin to turn standard WordPress feeds into iTunes compatible podcasts
 * Version:     0.0.1
 * Author:      yknivag
 * License:     LGPL2.1
 * License URI: https://www.gnu.org/licenses/lgpl-2.1.en.html
 * Text Domain: wpfeedstopodcasts
 */

//  If accessed directly, abort
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

// Initialization of the plugin function
if ( ! function_exists ( 'wpfeedstopodcasts_plugin_init' ) ) {
	function wpfeedstopodcasts_plugin_init() {
		global $wpfeedstopodcasts_options;
		// Internationalization, first(!)
		load_plugin_textdomain( 'wpfeedstopodcasts', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
		if ( ! is_admin() || ( is_admin() && isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wpfeedstopodcasts_plugin' ) ) {
			wpfeedstopodcasts_register_settings();
		}
	}
}

//// Adding admin plugin settings page function
//if ( ! function_exists( 'add_wpfeedstopodcasts_admin_menu' ) ) {
//	function wpfeedstopodcasts_add_admin_menu() {
//		add_menu_page( __( 'Feeds To Podcasts', 'wpfeedstopodcasts' ), __( 'Feeds To Podcasts', 'wpfeedstopodcasts' ), 'manage_options', 'wpfeedstopodcasts_plugin', 'wpfeedstopodcasts_settings_page', 'dashicons-networking');
//		//call register settings function
//	}
//}

// Adding admin plugin settings page function
if ( ! function_exists( 'add_wpfeedstopodcasts_admin_menu' ) ) {
	function wpfeedstopodcasts_register_options_page() {
		add_options_page( __( 'Feeds To Podcasts', 'wpfeedstopodcasts' ), __( 'Feeds To Podcasts', 'wpfeedstopodcasts' ), 'manage_options', 'wpfeedstopodcasts_plugin', 'wpfeedstopodcasts_options_page');
		//call register settings function
	}
}

// Plugin Deactivation Function
if ( ! function_exists( 'add_wpfeedstopodcasts_admin_menu' ) ) {
	function add_wpfeedstopodcasts_admin_menu() {
		// Nothing to do here.
	}
}
register_deactivation_hook( __FILE__, 'wpfeedstopodcasts_deactivate' );

// Plugin Delete Function
if ( ! function_exists( 'add_wpfeedstopodcasts_admin_menu' ) ) {
	function add_wpfeedstopodcasts_admin_menu() {
		delete_option( 'wpfeedstopodcasts_itunes_category' );
		delete_option( 'wpfeedstopodcasts_itunes_explicit' );
		delete_option( 'wpfeedstopodcasts_itunes_logo' );
		delete_option( 'wpfeedstopodcasts_copyright' );
	}
}
register_uninstall_hook( __FILE__, 'iaml_delete' );

// Initialization plugin settings function
if ( ! function_exists( 'wpfeedstopodcasts_register_settings' ) ) {
	function wpfeedstopodcasts_register_settings() {
		global $wpdb, $wpfeedstopodcasts_options;
		$wpfeedstopodcasts_option_defaults = array(
			'wpfeedstopodcasts_itunes_category' => 'News',
			'wpfeedstopodcasts_itunes_explicit' => 'no',
			'wpfeedstopodcasts_itunes_logo'     => '',
			'wpfeedstopodcasts_copyright'       => 'Copyright' . get_bloginfo( 'name' )
		);
		// install the option defaults
		if ( is_multisite() ) {
			if ( ! get_site_option( 'wpfeedstopodcasts_options' ) ) {
				add_site_option( 'wpfeedstopodcasts_options', $wpfeedstopodcasts_option_defaults, '', 'yes' );
			}
		} else {
			if ( ! get_option( 'wpfeedstopodcasts_options' ) )
				add_option( 'wpfeedstopodcasts_options', $wpfeedstopodcasts_option_defaults, '', 'yes' );
		}
		// get options from the database
		if ( is_multisite() )
			$wpfeedstopodcasts_options = get_site_option( 'wpfeedstopodcasts_options' ); // get options from the database
		else
			$wpfeedstopodcasts_options = get_option( 'wpfeedstopodcasts_options' );// get options from the database
		// array merge incase this version has added new options
		$wpfeedstopodcasts_options = array_merge( $wpfeedstopodcasts_option_defaults, $wpfeedstopodcasts_options );
		update_option( 'wpfeedstopodcasts_options', $wpfeedstopodcasts_options );
	}
}
// Admin plugin settings page content function
if ( ! function_exists( 'wpfeedstopodcasts_options_page' ) ) {
	function wpfeedstopodcasts_options_page() {
	    	    
		global $wpfeedstopodcasts_options;
		$message = '';
		if( isset( $_POST['wpfeedstopodcasts_submit'] ) && check_admin_referer( plugin_basename(__FILE__), 'wpfeedstopodcasts_nonce_name' ) ) {
                    
            //Save options
            $wpfeedstopodcasts_options[ 'wpfeedstopodcasts_itunes_category' ] = sanitize_text_field( trim( $_POST[ 'wpfeedstopodcasts_itunes_category' ] ) );
            $wpfeedstopodcasts_options[ 'wpfeedstopodcasts_itunes_explicit' ] = sanitize_text_field( trim( $_POST[ 'wpfeedstopodcasts_itunes_explicit' ] ) );
            $wpfeedstopodcasts_options[ 'wpfeedstopodcasts_itunes_logo' ] 	  = sanitize_text_field( trim( $_POST[ 'wpfeedstopodcasts_itunes_logo' ] ) );
            $wpfeedstopodcasts_options[ 'wpfeedstopodcasts_copyright' ]       = sanitize_text_field( trim( $_POST[ 'wpfeedstopodcasts_copyright' ] ) );

            $message = __( 'Settings Saved' , 'wpfeedstopodcasts' );
            update_option( 'wpfeedstopodcasts_options', $wpfeedstopodcasts_options );
		}
                
                
        //$wpfeedstopodcasts_options = get_option('wpfeedstopodcasts_options');
        $wpfeedstopodcasts_itunes_category = isset( $wpfeedstopodcasts_options[ 'wpfeedstopodcasts_itunes_category' ] ) ? $wpfeedstopodcasts_options[ 'wpfeedstopodcasts_itunes_category' ]  : 'News';
        $wpfeedstopodcasts_itunes_explicit = isset( $wpfeedstopodcasts_options[ 'wpfeedstopodcasts_itunes_explicit' ] ) ? $wpfeedstopodcasts_options[ 'wpfeedstopodcasts_itunes_explicit' ]  : 'no';
        $wpfeedstopodcasts_itunes_logo     = isset( $wpfeedstopodcasts_options[ 'wpfeedstopodcasts_itunes_logo' ] )     ? $wpfeedstopodcasts_options[ 'wpfeedstopodcasts_itunes_logo' ]      : '';
        $wpfeedstopodcasts_copyright       = isset( $wpfeedstopodcasts_options[ 'wpfeedstopodcasts_copyright' ] )       ? $wpfeedstopodcasts_options[ 'wpfeedstopodcasts_copyright' ]        : 'Copyright ' . get_bloginfo( 'name' );
               
		?>
		<div class="wrap">
		    <h2><?php esc_html_e( 'Feeds To Podcasts', 'wpfeedstopodcasts' ); ?></h2>
		    
		    <div id="poststuff"><div id="post-body">			
			<?php if ( $message != '' && isset( $_POST[ 'wpfeedstopodcasts_submit' ] ) ) { ?>
				<div class="updated fade">
					<p><strong><?php echo esc_html( $message ); ?></strong></p>
				</div>
			<?php } ?>
                        
			<div class="postbox">
    			<h3 class="hndle"><label for="title"><?php esc_html_e( 'Quick Usage Guide', 'wpfeedstopodcasts' ); ?></label></h3>
    			<div class="inside">
    			    <div class="wpfeedstopodcasts_info_block">
						<h4><?php esc_html_e( 'Purpose', 'wpfeedstopodcasts' ); ?></h4>
    				    	<p><?php esc_html_e( 'The plugin changes the standard RSS2 feed so that the link each feed points back to the human readable page of the same content.  It also adds the minimally required iTunes tags with options to set the iTunes category, logo and also a copyright statement or link.', 'wpfeedstopodcasts' ); ?></p>
							<p><?php esc_html_e( 'The plugin is deliberately designed to provide a minimalistic way of supporting iTunes, Google and Spotify requirements from standard WP feeds.  It deliberately does nothing more.  There are many very good full-featured podcast plugins with which this is not designed to compete.', 'wpfeedstopodcasts' ); ?></p>
    			    </div>
    			</div>
			</div>
			
			<div class="postbox">
    			<h3 class="hndle"><label for="title"><?php esc_html_e( 'Plugin Settings', 'wpfeedstopodcasts' ); ?></label></h3>
    			<div class="inside">			
        			<form id="wpfeedstopodcasts_settings_form" method='post' action=''>
        			    <fieldset>
        			        <legend><?php esc_html_e( 'Settings', 'wpfeedstopodcasts' ); ?></legend>
							<p><?php esc_html_e( 'These options apply the same to all feeds.', 'wpfeedstopodcasts' ); ?></p>
							<label>
        				        <?php esc_html_e( 'iTunes Category', 'wpfeedstopodcasts' ); ?>
        				        <input type='text' name='wpfeedstopodcasts_itunes_category' size='70' id='wpfeedstopodcasts_itunes_category' value="<?php if ( '' != $wpfeedstopodcasts_options['wpfeedstopodcasts_itunes_category'] ) echo esc_html( $wpfeedstopodcasts_options['wpfeedstopodcasts_itunes_category'] ); ?>" placeholder ="Category" />
        				    </label><br />
        				    <label>
        				        <?php esc_html_e( 'iTunes Explicit', 'wpfeedstopodcasts' ); ?>
        				        <select id="wpfeedstopodcasts_itunes_explicit" name="wpfeedstopodcasts_itunes_explicit">
                                    <option value="no" <?php echo ($wpfeedstopodcasts_itunes_explicit == 'no') ? 'selected="selected"' : ''; ?>>No</option>
                                    <option value="yes" <?php echo ($wpfeedstopodcasts_itunes_explicit == 'yes') ? 'selected="selected"' : ''; ?>>Yes</option>
                                </select>
        				    </label><br />
        				    <label>
        				        <?php esc_html_e( 'URL of iTunes Logo', 'wpfeedstopodcasts' ); ?> (<?php esc_html_e( 'Logo should be 1500x1500px', 'wpfeedstopodcasts' ); ?>)
        				        <input type='text' name='wpfeedstopodcasts_itunes_logo' size='70' id='wpfeedstopodcasts_itunes_logo' value="<?php if ( '' != $wpfeedstopodcasts_options['wpfeedstopodcasts_itunes_logo'] ) echo esc_html( $wpfeedstopodcasts_options['wpfeedstopodcasts_itunes_logo'] ); ?>" placeholder ="<?php esc_html_e( 'URL to large logo', 'wpfeedstopodcasts' ); ?>" />
        				    </label><br />
							<label>
        				        <?php esc_html_e( 'Copyright', 'wpfeedstopodcasts' ); ?> (<?php esc_html_e( 'Maybe a statement or a URL', 'wpfeedstopodcasts' ); ?>)
        				        <input type='text' name='wpfeedstopodcasts_copyright' size='70' id='wpfeedstopodcasts_copyright' value="<?php if ( '' != $wpfeedstopodcasts_options['wpfeedstopodcasts_copyright'] ) echo esc_html( $wpfeedstopodcasts_options['wpfeedstopodcasts_copyright'] ); ?>" placeholder ="<?php esc_html_e( 'Copyright', 'wpfeedstopodcasts' ); ?>" />
        				    </label><br />
                        </fieldset>
                        <p><input type="submit" name="wpfeedstopodcasts_submit" value="<?php esc_html_e( 'Save Changes', 'wpfeedstopodcasts' ); ?>" class="button-primary" /></p>
                        <?php wp_nonce_field( plugin_basename( __FILE__ ), 'wpfeedstopodcasts_nonce_name' ); ?>
                    </form>
    			</div>
			</div>
			
		    </div></div><!-- End of poststuff and postbody -->
		
		</div><!-- end of wrap -->
	<?php 
	}
}

function wpfeedstopodcasts_feed_rss_ns_changes() {
    echo 'xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"';
}
add_action( 'rss2_ns', 'wpfeedstopodcasts_feed_rss_ns_changes', 10, 0 );

function wpfeedstopodcasts_feed_rss_head_changes() {
	$wpfeedstopodcasts_options = get_option('wpfeedstopodcasts_options');

	$wpfeedstopodcasts_itunes_category = $wpfeedstopodcasts_options[ 'wpfeedstopodcasts_itunes_category' ];
	$wpfeedstopodcasts_itunes_explicit = $wpfeedstopodcasts_options[ 'wpfeedstopodcasts_itunes_explicit' ];
	$wpfeedstopodcasts_itunes_logo     = $wpfeedstopodcasts_options[ 'wpfeedstopodcasts_itunes_logo' ];
	$wpfeedstopodcasts_copyright       = $wpfeedstopodcasts_options[ 'wpfeedstopodcasts_copyright' ];

	echo "<itunes:subtitle>" . get_bloginfo( "description" ) . "</itunes:subtitle>\n";
    echo "<itunes:summary>" . get_bloginfo( "description" ) . "</itunes:summary>\n";
    echo "<itunes:author>" . get_bloginfo( "name" ) . "</itunes:author>\n";
    echo "<itunes:owner>\n";
    echo "<itunes:name>" . get_bloginfo( "name" ). "</itunes:name>\n";
    echo "<itunes:email>" . get_bloginfo( "admin_email" ) . "</itunes:email>\n";
    echo "</itunes:owner>\n";
	echo "<itunes:category text=\"" . $wpfeedstopodcasts_itunes_category . "\"/>\n";
	echo "<itunes:explicit>" . $wpfeedstopodcasts_itunes_explicit . "</itunes:explicit>\n";
	echo "<itunes:image href=\"" . $wpfeedstopodcasts_itunes_logo . "\" />\n";
	echo "<copyright>" . $wpfeedstopodcasts_copyright . "</copyright>\n";
}
add_action( 'rss2_head', 'wpfeedstopodcasts_feed_rss_head_changes', 10, 0 );

function wpfeedstopodcasts_feed_rss_item_changes () {
    echo "<itunes:explicit>" . $wpfeedstopodcasts_itunes_explicit . "</itunes:explicit>\n";
	echo "<itunes:summary><![CDATA[";
	the_excerpt_rss();
	echo "]]></itunes:summary>\n";
}
add_action( 'rss2_item', 'wpfeedstopodcasts_feed_rss_item_changes', 10, 0 );

function wpfeedstopodcasts_rss_feed_title_change ( $title ) {
	//Swap the title so that the blog name comes first.
	$title_parts = explode(" &#8211; ", $title);
	$title_part_one = array_shift($title_parts);
	$title_parts[] = $title_part_one;
	$new_title = implode(" &#8211; ", $title_parts);
	return $new_title;
}
add_filter( 'wp_title_rss', 'wpfeedstopodcasts_rss_feed_title_change', 20, 1 );

remove_action( 'do_feed_rss2', 'do_feed_rss2', 10 );

function wpfeedstopodcasts_do_feed_rss2( $for_comments ) {
	if ( $for_comments )
		load_template( ABSPATH . WPINC . '/feed-rss2-comments.php' );
	else
		load_template( plugin_dir_path( __FILE__ ) . 'feed-rss2.php' );
}
add_action( 'do_feed_rss2', 'wpfeedstopodcasts_do_feed_rss2' );

register_activation_hook( __FILE__, 'wpfeedstopodcasts_register_settings' );

add_action( 'init', 'wpfeedstopodcasts_plugin_init' );
add_action( 'admin_init', 'wpfeedstopodcasts_plugin_init' );
//add_action( 'admin_menu', 'wpfeedstopodcasts_add_admin_menu' );
add_action( 'admin_menu', 'wpfeedstopodcasts_register_options_page' );