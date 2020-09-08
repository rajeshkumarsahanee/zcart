<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo $category['name'] ?> Information</h3>
        <div class="box-tools pull-right">
            <input id="fetchfromurl" placeholder="Fetch from url" type="text"/>
            <i class="fa fa-spinner fa-spin" id="loading" style="display: none;"></i>
            <button onclick="fetchFromUrl()">Fetch</button>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>"/>
        <input type="hidden" name="category" value="<?php echo $product['category_id']; ?>"/>
        <!-- /.box-header -->
        <div class="box-body">
            <?php echo $savevariantmsg; ?>
            <div class="row">
                <div class="col-md-12" id="parseddata">

                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Categories</label>
                        <select name="categories[]" class="form-control select2" multiple="multiple">
                            <?php foreach($categories as $category) { ?>
                            <option value="<?php echo $category['id'] ?>" <?php if(in_array($category['id'], $productcategoryids)) { echo "selected"; } ?>><?php echo $category['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Sku</label>
                        <input id="skuhidden" type="hidden" class="form-control" value="<?php echo $product['sku']; ?>"/>
                        <input id="sku" type="text" class="form-control" name="sku" value="<?php echo $product['sku']; ?>" readonly required/>
                    </div>
                    <!-- /.form-group -->                                        
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Variant Sku</label>
                        <input id="variant_sku" type="text" class="form-control" name="variant_sku" value="<?php if(isset($_POST['variant_sku'])) { echo $_POST['variant_sku']; } ?>" required/>
                    </div>
                    <!-- /.form-group -->                                        
                </div>
                <!-- /.col -->
                <div class="col-md-7">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" value="<?php echo $product['name']; ?>" required/>
                    </div>
                    <!-- /.form-group -->                                        
                </div>
                <!-- /.col -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" class="form-control" name="slug" value="<?php echo $product['slug']; ?>" required/>
                    </div>
                    <div class="form-group">
                        <label>Short Description</label>                                                
                        <textarea name="short_description" class="form-control" maxlength="250" rows="3"><?php echo $product['short_description']; ?></textarea>
                    </div>
                    <!-- /.form-group -->                                        
                    <div class="form-group">
                        <label>Long Description</label>
                        <textarea id="long_description" name="long_description" class="form-control" rows="4"><?php echo $product['long_description']; ?></textarea>
                    </div>
                    <!-- /.form-group -->                                        
                </div>
                <!-- /.col -->
                <div class="col-md-12">                                                
                    <!-- /.form-group -->                                        
                    <div class="form-group">
                        <label>Images(comma or new line separated multiple images)</label>
                        <div class="pull-right">
                            <span id="uploadingspanmsg"></span>
                            <label href="" target="_blank" class="btn-flat btn btn-sm btn-primary" title="Upload">
                                <i class="fa fa-upload"></i><input id="imguploadinput" style="width: 40px;height: 40px;position: absolute;top: -8px;right: 21px;z-index: -1;" class="btn" type="file">
                            </label>
                        </div>
                        <textarea id="imagesta" name="images" class="form-control"><?php echo str_replace(",", "\n", $product['images']); ?></textarea>
                    </div>
                    <!-- /.form-group -->                                        
                </div>
                <!-- /.col -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" class="form-control" name="meta_title" value="<?php echo $product['meta_title']; ?>"/>
                    </div>
                    <div class="form-group">
                        <label>Meta Keywords</label>                                                
                        <textarea name="meta_keywords" class="form-control" maxlength="250" rows="2"><?php echo $product['meta_keywords']; ?></textarea>
                    </div>
                    <!-- /.form-group -->                                        
                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="3"><?php echo $product['meta_description']; ?></textarea>
                    </div>
                    <!-- /.form-group -->                                        
                </div>
                <div class="col-md-3">                                                
                    <!-- /.form-group -->                                        
                    <div class="form-group">
                        <label>Special</label>
                        <select name="special" class="form-control">
                            <option value="0" <?php if($product['special'] == 0) { echo "selected"; } ?>>No</option>
                            <option value="1" <?php if($product['special'] == 1) { echo "selected"; } ?>>Yes</option>
                        </select>
                    </div>
                    <!-- /.form-group -->                                        
                </div>
                <!-- /.col -->
                <div class="col-md-3">                                                
                    <!-- /.form-group -->                                        
                    <div class="form-group">
                        <label>Latest</label>
                        <select name="latest" class="form-control">
                            <option value="0" <?php if($product['latest'] == 0) { echo "selected"; } ?>>No</option>
                            <option value="1" <?php if($product['latest'] == 1) { echo "selected"; } ?>>Yes</option>
                        </select>
                    </div>
                    <!-- /.form-group -->                                        
                </div>
                <!-- /.col -->
                <div class="col-md-3">                                                
                    <!-- /.form-group -->                                        
                    <div class="form-group">
                        <label>Views</label>
                        <input type="number" min="0" name="views" value="<?php echo $product['views']; ?>" class="form-control"/>
                    </div>
                    <!-- /.form-group -->                                        
                </div>
                <!-- /.col -->
                <div class="col-md-3">                                                
                    <!-- /.form-group -->                                        
                    <div class="form-group">
                        <label>Likes</label>
                        <input type="number" min="0" name="likes" value="<?php echo $product['likes']; ?>" class="form-control"/>
                    </div>
                    <!-- /.form-group -->                                        
                </div>
                <div class="col-md-12">
                    <div class="box box-solid box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Attributes</h3>
                            <div class="box-tools">
                                <input type="text" style="padding-left: 5px;" id="searchattribute" placeholder="Search Attribute..."/>
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body no-padding" style="display: block; padding-top: 10px !important;">
                            <?php
                            foreach ($attributes as $attribute) {
                                //echo $attribute->getType()." ".$attribute->getName()." " . $attribute->getTermsString()."<br/>";
                                ?>                                            
                                <div class="col-md-4 attribute">
                                    <div class="form-group">
                                        <label><?php echo $attribute['name']; ?></label>
                                        <?php if ($attribute['type'] == 'Select') { ?>
                                            <select class="form-control select2" name="<?php echo $attribute['id']; ?>" style="width: 100%;">
                                                <?php foreach ($attribute['termsR'] as $term) { ?>
                                                    <option value="<?php echo $term; ?>" <?php if($product['attributes'][$attribute['id']]['value'] == $term) { echo "selected"; } ?>><?php echo $term; ?></option>
                                                <?php } ?>
                                            </select>
                                        <?php } else { ?>
                                            <input type="text" class="form-control" name="<?php echo $attribute['id']; ?>" value="<?php echo $product['attributes'][$attribute['id']]['value']; ?>"/>
                                        <?php } ?>
                                    </div>                                                
                                    <!-- /.form-group -->
                                </div>
                                <!-- /.col -->
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!-- /.col -->                
                <div class="col-md-12">                                            
                    <div class="box box-solid box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Price Calculation</h3>
                            <div class="box-tools">
                                <label>In Stock: </label>
                                <input type="radio" class="" name="pin_stock" value="Y" <?php if(isset($defaultseller['in_stock']) && $defaultseller['in_stock'] == 'Y') { echo 'checked'; } ?> required/> Yes
                                <input type="radio" class="" name="pin_stock" value="N" <?php if(isset($defaultseller['in_stock']) && $defaultseller['in_stock'] == 'N') { echo 'checked'; } ?> required/> No
                            </div>                                                        
                        </div>
                        <div class="box-body no-padding" style="display: block; padding-top: 10px !important;">
                            <input type="hidden" name="defaultsellerid" value="<?php echo $defaultseller['id'] ?>"/>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Price</label>
                                    <input id="pprice" type="text" class="form-control" name="pprice" value="<?php if(isset($defaultseller['price'])) { echo $defaultseller['price']; } ?>"/>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Shipping</label>
                                    <input id="pshipping" type="text" class="form-control" name="pshipping" value="<?php if(isset($defaultseller['shipping'])) { echo $defaultseller['shipping']; } ?>"/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Fix Charge (<?php if(isset($sys['config']['marketPlaceFees'])) { echo $sys['config']['marketPlaceFees']; } ?>%)</label>
                                    <input type="hidden" id="fixedrate" value="<?php if(isset($sys['config']['marketPlaceFees'])) { echo $sys['config']['marketPlaceFees']; } ?>"/>
                                    <input id="pmarketplacechage" type="text" class="form-control" name="pmarketplacechage" value="<?php  if(isset($defaultseller['marketplace_fees'])) { echo $defaultseller['marketplace_fees']; } ?>"/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Tax</label>
                                    <input type="hidden" id="taxrate" value="<?php if(isset($sys['config']['taxPercent'])) { echo $sys['config']['taxPercent']; } ?>"/>
                                    <input id="ptax" type="text" class="form-control" name="ptax" value="<?php if(isset($defaultseller['tax'])) { echo $defaultseller['tax']; } ?>" title="It can be changed"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?php echo $defaultseller['name'] ?> Selling Price</label>
                                    <input id="psellingprice" type="text" class="form-control" name="psellingprice" value="<?php if(isset($defaultseller['selling_price'])) { echo $defaultseller['selling_price']; } ?>" readonly/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="checkbox" style="margin-top: -5px;" name="active_discount" value="Y" <?php if(isset($defaultseller['active_discount']) && trim($defaultseller['active_discount']) == 'Y') { echo "checked"; } ?>/>
                                    <label>                                            
                                        Discount                                             
                                    </label>
                                    <input type="number" min="0" max="100" name="percent_discount" value="<?php if(isset($defaultseller['percent_discount'])) { echo $defaultseller['percent_discount']; } ?>" class="form-control" required/>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
                <div class="col-md-12">
                    <div class="box box-solid box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Affiliates</h3>
                            <div class="box-tools">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body no-padding" style="display: block; padding-top: 10px !important;">
                            <?php foreach ($affiliates as $affiliate) { ?>                            
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label><?php echo $affiliate['name'] ?> Product Url</label>
                                        <input type="text" class="form-control" name="pau<?php echo $affiliate['id'] ?>" value="<?php echo $product['affiliates'][$affiliate['id']]['url']; ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $affiliate['name'] ?> Price</label>
                                        <input type="text" class="form-control" name="pap<?php echo $affiliate['id'] ?>" value="<?php echo $product['affiliates'][$affiliate['id']]['price']; ?>"/>
                                    </div>
                                </div>                                                                            
                            <?php } ?>
                        </div>
                    </div>                    
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <input type="hidden" name="save_variant" value="Save Variant"/>
            <input type="submit" class="btn btn-success" value="Save"/>            
        </div>
    </form>
</div>