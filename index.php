<?php
include("yocommerce_widget.class.php");
include("yocommerce_paypalfunctions.php");
/*
Plugin Name: YoCommerce
Plugin URI: https://wordpress.org/plugins/yocommerce
Description: Simple plugin for create internet-shop on your wordpress blog with paypal payments
Version: 1.3
Author: Chugaev Aleksandr Aleksandrovich
Author URI: https://profiles.wordpress.org/aleksandrposs/
*/
 
function yocommerce_install() {  // install plugin
  global $wpdb;
   
  // create table "orders"
 $table = $wpdb->prefix . "plugin_yocommerce_orders";
  if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {	
	$sql = "CREATE TABLE `" . $table . "` (
	  `order_id` int(9) NOT NULL AUTO_INCREMENT,
	  `order_ip_address` VARCHAR(15) NOT NULL,
	  `order_summ` int(9),
          `order_order_id` int(9),
          `order_name` VARCHAR(30),
          `order_email` VARCHAR(30),
          `order_phone` VARCHAR(30),
          `order_address` VARCHAR(120),
	  `order_date` datetime,
	  `order_ok` ENUM('true',''),
      `order_token` VARCHAR(200),
	  UNIQUE KEY `id` (order_id)
	) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;";
	  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	  dbDelta($sql);
 }
 
  // create table "orders"
 $table = $wpdb->prefix . "plugin_yocommerce_goods";
  if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {	
	$sql = "CREATE TABLE `" . $table . "` (
	  `goods_id` int(9) NOT NULL AUTO_INCREMENT,
	  `goods_order_id` int(9) NOT NULL,
	  `goods_goods_id` int(9) NOT NULL,
	  UNIQUE KEY `id` (goods_id)
	) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;";
	  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	  dbDelta($sql);
 }
  // create table "settins 1"
 $table = $wpdb->prefix . "plugin_yocommerce_settings";
  if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {	
	$sql = "CREATE TABLE `" . $table . "` (
	  `setting_id` int(9) NOT NULL AUTO_INCREMENT,
	  `setting_paypal_username` varchar(150) NOT NULL,
	  `setting_paypal_password` varchar(150) NOT NULL,
	  `setting_paypal_signature` varchar(300) NOT NULL,
	  `setting_paypal_sandbox_flag` ENUM('true', 'false') ,
	  UNIQUE KEY `id` (setting_id)
	) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;";
	  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	  dbDelta($sql);
 }
 
   // create table "settings 2"
 $table = $wpdb->prefix . "plugin_yocommerce_settings_2";
  if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {	
	$sql = "CREATE TABLE `" . $table . "` (
	  `setting_id` int(9) NOT NULL AUTO_INCREMENT,
   `setting_theme_color` varchar(150) NOT NULL,
	  UNIQUE KEY `id` (setting_id)
	) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;";
	  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	  dbDelta($sql);
 } 
 
 
}
register_activation_hook( __FILE__,'yocommerce_install');

	

	

function yocommerce_uninstall() { // uninstall plugin
 
 global $wpdb;
 $table = $wpdb->prefix . "plugin_yocommerce_orders";	
 $wpdb->query("DROP TABLE IF EXISTS $table");
 
 $table = $wpdb->prefix . "plugin_yocommerce_goods";	
 $wpdb->query("DROP TABLE IF EXISTS $table");
 
 $table = $wpdb->prefix . "plugin_yocommerce_settings";	
 $wpdb->query("DROP TABLE IF EXISTS $table");
}
register_deactivation_hook( __FILE__,'yocommerce_uninstall');



function paypal_express_checkout(){
		include("yocommerce_paypal_checkout.php");
}

add_action( 'init', 'paypal_express_checkout' );



function yocommerce_show_buy_button($content) {
	
	global $post;
 global $wpdb;
 
   $sql = "SELECT * FROM " . $wpdb->prefix . "plugin_yocommerce_settings_2";  
         $color_scheme = $wpdb->get_results($sql);
         $color_scheme = $color_scheme[0]->setting_theme_color;
	
	$goods_cost = get_post_meta( $post->ID, 'cost',true );
 
        if ($goods_cost != 0) {
	       $content .= 'Cost is ' . $goods_cost . ' $ <br>'; 
        $content .= "<a href=\"?yocommerce_basket_good_id=" . $post->ID . "\">";
        $content .= '<input type="submit" name="submit" id="submit" style="color:' . $color_scheme . ';" class="button button-primary" value="Buy"  />';
        $content .= '</a><br>';
	}
	
    return $content;  
}
add_action('the_content','yocommerce_show_buy_button');


