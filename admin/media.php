<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php 
//Not authorized to access
if (!isUserHavePermission(MANAGE_MEDIA_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

$dates = getPostsDates(YEAR_MONTH);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Media Library - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
        <style>            
            .media-toolbar {
                background-color: white;
                padding: 10px 10px;
            }
            .media-toolbar-primary {
                float: right;
                margin-left: 3px;
            }
            .media-toolbar-secondary {
                float: left;
            }
            .attachment-filters {
                float: left;
                width: auto;
                margin: 3px;
            }
            .media-button, .media-buttons {
                margin: 3px;
                float: left;
            }
            .search {
                float: right;
                width: auto;
            }
            
            .attachments {
                list-style: none;
                position: static;                
                padding: 2px;                
                right: 0;
                margin:0;
                margin-right: 0;
                left:0;
                bottom:0;
                overflow: auto;
                outline: 0;
            }
            .attachment {                
                width: 12.5%;
                position: relative;
                list-style: none;
                float: left;
                cursor: pointer;
                text-align: center;
                margin:0;
                padding: 8px;    
                box-sizing: border-box;
            }
            .attachment-preview {
                position: relative;                
                background: #eee;
                cursor: pointer;
            }
            .attachment-preview:before {
                content: "";
                display: block;
                padding-top: 100%;
            }
            .attachment .thumbnail {
                overflow: hidden;
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                opacity: 1;
                transition: opacity .1s;
                border-radius: 0px;
            }
            .attachment .thumbnail .centered {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                transform: translate(50%,50%);
            }
            .attachment .thumbnail .centered img {
                transform: translate(-50%,-50%);
                position: absolute;
                top:0;
                left:0;
            }
            .attachment .landscape img {
                max-height: 100%;
            }
            .attachment .portrait img {
                max-width: 100%;
            }
            @media only screen and (max-width: 1000px) {
            
                .media-toolbar-primary, .media-toolbar-secondary {
                    float: left;
                    position: relative;
                    max-width: 100%;
                }
                
                .attachment {                
                    width: 25%;
                }                                
            }  
            
            @media only screen and (max-width: 782px) {
            
                .media-toolbar-primary, .media-toolbar-secondary {
                    float: left;
                    position: relative;
                    max-width: 100%;
                }
                
                .attachment {                
                    width: 50%;
                }                
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
                        Media Library
                        <small><a href="" class="btn btn-default">Add New</a></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active">Media Library</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="media-toolbar">
                        <div class="media-toolbar-secondary">
                            <div class="btn-group media-buttons">
                                <a href="media.php?mode=list" class="btn btn-default"><i class="glyphicon glyphicon-th-list"></i></a>
                                <a href="media.php?mode=grid" class="btn btn-default active"><i class="glyphicon glyphicon-th-large"></i></a>
                            </div>                            
                            <select id="media-attachment-filters" class="form-control attachment-filters">
                                <option value="">All media items</option>
                                <option value="image">Images</option>
                                <option value="audio">Audio</option>
                                <option value="video">Video</option>
                                <option value="unattached">Unattached</option>
                                <option value="mine">Mine</option>
                            </select>                            
                            <select id="media-attachment-date-filters" class="form-control attachment-filters">
                                <option value="">All dates</option>
                                <?php foreach($dates as $d) {
                                    $timestamp = strtotime($d."01");
                                    ?>
                                <option value="<?= date("Y-m", $timestamp) ?>"><?= date("F Y", $timestamp) ?></option>
                                <?php } ?>
                            </select>
                            <button type="button" class="btn btn-default media-button select-mode-toggle-button">Bulk Select</button>
                            <span class="spinner"></span>
                            <button type="button" class="button media-button button-primary button-large  delete-selected-button hidden">Delete Selected</button>
                        </div>
                        <div class="media-toolbar-primary search-form">                            
                            <input type="search" placeholder="Search media items..." id="media-search-input" class="form-control search">
                        </div>
                        <div class="clearfix"></div>
                    </div>  

                    <ul tabindex="-1" class="attachments" id="attachments">
                        
                    </ul>

                </section>
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>
            <?php include 'right_sidebar.php'; ?>

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>                
        <script>
            var paged = 1;
            var max_pages = 1;
            
            $("#media-attachment-filters, #media-attachment-date-filters").on("change", function(){
                paged = 1;
                $('#attachments').html("");
                loadmore();
            });
            $("#media-search-input").on("keyup", function(){
                paged = 1;
                $('#attachments').html("");
                loadmore();
            });
            
            function loadmore() {
                if(paged > max_pages) {
                    return;
                }
                
                var data = new FormData();
                data.append("type", $("#media-attachment-filters").val());
                data.append("year_month", $("#media-attachment-date-filters").val());
                data.append("q", $("#media-search-input").val());
                
                $.ajax({
                    type: "POST",
                    url: "<?= $sys['site_url'] ?>/requests.php?action=media&paged=" + paged,
                    data: data,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.msg === "success") {
                            for(var i = 0; i < response.items.length; i++) {
                                var template = $('#files-template').text();
                                var ext = response.items[i].file_path.split('.').pop().toLowerCase();
                                var file_path = "dist/img/unknown.png";
                                switch(ext) {
                                    case "jpeg":
                                    case "jpg":
                                    case "png":
                                    case "gif":
                                        file_path = response.items[i].file_path;
                                        break;
                                    case "pdf":
                                        file_path = "dist/img/pdf.png";
                                        break;
                                    case "doc":
                                        file_path = "dist/img/doc.png";
                                        break;
                                    case "docx":
                                        file_path = "dist/img/docx.png";
                                        break;
                                    case "mp3":
                                        file_path = "dist/img/mp3.png";
                                        break;
                                    case "mp4":
                                        file_path = "dist/img/mp4.png";
                                        break;
                                }
                                template = template.replace('%%filepath%%', file_path);

                                template = $(template);
                                template.prop('aria-label', response.items[i].file_name);
                                template.prop('title', response.items[i].file_name);
                                template.prop('data-id', response.items[i].post_id);
                                
                                var width = response.items[i].width;
                                var height = response.items[i].height;
                                if(width && height && width < height) {
                                    template.find('div.attachment-preview').addClass('portrait');
                                } else {
                                    template.find('div.attachment-preview').addClass('landscape');
                                }
                                
                                $('#attachments').find('li.empty').fadeOut(); // remove the 'no files yet'
                                $('#attachments').append(template);
                            }
                            max_pages = response.max_pages;
                            paged++;
                        } else {
                            alert(response.msg);
                        }
                    }
                });
            }
            
            $(window).scroll(function(e){
                if($(window).scrollTop() + $(window).height() == $(document).height()){
                    loadmore();
                }
            });
            
            $("#attachments").on("click", "li", function(){
                var post_id = $(this).prop("data-id");
                window.location.href = 'post-edit.php?id=' + post_id;
            });
            
            loadmore();
        </script>
        <script type="text/html" id="files-template">
            <li tabindex="0" role="checkbox" aria-label="" aria-checked="false" data-id="" class="attachment save-ready">
                <div class="attachment-preview">
                    <div class="thumbnail">
                        <div class="centered">
                            <img src="%%filepath%%" draggable="false" alt=""/>
                        </div>
                    </div>
                </div>                            
            </li>
        </script>
    </body>
</html>