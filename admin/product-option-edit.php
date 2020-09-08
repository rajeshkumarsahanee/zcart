<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(PRODUCT_OPTIONS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

$updatemsg = "";
//Update Product Option
if (isset($_POST['name']) && isset($_POST['display_order']) && isUserHavePermission(PRODUCT_OPTIONS_SECTION, getUserLoggedId())) {
    $option['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $option['type'] = filter_var(trim($_POST['type']), FILTER_SANITIZE_STRING);
    $option['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $option['display_order'] = filter_var(trim($_POST['display_order']), FILTER_SANITIZE_NUMBER_INT);
    $option['status'] = "A";

    $values = array();
    if (isset($_POST['o_values'])) {
        foreach ($_POST['o_values'] as $key => $value) {
            $values[] = array("id" => $_POST['o_ids'][$key], "value" => $value, "display_order" => $_POST['o_display_orders'][$key], "status" => "A");
        }
    }
    $option['values'] = $values;

    if ($option['name'] == '') {
        $updatemsg = '<div class="alert alert-danger">Please enter filter name</div>';
    } else {
        $updatemsg = '<div class="alert alert-success">Filter updated successfully!</div>';
        if (!updateOption($option)) {
            $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}

$option = getOption($_REQUEST['id']);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Product Option - Admin</title>
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
                        Edit Product Option
                        <small>Edit filter</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Catalog</li>
                        <li class="active"><a href="<?= $sys['config']['site_url'] ?>/admin/product-options">Product Options</a></li>
                        <li class="active">Edit Product Option</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Edit Product Option</h3>
                                    <div class="btn-group pull-right" data-toggle="btn-toggle">

                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data">  
                                        <input type="hidden" name="id" value="<?= $option['id'] ?>"/>
                                        <div class="form-group">
                                            <label>Type</label>
                                            <select id="type" name="type" class="form-control" required>
                                                <option value="">Please Choose</option>
                                                <?php foreach (getOptionTypes() as $ot) { ?>
                                                    <option value="<?= $ot ?>" <?= $ot == $option['type'] ? 'selected' : '' ?>><?= $ot ?></option>
                                                <?php } ?>
                                            </select> 
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" name="name" id="name" value="<?= $option['name'] ?>" placeholder="Enter Name" required>
                                        </div>                                            
                                        <div class="form-group">
                                            <label>Display Order</label>
                                            <input type="number" class="form-control" name="display_order" id="display_order" value="<?= $option['display_order'] ?>" placeholder="Display Order">
                                        </div>
                                        <table id="option_values_table" class="table" style="display:none;">
                                            <thead>
                                                <tr>
                                                    <th>Product Option Value</th>
                                                    <th colspan="2">Display Order</th>
                                                </tr>
                                            </thead>
                                            <tbody id="option_values_tbody">
                                                <?php
                                                $i = 1;
                                                foreach ($option['values'] as $v) {
                                                    ?>
                                                    <tr id="tr<?php echo $i; ?>">
                                                        <td>
                                                            <input type="hidden" name="o_ids[]" value="<?= $v['id'] ?>"/>
                                                            <input type="text" name="o_values[]" class="form-control" value="<?= $v['option_value'] ?>"/>
                                                        </td>
                                                        <td><input type="number" name="o_display_orders[]" class="form-control" value="<?= $v['display_order'] ?>"/></td>
                                                        <td><a class="btn btn-danger btn-sm pull-right" href="javascript:deleteRow('tr<?= $i++ ?>')"><i class="fa fa-minus"></i></a></td>
                                                    </tr>
                                                <?php } ?>
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
                                        <?php echo $updatemsg; ?>
                                        <button type="submit" class="btn btn-primary">Update</button>
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
            $("#type").change(function(){
                var selected_type = $('option:selected', ).val();
                if(selected_type === "Select/Listbox/Dropdown" || selected_type === "Radio" || selected_type === "Checkbox") {
                    $("#option_values_table").show();                    
                } else {
                    $("#option_values_table").hide();
                    $("#option_values_tbody tr").remove();
                }
            });

            function addRow() {    
                var trid = "tr" + $("#option_values_tbody tr").length;  
                var item = '<tr id="'+trid+'">'
                    + '<td>'
                    + '<input type="hidden" name="o_ids[]" value="" />'
                    + '<input type="text" name="o_values[]" class="form-control"/>'
                    + '</td>'
                    + '<td><input type="number" name="o_display_orders[]" class="form-control"/></td>'
                    + '<td><a class="btn btn-danger btn-sm pull-right" href="javascript:deleteRow(\''+trid+'\')"><i class="fa fa-minus"></i></a></td>'
                    item += '</tr>';
                $('#option_values_tbody').append(item);                 
            }

            function deleteRow(trid) {
                $("#"+trid).remove();
            }

            $("#type").trigger("change");
        </script>
    </body>
</html>