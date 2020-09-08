<?php

// +------------------------------------------------------------------------+
// | @author Rajesh Kumar Sahanee
// | @author_url 1: http://www.zatackcoder.com
// | @author_email: rajeshsahanee@gmail.com   
// +------------------------------------------------------------------------+

require 'system/init.php';

$action = isset($_GET['action']) ? secure($_GET['action']) : '';

if ($action == 'uploadPhoto') {
    $response['code'] = ERROR_RESPOSE_CODE;

    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {
        $uploaded = uploadProfilePic($_FILES['photo']);
        $user_id = isset($_REQUEST['user_id']) ? trim($_REQUEST['user_id']) : getUserLoggedId();
        if (!$uploaded['error'] && updateUserMeta($user_id, "photo", $uploaded['filename'])) {
            $response['code'] = SUCCESS_RESPOSE_CODE;
            $response['filepath'] = $uploaded['filename'];
        } else {
            $response['msg'] = $response['errormsg'];
        }
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}
if ($action == 'upload') {
    $response['code'] = ERROR_RESPOSE_CODE;

    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        $uploaded = upload($_FILES["file"]);

        if (!$uploaded['error']) {

            $post['post_type'] = "attachment";
            $post['post_title'] = $uploaded['file_name']; //file name without extension
            $post['post_content'] = "";
            $post['post_content_filtered'] = "";
            $post['post_excerpt'] = "";
            $post['post_author'] = getUserLoggedId();
            $post['post_password'] = "";
            $post['post_name'] = strtolower($uploaded['file_name']); //file name lower case without extension 
            $post['post_parent'] = "0";
            $post['post_mime_type'] = $uploaded['file_type']; //file mime type
            $post['to_ping'] = "";
            $post['pinged'] = "";
            $post['guid'] = $sys['site_url'] . "/" . $uploaded['upload_root'] . "/" . $uploaded['file_path']; //file absolute path
            $post['menu_order'] = "0";
            $post['comment_count'] = "0";
            $post['post_date'] = date("Y-m-d H:i:s");
            $post['post_modified'] = date("Y-m-d H:i:s");
            $post['ping_status'] = "closed";
            $post['comment_status'] = "open";
            $post['post_status'] = "inherit";

            $post['metas']['file_path'] = $uploaded['file_path'];
            $post['metas']['metadata'] = serialize($uploaded['metadata']);

            $post_id = addPost($post);
            if ($post_id) {
                $response['code'] = SUCCESS_RESPOSE_CODE;
                $response['msg'] = "Upload Completed";
                $response['file_name'] = $post['post_title'];
                $response['file_path'] = $post['guid'];
                $response['post_id'] = $post_id;
            } else {
                $response['msg'] = $queryerrormsg;
            }
        } else {
            $response['msg'] = $uploaded['errormsg'];
        }
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}
if ($action == 'upload-brand-image') {
    $response['code'] = ERROR_RESPOSE_CODE;
    $upload_path = 'uploads/brandimages/';
    $upload_url = $sys['config']['site_url'] . '/' . $upload_path;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //checking the required parameters from the request 
        if (isset($_FILES['image']['name'])) {
            $fileinfo = pathinfo($_FILES['image']['name']);
            $extension = $fileinfo['extension'];
            $filename = 'brandimage_' . generateKey() . '_' . date('d') . '_' . md5(time());
            $file_url = $upload_url . $filename . '.' . $extension;

            //file path to upload in the server 
            $file_path = $upload_path . $filename . '.' . $extension;
            try {
                //saving the file 
                if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                    $response['code'] = SUCCESS_RESPOSE_CODE;
                    $response['file_url'] = $file_url;
                    $response['msg'] = 'Uploaded successfully!';
                }
            } catch (Exception $e) {
                $response['code'] = ERROR_RESPOSE_CODE;
                $response['msg'] = 'Error!';
            }
        } else {
            $response['code'] = ERROR_RESPOSE_CODE;
            $response['msg'] = 'Please choose file!';
        }
    }
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}
if ($action == 'upload-category-image') {
    $response['code'] = ERROR_RESPOSE_CODE;
    $upload_path = 'uploads/categoryimages/';
    $upload_url = $sys['config']['site_url'] . '/' . $upload_path;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //checking the required parameters from the request 
        if (isset($_FILES['image']['name'])) {
            $fileinfo = pathinfo($_FILES['image']['name']);
            $extension = $fileinfo['extension'];
            $filename = 'category_' . generateKey() . '_' . date('d') . '_' . md5(time());
            $file_url = $upload_url . $filename . '.' . $extension;

            //file path to upload in the server 
            $file_path = $upload_path . $filename . '.' . $extension;
            try {
                //saving the file 
                if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                    $response['code'] = SUCCESS_RESPOSE_CODE;
                    $response['file_url'] = $file_url;
                    $response['msg'] = 'Uploaded successfully!';
                }
            } catch (Exception $e) {
                $response['code'] = ERROR_RESPOSE_CODE;
                $response['msg'] = 'Error!';
            }
        } else {
            $response['code'] = ERROR_RESPOSE_CODE;
            $response['msg'] = 'Please choose file!';
        }
    }
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}
if ($action == 'upload-logo') {
    $response['code'] = ERROR_RESPOSE_CODE;
    $upload_path = 'uploads/logos/';
    $upload_url = $sys['site_url'] . '/' . $upload_path;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //checking the required parameters from the request 
        if (isset($_FILES['image']['name'])) {
            $fileinfo = pathinfo($_FILES['image']['name']);
            $extension = $fileinfo['extension'];
            $filename = 'logo_' . generateKey() . '_' . date('d') . '_' . md5(time());
            $file_url = $upload_url . $filename . '.' . $extension;

            //file path to upload in the server 
            $file_path = $upload_path . $filename . '.' . $extension;
            try {
                //saving the file 
                if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                    $response['code'] = SUCCESS_RESPOSE_CODE;
                    $response['file_url'] = $file_url;
                    $response['msg'] = 'Uploaded successfully!';
                    $response['htmlmsg'] = '<div class="alert alert-success">Uploaded successfully!</div>';
                }
            } catch (Exception $e) {
                $response['code'] = ERROR_RESPOSE_CODE;
                $response['msg'] = 'Error!';
                $response['htmlmsg'] = '<div class="alert alert-danger">Error!</div>';
            }
        } else {
            $response['code'] = ERROR_RESPOSE_CODE;
            $response['msg'] = 'Please choose file!';
            $response['htmlmsg'] = '<div class="alert alert-danger">Please choose file!</div>';
        }
    }
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}
if ($action == 'upload-product-download') {
    $response['code'] = ERROR_RESPOSE_CODE;
    $upload_path = 'uploads/productdownloads/';
    $upload_url = $sys['site_url'] . '/' . $upload_path;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //checking the required parameters from the request 
        if (isset($_FILES['download']['name'])) {
            $fileinfo = pathinfo($_FILES['download']['name']);
            $extension = $fileinfo['extension'];
            $filename = 'download_' . generateKey() . '_' . date('d') . '_' . md5(time());
            $file_url = $upload_url . $filename . '.' . $extension;

            //file path to upload in the server 
            $file_path = $upload_path . $filename . '.' . $extension;
            try {
                //saving the file 
                if (move_uploaded_file($_FILES['download']['tmp_name'], $file_path)) {
                    $response['code'] = SUCCESS_RESPOSE_CODE;
                    $response['file_url'] = $file_url;
                    $response['msg'] = 'Uploaded successfully!';
                    $response['htmlmsg'] = '<div class="alert alert-success">Uploaded successfully!</div>';
                }
            } catch (Exception $e) {
                $response['code'] = ERROR_RESPOSE_CODE;
                $response['msg'] = 'Error!';
                $response['htmlmsg'] = '<div class="alert alert-danger">Error!</div>';
            }
        } else {
            $response['code'] = ERROR_RESPOSE_CODE;
            $response['msg'] = 'Please choose file!';
            $response['htmlmsg'] = '<div class="alert alert-danger">Please choose file!</div>';
        }
    }
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}
if ($action == 'upload-product-image') {
    $response['code'] = ERROR_RESPOSE_CODE;
    $upload_path = 'uploads/productimages/';
    $upload_url = $sys['site_url'] . '/' . $upload_path;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //checking the required parameters from the request 
        if (isset($_FILES['image']['name'])) {
            $fileinfo = pathinfo($_FILES['image']['name']);
            $extension = $fileinfo['extension'];
            $filename = 'product_' . generateKey() . '_' . date('d') . '_' . md5(time());
            $file_url = $upload_url . $filename . '.' . $extension;

            //file path to upload in the server 
            $file_path = $upload_path . $filename . '.' . $extension;
            try {
                //saving the file 
                if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                    $response['code'] = SUCCESS_RESPOSE_CODE;
                    $response['file_url'] = $file_url;
                    $response['msg'] = 'Uploaded successfully!';
                    $response['htmlmsg'] = '<div class="alert alert-success">Uploaded successfully!</div>';
                }
            } catch (Exception $e) {
                $response['code'] = ERROR_RESPOSE_CODE;
                $response['msg'] = 'Error!';
                $response['htmlmsg'] = '<div class="alert alert-danger">Error!</div>';
            }
        } else {
            $response['code'] = ERROR_RESPOSE_CODE;
            $response['msg'] = 'Please choose file!';
            $response['htmlmsg'] = '<div class="alert alert-danger">Please choose file!</div>';
        }
    }
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}
if ($action == 'upload-other-file') {
    $response['code'] = ERROR_RESPOSE_CODE;
    $upload_path = 'uploads/otherfiles/';
    $upload_url = $sys['site_url'] . '/' . $upload_path;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //checking the required parameters from the request 
        if (isset($_FILES['file']['name'])) {
            $fileinfo = pathinfo($_FILES['file']['name']);
            $extension = $fileinfo['extension'];
            $filename = $tmpfilename = $_FILES['file']['name'];
            while (file_exists($upload_path . $tmpfilename)) {
                $filename = rand(1000, 9999) . "_" . $tmpfilename;
            }
            $file_url = $upload_url . $filename;

            //file path to upload in the server 
            $file_path = $upload_path . $filename;
            try {
                //saving the file 
                if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
                    $response['code'] = SUCCESS_RESPOSE_CODE;
                    $response['file_name'] = $filename;
                    $response['file_ext'] = $extension;
                    $response['file_url'] = $file_url;
                    $response['msg'] = 'Uploaded successfully!';
                    $response['htmlmsg'] = '<div class="alert alert-success">Uploaded successfully!</div>';
                }
            } catch (Exception $e) {
                $response['code'] = ERROR_RESPOSE_CODE;
                $response['msg'] = 'Error!';
                $response['htmlmsg'] = '<div class="alert alert-danger">Error!</div>';
            }
        } else {
            $response['code'] = ERROR_RESPOSE_CODE;
            $response['msg'] = 'Please choose file!';
            $response['htmlmsg'] = '<div class="alert alert-danger">Please choose file!</div>';
        }
    }
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'media') {
    $response['code'] = SUCCESS_RESPOSE_CODE;

    $filters = array("post_type" => "attachment");
    if (isset($_REQUEST['type']) && trim($_REQUEST['type']) <> "") {
        if (in_array(trim($_REQUEST['type']), array("image", "audio", "video"))) {
            $filters['post_mime_type_like'] = filter_var(trim($_REQUEST['type']), FILTER_SANITIZE_STRING);
        }
        if (trim($_REQUEST['type']) == "unattached") {
            $filters['post_parent'] = '0';
        }
        if (trim($_REQUEST['type']) == "mine") {
            $filters['post_author'] = getUserLoggedId();
        }
    }
    if (isset($_REQUEST['year_month'])) {
        $filters['post_date'] = filter_var(trim($_REQUEST['year_month']), FILTER_SANITIZE_STRING);
    }
    if (isset($_REQUEST['q'])) {
        $filters['q'] = filter_var(trim($_REQUEST['q']), FILTER_SANITIZE_STRING);
    }

    $offset = 0;
    $limit = isset($_REQUEST['per_page']) ? filter_var(trim($_REQUEST['per_page']), FILTER_SANITIZE_NUMBER_INT) : 16;
    if (isset($_REQUEST['paged'])) {
        $offset = $_REQUEST['paged'] * $limit - $limit;
        $offset = $offset < 0 ? 0 : $offset;
    }
    $order_by = "id";
    $order = "DESC";
    $posts = getPosts(array('id', 'post_title', 'guid', 'post_mime_type'), $filters, $offset, $limit, $order_by, $order);
    $items = array();
    foreach ($posts as $post) {
        $item = array();
        $item['post_id'] = $post['id'];
        $item['file_name'] = $post['post_title'];
        $item['file_path'] = $sys['site_url'] . "/" . $sys['upload_root'] . "/" . getPostMeta($post['id'], "file_path"); //later it will be updated with thumbnail path
        $item['file_type'] = $post['post_mime_type'];

        if (strpos($post['post_mime_type'], 'image') !== FALSE) {
            list($width, $height, $type, $attr) = @getimagesize(str_replace(' ', '%20', $post['guid']));
            $item['width'] = $width;
            $item['height'] = $height;
        }
        $items[] = $item;
    }
    $response['items'] = $items;
    $response['max_pages'] = intval(count(getPosts(array("id"), $filters, 0, -1)) / $limit) + 1;
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'media-info') {
    $response['code'] = SUCCESS_RESPOSE_CODE;

    $post_id = filter_var(trim($_REQUEST['post_id']), FILTER_SANITIZE_STRING);
    $post = getPost($post_id, array(), true);

    $media['post_id'] = $post['id'];
    $media['file_name'] = $post['post_title'];
    $media['file_path'] = $sys['site_url'] . "/" . $sys['upload_root'] . "/" . $post['metas']['file_path']; //later it will be updated with thumbnail path
    $media['file_type'] = $post['post_mime_type'];
    $media['file_size'] = isset($post['metas']['file_size']) ? $post['metas']['file_size'] : filesize($sys['upload_root'] . "/" . $post['metas']['file_path']);
    $media['uploaded'] = date("F d, Y", strtotime($post['post_date']));
    $media['metadata'] = unserialize($post['metas']['metadata']);

    $response['data'] = $media;
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'add-menu-item') {
    $html = "";
    if (isset($_REQUEST['menu-item']) && is_array($_REQUEST['menu-item'])) {
        foreach ($_REQUEST['menu-item'] as $mi) {
            $post = array(
                'post_type' => "menu_item",
                'post_title' => "", //will be updated below conditionally
                'post_content' => "",
                'post_content_filtered' => "",
                'post_excerpt' => "",
                'post_author' => getUserLoggedId(),
                'post_password' => "",
                'post_name' => "", //updated with id after saving menu
                'post_parent' => "0",
                'post_mime_type' => "",
                'to_ping' => "",
                'pinged' => "",
                'guid' => "", //updated after insert
                'menu_order' => "1",
                'comment_count' => "0",
                'ping_status' => "closed",
                'comment_status' => "closed",
                'post_status' => "draft"
            );

            $post['metas']['menu_item_type'] = isset($mi['menu-item-type']) ? filter_var($mi['menu-item-type'], FILTER_SANITIZE_STRING) : "";
            $post['metas']['menu_item_parent'] = isset($mi['menu-item-parent']) ? filter_var($mi['menu-item-parent'], FILTER_SANITIZE_STRING) : "0";
            $post['metas']['menu_item_depth'] = isset($mi['menu-item-depth']) ? filter_var($mi['menu-item-depth'], FILTER_SANITIZE_STRING) : "0";
            $post['metas']['menu_item_object_id'] = isset($mi['menu-item-object-id']) ? filter_var($mi['menu-item-object-id'], FILTER_SANITIZE_STRING) : "";
            $post['metas']['menu_item_object'] = isset($mi['menu-item-object']) ? filter_var($mi['menu-item-object'], FILTER_SANITIZE_STRING) : "";
            $post['metas']['menu_item_target'] = isset($mi['menu-item-target']) ? filter_var($mi['menu-item-target'], FILTER_SANITIZE_STRING) : "";
            $post['metas']['menu_item_classes'] = isset($mi['menu-item-classes']) ? filter_var($mi['menu-item-classes'], FILTER_SANITIZE_STRING) : "";
            $post['metas']['menu_item_url'] = ""; //will be updated for custom menu type

            $post_id = addPost($post);

            if (isset($mi['menu-item-title']) && isset($mi['menu-item-type']) && trim($mi['menu-item-type']) == "custom") {
                $post['post_title'] = filter_var($mi['menu-item-title'], FILTER_SANITIZE_STRING);
                $post['metas']['menu_item_object_id'] = $post_id;
                $post['metas']['menu_item_object'] = "custom";
                $post['metas']['menu_item_url'] = isset($mi['menu-item-url']) ? filter_var($mi['menu-item-url'], FILTER_SANITIZE_STRING) : "";
            }

            $post['id'] = $post_id;
            $post['guid'] = $sys['site_url'] . "/?p=" . $post_id;

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
            if ($post_id && updatePost($post)) {
                //html content
                $html .= '<li id="menu-item-' . $post_id . '">
                            <div class="box box-default collapsed-box box-solid menu-item">
                                <div class="box-header with-border">
                                    <h3 class="box-title">' . $title . '</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">';

                if (trim($mi['menu-item-type']) == "custom") {
                    $html .= '<div class="form-group">
                                <i>Url</i>
                                <input type="text" name="menu-item-url[' . $post_id . ']" value="' . $post['metas']['menu_item_url'] . '" class="form-control"/>
                              </div>';
                }

                $html .= '<div class="form-group">
                            <i>Navigation Label</i>
                            <input type="text" name="menu-item-title[' . $post_id . ']" value="' . $title . '" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <i>Title Attribute</i>
                            <input type="text" name="menu-item-attr-title[' . $post_id . ']" value="" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" name="menu-item-target[' . $post_id . ']" value="_blank" class=""/>
                            Open link in a new tab
                        </div>
                        <div class="form-group">
                            <i>CSS Classes (optional)</i>
                            <input type="text" name="menu-item-classes[' . $post_id . ']" value="" class="form-control"/>
                        </div>
                        <fieldset class="field-move hide-if-no-js description description-wide">
                            <i class="field-move-visual-label" aria-hidden="true">Move</i>
                            <button type="button" class="button-link menus-move menus-move-up" data-dir="up" style="display: inline;" aria-label="Move up one">Up one</button>
                            <button type="button" class="button-link menus-move menus-move-down" data-dir="down" style="display: none;">Down one</button>
                            <button type="button" class="button-link menus-move menus-move-left" data-dir="left" style="display: none;"></button>
                            <button type="button" class="button-link menus-move menus-move-right" data-dir="right" style="display: inline;" aria-label="Move under Privacy Policy">Under Privacy Policy</button>
                            <button type="button" class="button-link menus-move menus-move-top" data-dir="top" style="display: inline;" aria-label="Move to the top">To the top</button>
                        </fieldset>';

                if (isset($original_link)) {
                    $html .= '<p class="link-to-original">';
                    $html .= 'Original: <a href="' . $original_link . '">' . $original_title . '</a>';
                    $html .= '</p>';
                }

                $html .= '<a class="item-delete text-danger" id="delete-' . $post_id . '" href="">Remove</a>';
                $html .= '<span class=""> | </span>';
                $html .= '<a class="item-cancel text-info" id="cancel-' . $post_id . '" href="">Cancel</a>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<input type="hidden" name="menu-item-type[' . $post_id . ']" value="' . $post['metas']['menu_item_type'] . '"/>';
                $html .= '<input type="hidden" name="menu-item-parent[' . $post_id . ']" value="0"/>';
                $html .= '<input type="hidden" name="menu-item-depth[' . $post_id . ']" value="0"/>';
                $html .= '<input type="hidden" name="menu-item-object-id[' . $post_id . ']" value="' . $post['metas']['menu_item_object_id'] . '"/>';
                $html .= '<input type="hidden" name="menu-item-object[' . $post_id . ']" value="' . $post['metas']['menu_item_object'] . '"/>';
                $html .= '<input type="hidden" name="menu-item-position[' . $post_id . ']" value="1"/>';
                $html .= '</li>';
            }
        }
    }
    echo $html;
}

