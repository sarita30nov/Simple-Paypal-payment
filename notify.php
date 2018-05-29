<?php

	require_once("includes/include.php");
	
	$data = $_REQUEST;
	
	$checpoint= mysql_fetch_assoc(mysql_query("SELECT * FROM payments where WHERE id='".$data['custom']."'  "));
	if($checpoint['return_updated']==0)
	{
		
		$sql = "UPDATE payments SET payment_date='".$data['payment_date']."'  ,   
		payment_status='".$data['payment_status']."'  , 
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
				"'Add Wallet', ".
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
		}	
}