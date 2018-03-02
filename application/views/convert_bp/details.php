<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if((isset($CI->permissions['action3']) && ($CI->permissions['action3']==1)))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DELETE"),
        'data-message-confirm'=>'Are you sure to Delete this data?',
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
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
                <label class="control-label pull-right">Date Convert<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo System_helper::display_date($item['date_convert']);?>
            </div>
        </div>
    </div>


    <div style="" class="row show-grid">
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
            <label id="variety_id" data-variety-id="<?php echo $item['variety_id']?>" class="control-label"><?php echo $item['variety_name']?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label for="warehouse_id_source" class="control-label pull-right">Source Warehouse :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['warehouse_name_source']?></label>
        </div>
    </div>

    <div class="row show-grid" id="quantity_id_container">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="quantity" class="control-label pull-right">Convert Quantity (KG)</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo $item['quantity_convert'];?>
            </div>
        </div>
    </div>

    <div class="row show-grid" id="destination_warehouse_id_container">
        <div class="col-xs-4">
            <label for="warehouse_id_destination" class="control-label pull-right">Destination Warehouse :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['warehouse_name_destination']?></label>
        </div>
    </div>

    <div class="row show-grid" id="pack_size_id_container">
        <div class="col-xs-4">
            <label for="pack_size_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PACK_SIZE');?> :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label id="pack_size_id" data-packsize-id="<?php echo $item['pack_size_id']?>" class="control-label"><?php echo $item['pack_size']?></label>
        </div>
    </div>

    <div class="row show-grid" id="quantity_pack_expected_container">
        <div class="col-xs-4">
            <label for="quantity_pack_expected" class="control-label pull-right">Expected Packet Quantity:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['quantity_packet_expected']?>
        </div>
    </div>

    <div class="row show-grid" id="quantity_pack_actual_container">
        <div class="col-xs-4">
            <label for="quantity_pack_actual" class="control-label pull-right">Actual Packet Quantity<span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8" id="quantity_pack_actual_id_input_container">
            <?php echo $item['quantity_packet_actual']?>
        </div>
    </div>

    <div class="row show-grid" id="expected_mf_container">
        <div class="col-xs-4">
            <label for="expected_mf" class="control-label pull-right">Expected Master Foil (KG):</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['quantity_master_foil_expected']?>
        </div>
    </div>
    <div class="row show-grid" id="actual_mf_container">
        <div class="col-xs-4">
            <label for="actual_mf" class="control-label pull-right">Actual Master Foil (KG)</label>
        </div>
        <div class="col-sm-4 col-xs-8" id="actual_mf_id_input_container">
            <?php echo $item['quantity_master_foil_actual']?>
        </div>
    </div>

    <div class="row show-grid" id="expected_f_container">
        <div class="col-xs-4">
            <label for="expected_f" class="control-label pull-right">Expected Foil (KG):</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['quantity_master_foil_expected']?>
        </div>
    </div>
    <div class="row show-grid" id="actual_f_container">
        <div class="col-xs-4">
            <label for="actual_f" class="control-label pull-right">Actual Foil (KG)<span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8" id="actual_f_id_input_container">
            <?php echo $item['quantity_master_foil_actual']?>
        </div>
    </div>
    <div class="row show-grid" id="expected_sticker_container">
        <div class="col-xs-4">
            <label for="expected_sticker" class="control-label pull-right">Expected Sticker:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['quantity_sticker_expected']?>
        </div>
    </div>
    <div class="row show-grid" id="actual_sticker_container">
        <div class="col-xs-4">
            <label for="actual_sticker" class="control-label pull-right">Actual Sticker<span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8" id="actual_sticker_id_input_container">
            <?php echo $item['quantity_sticker_actual']?>
        </div>
    </div>

    <div class="row show-grid" id="remarks_id_container">
        <div class="col-xs-4">
            <label for="remarks" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['remarks']?>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php
    if((isset($CI->permissions['action3']) && ($CI->permissions['action3']==1)))
        {?>
            <form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/delete');?>" method="post">
                <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
                <div class="row show-grid" id="remarks_id_container">
                    <div class="col-xs-4">
                        <label for="remarks" class="control-label pull-right">Delete Reason</label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <textarea name="item[remarks_delete]" id="remarks_delete" class="form-control"></textarea>
                    </div>
                </div>
            </form>
        <?php
    }
    ?>

</div>

<script type="text/javascript">

jQuery(document).ready(function()
{
    system_preset({controller:'<?php echo $CI->router->class; ?>'});

});
</script>