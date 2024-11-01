<?php


    /**/      
            $err = array();

            if (isset($_POST['yocommerce_buy_name']) ) {
             
                if (strlen($_POST['yocommerce_buy_name']) > 2 && strlen($_POST['yocommerce_buy_name']) < 30) {
                 } else {
                    $err[] = 'Name must contain 2-30 symbols';
                 }
                 $_SESSION['yocommerce_buy_name'] = $_POST['yocommerce_buy_name'];
            } 
              
            

             if (isset($_POST['yocommerce_buy_email']) ) {
                if (strlen($_POST['yocommerce_buy_email']) > 5 && strlen($_POST['yocommerce_buy_email']) < 30 ) {
                 } else {
                    $err[] = 'Email must contain 5-30 symbols';
                 }
                 $_SESSION['yocommerce_buy_email'] = $_POST['yocommerce_buy_email'];
            }


            if (isset($_POST['yocommerce_buy_phone']) ) {
                if (strlen($_POST['yocommerce_buy_phone']) > 5 && strlen($_POST['yocommerce_buy_phone']) < 30) {
                 } else {
                    $err[] = 'Phone must contain 5-30 symbols';
                 }
                 $_SESSION['yocommerce_buy_phone'] = $_POST['yocommerce_buy_phone'];
            }

             if (isset($_POST['yocommerce_buy_address']) ) {
                if (strlen($_POST['yocommerce_buy_address']) > 10 && strlen($_POST['yocommerce_buy_address']) < 120) {
                 } else {
                    $err[] = 'Phone must contain 10-120 symbols';
                 }
                 $_SESSION['yocommerce_buy_address'] = $_POST['yocommerce_buy_address'];
            }
            
            
            $_SESSION['yocommerce_buy_errors'] = $err;
          
            
           
            
            /*

            echo '<center>';
            foreach ($err as $error) {
                echo '<p style="color:black;font-size:12px;"> ' . $error . '</p>';
            }
            echo '</center>';*/
               
            
                
            
if (count($err) > 0 ) {
  
} else if (count($err) == 0  && isset($_GET['yocommerce_buy'])) {
     
    
  
			 // ==================================
		   // PayPal Express Checkout Module
		   // ==================================
		   
		   //'------------------------------------
		   //' The paymentAmount is the total value of 
		   //' the shopping cart, that was set 
		   //' earlier in a session variable 
		   //' by the shopping cart page
		   //'------------------------------------
		   $paymentAmount = $_SESSION['yocommerce_amount']; /*$_SESSION["Payment_Amount"]*/;
		   
		
		   
		   //'------------------------------------
		   //' The currencyCodeType and paymentType 
		   //' are set to the selections made on the Integration Assistant 
		   //'------------------------------------
		   $currencyCodeType = "RUB";
		   $paymentType = "Order";
		   
		   //'------------------------------------
		   //' The returnURL is the location where buyers return to when a
		   //' payment has been succesfully authorized.
		   //'
		   //' This is set to the value entered on the Integration Assistant 
		   //'------------------------------------
		   $returnURL = "http://" . $_SERVER['SERVER_NAME'] . "/?yocommerce_payment=return";
		   
		   //'------------------------------------
		   //' The cancelURL is the location buyers are sent to when they hit the
		   //' cancel button during authorization of payment during the PayPal flow
		   //'
		   //' This is set to the value entered on the Integration Assistant 
		   //'------------------------------------
		   $cancelURL = "http://" . $_SERVER['SERVER_NAME'] . "/?yocommerce_payment=cancel";
		   
		   //'------------------------------------
		   //' Calls the SetExpressCheckout API call
		   //'
		   //' The CallShortcutExpressCheckout function is defined in the file PayPalFunctions.php,
		   //' it is included at the top of this file.
		   //'-------------------------------------------------
		   $resArray = CallShortcutExpressCheckout ($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL);
			   
		   $ack = strtoupper($resArray["ACK"]);
		   
			
	   
			   
		   if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
		   {
			   RedirectToPayPal ( $resArray["TOKEN"] );
		   } 
		   else  
		   {
			   //Display a user friendly Error on the page using any of the following error information returned by PayPal
			   $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
			   $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
			   $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
			   $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
			   
			   echo "SetExpressCheckout API call failed. ";
			   echo "Detailed Error Message: " . $ErrorLongMsg;
			   echo "Short Error Message: " . $ErrorShortMsg;
			   echo "Error Code: " . $ErrorCode;
			   echo "Error Severity Code: " . $ErrorSeverityCode;
			 }

      }

?>