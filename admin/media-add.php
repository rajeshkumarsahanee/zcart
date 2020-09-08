<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Upload New Media - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
        <link href="<?= $sys['site_url']; ?>/admin/plugins/uploader/css/jquery.dm-uploader.min.css" rel="stylesheet" type="text/css" />    
        <style>
            /* 
                A couple styles to make the demo page look good
            */
            .row {
                margin-bottom: 1rem;
            }
            [class*="col-"] {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }
            .max-file-size {
                margin-top: 20px;
            }
            hr {
                margin-top: 0px;
                margin-bottom: 0px;
            }
            #files {
                overflow-y: scroll !important;
                min-height: 320px;
            }
            @media (min-width: 768px) {
                #files {
                    min-height: 0;
                }
            }
            #debug {
                overflow-y: scroll !important;
                height: 180px;	
            }

            .dm-uploader {
                border: 0.25rem dashed #A5A5C7;
                text-align: center;
                padding-bottom: 20px;
            }
            .dm-uploader.active {
                border-color: red;
                border-style: solid;
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
                        Upload New Media
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Media</li>
                        <li class="active">Add New</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">                                          
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <!-- Our markup, the important part here! -->
                            <div id="drag-and-drop-zone" class="dm-uploader">
                                <h3 class="text-muted">Drop files here</h3>
                                <p>or</p>
                                <div class="btn btn-sm btn-default">
                                    <span>Select Files</span>
                                    <input type="file" title='Click to add Files' />
                                </div>
                                <p class="max-file-size">Maximum upload file size: <?= min((int) ini_get('upload_max_filesize'), (int) ini_get('post_max_size'), (int) ini_get('memory_limit')) ?> MB.</p>
                            </div><!-- /uploader -->
                        </div>
                        <div class="col-md-12 col-sm-12">
                            <div class="card h-100">
                                <ul class="list-unstyled p-2 d-flex flex-column col" id="files">
                                    <li class="text-muted text-center empty">No files uploaded.</li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- /file list -->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card h-100">
                                <div class="card-header">
                                    Debug Messages
                                </div>

                                <ul class="list-group list-group-flush" id="debug">
                                    <li class="list-group-item text-muted empty">Loading plugin....</li>
                                </ul>
                            </div>
                        </div>
                    </div> <!-- /debug -->

                </section>
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>
            <?php include 'right_sidebar.php'; ?>

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>  
        <script src="<?= $sys['site_url'] ?>/admin/plugins/uploader/js/jquery.dm-uploader.min.js" type="text/javascript"></script>
        <script>
            $(function () {
                /*
                 * For the sake keeping the code clean and the examples simple this file
                 * contains only the plugin configuration & callbacks.
                 * 
                 * UI functions ui_* can be located in: demo-ui.js
                 */
                $('#drag-and-drop-zone').dmUploader({//
                    url: '<?= $sys['site_url'] ?>/requests.php?action=upload',
                    maxFileSize: 3000000, // 3 Megs 
                    onDragEnter: function () {
                        // Happens when dragging something over the DnD area
                        this.addClass('active');
                    },
                    onDragLeave: function () {
                        // Happens when dragging something OUT of the DnD area
                        this.removeClass('active');
                    },
                    onInit: function () {
                        // Plugin is ready to use
                        ui_add_log('Penguin initialized :)', 'info');
                    },
                    onComplete: function () {
                        // All files in the queue are processed (success or error)
                        ui_add_log('All pending tranfers finished');
                    },
                    onNewFile: function (id, file) {
                        // When a new file is added using the file selector or the DnD area
                        ui_add_log('New file added #' + id);
                        ui_multi_add_file(id, file);
                    },
                    onBeforeUpload: function (id) {
                        // about tho start uploading a file
                        ui_add_log('Starting the upload of #' + id);
                        ui_multi_update_file_status(id, 'uploading', 'Uploading...');
                        ui_multi_update_file_progress(id, 0, '', true);
                    },
                    onUploadCanceled: function (id) {
                        // Happens when a file is directly canceled by the user.
                        ui_multi_update_file_status(id, 'warning', 'Canceled by User');
                        ui_multi_update_file_progress(id, 0, 'warning', false);
                    },
                    onUploadProgress: function (id, percent) {
                        // Updating file progress
                        ui_multi_update_file_progress(id, percent);
                    },
                    onUploadSuccess: function (id, data) {
                        // A file was successfully uploaded
                        ui_add_log('Server Response for file #' + id + ': ' + JSON.stringify(data));
                        ui_add_log('Upload of file #' + id + ' COMPLETED', 'success');
                        ui_multi_update_file_status(id, 'success', data.msg);
                        ui_multi_update_file_progress(id, 100, 'success', false);
                        ui_multi_update_edit_link(id, data.post_id);
                    },
                    onUploadError: function (id, xhr, status, message) {
                        ui_multi_update_file_status(id, 'danger', message);
                        ui_multi_update_file_progress(id, 0, 'danger', false);
                    },
                    onFallbackMode: function () {
                        // When the browser doesn't support this plugin :(
                        ui_add_log('Plugin cant be used here, running Fallback callback', 'danger');
                    },
                    onFileSizeError: function (file) {
                        ui_add_log('File \'' + file.name + '\' cannot be added: size excess limit', 'danger');
                    }
                });
            });

            // Adds an entry to our debug area
            function ui_add_log(message, color) {
                var d = new Date();

                var dateString = (('0' + d.getHours())).slice(-2) + ':' +
                        (('0' + d.getMinutes())).slice(-2) + ':' +
                        (('0' + d.getSeconds())).slice(-2);

                color = (typeof color === 'undefined' ? 'muted' : color);

                var template = $('#debug-template').text();
                template = template.replace('%%date%%', dateString);
                template = template.replace('%%message%%', message);
                template = template.replace('##color##', color);

                $('#debug').find('li.empty').fadeOut(); // remove the 'no messages yet'
                $('#debug').prepend(template);
            }

            // Creates a new file and add it to our list
            function ui_multi_add_file(id, file) {
                var template = $('#files-template').text();
                template = template.replace('%%filename%%', file.name);

                template = $(template);
                template.prop('id', 'uploaderFile' + id);
                template.data('file-id', id);

                $('#files').find('li.empty').fadeOut(); // remove the 'no files yet'
                $('#files').prepend(template);
            }

            // Changes the status messages on our list
            function ui_multi_update_file_status(id, status, message) {
                $('#uploaderFile' + id).find('span').html(message).prop('class', 'status text-' + status);
            }

            // Updates a file progress, depending on the parameters it may animate it or change the color.
            function ui_multi_update_file_progress(id, percent, color, active) {
                color = (typeof color === 'undefined' ? false : color);
                active = (typeof active === 'undefined' ? true : active);

                var bar = $('#uploaderFile' + id).find('div.progress-bar');

                bar.width(percent + '%').attr('aria-valuenow', percent);
                bar.toggleClass('progress-bar-striped progress-bar-animated', active);

                if (percent === 0) {
                    bar.html('');
                } else {
                    bar.html(percent + '%');
                }

                if (color !== false) {
                    bar.removeClass('bg-success bg-info bg-warning bg-danger');
                    bar.addClass('bg-' + color);
                }
            }
            
            // change edit link
            function ui_multi_update_edit_link(id, post_id) {
                $('#uploaderFile' + id).find('a').prop('href', 'post-edit.php?id=' + post_id);
            }
        </script>
        <!-- File item template -->
        <script type="text/html" id="files-template">
            <li class="media">
                <div class="media-body mb-1">
                    <p class="mb-2">
                        <strong>%%filename%%</strong> - Status: <span class="text-muted">Waiting</span>
                        <a href="" class="pull-right" target="_blank">Edit</a>
                    </p>
                    <div class="progress mb-2" style="margin-bottom: 0px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                             role="progressbar"
                             style="width: 0%" 
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <hr class="mt-1 mb-1" />
                </div>
            </li>
        </script>

        <!-- Debug item template -->
        <script type="text/html" id="debug-template">
            <li class="list-group-item text-##color##"><strong>%%date%%</strong>: %%message%%</li>
        </script>
    </body>
</html>