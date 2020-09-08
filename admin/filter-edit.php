<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(FILTERS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

$updatemsg = "";
//Update Filter
if (isset($_POST['name']) && isset($_POST['display_order']) && isUserHavePermission(FILTERS_SECTION, getUserLoggedId())) {
    $filter['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $filter['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $filter['display_order'] = filter_var(trim($_POST['display_order']), FILTER_SANITIZE_NUMBER_INT);        
    $filter['status'] = "A";
    
    $filters = array();
    if (isset($_POST['f_names'])) {
        foreach ($_POST['f_names'] as $key => $value) {
            $filters[] = array("id" => $_POST['f_ids'][$key],"name" => $value, "display_order" => $_POST['f_display_orders'][$key], "status" => "A");
        }
    }
    $filter['filters'] = $filters;
    
    if ($filter['name'] == '') {
        $updatemsg = '<div class="alert alert-danger">Please enter filter name</div>';
    } else {        
        $updatemsg = '<div class="alert alert-success">Filter updated successfully!</div>';
        if (!updateFilter($filter)) {
            $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

$filter = getFilter($_REQUEST['id']);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Filter - Admin</title>
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
                        Edit Filter
                        <small>Edit filter</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Catalog</li>
                        <li class="active"><a href="<?php echo $sys['config']['site_url'] ?>/admin/filters.php">Filters</a></li>
                        <li class="active">Edit Filter</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Edit Filter</h3>
                                    <div class="btn-group pull-right" data-toggle="btn-toggle">

                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data">  
                                        <input type="hidden" name="id" value="<?= $filter['id'] ?>"/>
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" name="name" id="name" value="<?= $filter['name'] ?>" placeholder="Enter Name" required>
                                        </div>                                            
                                        <div class="form-group">
                                            <label>Display Order</label>
                                            <input type="number" class="form-control" name="display_order" id="display_order" value="<?= $filter['display_order'] ?>" placeholder="Display Order">
                                        </div>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Filter Name</th>
                                                    <th colspan="2">Display Order</th>
                                                </tr>
                                            </thead>
                                            <tbody id="filter_names_tbody">
                                                <?php
                                                $i = 1;
                                                foreach ($filter['filters'] as $f) {
                                                    ?>
                                                    <tr id="tr<?= $i ?>">
                                                        <td>
                                                            <input type="hidden" name="f_ids[]" value="<?= $f['id'] ?>"/>
                                                            <input type="text" name="f_names[]" class="form-control" value="<?= $f['name']; ?>"/>
                                                        </td>
                                                        <td><input type="number" name="f_display_orders[]" class="form-control" value="<?= $f['display_order']; ?>"/></td>
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
            function addRow() {    
                var trid = "tr" + $("#filter_names_tbody tr").length;  
                var item = '<tr id="'+trid+'">'
                        + '<td>'
                        + '<input type="hidden" name="f_ids[]" value="" />'
                        + '<input type="text" name="f_names[]" class="form-control"/>'
                        + '</td>'
                        + '<td><input type="number" name="f_display_orders[]" class="form-control"/></td>'
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