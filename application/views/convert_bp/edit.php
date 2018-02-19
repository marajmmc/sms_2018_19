<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE_NEW"),
        'id'=>'button_action_save_new',
        'data-form'=>'#save_form'
    );
}
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
<input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
<input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
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
            <input type="text" name="item[date_convert]" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_convert']);?>"/>
        </div>
    </div>
</div>


<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label for="crop_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item['crop_name']?></label>
    </div>
</div>

<div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="crop_type_id_container">
    <div class="col-xs-4">
        <label for="crop_type_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <?php if($item['id']){?>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['crop_type_name']?></label>
        </div>
    <?php }else{?>
        <div class="col-sm-4 col-xs-8">
            <select id="crop_type_id" class="form-control">

            </select>
        </div>
    <?php } ?>
</div>

<div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="variety_id_container">
    <div class="col-xs-4">
        <label for="variety_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <?php if($item['id']>0){?>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['variety_name']?></label>
        </div>
    <?php }else{?>
        <div class="col-sm-4 col-xs-8">
            <select id="variety_id" name="item[variety_id]" class="form-control">

            </select>
        </div>
    <?php } ?>
</div>

<div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="source_warehouse_id_container">
    <div class="col-xs-4">
        <label for="source_warehouse_id" class="control-label pull-right">Source Warehouse<span style="color:#FF0000">*</span></label>
    </div>
    <?php if($item['id']>0){?>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['source_ware_house_name']?></label>
        </div>

    <?php }else{?>
        <div class="col-sm-4 col-xs-8">
            <select id="source_warehouse_id" name="item[source_warehouse_id]" class="form-control">

            </select>
        </div>
    <?php } ?>
</div>

<div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="current_stock_container">
    <div class="col-xs-4">
        <label for="current_stock_id" class="control-label pull-right">Current Stock (In KG)</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label id="current_stock_id"><?php echo $item['current_stock'];?></label>
    </div>
</div>

<div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="quantity_id_container">
    <div class="row show-grid">
        <div class="col-xs-4">
            <label for="quantity" class="control-label pull-right"><?php echo $this->lang->line('LABEL_QUANTITY') .' (In KG)';?><span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <input type="text" name="item[quantity]" id="quantity_id" class="form-control float_type_positive" value="<?php echo $item['quantity'];?>"/>
        </div>
    </div>
</div>

<div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="destination_warehouse_id_container">
    <div class="col-xs-4">
        <label for="destination_warehouse_id" class="control-label pull-right">Destination Warehouse<span style="color:#FF0000">*</span></label>
    </div>
    <?php if($item['id']>0){?>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['destination_ware_house_name']?></label>
        </div>
    <?php }else{?>
        <div class="col-sm-4 col-xs-8">
            <select id="destination_warehouse_id" name="item[destination_warehouse_id]" class="form-control">
                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                <?php
                foreach($destination_warehouses as $destination_warehouse)
                {
                    ?>
                    <option value="<?php echo $destination_warehouse['value']?>"><?php echo $destination_warehouse['text'];?></option>
                <?php
                }
                ?>
            </select>
        </div>
    <?php } ?>
</div>

<div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="pack_size_id_container">
    <div class="col-xs-4">
        <label for="pack_size_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PACK_SIZE');?><span style="color:#FF0000">*</span></label>
    </div>
    <?php if($item['id']>0){?>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['pack_size']?></label>
        </div>
    <?php }else{?>
        <div class="col-sm-4 col-xs-8">
            <select id="pack_size_id" name="item[pack_size_id]" class="form-control">
                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                <?php
                foreach($packs as $pack)
                {
                    ?>
                    <option value="<?php echo $pack['value']?>" data-pack-size-name="<?php echo $pack['text'];?>"><?php echo $pack['text'];?></option>
                <?php
                }
                ?>
            </select>
        </div>
    <?php } ?>
</div>

<div style="display: none;" class="row show-grid" id="number_of_packet_container">
    <div class="col-xs-4">
        <label for="number_of_packet" class="control-label pull-right">Number Of Packet:</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label id="number_of_packet_id" class="control-label"></label>
    </div>
