<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(MANAGE_USERS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

$config = getConfig();
$msg = "";
//Delete User
if(isset($_REQUEST['del']) && isUserHavePermission(MANAGE_USERS_SECTION, getUserLoggedId())) {
    if(deleteUser(filter_var(trim($_REQUEST['del']), FILTER_SANITIZE_NUMBER_INT))) {
        $msg = '<div class="alert alert-success">User deleted</div>';
    } else {
        $msg = '<div class="alert alert-danger">Error</div>';
    }
}
//Delete Users
if(isset($_REQUEST['action']) && trim($_REQUEST['action']) == "trash" && isUserHavePermission(MANAGE_USERS_SECTION, getUserLoggedId())) {
    if (isset($_REQUEST['users'])) {
        $count = 0;
        foreach ($_REQUEST['users'] as $uid) {
            if($uid == 1) {
                continue;
            }
            if(deleteUser(filter_var(trim($uid), FILTER_SANITIZE_NUMBER_INT))) {
                $count++;
            }
        }
        $msg = '<div class="alert alert-success">' . $count . ' users deleted</div>';
    }
}
//Change role
if(isset($_REQUEST['changeit']) && isset($_REQUEST['new_role']) && trim($_REQUEST['new_role']) <> "" && isUserHavePermission(MANAGE_USERS_SECTION, getUserLoggedId())) {
    if (isset($_REQUEST['users'])) {
        $new_role = filter_var(trim($_REQUEST['new_role']), FILTER_SANITIZE_STRING);
        $permissions = array(DASHBOARD_SECTION);
        if($new_role == "contributer") {
            $permissions[] = MANAGE_POSTS_SECTION;
        }
        if($new_role == "author") {
            $permissions[] = MANAGE_POSTS_SECTION;
            $permissions[] = MANAGE_MEDIA_SECTION;
            $permissions[] = MANAGE_PAGES_SECTION;
            $permissions[] = MANAGE_COMMENTS_SECTION;
        }
        if($new_role == "editor") {
            $permissions[] = MANAGE_POSTS_SECTION;
            $permissions[] = MANAGE_MEDIA_SECTION;
            $permissions[] = MANAGE_PAGES_SECTION;
            $permissions[] = MANAGE_COMMENTS_SECTION;
            $permissions[] = MANAGE_SETTINGS_SECTION;
        }
        if($new_role == "administrator") {
            $permissions[] = MANAGE_POSTS_SECTION;
            $permissions[] = MANAGE_MEDIA_SECTION;
            $permissions[] = MANAGE_PAGES_SECTION;
            $permissions[] = MANAGE_COMMENTS_SECTION;
            $permissions[] = MANAGE_SETTINGS_SECTION;
            $permissions[] = MANAGE_APPEARANCE_SECTION;
            $permissions[] = MANAGE_USERS_SECTION;
        }
        foreach ($_REQUEST['users'] as $uid) {
            if($uid == 1) {
                continue;
            }
            $uid = filter_var(trim($uid), FILTER_SANITIZE_NUMBER_INT);
            updateUserMeta($uid, "role", $new_role);
            updateUserMeta($uid, "permissions", implode(",", $permissions));
        }
        $msg = '<div class="alert alert-success">Roles changed</div>';
    }
}

$filters = array("status" => 'A', "with_metas" => 1);
$querystring = "";
$role = "";
if(isset($_REQUEST['role'])) {
    $filters['role'] = filter_var(trim($_REQUEST['role']), FILTER_SANITIZE_STRING);
    $role = filter_var(trim($_REQUEST['role']), FILTER_SANITIZE_STRING);
    $querystring .= "&role=" . $_REQUEST['role'];
}
if(isset($_REQUEST['q'])) {
    $filters['query'] = filter_var(trim($_REQUEST['q']), FILTER_SANITIZE_STRING);
    $querystring .= "&q=" . $_REQUEST['q'];
}
/*pagination logic start*/
$items_count = count(getUsers(array('id'), $filters, 0, -1));
$items_per_page = isset($config['items_per_page_user_admin']) ? $config['items_per_page_user_admin'] : 20;
$max_pages = intval($items_count / $items_per_page + 1);
$current_page = !isset($_REQUEST['paged']) || intval($_REQUEST['paged']) < 1 ? 1 : filter_var(trim($_REQUEST['paged']), FILTER_SANITIZE_NUMBER_INT);
if($current_page > $max_pages) {
    header("location: users.php?" . $querystring . "&paged=" . $max_pages);
    exit();
}
$offset = $items_per_page * $current_page - $items_per_page;
/*pagination logic end*/
$order_by = isset($_REQUEST['order_by']) && in_array($_REQUEST['order_by'], array("id", "username", "email")) ? filter_var(trim($_REQUEST['order_by']), FILTER_SANITIZE_STRING) : "id";
$order = isset($_REQUEST['order']) && in_array($_REQUEST['order'], array("asc", "desc")) ? filter_var(trim($_REQUEST['order']), FILTER_SANITIZE_STRING) : "DESC";
$users = getUsers(array(), $filters, $offset, $items_per_page, $order_by, $order);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Users - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
        <link rel="stylesheet" href="<?= $sys['site_url']; ?>/admin/plugins/iCheck/flat/blue.css">
        <style>
            #users {
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
                        Users
                        <small><a href="user-add.php" class="btn btn-default btn-sm">Add New</a></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active"><a href="#">Users</a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <form method="" action="">
                        <?= $msg; ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="" style="float:left">
                                    <a href="users.php" class="<?= $role == "" ? "current" : "" ?>">All<span class="count">(<?= count(getUsers(array('id'), array(), 0, -1)) ?>)</span></a> 
                                    <?php foreach($sys['roles'] as $key => $value) { ?>
                                    | <a href="users.php?role=<?= $key ?>" class="<?= $role == $key ? "current" : "" ?>"><?= $value ?><span class="count">(<?= count(getUsers(array('id'), array('role' => $key), 0, -1)) ?>)</span></a>
                                    <?php } ?>
                                </div>
                                <div class="" style="float:right">
                                    <input type="text" class="form-control" name="q" value="<?= isset($_REQUEST['q']) ? $_REQUEST['q'] : "" ?>" style="width:auto; float:left;padding: 0px 2px;max-height: 30px;">
                                    <input type="submit" style="float:left;" value="Search Users" class="btn btn-default btn-sm">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="margin: 3px 0px;">
                                <div class="pull-left actions" style="width: inherit;">
                                    <select name="action" id="bulk-action-selector-top" style="max-width: 150px;float: left;padding: 0px 5px;max-height: 30px;margin-right: 2px;" class="form-control">
                                        <option value="-1">Bulk Actions</option>
                                        <option value="trash">Delete</option>
                                    </select>                            
                                    <input type="submit" id="doaction" class="btn btn-sm btn-default action" value="Apply">
                                </div>
                                <div class="pull-left actions" style="width: inherit;">
                                    <select name="new_role" id="new_role" style="max-width: 150px;float: left;padding: 0px 5px;max-height: 30px;margin-right: 2px;" class="form-control">
                                        <option value="">Change role to..</option>
                                        <?php foreach($sys['roles'] as $key => $value) { ?>
                                        <option value="<?= $key ?>"><?= $value ?></option>
                                        <?php } ?>
                                    </select>                            
                                    <input type="submit" id="changeit" class="btn btn-sm btn-default action" name="changeit" value="Change">
                                </div>
                            </div>
                            <div class="col-md-6" style="margin: 3px 0px;">
                                <div class="" style="float:right">
                                    <span class="displaying-num"><?= $items_count ?> items</span>
                                    <a class="first-page btn btn-default btn-sm btn-flat" href="users.php?<?= $querystring . '&paged=1' ?>"><i class="fa fa-angle-double-left"></i></a>
                                    <a class="previous-page btn btn-default btn-sm btn-flat" href="users.php?<?= $querystring . '&paged=' . ($current_page > 1 ? $current_page - 1 : 1) ?>"><i class="fa fa-angle-left"></i></a>
                                    <span class="paging-input"><input class="btn btn-sm btn-flat" style="cursor:auto;max-width: 50px;padding: 4px 10px;" id="current-page-selector" type="text" name="paged" value="<?= $current_page ?>"> of <?= $max_pages ?></span>
                                    <a class="next-page btn btn-default btn-sm btn-flat" href="users.php?<?= $querystring . '&paged=' . ($current_page < $max_pages ? $current_page + 1 : $max_pages) ?>"><i class="fa fa-angle-right"></i></a>
                                    <a class="last-page btn btn-default btn-sm btn-flat" href="users.php?<?= $querystring . '&paged=' . $max_pages ?>"><i class="fa fa-angle-double-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="box">
                            <div class="table-responsive">
                                <table id="users" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" class="checkall"/></th>
                                            <th><a href="users.php?order_by=username&order=<?= $order_by == "username" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Username <?= $order_by == "username" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th>Name</th>                                        
                                            <th><a href="users.php?order_by=email&order=<?= $order_by == "email" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Email <?= $order_by == "email" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th>Role</th>
                                            <th>Posts</th>
                                            <th>Registered</th>                                        
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php                       
                                        $i=1;
                                        foreach ($users as $user) {
                                            ?>
                                            <tr>   
                                                <th><input type="checkbox" name="users[]" value="<?= $user['id'] ?>"/></th>
                                                <td>
                                                    <a href="<?= $sys['site_url'] . '/admin/user-edit.php?id=' . $user['id']; ?>"><?= $user['username'] ?></a>
                                                    <?php if($user['id'] <> 1 && isUserHavePermission(MANAGE_USERS_SECTION, getUserLoggedId())) { ?>
                                                    <div class="row-actions">
                                                        <a href="<?= $sys['site_url'] . '/author/' . $user['username']; ?>">View</a> |
                                                        <a href="<?= $sys['site_url'] . '/admin/user-edit.php?id=' . $user['id']; ?>" title="Edit User">Edit</a> |
                                                        <a href="<?= $sys['site_url'] . '/admin/user-permission-edit.php?id=' . $user['id']; ?>" title="Edit User Permission">Edit Permissions</a> |
                                                        <a href="<?= $sys['site_url'] . '/admin/users.php?del=' . $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')" title="Delete User" style="color:red;">Delete</a>
                                                    </div>
                                                    <?php } ?>
                                                </td>
                                                <td><?= $user['display_name'] ?></td>
                                                <td><?= $user['email'] ?></td>
                                                <td><?= isset($user['metas']['role']) ? $sys['roles'][$user['metas']['role']] : "" ?></td>
                                                <td><a href="posts.php?author=<?= $user['id'] ?>"><?= count(getPosts(array('id'), array('post_author' => $user['id'], 'post_type' => "post"), 0, -1)) ?></a></td>
                                                <td><?= $user['registered'] ?></td>                                            
                                            </tr>                      
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>   
                                            <th><input type="checkbox" class="checkall"/></th>
                                            <th><a href="users.php?order_by=username&order=<?= $order_by == "username" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Username <?= $order_by == "username" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th>Name</th>                                        
                                            <th><a href="users.php?order_by=email&order=<?= $order_by == "email" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Email <?= $order_by == "email" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th>Role</th>
                                            <th>Posts</th>
                                            <th>Registered</th>                                        
                                        </tr>
                                    </tfoot>
                                </table>
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    </form>
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>    

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>
        <script src="<?= $sys['site_url']; ?>/admin/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
        <script type="text/javascript">    
            $('input[type="checkbox"]').iCheck({
              checkboxClass: 'icheckbox_flat-blue',
              radioClass: 'iradio_flat-blue'
            });
            $(".checkall").on("ifChanged", function(e){
                $("input[type='checkbox']").iCheck($(this).is(":checked") ? "check" : "uncheck");
            });
        </script>     
    </body>
</html>