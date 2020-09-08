<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(FILTERS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

$addmsg = "";
//Add Filter
if (isset($_POST['name']) && isset($_POST['display_order']) && isUserHavePermission(FILTERS_SECTION, getUserLoggedId())) {
    $filter['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);    
    $filter['display_order'] = filter_var(trim($_POST['display_order']), FILTER_SANITIZE_NUMBER_INT);
    $filter['status'] = "A";
    
    $filters = array();
    if (isset($_POST['filter_names'])) {        
        foreach ($_POST['filter_names'] as $key => $value) {
            $filters[] = array("name" => $value, "display_order" => $_POST['display_orders'][$key], "status" => "A");
        }
    }
    $filter['filters'] = $filters;
    
    if ($filter['name'] == '') {
        $addmsg = '<div class="alert alert-danger">Please enter filter name</div>';
    } else {        
        $addmsg = '<div class="alert alert-success">Filter added successfully!</div>';
        if (!addFilter($filter)) {
            $addmsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Add Filter - Admin</title>
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
                        Add Filter
                        <small>Add new filter</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Catalog</li>
                        <li class="active"><a href="<?= $sys['config']['site_url'] ?>/admin/filters.php">Filters</a></li>
                        <li class="active">Add Filter</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Add Filter</h3>
                                    <div class="btn-group pull-right" data-toggle="btn-toggle">

                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data">                                            
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" required>
                                        </div>                                            
                                        <div class="form-group">
                                            <label>Display Order</label>
                                            <input type="number" class="form-control" name="display_order" id="display_order" placeholder="Display Order">
                                        </div>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Filter Name</th>
                                                    <th colspan="2">Display Order</th>
                                                </tr>
                                            </thead>
                                            <tbody id="filter_names_tbody">

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
            function addRow() {    
                var trid = "tr" + $("#filter_names_tbody tr").length;  
                var item = '<tr id="'+trid+'">'
                        + '<td><input type="text" name="filter_names[]" class="form-control"/></td>'
                        + '<td><input type="number" name="display_orders[]" class="form-control"/></td>'
                        + '<td><a class="btn btn-danger btn-sm pull-right" href="javascript:deleteRow(\''+trid+'\')"><i class="fa fa-minus"></i></a></td>'
                item += '</tr>';
                $('#filter_names_tbody').append(item);                 
            }
            
            function deleteRow(trid) {
                $("#"+trid).remove();
            }
        </script>
    </body>
</html>