if ($action == 'comment-approve') {
    $response['code'] = ERROR_RESPOSE_CODE;

    if (isset($_REQUEST['cid']) && trim($_REQUEST['cid']) <> "" && isUserLogged() && isUserHavePermission(MANAGE_COMMENTS_SECTION, getUserLoggedId())) {
        $cid = filter_var(trim($_REQUEST['cid']), FILTER_SANITIZE_NUMBER_INT);
        $comment = getComment($cid);
        $comment['status'] = "approved";
        if (updateComment($comment)) {
            $response['code'] = SUCCESS_RESPOSE_CODE;
        }
    }
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}
if ($action == 'comment-unapprove') {
    $response['code'] = ERROR_RESPOSE_CODE;

    if (isset($_REQUEST['cid']) && trim($_REQUEST['cid']) <> "" && isUserLogged() && isUserHavePermission(MANAGE_COMMENTS_SECTION, getUserLoggedId())) {
        $cid = filter_var(trim($_REQUEST['cid']), FILTER_SANITIZE_NUMBER_INT);
        $comment = getComment($cid);
        $comment['status'] = "pending";
        if (updateComment($comment)) {
            $response['code'] = SUCCESS_RESPOSE_CODE;
        }
    }
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}
if ($action == 'comment-spam') {
    $response['code'] = ERROR_RESPOSE_CODE;

    if (isset($_REQUEST['cid']) && trim($_REQUEST['cid']) <> "" && isUserLogged() && isUserHavePermission(MANAGE_COMMENTS_SECTION, getUserLoggedId())) {
        $cid = filter_var(trim($_REQUEST['cid']), FILTER_SANITIZE_NUMBER_INT);
        $comment = getComment($cid, array(), true);
        $post = getPost($comment['post_id'], array('id', 'comment_count'));
        $new_comment_count = $comment['status'] <> "trash" ? ($post['comment_count'] - 1) : $post['comment_count'];
        $comment['metas']['last_status'] = $comment['status'];
        $comment['status'] = "spam";
        if (updateComment($comment)) {
            update(T_POSTS, array("comment_count" => $new_comment_count), array("id" => $post['id']));
            $response['code'] = SUCCESS_RESPOSE_CODE;
        }
    }
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}
if ($action == 'comment-unspam') {
    $response['code'] = ERROR_RESPOSE_CODE;

    if (isset($_REQUEST['cid']) && trim($_REQUEST['cid']) <> "" && isUserLogged() && isUserHavePermission(MANAGE_COMMENTS_SECTION, getUserLoggedId())) {
        $cid = filter_var(trim($_REQUEST['cid']), FILTER_SANITIZE_NUMBER_INT);
        $comment = getComment($cid, array(), true);
        $response['status'] = $comment['status'] = isset($comment['metas']['last_status']) ? $comment['metas']['last_status'] : "pending";
        if (updateComment($comment)) {
            $post = getPost($comment['post_id'], array('id', 'comment_count'));
            update(T_POSTS, array("comment_count" => $post['comment_count'] + 1), array("id" => $post['id']));
            $response['code'] = SUCCESS_RESPOSE_CODE;
        }
    }
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}
if ($action == 'comment-trash') {
    $response['code'] = ERROR_RESPOSE_CODE;

    if (isset($_REQUEST['cid']) && trim($_REQUEST['cid']) <> "" && isUserLogged() && isUserHavePermission(MANAGE_COMMENTS_SECTION, getUserLoggedId())) {
        $cid = filter_var(trim($_REQUEST['cid']), FILTER_SANITIZE_NUMBER_INT);
        $comment = getComment($cid, array(), true);
        $comment['metas']['last_status'] = $comment['status'];
        $comment['status'] = "trash";
        if (updateComment($comment)) {
            $post = getPost($comment['post_id'], array('id', 'comment_count'));
            update(T_POSTS, array("comment_count" => $post['comment_count'] - 1), array("id" => $post['id']));
            $response['code'] = SUCCESS_RESPOSE_CODE;
        }
    }
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}
if ($action == 'comment-untrash') {
    $response['code'] = ERROR_RESPOSE_CODE;

    if (isset($_REQUEST['cid']) && trim($_REQUEST['cid']) <> "" && isUserLogged() && isUserHavePermission(MANAGE_COMMENTS_SECTION, getUserLoggedId())) {
        $cid = filter_var(trim($_REQUEST['cid']), FILTER_SANITIZE_NUMBER_INT);
        $comment = getComment($cid, array(), true);
        $response['status'] = $comment['status'] = isset($comment['metas']['last_status']) ? $comment['metas']['last_status'] : "pending";
        if (updateComment($comment)) {
            $post = getPost($comment['post_id'], array('id', 'comment_count'));
            update(T_POSTS, array("comment_count" => $post['comment_count'] + 1), array("id" => $post['id']));
            $response['code'] = SUCCESS_RESPOSE_CODE;
        }
    }
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}
if ($action == 'comment-delete') {
    $response['code'] = ERROR_RESPOSE_CODE;

    if (isset($_REQUEST['cid']) && trim($_REQUEST['cid']) <> "" && isUserLogged() && isUserHavePermission(MANAGE_COMMENTS_SECTION, getUserLoggedId())) {
        $cid = filter_var(trim($_REQUEST['cid']), FILTER_SANITIZE_NUMBER_INT);
        if (deleteComment($cid)) {
            $response['code'] = SUCCESS_RESPOSE_CODE;
        }
    }
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'get-slug') {
    $response['code'] = ERROR_RESPOSE_CODE;

    if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
        /* Updating existing slug */
        $post = getPost(filter_var(trim($_REQUEST['id']), FILTER_SANITIZE_NUMBER_INT));
        $post['post_name'] = isset($_REQUEST['post_name']) && trim($_REQUEST['post_name']) <> "" ? filter_var(trim($_REQUEST['post_name']), FILTER_SANITIZE_STRING) : $post['post_title'];
        if (updatePost($post)) {
            $post = getPost($post['id']);
            $response['code'] = SUCCESS_RESPOSE_CODE;
            $response['msg'] = 'success';
            $response['post_id'] = $post['id'];
            $response['post_name'] = $post['post_name'];
        }
    } else {
        /* Generating new slug */
        $post_title = isset($_REQUEST['post_title']) && !empty($_REQUEST['post_title']) ? filter_var(trim($_REQUEST['post_title']), FILTER_SANITIZE_STRING) : getNextIncrement(T_POSTS);
        $post_type = isset($_REQUEST['post_type']) && isset($sys['post_types'][trim($_REQUEST['post_type'])]) ? trim($_REQUEST['post_type']) : "post";
        $post_author = isset($_REQUEST['post_author']) ? filter_var(trim($_REQUEST['post_author']), FILTER_SANITIZE_NUMBER_INT) : getUserLoggedId();

        $post = array(
            'post_type' => $post_type,
            'post_title' => $post_title,
            'post_content' => "",
            'post_content_filtered' => "",
            'post_excerpt' => "",
            'post_author' => $post_author,
            'post_password' => "",
            'post_name' => str_replace(" ", "-", $post_title),
            'post_parent' => "0",
            'post_mime_type' => "",
            'to_ping' => "",
            'pinged' => "",
            'guid' => "",
            'menu_order' => "0",
            'comment_count' => "0",
            'ping_status' => "closed",
            'comment_status' => "closed",
            'post_status' => "draft"
        );
        $id = addPost($post);
        if ($id) {
            $post = getPost($id);
            $response['code'] = SUCCESS_RESPOSE_CODE;
            $response['msg'] = 'success';
            $response['post_id'] = $post['id'];
            $response['post_name'] = $post['post_name'];
        }
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'get-terms') {
    $response['code'] = SUCCESS_RESPOSE_CODE;
    $filters['taxonomy'] = isset($_REQUEST['taxonomy']) ? filter_var(trim($_REQUEST['taxonomy']), FILTER_SANITIZE_STRING) : '';
    $filters['query'] = isset($_REQUEST['name']) ? filter_var(trim($_REQUEST['name']), FILTER_SANITIZE_STRING) : '';
    $terms = getTerms(array("ID", "name"), $filters, 0, -1);
    $html = '';
    foreach ($terms as $term) {
        $html .= '<li><a href="#" data-id="' . $term['ID'] . '">' . $term['name'] . '</a></li>';
    }
    $response['html'] = $html;
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'add-term' && isset($_REQUEST['name']) && trim($_REQUEST['name']) <> "" && isUserLogged()) {
    $response['code'] = SUCCESS_RESPOSE_CODE;

    $term['name'] = filter_var(trim($_REQUEST['name']), FILTER_SANITIZE_STRING);
    $term['taxonomy'] = isset($_REQUEST['taxonomy']) ? filter_var(trim($_REQUEST['taxonomy']), FILTER_SANITIZE_STRING) : 'tag';
    $term['term_group'] = 0;
    $term['slug'] = $term['name'];
    $term['description'] = "";
    $term['parent'] = isset($_REQUEST['parent']) ? filter_var(trim($_REQUEST['parent']), FILTER_SANITIZE_STRING) : '0';
    ;
    $term['count'] = "0";

    $data = array("ID" => "", "name" => "");
    $id = addTerm($term);
    if ($id) {
        $data = array("ID" => $id, "name" => $term['name']);
    }

    $response['data'] = $data;
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'add-terms' && isset($_REQUEST['name']) && trim($_REQUEST['name']) <> "" && isUserLogged()) {
    $response['code'] = SUCCESS_RESPOSE_CODE;

    $data = array();
    $names = explode(",", filter_var(trim($_REQUEST['name']), FILTER_SANITIZE_STRING));
    $taxonomy = isset($_REQUEST['taxonomy']) ? filter_var(trim($_REQUEST['taxonomy']), FILTER_SANITIZE_STRING) : 'tag';
    foreach ($names as $name) {
        $term['name'] = filter_var(trim($name), FILTER_SANITIZE_STRING);
        $term['taxonomy'] = $taxonomy;
        $term['term_group'] = 0;
        $term['slug'] = $term['name'];
        $term['description'] = "";
        $term['parent'] = "0";
        $term['count'] = "0";

        $id = addTerm($term);
        if ($id) {
            $data[] = array("ID" => $id, "name" => $term['name']);
        } else {
            $terms = getTerms(array("ID", "name"), array("name" => $term['name'], "taxonomy" => $term['taxonomy']));
            foreach ($terms as $t) {
                $data[] = $t;
            }
        }
    }

    $response['data'] = $data;
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'get-shops') {
    $response['code'] = SUCCESS_RESPOSE_CODE;
    $filters['name'] = isset($_REQUEST['name']) ? filter_var(trim($_REQUEST['name']), FILTER_SANITIZE_STRING) : '';
    $shops = getShops(array("id", "name"), $filters, 0, -1);
    $html = '';
    foreach ($shops as $shop) {
        $html .= '<li><a href="#" data-id="' . $shop['id'] . '">' . $shop['name'] . '</a></li>';
    }
    $response['html'] = $html;
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'get-hsn-codes') {
    $response['code'] = SUCCESS_RESPOSE_CODE;
    $filters['q'] = isset($_REQUEST['query']) ? filter_var(trim($_REQUEST['query']), FILTER_SANITIZE_STRING) : '';
    $hsnCodes = getHSNCodes(array("code", "description"), $filters);
    $html = '';
    foreach ($hsnCodes as $hsn) {
        $html .= '<li><a href="#" data-code="' . $hsn['code'] . '">' . $hsn['code'] . ' - ' . $hsn['description'] . '</a></li>';
    }
    $response['html'] = $html;
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'get-brands') {
    $response['code'] = SUCCESS_RESPOSE_CODE;
    $filters['name'] = isset($_REQUEST['name']) ? filter_var(trim($_REQUEST['name']), FILTER_SANITIZE_STRING) : '';
    $brands = getBrands(array("id", "name"), $filters, 0, -1);
    $html = '';
    foreach ($brands as $brand) {
        $html .= '<li><a href="#" data-id="' . $brand['id'] . '">' . $brand['name'] . '</a></li>';
    }
    $response['html'] = $html;
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'get-categories') {
    $response['code'] = SUCCESS_RESPOSE_CODE;
    $filters['q'] = isset($_REQUEST['q']) ? filter_var(trim($_REQUEST['q']), FILTER_SANITIZE_STRING) : '';
    $categories = getCategories(array("id", "name"), $filters, 0, -1);
    $html = '';
    foreach ($categories as $category) {
        $html .= '<li><a href="#" data-id="' . $category['id'] . '">' . $category['name'] . '</a></li>';
    }
    $response['html'] = $html;
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'get-subcategories') {
    $response['code'] = SUCCESS_RESPOSE_CODE;
    $filters['main_category'] = filter_var(trim($_REQUEST['category']), FILTER_SANITIZE_NUMBER_INT);
    $categories = getCategories(array("id", "name"), $filters, 0, -1); //getting sub categories
    $html = "";
    if (count($categories) > 0) {
        $html .= '<select name="categories[]" class="form-control subcategories sub-of-' . $filters['main_category'] . '">';
        $html .= '<option value="">- Select -</option>';
        foreach ($categories as $category) {
            $html .= '<option value="' . $category['id'] . '">' . $category['name'] . '</option>';
        }
        $html .= '</select>';
    }
    $response['html'] = $html;
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'get-tags') {
    $response['code'] = SUCCESS_RESPOSE_CODE;
    $filters['name'] = isset($_REQUEST['name']) ? filter_var(trim($_REQUEST['name']), FILTER_SANITIZE_STRING) : '';
    $tags = getTags(array("id", "name"), $filters, 0, -1);
    $html = '';
    foreach ($tags as $tag) {
        $html .= '<li><a href="#" data-id="' . $tag['id'] . '">' . $tag['name'] . '</a></li>';
    }
    $response['html'] = $html;
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'get-filters') {
    $response['code'] = SUCCESS_RESPOSE_CODE;
    $filters['q'] = isset($_REQUEST['q']) ? filter_var(trim($_REQUEST['q']), FILTER_SANITIZE_STRING) : '';
    $filters = getFilters(false, $filters, 0, 12);
    $html = '';
    foreach ($filters as $f) {
        $html .= '<li><a href="#" data-id="' . $f['id'] . '">' . $f['group_name'] . " > " . $f['name'] . '</a></li>';
    }
    $response['html'] = $html;
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'get-products') {
    $response['code'] = SUCCESS_RESPOSE_CODE;
    $filters['name'] = isset($_REQUEST['name']) ? filter_var(trim($_REQUEST['name']), FILTER_SANITIZE_STRING) : '';
    $filters['category'] = isset($_REQUEST['category']) ? filter_var(trim($_REQUEST['category']), FILTER_SANITIZE_NUMBER_INT) : '';
    $filters['shop'] = isset($_REQUEST['shop']) ? filter_var(trim($_REQUEST['shop']), FILTER_SANITIZE_STRING) : '';
    $filters['price_from'] = $price_from = isset($_REQUEST['price_from']) ? filter_var(trim($_REQUEST['price_from']), FILTER_SANITIZE_NUMBER_INT) : '';
    $filters['price_to'] = $price_to = isset($_REQUEST['price_to']) ? filter_var(trim($_REQUEST['price_to']), FILTER_SANITIZE_NUMBER_INT) : '';
    $filters['brand'] = isset($_REQUEST['brand']) ? filter_var(trim($_REQUEST['brand']), FILTER_SANITIZE_STRING) : '';
    $filters['filters'] = isset($_REQUEST['filters']) ? $_REQUEST['filters'] : array();
    $filters['query'] = isset($_REQUEST['s']) ? $_REQUEST['s'] : "";
    /* update price from and to according to currency for filter */
    $filters['price_from'] = $filters['price_from'] / $_SESSION['currency']['rate_dc_base'];
    $filters['price_to'] = $filters['price_to'] / $_SESSION['currency']['rate_dc_base'];

    if (!isset($_REQUEST['for']) || (isset($_REQUEST['for']) && $_REQUEST['for'] == 'related-products')) {
        $products = getProducts(array("id", "name"), $filters);
        $html = '';
        foreach ($products as $p) {
            $html .= '<li><a href="#" data-id="' . $p['id'] . '">' . $p['name'] . '</a></li>';
        }
        $response['html'] = $html;
        $response['msg'] = "success";
    } else if (isset($_REQUEST['for']) && in_array(trim($_REQUEST['for']), array('category-page', 'search-page'))) {
        /* pagination logic start */
        $items_count = count(getProducts(array('id'), $filters, 0, -1));
        $items_per_page = isset($_SESSION['products_per_page']) ? $_SESSION['products_per_page'] : 20;
        $max_pages = intval($items_count / $items_per_page + 1);
        $current_page = !isset($_REQUEST['paged']) || intval($_REQUEST['paged']) < 1 ? 1 : filter_var(trim($_REQUEST['paged']), FILTER_SANITIZE_NUMBER_INT);
        $offset = $items_per_page * $current_page - $items_per_page;
        /* pagination logic end */

        $order_by = isset($_SESSION['products_order_by']) ? $_SESSION['products_order_by'] : 'added_timestamp';
        $order = isset($_SESSION['products_order']) ? $_SESSION['products_order'] : 'DESC';

        $currency = $_SESSION['currency'];
        $tmpproducts = getProducts(array("id", "slug", "sku", "name", "images", "short_description"), $filters, $offset, $items_per_page, $order_by, $order);
        $products = array();
        foreach ($tmpproducts as $p) {
            $price = getProductPrice($p['id']);
            $sp_i_c = ($price['sale_price'] + $price['sale_price_gst'] + $price['sale_price_commission'] + $price['sale_price_commission_gst']) * $currency['rate_dc_base'];
            $p['sale_price'] = number_format($sp_i_c, $currency['decimal_places'], $currency['decimal_separator'], $currency['thousand_separator']);
            $p_i_c = ($price['price'] + $price['price_gst'] + $price['commission'] + $price['commission_gst']) * $currency['rate_dc_base'];
            $p['price'] = number_format($p_i_c, $currency['decimal_places'], $currency['decimal_separator'], $currency['thousand_separator']);
            if ($p_i_c >= $price_from && $p_i_c <= $price_to) {
                $products[] = $p;
            }
        }
        $response['products'] = $products;
        $response['page'] = $current_page;
        $response['pages'] = $max_pages;
        $response['msg'] = "success";
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'get-states') {
    $response['code'] = SUCCESS_RESPOSE_CODE;
    $filters['country_id'] = isset($_REQUEST['country_id']) ? filter_var(trim($_REQUEST['country_id']), FILTER_SANITIZE_NUMBER_INT) : '';
    $states = getStates(array('id', 'name'), $filters);
    $html = '<option value="">Select</option>';
    foreach ($states as $s) {
        $html .= '<option value="' . $s['id'] . '">' . $s['name'] . '</option>';
    }
    $response['html'] = $html;
    $response['msg'] = "success";
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'get-user-orders' && isUserLogged()) {
    $response['code'] = SUCCESS_RESPOSE_CODE;
    $filters['user_id'] = filter_var(trim($_REQUEST['user_id']), FILTER_SANITIZE_NUMBER_INT);
    $filters['with_products'] = array('product_name', 'customization_string', 'total', 'images', 'slug');

    /* pagination logic start */
    $items_count = count(getOrders(array('id'), $filters, 0, -1));
    $items_per_page = 15;
    $max_pages = intval($items_count / $items_per_page + 1);
    $current_page = !isset($_REQUEST['paged']) || intval($_REQUEST['paged']) < 1 ? 1 : filter_var(trim($_REQUEST['paged']), FILTER_SANITIZE_NUMBER_INT);
    $offset = $items_per_page * $current_page - $items_per_page;
    /* pagination logic end */

    $order_by = isset($_SESSION['uorders_order_by']) ? $_SESSION['uorders_order_by'] : 'added_timestamp';
    $order = isset($_SESSION['uorders_order']) ? $_SESSION['uorders_order'] : 'DESC';

    $orders = getOrders(array('id', 'reference_number', 'invoice_number', 'added_timestamp', 'total_amount'), $filters, $offset, $items_per_page, $order_by, $order);

    $response['orders'] = $orders;
    $response['page'] = $current_page;
    $response['pages'] = $max_pages;
    $response['items'] = $items_count;
    $response['msg'] = "success";

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'get-user-transactions' && isUserLogged()) {
    $response['code'] = SUCCESS_RESPOSE_CODE;
    $filters['user_id'] = filter_var(trim($_REQUEST['user_id']), FILTER_SANITIZE_NUMBER_INT);

    /* pagination logic start */
    $items_count = count(getUserTransactions(array('id'), $filters, 0, -1));
    $items_per_page = 15;
    $max_pages = intval($items_count / $items_per_page + 1);
    $current_page = !isset($_REQUEST['paged']) || intval($_REQUEST['paged']) < 1 ? 1 : filter_var(trim($_REQUEST['paged']), FILTER_SANITIZE_NUMBER_INT);
    $offset = $items_per_page * $current_page - $items_per_page;
    /* pagination logic end */

    $order_by = isset($_SESSION['utransactions_order_by']) ? $_SESSION['utransactions_order_by'] : 'id';
    $order = isset($_SESSION['utransactions_order']) ? $_SESSION['utransactions_order'] : 'DESC';

    $transactions = getUserTransactions(array(), $filters, $offset, $items_per_page, $order_by, $order);

    $response['transactions'] = $transactions;
    $response['page'] = $current_page;
    $response['pages'] = $max_pages;
    $response['items'] = $items_count;
    $response['msg'] = "success";

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'get-html') {
    $response['code'] = ERROR_RESPOSE_CODE;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //checking the required parameters from the request 
        if (isset($_POST['from_url'])) {
            $from_url = trim($_POST['from_url']);
            $html = file_get_html($from_url);
            $response['code'] = SUCCESS_RESPOSE_CODE;
            $response['html'] = addslashes($html);
            $response['msg'] = 'Url read successfully';
            $response['htmlmsg'] = '<div class="alert alert-success">Url read successfully!</div>';
        } else {
            $response['code'] = ERROR_RESPOSE_CODE;
            $response['msg'] = 'Please provide url';
            $response['htmlmsg'] = '<div class="alert alert-danger">Please provide url!</div>';
        }
    }
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'change-products-show-orderby') {
    $_SESSION['products_order_by'] = isset($_REQUEST['products_order_by']) && in_array(trim($_REQUEST['products_order_by']), array('id', 'name', 'price')) ? trim($_REQUEST['products_order_by']) : 'display_order';
    echo 'success';
}
if ($action == 'change-products-show-order') {
    $_SESSION['products_order'] = isset($_REQUEST['products_order']) && in_array(strtoupper(trim($_REQUEST['products_order'])), array('ASC', 'DESC')) ? strtoupper(trim($_REQUEST['products_order'])) : 'ASC';
    echo 'success';
}
if ($action == 'change-products-show-per-page') {
    $_SESSION['products_per_page'] = isset($_REQUEST['products_per_page']) && in_array(intval(trim($_REQUEST['products_per_page'])), array(10, 20, 50)) ? intval(trim($_REQUEST['products_per_page'])) : 20;
    echo 'success';
}

