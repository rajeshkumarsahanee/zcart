<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(PRODUCT_OPTIONS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

$addmsg = "";
//Add Product Options
if (isset($_POST['name']) && isset($_POST['display_order']) && isUserHavePermission(PRODUCT_OPTIONS_SECTION, getUserLoggedId())) {
    $option['type'] = filter_var(trim($_POST['type']), FILTER_SANITIZE_STRING);
    $option['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $option['display_order'] = filter_var(trim($_POST['display_order']), FILTER_SANITIZE_NUMBER_INT);
    $option['status'] = "A";

    $values = array();
    if (isset($_POST['option_values'])) {
        foreach ($_POST['option_values'] as $key => $value) {
            $values[] = array("value" => $value, "display_order" => $_POST['display_orders'][$key]);
        }
    }
    $option['values'] = $values;

    if ($option['name'] == '') {
        $addmsg = '<div class="alert alert-danger">Please enter option name</div>';
    } else {
        $addmsg = '<div class="alert alert-success">Product Options added successfully!</div>';
        if (!addOption($option)) {
            $addmsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Add Product Options - Admin</title>
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
                        Add Product Options
                        <small>Add new filter</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Catalog</li>
                        <li class="active"><a href="<?= $sys['site_url'] ?>/admin/product-options.php">Product Options</a></li>
                        <li class="active">Add Product Options</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Add Product Options</h3>
                                    <div class="btn-group pull-right" data-toggle="btn-toggle">

                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data">                                            
                                        <div class="form-group">
                                            <label>Type</label>
                                            <select id="type" name="type" class="form-control" required>
                                                <option value="">Please Choose</option>
                                                <?php foreach (getOptionTypes() as $ot) { ?>
                                                    <option value="<?= $ot ?>"><?= $ot ?></option>
                                                <?php } ?>
                                            </select> 
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" required>
                                        </div>                                            
                                        <div class="form-group">
                                            <label>Display Order</label>
                                            <input type="number" class="form-control" name="display_order" id="display_order" placeholder="Display Order">
                                        </div>
                                        <table id="option_values_table" class="table" style="display: none;">
                                            <thead>
                                                <tr>
                                                    <th>Product Options Name</th>
                                                    <th colspan="2">Display Order</th>
                                                </tr>
                                            </thead>
                                            <tbody id="option_values_tbody">

                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td></td>                                                        
                                                    <td><a class="btn btn-primary btn-sm pull-right" href="javascript:addRow()"><i class="fa fa-plus"></i></a></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <hr>
                                        <?php echo $addmsg; ?>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </form>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>                      
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>   

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>

        <script>
            $("#type").change(function () {
                var selected_type = $('option:selected', ).val();
                if (selected_type === "Select/Listbox/Dropdown" || selected_type === "Radio" || selected_type === "Checkbox") {
                    $("#option_values_table").show();
                } else {
                    $("#option_values_table").hide();
                    $("#option_values_tbody tr").remove();
                }
            });

            function addRow() {
                var trid = "tr" + $("#option_values_tbody tr").length;
                var item = '<tr id="' + trid + '">'
                        + '<td><input type="text" name="option_values[]" class="form-control"/></td>'
                        + '<td><input type="number" name="display_orders[]" class="form-control"/></td>'
                        + '<td><a class="btn btn-danger btn-sm pull-right" href="javascript:deleteRow(\'' + trid + '\')"><i class="fa fa-minus"></i></a></td>'
                item += '</tr>';
                $('#option_values_tbody').append(item);
            }

            function deleteRow(trid) {
                $("#" + trid).remove();
            }
        </script>
    </body>
</html>