</div>
<div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="number_of_actual_packet_container">
    <div class="col-xs-4">
        <label for="number_of_actual_packet" class="control-label pull-right">Number Of Actual Packet<span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8" id="number_of_actual_packet_id_input_container">
        <input type="text" name="item[number_of_actual_packet]" id="number_of_actual_packet_id" class="form-control float_type_positive" value="<?php echo $item['number_of_actual_packet'];?>"/>
    </div>
</div>
<div style="display: none;" class="row show-grid" id="expected_mf_container">
    <div class="col-xs-4">
        <label for="expected_mf" class="control-label pull-right">Expected Master Foil (In KG):</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label id="expected_mf_id" class="control-label"></label>
        <label for="expected_mf" id="expected_mf_id_in_pack_size_container" class="control-label pull-right">
            <input type="hidden" id="expected_mf_id_in_pack_size_change" value="<?php //echo $item['number_of_actual_packet'];?>"/>
        </label>
    </div>
</div>

<div style="<?php if(!($item['actual_master_foil']>0)){echo 'display:none';} ?>" class="row show-grid" id="actual_mf_container">
    <div class="col-xs-4">
        <label for="actual_mf" class="control-label pull-right">Actual Master Foil (In KG)<span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8" id="actual_mf_id_input_container">
        <input type="text" name="item[actual_mf]" id="actual_mf_id" class="form-control float_type_positive" value="<?php echo $item['actual_master_foil'];?>"/>
    </div>
</div>
<div style="display: none;" class="row show-grid" id="expected_f_container">
    <div class="col-xs-4">
        <label for="expected_f" class="control-label pull-right">Expected Foil (In KG):</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label id="expected_f_id" class="control-label"></label>
        <label for="expected_f" id="expected_f_id_in_pack_size_change_container" class="control-label pull-right">
            <input type="hidden" id="expected_f_id_in_pack_size_change" value="<?php //echo $item['number_of_actual_packet'];?>"/>
        </label>
    </div>
</div>
<div style="<?php if(!($item['actual_foil']>0)){echo 'display:none';} ?>" class="row show-grid" id="actual_f_container">
    <div class="col-xs-4">
        <label for="actual_f" class="control-label pull-right">Actual Foil (In KG)<span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8" id="actual_f_id_input_container">
        <input type="text" name="item[actual_f]" id="actual_f_id" class="form-control float_type_positive" value="<?php echo $item['actual_foil'];?>"/>
    </div>
</div>
<div style="display: none;" class="row show-grid" id="expected_sticker_container">
    <div class="col-xs-4">
        <label for="expected_sticker" class="control-label pull-right">Expected Sticker:</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label id="expected_sticker_id" class="control-label"></label>
        <label for="expected_mf" id="expected_sticker_id_in_pack_size_change_container" class="control-label pull-right">
            <input type="hidden" id="expected_sticker_id_in_pack_size_change" value="<?php //echo $item['number_of_actual_packet'];?>"/>
        </label>
    </div>
</div>
<div style="<?php if(!($item['actual_sticker']>0)){echo 'display:none';} ?>" class="row show-grid" id="actual_sticker_container">
    <div class="col-xs-4">
        <label for="actual_sticker" class="control-label pull-right">Actual Sticker<span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8" id="actual_sticker_id_input_container">
        <input type="text" name="item[actual_sticker]" id="actual_sticker_id" class="form-control float_type_positive" value="<?php echo $item['actual_sticker'];?>"/>
    </div>
</div>

<div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="remarks_id_container">
    <div class="col-xs-4">
        <label for="remarks" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <textarea name="item[remarks]" id="remarks_id" class="form-control"><?php echo $item['remarks'] ?></textarea>
    </div>
</div>
</div>
<div class="clearfix"></div>
</form>
<script type="text/javascript">

jQuery(document).ready(function()
{
    system_preset({controller:'<?php echo $CI->router->class; ?>'});

    $(".datepicker").datepicker({dateFormat : display_date_format});


});
</script>