if ($action == 'seller_update') {
    if (isset($_POST['seller_id']) && isset($_POST['seller_name']) && isUserHavePermission(SELLER_SECTION, EDIT_PERMISSION)) {
        $seller['id'] = filter_var(trim($_POST['seller_id']), FILTER_SANITIZE_NUMBER_INT);
        $seller['name'] = filter_var(trim($_POST['seller_name']), FILTER_SANITIZE_STRING);
        $seller['email'] = filter_var(trim($_POST['seller_email']), FILTER_SANITIZE_STRING);
        $seller['mobile'] = filter_var(trim($_POST['seller_mobile']), FILTER_SANITIZE_STRING);
        $seller['phone'] = filter_var(trim($_POST['seller_phone']), FILTER_SANITIZE_STRING);
        $seller['address'] = filter_var(trim($_POST['seller_address']), FILTER_SANITIZE_STRING);
        $seller['city'] = filter_var(trim($_POST['seller_city']), FILTER_SANITIZE_STRING);
        $seller['state'] = filter_var(trim($_POST['seller_state']), FILTER_SANITIZE_STRING);
        $seller['country'] = filter_var(trim($_POST['seller_country']), FILTER_SANITIZE_STRING);
        $seller['username'] = filter_var(trim($_POST['seller_username']), FILTER_SANITIZE_STRING);
        $seller['password'] = trim($_POST['seller_password']);
        $seller['status'] = trim($_POST['active']);

        if (trim($seller['name']) == "" || trim($seller['username']) == "" || trim($seller['password']) == "") {
            $message = '<div class="alert alert-danger">Seller name, username and password required!</div>';
        } else {
            $message = '<div class="alert alert-success">Seller updated successfully!</div>';
            if (!updateSeller($seller)) {
                $message = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
            }
        }
    }

    header("Content-type: text/html");
    echo $message;

    exit();
}

