<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
if (!isset($_REQUEST['id']) || !isUserHavePermission(MANAGE_BUYERS_SELLERS_SECTION, getUserLoggedId())) {
    header("location: customers.php");
}

$updatemsg = "";
//Update User Address
if (isset($_POST['update']) && isUserHavePermission(MANAGE_BUYERS_SELLERS_SECTION, getUserLoggedId())) {
    $address['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $tmpaddress = getUserAddress($address['id']);
    $address['user_id'] = $tmpaddress['user_id'];
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

    if (updateUserAddress($address)) {
        $updatemsg = '<div class="alert alert-success">Address Updated Successfully</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
    }
}

if (isset($_REQUEST['id']) && trim($_REQUEST['id']) != '') {
    $address = getUserAddress(trim($_REQUEST['id']));
    if ($address == null) {
        header("location: customers.php");
    }
} else {
    header("location: customers.php");
}
$countries = getCountries(array('id', 'name'), array(), 0, -1);
$states = getStates(array('id', 'name'), array('country_id' => $address['country']));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Customer Address - Admin</title>
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
                        Edit Customer Address
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="dashboard"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li><a href="customers.php">Buyers/Sellers</a></li>
                        <li><a href="#">Edit Customer</a></li>
                        <li class="active"><a href="#">Edit Customer Address</a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="">
                        <!-- form start -->
                        <form role="form" action="" method="post">
                            <div class="box">
                                <div class="box-body">
                                    <?= $updatemsg ?>
                                    <input type="hidden" name="id" value="<?= $address['id'] ?>"/>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Full Name*</label>
                                                <input type="text" class="form-control" name="name" value="<?= $address['name'] ?>" id="name" placeholder="Full Name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                            
                                                <label for="name">Mobile*</label>
                                                <input type="text" class="form-control" name="mobile" value="<?= $address['mobile'] ?>" id="mobile" placeholder="Mobile" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="address">Address*</label>
                                                <input type="text" class="form-control" name="address" value="<?= $address['address'] ?>" id="address" placeholder="Address" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="locality">Locality*</label>
                                                <input type="text" class="form-control" name="locality" value="<?= $address['locality'] ?>" id="locality" placeholder="Locality" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="countryforaddress">Country*</label>
                                                <select id="countryforaddress" name="country" class="form-control">
                                                    <option value="">Select</option>
                                                    <?php foreach ($countries as $c) { ?>
                                                        <option value="<?= $c['id'] ?>" <?= $address['country'] == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="stateforaddress">State*</label>
                                                <select id="stateforaddress" name="state" class="form-control">
                                                    <option value="">Select</option>
                                                    <?php foreach ($states as $s) { ?>
                                                        <option value="<?= $s['id'] ?>" <?= $address['state'] == $s['id'] ? 'selected' : '' ?>><?= $s['name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="city">City*</label>
                                                <input type="text" class="form-control" id="city" name="city" value="<?= $address['city'] ?>" placeholder="City/District/Town" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pincode">Pincode*</label>
                                                <input type="text" class="form-control" name="pincode" value="<?= $address['pincode'] ?>" id="pincode" placeholder="Pincode" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="landmark">Landmark</label>
                                                <input type="text" class="form-control" name="landmark" value="<?= $address['landmark'] ?>" id="landmark" placeholder="Landmark (Optional)"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Phone</label>
                                                <input type="text" class="form-control" name="phone" value="<?= $address['phone'] ?>" id="phone" placeholder="Alternate Phone (Optional)"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="address_type">Address Type</label>
                                                <select id="address_type" name="address_type" class="form-control">
                                                    <option value="HOME" <?= $address['address_type'] == 'HOME' ? 'selected' : '' ?>>Home</option>
                                                    <option value="OFFICE" <?= $address['address_type'] == 'OFFICE' ? 'selected' : '' ?>>Office</option>
                                                    <option value="OTHER" <?= $address['address_type'] == 'OTHER' ? 'selected' : '' ?>>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="is_default">Default</label>
                                                <select id="is_default" name="is_default" class="form-control">
                                                    <option value="N" <?= $address['is_default'] == 'N' ? 'selected' : '' ?>>No</option>
                                                    <option value="Y" <?= $address['is_default'] == 'Y' ? 'selected' : '' ?>>Yes</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input type="submit" name="update" value="Update" class="btn btn-primary"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div><!-- /.box -->

                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>    

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>             
        <script>
            $("#countryforaddress").change(function (e) {
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
                        $("#stateforaddress").html(data.html);
                    }
                }).fail(function (data) {
                    //any message
                });
            });
        </script>
    </body>
</html>