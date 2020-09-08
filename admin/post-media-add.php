<?php $dates = getPostsDates(YEAR_MONTH, "attachment"); ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
    .media-menu-item {
        display: block;
    }
    .media-selection {
        position: absolute;
        bottom: 0px;
    }
    .selection-info {
        display: inline-block;
        vertical-align: top;
        text-align: left;
    }
    .media-selection .count {
        display: block;
        font-size: 14px;
        line-height: 20px;
        font-weight: 600;
    }
    .media-selection .edit-selection {
        float: left;
        padding: 1px 8px;
        margin: 1px 8px 1px -8px;
        line-height: 16px;
        border-right: 1px solid #ddd;
        color: #0073aa;
        text-decoration: none;
    }
    .media-selection .attachment {
        width: 40px;
    }
    .selection-view {
        display: inline-block;
    }
    .selection-view .selected img {
        width: 100%;
    }

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

    #attachments {
        height: 350px;
        overflow-y: scroll;
    }
    #attachments .ui-selecting { 
        background: #FECA40; 
    }
    #attachments .ui-selected { 
        box-shadow: inset 0 0 0 3px #fff, inset 0 0 0 7px #0073aa;
    }
    .attachments {
        list-style: none;
        position: static;                
        padding: 2px;                
        right: 0;
        margin: 0;
        margin-right: 0;
        left: 0;
        bottom: 0;
        overflow: auto;
        outline: 0;
    }
    .attachment {                
        width: 19%;
        position: relative;
        list-style: none;
        float: left;
        cursor: pointer;
        text-align: center;
        margin: 4px 8px 4px 0px;
        padding: 7px;    
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
        margin-bottom: 0px;
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
        top: 0;
        left: 0;
    }
    .attachment .landscape img {
        max-height: 100%;
    }
    .attachment .portrait img {
        max-width: 100%;
    }
    #media-info {
        height: 390px;
        overflow-y: scroll;
        margin-left: -15px;
        padding-right: 10px;
    }
    .attachment-info .thumbnail {
        position: relative;
        float: left;
        max-width: 120px;
        max-height: 120px;
        margin-top: 5px;
        margin-right: 10px;
        margin-bottom: 5px;
        padding: 0px;
        border-radius: 0px;
    }
    .attachment-details {
        
    }
    .attachment-display-settings {
        display: table;
        width: 100%;
        margin-bottom: 10px;
    }
    .attachment-details h2, .attachment-display-settings h2 {
        text-transform: uppercase;
        font-size: 15px;
        font-weight: bold;
        color: #374850;
    }
    .control-label {
        padding: 0px;
    }
    
    @media only screen and (max-width: 1000px) {

        .media-toolbar-primary, .media-toolbar-secondary {
            float: left;
            position: relative;
            max-width: 100%;
        }

        .attachment {                
            width: 23%;
        }                                
    }  

    @media only screen and (max-width: 782px) {

        .media-toolbar-primary, .media-toolbar-secondary {
            float: left;
            position: relative;
            max-width: 100%;
        }

        .attachment {                
            width: 47%;
        }                
    }