if ($action == 'affiliate_update') {
    if (isset($_POST['seller_id']) && isset($_POST['seller_name']) && isUserHavePermission(AFFILIATE_SECTION, EDIT_PERMISSION)) {
        $affiliate['id'] = filter_var(trim($_POST['seller_id']), FILTER_SANITIZE_NUMBER_INT);
        $affiliate['name'] = filter_var(trim($_POST['seller_name']), FILTER_SANITIZE_STRING);
        $affiliate['affiliate_id'] = filter_var(trim($_POST['affiliate_id']), FILTER_SANITIZE_STRING);
        $affiliate['tracking_id'] = filter_var(trim($_POST['tracking_id']), FILTER_SANITIZE_STRING);
        $affiliate['status'] = $_POST['active'];

        if ($affiliate['name'] == '') {
            $message = '<div class="alert alert-danger">Please enter affiliate name</div>';
        } else {
            $message = '<div class="alert alert-success">Affiliate updated successfully!</div>';
            if (!updateAffiliate($affiliate)) {
                $message = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
            }
        }
    }

    header("Content-type: text/html");
    echo $message;

    exit();
}

if ($action == 'lister_update') {
    if (isset($_POST['lister_id']) && isset($_POST['lister_name']) && isUserHavePermission(SELLER_SECTION, EDIT_PERMISSION)) {
        $lister['id'] = filter_var(trim($_POST['lister_id']), FILTER_SANITIZE_NUMBER_INT);
        $lister['name'] = filter_var(trim($_POST['lister_name']), FILTER_SANITIZE_STRING);
        $lister['email'] = filter_var(trim($_POST['lister_email']), FILTER_SANITIZE_STRING);
        $lister['mobile'] = filter_var(trim($_POST['lister_mobile']), FILTER_SANITIZE_STRING);
        $lister['phone'] = filter_var(trim($_POST['lister_phone']), FILTER_SANITIZE_STRING);
        $lister['address'] = filter_var(trim($_POST['lister_address']), FILTER_SANITIZE_STRING);
        $lister['city'] = filter_var(trim($_POST['lister_city']), FILTER_SANITIZE_STRING);
        $lister['state'] = filter_var(trim($_POST['lister_state']), FILTER_SANITIZE_STRING);
        $lister['pincode'] = filter_var(trim($_POST['lister_pincode']), FILTER_SANITIZE_STRING);
        $lister['country'] = filter_var(trim($_POST['lister_country']), FILTER_SANITIZE_STRING);
        $lister['website'] = filter_var(trim($_POST['lister_website']), FILTER_SANITIZE_STRING);
        $lister['username'] = filter_var(trim($_POST['lister_username']), FILTER_SANITIZE_STRING);
        $lister['password'] = trim($_POST['lister_password']);
        $lister['status'] = trim($_POST['active']);

        if (trim($lister['name']) == "" || trim($lister['username']) == "" || trim($lister['password']) == "") {
            $message = '<div class="alert alert-danger">Lister name, username and password required!</div>';
        } else {
            $message = '<div class="alert alert-success">Lister updated successfully!</div>';
            if (!updateLister($lister)) {
                $message = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
            }
        }
    }

    header("Content-type: text/html");
    echo $message;

    exit();
}

if ($action == 'searchsuggestions') {
    $keyword = $_REQUEST['q'];
    $keywordsuggestions = getSuggestionsByQuery($keyword);
    $productssuggestions = getProductsSuggestionsByQuery($keyword, 0, 3);
    $response = '<div class="list-group">';
    foreach ($keywordsuggestions as $suggestion) {
        $response .= '<a href="' . $sys['config']['site_url'] . '/search?q=' . $suggestion['keyword'] . '" class="list-group-item" style="border: none; padding: 5px;">' . $suggestion['keyword'] . '</a>';
    }
    $response .= '</div><div><h5 style="margin-bottom: 5px;">Popular Products</h5>';
    foreach ($productssuggestions as $suggestion) {
        $response .= '<a href="' . $sys['config']['site_url'] . '/product/' . $suggestion['slug'] . '" class="list-group-item">' . $suggestion['name'] . '</a>';
    }
    $response .= "</div>";
    echo $response;
}

if ($action == 'postreview') {
    $review['product_id'] = $_REQUEST['product_id'];
    $review['name'] = $_REQUEST['name'];
    $review['review'] = $_REQUEST['review'];
    $review['rating'] = $_REQUEST['rating'];
    $review['ip_address'] = $_SERVER['REMOTE_ADDR'];

    if (addReview($review)) {
        echo '<div class="alert alert-success">Posted Successfully</div>';
    } else {
        echo '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
    }
}

if ($action == 'send-otp') {
    $for = isset($_REQUEST['for']) ? filter_var(trim($_REQUEST['for']), FILTER_SANITIZE_STRING) : "";

    if ($for = "registration" && isset($_REQUEST['mobile']) && trim($_REQUEST['mobile']) <> "") {
        //send on mobile 
        $otp = mt_rand(100000, 999999);
        $_SESSION['otp'] = $otp;
    }
    if ($for = "registration" && isset($_REQUEST['email']) && trim($_REQUEST['email']) <> "") {
        //send on email
        $otp = mt_rand(100000, 999999);
        $_SESSION['otp'] = $otp;
    }
    if ($for = "email-update" && isset($_REQUEST['new-email']) && trim($_REQUEST['new-email']) <> "" && isUserLogged()) {
        $response['code'] = ERROR_RESPOSE_CODE;
        $configs = getConfig();

        $newemail = filter_var(trim($_REQUEST['new-email']), FILTER_SANITIZE_EMAIL);

        $otp1 = mt_rand(100000, 999999);
        $otp2 = mt_rand(100000, 999999);
        $_SESSION['otp1'] = $otp1;
        $_SESSION['otp2'] = $otp2;
        $_SESSION['new-email'] = $newemail;
        $u = getUser(getUserLoggedId());

        $template1 = $template2 = getEmailTemplate('email_verification_otp');
        $logotag = isset($configs["EMAIL_TEMPLATE_LOGO"]) && trim($configs["EMAIL_TEMPLATE_LOGO"]) <> "" ? '<img src="' . $configs["EMAIL_TEMPLATE_LOGO"] . '"/>' : "";

        if (trim($u['email']) <> "" && $template1 != null) {
            $subject = str_replace('{website_name}', $sys['site_name'], $template1['subject']);
            $searchfor = array('{Company_Logo}', '{current_date}', '{user_full_name}', '{website_name}', '{website_url}', '{otp}', '{website_name}');
            $replacements = array($logotag, date("Y-m-d"), $u['display_name'], $sys['site_name'], $sys['site_url'], $otp1, $sys['site_name']);
            $body = str_replace($searchfor, $replacements, $template1['body']);

            $data = array(); //clearing data array
            $data['from_email'] = secure($sys['admin_email']);
            $data['from_name'] = $sys['site_name'];
            $data['to_email'] = $u['email'];
            $data['to_name'] = $u['display_name'];
            $data['charSet'] = "";
            $data['is_html'] = true;
            $data['subject'] = $subject;
            $data['message_body'] = $body;
            sendMessage($data);
        }

        if ($template2 != null) {
            $subject = str_replace('{website_name}', $sys['site_name'], $template2['subject']);
            $searchfor = array('{Company_Logo}', '{current_date}', '{user_full_name}', '{website_name}', '{website_url}', '{otp}', '{website_name}');
            $replacements = array($logotag, date("Y-m-d"), $u['display_name'], $sys['site_name'], $sys['site_url'], $otp2, $sys['site_name']);
            $body = str_replace($searchfor, $replacements, $template2['body']);

            $data = array(); //clearing data array
            $data['from_email'] = secure($sys['admin_email']);
            $data['from_name'] = $sys['site_name'];
            $data['to_email'] = $newemail;
            $data['to_name'] = $u['display_name'];
            $data['charSet'] = "";
            $data['is_html'] = true;
            $data['subject'] = $subject;
            $data['message_body'] = $body;
            if (sendMessage($data)) {
                $response['code'] = SUCCESS_RESPOSE_CODE;
                $response['msg'] = 'OTP Sent';
                $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
            } else {
                $response['msg'] = 'Error Sending OTP';
                $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
            }
        } else {
            $response['msg'] = 'Email Template Not Defined!';
            $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
        }

        header("Content-Type:application/json");
        echo json_encode($response);
        exit();
    }
    if ($for = "mobile-update" && isset($_REQUEST['new-mobile']) && trim($_REQUEST['new-mobile']) <> "" && isUserLogged()) {
        $response['code'] = ERROR_RESPOSE_CODE;
        $newmobile = filter_var(trim($_REQUEST['new-mobile']), FILTER_SANITIZE_STRING);

        $_SESSION['new-mobile'] = $newmobile;

        if (isset($sys['ACCOUNT_OTP_VERIFICATION']) && trim($sys['ACCOUNT_OTP_VERIFICATION']) == 'Y') {
            $otp1 = mt_rand(100000, 999999);
            $otp2 = mt_rand(100000, 999999);
            $_SESSION['otp1'] = $otp1;
            $_SESSION['otp2'] = $otp2;

            $u = getUser(getUserLoggedId());

            if (trim($u['mobile']) <> "") {
                //send message on old mobile
            }

            //send message on new mobile   
        } else {
            $response['code'] = SUCCESS_RESPOSE_CODE;
        }

        header("Content-Type:application/json");
        echo json_encode($response);
        exit();
    }
}

