<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(COMMISSION_SETTINGS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$msg = "";

//Add Commission
if (isset($_POST['fees']) && isUserHavePermission(COMMISSION_SETTINGS_SECTION, getUserLoggedId())) {
    $commission['category_id'] = filter_var(trim($_POST['category_id']), FILTER_SANITIZE_NUMBER_INT);
    $commission['shop_id'] = filter_var(trim($_POST['shop_id']), FILTER_SANITIZE_NUMBER_INT);
    $commission['product_id'] = filter_var(trim($_POST['product_id']), FILTER_SANITIZE_NUMBER_INT);
    $commission['fees'] = filter_var(trim($_POST['fees']), FILTER_SANITIZE_STRING);
    
    if($commission['shop_id'] != 0) {
        $commission['category_id'] = 0;
    }
    if($commission['product_id'] != 0) {
        $commission['category_id'] = 0;
        $commission['shop_id'] = 0;
    }
    if ($commission['fees'] == '') {
        $msg = '<div class="alert alert-danger">Commission percent field is required!</div>';
    } else {        
        $msg = '<div class="alert alert-success">Commission rate added successfully!</div>';
        if (!addCommission($commission)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

//Delete Commission
if (isset($_GET['del']) && isUserHavePermission(COMMISSION_SETTINGS_SECTION, getUserLoggedId())) {    
    $id = filter_var(trim($_GET['del']), FILTER_SANITIZE_NUMBER_INT);
    if (deleteCommission($id)) {
        echo "<script>alert('Deleted successfully'); location.href='settings-commission.php';</script>";
    } else {
        echo "<script>alert('Cannot be deleted'); location.href='settings-commission.php';</script>";
    }
}
$filters = array('status' => 'A');
$commissions = getCommissions(array(), $filters);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Commissions - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
        <link href="<?= $sys['site_url'] ?>/admin/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <style>
            .custom-nav {    
                position: absolute;
                width: 96%;
                z-index: 1;
                background: white;
            }
        </style>
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
                        Commissions
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li class="active">Commissions</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Add New Commission</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data"> 
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input id="category_name" type="text" class="form-control" name="category_name" placeholder="Category" autocomplete="off"/>
                                                    <input id="category_id" type="hidden" class="form-control" name="category_id"/>
                                                    <ul id="categorieslist" class="nav custom-nav">

                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input id="shop_name" type="text" class="form-control" name="shop_name" placeholder="Shop" autocomplete="off"/>
                                                    <input id="shop_id" type="hidden" class="form-control" name="shop_id" required/>
                                                    <ul id="shopslist" class="nav custom-nav">

                                                    </ul>
                                                </div>  
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input id="product_name" type="text" class="form-control" name="product_name" placeholder="Product" autocomplete="off"/>
                                                    <input id="product_id" type="hidden" class="form-control" name="product_id" required/>
                                                    <ul id="productslist" class="nav custom-nav">

                                                    </ul>
                                                </div> 
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <input id="fees" type="text" class="form-control" name="fees" placeholder="Commission Percentage" autocomplete="off" required/>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <input type="submit" name="add" value="Add" class="btn btn-success"/>
                                            </div>
                                        </div>                                                                                 
                                        <?= $msg ?>
                                    </form>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-body">                                                                        
                                    <div class="">
                                        <table id="commissionsT" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>      
                                                    <th>Category</th>
                                                    <th>Vendor</th>
                                                    <th>Product</th>
                                                    <th>Fees[%]</th>
                                                    <th width="100px">Action</th>                                                
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($commissions as $c) {
                                                    ?>
                                                    <tr>                                                    
                                                        <td><?= !empty($c['category_name']) ? $c['category_name'] : '-NA-' ?></td>
                                                        <td><?= !empty($c['shop_name']) ? $c['shop_name'] : '-NA-' ?></td>
                                                        <td><?= !empty($c['product_name']) ? $c['product_name'] : '-NA-' ?></td>
                                                        <td><?= $c['fees'] ?></td>
                                                        <td>
                                                            <div class='btn-group'>
                                                                <?php if (isUserHavePermission(REASONS_SECTION, getUserLoggedId()) && !$c['is_mandatory']) { ?>
                                                                    <a class='btn btn-sm btn-danger' href="<?= $sys['site_url'] . '/admin/settings-commission.php?del=' . $c['id']; ?>" onclick="return confirm('Are you sure you want to delete?')" title="Delete"><i class="fa fa-trash"></i></a>
                                                                <?php } ?>                                                                
                                                            </div>
                                                        </td>                                                    
                                                    </tr>
                                                <?php } ?>
                                            </tbody>  
                                        </table>
                                    </div>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div><!-- /.col-md-8 -->
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>    

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>
        <script src="<?= $sys['site_url'] ?>/admin/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="<?= $sys['site_url'] ?>/admin/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $('#commissionsT').dataTable({
                "bPaginate": true,
                "bLengthChange": true,
                "bFilter": true,
                "bSort": true,
                "bInfo": true,
                "bAutoWidth": false
            });
            $("#category_name").on("keyup focus", function(e){
                var q = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "<?= $sys['site_url'] ?>/requests.php?action=get-categories&q=" + q,
                    success: function (response) {
                        $("#categorieslist").html(response.html);
                        $("#categorieslist").css("border", "solid thin #d2d6de");
                        if(response.html === "") {
                            $("#categorieslist").css("border", "none");
                        }
                    }
                });
            });
            $("#categorieslist").on("click", "a", function(e){
                $("#category_name").val($(this).html());
                $("#category_id").val($(this).attr("data-id"));
                $("#categorieslist li").remove();
                $("#categorieslist").css("border", "none");
                return false;
            });
            $("#shop_name").on("keyup focus", function(e){
                var name = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "<?= $sys['site_url'] ?>/requests.php?action=get-shops&name=" + name,
                    success: function (response) {
                        $("#shopslist").html(response.html);
                        $("#shopslist").css("border", "solid thin #d2d6de");
                        if(response.html === "") {
                            $("#shopslist").css("border", "none");
                        }
                    }
                });
            });
            $("#shopslist").on("click", "a", function(e){
                $("#shop_name").val($(this).html());
                $("#shop_id").val($(this).attr("data-id"));
                $("#shopslist li").remove();
                $("#shopslist").css("border", "none");
                return false;
            });
            $("#product_name").on("keyup focus", function(e){
                var name = $(this).val();
                var shop_id = $("#shop_id").val();
                $.ajax({
                    type: "GET",
                    url: "<?= $sys['site_url'] ?>/requests.php?action=get-products&name=" + name + "&shop=" + shop_id,
                    success: function (response) {
                        $("#productslist").html(response.html);
                        $("#productslist").css("border", "solid thin #d2d6de");
                        if(response.html === "") {
                            $("#productslist").css("border", "none");
                        }
                    }
                });
            });
            $("#productslist").on("click", "a", function(e){
                $("#product_name").val($(this).html());
                $("#product_id").val($(this).attr("data-id"));
                $("#productslist li").remove();
                $("#productslist").css("border", "none");
                
                $("#category_name").val("");
                $("#category_id").val("");
                $("#shop_name").val("");
                $("#shop_id").val("");
                return false;
            });
            $(".content-wrapper").on("click", function(e){
                //clear shops list
                $("#shopslist").html("");
                $("#shopslist").css("border", "none");
                //clear shops list
                $("#categorieslist").html("");
                $("#categorieslist").css("border", "none");
                //clear brands list
                $("#productslist").html("");
                $("#productslist").css("border", "none");
            });
        </script>        
        <!-- Modal -->        
    </body>
</html>