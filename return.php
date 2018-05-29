<?php

	require_once("includes/include.php");
	
	$data = $_REQUEST;
	
	$sql = "UPDATE payments SET payment_date='".$data['payment_date']."'  ,   
	payment_status='".$data['payment_status']."'  , 
	payer_email='".$data['payer_email']."'  , 
	updatedDate='".date("Y-m-d H:i:s")."'  , 
	return_updated='1' , 
	payment_log='".json_encode($data)."' 
	WHERE id='".$data['custom']."'";
	
	mysql_query($sql);		

if($data['payment_status']=='Completed')
{
	$query= mysql_query("SELECT * FROM userWALLET where userID= '".$_SESSION['userID']."' ");
	
	$wallet_gross = 0;
	if(mysql_num_rows($query)>0)
	{
		$record  = mysql_fetch_assoc($query); 
		
		$wallet_gross  = $record['walletMONEY']+ $data['payment_gross'] ;
		
		$sql_update = "UPDATE userWALLET SET walletMONEY='".$wallet_gross ."'  
				WHERE userID='".$_SESSION['userID']."'";
		mysql_query($sql_update);
	}
	else
	{
		$sql_insert="insert into userWALLET(walletMONEY,userID) value('".$data['payment_gross']."','".$_SESSION['userID']."' )"; 
		mysql_query($sql_insert);
		
		$wallet_gross  = $data['payment_gross'];
	}
	
	
		$poolSql = "INSERT INTO user_wallet_action (" .
		"userID, ".
		"movement_type, ".
		"plus_minus, ".
		"deposit_total, ".
		"paypal_cut, ".
		"withdrawl_fee, ".
		"operations_fee, ".
		"account_update, ".
		"confirmation_number, ".
		"created_date, ".
		"created_by".
		") VALUES (" .
		"'".$_SESSION['userID']."', ".
		"'paypaldeposit', ".
		"'plus', ".
		"'".$data['payment_gross']."', ".
		"'0', ".
		"'', ".
		"'0', ".
		"'".$wallet_gross."', ".
		"'".$data['txn_id']."', ".
		"'".date('Y-m-d h:i:s')."', ".
		"'paypal' ".
		")";
		mysql_query($poolSql);
	
	   //send email to user for sucessfull payment
		
		$wallet_record= mysql_fetch_assoc(mysql_query("SELECT * FROM userWALLET where userID= '".$_SESSION['userID']."' "));
		$user_record= mysql_fetch_assoc(mysql_query("SELECT * FROM userregisteration where  userID='".$_SESSION['userID']."' "));
		
		
	   // send email to admin 	
	  
		$qry=mysql_query("select * from email_content where emailID='15'");
		$dataemailcontent=mysql_fetch_assoc($qry);
		$to = $user_record['userEMAIL'];
		$subject = $dataemailcontent['emailTITLE'];
		$EmailBODY = nl2br($dataemailcontent['emailBODY']);
		
		$EmailBODY = str_replace("[user_name]" , $user_record['userNAME'] ,  $EmailBODY ) ; 
		$EmailBODY = str_replace("[added_amount]" , $data['payment_gross'] ,  $EmailBODY ) ; 
		$EmailBODY = str_replace("[wallet_amount]" , $wallet_record['walletMONEY'] ,  $EmailBODY ) ; 
		
		$senderEmail = $dataemailcontent['senderEMAIL'];
		$message = "
		<html>
		<head>
		<title>$subject</title>
		</head>
		<body>
			$EmailBODY
		</body>
		</html>
		";
		//echo $message ;die;
		
		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= "From: <$senderEmail>" . "\r\n";
		
		if(mail($to,$subject,$message,$headers))
		{
			//return 1;
		}
		else 
		{
			//return 0;
		}
		
	   //send email to user 
	   $qry=mysql_query("select * from email_content where emailID='16'");
		$dataemailcontent=mysql_fetch_assoc($qry);
		$to = 'info@quinielasdeportivas.com';
		$subject = $dataemailcontent['emailTITLE'];
		$EmailBODY = nl2br($dataemailcontent['emailBODY']);
		
		$EmailBODY = str_replace("[user_name]" , $user_record['userNAME'] ,  $EmailBODY ) ; 
		$EmailBODY = str_replace("[added_amount]" , $data['payment_gross'] ,  $EmailBODY ) ; 
		$EmailBODY = str_replace("[wallet_amount]" , $wallet_record['walletMONEY'] ,  $EmailBODY ) ; 
		
		$senderEmail = $dataemailcontent['senderEMAIL'];
		$message = "
		<html>
		<head>
		<title>$subject</title>
		</head>
		<body>
			$EmailBODY
		</body>
		</html>
		";
		//echo $message ;die;
		
		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= "From: <$senderEmail>" . "\r\n";
		
		if(mail($to,$subject,$message,$headers))
		{
			//return 1;
		}
		else 
		{
			//return 0;
		}
}

header("Location:success.php?payment_status=".$data['payment_status']."&custom=".$data['custom']."");