if ($action == 'register') {
    $response['code'] = ERROR_RESPOSE_CODE;
    $configs = getConfig();

    if ((isset($_POST['email']) || isset($_POST['mobile'])) && isset($_POST['password'])) {
        $user['email'] = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : "";
        $user['mobile'] = isset($_POST['mobile']) ? filter_var(trim($_POST['mobile']), FILTER_SANITIZE_STRING) : "";
        $user['username'] = $user['email'] <> "" ? $user['email'] : $user['mobile'];
        $user['password'] = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);
        $user['display_name'] = isset($_POST['display_name']) ? filter_var(trim($_POST['display_name'])) : $user['username'];
        if (isset($configs['ACCOUNT_ADMIN_APPROVE']) && trim($configs['ACCOUNT_ADMIN_APPROVE']) == "Y") {
            $user['status'] = 'P';
            $response['redirecturl'] = "";
            $response['msg'] = 'You will receive confirmation email when your account is activated!';
            $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
        } else if (isset($configs['ACCOUNT_EMAIL_VERIFICATION']) && trim($configs['ACCOUNT_EMAIL_VERIFICATION']) == "Y") {
            $user['status'] = 'P';
            $user['activation_key'] = generateKey();
            $response['redirecturl'] = "";
            $response['msg'] = 'Check Your Email and Verify to Complete Your Registration!';
            $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
        } else {
            $user['status'] = 'A';
            $response['redirecturl'] = isset($_POST['redirect']) ? $_POST['redirect'] : $sys['site_url'];
            $response['msg'] = 'User registered successfully!';
            $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
        }
        //OTP Verification for Mobile if active
        $otp = isset($_POST['otp']) ? filter_var(trim($_POST['otp'])) : "";

        $user['metas'] = array();
        $user['metas']['role'] = isset($_POST['role']) && in_array($_POST['role'], array('buyer', 'seller')) ? $_POST['role'] : 'buyer';

        // Include email verify library file
        require_once 'system/import/VerifyEmail.php';
        $verifymail = new VerifyEmail();
        $verifymail->setStreamTimeoutWait(30);
        //$verifymail->Debug= TRUE; 
        //$verifymail->Debugoutput= 'html'; 
        $verifymail->setEmailFrom($sys['admin_email']);

        if ($user['password'] == '') {
            $response['msg'] = 'Please provide Password';
            $response['htmlmsg'] = '<div class="alert alert-danger">Please provide Password</div>';
        } else if ($user['email'] == '' && $user['mobile'] == '') {
            $response['msg'] = 'Please provide Email or Mobile';
            $response['htmlmsg'] = '<div class="alert alert-danger">Please provide Email</div>';
        } else if (isset($configs['ACCOUNT_OTP_VERIFICATION']) && trim($configs['ACCOUNT_OTP_VERIFICATION']) == "Y" && $otp != $_SESSION['otp']) {
            $response['msg'] = 'Incorrect OTP';
            $response['htmlmsg'] = '<div class="alert alert-danger">Incorrect OTP</div>';
        } else if (isset($configs['ACCOUNT_EMAIL_VERIFY_THROUGH_API']) && trim($configs['ACCOUNT_EMAIL_VERIFY_THROUGH_API']) == "Y" && !$verifymail->check($user['email'])) {
            $response['msg'] = 'Invalid Email';
            $response['htmlmsg'] = '<div class="alert alert-danger">Invalid Email</div>';
        } else {
            $response['code'] = ERROR_RESPOSE_CODE;
            if (addUser($user)) {
                $response['code'] = SUCCESS_RESPOSE_CODE;

                $u = getUser($user['username']);
                $logotag = isset($configs["EMAIL_TEMPLATE_LOGO"]) && trim($configs["EMAIL_TEMPLATE_LOGO"]) <> "" ? '<img src="' . $configs["EMAIL_TEMPLATE_LOGO"] . '"/>' : "";

                //email verification
                if (isset($configs['ACCOUNT_EMAIL_VERIFICATION']) && trim($configs['ACCOUNT_EMAIL_VERIFICATION']) == "Y") {
                    $template = getEmailTemplate('signup_verification');
                    if ($template != null) {
                        $verification_url = $sys['site_url'] . '/requests.php?action=verify-email&email=' . $u['email'] . '&code=' . $u['activation_key'];
                        $subject = str_replace('{website_name}', $sys['site_name'], $template['subject']);
                        $searchfor = array('{Company_Logo}', '{current_date}', '{user_full_name}', '{website_name}', '{verification_url}', '{website_name}');
                        $replacements = array($logotag, date("Y-m-d"), $u['display_name'], $sys['site_name'], $verification_url, $sys['site_name']);
                        $body = str_replace($searchfor, $replacements, $template['body']);

                        $data['from_email'] = secure($sys['admin_email']);
                        $data['from_name'] = $sys['site_name'];
                        $data['to_email'] = $u['email'];
                        $data['to_name'] = $u['display_name'];
                        $data['charSet'] = "";
                        $data['is_html'] = true;
                        $data['subject'] = $subject;
                        $data['message_body'] = $body;
                        @sendMessage($data);
                    }
                }
                //auto login
                if (isset($configs['ACCOUNT_AUTO_LOGIN']) && trim($configs['ACCOUNT_AUTO_LOGIN']) == "Y") {
                    $_SESSION['user_id'] = $u['id'];
                    $_SESSION['username'] = $u['username'];
                    $_SESSION['role'] = isset($u['metas']['role']) ? $u['metas']['role'] : "na";
                    $_SESSION['display_name'] = $u['display_name'];
                    $_SESSION['registered'] = date("M. Y", strtotime($u['registered']));
                    /* Syncing Cart */
                    syncCart(session_id(), $u['id']);
                }
                //notify admin
                if (isset($configs['ACCOUNT_NOTIFICATION_ADMIN']) && trim($configs['ACCOUNT_NOTIFICATION_ADMIN']) == "Y") {
                    $template = getEmailTemplate('new_registration_admin');
                    if ($template != null) {
                        $subject = str_replace('{website_name}', $sys['site_name'], $template['subject']);
                        $searchfor = array('{Company_Logo}', '{current_date}', '{website_name}', '{username}', '{email}', '{name}', '{website_name}');
                        $replacements = array($logotag, date("Y-m-d"), $sys['site_name'], $u['username'], $u['email'], $u['display_name'], $sys['site_name']);
                        $body = str_replace($searchfor, $replacements, $template['body']);

                        $data['from_email'] = secure($sys['admin_email']);
                        $data['from_name'] = $sys['site_name'];
                        $data['to_email'] = $u['email'];
                        $data['to_name'] = $u['display_name'];
                        $data['charSet'] = "";
                        $data['is_html'] = true;
                        $data['subject'] = $subject;
                        $data['message_body'] = $body;
                        @sendMessage($data);
                    }
                }
                //notify user
                if (isset($configs['ACCOUNT_WELCOME_EMAIL']) && trim($configs['ACCOUNT_WELCOME_EMAIL']) == "Y") {
                    $template = getEmailTemplate('welcome_registration');
                    if ($template != null) {
                        $subject = str_replace('{website_name}', $sys['site_name'], $template['subject']);
                        $searchfor = array('{Company_Logo}', '{current_date}', '{name}', '{website_name}', '{contact_us_email}', '{website_name}');
                        $replacements = array($logotag, date("Y-m-d"), $u['username'], $sys['site_name'], '<a href="mailto:' . $sys['admin_email'] . '">' . $sys['admin_email'] . '</a>', $sys['site_name']);
                        $body = str_replace($searchfor, $replacements, $template['body']);

                        $data['from_email'] = secure($sys['admin_email']);
                        $data['from_name'] = $sys['site_name'];
                        $data['to_email'] = $u['email'];
                        $data['to_name'] = $u['display_name'];
                        $data['charSet'] = "";
                        $data['is_html'] = true;
                        $data['subject'] = $subject;
                        $data['message_body'] = $body;
                        @sendMessage($data);
                    }
                }
            } else {
                $response['msg'] = $queryerrormsg;
                $response['htmlmsg'] = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
            }
        }
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'verify-email') {
    $response['code'] = ERROR_RESPOSE_CODE;
    $configs = getConfig();

    if (isset($_REQUEST['email']) && isset($_REQUEST['code'])) {
        $email = filter_var(trim($_REQUEST['email']), FILTER_SANITIZE_EMAIL);
        $code = filter_var(trim($_REQUEST['code']), FILTER_SANITIZE_STRING);

        $u = getUser($email, array('id', 'email', 'display_name', 'activation_key'), "email");

        if ($u['activation_key'] == $code) {
            if (isset($configs['ACCOUNT_ADMIN_APPROVE']) && trim($configs['ACCOUNT_ADMIN_APPROVE']) == "Y") {
                echo 'Thanks for verifying your email, you will be notified when you account is activated!';
            } else if (update(T_USERS, array('status' => 'A', 'activation_key' => generateKey()), array('id' => $u['id']))) {
                //send mail
                $logotag = isset($configs["EMAIL_TEMPLATE_LOGO"]) && trim($configs["EMAIL_TEMPLATE_LOGO"]) <> "" ? '<img src="' . $configs["EMAIL_TEMPLATE_LOGO"] . '"/>' : "";

                $template = getEmailTemplate('account_activated');
                $subject = str_replace('{website_name}', $sys['site_name'], $template['subject']);
                $searchfor = array('{Company_Logo}', '{current_date}', '{name}', '{website_name}', '{website_url}', '{contact_us_email}', '{website_name}');
                $replacements = array($logotag, date("Y-m-d"), $u['display_name'], $sys['site_name'], $sys['site_url'], $sys['admin_email'], $sys['site_name']);
                $body = str_replace($searchfor, $replacements, $template['body']);

                $data['from_email'] = secure($sys['admin_email']);
                $data['from_name'] = $sys['site_name'];
                $data['to_email'] = $u['email'];
                $data['to_name'] = $u['display_name'];
                $data['charSet'] = "";
                $data['is_html'] = true;
                $data['subject'] = $subject;
                $data['message_body'] = $body;
                @sendMessage($data);
                echo 'Your account activated successfully! <a href="' . $sys['site_url'] . '/login">click here</a> to login';
            }
        } else {
            echo 'Invalid Activation Code!';
        }
    } else {
        echo "Email & Verification Code Required!";
    }
    exit();
}

