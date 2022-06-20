<?php
require_once("../lib/config.php");
require_once("../lib/constants.php");
$logged_user_id = my_session('user_id');
if (isset($_REQUEST['source']) && ($_REQUEST['source'] == 'app')) {
  $logged_user_id = $_REQUEST['user_id'];
}
$action_type = $_REQUEST['action_type'];
$return_data  = array();

if ($action_type == "Edit_receipt") {

  $value = $_REQUEST['value'];
  $receipt_mode = $_REQUEST['receipt_mode'];
  $receipt_number = $_REQUEST['receipt_number'];
  $receipt_date = $_REQUEST['receipt_date'];
  $newDate = date("Y-m-d", strtotime($receipt_date));
  $transaction_date = $_REQUEST['transaction_date'];
  $newDate1 = date("Y-m-d", strtotime($transaction_date));
  $received_from = $_REQUEST['received_from'];
  $receipt_id = $_REQUEST['receipt_id'];
  $reference_no = $_REQUEST['reference_no'];
  $remarks = $_REQUEST['remarks'];
  $bank_name = $_REQUEST['bank_name'];
  // echo $value;
  // die;
  $query = "update other_receipts set amount='" . $value . "',receipt_mode='" . $receipt_mode . "',receipt_number='" . $receipt_number . "',receipt_date='" . $newDate . "', transaction_date='" . $newDate1 . "',received_from='" . $received_from . "',reference_no='" . $reference_no . "',remarks='" . $remarks . "',bank_id=$bank_name where receipt_id='" . $receipt_id . "'";
  // echo $query;
  // die;
  $request = $db->query($query);
  if ($request) {
    $return['key'] = 'S';
  } else {
    $return['key'] = 'E';
  }

  echo json_encode($return);
} elseif ($action_type == "Bank") {
   $receipt_id = intval($_REQUEST['receipt_id']);
  // $query = "SELECT bank_id FROM other_receipts where receipt_id=" . $receipt_id . "";
  // // echo $query;die();
  // $result = $db->query($query);
  // while ($data = mysqli_fetch_assoc($result)) {
  //   $ret[] = $data;
  // }

  // $bank_head_office_address = $_REQUEST['bank_head_office_address'];
  
  $query = "SELECT bank_id,bank_name,bank_head_office_address FROM bank_master";
  // echo $query;die();
  $result = $db->query($query);
  while ($data = mysqli_fetch_assoc($result)) {
    $ret[] = $data;
  }

 
  $queryk = "SELECT * FROM other_receipts where receipt_id=" . $receipt_id . "";
  
  $resultk = $db->query($queryk);
  while ($datak = mysqli_fetch_assoc($resultk)) {
    $retk[] = $datak;
  }


  $return_data  = array('status' => true, 'bank_list' => $ret,'bank_list_id' => $retk);
  echo json_encode($return_data);
} elseif ($action_type == "branch") {
  $bank_id = $_REQUEST['bank_id'];

  $query = "SELECT bank_head_office_address as branch FROM bank_master where bank_id=" . $bank_id . "";
  //echo $query;die;
  // echo $query;die();
  $result = $db->query($query);
  while ($data = mysqli_fetch_assoc($result)) {
    $ret[] = $data;
  }
  $return_data  = array('status' => true, 'branch_list' => $ret);
  echo json_encode($return_data);
}
elseif($action_type == "EDIT_RECEIPT_VOUCHER"){
	 $receipt_id = intval($_REQUEST['receipt_id']);

  $query = "SELECT op.receipt_id, op.receipt_number,op.bank_id, ah.description ac_head_name, ash.description ac_subhead_name, 
		DATE_FORMAT(op.receipt_date,'%d/%m/%Y') receipt_date, op.receipt_mode, op.amount, op.reference_no, 
		DATE_FORMAT(op.transaction_date,'%d/%m/%Y') transaction_date,  b.bank_name, b.bank_head_office_address, op.received_from, op.remarks
		FROM other_receipts op
		LEFT JOIN acc_ledger_head_master ah ON ah.acc_ledger_head_id = op.acc_ledger_head_id
		LEFT JOIN acc_ledger_sub_head ash ON op.acc_ledger_sub_head_id = ash.acc_ledger_sub_head_id
		LEFT JOIN bank_master b ON b.bank_id = op.bank_id
	WHERE op.receipt_id = " . $receipt_id . ";";
	$result = $db->query($query);
	//echo json_encode($query); exit();
	$receipt_details = mysqli_fetch_assoc($result);
	$return_data = array('status' => true, 'receipt_details' => $receipt_details, 'query' => $query);
	// echo "<pre>";
	// print_r($return_data);
	// die;
	echo json_encode($return_data);
}

