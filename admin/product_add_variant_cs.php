<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo $category['name'] ?> Size/Color Variant</h3>
        <div class="box-tools pull-right">                        
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>"/>
        <input type="hidden" name="category" value="<?php echo $product['category_id']; ?>"/>
        <!-- /.box-header -->
        <div class="box-body">
            <?php echo $savevariantcsmsg; ?>
            <div class="row">                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Sku</label>
                        <input type="text" class="form-control" name="sku" value="<?php echo $product['sku']; ?>" readonly required/>
                    </div>
                    <!-- /.form-group -->                                        
                </div>
                <!-- /.col -->
                <div class="col-md-9">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" value="<?php echo $product['name']; ?>" readonly required/>
                    </div>
                    <!-- /.form-group -->                                        
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Color</label> (Put <span style="color:red;font-weight: bold;">NA</span> for no color variant)
                        <input type="text" class="form-control" name="color" required/>
                    </div>
                    <div class="form-group">
                        <label>Size</label> (Put <span style="color:red;font-weight: bold;">NA</span> for no size variant)
                        <input type="text" class="form-control" name="size" required/>
                    </div>
                </div>                         
                <div class="col-md-9">                                                
                    <!-- /.form-group -->                                        
                    <div class="form-group">
                        <label>Images for color variant (comma or new line separated multiple images)</label>
                        <div class="pull-right">
                            <span id="uploadingspanmsg"></span>
                            <label href="" target="_blank" class="btn-flat btn btn-sm btn-primary" title="Upload" style="margin-top: -5px;">
                                <i class="fa fa-upload"></i><input id="imguploadinput" style="width: 40px;height: 40px;position: absolute;top: -8px;right: 21px;z-index: -1;" class="btn" type="file">
                            </label>
                        </div>
                        <textarea id="imagesta" name="images" rows="4" class="form-control"></textarea>
                    </div>
                    <!-- /.form-group -->                                        
                </div>
                <!-- /.col -->                                              
                <div class="col-md-12">                                            
                    <div class="box box-solid box-default">
                        <div class="box-header with-border">
                            <h1 class="box-title">Price Calculation</h1>
                            <div class="box-tools">
                                <label>In Stock: </label>
                                <input type="radio" class="" name="pin_stock" value="Y" checked/> Yes
                                <input type="radio" class="" name="pin_stock" value="N" /> No
                            </div>
                        </div>
                        <div class="box-body no-padding" style="display: block; padding-top: 10px !important;">                            
                            <input type="hidden" name="defaultsellerid" value="<?php echo $defaultseller['id'] ?>"/>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Price</label>
                                    <input id="pprice" type="text" class="form-control" name="pprice"/>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Shipping</label>
                                    <input id="pshipping" type="text" class="form-control" name="pshipping"/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Fix Charge (<?php if(isset($sys['config']['marketPlaceFees'])) { echo $sys['config']['marketPlaceFees']; } ?>%)</label>
                                    <input type="hidden" id="fixedrate" value="<?php if(isset($sys['config']['marketPlaceFees'])) { echo $sys['config']['marketPlaceFees']; } ?>"/>
                                    <input id="pmarketplacechage" type="text" class="form-control" name="pmarketplacechage" readonly/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Tax(<?php if(isset($sys['config']['taxPercent'])) { echo $sys['config']['taxPercent']; } ?>%)</label>
                                    <input type="hidden" id="taxrate" value="<?php if(isset($sys['config']['taxPercent'])) { echo $sys['config']['taxPercent']; } ?>"/>
                                    <input id="ptax" type="text" class="form-control" name="ptax" title="It can be changed"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?php echo $defaultseller['name'] ?> Selling Price</label>
                                    <input id="psellingprice" type="text" class="form-control" name="psellingprice" readonly/>
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
            </div>
            <!-- /.row -->
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <input type="submit" class="btn btn-success" name="save_variant_cs" value="Save"/>            
        </div>
    </form>
</div>