// admin page

function yocommerce_main_page() { // admin page
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<h2>Yo Commerce</h2>';
	                       
    // get list posts
    global $wpdb;
   
    $posts = get_posts();
    foreach ( $posts as $post ) {
       
    }
     
	echo '</div>';
}




/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function yocommerce_add_meta_box() {

	$screens = array( 'post', 'page' );

	foreach ( $screens as $screen ) {

		add_meta_box(
			'yocommerce_sectionid',
			__( 'YoCommerce - Cost', 'myplugin_textdomain' ),
			'yocommerce_meta_box_callback',
			$screen
		);
	}
}
add_action( 'add_meta_boxes', 'yocommerce_add_meta_box' );

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function yocommerce_meta_box_callback( $post ) {

	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'myplugin_save_meta_box_data', 'myplugin_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$value = get_post_meta( $post->ID, 'cost', true );

	echo '<label for="myplugin_new_field">';
	_e( 'Cost for product is ', 'myplugin_textdomain' );
	echo '</label> ';
	echo '<input type="text" id="myplugin_new_field" name="myplugin_new_field" value="' . (int) esc_attr( $value ) . '" size="25" />';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function yocommerce_save_meta_box_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['myplugin_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['myplugin_meta_box_nonce'], 'myplugin_save_meta_box_data' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */
	
	// Make sure that it is set.
	if ( ! isset( $_POST['myplugin_new_field'] ) ) {
		return;
	}

	// Sanitize user input.
	$my_data = sanitize_text_field( $_POST['myplugin_new_field'] );

	// Update the meta field in the database.
	update_post_meta( $post_id, 'cost', $my_data );
}
add_action( 'save_post', 'yocommerce_save_meta_box_data' );




/* WIDGET REGISTER */
// Register and load the widget
function wpb_load_widget() {
	register_widget( 'yocommerce_wpb_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );

/* MAIN */

function yocommerce_new_order_id(){
  return rand(1000,1000000000);
}


// get and posts requests

function yocommerce_get_requests() {
  
    global $wpdb;
    
  
    
    // session_id
	if(!session_id()) {
			session_start();
			if (!isset($_SESSION['yocommerce_order_id']) or $_SESSION['yocommerce_order_id'] == 2) {
                $_SESSION['yocommerce_order_id'] =  yocommerce_new_order_id();
			}
	}
	// new id for second and next orders for one man
	if ($_SESSION['yocommerce_order_id'] == 0) {
                $_SESSION['yocommerce_order_id'] =  yocommerce_new_order_id();
	}
	

	// add goods to basket
	if (isset($_GET['yocommerce_basket_good_id'])) {
		  $basket_good_id = (int) $_GET['yocommerce_basket_good_id'];
		  
	 
		  $wpdb->insert($wpdb->prefix . "plugin_yocommerce_goods", array(
			  "goods_order_id" => $_SESSION['yocommerce_order_id'],
			  "goods_goods_id" => (int) $_GET['yocommerce_basket_good_id'],
		  ));
	  
	}
    //clear basket
	if (isset($_GET['yocommerce_basket']) && $_GET['yocommerce_basket'] == 'clear') {
		   $_SESSION['yocommerce_order_id'] =  yocommerce_new_order_id();
	}
    //after payment going to return URL
	if (isset($_GET['yocommerce_payment']) && $_GET['yocommerce_payment'] == 'return') {
	  			
		$check_this_order = $wpdb->get_results ("SELECT * FROM `" . $wpdb->prefix . "plugin_yocommerce_orders` WHERE `order_token`='" . $_SESSION['TOKEN'] . "';");
		
		if (count($check_this_order) == 0) {		
				
		$wpdb->insert($wpdb->prefix . "plugin_yocommerce_orders", array(
		   "order_order_id" =>$_SESSION["yocommerce_order_id"],
		   "order_summ" => $_SESSION["yocommerce_amount"],
		   "order_date" => date('Y-m-d H:i:s'),
		   "order_ok" => "true",
		   "order_token" => $_SESSION['TOKEN'],
                   "order_name" => $_SESSION['yocommerce_buy_name'],
                   "order_email" => $_SESSION['yocommerce_buy_email'],
                   "order_phone" => $_SESSION['yocommerce_buy_phone'],
                   "order_address" => $_SESSION['yocommerce_buy_address']
                        
		));
		$_SESSION['yocommerce_order_id'] =  yocommerce_new_order_id(); // new session id
		}
  

	}
	
	 
}
add_action( 'init', 'yocommerce_get_requests' );

// ADMIN
include("yocommerce_admin.php");
?>