<?php require_once 'check_login_status.php'; ?>
<?php 
if(isset($_REQUEST['del'])) {
    if(Sys_deleteAffiliate(trim($_REQUEST['del']))){
        echo '<script>alert("Affiliate account deleted successfully!");</script>';
    }
}
$affiliates = Sys_getAffiliates();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>List Affiliates - Admin</title>
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
                        List Affiliates
                        <small>List of affiliates</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active">Affiliates</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Affiliates</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">                            
                            <table class="table table-bordered table-striped" id='affiliates'>
                                <thead>
                                    <tr>
                                        <th width="50px">ID</th>
                                        <th>Name</th>
                                        <th>Affiliate Id</th>
                                        <th>Tracking Id</th>                                        
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($affiliates as $seller) { ?>
                                        <tr>
                                            <td><?php echo $seller['id']; ?></td>
                                            <td><?php echo $seller['name']; ?></td>                                            
                                            <td><?php echo $seller['affiliate_id']; ?></td>                                            
                                            <td><?php echo $seller['tracking_id']; ?></td>
                                            <td><?php echo $seller['status']; ?></td>
                                            <td>
                                                <div class='btn-group'>
                                                    <?php if (isUserHavePermission(AFFILIATE_SECTION, EDIT_PERMISSION)) { ?>
                                                        <a class='btn btn-sm btn-primary' href="javascript:editSeller('<?php echo $seller['id']; ?>','<?php echo $seller['name']; ?>','<?php echo $seller['affiliate_id']; ?>','<?php echo $seller['tracking_id']; ?>','<?php echo $seller['status']; ?>')" title="Edit"><i class="fa fa-pencil"></i></a>
                                                    <?php } ?>
                                                        <?php if (isUserHavePermission(AFFILIATE_SECTION, DELETE_PERMISSION)) { ?>
                                                        <a class='btn btn-sm btn-danger' href="<?php echo $sys['config']['site_url'].'/admin/affiliates?del='.$seller['id']; ?>" onclick="return confirm('Are you sure you want to delete this affiliate account?')" title="Delete"><i class="fa fa-trash"></i></a>
                                                    <?php } ?>                                                    
                                                </div>
                                            </td>                                                    
                                        </tr>
                                <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th width="50px">ID</th>
                                        <th>Name</th>
                                        <th>Affiliate Id</th>
                                        <th>Tracking Id</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->

                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>    

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>        
        <script type="text/javascript">
            $(function () {
                    $('#affiliates').dataTable({
                    "bPaginate": true,
                    "bLengthChange": true,
                    "bFilter": true,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });             
            });

            function editSeller(id, name, affiliateid, trackingid, activeid) {
                $("#seller_id").val(id);
                $("#seller_name").val(name);
                $("#affiliate_id").val(affiliateid);
                $("#tracking_id").val(trackingid);
                $("#activeid").val(activeid);
                $("#editModal").modal("show");
            }    
            
            $(function(){
                $("#suform").submit(function(e){                  
                    e.preventDefault();
                    var action = $(this).attr('action');
                    var data = $(this).serialize();
                    $.ajax({
                        type: 'POST',
                        url: action,
                        data: data
                    }).done(function(data){
                        $("#msg").html(data);
                    }).fail(function(data){
                        //any message
                    });                
                });
            });
        </script>        
        <!-- Modal -->
        <div id="editModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Seller</h4>
                    </div>
                    <div class="modal-body">                        
                        <div class="box-body">
                            <form id="suform" role="form" method="post" action="<?php echo $sys['config']['site_url'].'/requests.php?f=affiliate_update'; ?>">
                                <input id="seller_id" type="hidden" name="seller_id"/>
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Attribute Name</label>
                                    <input id="seller_name" type="text" name="seller_name" class="form-control" placeholder="Enter Seller Name..." required/>
                                </div>                    
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Affiliate ID</label>
                                    <input id="affiliate_id" type="text" name="affiliate_id" class="form-control" placeholder="Enter Affiliate Id..."/>
                                </div>                    
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Tracking ID</label>
                                    <input id="tracking_id" type="text" name="tracking_id" class="form-control" placeholder="Enter Tracking Id..."/>
                                </div>
                                <!-- select -->
                                <div class="form-group">
                                    <label>Active</label>
                                    <select id="activeid" name="active" class="form-control">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>                                
                                <div id="msg"></div>                                
                                <div class="box-footer">
                                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div><!-- /.box-body -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
    </body>
</html>