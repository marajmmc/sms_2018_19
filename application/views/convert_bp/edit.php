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
        <label for="source_warehouse_id" class="control-label pull-right">Source Warehouse :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item['source_ware_house_name']?></label>
    </div>
</div>

<div class="row show-grid" id="current_stock_container">
    <div class="col-xs-4">
        <label for="current_stock_id" class="control-label pull-right">Current Stock (In KG) :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label id="current_stock_id"><?php echo $item['current_stock'];?></label>
    </div>
</div>

<div class="row show-grid" id="quantity_id_container">
    <div class="row show-grid">
        <div class="col-xs-4">
            <label for="quantity" class="control-label pull-right"><?php echo $this->lang->line('LABEL_QUANTITY') .' (In KG)';?><span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <input type="text" name="item[quantity]" id="quantity_id" class="form-control float_type_positive" value="<?php echo $item['quantity'];?>"/>
        </div>
    </div>
</div>

<div class="row show-grid" id="destination_warehouse_id_container">
    <div class="col-xs-4">
        <label for="destination_warehouse_id" class="control-label pull-right">Destination Warehouse :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item['destination_ware_house_name']?></label>
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

<div class="row show-grid" id="number_of_packet_container">
    <div class="col-xs-4">
        <label for="number_of_packet" class="control-label pull-right">Number Of Expected Packet:</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label id="number_of_packet_id" class="control-label"><?php echo (($item['quantity']*1000)/$item['pack_size'])?></label>
    </div>
</div>

<div class="row show-grid" id="number_of_actual_packet_container">
    <div class="col-xs-4">
        <label for="number_of_actual_packet" class="control-label pull-right">Number Of Actual Packet<span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8" id="number_of_actual_packet_id_input_container">
        <input type="text" name="item[number_of_actual_packet]" id="number_of_actual_packet_id" class="form-control float_type_positive" value="<?php echo $item['number_of_actual_packet'];?>"/>
    </div>
</div>

<div style="<?php if(!($item['quantity_actual_master_foil']>0)){echo 'display:none';} ?>" class="row show-grid" id="expected_mf_container">
    <div class="col-xs-4">
        <label for="expected_mf" class="control-label pull-right">Expected Master Foil (In KG):</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label data-unit-mf-id="<?php echo $item['unit_master_foil']?>" id="expected_mf_id" class="control-label"><?php echo $item['expected_master_foil'];?></label>
    </div>
</div>

<div style="<?php if(!($item['quantity_actual_master_foil']>0)){echo 'display:none';} ?>" class="row show-grid" id="actual_mf_container">
    <div class="col-xs-4">
        <label for="actual_mf" class="control-label pull-right">Actual Master Foil (In KG)<span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8" id="actual_mf_id_input_container">
        <input type="text" name="item[actual_mf]" id="actual_mf_id" class="form-control float_type_positive" value="<?php echo $item['quantity_actual_master_foil'];?>"/>
    </div>
</div>
<div style="<?php if(!($item['quantity_actual_foil']>0)){echo 'display:none';} ?>" class="row show-grid" id="expected_f_container">
    <div class="col-xs-4">
        <label for="expected_f" class="control-label pull-right">Expected Foil (In KG):</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label data-unit-f-id="<?php echo $item['unit_foil']?>" id="expected_f_id" class="control-label"><?php echo $item['expected_foil'];?></label>
    </div>
</div>
<div style="<?php if(!($item['quantity_actual_foil']>0)){echo 'display:none';} ?>" class="row show-grid" id="actual_f_container">
    <div class="col-xs-4">
        <label for="actual_f" class="control-label pull-right">Actual Foil (In KG)<span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8" id="actual_f_id_input_container">
        <input type="text" name="item[actual_f]" id="actual_f_id" class="form-control float_type_positive" value="<?php echo $item['quantity_actual_foil'];?>"/>
    </div>
</div>
<div style="<?php if(!($item['quantity_actual_sticker']>0)){echo 'display:none';} ?>" class="row show-grid" id="expected_sticker_container">
    <div class="col-xs-4">
        <label for="expected_sticker" class="control-label pull-right">Expected Sticker:</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label data-unit-sticker-id="<?php echo $item['unit_sticker']?>" id="expected_sticker_id" class="control-label"><?php echo $item['expected_sticker'];?></label>
    </div>
