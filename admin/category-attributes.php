<?php require_once 'check_login_status.php'; ?>
<?php
if(isUserHavePermission(ATTRIBUTE_SECTION, VIEW_PERMISSION) || isUserHavePermission(ATTRIBUTE_SECTION, EDIT_PERMISSION)) {    
} else {
    header("location: dashboard");
}

$addmsg = "";
//add attribute
if(isset($_POST['id']) && isset($_POST['catattrids'])) {
    $category = Sys_getCategory($_POST['id']);
    $catattrids = $_POST['catattrids'];
    $catattridstmp = $category['attributesR'];
    foreach ($catattrids as $catattrid) {
        if (!in_array($catattrid, $category['attributesR'])) {            
            array_push($catattridstmp, $catattrid);
            $category['attributes'] = implode(",", $catattridstmp);            
        }
    }
    Sys_updateCategory($category);
    $addmsg = '<div class="alert alert-success">Added Successfully!</div>';
}

//delete attribute
if(isset($_GET['id']) && isset($_GET['del'])) {
    $category = Sys_getCategory($_GET['id']);
    $catattrid = trim($_GET['del']);
    if(in_array($catattrid, $category['attributesR'])){
        $catattrids = $category['attributesR'];        
        if (($key = array_search($catattrid, $catattrids)) !== false) {
            unset($catattrids[$key]);
        }
        $category['attributes'] = implode(",", $catattrids);
        Sys_updateCategory($category);
        $addmsg = '<div class="alert alert-danger">Deleted Successfully!</div>';
    }
}

if(isset($_REQUEST['id'])) {
    $category = Sys_getCategory($_REQUEST['id']);
} else {
    header("location: dashboard");
}

$existingattr = array();
$nonexistingattr = array();
$i =0; $j =0;
foreach (Sys_getAttributes() as $attribute) {
    if (in_array($attribute['id'], $category['attributesR'])) {
        $existingattr[$i++] = $attribute;        
    } else {
        $nonexistingattr[$j++] = $attribute;
    }
}
//To store existing attributes
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Category Attributes - Admin</title>
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
                        Category Attributes
                        <small>Category attributes</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class=""><a href="<?php echo $sys['config']['site_url'] ?>/categories">List Category</a></li>
                        <li class="active">Category Attribute</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <?php if(isUserHavePermission(CATEGORY_SECTION, EDIT_PERMISSION)) { ?>
                        <div class="col-md-4">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Add New Attribute to <?php echo $category['name']; ?> Category</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post">
                                        <div class="form-group">
                                            <input type="hidden" name="id" value="<?php echo $category['id']; ?>"/>
                                            <label for="catattrid">Select Attribute</label>
                                            <select id="catattrid" name="catattrids[]" class="form-control" multiple="multiple">
                                            <?php                                            
                                            foreach($nonexistingattr as $attribute){                                                
                                            ?>                                            
                                                <option value="<?php echo $attribute['id']; ?>"><?php echo $attribute['name']; ?></option>
                                            <?php } ?>    
                                            </select>
                                        </div>                                        
                                        <?php echo $addmsg; ?>
                                        <button type="submit" class="btn btn-primary">Add</button>
                                    </form>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                        <?php } ?>
                        <div class="col-md-8">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title"><?php echo $category['name']; ?> Category Attributes</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    <?php
                                    //$attributes = explode(",", $category['attributes']);                                                                        
                                    ?>
                                    <table id="attributes" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>            
                                                <th>#</th>
                                                <th>Name</th>
                                                <?php if(isUserHavePermission(CATEGORY_SECTION, EDIT_PERMISSION)) { ?>
                                                <th>Action</th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $i = 1;
                                            foreach ($existingattr as $attribute) {
                                                if($attribute['id'] == ''){
                                                    continue;
                                                }                                                
                                                ?>
                                                <tr> 
                                                    <td><?php echo $i++; ?></td>
                                                    <td><?php echo $attribute['name']; ?></td>
                                                    <?php if(isUserHavePermission(CATEGORY_SECTION, EDIT_PERMISSION)) { ?>
                                                    <td>                                                        
                                                        <a class='btn btn-sm btn-danger' href="<?php echo $sys['config']['site_url'].'/admin/category-attributes?id='.$category['id']."&del=".$attribute['id']; ?>" onclick="return confirm('Are you sure you want to delete this attribute?')" title="Delete"><i class="fa fa-trash"></i></a>
                                                    </td>
                                                    <?php } ?>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>                                                
                                                <th>Name</th>   
                                                <?php if(isUserHavePermission(CATEGORY_SECTION, EDIT_PERMISSION)) { ?>
                                                <th>Action</th>
                                                <?php } ?>
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
        <?php if(isset($deletemsg)) { echo $deletemsg; } ?>
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