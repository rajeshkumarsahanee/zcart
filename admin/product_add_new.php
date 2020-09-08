<form action="" method="post" enctype="multipart/form-data">
    <?php echo $savemsg; ?>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#general" data-toggle="tab">GENERAL</a></li>
            <li><a href="#data" data-toggle="tab">DATA</a></li>
            <li><a href="#links" data-toggle="tab">LINKS</a></li>
            <li><a href="#seo" data-toggle="tab">SEO</a></li>
            <li><a href="#specifications" data-toggle="tab">SPECIFICATIONS</a></li>
            <li><a href="#option" data-toggle="tab">OPTION</a></li>
            <li><a href="#qty_discount" data-toggle="tab">QTY DISCOUNT</a></li>
            <li><a href="#special_discount" data-toggle="tab">SPECIAL DISCOUNT</a></li>
            <li><a href="#downloads" data-toggle="tab">DOWNLOADS</a></li>                            
        </ul>
        <div class="tab-content">                            
            <div class="tab-pane active" id="general">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" class="form-control" required>
                                <?php foreach ($PRODUCT_TYPES as $key => $value) { ?>
                                    <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <label>Shop*</label>
                            <input id="shop_name" type="text" class="form-control" name="shop_name" autocomplete="off" required/>
                            <input id="shop_id" type="hidden" class="form-control" name="shop_id" required/>
                            <ul id="shopslist" class="nav custom-nav">
                               
                            </ul>
                        </div>
                    </div>                                    
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Sku*</label>
                            <input type="text" class="form-control" name="sku" required/>
                        </div>
                        <!-- /.form-group -->                                        
                    </div>
                    <!-- /.col -->
                    <div class="col-md-9">
                        <div class="form-group">
                            <label>Name*</label>
                            <input type="text" class="form-control" name="name" required/>
                        </div>
                        <!-- /.form-group -->                                        
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Slug*</label>
                            <input type="text" class="form-control" name="slug" required/>
                            Do not use spaces, instead replace spaces with - and make sure the keyword is globally unique.

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Price*</label>
                            <input type="text" class="form-control" name="price" required/>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Quantity/Stock*</label>
                            <input type="number" class="form-control" name="stock" required/>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Minimum Quantity*</label>
                            <input type="number" class="form-control" name="min_order_qty" required/>
                            <span style="font-size: 12px">Force minimum ordered qty.</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>HSN Code*</label>
                            <input id="hsn_code" type="text" class="form-control" name="hsn_code" required/>
                            <input id="hsn_code_hidden" type="hidden" class="form-control"/>
                            <ul id="hsncodelist" class="nav custom-nav">
                               
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tax Code*</label>
                            <select class="form-control" name="tax_code" required>
                                <option value="">-Select Code-</option>
                                <?php foreach($TAX_PERCENT as $key => $value) { ?>
                                <option value="<?= $key ?>"><?= $key ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Brand*</label>
                            <input id="brand" type="text" class="form-control" name="brand" autocomplete="off" required/>
                            <ul id="brandslist" class="nav custom-nav">
                               
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Category*</label>
                            <select id="rootcategories" name="categories[]" class="form-control" required>
                                <option value="">-Select Category-</option>
                                <?php foreach ($categories as $category) { ?>
                                    <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                <?php } ?>
                            </select>
                            <div id="subcategories">
                                
                            </div>
                        </div>
                        <!-- /.form-group -->                                        
                    </div>
                    <!-- /.col -->                                                          
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Model*</label>
                            <input type="text" class="form-control" name="model" required/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Condition</label>
                            <select name="product_condition" class="form-control">
                                <?php foreach ($CONDITIONS as $key => $value) { ?>
                                    <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                <?php } ?>
                            </select>                                            
                        </div>
                        <!-- /.form-group -->                                        
                    </div>
                    <!-- /.col -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <?php foreach ($PRODUCT_STATUSES as $key => $value) { ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php } ?>
                            </select> 
                        </div>
                        <!-- /.form-group -->                                        
                    </div>                                    
                </div>
                <div class="row">
                    <div class="col-md-12">                                                
                        <!-- /.form-group -->                                        
                        <div class="form-group">
                            <label>Images (Click on upload icon to upload or link icon to fetch image from link)</label>
                            <div class="pull-right">
                                <span id="uploadingspanmsg"></span>
                                <label class="btn-flat btn btn-sm btn-default" title="Image Link" id="imgaddlink"><i class="fa fa-link"></i></label>
                                <label class="btn-flat btn btn-sm btn-primary" title="Upload"><i class="fa fa-upload"></i><input id="imguploadinput" type="file"></label>
                            </div>                                                
                            <ul class="nav navbar-nav sortable" id="thumbnails">
                                
                            </ul>
                        </div>
                        <!-- /.form-group -->                                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">                                        
                        <div class="form-group">
                            <label>Short Description</label>                                                
                            <textarea name="short_description" class="form-control" maxlength="250" rows="2"></textarea>
                        </div>
                        <!-- /.form-group -->                                        
                        <div class="form-group">
                            <label>Long Description</label>
                            <textarea id="long_description" name="long_description" class="form-control" rows="4"></textarea>
                        </div>
                        <!-- /.form-group -->                                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">                                        
                        <div class="form-group">
                            <label>Tags</label>
                            <input type="text" id="tag" class="form-control"/>
                            <ul class="nav custom-nav" id="tags">
                                
                            </ul>
                            <ul class="nav navbar-nav" id="selected-tags">
                                
                            </ul>
                        </div>                                                                                                                       
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Requires Shipping</label>                                                
                            <select class="form-control" name="requires_shipping">
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>                                                                                                                       
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Shipping Country</label>                                                
                            <select class="form-control select2" name="shipping_country">
                                <option value=""></option>
                                <?php foreach ($countries as $c) { ?>
                                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>                                                                                                                       
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Free Shipping</label><br/>                                   
                            <input type="checkbox" name="ship_free"/>
                            Shipping Prices will not be considered for any location for ship free products.
                        </div>                                                                                                                       
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table id="shippings" class="table">
                            <thead>
                                <tr>
                                    <th>SHIPS TO</th>
                                    <th>SHIPPING COMPANY</th>
                                    <th>PROCESSING TIME</th>
                                    <th>COST[&#8377;]</th>
                                    <th>EACH ADDITIONAL ITEM [&#8377;]</th>
                                </tr>
                            </thead>    
                            <tbody id="shipping_tbody">

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td> 
                                    <td></td> 
                                    <td><a class="btn btn-primary btn-sm pull-right" href="javascript:addShippingRow()"><i class="fa fa-plus"></i></a></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>                                    
                </div>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="data">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Subtract Stock</label>                                                
                            <select class="form-control" name="substract_stock">
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Track Inventory</label>
                            <select class="form-control" name="track_inventory">
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>
                    </div>                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Alert Stock Level</label>                                                
                            <input type="number" class="form-control" name="alert_stock_level"/>
                            Note: You will receive email notification when product stock qty is below or equal to threshold level and Inventory tracking is enabled.
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>In Stock</label>
                            <select class="form-control" name="in_stock">
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group">
                            <label>Youtube Video Link</label>                                                
                            <input type="text" class="form-control" name="youtube_video"/>
                            Please enter the youtube video URL here.
                        </div>
                    </div>                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date Available</label>                                                
                            <input type="text" class="form-control date" name="date_available"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Length Class</label>                                                
                            <select class="form-control" name="length_class">
                                <option value="CM">Centimeter</option>
                                <option value="MM">Millimeter</option>
                                <option value="IN">Inch</option>
                            </select>    
                        </div>
                    </div> 
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Dimensions (L x W x H)</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="length" placeholder="Length"/>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="width" placeholder="Width"/>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="height" placeholder="Height"/>
                                </div>
                            </div>                            
                        </div>                        
                    </div>
                </div>
                <div class="row">                                       
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Weight Class</label>                                                
                            <select class="form-control" name="weight_class">
                                <option value="KG">Kilogram</option>
                                <option value="GM">Grams</option>
                                <option value="PD">Pound</option>
                                <option value="OU">Ounce</option>
                                <option value="LT">Litres</option>
                                <option value="ML">Milli Litre</option>
                            </select>                            
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Weight</label>                                                
                            <input type="text" class="form-control" name="weight"/>
                        </div>
                    </div>
                </div>
                <div class="row">                                       
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Display Order</label>                                                
                            <input type="number" class="form-control" name="display_order"/>                          
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Featured Product</label><br/>
                            <input type="checkbox" class="" name="featured_product" value="Y"/>
                            Featured Products will be listed on Featured Products Page.
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Enable COD</label><br/>                                            
                            <input type="checkbox" class="" name="enable_cod"/>                            
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="links">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="filters">Filters</label>
                            <input type="text" id="filter" class="form-control"/>
                            <ul class="nav custom-nav" id="filters">
                                
                            </ul>
                            <ul class="nav" id="selected-filters">
                                
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Related Products</label>
                            <input type="text" id="related-product" class="form-control"/>
                            <ul class="nav custom-nav" id="related-products">
                                
                            </ul>
                            <ul class="nav" id="selected-related-products">
                                
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="seo">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Meta Title</label>
                            <input type="text" class="form-control" name="meta_title"/>
                        </div>
                        <div class="form-group">
                            <label>Meta Keywords</label>                                                
                            <textarea name="meta_keywords" class="form-control" maxlength="250" rows="2"></textarea>
                        </div>
                        <!-- /.form-group -->                                        
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3"></textarea>
                        </div>
                        <!-- /.form-group -->                                        
                    </div>
                </div>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="specifications">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Specification</th>
                                        <th style="width: 40%;">Value</th>
                                        <th style="width: 20%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="specifications_tbody">

                                </tbody>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align: right;"><a href="javascript:addSpecificationsRow()" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></a></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="option">
                <div class="row">
                    <div class="col-md-3">                        
                        <table class="table">
                            <tbody id="option_container_tbody">
                                
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">
                                        <select id="product_option" onchange="addOption()" class="form-control select2" style="width: 100%; border-radius: 0px;" placeholder="Option">
                                            <option value=""></option>
                                            <?php
                                            $choose_options = $input_options = $date_options = $file_options = "";
                                            $option_values = array();
                                            foreach ($options as $productOption) {                                                                                                
                                                if($productOption['type'] == "Select/Listbox/Dropdown" || $productOption['type'] == "Radio" || $productOption['type'] == "Checkbox") {
                                                    $option_value = '<select id="option_values'.$productOption['id'].'" style="display:none;">';
                                                    foreach($productOption['values'] as $value) {
                                                        $option_value .= '<option value="' . $value['option_value'] . '">' . $value['option_value'] . '</option>';
                                                    }
                                                    $option_value .= '</select>';
                                                    $option_values[]  = $option_value;    
                                                    $choose_options .= '<option value="' . $productOption['id'] . '" option_type="' . $productOption['type'] . '">' . $productOption['name'] . '</option>';
                                                }
                                                if($productOption['type'] == "Textarea" || $productOption['type'] == "Text") {
                                                    $input_options .= '<option value="' . $productOption['id'] . '" option_type="' . $productOption['type'] . '">' . $productOption['name'] . '</option>';
                                                }
                                                if($productOption['type'] == "Date &amp; Time" || $productOption['type'] == "Date" || $productOption['type'] == "Time") {
                                                    $date_options .= '<option value="' . $productOption['id'] . '" option_type="' . $productOption['type'] . '">' . $productOption['name'] . '</option>';
                                                }
                                                if($productOption['type'] == "File") {
                                                    $file_options .= '<option value="' . $productOption['id'] . '" option_type="' . $productOption['type'] . '">' . $productOption['name'] . '</option>';
                                                }
                                            }
                                            ?>                                            
                                            <optgroup label="Choose">
                                                <?= $choose_options ?>                                                
                                            </optgroup>
                                            <optgroup label="Input">                                                                                               
                                                <?= $input_options ?>
                                            </optgroup>
                                            <optgroup label="Date">
                                                <?= $date_options ?>                                                
                                            </optgroup>
                                            <optgroup label="File">
                                                <?= $file_options ?>                                                
                                            </optgroup>                                            
                                        </select>                                        
                                        <?php 
                                        foreach($option_values as $option_value) {
                                            echo $option_value . "<br/>";
                                        }
                                        ?>
                                    </td>
                                </tr>                                
                            </tfoot>
                        </table>                        
                    </div>
                    <div class="col-md-9">
                        <div id="option_value_container" class="">
                            <table>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>                        
                    </div>
                </div>                
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="qty_discount">
                <div class="col-md-12">                    
                    <table id="qty_discount_table" class="table">
                        <thead>
                            <tr>
                                <th>QUANTITY</th>
                                <th>PRIORITY</th>
                                <th>DISCOUNTED PRICE [&#8377;]</th>
                                <th>DATE START</th>
                                <th>DATE END</th>
                            </tr>
                        </thead>    
                        <tbody id="qty_discount_tbody">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td> 
                                <td></td> 
                                <td><a class="btn btn-primary btn-sm pull-right" href="javascript:addQtyDiscount()"><i class="fa fa-plus"></i></a></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>                                    
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="special_discount">
                <div class="col-md-12">                    
                    <table id="special_discount_table" class="table">
                        <thead>
                            <tr>                                
                                <th>PRIORITY</th>
                                <th>SPECIAL PRICE [&#8377;]</th>
                                <th>DATE START</th>
                                <th>DATE END</th>
                            </tr>
                        </thead>    
                        <tbody id="special_discount_tbody">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td> 
                                <td></td> 
                                <td><a class="btn btn-primary btn-sm pull-right" href="javascript:addSpecialDiscount()"><i class="fa fa-plus"></i></a></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>                                    
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="downloads">
                <div class="col-md-12">                    
                    <table id="downloads_table" class="table">
                        <thead>
                            <tr>                                
                                <th>Download Name</th>
                                <th>File Name</th>
                                <th>Max Download Times</th>
                                <th>Validity (days)</th>
                            </tr>
                        </thead>    
                        <tbody id="downloads_tbody">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td> 
                                <td></td> 
                                <td><a class="btn btn-primary btn-sm pull-right" href="javascript:addDownload()"><i class="fa fa-plus"></i></a></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>                                    
            </div>
            <!-- /.tab-pane -->
            <hr/>
            <input type="submit" class="btn btn-success" name="save" value="Save"/>                            
        </div>
        <!-- /.tab-content -->
    </div>                    
</form>