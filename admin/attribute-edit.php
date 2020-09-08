<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isset($_REQUEST['id']) || trim($_REQUEST['id']) == '' || !isUserHavePermission(ATTRIBUTES_SPECIFICATIONS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

$updatemsg = "";

//Update Attribute
if (isset($_POST['id']) && isset($_POST['ag_name']) && isUserHavePermission(ATTRIBUTES_SPECIFICATIONS_SECTION, getUserLoggedId())) {
    $attribute['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_STRING);    
    $attribute['name'] = filter_var(trim($_POST['ag_name']), FILTER_SANITIZE_STRING);    
    $attribute['display_order'] = filter_var(trim($_POST['ag_display_order']), FILTER_SANITIZE_NUMBER_INT);
    $attribute['status'] = "A";
    
    $attributes = array();
    if (isset($_POST['a_names'])) {
        foreach ($_POST['a_names'] as $key => $value) {
            $attributes[] = array("id" => $_POST['a_ids'][$key],"name" => $value, "display_order" => $_POST['a_display_orders'][$key], "status" => "A");
        }
    }
    $attribute['attributes'] = $attributes;
    
    if ($attribute['name'] == '') {
        $updatemsg = '<div class="alert alert-danger">Please enter attribute group name</div>';
    } else {
        if (!updateAttribute($attribute)) {
            $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        } else {
            header("location: attributes.php");
        }
    }
}

$attribute = getAttribute(filter_var(trim($_REQUEST['id']), FILTER_SANITIZE_NUMBER_INT));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Attribute - Admin</title>
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
                        Edit Attribute
                        <small>update existing attribute here</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Catalog</li>
                        <li class=""><a href="attributes.php">Attributes</a></li>
                        <li class="active">Edit Attribute</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">                        
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-body">  
                                    <form method="post">
                                        <input type="hidden" name="id" value="<?= $attribute['id'] ?>"/>
                                        <div class="form-group">
                                            <label>Attribute Group Name</label>
                                            <input type="text" class="form-control" name="ag_name" id="ag_name" value="<?= $attribute['name'] ?>" placeholder="Enter Name">
                                        </div>
                                        <div class="form-group">
                                            <label>Display Order</label>
                                            <input type="number" class="form-control" name="ag_display_order" id="ag_display_order" value="<?= $attribute['display_order'] ?>" placeholder="Enter Order">
                                        </div>                                                                          
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Attribute Name</th>
                                                    <th colspan="2">Display Order</th>
                                                </tr>
                                            </thead>
                                            <tbody id="attribute_names_tbody">
                                                <?php 
                                                $i = 1;
                                                foreach($attribute['attributes'] as $a) { ?>
                                                <tr id="tr<?php echo $i; ?>">
                                                    <td>
                                                        <input type="hidden" name="a_ids[]" value="<?= $a['id'] ?>"/>
                                                        <input type="text" name="a_names[]" class="form-control" value="<?= $a['name'] ?>"/>
                                                    </td>
                                                    <td><input type="number" name="a_display_orders[]" class="form-control" value="<?= $a['display_order'] ?>"/></td>
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
        <script type="text/javascript">
            function addRow() {    
                var trid = "tr" + $("#attribute_names_tbody tr").length;  
                var item = '<tr id="'+trid+'">'
                        + '<td>'
                        + '<input type="hidden" name="a_ids[]" value="" />'
                        + '<input type="text" name="a_names[]" class="form-control"/>'
                        + '</td>'
                        + '<td><input type="number" name="a_display_orders[]" class="form-control"/></td>'
                        + '<td><a class="btn btn-danger btn-sm pull-right" href="javascript:deleteRow(\''+trid+'\')"><i class="fa fa-minus"></i></a></td>'
                item += '</tr>';
                $('#attribute_names_tbody').append(item);                 
            }
            
            function deleteRow(trid) {
                $("#"+trid).remove();
            }
        </script>
        
    </body>
</html>