</style>
<div id="add-media-modal" class="modal">
    <div class="modal-dialog modal-lg" style="width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">?</span></button>
                <h4 class="modal-title">Add Media</h4>
            </div>
            <div class="modal-body" style="padding: 12px 6px 0px 6px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="nav-tabs-custom" style="margin-bottom: 0px;">
                            <ul class="nav nav-tabs">
                                <li><a href="#upload" data-toggle="tab">Upload Files</a></li>
                                <li class="active"><a href="#library" data-toggle="tab">Media Library</a></li>
                            </ul>
                            <div class="tab-content" style="padding-bottom: 0px;">
                                <div class="tab-pane" id="upload">

                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane active" id="library">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-6">
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
                                                        <?php
                                                        foreach ($dates as $d) {
                                                            $timestamp = strtotime($d . "01");
                                                            ?>
                                                            <option value="<?= date("Y-m", $timestamp) ?>"><?= date("F Y", $timestamp) ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="search" placeholder="Search media items..." id="media-search-input" class="form-control search">
                                                </div>
                                            </div>
                                            <ul tabindex="-1" class="attachments selectable" id="attachments">

                                            </ul>
                                        </div>
                                        <div class="col-md-3">
                                            <div id="media-info" class="media-info" style="display: none;">
                                                <div class="attachment-details">
                                                    <h2>Attachment Details</h2>
                                                    <div class="attachment-info">
                                                        <div class="thumbnail thumbnail-image">
                                                            <img src="" draggable="false" alt="">
                                                        </div>
                                                        <div class="details">
                                                            <div class="filename"></div>
                                                            <div class="uploaded"></div>
                                                            <div class="file-size"></div>
                                                            <div class="dimensions"></div>
                                                            <a class="edit-attachment" href="" target="_blank">Edit Image</a><br/>
                                                            <a class="text-danger block">Delete Permanently</a>
                                                            <div class="compat-meta">

                                                            </div>
                                                        </div>
                                                        <br style="clear:both;">
                                                        <hr style="clear:both;margin-bottom:10px">
                                                        <div class="w3eden media-access-control-container">
                                                            <div id="wpdm-media-access">
                                                                <div class="panel panel-default" id="__protm">
                                                                    <div class="panel-body">This file is not protected.</div>
                                                                    <div class="panel-footer">
                                                                        <button class="btn btn-success btn-block" onclick="jQuery('#__protm').slideUp();jQuery('#__prots').slideDown();">Protect this file</button>
                                                                    </div>
                                                                </div>
                                                                <div id="__prots" class="panel panel-default" style="display: none">
                                                                    <div class="panel-footer">
                                                                        <button class="btn btn-block btn-primary btn-sm" id="__makeprivate" data-id="1823">Block Direct Access</button>
                                                                    </div>
                                                                    <div class="panel-footer">
                                                                        <button class="btn btn-block btn-danger btn-sm" id="__makepublic" data-id="1823">Remove Protection</button>
                                                                    </div>
                                                                </div>
                                                                <style>
                                                                    #acx{
                                                                        height: 150px;overflow: auto;
                                                                    }
                                                                    #acx input[type=checkbox]{
                                                                        transform: scale(0.7);
                                                                        margin-top: -1px;
                                                                    }
                                                                    #acx label{
                                                                        font-weight: 400;
                                                                        padding: 0 15px;
                                                                        line-height: 18px;
                                                                        font-size: 10px;
                                                                        display: block;
                                                                        width: 100%;
                                                                    }
                                                                </style>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group" data-setting="alt">
                                                        <label for="attachment-details-alt-text" class="col-sm-3 control-label">Alt Text</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="attachment-details-alt-text" class="form-control" aria-describedby="alt-text-description">
                                                        </div>
                                                    </div>
                                                    <div class="form-group" data-setting="alt">
                                                        <div class="col-sm-offset-3 col-sm-9">
                                                            <a href="https://www.w3.org/WAI/tutorials/images/decision-tree" target="_blank" rel="noopener noreferrer">Describe the purpose of the image<span class="screen-reader-text"> (opens in a new tab)</span></a>. Leave empty if the image is purely decorative.
                                                        </div>
                                                    </div>
                                                    <div class="form-group" data-setting="title">
                                                        <label for="attachment-details-title" class="col-sm-3 control-label">Title</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="attachment-details-title" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="form-group" data-setting="caption">
                                                        <label for="attachment-details-caption" class="col-sm-3 control-label">Caption</label>
                                                        <div class="col-sm-9">
                                                            <textarea id="attachment-details-caption" class="form-control"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" data-setting="description">
                                                        <label for="attachment-details-description" class="col-sm-3 control-label">Description</label>
                                                        <div class="col-sm-9">
                                                            <textarea id="attachment-details-description" class="form-control"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" data-setting="url">
                                                        <label for="attachment-details-copy-link" class="col-sm-3 control-label">Copy Link</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="attachment-details-copy-link" class="form-control" readonly=""/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <form class="compat-item">
                                                    <input type="hidden" name="attachments[1823][menu_order]" value="0">
                                                    <p class="media-types media-types-required-info">Required fields are marked <span class="required">*</span></p>
                                                    <table class="compat-attachment-fields">		
                                                        <tbody>
                                                            <tr class="compat-field-mac">			
                                                                <th scope="row" class="label">
                                                                    <label for="attachments-1823-mac"><span class="alignleft"></span><br class="clear"></label>
                                                                </th>
                                                                <td class="field">
                                                                    
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </form>
                                                <div class="attachment-display-settings">
                                                    <h2>Attachment Display Settings</h2>
                                                    <div class="form-group">
                                                        <label for="attachment-display-settings-alignment" class="col-sm-3 control-label">Alignment</label>
                                                        <div class="col-sm-9">
                                                            <select id="attachment-display-settings-alignment" class="form-control" data-setting="align" data-user-setting="align">
                                                                <option value="left">Left</option>
                                                                <option value="center">Center</option>
                                                                <option value="right">Right</option>
                                                                <option value="none" selected="">None</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="attachment-display-settings-link-to" class="col-sm-3 control-label">Link To</label>
                                                        <div class="col-sm-9">
                                                            <select id="attachment-display-settings-link-to" class="form-control" data-setting="link" data-user-setting="urlbutton">
                                                                <option value="none" selected="">None</option>
                                                                <option value="file">Media File</option>
                                                                <option value="post">Attachment Page</option>
                                                                <option value="custom">Custom URL</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="display: none;">
                                                        <label for="attachment-display-settings-link-to-custom" class="col-sm-3 control-label">URL</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="attachment-display-settings-link-to-custom" class="form-control" data-setting="linkUrl">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="attachment-display-settings-size" class="col-sm-3 control-label">Size</label>
                                                        <div class="col-sm-9">
                                                            <select id="attachment-display-settings-size" class="form-control" name="size" data-setting="size" data-user-setting="imgsize">
                                                                
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                            </div>
                            <!-- /.tab-content -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="media-selection pull-left">
                    <div class="selection-info">
                        <span class="count"></span>
                        <a type="button" class="edit-selection">Edit Selection</a>
                        <a type="button" class="text-danger clear-selection">Clear</a>
                    </div>
                    <div class="selection-view">
                        <ul tabindex="-1" class="attachments">
                            <li tabindex="0" role="checkbox" aria-label="" aria-checked="true" data-id="1428" class="attachment selected save-ready">
                                <div class="attachment-preview landscape">
                                    <div class="thumbnail">
                                        <div class="centered">
                                            <img src="https://zatackcoder.com/wp-content/uploads/2019/06/credit-debit-balance-300x63.png" draggable="false" alt="">
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li tabindex="0" role="checkbox" aria-label="" aria-checked="true" data-id="1421" class="attachment selected save-ready">
                                <div class="attachment-preview landscape">
                                    <div class="thumbnail">
                                        <div class="centered">
                                            <img src="https://zatackcoder.com/wp-content/uploads/2019/06/yellow-car-square-banner-e1530804982926-1-300x207.jpg" draggable="false" alt="">
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <button id="insert-into-post" type="button" class="btn btn-primary">Insert Into Post</button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    var paged = 1;
    var max_pages = 1;

    $("#media-attachment-filters, #media-attachment-date-filters").on("change", function () {
        paged = 1;
        $('#attachments').html("");
        loadmore();
    });
    $("#media-search-input").on("keyup", function () {
        paged = 1;
        $('#attachments').html("");
        loadmore();
    });
    $(".selectable").selectable({
        selected: function(event, ui) {
            showHideMediaInfoAndSelection()
        },
        unselected: function(event, ui) {
            showHideMediaInfoAndSelection()
        }
    });
    
    function showHideMediaInfoAndSelection() {
        $("#media-info").hide();
        $(".media-selection").hide();
        if($(".ui-selected").length > 0) {
            $("#media-info").show();
            $(".media-selection").show();
            $(".media-selection .count").html($(".ui-selected").length + " selected");
            $(".selection-view .attachments").html("");
            $(".ui-selected").each(function() {
                var template = $('#files-template').text();
                template = template.replace('%%filepath%%', $(this).find("img").attr("src"));
                template = $(template);
                template.addClass("selected");
                $(".selection-view .attachments").append(template);
            });
        }
    }
    
    function loadmore() {
        if (paged > max_pages) {
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
                    for (var i = 0; i < response.items.length; i++) {
                        var template = $('#files-template').text();
                        var ext = response.items[i].file_path.split('.').pop().toLowerCase();
                        var file_path = "dist/img/unknown.png";
                        switch (ext) {
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
                        //template.prop('title', response.items[i].file_name);
                        template.prop('data-id', response.items[i].post_id);

                        var width = response.items[i].width;
                        var height = response.items[i].height;
                        if (width && height && width < height) {
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

    $("#attachments").scroll(function (e) {
        var attachments = document.querySelector('#attachments');
        if (attachments.scrollTop + attachments.clientHeight >= attachments.scrollHeight) {
            loadmore();
        }
    });

    $("#attachments").on("click", "li", function () {
        var post_id = $(this).prop("data-id");
        $.ajax({
            type: "POST",
            url: "<?= $sys['site_url'] ?>/requests.php?action=media-info&post_id=" + post_id,
            data: null,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.msg === "success") {
                    var data = response.data;
                    var ext = data.file_path.split('.').pop().toLowerCase();
                    var file_path = "dist/img/unknown.png";
                    switch (ext) {
                        case "jpeg":
                        case "jpg":
                        case "png":
                        case "gif":
                            file_path = data.file_path;
                            $("#attachment-details-copy-link").val(file_path);
                            var sizes = data.metadata.sizes;
                            var file_root = "<?= $sys['site_url'] . "/" . $sys['upload_root'] ?>/";
                            var sizeshtml = '<option value="' + file_root + sizes['thumbnail'].file + '">Thumbnail - ' + sizes['thumbnail'].width + ' x ' + sizes['thumbnail'].height + '</option>'
                                    + '<option value="' + file_root + sizes['medium'].file + '">Medium - ' + sizes['medium'].width + ' x ' + sizes['medium'].height + '</option>'
                                    + '<option value="' + file_root + sizes['large'].file + '">Large - ' + sizes['large'].width + ' x ' + sizes['large'].height + '</option>';
                            $("#attachment-display-settings-size").html(sizeshtml);
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
                    $("#media-info img").attr("src", file_path);
                    $("#media-info .filename").html(data.file_name);
                    $("#media-info .uploaded").html(data.uploaded);
                    $("#media-info .file-size").html(formatBytes(data.file_size));
                    if(data.width && data.height) {
                        $("#media-info .dimensions").html(data.width + " by " + data.height + " pixels");
                    }
                    $("#media-info .edit-attachment").attr("href", "post-edit.php?id=" + data.post_id);
                } else {
                    alert(response.msg);
                }
            }
        });
    });
    
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
    
    $("#attachment-display-settings-link-to").change(function(e){
        if($(this).val() === "custom") {
            $("#attachment-display-settings-link-to-custom").closest(".form-group").show();
        } else {
            $("#attachment-display-settings-link-to-custom").closest(".form-group").hide();
        }
    });
    
    $("#insert-into-post").click(function(e){
        var caption = $("#attachment-details-caption").val().trim();
        var alt_txt = '', title_txt = '';
        if($("#attachment-details-alt-text").val().trim() !== "") {
            alt_txt = 'alt="' + $("#attachment-details-alt-text").val().trim() + '"';
        }
        if($("#attachment-details-title").val().trim() !== "") {
            title_txt = 'title="' + $("#attachment-details-title").val().trim() + '"';
        }
        var img_src = $("#attachment-display-settings-size").val();
        var align = $("#attachment-display-settings-alignment").val();
        var html = '<img src="' + img_src + '" class="" align="' + align + '" ' + alt_txt + ' ' + title_txt + '/>';
        if(caption !== '') {
            html = '<figure id="" style="" align="' + align + '">'
                    + '<img src="' + img_src + '" class="" ' + alt_txt + ' ' + title_txt + '>'
                    + '<figcaption id="" class="">' + caption + '</figcaption>'
                    + '</figure>';
        }
        var link_to = $("#attachment-display-settings-link-to").val();
        switch(link_to) {
            case "file" : 
                html = '<a href="' + img_src + '">' + html + '</a>'
                break;
            case "post" : 
                html = '<a href="' + $("#sample-permalink").text() + '">' + html + '</a>'
                break;
            case "custom" : 
                html = '<a href="' + $("#attachment-display-settings-link-to-custom").val() + '">' + html + '</a>'
                break;
        }
        if(insertContent(html)) {
            $("#add-media-modal").modal("hide");
        }
    });
</script>
<script type="text/html" id="files-template">
    <li tabindex="0" role="checkbox" aria-label="" aria-checked="false" data-id="" class="attachment">
        <div class="attachment-preview">
            <div class="thumbnail">
                <div class="centered">
                    <img src="%%filepath%%" draggable="false" alt=""/>
                </div>
            </div>
        </div>                            
    </li>
</script>