if ($action == 'login') {
    $response['code'] = ERROR_RESPOSE_CODE;
    if ((isset($_POST['username']) || isset($_POST['email']) || isset($_POST['mobile'])) && isset($_POST['password'])) {
        if (isset($_POST['username']) && trim($_POST['username']) <> "") {
            $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
        } else if (isset($_POST['email']) && trim($_POST['email']) <> "") {
            $username = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        } else if (isset($_POST['mobile']) && trim($_POST['mobile']) <> "") {
            $username = filter_var(trim($_POST['mobile']), FILTER_SANITIZE_STRING);
        } else {
            $username = "";
        }
        $password = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);

        if ($username == '') {
            $response['msg'] = 'Please provide Username/Email/Mobile';
            $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
        } else if ($password == '') {
            $response['msg'] = 'Please provide Password';
            $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
        } else {
            $response['msg'] = 'Username or Password incorrect!';
            $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
            if (isValidUser($username, $password)) {
                $u = getUser($username);
                if ($u['status'] == 'A') {
                    $response['code'] = SUCCESS_RESPOSE_CODE;
                    $_SESSION['user_id'] = $u['id'];
                    $_SESSION['username'] = $u['username'];
                    $_SESSION['role'] = isset($u['metas']['role']) ? $u['metas']['role'] : "na";
                    $_SESSION['display_name'] = $u['display_name'];
                    $_SESSION['registered'] = date("M. Y", strtotime($u['registered']));
                    /* Syncing Cart */
                    syncCart(session_id(), $u['id']);

                    $response['redirecturl'] = isset($_POST['redirect']) ? $_POST['redirect'] : $sys['site_url'];
                    $response['msg'] = 'Successfully Logged In!';
                    $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
                } else if ($u['status'] == 'P') {
                    $response['code'] = ERROR_RESPOSE_CODE;
                    $response['msg'] = 'Your account activation is pending!';
                    $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
                } else if ($u['status'] == 'I') {
                    $response['code'] = ERROR_RESPOSE_CODE;
                    $response['msg'] = 'Your account is inactive!';
                    $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
                } else {
                    $response['code'] = ERROR_RESPOSE_CODE;
                    $response['msg'] = 'It looks you have requested to delete your account!';
                    $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
                }
            } else {
                $response['code'] = ERROR_RESPOSE_CODE;
                $response['msg'] = 'Username or Password Incorrect';
                $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
            }
        }
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'login-price-lister') {
    $response['code'] = ERROR_RESPOSE_CODE;
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
        $password = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);

        if ($username == '') {
            $response['msg'] = 'Please provide Username';
            $response['htmlmsg'] = '<div class="alert alert-danger">Please provide Username</div>';
        } else if ($password == '') {
            $response['msg'] = 'Please provide Password';
            $response['htmlmsg'] = '<div class="alert alert-danger">Please provide Password</div>';
        } else {
            $response['msg'] = 'Username or Password incorrect!';
            $response['htmlmsg'] = '<div class="alert alert-danger">Username or Password incorrect!</div>';
            if (loginLister($username, $password)) {
                if (isListerActive($username)) {
                    $response['code'] = SUCCESS_RESPOSE_CODE;
                    $response['redirecturl'] = $sys['config']['site_url'];
                    $listerdata = getLister(getListerId($username, $password));
                    $_SESSION['is_lister_login'] = true;
                    $_SESSION['lister_id'] = $listerdata['id'];
                    $_SESSION['lister_username'] = $listerdata['username'];
                    $_SESSION['lister_last_login'] = $listerdata['last_login_timestamp'];
                    updateListerLastLogin($listerdata['id']);
                    $response['msg'] = 'Successfully Logged In!';
                    $response['htmlmsg'] = '<div class="alert alert-success">Successfully Logged In!</div>';
                } else {
                    $response['code'] = ERROR_RESPOSE_CODE;
                    $response['msg'] = 'Your account is inactive!';
                    $response['htmlmsg'] = '<div class="alert alert-danger">Your account is inactive!</div>';
                }
            }
        }
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'register-price-lister') {
    $response['code'] = ERROR_RESPOSE_CODE;
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['website']) && isset($_POST['password']) && isset($_POST['cpassword'])) {
        $lister['username'] = filter_var(trim($_POST['email']), FILTER_SANITIZE_STRING);
        $lister['password'] = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);
        $lister['cpassword'] = filter_var(trim($_POST['cpassword']), FILTER_SANITIZE_STRING);
        $lister['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
        $lister['email'] = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $lister['mobile'] = filter_var(trim($_POST['mobile']), FILTER_SANITIZE_STRING);
        $lister['phone'] = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
        $lister['address'] = filter_var(trim($_POST['address']), FILTER_SANITIZE_STRING);
        $lister['city'] = filter_var(trim($_POST['city']), FILTER_SANITIZE_STRING);
        $lister['state'] = filter_var(trim($_POST['state']), FILTER_SANITIZE_STRING);
        $lister['pincode'] = filter_var(trim($_POST['pincode']), FILTER_SANITIZE_STRING);
        $lister['website'] = filter_var(trim($_POST['website']), FILTER_SANITIZE_STRING);
        $lister['country'] = "India";
        $lister['status'] = 'A';

        if ($lister['password'] == '') {
            $response['msg'] = 'Please provide Password';
            $response['htmlmsg'] = '<div class="alert alert-danger">Please provide Password</div>';
        } else if ($lister['email'] == '') {
            $response['msg'] = 'Please provide Email';
            $response['htmlmsg'] = '<div class="alert alert-danger">Please provide Email</div>';
        } else if ($lister['password'] != $lister['cpassword']) {
            $response['msg'] = 'Password not matched!';
            $response['htmlmsg'] = '<div class="alert alert-danger">Password not matched!</div>';
        } else {
            $response['code'] = ERROR_RESPOSE_CODE;
            if (addLister($lister)) {
                $response['code'] = SUCCESS_RESPOSE_CODE;
                //making user logged
                $listerdata = getLister(getListerId($lister['username'], $lister['password']));
                $_SESSION['is_lister_login'] = true;
                $_SESSION['lister_id'] = $listerdata['id'];
                $_SESSION['lister_username'] = $listerdata['username'];

                mail($lister['email'], "Lister Account Creation on eValueBazaar", "Your lister account has been created", getEmailHeaders());
                $response['redirecturl'] = $sys['config']['site_url'];
                $response['msg'] = 'Lister registered successfully!';
                $response['htmlmsg'] = '<div class="alert alert-success">Lister registered successfully!</div>';
            } else {
                $response['msg'] = $queryerrormsg;
                $response['htmlmsg'] = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
            }
        }
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'list-price') {
    $response['code'] = ERROR_RESPOSE_CODE;
    if (isset($_POST['productid']) && isset($_POST['productUrl']) && isset($_POST['price'])) {
        $pid = filter_var(trim($_POST['productid']), FILTER_SANITIZE_NUMBER_INT);
        $purl = filter_var(trim($_POST['productUrl']), FILTER_SANITIZE_STRING);
        $price = filter_var(trim($_POST['price']), FILTER_SANITIZE_STRING);
        $listerid = $_SESSION['lister_id'];

        if ($purl == '') {
            $response['msg'] = 'Please url required';
            $response['htmlmsg'] = '<div class="alert alert-danger">Please url required!</div>';
        } else if ($price == '' && is_nan($price)) {
            $response['msg'] = 'Please provide valid price';
            $response['htmlmsg'] = '<div class="alert alert-danger">Please provide valid price</div>';
        } else {
            $response['code'] = ERROR_RESPOSE_CODE;
            if (addListerPrice($pid, $listerid, $purl, $price, "A")) {
                $response['code'] = SUCCESS_RESPOSE_CODE;
                //mail($lister['email'], "Lister Account Creation on eValueBazaar", "Your lister account has been created", getHeaders());                
                $response['msg'] = 'Your price listed successfully!';
                $response['htmlmsg'] = '<div class="alert alert-success">Your price listed successfully!</div>';
            } else {
                $response['msg'] = $queryerrormsg;
                $response['htmlmsg'] = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
            }
        }
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'update-user-personal-info' && isUserLogged()) {
    $response['code'] = ERROR_RESPOSE_CODE;

    $u = getUser(getUserLoggedId());

    $first_name = filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING);
    $last_name = filter_var(trim($_POST['last_name']), FILTER_SANITIZE_STRING);
    $display_name = $first_name . " " . $last_name;
    $gender = filter_var(trim($_POST['gender']), FILTER_SANITIZE_STRING);

    $phone = isset($_POST['phone']) ? filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING) : "";
    $country = isset($_POST['country']) ? filter_var(trim($_POST['country']), FILTER_SANITIZE_STRING) : "";
    $state = isset($_POST['state']) ? filter_var(trim($_POST['state']), FILTER_SANITIZE_STRING) : "";
    $city = isset($_POST['city']) ? filter_var(trim($_POST['city']), FILTER_SANITIZE_STRING) : '';

    if ($first_name == '') {
        $response['msg'] = 'First name cannot be blank!';
        $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
    } else if (update(T_USERS, array("display_name" => $display_name), array("id" => $u['id']))) {
        updateUserMeta($u['id'], "first_name", $first_name);
        updateUserMeta($u['id'], "last_name", $last_name);
        updateUserMeta($u['id'], "gender", $gender);
        updateUserMeta($u['id'], "phone", $phone);
        updateUserMeta($u['id'], "city", $city);
        updateUserMeta($u['id'], "state", $state);
        updateUserMeta($u['id'], "country", $country);
        $_SESSION['display_name'] = $display_name;

        $response['code'] = SUCCESS_RESPOSE_CODE;
        $response['msg'] = 'Personal info updated successfully!';
        $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
    } else {
        $response['msg'] = 'Error! ' . $queryerrormsg;
        $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}
if ($action == 'update-user-email' && isUserLogged()) {
    $response['code'] = ERROR_RESPOSE_CODE;
    $u = getUser(getUserLoggedId());

    if (isset($_POST['otp2']) && isset($_POST['password'])) {
        $email = filter_var(trim($_SESSION['new-email']), FILTER_SANITIZE_STRING);
        $otp1 = isset($_POST['otp1']) ? filter_var(trim($_POST['otp1']), FILTER_SANITIZE_STRING) : "";
        $otp2 = filter_var(trim($_POST['otp2']), FILTER_SANITIZE_STRING);
        $password = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);

        if (trim($u['email']) <> "" && $otp1 != $_SESSION['otp1'] || $otp2 != $_SESSION['otp2']) {
            $response['msg'] = 'Incorrect OTP!';
            $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
        } else if (trim($u['email']) == "" && $otp2 != $_SESSION['otp2']) {
            $response['msg'] = 'Incorrect OTP!';
            $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
        } else if (md5($password) != $u['password']) {
            $response['msg'] = 'Incorrect Password!';
            $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
        } else if ($email == "") {
            $response['msg'] = 'Email Cannot be Empty!';
            $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
        } else if (isUserExists($email, "email")) {
            $response['msg'] = 'User Already Registered with the Given New Email!';
            $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
        } else {

            if (update(T_USERS, array("email" => $email), array("id" => $u['id']))) {
                unset($_SESSION['new-email']);
                $response['code'] = SUCCESS_RESPOSE_CODE;
                $response['msg'] = 'Email Address Updated Successfully!';
                $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
            } else {
                $response['msg'] = 'There is Error! Please try again later';
                $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
            }
        }
    } else {
        $response['msg'] = 'OTP and Password are required';
        $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}