</div>
<div style="<?php if(!($item['quantity_actual_sticker']>0)){echo 'display:none';} ?>" class="row show-grid" id="actual_sticker_container">
    <div class="col-xs-4">
        <label for="actual_sticker" class="control-label pull-right">Actual Sticker<span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8" id="actual_sticker_id_input_container">
        <input type="text" name="item[actual_sticker]" id="actual_sticker_id" class="form-control float_type_positive" value="<?php echo $item['quantity_actual_sticker'];?>"/>
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

    $(document).off('input','#quantity_id');
    $(document).on("input","#quantity_id",function()
    {
        var quantity=parseFloat($('#quantity_id').val());


        var pack_size=$('#pack_size_id').html();

        if(isNaN(pack_size))
        {
            pack_size=0;
        }
        if(pack_size>0)
        {
            $('#number_of_packet_id').html("");
            $('#number_of_actual_packet_id').val("");

            var number_of_packet=((quantity*1000)/pack_size);
            $("#number_of_packet_id").html(number_of_packet);
            $("#number_of_actual_packet_id").val(number_of_packet);

            var pack_size_id=$('#pack_size_id').attr('data-packsize-id');
            var variety_id=$('#variety_id').attr('data-variety-id');;
            var quantity=$('#quantity_id').val();
            $.ajax({
                url: base_url+"<?php echo $CI->controller_url?>/check_variety_raw_config/",
                type: 'POST',
                datatype: "JSON",
                data:{variety_id:variety_id,pack_size_id:pack_size_id,quantity:quantity},
                success: function (data, status)
                {
                    if(data['quantity_master_foil']>0)
                    {
                        $('#number_of_packet_container').show();
                        $('#number_of_actual_packet_container').show();
                        $('#expected_mf_container').show();
                        $('#actual_mf_container').show();
                    }
                    else if(data['quantity_foil']>0 && data['quantity_sticker']>0)
                    {
                        $('#number_of_packet_container').show();
                        $('#number_of_actual_packet_container').show();
                        $('#expected_f_container').show();
                        $('#actual_f_container').show();
                        $('#expected_sticker_container').show();
                        $('#actual_sticker_container').show();
                    }
                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });

        }
    });

    $(document).off('change','#number_of_actual_packet_id');
    $(document).on("change","#number_of_actual_packet_id",function()
    {
        var number_of_packet_id=$('#number_of_packet_id').html();
        var expected_mf_id=$('#expected_mf_id').html();
        if(isNaN(expected_mf_id))
        {
            expected_mf_id=0;
        }
        var expected_f_id=$('#expected_f_id').html();
        if(isNaN(expected_f_id))
        {
            expected_f_id=0;
        }
        var expected_sticker_id=$('#expected_sticker_id').html();
        if(isNaN(expected_sticker_id))
        {
            expected_sticker_id=0;
        }
        var number_of_actual_packet_id=$('#number_of_actual_packet_id').val();


        if(expected_mf_id>0)
        {
            var required_unit_mf=$('#expected_mf_id').attr('data-unit-mf-id');
            var required_mf=((number_of_actual_packet_id*required_unit_mf)/1000);
            $("#expected_mf_id").html(required_mf);
            $("#actual_mf_id").val(required_mf);
        }

        if(expected_f_id>0 && expected_sticker_id>0)
        {
            var required_unit_f=$('#expected_f_id').attr('data-unit-f-id');
            var required_foil=((number_of_actual_packet_id*required_unit_f)/1000);
            var required_unit_sticker=$('#expected_sticker_id').attr('data-unit-sticker-id');
            var required_sticker=(number_of_actual_packet_id*required_unit_sticker);

            $("#expected_f_id").html(required_foil);
            $("#actual_f_id").val(required_foil);

            $("#expected_sticker_id").html(required_sticker);
            $("#actual_sticker_id").val(required_sticker);
        }
    });

});
</script>