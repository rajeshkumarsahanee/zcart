<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(FILTERS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}


//Delete Category
if (isset($_GET['del']) && isUserHavePermission(FILTERS_SECTION, getUserLoggedId())) {    
    $fltr = getFilter(filter_var(trim($_GET['del']), FILTER_SANITIZE_NUMBER_INT));
    if (updateFilter($fltr)) {
        echo "<script>alert('Deleted successfully'); location.href='filters.php';</script>";
    } else {
        echo "<script>alert('Cannot be deleted'); location.href='filters.php';</script>";
    }
}
$filters = getFilters(true);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Filters - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
        <link href="<?= $sys['site_url'] ?>/admin/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
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
                        Filters
                        <small>List of created filters</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Catalog</li>
                        <li class="active">Filters</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">                        
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">List of product filters</h3>
                                    <div class="pull-right">
                                        <a class="btn btn-primary btn-sm" href="filter-add.php">Add Filter</a>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">                                                                        
                                    <div class="">
                                        <table id="filtersT" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>                                                
                                                <th>Name</th>                                                                                                 
                                                <th>Display Order</th>
                                                <th width="110px">Action</th>                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($filters as $filter) {
                                                ?>
                                                <tr>                                                    
                                                    <td><?= $filter['group_name'] ?></td>
                                                    <td><?= $filter['group_display_order'] ?></td>                                                                                                        
                                                    <td>
                                                        <div class='btn-group'>
                                                            <?php if (isUserHavePermission(FILTERS_SECTION, getUserLoggedId())) { ?>
                                                            <a class='btn btn-sm btn-primary' href="<?= $sys['site_url'].'/admin/filter-edit.php?id='.$filter['group_id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>                                                            
                                                            <a class='btn btn-sm btn-danger' href="<?= 'filters.php?del='.$filter['group_id']; ?>" onclick="return confirm('Are you sure you want to delete this filter?')" title="Delete"><i class="fa fa-trash"></i></a>
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
            $(function () {
                $('#filtersT').dataTable({
                    "bPaginate": true,
                    "bLengthChange": true,
                    "bFilter": true,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });
            });            
        </script>        
        <!-- Modal -->        
    </body>
</html>