
<div class="col-md-8">
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><?php echo $category['name'] ?> Import</h3>
            <div class="box-tools pull-right">                                    
                <a href="product-add?downloadsample=<?php echo $category['id']; ?>" target="_blank" class="btn btn-box-tool btn-default"><i class="fa fa-download"></i> Download Sample</a>
            </div>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>"/>
            <input type="hidden" name="category" value="<?php echo $product['category_id']; ?>"/>
            <!-- /.box-header -->
            <div class="box-body">
                <?php echo $savevariantcsmsg; ?>
                <div class="row">                
                    <div class="col-md-12">
                        <input type="hidden" name="category_id" value="<?php echo $category['id'] ?>"/>
                        <div class="form-group">
                            <label>Choose CSV File</label>
                            <input type="file" class="form-control" name="csvfile" required/>
                        </div>
                        <div class="form-group" style="height: 100%; max-height: 300px; overflow-y: auto;">
                            <?php if(isset($importmsg )) { echo $importmsg; } ?>
                        </div>
                        <!-- /.form-group -->                                        
                    </div>
                    <!-- /.col -->                                                                  
                </div>
                <!-- /.row -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <input type="submit" class="btn btn-success" name="save_import" value="Import"/>            
            </div>
        </form>
    </div>
</div>