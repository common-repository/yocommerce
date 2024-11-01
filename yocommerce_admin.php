<?php
// admin get requests 1
function yocommerce_admin_get_requests(){ // admin panel
     if (isset($_POST['yocommerce_paypal_username']) && isset($_POST['yocommerce_paypal_password'])
		&& isset($_POST['yocommerce_paypal_signature']) && isset($_POST['yocommerce_paypal_sandbox']) ) {
       		 
		 global $wpdb;
		 // clear table
         $wpdb->query("TRUNCATE TABLE `wp_plugin_yocommerce_settings`");
		 // write new first string
	     $wpdb->insert($wpdb->prefix . "plugin_yocommerce_settings", array(
		  "setting_paypal_username" =>$_POST['yocommerce_paypal_username'],
		  "setting_paypal_password" => $_POST['yocommerce_paypal_password'],
		  "setting_paypal_signature" => $_POST['yocommerce_paypal_signature'],
          "setting_paypal_sandbox_flag" => $_POST['yocommerce_paypal_sandbox']
	   ));
	}
    
}
add_action( 'admin_init', 'yocommerce_admin_get_requests' ); 

function register_yocommerce_admin_page(){
	add_menu_page( 'YoCommerce', 'YoCommerce', 'manage_options', 'yocommerce', 'yocommerce_admin_page', plugins_url( 'images/icon.png' ) ); 
}

// admin get requests 2
function yocommerce_admin_get_requests_2(){ // admin panel
	

	
     if (isset($_POST['yocommerce_theme_color']) ) {
		
    $array_theme_colors = array('black', 'red', 'green', 'orange', 'blue');
	if (array_search($_POST['yocommerce_theme_color'],$array_theme_colors) != false) {
		 $theme_color = $_POST['yocommerce_theme_color'];
	} else {
		 $theme_color = 'black';
	}
       		 
		 global $wpdb;
		 // clear table
         $wpdb->query("TRUNCATE TABLE `wp_plugin_yocommerce_settings_2`");
		 // write new first string
	     $wpdb->insert($wpdb->prefix . "plugin_yocommerce_settings_2", array(
		  "setting_theme_color" =>$theme_color,
	   ));
	}
}
add_action( 'admin_init', 'yocommerce_admin_get_requests_2' ); 

function register_yocommerce_admin_page_2(){
	add_menu_page( 'YoCommerce', 'YoCommerce', 'manage_options', 'yocommerce', 'yocommerce_admin_page_2', plugins_url( 'images/icon.png' ) ); 
}

function yocommerce_admin_page(){
 	
	global $wpdb;
		
	$settings = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "plugin_yocommerce_settings` " );
        $settings = $settings[0];
	?>
	<style type="text/css">
			   #yocommerce_paypal_input{width:500px;}
			   #yocommerce_admin_save_button{width:100px;}
	</style>
		
	<h2>YoCommerce - Admin page</h2>
        
        <?php
        
        ?>
	
	<h4>Paypal settings</h4>
	<form id="yocommerce_admin_form" action="#" method="POST">
	  UserName:<br>
	  <input type="text" id="yocommerce_paypal_input" name="yocommerce_paypal_username" value="<?php echo $settings->setting_paypal_username; ?>">
	  <br>
	  Password:<br>
	  <input type="text" id="yocommerce_paypal_input" name="yocommerce_paypal_password" value="<?php echo $settings->setting_paypal_password; ?>">
	  <br>
	  Signature:<br>
	  <input type="text" id="yocommerce_paypal_input" name="yocommerce_paypal_signature" value="<?php echo $settings->setting_paypal_signature; ?>">
	  <br>
	  Sandbox:<br>
	  <?php
	  if ( $settings->setting_paypal_sandbox_flag == "true") { ?>
		  <input type="radio" name="yocommerce_paypal_sandbox" value="true" checked="checked" >true
	      <input type="radio" name="yocommerce_paypal_sandbox" value="false" >false
	  <?php
	   } else  {
	  ?>
	  	  <input type="radio" name="yocommerce_paypal_sandbox" value="true" >true
	      <input type="radio" name="yocommerce_paypal_sandbox" value="false" checked="checked">false
	  <?php
	  }
	  ?>  
	  
          <?php
	  $other_attributes = array( 'id' => 'yocommerce_admin_save_button' );
        submit_button( 'Save Settings', 'primary', 'yocommerce_admin_save_button', true, $other_attributes );
        ?>
	</form>
	
	<h4>Theme color settings</h4>
	<form id="yocommerce_admin_form_2" action="#" method="POST">
	  Color:<br>
	  <select id="yocommerce_theme_color" name="yocommerce_theme_color">
			<option value="black">Black</option>
			<option value="red">Red</option>
			<option value="green">Green</option>
			<option value="orange">Orange</option>	
			<option value="blue">Blue</option>	
	</select>
	  <br>
          <?php
	  $other_attributes2 = array( 'id' => 'yocommerce_admin_save_button_2' );
        submit_button( 'Save Settings', 'primary', 'yocommerce_admin_save_button_2', true, $other_attributes2 );
        ?>
	</form>
	<?php
	
	
    // buy products
    if (!isset($_GET['info_order_id'])) {
		  echo '<h4>Orders</h4>';
	 	  
                  global $wpdb; 
		  $orders = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix ."plugin_yocommerce_orders` " );
			   echo 'ID DATE SUMM <br>';
			   
		  for ($i = 0; $i < count($orders); $i++) {
			  echo $orders[$i]->order_order_id .' ' .  $orders[$i]->order_date . '';
			  echo '<a href="?page=yocommerce&info_order_id=' . $orders[$i]->order_order_id . '">details</a><br>';
		  }
                  
               
	
	} else {
          
	       
                  global $wpdb; 
	     $order_id = (int) $_GET['info_order_id'];
	     $order = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "plugin_yocommerce_orders`, `" .$wpdb->prefix . "plugin_yocommerce_goods` WHERE `" .$wpdb->prefix . "plugin_yocommerce_orders`.`order_order_id`=`" .$wpdb->prefix . "plugin_yocommerce_goods`.`goods_order_id` AND `" .$wpdb->prefix . "plugin_yocommerce_orders`.`order_order_id`='" . $order_id . "' ; ");
                   
             
               echo '<br><br><b>Buyer\'s Info</b><br>';
               echo $order[0]->order_name . '<br>';
               echo $order[0]->order_email . '<br>';
               echo $order[0]->order_phone . '<br>';
               echo $order[0]->order_address . '<br>';
             
		 echo '<br><a href="/wp-admin/admin.php?page=yocommerce">Back</a>';
		 echo '<h4>Order ' . $order_id . '</h4>';
                 
                
                 
                 
                 
		 echo 'Goods: <br>';
		  
          for ($i = 0; $i < count($order); $i++) {
			  echo '<a href="'. get_permalink($order[$i]->goods_goods_id) . '" target=_blank>' . get_the_title($order[$i]->goods_goods_id) . '</a> ' . $order[$i]->order_summ . '$ <br>';
		  }
                  
                  
	}
        
       
            
        
}
add_action( 'admin_menu', 'register_yocommerce_admin_page' );





?>
