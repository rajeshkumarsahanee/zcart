<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(MANAGE_APPEARANCE_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$config = getConfig();
$menu_options = isset($config['menu_options']) ? json_decode($config['menu_options'], true) : array("auto_add" => array());
$menu_locations = array("primary" => array('name' => "Primary Menu", 'menu' => 0));
if(isset($config['menu_locations']) && !empty($config['menu_locations'])) {
    $menu_locations = json_decode($config['menu_locations'], true);
}

$msg = "";

//Create Menu
if(isset($_POST['create_menu'])) {
    $term['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $term['slug'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $term['term_group'] = "0";
    $term['taxonomy'] = "menu";
    $term['description'] = "";
    $term['parent'] = "0";
    $term['count'] = "0";
    $term['metas'] = array();

    if ($term['name'] == '') {
        $msg = '<div class="alert alert-danger">Please enter name</div>';
    } else if ($term['slug'] == '') {
        $msg = '<div class="alert alert-danger">Please enter slug</div>';
    } else {
        $term_id = addTerm($term);
        $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        if ($term_id) {
            redirect("menus.php?menu=" . $term_id);
            exit();
        }
    }
}

//Select or Create First Menu
if(isset($_REQUEST['menu'])) {
    $term = getTerm(filter_var($_REQUEST['menu'], FILTER_SANITIZE_NUMBER_INT));
    if($term['taxonomy'] == "menu") {
        $_SESSION['menu'] = $term['term_id'];
    }
} else { 
    if (!isset($_SESSION['menu']) && !isset($_REQUEST['action'])) {
        $terms = getTerms(array(), array('taxonomy' => "menu"), 0, 1, 'term_id');
        if (count($terms) > 0) {
            foreach ($terms as $t) {
                $_SESSION['menu'] = $t['term_id'];
            }
        } else {
            redirect("menus.php?action=new");
            exit();
        }
    }
}

//Save Menu
if (isset($_POST['save_menu']) && isset($_POST['menu'])) {
    $menu_id = filter_var(trim($_POST['menu']), FILTER_SANITIZE_NUMBER_INT);
    $msg = '<div class="alert alert-success">Saved Successfully!</div>';

    //Saving menu items
    if (isset($_POST['menu-item-type']) && is_array($_POST['menu-item-type'])) {
        $menu_order = 1;
        foreach ($_POST['menu-item-type'] as $key => $value) {
            $post = getPost($key);
            $post['post_title'] = isset($_POST['menu-item-title'][$key]) ? filter_var($_POST['menu-item-title'][$key], FILTER_SANITIZE_STRING) : $post['post_title'];
            $post['menu_order'] = isset($_POST['menu-item-position'][$key]) ? $_POST['menu-item-position'][$key] : $menu_order++;
            $post['post_status'] = "published";
            $post['metas']['menu_item_type'] = filter_var($value, FILTER_SANITIZE_STRING);
            $post['metas']['menu_item_parent'] = isset($_POST['menu-item-parent'][$key]) ? $_POST['menu-item-parent'][$key] : "0";
            $post['metas']['menu_item_depth'] = isset($_POST['menu-item-depth'][$key]) ? $_POST['menu-item-depth'][$key] : "0";
            $post['metas']['menu_item_object_id'] = isset($_POST['menu-item-object-id'][$key]) ? $_POST['menu-item-object-id'][$key] : $post['metas']['menu_item_object_id'];
            $post['metas']['menu_item_object'] = isset($_POST['menu-item-object'][$key]) ? $_POST['menu-item-object'][$key] : $post['metas']['menu_item_object'];
            $post['metas']['menu_item_target'] = isset($_POST['menu-item-target'][$key]) ? $_POST['menu-item-target'][$key] : "";
            $post['metas']['menu_item_classes'] = isset($_POST['menu-item-classes'][$key]) ? $_POST['menu-item-classes'][$key] : $post['metas']['menu_item_classes'];
            $post['metas']['menu_item_url'] = isset($_POST['menu-item-url'][$key]) ? $_POST['menu-item-url'][$key] : "";
            $post['metas']['menu_item_attr_title'] = isset($_POST['menu-item-attr-title'][$key]) ? $_POST['menu-item-attr-title'][$key] : $post['metas']['menu_item_attr_title'];
            
            if (!isset($_POST['delete-menu-item']) || isset($_POST['delete-menu-item']) && !in_array($key, $_POST['delete-menu-item'])) {
                $post['terms'] = array($menu_id);
            }
            if (!updatePost($post)) {
                $msg = '<div class="alert alert-danger">Error!</div>';
                break;
            }
        }
    }
    
    //Saving auto add option
    if (($key = array_search($menu_id, $menu_options['auto_add'])) !== FALSE) {
        unset($menu_options['auto_add'][$key]);
    }
    if (isset($_POST['auto-add-pages'])) {
        $menu_options['auto_add'][] = $menu_id;
    }
    saveConfig("menu_options", json_encode($menu_options));

    //Setting menu locations
    foreach ($menu_locations as $key => $value) {
        if (isset($_POST['menu-locations'][$key])) {
            $menu_locations[$key]['menu'] = filter_var($_POST['menu-locations'][$key], FILTER_SANITIZE_NUMBER_INT);
        }
    }
    saveConfig("menu_locations", json_encode($menu_locations));
}

$menus = getTerms(array(), array('taxonomy' => "menu"), 0, -1);
$menu = isset($_SESSION['menu']) ? getTerm(filter_var($_SESSION['menu']), FILTER_SANITIZE_NUMBER_INT) : null;
$menu_items = getPosts(array('id', 'post_title', 'menu_order'), array('term_taxonomy_id' => $menu['term_id'], 'with_metas' => 1), 0, -1, "menu_order", "ASC");
$pagesrecents = getPosts(array('id', 'post_title'), array('post_type' => "page"), 0, 6, "post_date");
$pagesall = getPosts(array('id', 'post_title'), array('post_type' => "page"), 0, 50, "post_title");
$postsrecents = getPosts(array('id', 'post_title'), array('post_type' => "post"), 0, 6, "post_date");
$postsall = getPosts(array('id', 'post_title'), array('post_type' => "post"), 0, 50, "post_title");
$categoriesrecents = getTerms(array(), array('taxonomy' => "category"), 0, 6, "count");
$categoriesall = getTerms(array(), array('taxonomy' => "category"), 0, 50);
$i = 0;

function buildNestedMenu($items, $parentId = "0", $isRoot = true) {
    // Parent items control
    $isParentItem = false;
    foreach ($items as $item) {
        if ($item['metas']['menu_item_parent'] === $parentId) {
            $isParentItem = true;
            break;
        }
    }

    // Prepare items
    $html = "";
    if ($isParentItem) {
        $html .= $isRoot ? '' : '<ul>';
        foreach ($items as $item) {
            if ($item['metas']['menu_item_parent'] === $parentId) {
                $html .= '<li id="menu-item-'.$item['id'].'" class="draggable" style="display:block;">'; 
                $html .= getMenuContent($item); 
                $html .= buildNestedMenu($items, $item['id'], false);
                $html .= '</li>';
                
            }
        }
        $html .= $isRoot ? '' : '</ul>';
    }
    return $html;
}

function getMenuContent($post) {
    global $sys;
    $original = $original_link = $original_title = null;
    if ($post['metas']['menu_item_type'] == 'post_type') {
        $original = getPost($post['metas']['menu_item_object_id']);
        $original_link = $sys['site_url'] . "/" . $original['post_name'];
        $original_title = $original['post_title'];
        $title = $original['post_title'];
    } else if ($post['metas']['menu_item_type'] == 'taxonomy') {
        $original = getTerm($post['metas']['menu_item_object_id']);
        $original_link = $sys['site_url'] . "/" . $original['slug'];
        $original_title = $original['name'];
        $title = $original['name'];
    } else {
        $title = $post['post_title'];
    }

    $html = '<div class="box box-default collapsed-box box-solid menu-item">
            <div class="box-header with-border">
                <h3 class="box-title">' . $title . '</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="box-body">';
                
                if (trim($post['metas']['menu_item_type']) == "custom") {
                    $html .= '<div class="form-group">
                        <i>Url</i>
                        <input type="text" name="menu-item-url[' . $post['id'] . ']" value="' . $post['metas']['menu_item_url'] . '" class="form-control"/>
                    </div>';
                }
                
                $html .= '<div class="form-group">
                    <i>Navigation Label</i>
                    <input type="text" name="menu-item-title['. $post['id'] . ']" value="' . $title . '" class="form-control"/>
                </div>
                <div class="form-group">
                    <i>Title Attribute</i>
                    <input type="text" name="menu-item-attr-title[' . $post['id'] . ']" value="' . $post['metas']['menu_item_attr_title'] . '" class="form-control"/>
                </div>
                <div class="form-group hidden">
                    <input type="checkbox" name="menu-item-target[' . $post['id'] . ']" value="' . $post['metas']['menu_item_target'] . '" class=""/>
                    Open link in a new tab
                </div>
                <div class="form-group hidden">
                    <i>CSS Classes (optional)</i>
                    <input type="text" name="menu-item-classes[' . $post['id'] . ']" value="' . $post['metas']['menu_item_classes'] . '" class="form-control"/>
                </div>
                <fieldset class="field-move" style="margin-bottom: 10px;">
                    <i class="field-move-visual-label" aria-hidden="true">Move</i>
                    <a href="#" class="menu-move menus-move-up" data-dir="up" style="display: inline;">Up</a>
                    <a href="#" class="menu-move menus-move-down" data-dir="down" style="display: inline;">Down</a>
                    <a href="#" class="menu-move menus-move-left" data-dir="left" style="display: inline;">Left</a>
                    <a href="#" class="menu-move menus-move-right" data-dir="right" style="display: inline;">Right</a>
                    <a href="#" class="menu-move menus-move-top" data-dir="top" style="display: inline;">Top</a>
                </fieldset>';

                if (isset($original_link)) {
                    $html .= '<p class="link-to-original">
                        Original: <a href="' . $original_link . '">' . $original_title . '</a>
                    </p>';
                }
                
               $html .= '<a class="item-delete text-danger" item-id="' . $post['id'] . '" href="#">Remove</a>
                <span class=""> | </span>
                <a class="item-cancel text-info" id="cancel-' . $post['id'] . '" data-widget="collapse" href="#">Cancel</a>
            </div>
        </div>
        <input type="hidden" name="menu-item-type[' . $post['id'] . ']" value="' . $post['metas']['menu_item_type'] . '"/>
        <input type="hidden" name="menu-item-parent[' . $post['id'] . ']" class="menu-item-parent" value="' . $post['metas']['menu_item_parent'] . '"/>
        <input type="hidden" name="menu-item-depth[' . $post['id'] . ']" class="menu-item-depth" value="' . $post['metas']['menu_item_depth'] . '"/>
        <input type="hidden" name="menu-item-object-id[' . $post['id'] . ']" value="' . $post['metas']['menu_item_object_id'] . '"/>
        <input type="hidden" name="menu-item-object[' . $post['id'] . ']" value="' . $post['metas']['menu_item_object'] . '"/>
        <input type="hidden" name="menu-item-position[' . $post['id'] . ']" class="menu-item-position" value="' . $post['menu_order'] . '"/>';
    return $html;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Menus - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?> 
        <link rel="stylesheet" href="<?= $sys['site_url']; ?>/admin/plugins/iCheck/flat/blue.css">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <style>
            .manage-menus {
                background-color: white;
                padding: 10px 10px;
                margin-bottom: 15px;
            }
            .select-meu-label {
                float: left;
                font-weight: normal;
                line-height: 30px;
                margin-right: 5px;
            }
            .panel {
                margin-bottom: 0px !important;
            }
            .box-title a {
                font-size: 16px;
                font-weight: bold;
                color: black;
            }
            .nav > li > a {
                padding: 5px 10px !important;
            }
            .link-to-original {
                margin-bottom: 10px;
                padding: 5px 10px;
                border-radius: 0;
                background: whitesmoke;
                border: solid thin #d2d6de;
            }
            .menu-item {
                max-width: 385px;
                margin-bottom: 5px;
                border-radius: 0px;
            }
            .draggable .box:hover {
                border: solid thin #616366 !important;
                cursor: move;
            }
            .placeholder {
                outline: 1px dashed #4183C4;
                max-width: 385px;
                list-style: none;
            }
            .menu-move {
                text-decoration: underline !important;
                font-style: italic;
                padding: 2px;
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
                        Menus
                        <small>information</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class=""><a href="<?= $sys['config']['site_url'] ?>/admin/themes.php">Appearance</a></li>
                        <li class="active">Menus</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="manage-menus">
                        <form action="" method="get">
                            <label for="select-menu-to-edit" class="select-meu-label">Select a menu to edit:</label>
                            <select name="menu" id="select-menu-to-edit" style="max-width: 150px;float: left;padding: 0px 5px;max-height: 30px;margin-right: 2px;" class="form-control">
                                <?php foreach ($menus as $m) { ?>
                                    <option value="<?= $m['id'] ?>" <?= $menu != null && $menu['term_id'] == $m['term_id'] ? "selected" : "" ?>><?= $m['name'] ?></option>
                                <?php } ?>
                            </select>                            
                            <input type="submit" id="selectmenu" class="btn btn-sm btn-default action" value="Select">
                            or
                            <a href="menus.php?action=new">create a new menu</a>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-md-3">                     
                            <div class="box-group" id="accordion">
                                <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                                <div class="panel">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapsePages">Pages</a>
                                        </h4>
                                    </div>
                                    <div id="collapsePages" class="panel-collapse collapse in">
                                        <div class="box-body">
                                            <form class="menu-item-add-form" method="post">
                                                <input type="hidden" name="menu" value="<?= $menu['term_id'] ?>"/>
                                                <div class="nav-tabs-custom">
                                                    <ul class="nav nav-tabs">
                                                        <li class="active"><a href="#pages_recent" data-toggle="tab">Recent</a></li>
                                                        <li><a href="#pages_all" data-toggle="tab">View All</a></li>
                                                        <li><a href="#pages_search" data-toggle="tab">Search</a></li>
                                                    </ul>
                                                    <div class="tab-content">
                                                        <div class="tab-pane active" id="pages_recent">
                                                            <ul id="pagechecklist-recent" class="nav">
                                                                <?php foreach ($pagesrecents as $p) { ?>
                                                                    <li>
                                                                        <label><input type="checkbox" id="menu-item[<?= $i ?>]" name="menu-item[<?= $i ?>][menu-item-object-id]" value="<?= $p['id'] ?>" class="menu-item-checkbox"/> <?= $p['post_title'] ?></label>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-type]" value="post_type" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-parent]" value="0" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-depth]" value="0" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-object]" value="page" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-target]" value="" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-classes]" value="" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-url]" value="<?= $sys['site_url'] . "/" . $p['post_name'] ?>" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-title]" value="<?= $p['post_title'] ?>" disabled=""/>
                                                                    </li>
                                                                    <?php
                                                                    $i++;
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                        <!-- /.tab-pane -->
                                                        <div class="tab-pane" id="pages_all">
                                                            <ul id="pagechecklist-all" class="nav">
                                                                <?php foreach ($pagesall as $p) { ?>
                                                                    <li>
                                                                        <label><input type="checkbox" id="menu-item[<?= $i ?>]" name="menu-item[<?= $i ?>][menu-item-object-id]" value="<?= $p['id'] ?>" class="menu-item-checkbox"/> <?= $p['post_title'] ?></label>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-type]" value="post_type" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-parent]" value="0" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-depth]" value="0" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-object]" value="page" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-target]" value="" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-classes]" value="" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-url]" value="<?= $sys['site_url'] . "/" . $p['post_name'] ?>" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-title]" value="<?= $p['post_title'] ?>" disabled=""/>
                                                                    </li>
                                                                    <?php
                                                                    $i++;
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                        <!-- /.tab-pane -->
                                                        <div class="tab-pane" id="pages_search">
                                                            <input type="text" id="search_pages" class="form-control"/>
                                                            <ul id="pagechecklist-search" class="nav">
                                                                <li></li>
                                                            </ul>
                                                        </div>
                                                        <!-- /.tab-pane -->
                                                    </div>
                                                    <!-- /.tab-content -->
                                                </div>
                                                <a href="#" class="selectall">Select All</a>
                                                <input type="submit" id="add-posttype-page" name="add-posttype-page" value="Add to Menu" class="btn btn-default btn-sm pull-right"/>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapsePosts">Posts</a>
                                        </h4>
                                    </div>
                                    <div id="collapsePosts" class="panel-collapse collapse">
                                        <div class="box-body">
                                            <form class="menu-item-add-form" method="post">
                                                <input type="hidden" name="menu" value="<?= $menu['term_id'] ?>"/>
                                                <div class="nav-tabs-custom">
                                                    <ul class="nav nav-tabs">
                                                        <li class="active"><a href="#posts_recent" data-toggle="tab">Recent</a></li>
                                                        <li><a href="#posts_all" data-toggle="tab">View All</a></li>
                                                        <li><a href="#posts_search" data-toggle="tab">Search</a></li>
                                                    </ul>
                                                    <div class="tab-content">
                                                        <div class="tab-pane active" id="posts_recent">
                                                            <ul id="postchecklist-recent" class="nav">
                                                                <?php foreach ($postsrecents as $p) { ?>
                                                                    <li>
                                                                        <label><input type="checkbox" id="menu-item[<?= $i ?>]" name="menu-item[<?= $i ?>][menu-item-object-id]" value="<?= $p['id'] ?>" class="menu-item-checkbox"/> <?= $p['post_title'] ?></label>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-type]" value="post_type" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-parent]" value="0" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-depth]" value="0" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-object]" value="post" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-target]" value="" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-classes]" value="" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-url]" value="<?= $sys['site_url'] . "/" . $p['post_name'] ?>" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-title]" value="<?= $p['post_title'] ?>" disabled=""/>
                                                                    </li>
                                                                    <?php
                                                                    $i++;
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                        <!-- /.tab-pane -->
                                                        <div class="tab-pane" id="posts_all">
                                                            <ul id="postchecklist-all" class="nav">
                                                                <?php foreach ($postsall as $p) { ?>
                                                                    <li>
                                                                        <label><input type="checkbox" id="menu-item[<?= $i ?>]" name="menu-item[<?= $i ?>][menu-item-object-id]" value="<?= $p['id'] ?>" class="menu-item-checkbox"/> <?= $p['post_title'] ?></label>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-type]" value="post_type" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-parent]" value="0" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-depth]" value="0" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-object]" value="post" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-target]" value="" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-classes]" value="" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-url]" value="<?= $sys['site_url'] . "/" . $p['post_name'] ?>" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-title]" value="<?= $p['post_title'] ?>" disabled=""/>
                                                                    </li>
                                                                    <?php
                                                                    $i++;
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                        <!-- /.tab-pane -->
                                                        <div class="tab-pane" id="posts_search">
                                                            <input type="text" id="search_posts" class="form-control"/>
                                                            <ul id="postchecklist-search" class="nav">
                                                                <li></li>
                                                            </ul>
                                                        </div>
                                                        <!-- /.tab-pane -->
                                                    </div>
                                                    <!-- /.tab-content -->
                                                </div>
                                                <a href="#" class="selectall">Select All</a>
                                                <input type="submit" id="add-posttype-post" name="add-posttype-post" value="Add to Menu" class="btn btn-default btn-sm pull-right"/>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseCustomLinks">Custom Links</a>
                                        </h4>
                                    </div>
                                    <div id="collapseCustomLinks" class="panel-collapse collapse">
                                        <div class="box-body">
                                            <form class="menu-item-add-form" method="post">
                                                <input type="hidden" name="menu" value="<?= $menu['term_id'] ?>"/>
                                                <div class="form-group">
                                                    <input type="text" name="menu-item[-1][menu-item-url]" value="http://" placeholder="Link" class="form-control" required=""/>
                                                </div>
                                                <div class="form-group">
                                                    <input type="text" name="menu-item[-1][menu-item-title]" placeholder="Link Text" class="form-control" required=""/>
                                                </div>
                                                <input type="hidden" name="menu-item[-1][menu-item-type]" value="custom"/>
                                                <input type="submit" id="add-customlink" name="add-customlink" value="Add to Menu" class="btn btn-default btn-sm pull-right"/>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseCategories">Categories</a>
                                        </h4>
                                    </div>
                                    <div id="collapseCategories" class="panel-collapse collapse">
                                        <div class="box-body">
                                            <form class="menu-item-add-form" method="post"> 
                                                <input type="hidden" name="menu" value="<?= $menu['term_id'] ?>"/>
                                                <div class="nav-tabs-custom">
                                                    <ul class="nav nav-tabs">
                                                        <li class="active"><a href="#categories_recent" data-toggle="tab">Recent</a></li>
                                                        <li><a href="#categories_all" data-toggle="tab">View All</a></li>
                                                        <li><a href="#categories_search" data-toggle="tab">Search</a></li>
                                                    </ul>
                                                    <div class="tab-content">
                                                        <div class="tab-pane active" id="categories_recent">
                                                            <ul id="categorychecklist-recent" class="nav">
                                                                <?php foreach ($categoriesrecents as $c) { ?>
                                                                    <li>
                                                                        <label><input type="checkbox" id="menu-item[<?= $i ?>]" name="menu-item[<?= $i ?>][menu-item-object-id]" value="<?= $c['id'] ?>" class="menu-item-checkbox"/> <?= $c['name'] ?></label>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-type]" value="taxonomy" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-parent]" value="0" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-depth]" value="0" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-object]" value="category" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-target]" value="" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-classes]" value="" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-url]" value="<?= $sys['site_url'] . "/" . $c['slug'] ?>" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-title]" value="<?= $c['name'] ?>" disabled=""/>
                                                                    </li>
                                                                    <?php
                                                                    $i++;
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                        <!-- /.tab-pane -->
                                                        <div class="tab-pane" id="categories_all">
                                                            <ul id="categorychecklist-all" class="nav">
                                                                <?php foreach ($categoriesall as $c) { ?>
                                                                    <li>
                                                                        <label><input type="checkbox" id="menu-item[<?= $i ?>]" name="menu-item[<?= $i ?>][menu-item-object-id]" value="<?= $c['id'] ?>" class="menu-item-checkbox"/> <?= $c['name'] ?></label>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-type]" value="taxonomy" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-parent]" value="0" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-depth]" value="0" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-object]" value="category" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-target]" value="" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-classes]" value="" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-url]" value="<?= $sys['site_url'] . "/" . $c['slug'] ?>" disabled=""/>
                                                                        <input type="hidden" name="menu-item[<?= $i ?>][menu-item-title]" value="<?= $c['name'] ?>" disabled=""/>
                                                                    </li>
                                                                    <?php
                                                                    $i++;
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                        <!-- /.tab-pane -->
                                                        <div class="tab-pane" id="categories_search">
                                                            <input type="text" id="search_categories" class="form-control"/>
                                                            <ul id="categorychecklist-search" class="nav">
                                                                <li></li>
                                                            </ul>
                                                        </div>
                                                        <!-- /.tab-pane -->
                                                    </div>
                                                    <!-- /.tab-content -->
                                                </div>
                                                <a href="#" class="selectall">Select All</a>
                                                <input type="submit" id="add-taxonomy-category" name="add-taxonomy-category" value="Add to Menu" class="btn btn-default btn-sm pull-right"/>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <?php if (isset($_REQUEST['action']) && trim($_REQUEST['action']) == "new") { ?>
                                <form id="add-nav-menu" method="post" action="">
                                    <div class="box box-solid">
                                        <div class="box-header with-border">
                                            <i class="">Menu Name</i>
                                            <h3 class="box-title"><input type="text" name="name" class="form-control" style="max-height: 30px; padding: 0px 5px;"/></h3>
                                            <input type="submit" id="save_menu_header" name="create_menu" value="Create Menu" class="btn btn-success btn-sm pull-right"/>
                                        </div>
                                        <!-- /.box-header -->
                                        <div class="box-body">
                                            <p style="margin-bottom: 30px;">Give your menu a name, then click Create Menu.</p>
                                        </div>
                                        <div class="box-footer with-border">
                                            <input type="submit" id="save_menu_header" class="btn btn-success btn-sm pull-right" name="create_menu" value="Create Menu">
                                        </div>
                                    </div>
                                </form>
                            <?php } else { ?>
                                <form id="update-nav-menu" method="post" action="" enctype="multipart/form-data">
                                    <input type="hidden" name="menu" value="<?= $menu['term_id'] ?>"/>
                                    <div class="box box-solid">
                                        <div class="box-header with-border">
                                            <i class="">Menu Name</i>
                                            <h3 class="box-title"><input type="text" name="name" value="<?= $menu['name'] ?>" class="form-control" style="max-height: 30px;"/></h3>
                                            <input type="submit" id="save_menu_header" name="save_menu" value="Save Menu" class="btn btn-success btn-sm pull-right"/>
                                        </div>
                                        <!-- /.box-header -->
                                        <div class="box-body">
                                            <h4><b>Menu Structure</b></h4>
                                            <p>Drag each item into the order you prefer. Click the plus on the right of the item to reveal additional configuration options.</p>
                                            <!-- Existing menu items -->
                                            <ul class="nav menu sortable" id="menu-to-edit">
                                            <?= buildNestedMenu($menu_items) ?>
                                            </ul>
                                            
                                            <div class="menu-settings">
                                                <h4><b>Menu Settings</b></h4>
                                                <fieldset class="menu-settings-group auto-add-pages" style="padding-left: 20%;">
                                                    <i style="float: left;width: 25%;margin-left: -25%;">Auto add pages</i>
                                                    <div class="menu-settings-input checkbox-input">
                                                        <input type="checkbox" name="auto-add-pages" id="auto-add-pages" value="1" <?= isset($menu_options['auto_add']) && in_array($menu['term_id'], $menu_options['auto_add']) ? "checked" : "" ?>> <label for="auto-add-pages">Automatically add new top-level pages to this menu</label>
                                                    </div>
                                                </fieldset>
                                                <fieldset class="menu-settings-group menu-theme-locations" style="padding-left: 20%;">
                                                    <i class="" style="float: left;width: 25%;margin-left: -25%;">Display location</i>
                                                    <?php foreach($menu_locations as $key => $value) { ?>
                                                    <div class="menu-settings-input checkbox-input">
                                                        <input type="checkbox" id="menu-locations-<?= $key ?>" name="menu-locations[<?= $key ?>]" value="<?= $menu['term_id'] ?>" <?= $value['menu'] == $menu['term_id'] ? "checked" : "" ?>>
                                                        <label for="menu-locations-<?= $key ?>"><?= $value['name'] ?></label>
                                                        <span class="theme-location-set">
                                                            <?php
                                                            $setto = "";
                                                            foreach ($menus as $m) {
                                                                if ($m['term_id'] == $value['menu'] && $m['term_id'] != $menu['term_id']) {
                                                                    $setto .= $m['name'] . ",";
                                                                }
                                                            }
                                                            echo $setto <> "" ? '(Currently set to: ' . trim($setto, ",") . ")" : "";
                                                            ?>
                                                        </span>
                                                    </div>
                                                    <?php } ?>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="box-footer with-border">
                                            <a href="menus.php?action=delete&menu=<?= $menu['term_id'] ?>" onclick="return confirm('Sure! You want to delete this menu?')" style="font-weight: bold;color: red;" class="">Delete Menu</a>
                                            <input type="submit" id="save_menu_header" class="btn btn-success btn-sm pull-right" name="save_menu" value="Save Menu">
                                        </div>
                                    </div>
                                </form>
                            <?php } ?>
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
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/nestedSortable/2.0.0/jquery.mjs.nestedSortable.min.js"></script>
        <script type="text/javascript">    
            $('input[type="checkbox"]').iCheck({
              checkboxClass: 'icheckbox_flat-blue',
              radioClass: 'iradio_flat-blue'
            });
            $(".menu-item-checkbox").on("ifChanged", function(){
                if($(this).is(":checked")) {
                    $('input[type=hidden][name^="' + $(this).attr("id") + '"]').prop("disabled", false);
                } else {
                    $('input[type=hidden][name^="' + $(this).attr("id") + '"]').prop("disabled", true);
                }
            });
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
              var target = $(e.target).attr("href");
              $(target).parent().find(".menu-item-checkbox").iCheck("uncheck");
            });
            $(".selectall").click(function(){
                $(this).parent().find(".tab-pane.active").find(".menu-item-checkbox").iCheck("toggle")
                return false;
            });
            $(".menu-item-add-form").submit(function(e){
                e.preventDefault();
                var data = new FormData(e.target);
                $.ajax({
                    type: "POST",
                    url: "<?= $sys['site_url'] ?>/requests.php?action=add-menu-item",
                    data: data,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $("#menu-to-edit").append(response);
                        $(e.target).find(".menu-item-checkbox").iCheck("uncheck");
                    }
                });
            });
            
            $( ".sortable" ).nestedSortable({
                forcePlaceholderSize: true,
                handle: 'div',
                helper:	'clone',
                items: 'li',
                placeholder: 'placeholder',
		toleranceElement: '> div',
                listType: "ul",
                update: function(event, ui) {
                  $.each($(this).nestedSortable('toArray'), function(i, o){
                    $("#menu-item-" + o.id).find(".menu-item-position").val(i);
                    $("#menu-item-" + o.id).find(".menu-item-parent").val(o.parent_id != null ? o.parent_id : 0);
                    $("#menu-item-" + o.id).find(".menu-item-depth").val(o.depth != null ? o.depth : 0);
                    console.log(o);
                  });
               }
            });
            $(".menu-move").click(function(){
                return false;
            });
            $("#menu-to-edit").on("click", ".item-delete", function(e){
                e.preventDefault();
                $("#update-nav-menu").append('<input type="hidden" name="delete-menu-item[]" value="' + $(this).attr('item-id') + '"/>');
                $(this).closest('li').fadeOut();
            });
            //$( ".draggable" ).draggable();
        </script>        
    </body>
</html>