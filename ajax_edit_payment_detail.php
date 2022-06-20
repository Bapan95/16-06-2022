<?php
require_once("../lib/config.php");
require_once("../lib/constants.php");

$logged_user_id = my_session('user_id');
if (isset($_REQUEST['source']) && ($_REQUEST['source'] == 'app')) {
  $logged_user_id = $_REQUEST['user_id'];

  //  print_r($value);die;
}
$action_type = $_REQUEST['action_type'];
$return_data  = array();


if ($action_type == "Edit_payment") {

  $value = $_REQUEST['value'];
  $payment_mode = $_REQUEST['payment_mode'];
  $payment_number = $_REQUEST['payment_number'];
  $transaction_date = $_REQUEST['transaction_date'];
  $newDate = date("Y-m-d", strtotime($transaction_date));
  $payment_date = $_REQUEST['payment_date'];
  $newDate1 = date("Y-m-d", strtotime($payment_date));
  $bank_name = $_REQUEST['bank_name'];
  $paid_to = $_REQUEST['paid_to'];
  $payment_id = $_REQUEST['payment_id'];
  $reference_no = $_REQUEST['reference_no'];
  $remarks = $_REQUEST['remarks'];
  $bank_name = $_REQUEST['bank_name'];
  //  print_r('ok');die;


  $query = "update other_payments set amount='" . $value . "',payment_mode='" . $payment_mode . "',payment_number='" . $payment_number . "',transaction_date='" . $newDate . "', payment_date='" . $newDate1 . "',paid_to='" . $paid_to . "',reference_no='" . $reference_no . "',remarks='" . $remarks . "',bank_id=$bank_name where payment_id='" . $payment_id . "'";
  // echo $query;
  // die;
  $request = $db->query($query);
  //  print_r($request);die;
  if ($request) {
    $return['key'] = 'S';
  } else {
    $return['key'] = 'E';
  }

  echo json_encode($return);
} elseif ($action_type == "Bank") {
  $payment_id = intval($_REQUEST['payment_id']);

  $query = "SELECT bank_id,bank_name,bank_head_office_address FROM bank_master";
  // echo $query;die();
  $result = $db->query($query);
  while ($data = mysqli_fetch_assoc($result)) {
    $ret[] = $data;
  }


  $queryk = "SELECT * FROM other_payments where payment_id=" . $payment_id . "";

  $resultk = $db->query($queryk);
  while ($datak = mysqli_fetch_assoc($resultk)) {
    $retk[] = $datak;
  }


  $return_data  = array('status' => true, 'bank_list' => $ret, 'bank_list_id' => $retk);
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
} elseif ($action_type == "EDIT_PAYMENT_VOUCHER") {
  $payment_id = intval($_REQUEST['payment_id']);

  $query = "SELECT op.payment_id, op.payment_number, ah.description ac_head_name, ash.description ac_subhead_name, 
		DATE_FORMAT(op.payment_date,'%d/%m/%Y') payment_date, op.payment_mode, op.amount, op.reference_no, 
		DATE_FORMAT(op.transaction_date,'%d/%m/%Y') transaction_date, b.bank_name, b.bank_head_office_address, op.paid_to, op.remarks
		FROM other_payments op
		LEFT JOIN acc_ledger_head_master ah ON ah.acc_ledger_head_id = op.acc_ledger_head_id
		LEFT JOIN acc_ledger_sub_head ash ON op.acc_ledger_sub_head_id = ash.acc_ledger_sub_head_id
		LEFT JOIN bank_master b ON b.bank_id = op.bank_id
	WHERE op.payment_id = " . $payment_id . ";";
  $result = $db->query($query);
  //echo json_encode($query); exit();
  $payment_details = mysqli_fetch_assoc($result);
  $payment_details['remarks'] = nl2br($payment_details['remarks']);
  $return_data = array('status' => true, 'payment_details' => $payment_details, 'query' => $query);
  echo json_encode($return_data);
}
