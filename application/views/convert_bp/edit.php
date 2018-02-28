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
            <label for="warehouse_id_source" class="control-label pull-right">Source Warehouse :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['warehouse_name_source']?></label>
        </div>
    </div>

    <div class="row show-grid" id="current_stock_container">
        <div class="col-xs-4">
            <label for="current_stock_id" class="control-label pull-right">Current Bulk Stock (KG) :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label id="current_stock_id"><?php echo $item['current_stock'];?></label>
        </div>
    </div>

    <div class="row show-grid" id="quantity_id_container">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="quantity" class="control-label pull-right">Convert Quantity (KG)<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[quantity_convert]" id="quantity_convert_id" class="form-control float_type_positive" value="<?php echo $item['quantity_convert'];?>"/>
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
            <label id="quantity_pack_expected_id" class="control-label"><?php echo (($item['quantity_convert']*1000)/$item['pack_size'])?></label>
        </div>
    </div>

    <div class="row show-grid" id="quantity_pack_actual_container">
        <div class="col-xs-4">
            <label for="quantity_pack_actual" class="control-label pull-right">Actual Packet Quantity<span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8" id="quantity_pack_actual_id_input_container">
            <input type="text" data-master-foil-per-pack="<?php echo $item['unit_master_foil']?>" data-common-foil-per-pack="<?php echo $item['unit_foil']?>" data-sticker-per-pack="<?php echo $item['unit_sticker']?>" name="item[quantity_pack_actual]" id="quantity_pack_actual_id" class="form-control float_type_positive" value="<?php echo $item['quantity_packet_actual'];?>"/>
        </div>
    </div>

    <div style="<?php if(!($item['unit_master_foil']>0)){echo 'display:none';} ?>" class="row show-grid" id="expected_mf_container">
        <div class="col-xs-4">
            <label for="expected_mf" class="control-label pull-right">Expected Master Foil (KG):</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label id="expected_mf_id" class="control-label"><?php echo $item['quantity_master_foil_expected'];?></label>
        </div>
    </div>
    <div style="<?php if(!($item['unit_master_foil']>0)){echo 'display:none';} ?>" class="row show-grid" id="actual_mf_container">
        <div class="col-xs-4">
            <label for="actual_mf" class="control-label pull-right">Actual Master Foil (KG)<span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8" id="actual_mf_id_input_container">
            <input type="text" name="item[quantity_master_foil_actual]" id="actual_mf_id" class="form-control float_type_positive" value="<?php echo $item['quantity_master_foil_actual'];?>"/>
        </div>
    </div>

    <div style="<?php if(!($item['unit_foil']>0)){echo 'display:none';} ?>" class="row show-grid" id="expected_f_container">
        <div class="col-xs-4">
            <label for="expected_f" class="control-label pull-right">Expected Foil (KG):</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label id="expected_f_id" class="control-label"><?php echo $item['quantity_foil_expected'];?></label>
        </div>
    </div>
    <div style="<?php if(!($item['unit_foil']>0)){echo 'display:none';} ?>" class="row show-grid" id="actual_f_container">
        <div class="col-xs-4">
            <label for="actual_f" class="control-label pull-right">Actual Foil (KG)<span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8" id="actual_f_id_input_container">
            <input type="text" name="item[quantity_foil_actual]" id="actual_f_id" class="form-control float_type_positive" value="<?php echo $item['quantity_foil_actual'];?>"/>
        </div>
    </div>
    <div style="<?php if(!($item['unit_sticker']>0)){echo 'display:none';} ?>" class="row show-grid" id="expected_sticker_container">
        <div class="col-xs-4">
            <label for="expected_sticker" class="control-label pull-right">Expected Sticker:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label id="expected_sticker_id" class="control-label"><?php echo $item['quantity_sticker_expected'];?></label>
        </div>
    </div>
    <div style="<?php if(!($item['unit_sticker']>0)){echo 'display:none';} ?>" class="row show-grid" id="actual_sticker_container">
        <div class="col-xs-4">
            <label for="actual_sticker" class="control-label pull-right">Actual Sticker<span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8" id="actual_sticker_id_input_container">
            <input type="text" name="item[quantity_sticker_actual]" id="actual_sticker_id" class="form-control float_type_positive" value="<?php echo $item['quantity_sticker_actual'];?>"/>
        </div>
    </div>

    <div class="row show-grid" id="remarks_id_container">
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

    $(document).off('input','#quantity_convert_id');
    $(document).on("input","#quantity_convert_id",function()
    {
        var convert_quantity=parseFloat($('#quantity_convert_id').val());

        var pack_size=$('#pack_size_id').html();
        if(isNaN(pack_size))
        {
            pack_size=0;
        }
        //alert(pack_size);
        if(pack_size>0)
        {
            var qunatity_pack_expected=(convert_quantity*1000)/pack_size;
            $('#qunatity_pack_expected_id').html(qunatity_pack_expected);
            $('#quantity_pack_actual_id').val(qunatity_pack_expected);

            var unit_master_foil=$("#quantity_pack_actual_id").attr('data-master-foil-per-pack');
            var unit_common_foil=$("#quantity_pack_actual_id").attr('data-common-foil-per-pack');
            var unit_sticker=$("#quantity_pack_actual_id").attr('data-sticker-per-pack');

            if(unit_master_foil>0)
            {
                var quantity_expected_mf=((unit_master_foil*qunatity_pack_expected)/1000);
                $('#expected_mf_id').html(quantity_expected_mf);
                $('#actual_mf_id').val(quantity_expected_mf);
                $('#expected_f_id').html('');
                $('#actual_f_id').val('');
                $('#expected_sticker_id').html('');
                $('#actual_sticker_id').val('');
            }
            else if(unit_common_foil>0 && unit_sticker>0)
            {
                var quantity_expected_f=((unit_common_foil*qunatity_pack_expected)/1000);
                var quantity_expected_sticker=(unit_sticker*qunatity_pack_expected);
                $('#expected_f_id').html(quantity_expected_f);
                $('#actual_f_id').val(quantity_expected_f);
                $('#expected_sticker_id').html(quantity_expected_sticker);
                $('#actual_sticker_id').val(quantity_expected_sticker);
                $('#expected_mf_id').html('');
                $('#actual_mf_id').val('');
            }
        }


    });

    $(document).off('change','#quantity_pack_actual_id');
    $(document).on("change","#quantity_pack_actual_id",function()
    {
        $('#expected_mf_id').html('');
        $('#actual_mf_id').val('');
        $('#expected_f_id').html('');
        $('#actual_f_id').val('');
        $('#expected_sticker_id').html('');
        $('#actual_sticker_id').val('');

        var pack_size=$('#pack_size_id').html();
        if(isNaN(pack_size))
        {
            pack_size=0;
        }

        var quantity_pack_actual=$('#quantity_pack_actual_id').val();

        var unit_master_foil=$("#quantity_pack_actual_id").attr('data-master-foil-per-pack');
        var unit_common_foil=$("#quantity_pack_actual_id").attr('data-common-foil-per-pack');
        var unit_sticker=$("#quantity_pack_actual_id").attr('data-sticker-per-pack');

        if(unit_master_foil>0)
        {
            var quantity_expected_mf=((unit_master_foil*quantity_pack_actual)/1000);
            $('#expected_mf_id').html(quantity_expected_mf);
            $('#actual_mf_id').val(quantity_expected_mf);
        }
        else if(unit_common_foil>0 && unit_sticker>0)
        {
            var quantity_expected_f=((unit_common_foil*quantity_pack_actual)/1000);
            var quantity_expected_sticker=(unit_sticker*quantity_pack_actual);
            $('#expected_f_id').html(quantity_expected_f);
            $('#actual_f_id').val(quantity_expected_f);
            $('#expected_sticker_id').html(quantity_expected_sticker);
            $('#actual_sticker_id').val(quantity_expected_sticker);
        }

    });

});
</script>