<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
$taxonomy = isset($_REQUEST['taxonomy']) ? filter_var(trim($_REQUEST['taxonomy']), FILTER_SANITIZE_STRING) : "tag";
if (!isset($sys['taxonomies'][$taxonomy])) {
    die("Invalid Taxonomy");
}
//Not authorized to access
if (!isUserHavePermission(MANAGE_POSTS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

$config = getConfig();
$msg = "";
//Add Term
if (isset($_POST['name']) && isset($_POST['slug'])) {
    $term['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $term['slug'] = filter_var(trim($_POST['slug']), FILTER_SANITIZE_STRING);
    $term['term_group'] = isset($_POST['term_group']) ? filter_var(trim($_POST['term_group']), FILTER_SANITIZE_STRING) : "0";
    $term['taxonomy'] = isset($_POST['taxonomy']) ? filter_var(trim($_POST['taxonomy']), FILTER_SANITIZE_STRING) : "tag";
    $term['description'] = trim($_POST['description']);
    $term['parent'] = isset($_POST['parent']) ? filter_var(trim($_POST['parent']), FILTER_SANITIZE_STRING) : "0";
    $term['count'] = isset($_POST['count']) ? filter_var(trim($_POST['count']), FILTER_SANITIZE_STRING) : "0";
    $term['metas'] = array();

    if ($term['name'] == '') {
        $msg = '<div class="alert alert-danger">Please enter name</div>';
    } else if ($term['slug'] == '') {
        $msg = '<div class="alert alert-danger">Please enter slug</div>';
    } else {
        $msg = '<div class="alert alert-success">Added successfully!</div>';
        if (!addTerm($term)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}
//Delete Term
if(isset($_REQUEST['del'])) {
    deleteTerm(filter_var(trim($_REQUEST['del']), FILTER_SANITIZE_NUMBER_INT));
}
//Delete Terms
if(isset($_REQUEST['action']) && trim($_REQUEST['action']) == "trash") {
    if (isset($_REQUEST['term'])) {
        foreach ($_REQUEST['term'] as $del) {
            deleteTerm(filter_var(trim($del), FILTER_SANITIZE_NUMBER_INT));
        }
    }
}

$filters = array("taxonomy" => $taxonomy);
$querystring = "taxonomy=" . $taxonomy;
if(isset($_REQUEST['q'])) {
    $filters['query'] = filter_var(trim($_REQUEST['q']), FILTER_SANITIZE_STRING);
    $querystring .= "&q=" . $_REQUEST['q'];
}
/*pagination logic start*/
$items_count = count(getTerms(array(), $filters, 0, -1));
$items_per_page = isset($config['items_per_page_tag_admin']) ? $config['items_per_page_tag_admin'] : 20;
$max_pages = intval($items_count / $items_per_page + 1);
$current_page = !isset($_REQUEST['paged']) || intval($_REQUEST['paged']) < 1 ? 1 : filter_var(trim($_REQUEST['paged']), FILTER_SANITIZE_NUMBER_INT);
if($current_page > $max_pages) {
    header("location: tags.php?" . $querystring . "&paged=" . $max_pages);
    exit();
}
$offset = $items_per_page * $current_page - $items_per_page;
/*pagination logic end*/
$terms = getTerms(array(), $filters, $offset, $items_per_page);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= $sys['taxonomies'][$taxonomy]['plural'] ?> - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>  
        <link rel="stylesheet" href="<?= $sys['site_url']; ?>/admin/plugins/iCheck/flat/blue.css">
        <style>
            #terms {
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
                        <?= $sys['taxonomies'][$taxonomy]['plural'] ?>
                        <small>information</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class=""><a href="<?= $sys['config']['site_url'] ?>/admin/posts.php">Posts</a></li>
                        <li class="active"><?= $sys['taxonomies'][$taxonomy]['plural'] ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-4">
                            <form action="" method="post">
                                <h4>Add New <?= $sys['taxonomies'][$taxonomy]['name'] ?></h4>
                                <?= $msg ?>
                                <input type="hidden" name="taxonomy" value="<?= $taxonomy ?>"/>
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control"/>
                                    The name is how it appears on your site.
                                </div>
                                <div class="form-group">
                                    <label>Slug</label>
                                    <input type="text" name="slug" class="form-control"/>
                                    The "slug" is the URL-friendly version of the name. It is usually all lowercase 
                                    and contains only letters, numbers, and hyphens.
                                </div>
                                <?php if($taxonomy == "category") {
                                    $parentcategories = getTerms(array(), array("taxonomy" => "category"), 0, -1);
                                    ?>
                                <div class="form-group">
                                    <label>Parent Category</label>
                                    <select name="parent" class="form-control">
                                        <option value="0">None</option>
                                        <?php foreach($parentcategories as $c) { ?>
                                        <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                    Categories, unlike tags, can have a hierarchy. You might have a Jazz category, 
                                    and under that have children categories for Bebop and Big Band. Totally optional.
                                </div>
                                <?php } ?>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control"></textarea>
                                    The description is not prominent by default; however, some themes may show it.
                                </div>
                                <div class="form-group">                                    
                                    <input type="submit" name="add_new" value="Add New <?= $sys['taxonomies'][$taxonomy]['name'] ?>" class="btn btn-success"/>
                                </div>
                            </form>                            
                        </div>
                        <div class="col-md-8">
                            <form method="" action="">
                                <input type="hidden" name="taxonomy" value="<?= $taxonomy ?>"/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="" style="float:left">

                                        </div>
                                        <div class="" style="float:right">
                                            <input type="text" class="form-control" name="q" value="<?= isset($_REQUEST['q']) ? $_REQUEST['q'] : "" ?>" style="width:auto; float:left;padding: 0px 2px;max-height: 30px;">
                                            <input type="submit" style="float:left;" value="Search <?= $sys['taxonomies'][$taxonomy]['plural'] ?>" class="btn btn-default btn-sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6" style="margin: 3px 0px;">
                                        <select name="action" id="bulk-action-selector-top" style="max-width: 150px;float: left;padding: 0px 5px;max-height: 30px;margin-right: 2px;" class="form-control">
                                            <option value="-1">Bulk Actions</option>
                                            <option value="trash">Delete</option>
                                        </select>                            
                                        <input type="submit" id="doaction" class="btn btn-sm btn-default action" value="Apply">
                                    </div>
                                    <div class="col-md-6" style="margin: 3px 0px;">
                                        <div class="" style="float:right">
                                            <span class="displaying-num"><?= $items_count ?> items</span>
                                            <a class="first-page btn btn-default btn-sm btn-flat" href="tags.php?<?= $querystring . '&paged=1' ?>"><i class="fa fa-angle-double-left"></i></a>
                                            <a class="previous-page btn btn-default btn-sm btn-flat" href="tags.php?<?= $querystring . '&paged=' . ($current_page > 1 ? $current_page - 1 : 1) ?>"><i class="fa fa-angle-left"></i></a>
                                            <span class="paging-input"><input class="btn btn-sm btn-flat" style="cursor:auto;max-width: 50px;padding: 4px 10px;" id="current-page-selector" type="text" name="paged" value="<?= $current_page ?>"> of <?= $max_pages ?></span>
                                            <a class="next-page btn btn-default btn-sm btn-flat" href="tags.php?<?= $querystring . '&paged=' . ($current_page < $max_pages ? $current_page + 1 : $max_pages) ?>"><i class="fa fa-angle-right"></i></a>
                                            <a class="last-page btn btn-default btn-sm btn-flat" href="tags.php?<?= $querystring . '&paged=' . $max_pages ?>"><i class="fa fa-angle-double-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="box">    
                                    <div class="table-responsive">
                                        <table id="terms" class="table table-bordered table-striped list-table">
                                            <thead>
                                                <tr>       
                                                    <th><input type="checkbox" class="checkall"/></th>
                                                    <th>Name</th>
                                                    <th>Description</th>
                                                    <th>Slug</th>                                        
                                                    <th>Count</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                foreach ($terms as $term) {
                                                    ?>
                                                    <tr>                                       
                                                        <th style="width: 40px;"><input type="checkbox" name="term[]" value="<?= $term['ID'] ?>"/></th>
                                                        <td>
                                                            <a href=""><b><?= $term['name'] ?></b></a>
                                                            <div class="row-actions">
                                                                <a href="<?= $sys['site_url'] . ($taxonomy == "category" ? "/" : "/" . $taxonomy . "/" ) . $term['slug']; ?>">View</a> |
                                                                <a href="<?= $sys['site_url'] . '/admin/tag-edit.php?id=' . $term['id'] . '&taxonomy=' . $taxonomy ?>" title="Edit">Edit</a> |
                                                                <a href="<?= $sys['site_url'] . '/admin/tags.php?del=' . $term['id']; ?>" onclick="return confirm('Are you sure you want to delete?')" title="Delete" style="color:red;">Delete</a>
                                                            </div>
                                                        </td>
                                                        <td><?= $term['description'] ?></td>
                                                        <td><?= $term['slug'] ?></td>
                                                        <td><?= $term['count'] ?></td>
                                                    </tr>                      
                                                <?php } ?>
                                            </tbody> 
                                            <tfoot>
                                                <tr>   
                                                    <th><input type="checkbox" class="checkall"/></th>
                                                    <th>Name</th>
                                                    <th>Description</th>
                                                    <th>Slug</th>                                        
                                                    <th>Count</th>                                        
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>                    
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