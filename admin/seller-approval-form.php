<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(SELLER_APPROVAL_FORM_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$msg = "";

//Add Field
if (isset($_POST['fields']) && isUserHavePermission(SELLER_APPROVAL_FORM_SECTION, getUserLoggedId())) {
    
    $error = false;
    foreach($_POST['fields'] as $f) {
        if($f['type'] == '' || $f['caption'] == '') {
            $error = true;
            break;
        }
    }
    
    if ($error) {
        $msg = '<div class="alert alert-danger">Type and Caption field is required!</div>';
    } else {
        $msg = '<div class="alert alert-success">Saved Successfully!</div>';
        if (!saveConfig("SELLER_APPROVAL_FORM_FIELDS", json_encode($_POST['fields']))) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

$types = array("Date", "Date & Time", "File", "Text", "Textarea", "Time");
$fields = json_decode(getConfig("SELLER_APPROVAL_FORM_FIELDS"));
$fields = $fields != null ? $fields : array();
usort($fields, function($a, $b) {
    return $a->display_order - $b->display_order;
});
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Seller Approval Form - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
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
                        Seller Approval Form
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Buyers/Sellers</li>
                        <li class="active">Seller Approval Form</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <form class="" action="" method="post">                                                                        
                                <div class="box">
                                    <table id="commissionsT" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>      
                                                <th>Type</th>
                                                <th>Name</th>
                                                <th>Caption</th>
                                                <th>Help Text</th>
                                                <th>Required</th>
                                                <th style="width: 110px;">Display Order</th>
                                                <th style="width: 80px;">Action</th>                                                
                                            </tr>
                                        </thead>
                                        <tbody id="fieldstbody">
                                            <?php
                                            $i = 0;
                                            foreach ($fields as $f) {
                                                ?>
                                                <tr id="<?= $i ?>">                                                    
                                                    <td>
                                                        <select name="fields[<?= $i ?>][type]" class="form-control">
                                                            <option>-Select-</option>
                                                            <?php foreach ($types as $t) { ?>
                                                                <option value="<?= $t ?>" <?= $t == $f->type ? "selected" : "" ?>><?= $t ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="fields[<?= $i ?>][name]" value="<?= isset($f->name) ? $f->name : '' ?>" class="form-control" readonly=""/></td>
                                                    <td><input type="text" name="fields[<?= $i ?>][caption]" value="<?= isset($f->caption) ? $f->caption : '' ?>" class="form-control"/></td>
                                                    <td><input type="text" name="fields[<?= $i ?>][help_text]" value="<?= isset($f->help_text) ? $f->help_text : '' ?>" class="form-control"/></td>
                                                    <td>
                                                        <select name="fields[<?= $i ?>][required]" class="form-control">
                                                            <option value="Y" <?= isset($f->required) && $f->required == "Y" ? "selected" : "" ?>>Yes</option>
                                                            <option value="N" <?= isset($f->required) && $f->required == "N" ? "selected" : "" ?>>No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="fields[<?= $i ?>][display_order]" value="<?= isset($f->display_order) ? $f->display_order : '' ?>" class="form-control"/></td>
                                                    <td>
                                                        <div class='btn-group'>
                                                            <?php if (isUserHavePermission(SELLER_APPROVAL_FORM_SECTION, getUserLoggedId()) && !isset($f->is_mandatory)) { ?>
                                                                <a class='btn btn-sm btn-danger' href="javascript:deleteField(<?= $i ?>);" onclick="return confirm('Are you sure you want to delete?')" title="Delete"><i class="fa fa-minus"></i></a>
                                                            <?php } ?>                                                                
                                                        </div>
                                                    </td>                                                    
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                            ?>
                                        </tbody>  
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td><a class="btn btn-sm btn-default btn-add" href="javascript:addNewField();" title="Add"><i class="fa fa-plus"></i></a></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div><!-- /.box -->
                                <?= $msg ?>
                                <input type="submit" name="save" value="Save" class="btn btn-primary">
                            </form>
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
            function addNewField() {
                var i = $("#fieldstbody tr").length;
                var html = '<tr id="' + i + '">'
                                + '<td>'
                                    + '<select name="fields['+i+'][type]" class="form-control">'
                                        + '<option>-Select-</option>'
                                        <?php foreach($types as $t) { ?>
                                        + '<option value="<?= $t ?>"><?= $t ?></option>'
                                        <?php } ?>
                                    + '</select>'
                                + '</td>'
                                + '<td><input type="text" name="fields['+i+'][name]" class="form-control"/></td>'
                                + '<td><input type="text" name="fields['+i+'][caption]" class="form-control"/></td>'
                                + '<td><input type="text" name="fields['+i+'][help_tex]t" class="form-control"/></td>'
                                + '<td>'
                                    + '<select name="fields['+i+'][required]" class="form-control">'
                                        + '<option value="Y">Yes</option>'
                                        + '<option value="N">No</option>'
                                    + '</select>'
                                + '</td>'
                                + '<td><input type="number" name="fields['+i+'][display_order]" class="form-control"/></td>'
                                + '<td>'
                                    + '<div class="btn-group">'
                                        <?php if (isUserHavePermission(SELLER_APPROVAL_FORM_SECTION, getUserLoggedId())) { ?>
                                            + '<a class="btn btn-sm btn-danger" href="javascript:deleteField(' + i + ')" onclick="return confirm(\'Are you sure you want to delete?\')" title="Delete"><i class="fa fa-minus"></i></a>'
                                        <?php } ?>                                                                
                                    + '</div>'
                                + '</td>'                                                   
                            + '</tr>';
                    
                $("#fieldstbody").append(html);
            }
            function deleteField(id) {
                $("#" + id).remove();
                return false;
            }
        </script>        
        <!-- Modal -->        
    </body>
</html>