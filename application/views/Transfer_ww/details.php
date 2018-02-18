<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5']) && ($CI->permissions['action5']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'class'=>'button_action_download',
        'data-title'=>"Download"
    );
}
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row show-grid">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Transfer Date :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date($item['date_transfer']);?></label>
            </div>
        </div>
    </div>


    <div class="row show-grid">
        <div class="col-xs-4">
            <label for="crop_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?> :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['crop_name']?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label for="crop_type_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?> :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['crop_type_name']?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label for="variety_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?> :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['variety_name']?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label for="pack_size_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PACK_SIZE');?> :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php if($item['pack_size_id']==0){echo 'Bulk';}else{echo $item['pack_size'];}?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label for="source_warehouse_id" class="control-label pull-right">Source Warehouse :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['source_ware_house_name']?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label for="current_stock_id" class="control-label pull-right">Current Stock :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label><?php $current_stock=System_helper::get_variety_stock(array($item['variety_id'])); if(isset($current_stock)){echo $current_stock[$item['variety_id']][$item['pack_size_id']][$item['source_warehouse_id']]['current_stock'];}else{echo 0;}?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label for="destination_warehouse_id" class="control-label pull-right">Destination Warehouse :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['destination_ware_house_name']?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="quantity" class="control-label pull-right"><?php echo $this->lang->line('LABEL_QUANTITY');?> :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['quantity']?></label>
            </div>
        </div>
    </div>

    <div class="row show-grid">
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="remarks" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?> :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['remarks']?></label>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
</form>
<script type="text/javascript">

jQuery(document).ready(function()
{
    system_preset({controller:'<?php echo $CI->router->class; ?>'});
});
</script>