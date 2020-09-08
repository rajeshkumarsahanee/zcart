<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(ATTRIBUTES_SPECIFICATIONS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

if(isset($_REQUEST['id']) && trim($_REQUEST['id']) != ''){
    $attribute = getAttribute(trim($_REQUEST['id']));
    if($attribute == null){
        header("location: attributes.php");
    }
} else {
    header("location: attributes.php");
}
$errormsg = "";

//Add Term
if (isset($_POST['attributetermname']) && isUserHavePermission(ATTRIBUTES_SPECIFICATIONS_SECTION, getUserLoggedId())) {
    $attributetermname = filter_var(trim($_POST['attributetermname']), FILTER_SANITIZE_STRING);   
    if(!in_array($attributetermname, $attribute['termsR'])){
        $attrterms = $attribute['termsR'];
        array_push($attrterms, $attributetermname);
        $attribute['termsR'] = $attrterms;
    }
    if ($attributetermname == '') {
        $errormsg = '<div class="alert alert-danger">Please enter name</div>';
    } else {
        if (!updateAttribute($attribute)) {
            $errormsg = '<div class="alert alert-danger">'.$queryerrormsg.'</div>';
        } else {
            header("location: attributes-terms.php?id=".$attribute['id']);
        }
    }
}

//Delete Term
if (isset($_GET['id']) && isset($_GET['term']) && isUserHavePermission(ATTRIBUTES_SPECIFICATIONS_SECTION, getUserLoggedId())) {        
    if (deleteAttributeTerm(filter_var(trim($_GET['id']), FILTER_SANITIZE_NUMBER_INT),trim($_GET['term']))) {
        echo "<script>alert('Deleted successfully'); location.href='attributes-terms.php?id=".$attribute['id']."';</script>";
    } else {
        echo "<script>alert('Cannot delete'); location.href='attributes-terms.php?id=".$attribute['id']."';</script>";
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>List Attributes Terms - Admin</title>
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
                        Attributes Terms
                        <small>List of created attributes</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active">List Attribute</li>
                        <li class="active">Attribute Terms</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Add New <?= $attribute['name']; ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post">
                                        <div class="form-group">
                                            <input type="hidden" name="id" value="<?= $attribute['id']; ?>"/>
                                            <label for="attributetermname"><?= $attribute['name']; ?> Name</label>
                                            <input type="text" class="form-control" name="attributetermname" id="attributetermname" placeholder="Enter Name">
                                        </div>                                        
                                        <?php echo $errormsg; ?>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </form>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                        <div class="col-md-8">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">List of terms</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">                                    
                                    <table id="terms" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>                                                
                                                <th>Name</th>
                                                <?php if(isUserHavePermission(ATTRIBUTES_SPECIFICATIONS_SECTION, getUserLoggedId())) { ?>
                                                <th>Action</th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            foreach ($attribute['termsR'] as $term) {
                                                if(trim($term) == ''){
                                                    continue;
                                                }
                                                ?>
                                                <tr>                                                    
                                                    <td><?php echo $term; ?></td>
                                                    <?php if(isUserHavePermission(ATTRIBUTES_SPECIFICATIONS_SECTION, getUserLoggedId())) { ?>
                                                    <td>                                                        
                                                        <a class='btn btn-sm btn-danger' href="attributes-terms.php?id=<?= $attribute['id'] ?>&term=<?= $term ?>" onclick="return confirm('Are you sure you want to delete this term?')" title="Delete"><i class="fa fa-trash"></i></a>
                                                    </td>
                                                    <?php } ?>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>                                                
                                                <th>Name</th>
                                                <?php if(isUserHavePermission(ATTRIBUTES_SPECIFICATIONS_SECTION, getUserLoggedId())) { ?>
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
        <script type="text/javascript">
            $(function () {
                $('#terms').dataTable({
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