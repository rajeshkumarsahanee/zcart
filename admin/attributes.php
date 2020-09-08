<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(ATTRIBUTES_SPECIFICATIONS_SECTION, getUserLoggedId())) {
   header("location: dashboard.php"); 
}

//Delete Attribute
if (isset($_GET['del']) && isUserHavePermission(ATTRIBUTES_SPECIFICATIONS_SECTION, getUserLoggedId())) {
    $attr = getAttribute(filter_var(trim($_GET['del']), FILTER_SANITIZE_NUMBER_INT));
    $attr['status'] = "T";
    if (updateAttribute($attr)) {
        echo "<script>alert('Deleted successfully'); location.href='attributes.php';</script>";
    } else {
        echo "<script>alert('Cannot be deleted'); location.href='attributes.php';</script>";
    }
}

$attributes = getAttributes();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>List Attributes - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
        <style>
            #attributes {
                margin-bottom: 0;
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
                        Manage Attributes
                        <small><a class="btn btn-default btn-sm" href="attribute-add.php">Add New</a></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Catalog</li>
                        <li class="active">Attributes</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">                        
                        <div class="col-md-12">
                            <div class="box">
                                <div class="table-responsive">
                                    <table id="attributes" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th width="50px">S.No</th>
                                                <th>Name</th>                                                
                                                <th>Display Order</th>                                               
                                                <th>Action</th>                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $i = 1;
                                            foreach ($attributes as $attribute) { ?>
                                                <tr>
                                                    <td><?= $i++; ?></td>
                                                    <td><?= $attribute['name']; ?></td>                                                                                                        
                                                    <td><?= $attribute['display_order']; ?></td>                                                    
                                                    <td>
                                                        <div class='btn-group'>
                                                            <?php if (isUserHavePermission(ATTRIBUTES_SPECIFICATIONS_SECTION, getUserLoggedId())) { ?>
                                                            <a class='btn btn-sm btn-primary' href="attribute-edit.php?id=<?= $attribute['id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                                            <a class='btn btn-sm btn-danger' href="attributes.php?del=<?= $attribute['id']; ?>" onclick="return confirm('Are you sure you want to delete this attribute?')" title="Delete"><i class="fa fa-trash"></i></a>
                                                            <?php } ?>
                                                        </div>
                                                    </td>                                                    
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th width="50px">S.No</th>
                                                <th>Name</th>                                                
                                                <th>Display Order</th>                                               
                                                <th>Action</th>                                                
                                            </tr>
                                        </tfoot>
                                    </table>
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
        <script type="text/javascript">
            $(function () {
                $('#attributes').dataTable({
                    "bPaginate": true,
                    "bLengthChange": true,
                    "bFilter": true,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });
            });
        </script>
    </body>
</html>