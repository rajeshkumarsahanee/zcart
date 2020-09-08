<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
if (!isset($_REQUEST['id']) || trim($_REQUEST['id']) == '1' || !isUserHavePermission(MANAGE_BUYERS_SELLERS_SECTION, getUserLoggedId())) {
    header("location: customers.php");
}

$updatemsg = "";
//Update User General Details
if (isset($_POST['update']) && isUserHavePermission(MANAGE_BUYERS_SELLERS_SECTION, getUserLoggedId())) {
    $user['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $tmpuser = getUser($user['id']);
    $user['username'] = $tmpuser['username'];//filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $user['email'] = filter_var(trim($_POST['email']), FILTER_SANITIZE_STRING);
    $user['display_name'] = isset($_POST['display_name']) ? filter_var(trim($_POST['display_name']), FILTER_SANITIZE_STRING) : $user['username'];
    $user['status'] = isset($_POST['status']) ? filter_var(trim($_POST['status']), FILTER_SANITIZE_STRING) : $tmpuser['status'];

    $meta['phone'] = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
    $meta['country'] = filter_var(trim($_POST['country']), FILTER_SANITIZE_STRING);
    $meta['state'] = filter_var(trim($_POST['state']), FILTER_SANITIZE_STRING);
    $meta['city'] = filter_var(trim($_POST['city']), FILTER_SANITIZE_STRING);
    $user['metas'] = $meta;

    if (updateUser($user)) {
        $updatemsg = '<div class="alert alert-success">General Details Updated Successfully</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
    }
}

//Add User Address
if (isset($_POST['addnewaddress']) && isUserHavePermission(MANAGE_BUYERS_SELLERS_SECTION, getUserLoggedId())) {
    $user_id = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $tmpuser = getUser($user_id);
    $address['user_id'] = $tmpuser['id'];
    $address['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $address['mobile'] = filter_var(trim($_POST['mobile']), FILTER_SANITIZE_STRING);
    $address['phone'] = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
    $address['address'] = filter_var(trim($_POST['address']), FILTER_SANITIZE_STRING);
    $address['locality'] = filter_var(trim($_POST['locality']), FILTER_SANITIZE_STRING);
    $address['landmark'] = filter_var(trim($_POST['landmark']), FILTER_SANITIZE_STRING);
    $address['city'] = filter_var(trim($_POST['city']), FILTER_SANITIZE_STRING);
    $address['state'] = filter_var(trim($_POST['state']), FILTER_SANITIZE_STRING);
    $address['pincode'] = filter_var(trim($_POST['pincode']), FILTER_SANITIZE_STRING);
    $address['country'] = filter_var(trim($_POST['country']), FILTER_SANITIZE_STRING);
    $address['address_type'] = filter_var(trim($_POST['address_type']), FILTER_SANITIZE_STRING);
    $address['is_default'] = filter_var(trim($_POST['is_default']), FILTER_SANITIZE_STRING);
    $address['status'] = 'A';

    if (addUserAddress($address)) {
        $updatemsg = '<div class="alert alert-success">Address Added Successfully</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
    }
}

//Delete User Address
if (isset($_GET['deladdress']) && isUserHavePermission(MANAGE_BUYERS_SELLERS_SECTION, getUserLoggedId())) {
    $address_id = filter_var(trim($_GET['deladdress']), FILTER_SANITIZE_NUMBER_INT);
    
    if (deleteUserAddress($address_id)) {
        $updatemsg = '<div class="alert alert-success">Address Deleted Successfully</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
    }
}

//Update User Bank Info
if (isset($_POST['updatebankdetails']) && isUserHavePermission(MANAGE_BUYERS_SELLERS_SECTION, getUserLoggedId())) {
    $user_id = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $tmpuser = getUser($user_id);
    $bank['user_id'] = $tmpuser['id'];
    $bank['bankname'] = filter_var(trim($_POST['bankname']), FILTER_SANITIZE_STRING);
    $bank['accountname'] = filter_var(trim($_POST['accountname']), FILTER_SANITIZE_STRING);
    $bank['accountnumber'] = filter_var(trim($_POST['accountnumber']), FILTER_SANITIZE_STRING);
    $bank['ifsc'] = filter_var(trim($_POST['ifsc']), FILTER_SANITIZE_STRING);
    $bank['bankaddress'] = filter_var(trim($_POST['bankaddress']), FILTER_SANITIZE_STRING);

    if (addUpdateUserBankDetail($bank)) {
        $updatemsg = '<div class="alert alert-success">Bank Details Updated Successfully</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
    }
}

//Add Transaction
if (isset($_POST['addtransaction']) && isUserHavePermission(MANAGE_BUYERS_SELLERS_SECTION, getUserLoggedId())) {
    $user_id = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $tmpuser = getUser($user_id);
    $transaction['user_id'] = $tmpuser['id'];
    $transaction['txn_datetime'] = date("Y-m-d H:i:s");
    $transaction['credit'] = trim($_POST['txn_type']) == "credit" ? filter_var(trim($_POST['amount']), FILTER_SANITIZE_STRING) : "0";
    $transaction['debit'] = trim($_POST['txn_type']) == "debit" ? filter_var(trim($_POST['amount']), FILTER_SANITIZE_STRING) : "0";
    $transaction['comments'] = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
    $transaction['order_id'] = "0";
    $transaction['withdrawal_id'] = "0";
    $transaction['status'] = "1";
    

    if (addUserTransaction($transaction)) {
        $updatemsg = '<div class="alert alert-success">Transaction Added Successfully</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
    }
}

//Update User Password
if (isset($_POST['updatepassword']) && isUserHavePermission(MANAGE_BUYERS_SELLERS_SECTION, getUserLoggedId())) {
    $user['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $tmpuser = getUser($user['id']);
    $password = isset($_POST['password']) && trim($_POST['password']) <> "" ? filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING) : $tmpuser['password'];
    $cpassword = isset($_POST['cpassword']) && trim($_POST['password']) <> "" ? filter_var(trim($_POST['cpassword']), FILTER_SANITIZE_STRING) : $tmpuser['password'];
    if ($password == $cpassword) {
        if (update(T_USERS, array('password' => $password), array('id' => $tmpuser['id']))) {
            $updatemsg = '<div class="alert alert-success">Password Updated Successfully</div>';
        } else {
            $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    } else {
        $updatemsg = '<div class="alert alert-danger">Password does not match!</div>';
    }
}

//Send Email
if (isset($_POST['sendemail']) && isUserHavePermission(MANAGE_BUYERS_SELLERS_SECTION, getUserLoggedId())) {
    $user_id = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $u = getUser($user_id);
    $lu = getUser(getUserLoggedId());
    $user_subject = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
    $user_message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);
    $logo = getConfig("EMAIL_TEMPLATE_LOGO");
    $logotag = $logo != null && trim($logo) <> "" ? '<img src="'.$logo.'"/>' : "";
    
    $template = getEmailTemplate('send_message');
    $subject = str_replace('{website_name}', $sys['site_name'], $template['subject']);
    $searchfor = array('{Company_Logo}', '{current_date}', '{user_full_name}', '{username}', '{message_subject}', '{message}', '{click_here}', '{website_name}');
    $replacements = array($logotag, date("Y-m-d"), $u['display_name'], $lu['username'], $user_subject, $user_message, '<a href="">click here</a>', $sys['site_name']);
    $body = str_replace($searchfor, $replacements, $template['body']);
    
    $data['from_email'] = secure($sys['admin_email']);
    $data['from_name'] = $sys['site_name'];
    $data['to_email'] = $u['email'];
    $data['to_name'] = $u['display_name'];
    $data['charSet'] = "";
    $data['is_html'] = true;
    $data['subject'] = $subject;
    $data['message_body'] = $body;
    
    if(sendMessage($data)) {
        $updatemsg = '<div class="alert alert-success">Email Message Sent Successfully</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">Error! Sending Mail</div>';
    }
}

if (isset($_REQUEST['id']) && trim($_REQUEST['id']) != '') {
    $user = getUser(trim($_REQUEST['id']));
    if ($user == null) {
        header("location: customers.php");
    }
} else {
    header("location: customers.php");
}
$addresses = getUserAddresses(array(), array('user_id' => $user['id']), 0, -1);
$bank = getUserBankDetail($user['id']);
$tmpcountries = getCountries(array('id', 'name'), array(), 0, -1);
foreach($tmpcountries as $c) {
   $countries[$c['id']] = $c; 
}
$tmpstates = isset($user['metas']['country']) ? getStates(array('id', 'name'), array('country_id' => $user['metas']['country'])) : array();
foreach($tmpstates as $s) {
   $states[$s['id']] = $s; 
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Customer - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
    </head>
    <!--
    BODY TAG OPTIONS:
    =================
    Apply one or more of the following classes to get the
    desired effect
    |---------------------------------------------------------|
    | SKINS         | skin-blue                               |
    |               | skin-black                              |
    |               | skin-purple                             |
    |               | skin-yellow                             |
    |               | skin-red                                |
    |               | skin-green                              |
    |---------------------------------------------------------|
    |LAYOUT OPTIONS | fixed                                   |
    |               | layout-boxed                            |
    |               | layout-top-nav                          |
    |               | sidebar-collapse                        |
    |               | sidebar-mini                            |
    |---------------------------------------------------------|
    -->
    <body class="skin-blue sidebar-mini">
        <div class="wrapper">

            <!-- Main Header -->
            <?php include 'header.php'; ?>
            <!-- Left side column. contains the logo and sidebar -->
            <?php include 'left_sidebar.php'; ?>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Edit Customer
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="dashboard"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li><a href="customers.php">Buyers/Sellers</a></li>
                        <li class="active"><a href="#">Edit Customer</a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="">
                        <!-- form start -->
                        <form role="form" action="customer-edit.php" method="post">
                            <?= $updatemsg ?>
                            <input type="hidden" name="status" id="activeid" value="<?= $user['status'] ?>"/>
                            <input type="hidden" name="id" value="<?= $user['id'] ?>"/>
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#general" data-toggle="tab">General</a></li>
                                    <li><a href="#addresses" data-toggle="tab">Addresses</a></li>
                                    <li><a href="#bankinfo" data-toggle="tab">Bank Info</a></li>
                                    <li><a href="#rewardpoints" data-toggle="tab">Reward Points</a></li>
                                    <li><a href="#transactions" data-toggle="tab">Transactions</a></li>
                                    <li><a href="#changepassword" data-toggle="tab">Change Password</a></li>
                                    <li><a href="#sendemail" data-toggle="tab">Send Email</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="general">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="username">Username</label>
                                                    <input type="text" class="form-control" id="username" name="username" value="<?= $user['username'] ?>" placeholder="Username" readonly/>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label for="email">Email address</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?>"  placeholder="Email"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label for="phone">Phone</label>
                                                    <input type="text" class="form-control" id="phone" name="phone" value="<?= isset($user['metas']['phone']) ? $user['metas']['phone'] : '' ?>" placeholder="Phone"/>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="display_name">Name</label>
                                                    <input type="text" class="form-control" id="display_name" name="display_name" value="<?= $user['display_name'] ?>" placeholder="Display Name"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="country">Country</label>
                                                    <select id="country" name="country" class="form-control">
                                                        <option value="">Select</option>
                                                        <?php foreach($countries as $c) { ?>
                                                        <option value="<?= $c['id'] ?>" <?= isset($user['metas']['country']) && $user['metas']['country'] == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="state">State</label>
                                                    <select id="state" name="state" class="form-control">
                                                        <option value="">Select</option>
                                                        <?php foreach($states as $s) { ?>
                                                        <option value="<?= $s['id'] ?>" <?= isset($user['metas']['state']) && $user['metas']['state'] == $s['id'] ? 'selected' : '' ?>><?= $s['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="city">City</label>
                                                    <input type="text" class="form-control" id="city" name="city" value="<?= isset($user['metas']['city']) ? $user['metas']['city'] : '' ?>" placeholder="City"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="submit" name="update" value="Update" class="btn btn-primary"/>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="addresses">
                                        <a href="#" title="Add New Address" data-toggle="modal" data-target="#addressModal" class="btn btn-default btn-sm"><i class="fa fa-plus-circle"></i> Add New</a>
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Address</th>
                                                    <th>Default</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $i = 1;
                                                foreach($addresses as $a) { ?>
                                                <tr>
                                                    <td><?= $i++ ?></td>
                                                    <td>
                                                        <b><?= $a['name'] ?></b><br/>
                                                        <?= !empty($a['address']) ? $a['address'] .","  : "" ?>
                                                        <?= !empty($a['locality']) ? $a['locality'] . ", " : "" ?>
                                                        <?= !empty($a['city']) ? $a['city'] . ", " : "" ?>
                                                        <?= isset($states[$a['state']]) ? $states[$a['state']]['name'] : "" ?>
                                                        <?= !empty($a['pincode']) ? " - " . $a['pincode'] . "," : "" ?>
                                                        <?= isset($countries[$a['country']]) ? $countries[$a['country']]['name'] : "" ?><br/>
                                                        <b>M:</b> <?= $a['mobile'] ?> <b>P:</b> <?= $a['phone'] ?>
                                                    </td>
                                                    <td><?= $a['is_default'] ?></td>
                                                    <td>
                                                        <div class='btn-group'>
                                                            <?php if (isUserHavePermission(MANAGE_BUYERS_SELLERS_SECTION, getUserLoggedId())) { ?>
                                                                <a class='btn btn-sm btn-primary' href="<?= $sys['site_url'] . '/admin/customer-address-edit.php?id=' . $a['id']; ?>" title="Edit" target="_blank"><i class="fa fa-pencil"></i></a>
                                                                <a class='btn btn-sm btn-danger' href="<?= $sys['site_url'] . '/admin/customer-edit.php?id='.$user['id'].'&deladdress=' . $a['id']; ?>" onclick="return confirm('Are you sure you want to delete?')" title="Delete"><i class="fa fa-trash"></i></a>
                                                            <?php } ?>                                                                
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="bankinfo">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="bankname">Bank Name</label>
                                                    <input type="text" class="form-control" id="bankname" name="bankname" value="<?= isset($bank['bankname']) ? $bank['bankname'] : '' ?>" placeholder="Bank Name"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label for="accountname">Account Holder Name</label>
                                                    <input type="text" class="form-control" id="accountname" name="accountname" value="<?= isset($bank['accountname']) ? $bank['accountname'] : '' ?>"  placeholder="Account Holder Name"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">                                        
                                                    <label for="accountnumber">Account Number</label>
                                                    <input type="text" class="form-control" id="accountnumber" name="accountnumber" value="<?= isset($bank['accountnumber']) ? $bank['accountnumber'] : '' ?>"  placeholder="Account Number"/>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">                                        
                                                    <label for="ifsc">IFSC Code/Swift Code</label>
                                                    <input type="text" class="form-control" id="ifsc" name="ifsc" value="<?= isset($bank['ifsc']) ? $bank['ifsc'] : '' ?>"  placeholder="IFSC/Swift"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="bankaddress">Bank Address</label>
                                                    <textarea class="form-control" id="bankaddress" name="bankaddress" placeholder="Bank Address"><?= isset($bank['bankaddress']) ? $bank['bankaddress'] : '' ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="submit" name="updatebankdetails" value="Update" class="btn btn-primary"/>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="rewardpoints">
                                        coming soon
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="transactions">
                                        <table id="transactionsT" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Credit</th>
                                                    <th>Debit</th>
                                                    <th>Balance</th>
                                                    <th>Description</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="transactionsTBody">
                                                <!-- transactions goes here through ajax -->
                                            </tbody>
                                        </table>
                                        <div>
                                            <ul id="pagination" class="pagination pagination-sm no-margin">
                                                
                                            </ul>
                                            <span id="entries" class="pull-right" style="background: #f7f7f7;color: #666;padding: 5px;"></span>
                                            <div style="clear: both; margin-bottom: 5px;"></div>
                                        </div>
                                        <div class="" style="background: #3c8dbc;padding: 10px;">
                                             <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <select name="txn_type" class="form-control">
                                                            <option value="credit">Credit</option>
                                                            <option value="debit">Debit</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">                                        
                                                        <input type="text" class="form-control" name="amount" placeholder="Amount"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <textarea name="description" class="form-control" placeholder="Description"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <input type="submit" name="addtransaction" value="Add" class="btn btn-primary"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="changepassword">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password">New Password</label>
                                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password"/>
                                                    <a href="#" class="showpassword" onclick="return showHidePassword()">Show Password</a>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label for="cpassword">Confirm Password</label>
                                                    <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="Confirm Password"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="submit" name="updatepassword" value="Update" class="btn btn-primary"/>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="sendemail">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="subject">Subject</label>
                                                    <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label for="message">Message</label>
                                                    <textarea class="form-control" id="message" name="message" placeholder="Message"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="submit" id="sendemail" name="sendemail" value="Send Email" class="btn btn-primary"/>
                                    </div>
                                    <!-- /.tab-pane -->
                                </div>
                                <!-- /.tab-content -->
                            </div>
                        </form>
                    </div><!-- /.box -->

                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <div id="addressModal" class="modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title">Add New Address</h4>
                        </div>
                        <form action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>"/>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="name" id="name" placeholder="Full Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">                                            
                                            <input type="text" class="form-control" name="mobile" id="mobile" placeholder="Mobile" required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="address" id="address" placeholder="Address" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="locality" id="locality" placeholder="Locality" required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select id="countryforaddress" name="country" class="form-control" required>
                                                <option value="">-Select Country-</option>
                                                <?php foreach ($countries as $c) { ?>
                                                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select id="stateforaddress" name="state" class="form-control" required>
                                                <option value="">-Select State-</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="city" name="city" placeholder="City/District/Town" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="pincode" id="pincode" placeholder="Pincode"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="landmark" id="landmark" placeholder="Landmark (Optional)"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="phone" id="phone" placeholder="Alternate Phone (Optional)"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address_type" style="color: silver;font-weight: normal;font-size: 12px;">Address Type</label>
                                            <select id="address_type" name="address_type" class="form-control">
                                                <option value="HOME">Home</option>
                                                <option value="OFFICE">Office</option>
                                                <option value="OTHER">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="is_default" style="color: silver;font-weight: normal;font-size: 12px;">Make This Address Default</label>
                                            <select id="is_default" name="is_default" class="form-control">
                                                <option value="N">No</option>
                                                <option value="Y">Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                <input type="submit" name="addnewaddress" value="Save" class="btn btn-primary"/>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
                
            <!-- Main Footer -->
            <?php include 'footer.php'; ?>    

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>             
        <script>
            $("#country, #countryforaddress").change(function (e) {
                e.preventDefault();
                var id = $(this).attr('id');
                var country_id = $(this).val();
                var action = "<?= $sys['site_url'] ?>/requests.php?action=get-states&country_id=" + country_id;
                
                $.ajax({
                    type: 'POST',
                    url: action,
                    data: null,
                    /*THIS MUST BE DONE FOR FILE UPLOADING*/
                    contentType: false,
                    processData: false,
                }).done(function (data) {
                    if (data.code === '<?= SUCCESS_RESPOSE_CODE ?>') {
                        if(id === "country") {
                            $("#state").html(data.html);
                        }
                        if(id === "countryforaddress") {
                            $("#stateforaddress").html(data.html);
                        }
                    }
                }).fail(function (data) {
                    //any message
                });
            });
            //load transactions for first time
            function loadTransactions() {
                var data = new FormData();
                data.append("user_id", "<?= $user['id'] ?>");
                $.ajax({
                    type: 'POST',
                    url: "<?= $sys['site_url'] ?>/requests.php?action=get-user-transactions&page=" + 1,
                    data: data,
                    processData: false,
                    contentType: false,
                }).done(function (data) {
                    if(data.code === '0') {
                        var transactions = "";
                        var from = data.items > 0 ? (data.page * 15 - 15 + 1) : 0;
                        var to = from === 0 ? 0 : (from + data.transactions.length - 1);
                        for(var i = 0; i < data.transactions.length; i++) {
                            transactions += '<tr>'
                                    + '<td>' + data.transactions[i].txn_datetime + '</td>'
                                    + '<td>' + data.transactions[i].credit + '</td>'
                                    + '<td>' + data.transactions[i].debit + '</td>'
                                    + '<td>' + data.transactions[i].balance + '</td>'
                                    + '<td>' + data.transactions[i].comments + '</td>'
                                    + '<td>' + data.transactions[i].status + '</td>'
                                    + '</tr>';
                        }
                        $('#transactionsTBody').html(transactions);
                        var pages = '<li><a href="#" page="' + (parseInt(data.page)-1) + '">«</a></li>';
                        for(var i = 1; i <= data.pages; i++) {
                            pages += '<li class="'+(data.page == i ? 'active' : '' )+'"><a href="#" page="' + i + '">' + i + '</a></li>';
                        }
                        pages += '<li><a href="#" page="' + (parseInt(data.page)+1) + '">»</a></li>';
                        $("#pagination").html(pages);
                        var entries = 'Showing ' + from + ' to ' + to + ' of ' + data.items + ' entries';
                        $("#entries").html(entries);
                    }
                }).fail(function (data) {
                    //any message
                });
            }
            $("#pagination a").on("click", function (e) {
                e.preventDefault();
                
                var page = $(this).attr("page");
                var action = "<?= $sys['site_url'] ?>/requests.php?action=get-user-transactions&page=" + page;
                var data = new FormData();
                data.append("user_id", "<?= $user['id'] ?>");
                
                $.ajax({
                    type: 'POST',
                    url: action,
                    data: data,
                    processData: false,
                    contentType: false,
                }).done(function (data) {
                    if(data.code === '0') {
                        var transactions = "";
                        var from = data.page * 15 + 1;
                        var to = from + data.transactions.length;
                        for(var i = 0; i < data.transactions.length; i++) {
                            transactions += '<tr>'
                                    + '<td>' + data.transactions[i].txn_datetime + '</td>'
                                    + '<td>' + data.transactions[i].credit + '</td>'
                                    + '<td>' + data.transactions[i].debit + '</td>'
                                    + '<td>' + data.transactions[i].balance + '</td>'
                                    + '<td>' + data.transactions[i].comments + '</td>'
                                    + '<td>' + data.transactions[i].status + '</td>'
                                    + '</tr>';
                        }
                        $('#transactionsTBody').html(transactions);
                        var entries = 'Showing ' + from + ' to ' + to + ' of ' + data.items + ' entries';
                        $("#entries").html(entries);
                    }
                }).fail(function (data) {
                    //any message
                });
            });
            function showHidePassword() {
                if($("#password").attr("type") === "password") {
                    $("#password").attr("type", "text")
                } else {
                    $("#password").attr("type", "password")
                }
                return false;
            }
            //some automation
            loadTransactions();
        </script>
    </body>
</html>