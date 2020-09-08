<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
if (!isset($_REQUEST['id']) || trim($_REQUEST['id']) == "") {
    header("location: posts.php");
    exit();
}
$id = filter_var(trim($_REQUEST['id']), FILTER_SANITIZE_NUMBER_INT);
$msg = "";
//Update post
if (isset($_POST['post_title']) && isset($_REQUEST['id'])) {
    $post = getPost($id);
    //$post['id'] = $id;//Already set by getPost method called
    //$post['post_type'] = filter_var(trim($_POST['post_type']), FILTER_SANITIZE_STRING);
    $post['post_title'] = filter_var(trim($_POST['post_title']), FILTER_SANITIZE_STRING);
    $post['post_content'] = $_POST['content'];
    $post['post_content_filtered'] = $_POST['content'];
    $post['post_excerpt'] = isset($_POST['post_excerpt']) ? filter_var(trim($_POST['post_excerpt']), FILTER_SANITIZE_STRING) : $post['post_excerpt'];
    $post['post_author'] = isset($_POST['post_author']) ? filter_var(trim($_POST['post_author']), FILTER_SANITIZE_NUMBER_INT) : getUserLoggedId();
    $post['post_password'] = isset($_POST['post_password']) ? filter_var(trim($_POST['post_password']), FILTER_SANITIZE_STRING) : "";
    //$post['post_name'] = filter_var(trim($_POST['post_name']), FILTER_SANITIZE_STRING);//Already set by getPost method called
    $post['post_parent'] = isset($_POST['post_parent']) ? filter_var(trim($_POST['post_parent']), FILTER_SANITIZE_NUMBER_INT) : "0";
    $post['post_mime_type'] = isset($_POST['post_mime_type']) ? filter_var(trim($_POST['post_mime_type']), FILTER_SANITIZE_STRING) : "0";
    $post['to_ping'] = isset($_POST['to_ping']) ? filter_var(trim($_POST['to_ping']), FILTER_SANITIZE_STRING) : "";
    $post['pinged'] = isset($_POST['pinged']) ? filter_var(trim($_POST['pinged']), FILTER_SANITIZE_STRING) : "";
    $post['guid'] = isset($_POST['guid']) ? filter_var(trim($_POST['guid']), FILTER_SANITIZE_STRING) : "";
    $post['menu_order'] = isset($_POST['menu_order']) ? filter_var(trim($_POST['menu_order']), FILTER_SANITIZE_NUMBER_INT) : "0";
    $post['comment_count'] = isset($_POST['comment_count']) ? filter_var(trim($_POST['comment_count']), FILTER_SANITIZE_NUMBER_INT) : "0";
    $post['post_date'] = isset($_POST['post_date']) ? filter_var(trim($_POST['post_date']), FILTER_SANITIZE_STRING) : date("Y-m-d H:i:s");
    $post['post_modified'] = isset($_POST['post_modified']) ? filter_var(trim($_POST['post_modified']), FILTER_SANITIZE_STRING) : date("Y-m-d H:i:s");
    $post['ping_status'] = isset($_POST['ping_status']) ? filter_var(trim($_POST['ping_status']), FILTER_SANITIZE_STRING) : "";
    $post['comment_status'] = isset($_POST['comment_status']) ? filter_var(trim($_POST['comment_status']), FILTER_SANITIZE_STRING) : "";
    $post['post_status'] = isset($_POST['post_status']) ? filter_var(trim($_POST['post_status']), FILTER_SANITIZE_STRING) : "";

    $post['terms'] = isset($_POST['terms']) ? $_POST['terms'] : array();

    $post['metas']['meta_title'] = isset($_POST['meta_title']) ? filter_var(trim($_POST['meta_title']), FILTER_SANITIZE_STRING) : "";
    $post['metas']['meta_keywords'] = isset($_POST['meta_keywords']) ? filter_var(trim($_POST['meta_keywords']), FILTER_SANITIZE_STRING) : "";
    $post['metas']['meta_description'] = isset($_POST['meta_description']) ? filter_var(trim($_POST['meta_description']), FILTER_SANITIZE_STRING) : "";
    $post['metas']['meta_others'] = isset($_POST['meta_others']) ? filter_var(trim($_POST['meta_others']), FILTER_SANITIZE_STRING) : "";
    $post['metas']['post_format'] = isset($_POST['post_format']) ? filter_var(trim($_POST['post_format']), FILTER_SANITIZE_STRING) : "standard";

    $post_status_msg = $post_status_msg = $sys['post_types'][$post['post_type']]['name'] . " saved successfully!";
    if (isset($_POST['visibility']) && trim($_POST['visibility']) == "private") {
        $post['post_status'] = "private";
        $post_status_msg = $sys['post_types'][$post['post_type']]['name'] . " published successfully!";
    } else if (isset($_POST['draft'])) {
        $post['post_status'] = "draft";
    } else if (isset($_POST['pending'])) {
        $post['post_status'] = "pending";
    } else if (isset($_POST['publish'])) {
        $post['post_status'] = "published";
        $post_status_msg = $post_status_msg = $sys['post_types'][$post['post_type']]['name'] . " published successfully!";
    }

    if ($post['post_type'] == "post" && !isUserHavePermission(MANAGE_POSTS_SECTION, getUserLoggedId())) {
        $msg = '<div class="alert alert-danger">You are not authorized manage posts!</div>';
    } else if ($post['post_type'] == "page" && !isUserHavePermission(MANAGE_PAGES_SECTION, getUserLoggedId())) {
        $msg = '<div class="alert alert-danger">You are not authorized manage pages!</div>';
    } else if ($post['post_title'] == '') {
        $msg = '<div class="alert alert-danger">Please enter post title</div>';
    } else if ($post['post_name'] == '') {
        $msg = '<div class="alert alert-danger">Please enter post name</div>';
    } else {
        $msg = '<div class="alert alert-success">' . $post_status_msg . '</div>';
        if (!updatePost($post)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}

$post = getPost($id, array(), true, true);
if ($post == null) {
    die("<div>You attempted to edit an item that doesn't exist. May be it was deleted?</div>");
}
$post_type = $post['post_type'];

if (!isset($sys['post_types'][$post_type])) {
    die('<div style="">Invalid Post Type</div>');
}
//Not authorized to access
if ($post_type == "post" && !isUserHavePermission(MANAGE_POSTS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}
if ($post_type == "page" && !isUserHavePermission(MANAGE_PAGES_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}
//redirects for other post types
if (!in_array($post_type, array("post", "page"))) {
    if ($post_type == "attachment") {
        header("location: media-add.php");
        exit();
    }
}

$post_categories = $post_tags = array();
foreach ($post['terms'] as $t) {
    if ($t['taxonomy'] == "category") {
        $post_categories[] = $t['id'];
    } else if ($t['taxonomy'] == "tag") {
        $post_tags[] = $t;
    }
}
$categories = getTerms(array(), array("taxonomy" => "category"));
$authors = getUsers(array('id', 'username', 'display_name'), array(), 0, -1);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit <?= $sys['post_types'][$post_type]['name'] ?> - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
        <link rel="stylesheet" href="<?= $sys['site_url'] ?>/admin/plugins/select2/select2.min.css">
        <style>
            #permalink {
                padding: 3px 0px;
            }
            #permalink .editable-part {
                font-weight: bold;
            }
            #permalink-edit-buttons .btn-edit, .btn-ok {
                padding: 2px 5px;
            }
            #permalink-edit-buttons .btn-cancel {
                padding: 2px 5px;
                border: 0;
                background: none;
            }
            .btn-media-add {
                margin-bottom: 5px;
            }
            #post-status-display, 
            #post-visibility-display, 
            #post-timestamp-display,
            #post-views-display {
                text-transform: capitalize;
            }
            .timestamp-wrap input {
                height: 21px;
                line-height: 14px;
                padding: 0;
                vertical-align: top;
                font-size: 12px;
            }
            .timestamp-wrap #tm {
                height: 21px;
                line-height: 14px;
                padding: 0;
                vertical-align: top;
                font-size: 12px;
            }
            .timestamp-wrap #td {
                width: 2em;
            }
            .timestamp-wrap #ty {
                width: 3.4em;
            }
            .timestamp-wrap #th {
                width: 2em;
            }
            .timestamp-wrap #tmin {
                width: 2em;
            }
            .custom-height-pad {
                height: 30px;
                padding: 0px 6px;
            }
            .categories {
                max-height: 150px;
                overflow: auto;
                border: solid thin #ecf0f5;
                padding: 6px 6px;
            }
            .custom-nav {    
                position: absolute;
                width: 96%;
                z-index: 1;
                background: white;
            }
            #selected-tags {
                padding: 5px 0px;
            }
            #selected-tags li {
                margin-right: 5px;
            }
            #selected-tags .delete {
                cursor: pointer;
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
                        Edit <?= $sys['post_types'][$post_type]['name'] ?>
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class=""><a href="<?= $sys['site_url'] ?>/admin/posts.php?type=<?= $post_type ?>"><?= $sys['post_types'][$post_type]['plural'] ?></a></li>                        
                        <li class="active">Edit <?= $sys['post_types'][$post_type]['name'] ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <form id="post-add-form" action="" method="post" enctype="multipart/form-data">
                        <!--<input type="hidden" id="id" name="id" value="<?= $post['id'] ?>"/>-->
                        <?= $msg ?>
                        <div class="row">                        
                            <div class="col-md-9">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="post_title" value="<?= $post['post_title'] ?>" id="post_title" placeholder="Title" required="">
                                    <div id="permalink"><b>Permalink:</b> 
                                        <span id="sample-permalink"><a href="" target="_blank"><?= $sys['site_url'] ?>/<span class="editable-part"><?= $post['post_name'] ?></span>/</a></span>
                                        <span id="permalink-edit-buttons"><button type="button" class="btn btn-sm btn-default btn-edit">Edit</button></span>
                                    </div>
                                </div>
                                <button class="btn btn-default btn-media-add"><i class="fa fa-film"></i> &nbsp;Add Media</button>
                                <div class="form-group">
                                    <textarea id="content" name="content" class="form-control" placeholder="Content"><?= $post['post_content'] ?></textarea>
                                </div>
                                <div class="box box-default">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">SEO</h3>
                                        <div class="box-tools">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div><!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="meta_title" value="<?= isset($post['metas']['meta_title']) ? $post['metas']['meta_title'] : "" ?>" id="meta_title" placeholder="Meta Title">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="meta_keywords" value="<?= isset($post['metas']['meta_keywords']) ? $post['metas']['meta_keywords'] : "" ?>" id="meta_keywords" placeholder="Meta Keywords">
                                        </div>
                                        <div class="form-group">
                                            <textarea class="form-control" name="meta_description" placeholder="Meta Description"><?= isset($post['metas']['meta_description']) ? $post['metas']['meta_description'] : "" ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <textarea name="meta_others" class="form-control" placeholder="Meta Others"><?= isset($post['metas']['meta_others']) ? $post['metas']['meta_others'] : "" ?></textarea>
                                        </div>
                                    </div><!-- /.box-body -->
                                </div>
                                <?php if (in_array($post_type, array("post"))) { ?>
                                    <div class="box box-default">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Post Settings</h3>
                                            <div class="box-tools">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                            </div>
                                        </div>
                                        <div class="box-body">

                                        </div>
                                    </div>
                                    <div class="box box-default">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Excerpt</h3>
                                            <div class="box-tools">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <div class="form-group">
                                                <textarea name="post_excerpt" class="form-control" placeholder="Excerpt"><?= $post['post_excerpt'] ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="box box-default">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Discussion</h3>
                                        <div class="box-tools">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="form-group">
                                            <input type="checkbox" name="comment_status" value="open" <?= $post['comment_status'] == 'open' ? "checked" : "" ?>/> Allow Comments<br/>
                                            <input type="checkbox" name="ping_status" value="open" <?= $post['ping_status'] == 'open' ? "checked" : "" ?>/> Allow Pingbacks
                                        </div>
                                    </div>
                                </div>
                                <div class="box box-default">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Author</h3>
                                        <div class="box-tools">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="form-group">
                                            <select class="form-control" name="post_author">
                                                <?php foreach ($authors as $a) { ?>
                                                    <option value="<?= $a['id'] ?>" <?= $a['id'] == $post['post_author'] ? "selected" : "" ?>><?= $a['display_name'] . " (" . $a['username'] . ")" ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>              
                            <div class="col-md-3">
                                <div class="box box-default">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Publish</h3>

                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="publishing-actions">
                                                    <?php if ($post['post_status'] == "draft") { ?>
                                                        <input type="submit" name="draft" id="draft-post" value="Save Draft" class="btn btn-default btn-sm pull-left">
                                                    <?php } else if ($post['post_status'] == "pending") { ?>
                                                        <input type="submit" name="pending" id="save-post" value="Save as Pending" class="btn btn-default btn-sm pull-left">
                                                    <?php } ?>
                                                    <a class="btn btn-default btn-sm pull-right" href="#" target="_blank" id="post-preview">Preview</a>
                                                    <div class="clear" style="clear: both"></div>
                                                </div>
                                                <div class="publishing-actions" style="margin-top: 5px;line-height: 1.7;">
                                                    <div class="post-status">
                                                        Status: <strong id="post-status-display"><?= $post['post_status'] ?></strong>
                                                        <a href="#edit_post_status" class="edit" role="button" style="display: inline;"><span aria-hidden="true">Edit</span></a>
                                                        <div id="edit_post_status" class="" style="display: none;">
                                                            <select name="post_status" id="post_status" class="form-control custom-height-pad" style="width:auto;">
                                                                <?php if ($post['post_status'] == "published") { ?>
                                                                    <option value="published" selected>Published</option>
                                                                <?php } ?>
                                                                <option value="pending" <?= $post['post_status'] == 'pending' ? "selected" : "" ?>>Pending Review</option>
                                                                <option value="draft" <?= $post['post_status'] == 'draft' ? "selected" : "" ?>>Draft</option>
                                                            </select>
                                                            <a href="#edit_post_status" class="btn btn-default btn-sm ok">OK</a>
                                                            <a href="#edit_post_status" class="cancel">Cancel</a>
                                                        </div>
                                                    </div>
                                                    <div class="post-visibility">
                                                        Visibility: <strong id="post-visibility-display">Public</strong>
                                                        <a href="#edit_post_visibility" class="edit" role="button"><span aria-hidden="true">Edit</span></a>
                                                        <div id="edit_post_visibility" class="" style="display: none;line-height: 0.7;">
                                                            <input type="radio" name="visibility" id="visibility-radio-public" value="public" checked="checked"> <label for="visibility-radio-public" class="selectit">Public</label><br>
                                                            <span id="sticky-span" style="margin-left: 7px;"><input id="sticky" name="sticky" type="checkbox" value="sticky"> <label for="sticky" class="selectit">Stick post to front page</label><br></span>
                                                            <input type="radio" name="visibility" id="visibility-radio-password" value="password"> <label for="visibility-radio-password" class="selectit">Password protected</label><br>
                                                            <span id="password-span" style="display: none;"><label for="post_password">Password:</label> <input type="text" name="post_password" id="post_password" value="" maxlength="255" class="form-control custom-height-pad"><br></span>
                                                            <input type="radio" name="visibility" id="visibility-radio-private" value="private"> <label for="visibility-radio-private" class="selectit">Private</label><br>
                                                            <p>
                                                                <a href="#edit_post_visibility" class="btn btn-default btn-sm ok">OK</a>
                                                                <a href="#edit_post_visibility" class="cancel">Cancel</a>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="post-timestamp">
                                                        <span id="post-timestamp-display">Publish <strong>immediately</strong></span>
                                                        <a href="#edit_timestamp" class="edit" role="button"><span aria-hidden="true">Edit</span></a>
                                                        <fieldset id="edit_timestamp" class="" style="display: none;">
                                                            <div class="timestamp-wrap">
                                                                <label>
                                                                    <select id="tm" name="tm" class="form-control">
                                                                        <?php foreach ($MONTHS as $key => $value) { ?>
                                                                            <option value="<?= $key ?>" <?= $key == date("m") ? "selected" : "" ?>><?= $key . "-" . $value ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </label> 
                                                                <label><input type="text" class="form-control" id="td" name="td" value="<?= date("d") ?>" size="2" maxlength="2" autocomplete="off"></label>, 
                                                                <label><input type="text" class="form-control" id="ty" name="ty" value="<?= date("Y") ?>" size="4" maxlength="4" autocomplete="off"></label> @ 
                                                                <label><input type="text" class="form-control" id="th" name="th" value="<?= date("H") ?>" size="2" maxlength="2" autocomplete="off"></label>:
                                                                <label><input type="text" class="form-control" id="tmin" name="tmin" value="<?= date("i") ?>" size="2" maxlength="2" autocomplete="off"></label>
                                                            </div>
                                                            <p>
                                                                <a href="#edit_timestamp" class="btn btn-default btn-sm ok">OK</a>
                                                                <a href="#edit_timestamp" class="cancel">Cancel</a>
                                                            </p>
                                                        </fieldset>
                                                    </div>
                                                    <div class="post-views">
                                                        <span id="post-views-display">Post Views: <strong>0</strong></span>
                                                        <a href="#edit_post_views" class="edit">Edit</a>
                                                        <div id="edit_post_views" class="" style="display: none;line-height: 0.7;">
                                                            <p>Adjust the views count for this post.</p>
                                                            <input type="text" name="post_views" id="post-views-input" value="0" class="form-control custom-height-pad"/>
                                                            <p>
                                                                <a href="#edit_post_views" class="btn btn-default btn-sm ok">OK</a>
                                                                <a href="#edit_post_views" class="cancel">Cancel</a>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="content-score" id="content-score">
                                                        <span class="image yoast-logo svg bad"></span>
                                                        <span class="score-text">Readability: <strong>Needs improvement</strong></span>
                                                    </div>
                                                    <div class="keyword-score" id="keyword-score">
                                                        <span class="image yoast-logo svg na"></span>
                                                        <span class="score-text">SEO: <strong>Not available</strong></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.row -->
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <input type="submit" class="btn btn-success pull-right" name="<?= $post['post_status'] == "published" ? "update" : "publish" ?>" value="<?= $post['post_status'] == "published" ? "Update" : "Publish" ?>"/>
                                    </div>
                                    <!-- /.footer -->
                                </div>
                                <?php if (in_array($post_type, array("post"))) { ?>
                                    <div class="box box-default">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Format</h3>
                                            <div class="box-tools">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                            </div>
                                        </div><!-- /.box-header -->
                                        <div class="box-body">
                                            <div class="form-group">
                                                <?php foreach ($sys['post_formats'] as $key => $value) { ?>
                                                    <input type="radio" name="post_format" value="<?= $key ?>" <?= isset($post['metas']['post_format']) && trim($post['metas']['post_format']) == $key ? "checked" : "" ?>/> <?= $value['name'] ?><br/>
                                                <?php } ?>
                                            </div>
                                        </div><!-- /.box-body -->
                                    </div>
                                    <div class="box box-default">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Categories</h3>
                                            <div class="box-tools">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                            </div>
                                        </div><!-- /.box-header -->
                                        <div class="box-body">
                                            <div class="form-group">
                                                <ul class="nav categories">
                                                    <?php
                                                    foreach ($categories as $category) {
                                                        ?>     
                                                        <li><label><input type="checkbox" name="terms[]" value="<?= $category['id'] ?>" <?= in_array($category['id'], $post_categories) ? "checked" : "" ?>> <?= $category['name'] ?></label><li>
                                                        <?php } ?>                    
                                                </ul>
                                            </div>
                                            <a href="#" class="add-new-category">+ Add New Category</a>
                                            <div id="add-category" style="display: none; margin-top: 10px;">
                                                <div class="form-group">
                                                    <input id="category-name" class="form-control" placeholder="Category Name"/>
                                                </div>
                                                <div class="form-group">
                                                    <select id="category-parent" class="form-control">
                                                        <option value="0">- Parent Category -</option>
                                                        <?php
                                                        foreach ($categories as $category) {
                                                            ?>     
                                                            <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                                        <?php } ?>                    
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <input type="button" id="add-new-category" name="add-new-category" value="Add New Category" class="btn btn-default"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box box-default">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Tags</h3>
                                            <div class="box-tools">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                            </div>
                                        </div><!-- /.box-header -->
                                        <div class="box-body">
                                            <div class="input-group">
                                                <input type="text" id="tag" class="form-control" autocomplete="off">
                                                <span class="input-group-btn">
                                                    <button type="submit" id="add-new-tag" class="btn btn-default btn-flat">Add</button>
                                                </span>
                                            </div>
                                            <ul class="nav custom-nav" id="tags">

                                            </ul>
                                            <ul class="nav navbar-nav" id="selected-tags" style="width: 100%;">
                                                <?php foreach ($post_tags as $t) { ?>
                                                    <li>
                                                        <span class="badge">
                                                            <span class="delete"><i class="fa fa-times"></i></span>
                                                            <input type="hidden" name="terms[]" value="<?= $t['id'] ?>"/>
                                                            <?= $t['name'] ?>
                                                        </span>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                            <a href="#">Choose from most used tags</a>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="box box-default">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Featured Image</h3>
                                        <div class="box-tools">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div><!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="form-group">
                                            <a href="#">Set featured image</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.row -->
                    </form>
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>   

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>
        <?php include_once 'post-media-add.php'; ?>
        <script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script> 
        <script>
            $(function () {
                CKEDITOR.replace('content');
                CKEDITOR.instances.content.on("change", function () {
                    var post_title = $("#post_title").val();
                    if (post_title === "" || $("#permalink").css("display") === "block" || $("#sample-permalink span.editable-part").text() !== "") {
                        console.log("returned");
                        return;
                    }
                    //ajax call to generate slug/permalink
                    var action = "<?= $sys['site_url'] ?>/requests.php?action=get-slug";
                    var data = new FormData();
                    data.append('post_type', "<?= $post_type ?>");
                    data.append('post_title', $("#post_title").val());
                    $.ajax({
                        type: 'POST',
                        url: action,
                        data: data,
                        /*THIS MUST BE DONE FOR FILE UPLOADING*/
                        contentType: false,
                        processData: false,
                    }).done(function (data) {
                        if (data.code === '<?= SUCCESS_RESPOSE_CODE ?>') {
                            $("#id").val(data.post_id);
                            $("#sample-permalink").html('<a href="" target="_blank"><?= $sys['site_url'] ?>/<span class="editable-part">' + data.post_name + '</span>/</a>');
                            $("#permalink-edit-buttons").html('<button type="button" class="btn btn-sm btn-default btn-edit">Edit</button>');
                            $("#post-add-form").attr("action", "post-edit.php");
                        }
                    }).fail(function (data) {
                        //any message
                    });
                    $("#permalink").css("display", "block");
                });
            });

            var permalink = "";
            $("#permalink-edit-buttons").on("click", ".btn-edit, .btn-ok, .btn-cancel", function () {
                if ($(this).text() == "Edit") {
                    permalink = $("#sample-permalink span.editable-part").text();
                    $("#sample-permalink").html('<?= $sys['site_url'] ?>/<input type="text" id="post_name" name="post_name" value="' + permalink + '">/');
                    $("#permalink-edit-buttons").html('<button type="button" class="btn btn-sm btn-default btn-ok">Ok</button><button type="button" class="btn btn-sm btn-default btn-cancel">Cancel</button>');
                    //$(this).html("Ok");
                } else if ($(this).text() == "Ok") {
                    //calling ajax and update post_name value
                    var action = "<?= $sys['site_url'] ?>/requests.php?action=get-slug";
                    var data = new FormData();
                    data.append('id', $("#id").val());
                    data.append('post_title', $("#post_title").val());
                    data.append('post_name', $("#post_name").val());
                    $.ajax({
                        type: 'POST',
                        url: action,
                        data: data,
                        /*THIS MUST BE DONE FOR FILE UPLOADING*/
                        contentType: false,
                        processData: false,
                    }).done(function (data) {
                        if (data.code === '<?= SUCCESS_RESPOSE_CODE ?>') {
                            $("#id").val(data.post_id);
                            $("#sample-permalink").html('<a href="" target="_blank"><?= $sys['site_url'] ?>/<span class="editable-part">' + data.post_name + '</span>/</a>');
                            $("#permalink-edit-buttons").html('<button type="button" class="btn btn-sm btn-default btn-edit">Edit</button>');
                            $("#post-add-form").attr("action", "post-edit.php");
                        }
                    }).fail(function (data) {
                        //any message
                    });
                    //$(this).html("Edit");
                } else {
                    $("#sample-permalink").html('<a href="" target="_blank"><?= $sys['site_url'] ?>/<span class="editable-part">' + permalink + '</span>/</a>');
                    $("#permalink-edit-buttons").html('<button type="button" class="btn btn-sm btn-default btn-edit">Edit</button>');
                    //$(this).html("Edit");
                }
            });

            $(".btn-media-add").click(function (e) {
                e.preventDefault();
                $("#add-media-modal").modal('show');
                loadmore();
            });

            $("a.edit").click(function (e) {
                $(this).hide();
                var container_id = $(this).attr("href");
                $(container_id).slideDown("slow");
                return false;
            });
            $("a.ok").click(function (e) {
                var container_id = $(this).attr("href");
                $(container_id).slideUp("slow");
                $('a[href="' + container_id + '"]').show();
                switch (container_id) {
                    case "#edit_post_status":
                        $("#post-status-display").html($("#post_status option:selected").val())
                        break;
                    case "#edit_post_visibility":
                        $("#post-visibility-display").html($("input[name='visibility']:checked").val());
                        if ($("input[name='visibility']:checked").val() === "private") {
                            $("#post-status-display").html("Privately Published");
                            $("#draft-post").hide();
                        } else {
                            $("#post-status-display").html("Published");
                            $("#draft-post").show();
                        }
                        break;
                    case "#edit_timestamp":
                        var cur_time = "<?= date("Y-m-d H:i") ?>";
                        var sel_time = $("#ty").val() + "-" + $("#tm").val() + "-" + $("#td").val() + " " + $("#th").val() + ":" + $("#tmin").val();
                        var html = cur_time === sel_time ? "Publish <strong>Immediately</strong>" : "Scheduled for <strong>" + sel_time + "</strong>";
                        $("#post-timestamp-display").html(html);
                        //code pending
                        break;
                    case "#edit_post_views":
                        $("#post-views-display").html("Post Views: <b>" + $("#post-views-input").val() + "</b>");
                        break;
                }
                return false;
            });
            $("a.cancel").click(function (e) {
                var container_id = $(this).attr("href");
                $(container_id).slideUp("slow");
                $('a[href="' + container_id + '"]').show();
                return false;
            });

            $("#edit_post_visibility input[type=radio]").change(function (e) {
                if ($(this).val() == "public") {
                    $("#sticky-span").show();
                    $("#password-span").hide();
                    $("#password-span input").val("");
                }
                if ($(this).val() == "password") {
                    $("#sticky-span").hide();
                    $("#password-span").show();
                }
                if ($(this).val() == "private") {
                    $("#sticky-span").hide();
                    $("#password-span").hide();
                    $("#password-span input").val("");
                }
            });

            $("a.add-new-category").click(function (e) {
                $("#add-category").toggle("slow");
                return false;
            });
            $("#add-new-category").on("click", function (e) {
                e.preventDefault();
                var name = $("#category-name").val();
                var parent = $("#category-parent").val();
                $.ajax({
                    type: "GET",
                    url: "<?= $sys['site_url'] ?>/requests.php?action=add-term&taxonomy=category&name=" + name + "&parent=" + parent,
                    success: function (response) {
                        if (response.data.ID !== "") {
                            $("ul.categories").append('<li><label>' + '<input type="checkbox" name="terms[]" value="' + response.data.ID + '" checked/> ' + response.data.name + '</label></li>');
                        }
                        $("#category-name").val("");
                        $("#category-parent").val(0);
                    }
                });
            });

            $("#tag").on("keyup", function (e) {
                var name = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "<?= $sys['site_url'] ?>/requests.php?action=get-terms&taxonomy=tag&name=" + name,
                    success: function (response) {
                        $("#tags").html(response.html);
                        $("#tags").css("border", "solid thin #d2d6de");
                        if (response.html === "") {
                            $("#tags").css("border", "none");
                        }
                    }
                });
            });
            $("#tags").on("click", "a", function (e) {
                var selectedtagid = $(this).attr("data-id");
                var alreadyselected = false;
                var html = '<li>'
                        + '<span class="badge">'
                        + '<span class="delete"><i class="fa fa-times"></i></span> '
                        + '<input type="hidden" name="terms[]" value="' + $(this).attr("data-id") + '"/>'
                        + $(this).text()
                        + '</span>'
                        + '</li>';
                $("#selected-tags input").each(function () {
                    if (selectedtagid === $(this).val()) {
                        alreadyselected = true;
                    }
                });
                if (!alreadyselected) {
                    $("#selected-tags").prepend(html);
                }
                //clear tags list
                $("#tags").html("");
                $("#tags").css("border", "none");
                return false;
            });
            $("#selected-tags").on("click", ".delete", function () {
                $(this).parent().remove();
            });
            $("#add-new-tag").on("click", function (e) {
                e.preventDefault();
                var name = $("#tag").val();
                $.ajax({
                    type: "GET",
                    url: "<?= $sys['site_url'] ?>/requests.php?action=add-terms&taxonomy=tag&name=" + name,
                    success: function (response) {
                        for (var i = 0; i < response.data.length; i++) {
                            if (response.data[i].ID !== "") {
                                var alreadyselected = false;
                                var html = '<li>'
                                        + '<span class="badge">'
                                        + '<span class="delete"><i class="fa fa-times"></i></span> '
                                        + '<input type="hidden" name="terms[]" value="' + response.data[i].ID + '"/>'
                                        + response.data[i].name
                                        + '</span>'
                                        + '</li>';
                                $("#selected-tags input").each(function () {
                                    if (response.data[i].ID === $(this).val()) {
                                        alreadyselected = true;
                                    }
                                });
                                if (!alreadyselected) {
                                    $("#selected-tags").prepend(html);
                                }
                            }
                        }

                        $("#tag").val("");
                        $("#tags").html("");
                        $("#tags").css("border", "none");
                    }
                });
            });
            function insertContent(html) {
                //var doctarget = opener.CKEDITOR.instances.editable;
                //doctarget.insertHtml(html);
                for(var i in CKEDITOR.instances) {
                    CKEDITOR.instances[i].insertHtml(html);
                }
                return true;
            }
        </script>        
    </body>
</html>