if ($action == 'update-user-mobile' && isUserLogged()) {
    $response['code'] = ERROR_RESPOSE_CODE;
    $u = getUser(getUserLoggedId());

    if (!isset($_POST['password'])) {
        $response['msg'] = 'Password is required';
        $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
    } else if (!isset($_SESSION['new-mobile'])) {
        $response['msg'] = 'Mobile number not available in session';
        $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
    } else if (isset($sys['ACCOUNT_OTP_VERIFICATION']) && trim($sys['ACCOUNT_OTP_VERIFICATION']) == 'Y' && !isset($_POST['otp2'])) {
        $response['msg'] = 'OTP required';
        $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
    } else {
        $mobile = filter_var(trim($_SESSION['new-mobile']), FILTER_SANITIZE_STRING);
        $otp1 = isset($_POST['otp1']) ? filter_var(trim($_POST['otp1']), FILTER_SANITIZE_STRING) : "";
        $otp2 = isset($_POST['otp2']) ? filter_var(trim($_POST['otp2']), FILTER_SANITIZE_STRING) : "";
        $password = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);

        if (isset($sys['ACCOUNT_OTP_VERIFICATION']) && trim($sys['ACCOUNT_OTP_VERIFICATION']) == 'Y' && trim($u['mobile']) <> "" && ($otp1 != $_SESSION['otp1'] || $otp2 != $_SESSION['otp2'])) {
            $response['msg'] = 'Incorrect OTP!';
            $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
        } else if (isset($sys['ACCOUNT_OTP_VERIFICATION']) && trim($sys['ACCOUNT_OTP_VERIFICATION']) == 'Y' && trim($u['mobile']) == "" && $otp2 != $_SESSION['otp2']) {
            $response['msg'] = 'Incorrect OTP!';
            $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
        } else if (md5($password) != $u['password']) {
            $response['msg'] = 'Incorrect Password!';
            $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
        } else if ($mobile == "") {
            $response['msg'] = 'New Mobile Number Cannot be Empty!';
            $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
        } else if (isUserExists($mobile, "mobile")) {
            $response['msg'] = 'User Already Registered with the Given New Mobile!';
            $response['htmlmsg'] = '<div class="alert alert-danger">' . $response['msg'] . '</div>';
        } else {

            if (update(T_USERS, array("mobile" => $mobile), array("id" => $u['id']))) {
                $response['code'] = SUCCESS_RESPOSE_CODE;
                $response['msg'] = 'Mobile Number Updated Successfully!';
                $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
            } else {
                $response['msg'] = 'There is Error! Please try again later';
                $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
            }
        }
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}
if ($action == 'update-user-addresses' && isUserLogged()) {
    $response['code'] = ERROR_RESPOSE_CODE;
    if (isset($_POST['add-address']) && trim($_POST['add-address']) == getUserLoggedId()) {
        $address['user_id'] = getUserLoggedId();
        $address['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
        $address['mobile'] = filter_var(trim($_POST['mobile']), FILTER_SANITIZE_STRING);
        $address['phone'] = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
        $address['address'] = filter_var(trim($_POST['address']), FILTER_SANITIZE_STRING);
        $address['locality'] = filter_var(trim($_POST['locality']), FILTER_SANITIZE_STRING);
        $address['landmark'] = filter_var(trim($_POST['landmark']), FILTER_SANITIZE_STRING);
        $address['city'] = filter_var(trim($_POST['city']), FILTER_SANITIZE_STRING);
        $address['state'] = filter_var(trim($_POST['state']), FILTER_SANITIZE_STRING);
        $address['pincode'] = filter_var(trim($_POST['pincode']), FILTER_SANITIZE_STRING);
        $address['country'] = filter_var(trim($_POST['country']), FILTER_SANITIZE_STRING);
        $address['address_type'] = filter_var(trim($_POST['address_type']), FILTER_SANITIZE_STRING);
        $address['is_default'] = filter_var(trim($_POST['is_default']), FILTER_SANITIZE_STRING);
        $address['status'] = 'A';

        $aid = addUserAddress($address);
        if ($aid) {
            $response['address'] = getUserAddress($aid);
            $response['code'] = SUCCESS_RESPOSE_CODE;
            $response['msg'] = 'Addresses added successfully!';
            $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
        } else {
            $response['code'] = ERROR_RESPOSE_CODE;
            $response['msg'] = 'Error!';
            $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
        }
    }
    if (isset($_POST['update-address']) && trim($_POST['update-address']) == getUserLoggedId()) {
        $address['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
        $address['user_id'] = getUserLoggedId();
        $address['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
        $address['mobile'] = filter_var(trim($_POST['mobile']), FILTER_SANITIZE_STRING);
        $address['phone'] = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
        $address['address'] = filter_var(trim($_POST['address']), FILTER_SANITIZE_STRING);
        $address['locality'] = filter_var(trim($_POST['locality']), FILTER_SANITIZE_STRING);
        $address['landmark'] = filter_var(trim($_POST['landmark']), FILTER_SANITIZE_STRING);
        $address['city'] = filter_var(trim($_POST['city']), FILTER_SANITIZE_STRING);
        $address['state'] = filter_var(trim($_POST['state']), FILTER_SANITIZE_STRING);
        $address['pincode'] = filter_var(trim($_POST['pincode']), FILTER_SANITIZE_STRING);
        $address['country'] = filter_var(trim($_POST['country']), FILTER_SANITIZE_STRING);
        $address['address_type'] = filter_var(trim($_POST['address_type']), FILTER_SANITIZE_STRING);
        $address['is_default'] = filter_var(trim($_POST['is_default']), FILTER_SANITIZE_STRING);
        $address['status'] = 'A';

        if (updateUserAddress($address)) {
            $response['address'] = getUserAddress($address['id']);
            $response['code'] = SUCCESS_RESPOSE_CODE;
            $response['msg'] = 'Addresses updated successfully!';
            $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
        } else {
            $response['code'] = ERROR_RESPOSE_CODE;
            $response['msg'] = 'Error!';
            $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
        }
    }
    if (isset($_POST['delete-address']) && trim($_POST['delete-address']) == getUserLoggedId()) {
        $id = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);

        if (deleteUserAddress($id)) {
            $response['code'] = SUCCESS_RESPOSE_CODE;
            $response['msg'] = 'Addresses deleted successfully!';
            $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
        } else {
            $response['code'] = ERROR_RESPOSE_CODE;
            $response['msg'] = 'Error!';
            $response['htmlmsg'] = '<div class="alert alert-success">' . $response['msg'] . '</div>';
        }
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'getprice') {
    $response['code'] = ERROR_RESPOSE_CODE;
    if (isset($_POST['product_id']) && isset($_POST['seller_id']) && isset($_POST['product_color']) && isset($_POST['product_size'])) {
        $product_id = filter_var(trim($_POST['product_id']), FILTER_SANITIZE_STRING);
        $sid = filter_var(trim($_POST['seller_id']), FILTER_SANITIZE_STRING);
        $pcolor = filter_var(trim($_POST['product_color']), FILTER_SANITIZE_STRING);
        $psize = filter_var(trim($_POST['product_size']), FILTER_SANITIZE_STRING);
        $product = getProduct($product_id);
        if (isDiscountActive($product['sellers'][$sid], $pcolor, $psize)) {
            $response['code'] = SUCCESS_RESPOSE_CODE;
            $response['html'] = '<del>' . getSellingPrice($product['sellers'][$sid], $pcolor, $psize) . '</del> ' . getDiscountedPrice($product['sellers'][$sid], $pcolor, $psize);
        } else {
            $response['code'] = SQL_ERROR_RESPOSE_CODE;
            $response['html'] = getSellingPrice($product['sellers'][$sid], $pcolor, $psize);
        }
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'add-to-cart') {
    $response['code'] = ERROR_RESPOSE_CODE;
    $user_id = isUserLogged() ? getUserLoggedId() : session_id();
    $cart = getCart($user_id);
    $cart_details = $cart['cart_details'];

    if (isset($_POST['product_id']) && isset($_POST['shop_id']) && isset($_POST['quantity'])) {
        $ipinfo = ipInfo($_SERVER['REMOTE_ADDR']);
        $product_id = filter_var(trim($_POST['product_id']), FILTER_SANITIZE_NUMBER_INT);
        $shop_id = filter_var(trim($_POST['shop_id']), FILTER_SANITIZE_NUMBER_INT);

        $product = getProduct($product_id, $shop_id);
        $options = getOptions();
        /* Product Options */
        $customization_string = "";
        $itemOptions = array();
        $customization_price = 0;
        foreach ($product['product_options'] as $product_option) {
            if (isset($_POST['product_options'][$product_option['id']])) {
                $itemOption = array();
                $option_type = $options[$product_option['option_id']]['type'];
                //add customization_string
                //calculate customization_price
                //add product options for orders_products_options table
                if (in_array($option_type, array('Select/Listbox/Dropdown', 'Radio'))) {
                    //select and radio type options goes here
                    $tmp = trim($_POST['product_options'][$product_option['id']]) <> "" ? filter_var(trim($_POST['product_options'][$product_option['id']]), FILTER_SANITIZE_STRING) : "";
                    foreach ($product_option['values'] as $ov) {
                        if ($tmp == $ov['id']) {
                            $itemOption['product_option_id'] = $product_option['id'];
                            $itemOption['product_option_value_id'] = $ov['id'];
                            $itemOption['option_name'] = $options[$product_option['option_id']]['name'];
                            $itemOption['option_value'] = $ov['option_value'];
                            $itemOption['option_type'] = $option_type;
                            $customization_string .= "<br/>- <small>" . $options[$product_option['option_id']]['name'] . " : " . $itemOption['option_value'] . "</small>";
                            $customization_price += $ov['price_prefix'] == '+' ? +$ov['price'] : -$ov['price'];
                            break;
                        }
                    }
                } else if ($option_type === 'Checkbox') {
                    //checkbox type options goes here
                    $selected = $_POST['product_options'][$product_option['id']];
                    foreach ($product_option['values'] as $ov) {
                        if (in_array($ov['id'], $selected)) {
                            $itemOption['product_option_id'] = $product_option['id'];
                            $itemOption['product_option_value_id'] = $ov['id'];
                            $itemOption['option_name'] = $options[$product_option['option_id']]['name'];
                            $itemOption['option_value'] = $ov['option_value'];
                            $itemOption['option_type'] = $option_type;
                            $customization_string .= "<br/>- <small>" . $options[$product_option['option_id']]['name'] . " : " . $itemOption['option_value'] . "</small>";
                            $customization_price += $ov['price_prefix'] == '+' ? +$ov['price'] : -$ov['price'];
                        }
                    }
                } else if (in_array($option_type, array('Textarea', 'Text', 'Date &amp; Time', 'Date', 'Time', 'File'))) {
                    //all other type options goes here
                    $itemOption['product_option_id'] = $product_option['id'];
                    $itemOption['product_option_value_id'] = "0";
                    $itemOption['option_name'] = $options[$product_option['option_id']]['name'];
                    $itemOption['option_value'] = trim($_POST['product_options'][$product_option['id']]) <> "" ? trim($_POST['product_options'][$product_option['id']]) : "";
                    $itemOption['option_type'] = $option_type;
                    $customization_string .= "<br/>- <small>" . $options[$product_option['option_id']]['name'] . " : " . $itemOption['option_value'] . "</small>";
                }
                $itemOptions[] = $itemOption;
            }
        }
        $item['options'] = $itemOptions;

        $quantity = filter_var(trim($_POST['quantity']), FILTER_SANITIZE_NUMBER_INT);
        $id = md5($shop_id . "-" . $product_id . "-" . $customization_string);
        if (isset($cart_details['items'][$id])) {
            $tmpitem = $cart_details['items'][$id];
            $quantity = $quantity + $tmpitem['quantity'];
        }

        /* Shipping Details */
        $country = getCountry($ipinfo['country_code'], array('id'));
        $shipping = getProductShipping($product_id, $shop_id, $country['id']);
        if (empty($shipping)) {
            $shipping = getProductShipping($product_id, $shop_id, "0");
        }
        if (!empty($shipping)) {
            $duration = getShippingDuration($shipping['duration_id']);
        }

        $price = getProductPrice($product_id, $shop_id, $quantity);
        $item['product_id'] = $product_id;
        $item['shop_id'] = $shop_id;
        $item['sale_price'] = $price['sale_price'];
        $item['discount'] = $price['discount'];
        $item['price'] = $price['price'];
        $item['price_gst'] = $price['price_gst'];
        $item['commission_percent'] = $price['commission_percent'];
        $item['commission'] = $price['commission'];
        $item['commission_gst'] = $price['commission_gst'];
        $item['customization_string'] = $customization_string;
        $item['customization_price'] = $customization_price;
        $item['customization_price_gst'] = $customization_price * 0.18;
        $item['shipping_free'] = $price['ship_free'];
        $item['shipping_required'] = $product['requires_shipping'];
        $item['shipping_id'] = empty($shipping) ? 0 : $shipping['country'];
        $item['shipping_days'] = isset($duration) ? $duration['duration_to'] : "0";
        $item['shipping_company'] = empty($shipping) ? 0 : $shipping['company'];
        $item['shipping_label'] = isset($duration) ? $duration['label'] : "";
        $item['shipping_charges'] = $price['ship_free'] == 'Y' ? 0 : empty($shipping) ? 0 : ($shipping['charges'] + $shipping['additional_charges'] * ($item['quantity'] - 1));
        $item['shipped_datetime'] = "0000-00-00 00:00:00";
        $item['delivered_datetime'] = "0000-00-00 00:00:00";
        $item['completion_datetime'] = "0000-00-00 00:00:00";
        $item['amount'] = intval($item['price'] + $item['price_gst'] + $item['commission'] + $item['commission_gst'] + $item['customization_price'] + $item['customization_price_gst']);
        $item['quantity'] = $quantity;
        $item['total'] = $item['amount'] * $item['quantity'] + $item['shipping_charges'];
        $item['tax_free'] = "N";
        $item['refund_quantity'] = 0;
        $item['refund_amount'] = 0;
        $item['refund_amount_gst'] = 0;
        $item['refund_commission'] = 0;
        $item['refund_commission_gst'] = 0;
        $item['refund_total'] = 0;
        $item['refund_tax_total'] = 0;
        $item['affiliate_commission_percentage'] = 0;
        $item['affiliate_commission'] = 0;
        $item['is_cod'] = 'N';
        $item['note'] = filter_var(trim($_POST['note']), FILTER_SANITIZE_STRING);
        $item['additional_info'] = isset($_POST['additional_info']) ? json_encode($_POST['additional_info']) : "";
        $item['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $item['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $item['added_timestamp'] = date("Y-m-d H:i:s");
        $item['updated_timestamp'] = date("Y-m-d H:i:s");
        $cart_details['items'][$id] = $item;

        /*         * calculating cart total, shipping total & grand total */
        $cart_details['cart_total'] = 0;
        $cart_details['shipping_total'] = 0;
        foreach ($cart_details['items'] as $key => $i) {
            $cart_details['cart_total'] += $i['total'];
            $cart_details['shipping_total'] += $i['shipping_charges'];
        }
        $cart_details['grand_total'] = $cart_details['cart_total'] + $cart_details['shipping_total'];
        $cart['cart_details'] = $cart_details;

        if ($item['product_id'] == '' || $item['shop_id'] == '' || $item['quantity'] == '') {
            $response['htmlmsg'] = '<div class="alert alert-danger">Product Id, Shop Id and Quantity is required!</div>';
        } else {
            $response['code'] = SUCCESS_RESPOSE_CODE;
            $response['redirecturl'] = $sys['site_url'] . "/cart";
            $response['htmlmsg'] = '<div class="alert alert-success">Added in cart!</div>';
            if (!updateCart($cart)) {
                $response['code'] = SQL_ERROR_RESPOSE_CODE;
                $response['htmlmsg'] = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
            }
        }
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'update-cart-item-qty') {
    $response['code'] = ERROR_RESPOSE_CODE;
    $user_id = isUserLogged() ? getUserLoggedId() : session_id();
    $cart = getCart($user_id);
    $cart_details = $cart['cart_details'];
    $ipinfo = ipInfo($_SERVER['REMOTE_ADDR']);

    if (isset($_REQUEST['item_id'])) {
        $item_id = filter_var(trim($_REQUEST['item_id']), FILTER_SANITIZE_STRING);
        $quantity = filter_var(trim($_REQUEST['quantity']), FILTER_SANITIZE_NUMBER_INT);

        $tmpitem = $cart_details['items'][$item_id];
        $country = getCountry($ipinfo['country_code'], array('id'));
        $shipping = getProductShipping($tmpitem['product_id'], $tmpitem['shop_id'], $country['id']);
        if (empty($shipping)) {
            $shipping = getProductShipping($tmpitem['product_id'], $tmpitem['shop_id'], "0");
        }
        $shipping_charges = empty($shipping) ? 0 : ($shipping['charges'] + $shipping['additional_charges'] * ($quantity - 1));

        $cart_details['items'][$item_id]['quantity'] = $quantity;
        $cart_details['items'][$item_id]['shipping_charges'] = $shipping_charges;
        $cart_details['items'][$item_id]['total'] = $cart_details['items'][$item_id]['amount'] * $quantity + $shipping_charges;

        /*         * calculating cart total, shipping total & grand total */
        $cart_details['cart_total'] = 0;
        $cart_details['shipping_total'] = 0;
        foreach ($cart_details['items'] as $key => $i) {
            $cart_details['cart_total'] += $i['total'];
            $cart_details['shipping_total'] += $i['shipping_charges'];
        }
        $cart_details['grand_total'] = $cart_details['cart_total'] + $cart_details['shipping_total'];

        $cart['cart_details'] = $cart_details;
        $cart['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $cart['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        if (updateCart($cart)) {
            $response['code'] = SUCCESS_RESPOSE_CODE;
            $response['msg'] = "item quantity updated";
        }
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'delete-cart-item') {
    $response['code'] = ERROR_RESPOSE_CODE;
    $user_id = isUserLogged() ? getUserLoggedId() : session_id();
    $cart = getCart($user_id);
    $cart_details = $cart['cart_details'];

    if (isset($_REQUEST['item_id'])) {
        $item_id = filter_var(trim($_REQUEST['item_id']), FILTER_SANITIZE_STRING);
        unset($cart_details['items'][$item_id]);

        /*         * calculating cart total, shipping total & grand total */
        $cart_details['cart_total'] = 0;
        $cart_details['shipping_total'] = 0;
        foreach ($cart_details['items'] as $key => $i) {
            $cart_details['cart_total'] += $i['total'];
            $cart_details['shipping_total'] += $i['shipping_charges'];
        }
        $cart_details['grand_total'] = $cart_details['cart_total'] + $cart_details['shipping_total'];

        $cart['cart_details'] = $cart_details;
        $cart['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $cart['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        if (updateCart($cart)) {
            $response['code'] = SUCCESS_RESPOSE_CODE;
            $response['msg'] = "item deleted from cart";
        }
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'checkout') {
    $response['code'] = ERROR_RESPOSE_CODE;
    if (isset($_POST['saveorder']) && isset($_POST['delivery_address']) && isset($_POST['payment_method']) && isUserLogged()) {
        $order['reference_number'] = getReferenceNumber();
        $order['invoice_number'] = getInvoiceNumber();
        $order['user_id'] = getUserLoggedId();

        $u = getUser(getUserLoggedId());
        $a = getUserAddress(filter_var(trim($_POST['delivery_address']), FILTER_SANITIZE_STRING));
        $c = getCountry($a['country']);

        $order['name'] = $u['display_name'];
        $order['phone'] = $u['mobile'];
        $order['email'] = $u['email'];
        $order['billing_name'] = $a['name'];
        $order['billing_mobile'] = $a['mobile'];
        $order['billing_phone'] = $a['phone'];
        $order['billing_email'] = $u['email'];
        $order['billing_address'] = $a['address'];
        $order['billing_locality'] = $a['locality'];
        $order['billing_landmark'] = $a['landmark'];
        $order['billing_city'] = $a['city'];
        $order['billing_state'] = $a['state'];
        $order['billing_pincode'] = $a['pincode'];
        $order['billing_country'] = $c['name'];
        $order['billing_country_id'] = $a['country'];
        $order['billing_address_type'] = $a['address_type'];
        $order['shipping_name'] = $a['name'];
        $order['shipping_mobile'] = $a['mobile'];
        $order['shipping_phone'] = $a['phone'];
        $order['shipping_email'] = $u['email'];
        $order['shipping_address'] = $a['address'];
        $order['shipping_locality'] = $a['locality'];
        $order['shipping_landmark'] = $a['landmark'];
        $order['shipping_city'] = $a['city'];
        $order['shipping_state'] = $a['state'];
        $order['shipping_pincode'] = $a['pincode'];
        $order['shipping_country'] = $c['name'];
        $order['shipping_country_id'] = $a['country'];
        $order['shipping_address_type'] = $a['address_type'];
        $order['shipping_method'] = "-";
        $order['shipping_required'] = "Y";

        $pmethod = getPaymentMethod(filter_var(trim($_POST['payment_method']), FILTER_SANITIZE_STRING));

        $cart = getCart(getUserLoggedId());
        $products = array();
        $cgst = $sgst = $igst = $tax_total = $total_amount = $shipping_charges = $site_commission = 0;
        foreach ($cart['cart_details']['items'] as $item) {
            $p = getProduct($item['product_id'], $item['shop_id'], array("type", "sku", "name", "brand", "model"));
            $s = getShop($item['shop_id'], array("owner_id", "name", "state"));
            $o = getUser($s['owner_id'], array("display_name", "username", "email", "mobile"));

            $item['product_type'] = $p['type'];
            $item['product_sku'] = $p['sku'];
            $item['product_name'] = $p['name'];
            $item['product_brand'] = $p['brand'];
            $item['product_model'] = $p['model'];
            $item['shop_name'] = $s['name'];
            $item['shop_owner_name'] = $o['display_name'];
            $item['shop_owner_username'] = $o['username'];
            $item['shop_owner_email'] = $o['email'];
            $item['shop_owner_phone'] = $o['mobile'];
            $item['status'] = "1";

            $item['price_cgst'] = trim($a['state']) == trim($s['state']) ? ($item['price_gst'] / 2) : 0.00;
            $item['price_sgst'] = trim($a['state']) == trim($s['state']) ? ($item['price_gst'] / 2) : 0.00;
            $item['price_igst'] = trim($a['state']) <> trim($s['state']) ? $item['price_gst'] : 0.00;
            $item['commission_cgst'] = isset($sys['STATE']) && trim($sys['STATE']) == trim($s['state']) ? ($item['commission_gst'] / 2) : 0.00;
            $item['commission_sgst'] = isset($sys['STATE']) && trim($sys['STATE']) == trim($s['state']) ? ($item['commission_gst'] / 2) : 0.00;
            $item['commission_igst'] = isset($sys['STATE']) && trim($sys['STATE']) <> trim($s['state']) ? $item['commission_gst'] : 0.00;
            $item['customization_price_cgst'] = trim($a['state']) == trim($s['state']) ? ($item['customization_price_gst']) / 2 : 0.00;
            $item['customization_price_sgst'] = trim($a['state']) == trim($s['state']) ? ($item['customization_price_gst']) / 2 : 0.00;
            $item['customization_price_igst'] = trim($a['state']) <> trim($s['state']) ? $item['customization_price_gst'] : 0.00;
            $item['refund_amount_cgst'] = "0.00";
            $item['refund_amount_sgst'] = "0.00";
            $item['refund_amount_igst'] = "0.00";
            $item['refund_commission_cgst'] = "0.00";
            $item['refund_commission_sgst'] = "0.00";
            $item['refund_commission_igst'] = "0.00";
            $item['is_cod'] = $pmethod['code'] == PM_CASH_ON_DELIVERY ? "Y" : "N";

            $cgst += $item['price_cgst'] + $item['commission_cgst'] + $item['customization_price_cgst'];
            $sgst += $item['price_sgst'] + $item['commission_sgst'] + $item['customization_price_sgst'];
            $igst += $item['price_igst'] + $item['commission_igst'] + $item['customization_price_igst'];
            $total_amount += $item['total'];
            $shipping_charges += $item['shipping_charges'];
            $site_commission += $item['commission'] + $item['commission_gst'];

            $products[] = $item;
        }
        $order['cart_quantity'] = count($cart['cart_details']['items']);
        $order['cart_total'] = $total_amount;
        $order['cgst'] = $cgst;
        $order['sgst'] = $sgst;
        $order['igst'] = $igst;
        $order['vat_percentage'] = "0.00";
        $order['vat'] = "0.00";
        $order['tax_total'] = $cgst + $sgst + $igst;
        $order['shipping_charges'] = $shipping_charges;
        $order['pg_charges'] = 0.00;
        $order['total_amount'] = $total_amount;
        $order['coupon_code'] = "";
        $order['coupon_amount'] = 0.00;
        $order['discount_label'] = "";
        $order['discount_amount'] = 0.00;
        $order['payable_amount'] = $order['total_amount'] + $order['shipping_charges'] + $order['pg_charges'] - $order['coupon_amount'] - $order['discount_amount'];
        $order['credits_used'] = 0.00;
        $order['paid_amount'] = $order['payable_amount'] - $order['credits_used'];
        $order['wallet_used'] = $order['credits_used'] != 0.00 ? "Y" : "N";
        $order['payment_method'] = $pmethod['code'];
        $order['payment_method_id'] = $pmethod['id'];
        $order['site_commission'] = $site_commission;
        $order['language'] = "";
        $order['customer_comments'] = "";
        $order['admin_comments'] = "";
        $order['cancelled_by'] = "0";
        $order['is_cod'] = $pmethod['code'] == PM_CASH_ON_DELIVERY ? "Y" : "N";
        $order['note'] = "";
        $order['additional_info'] = "";
        $order['affiliate_id'] = "0";
        $order['affiliate_commission'] = "0.00";
        $order['referrer_id'] = "0";
        $order['referrer_reward_points'] = "0";
        $order['referee_reward_points'] = "0";
        $order['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $order['forwarded_ip_address'] = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $order['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $order['added_timestamp'] = date("Y-m-d H:i:s");
        $order['updated_timestamp'] = date("Y-m-d H:i:s");
        $order['order_status'] = ($order['payment_method'] == PM_CASH_ON_DELIVERY && isset($sys['ORDER_COD_DEFAULT_STATUS'])) ? $sys['ORDER_COD_DEFAULT_STATUS'] : OS_PAYMENT_PENDING;
        $order['payment_status'] = ($order['payment_method'] !== PM_CASH_ON_DELIVERY && isset($sys['ORDER_DEFAULT_STATUS'])) ? $sys['ORDER_DEFAULT_STATUS'] : OS_PAYMENT_PENDING;
        $order['products'] = $products;

        if ($order['total_amount'] == 0 || $order['total_amount'] == '' || $order['email'] == '') {
            $response['htmlmsg'] = '<div class="alert alert-danger">Total value or email is empty!</div>';
        } else {
            $order_id = addOrder($order);
            //updateCartOrderId($order['username'], $order_id);
            $response['code'] = SUCCESS_RESPOSE_CODE;
            $response['redirecturl'] = $sys['site_url'] . "/order-placed?orderid=" . $order_id;
            if ($order['is_cod'] == "N") {
                $response['redirecturl'] = $sys['site_url'] . "/pay?orderid=" . $order_id;
            }
            if ($order_id) {
                $response['htmlmsg'] = '<div class="alert alert-success">Order Saved!</div>';
                $response['orderid'] = $order_id;
                $cart['cart_details'] = array('items' => array(), 'cart_total' => 0, 'tax_total' => 0, 'grand_total' => 0);
                updateCart($cart);
            } else {
                $response['code'] = SQL_ERROR_RESPOSE_CODE;
                $response['htmlmsg'] = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
            }
        }
    }

    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == 'cancel-order') {
    $response['code'] = ERROR_RESPOSE_CODE;
    if (isset($_POST['order_id'])) {
        $request['order_id'] = filter_var(trim($_POST['order_id']), FILTER_SANITIZE_STRING);
        $request['user_id'] = isset($_REQUEST['user_id']) ? trim($_REQUEST['user_id']) : getUserLoggedId();
        $request['reason_id'] = isset($_POST['reason_id']) ? filter_var(trim($_POST['reason_id']), FILTER_SANITIZE_STRING) : "";
        $request['message'] = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);
        $request['request_date'] = date("Y-m-d H:i:s");
        $request['request_status'] = "P";
        $response['orderid'] = $request['order_id'];

        if (getOrderCancelRequest(array('order_id' => $request['order_id'])) == false) {
            if (addOrderCancelRequest($request)) {
                $response['code'] = SUCCESS_RESPOSE_CODE;
                $response['htmlmsg'] = '<div class="alert alert-success">Order Cancel Request Saved!</div>';
            } else {
                $response['code'] = SQL_ERROR_RESPOSE_CODE;
                $response['htmlmsg'] = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
            }
        } else {
            $response['code'] = SUCCESS_RESPOSE_CODE;
            $response['htmlmsg'] = '<div class="alert alert-danger">Order Cancel Request Already Initiated!</div>';
        }
    }
    $response['redirecturl'] = $sys['site_url'] . "/order-cancel-request-saved?orderid=" . $request['order_id'];
    header("Content-Type:application/json");
    echo json_encode($response);
    exit();
}

if ($action == "change-currency") {
    $currency = filter_var(trim($_REQUEST['currency']), FILTER_SANITIZE_STRING);
    $currencies = getCurrencies(array("code"));
    foreach ($currencies as $c) {
        if ($c['code'] == $currency) {
            $_SESSION['currency'] = getCurrency($c['code']);
            break;
        }
    }
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $sys['site_url'];
    header("location: " . $referer);
}

if ($action == "update-currency-rate") {
    $timestamp = isset($sys["CURRENCY_RATE_LAST_UPDATED"]) ? strtotime($sys["CURRENCY_RATE_LAST_UPDATED"]) : strtotime("-4 hours");
    if ((time() - $timestamp) < (4 * 60 * 60)) {
        die("Currency rate already updated " . timeElapsedString($timestamp));
    }
    $DC = isset($sys["DEFAULT_CURRENCY"]) ? $sys["DEFAULT_CURRENCY"] : "INR";
    $currencies = getCurrencies(array("id", "code", "rate_usd_base", "rate_dc_base"));

    $tmpCurrencies = array();
    foreach ($currencies as $c) {
        $tmpCurrencies[] = $c['code'];
    }
    $output = currencyRates(implode(",", $tmpCurrencies));
    if ($output == null) {
        die("Error - NULL");
    }
    if ($output['success']) {
        $USDDC = isset($output['quotes']['USD' . $DC]) ? $output['quotes']['USD' . $DC] : $output['quotes']['USDINR'];

        foreach ($currencies as $c) {
            $rate_usd_base = isset($output['quotes']['USD' . $c['code']]) ? $output['quotes']['USD' . $c['code']] : $c['rate_usd_base'];
            $rate_dc_base = $rate_usd_base / $USDDC;
            $data = array(
                "rate_usd_base" => $rate_usd_base,
                "rate_dc_base" => $rate_dc_base,
                "rate_last_updated" => date("Y-m-d H:i:s"),
                "rate_last_updated_by" => getUserLoggedId()
            );
            update(T_CURRENCIES, $data, array("id" => $c['id']));
        }
        saveConfig("CURRENCY_RATE_LAST_UPDATED", date("Y-m-d H:i:s"));
        echo "Currency Rate Update Successfully!";
    } else {
        die("Error - " . $output['msg']);
    }
}
?>