<?php
require_once("includes/include.php");
$paypal_url='https://www.sandbox.paypal.com/cgi-bin/webscr'; // Test Paypal API URL
$paypal_id='akazen79-facilitator@gmail.com'; // Business email ID

$amount =$_REQUEST['amount'];

$sql="insert into payments(userID,payment_amount,createdDATE) value('".$_SESSION['userID']."','".$_REQUEST['amount']."','".date("Y-m-d H:i:s")."' )"; 

//echo $sql;  die;
mysql_query($sql);
$custom= mysql_insert_id();
?>
<!DOCTYPE html>
<html>
	<head>	
	<title> Paypal Payment Gateway </title>
	<script type="text/javascript">
	function submitVal()
	{
	   document.getElementById("paypal_form").submit(); 
	}
	</script>
	
	</head>
<body onload="submitVal();">
<center><h2>Please do not back or refresh button during processing</h2> </center>
<center><img src="./images/loader.gif" alt="loader image" title="loader image"> </center>
<form action="<?php echo $paypal_url; ?>" method="post" enctype="multipart/form-data" id="paypal_form">
	<input type="hidden" name="amount" value="<?php echo $amount; ?>" placeholder="Enter Amount without MXN" />
	<input type="hidden" name="no_shipping" value="1">
	<input type="hidden" name="cmd" value="_xclick">
	<input type="hidden" name="business" value="akazen79-facilitator@gmail.com">
	<input type="hidden" name="item_name" value="Add Money to wallet">
	<input type="hidden" name="item_number" value="1">
	<input type="hidden" name="custom" value="<?php echo $custom; ?>">
	<input type="hidden" name="currency_code" value="MXN">
	<input type="hidden" name="bn" value="PP-BuyNowBF">
	<input type="hidden" name="rm" value="2">
	<input type="hidden" name="lc" value="en">
	<input type="hidden" name="cancel_return" value="<?php echo $site_url;?>/cancel.php">
	<input type="hidden" name="return" value="<?php echo $site_url;?>/return.php">
	<input type="hidden" name="notify" value="<?php echo $site_url;?>/notify.php">
</form>
</body>
</html>