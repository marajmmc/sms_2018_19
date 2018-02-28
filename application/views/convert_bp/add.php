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
            <select id="crop_id" class="form-control">
                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                <?php
                foreach($crops as $crop)
                {
                    ?>
                    <option value="<?php echo $crop['value']?>"><?php echo $crop['text'];?></option>
                <?php
                }
                ?>
            </select>
        </div>
    </div>

    <div style="display: none;" class="row show-grid" id="crop_type_id_container">
        <div class="col-xs-4">
            <label for="crop_type_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?><span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <select id="crop_type_id" class="form-control">

            </select>
        </div>
    </div>

    <div style="display: none;" class="row show-grid" id="variety_id_container">
        <div class="col-xs-4">
            <label for="variety_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?><span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <select id="variety_id" name="item[variety_id]" class="form-control">

            </select>
        </div>
    </div>

    <div style="display: none;" class="row show-grid" id="warehouse_id_source_container">
        <div class="col-xs-4">
            <label for="warehouse_id_source" class="control-label pull-right">Source Warehouse<span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <select id="warehouse_id_source" name="item[warehouse_id_source]" class="form-control">

            </select>
        </div>
    </div>

    <div style="display: none;" class="row show-grid" id="current_stock_container">
        <div class="col-xs-4">
            <label for="current_stock_id" class="control-label pull-right">Current Bulk Stock (KG)</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label id="current_stock_id"><?php echo $item['current_stock'];?></label>
        </div>
    </div>

    <div style="display: none;" class="row show-grid" id="convert_quantity_id_container">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="convert_quantity" class="control-label pull-right"><?php echo $this->lang->line('LABEL_CONVERT_QUANTITY') .' (KG)';?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[convert_quantity]" id="convert_quantity_id" class="form-control float_type_positive" value="<?php echo $item['convert_quantity'];?>"/>
            </div>
        </div>
    </div>

    <div style="display: none;" class="row show-grid" id="warehouse_id_destination_container">
        <div class="col-xs-4">
            <label for="warehouse_id_destination" class="control-label pull-right">Destination Warehouse<span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <select id="warehouse_id_destination" name="item[warehouse_id_destination]" class="form-control">
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
    </div>

    <div style="display: none;" class="row show-grid" id="pack_size_id_container">
        <div class="col-xs-4">
            <label for="pack_size_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PACK_SIZE');?><span style="color:#FF0000">*</span></label>
        </div>
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
    </div>

    <div style="display: none;" class="row show-grid" id="qunatity_pack_expected_container">
        <div class="col-xs-4">
            <label for="qunatity_pack_expected" class="control-label pull-right">Expected Packet Quantity</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label id="qunatity_pack_expected_id" class="control-label"></label>
        </div>
    </div>
    <div style="display: none;" class="row show-grid" id="quantity_pack_actual_container">
        <div class="col-xs-4">
            <label for="quantity_pack_actual" class="control-label pull-right">Actual Packet Quantity<span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8" id="quantity_pack_actual_id_input_container">
            <input type="text" data-master-foil-per-pack="" data-common-foil-per-pack="" data-sticker-per-pack="" name="item[quantity_pack_actual]" id="quantity_pack_actual_id" class="form-control float_type_positive" value=""/>
        </div>
    </div>
    <div style="display: none;" class="row show-grid" id="expected_mf_container">
        <div class="col-xs-4">
            <label for="expected_mf" class="control-label pull-right">Expected Master Foil (KG):</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label id="expected_mf_id" class="control-label"></label>
            <label id="current_stock_mf" class="control-label"></label>
        </div>
    </div>

    <div style="display: none;" class="row show-grid" id="actual_mf_container">
        <div class="col-xs-4">
            <label for="actual_mf" class="control-label pull-right">Actual Master Foil (KG)<span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8" id="actual_mf_id_input_container">
            <input type="text" name="item[quantity_master_foil_actual]" id="actual_mf_id" class="form-control float_type_positive" value=""/>
        </div>
    </div>

    <div style="display: none;" class="row show-grid" id="expected_f_container">
        <div class="col-xs-4">
            <label for="expected_f" class="control-label pull-right">Expected Foil (KG):</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label id="expected_f_id" class="control-label"></label>
            <label id="current_stock_f" class="control-label"></label>
        </div>
    </div>
    <div style="display: none;" class="row show-grid" id="actual_f_container">
        <div class="col-xs-4">
            <label for="actual_f" class="control-label pull-right">Actual Foil (KG)<span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8" id="actual_f_id_input_container">
            <input type="text" name="item[quantity_foil_actual]" id="actual_f_id" class="form-control float_type_positive" value=""/>
        </div>
    </div>
    <div style="display: none;" class="row show-grid" id="expected_sticker_container">
        <div class="col-xs-4">
            <label for="expected_sticker" class="control-label pull-right">Expected Sticker:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label id="expected_sticker_id" class="control-label"></label>
            <label id="current_stock_sticker" class="control-label"></label>
        </div>
    </div>
    <div style="display: none;" class="row show-grid" id="actual_sticker_container">
        <div class="col-xs-4">
            <label for="actual_sticker" class="control-label pull-right">Actual Sticker<span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8" id="actual_sticker_id_input_container">
            <input type="text" name="item[quantity_sticker_actual]" id="actual_sticker_id" class="form-control float_type_positive" value=""/>
        </div>
    </div>

    <div style="display: none;" class="row show-grid" id="remarks_id_container">
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

    $(document).off('change','#crop_id');
    $(document).on("change","#crop_id",function()
    {
        $("#crop_type_id").val('');
        $("#variety_id").val('');
        $("#warehouse_id_source").val('');
        $("#current_stock_id").html('');
        $("#convert_quantity_id").val('');
        $("#warehouse_id_destination").val('');
        $("#pack_size_id").val('');
        $('#qunatity_pack_expected_id').html('');
        $('#quantity_pack_actual_id').val('');
        $('#quantity_pack_actual_id').attr('data-master-foil-per-pack','');
        $('#quantity_pack_actual_id').attr('data-common-foil-per-pack','');
        $('#quantity_pack_actual_id').attr('data-sticker-per-pack','');
        $('#expected_mf_id').html('');
        $('#current_stock_mf').html('');
        $('#actual_mf_id').val('');
        $('#expected_f_id').html('');
        $('#current_stock_f').html('');
        $('#actual_f_id').val('');
        $('#expected_sticker_id').html('');
        $('#current_stock_sticker').html('');
        $('#actual_sticker_id').val('');
        $("#remarks_id").html('');

        $('#crop_type_id_container').hide();
        $('#variety_id_container').hide();
        $('#warehouse_id_source_container').hide();
        $('#current_stock_container').hide();
        $('#convert_quantity_id_container').hide();
        $('#warehouse_id_destination_container').hide();
        $('#pack_size_id_container').hide();
        $('#qunatity_pack_expected_container').hide();
        $('#quantity_pack_actual_container').hide();
        $('#expected_mf_container').hide();
        $('#actual_mf_container').hide();
        $('#expected_f_container').hide();
        $('#actual_f_container').hide();
        $('#expected_sticker_container').hide();
        $('#actual_sticker_container').hide();
        $('#remarks_id_container').hide();

        var crop_id=$('#crop_id').val();
        if(crop_id>0)
        {
            $('#crop_type_id_container').show();
            $('#crop_type_id').html(get_dropdown_with_select(system_types[crop_id]));
        }
    });

    $(document).off('change','#crop_type_id');
    $(document).on("change","#crop_type_id",function()
    {
        $("#variety_id").val('');
        $("#warehouse_id_source").val('');
        $("#current_stock_id").html('');
        $("#convert_quantity_id").val('');
        $("#warehouse_id_destination").val('');
        $("#pack_size_id").val('');
        $('#qunatity_pack_expected_id').html('');
        $('#quantity_pack_actual_id').val('');
        $('#quantity_pack_actual_id').attr('data-master-foil-per-pack','');
        $('#quantity_pack_actual_id').attr('data-common-foil-per-pack','');
        $('#quantity_pack_actual_id').attr('data-sticker-per-pack','');
        $('#expected_mf_id').html('');
        $('#current_stock_mf').html('');
        $('#actual_mf_id').val('');
        $('#expected_f_id').html('');
        $('#current_stock_f').html('');
        $('#actual_f_id').val('');
        $('#expected_sticker_id').html('');
        $('#current_stock_sticker').html('');
        $('#actual_sticker_id').val('');
        $("#remarks_id").html('');

        $('#variety_id_container').hide();
        $('#warehouse_id_source_container').hide();
        $('#current_stock_container').hide();
        $('#convert_quantity_id_container').hide();
        $('#warehouse_id_destination_container').hide();
        $('#pack_size_id_container').hide();
        $('#qunatity_pack_expected_container').hide();
        $('#quantity_pack_actual_container').hide();
        $('#expected_mf_container').hide();
        $('#actual_mf_container').hide();
        $('#expected_f_container').hide();
        $('#actual_f_container').hide();
        $('#expected_sticker_container').hide();
        $('#actual_sticker_container').hide();
        $('#remarks_id_container').hide();

        var crop_type_id=$('#crop_type_id').val();
        if(crop_type_id>0)
        {
            $('#variety_id_container').show();
            $('#variety_id').html(get_dropdown_with_select(system_varieties[crop_type_id]));
        }
    });

    $(document).off('change','#variety_id');
    $(document).on("change","#variety_id",function()
    {
        $("#warehouse_id_source").val('');
        $("#current_stock_id").html('');
        $("#convert_quantity_id").val('');
        $("#warehouse_id_destination").val('');
        $("#pack_size_id").val('');
        $('#qunatity_pack_expected_id').html('');
        $('#quantity_pack_actual_id').val('');
        $('#quantity_pack_actual_id').attr('data-master-foil-per-pack','');
        $('#quantity_pack_actual_id').attr('data-common-foil-per-pack','');
        $('#quantity_pack_actual_id').attr('data-sticker-per-pack','');
        $('#expected_mf_id').html('');
        $('#current_stock_mf').html('');
        $('#actual_mf_id').val('');
        $('#expected_f_id').html('');
        $('#current_stock_f').html('');
        $('#actual_f_id').val('');
        $('#expected_sticker_id').html('');
        $('#current_stock_sticker').html('');
        $('#actual_sticker_id').val('');
        $("#remarks_id").html('');

        $('#warehouse_id_source_container').hide();
        $('#current_stock_container').hide();
        $('#convert_quantity_id_container').hide();
        $('#warehouse_id_destination_container').hide();
        $('#pack_size_id_container').hide();
        $('#qunatity_pack_expected_container').hide();
        $('#quantity_pack_actual_container').hide();
        $('#expected_mf_container').hide();
        $('#actual_mf_container').hide();
        $('#expected_f_container').hide();
        $('#actual_f_container').hide();
        $('#expected_sticker_container').hide();
        $('#actual_sticker_container').hide();
        $('#remarks_id_container').hide();

        var variety_id=$('#variety_id').val();
        if(variety_id>0)
        {
            $('#warehouse_id_source_container').show();
            $.ajax({
                url: base_url+"<?php echo $CI->controller_url?>/get_warehouse_source_and_packsize/",
                type: 'POST',
                datatype: "JSON",
                data:{variety_id:variety_id},
                success: function (data, status)
                {

                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });
        }
    });

    $(document).off('change','#warehouse_id_source');
    $(document).on("change","#warehouse_id_source",function()
    {
        $("#current_stock_id").html('');
        $("#convert_quantity_id").val('');
        $("#warehouse_id_destination").val('');
        $('#qunatity_pack_expected_id').html('');
        $('#quantity_pack_actual_id').val('');
        $('#quantity_pack_actual_id').attr('data-master-foil-per-pack','');
        $('#quantity_pack_actual_id').attr('data-common-foil-per-pack','');
        $('#quantity_pack_actual_id').attr('data-sticker-per-pack','');
        $('#expected_mf_id').html('');
        $('#current_stock_mf').html('');
        $('#actual_mf_id').val('');
        $('#expected_f_id').html('');
        $('#current_stock_f').html('');
        $('#actual_f_id').val('');
        $('#expected_sticker_id').html('');
        $('#current_stock_sticker').html('');
        $('#actual_sticker_id').val('');
        $("#remarks_id").html('');

        $('#current_stock_container').hide();
        $('#convert_quantity_id_container').hide();
        $('#warehouse_id_destination_container').hide();
        $('#pack_size_id_container').hide();
        $('#qunatity_pack_expected_container').hide();
        $('#quantity_pack_actual_container').hide();
        $('#expected_mf_container').hide();
        $('#actual_mf_container').hide();
        $('#expected_f_container').hide();
        $('#actual_f_container').hide();
        $('#expected_sticker_container').hide();
        $('#actual_sticker_container').hide();
        $('#remarks_id_container').hide();

        var variety_id=$('#variety_id').val();
        var pack_size_id=0;
        var warehouse_id_source=$('#warehouse_id_source').val();
        if(warehouse_id_source>0)
        {
            $('#current_stock_container').show();
            $('#convert_quantity_id_container').show();
            //$('#remarks_id_container').show();
            //$('#warehouse_id_destination_container').show();

            $.ajax({
                url: base_url+"common_controller/get_current_stock/",
                type: 'POST',
                datatype: "JSON",
                data:{
                    warehouse_id:warehouse_id_source,
                    pack_size_id:pack_size_id,
                    variety_id:variety_id
                },
                success: function (data, status)
                {

                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });
        }
    });

    $(document).off('input','#convert_quantity_id');
    $(document).on("input","#convert_quantity_id",function()
    {

        var convert_quantity=parseFloat($('#convert_quantity_id').val());

        if(convert_quantity>0)
        {
            $('#warehouse_id_destination_container').show();
            var pack_size=parseFloat($('option:selected', $("#pack_size_id")).html());
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
        }else
        {
            $('#pack_size_id').val('');
            $("#warehouse_id_destination").val('');
            $('#qunatity_pack_expected_id').html('');
            $('#quantity_pack_actual_id').val('');
            $('#quantity_pack_actual_id').attr('data-master-foil-per-pack','');
            $('#quantity_pack_actual_id').attr('data-common-foil-per-pack','');
            $('#quantity_pack_actual_id').attr('data-sticker-per-pack','');
            $('#expected_mf_id').html('');
            $('#current_stock_mf').html('');
            $('#actual_mf_id').val('');
            $('#expected_f_id').html('');
            $('#current_stock_f').html('');
            $('#actual_f_id').val('');
            $('#expected_sticker_id').html('');
            $('#current_stock_sticker').html('');
            $('#actual_sticker_id').val('');
            $("#remarks_id").html('');

            $('#warehouse_id_destination_container').hide();
            $('#pack_size_id_container').hide();
            $('#qunatity_pack_expected_container').hide();
            $('#quantity_pack_actual_container').hide();
            $('#expected_mf_container').hide();
            $('#actual_mf_container').hide();
            $('#expected_f_container').hide();
            $('#actual_f_container').hide();
            $('#expected_sticker_container').hide();
            $('#actual_sticker_container').hide();
            $('#remarks_id_container').hide();
        }

    });

    $(document).off('change','#warehouse_id_destination');
    $(document).on("change","#warehouse_id_destination",function()
    {
        $('#pack_size_id').val('');
        $('#qunatity_pack_expected_id').html('');
        $('#quantity_pack_actual_id').val('');
        $('#quantity_pack_actual_id').attr('data-master-foil-per-pack','');
        $('#quantity_pack_actual_id').attr('data-common-foil-per-pack','');
        $('#quantity_pack_actual_id').attr('data-sticker-per-pack','');
        $('#expected_mf_id').html('');
        $('#current_stock_mf').html('');
        $('#actual_mf_id').val('');
        $('#expected_f_id').html('');
        $('#current_stock_f').html('');
        $('#actual_f_id').val('');
        $('#expected_sticker_id').html('');
        $('#current_stock_sticker').html('');
        $('#actual_sticker_id').val('');
        $("#remarks_id").html('');

        $('#pack_size_id_container').hide();
        $('#qunatity_pack_expected_container').hide();
        $('#quantity_pack_actual_container').hide();
        $('#expected_mf_container').hide();
        $('#actual_mf_container').hide();
        $('#expected_f_container').hide();
        $('#actual_f_container').hide();
        $('#expected_sticker_container').hide();
        $('#actual_sticker_container').hide();
        $('#remarks_id_container').hide();

        var warehouse_id_destination=$('#warehouse_id_destination').val();
        if(warehouse_id_destination>0)
        {
            $('#pack_size_id_container').show();
        }
    });

    $(document).off('change','#pack_size_id');
    $(document).on("change","#pack_size_id",function()
    {
        $('#qunatity_pack_expected_id').html('');
        $('#quantity_pack_actual_id').val('');
        $('#quantity_pack_actual_id').attr('data-master-foil-per-pack','');
        $('#quantity_pack_actual_id').attr('data-common-foil-per-pack','');
        $('#quantity_pack_actual_id').attr('data-sticker-per-pack','');
        $('#expected_mf_id').html('');
        $('#current_stock_mf').html('');
        $('#actual_mf_id').val('');
        $('#expected_f_id').html('');
        $('#current_stock_f').html('');
        $('#actual_f_id').val('');
        $('#expected_sticker_id').html('');
        $('#current_stock_sticker').html('');
        $('#actual_sticker_id').val('');
        $("#remarks_id").html('');

        $('#qunatity_pack_expected_container').hide();
        $('#quantity_pack_actual_container').hide();
        $('#expected_mf_container').hide();
        $('#actual_mf_container').hide();
        $('#expected_f_container').hide();
        $('#actual_f_container').hide();
        $('#expected_sticker_container').hide();
        $('#actual_sticker_container').hide();
        $('#remarks_id_container').hide();

        var variety_id=$('#variety_id').val();
        var pack_size_id=$('#pack_size_id').val();
        var convert_quantity=$('#convert_quantity_id').val();

        if(pack_size_id>0)
        {
            $('#remarks_id_container').show();

            $.ajax({
                url: base_url+"<?php echo $CI->controller_url?>/check_variety_raw_config/",
                type: 'POST',
                datatype: "JSON",
                data:{variety_id:variety_id,pack_size_id:pack_size_id,convert_quantity:convert_quantity},
                success: function (data, status)
                {
                    $('#quantity_pack_actual_id').attr('data-master-foil-per-pack',data['unit_quantity_master_foil']);
                    $('#quantity_pack_actual_id').attr('data-common-foil-per-pack',data['unit_quantity_foil']);
                    $('#quantity_pack_actual_id').attr('data-sticker-per-pack',data['unit_quantity_sticker']);

                    if(data['unit_quantity_master_foil']>0)
                    {
                        $('#qunatity_pack_expected_container').show();
                        $('#quantity_pack_actual_container').show();
                        $('#expected_mf_container').show();
                        $('#actual_mf_container').show();

                        $('#qunatity_pack_expected_id').html(data['quantity_pack_expected']);
                        $('#quantity_pack_actual_id').val(data['quantity_pack_expected']);
                        $('#expected_mf_id').html(data['expected_quantity_mf']);
                        $('#actual_mf_id').val(data['expected_quantity_mf']);
                        $('#current_stock_mf').html(data['stock_current_mf']);

                    }
                    else if(data['expected_quantity_f']>0 && data['expected_quantity_sticker']>0)
                    {
                        $('#qunatity_pack_expected_container').show();
                        $('#quantity_pack_actual_container').show();
                        $('#expected_f_container').show();
                        $('#actual_f_container').show();
                        $('#expected_sticker_container').show();
                        $('#actual_sticker_container').show();

                        $('#qunatity_pack_expected_id').html(data['quantity_pack_expected']);
                        $('#quantity_pack_actual_id').val(data['quantity_pack_expected']);
                        $('#expected_f_id').html(data['expected_quantity_f']);
                        $('#actual_f_id').val(data['expected_quantity_f']);
                        $('#expected_sticker_id').html(data['expected_quantity_sticker']);
                        $('#actual_sticker_id').val(data['expected_quantity_sticker']);
                        $('#current_stock_f').html(data['stock_current_f']);
                        $('#current_stock_sticker').html(data['stock_current_sticker']);
                    }
                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });
        }
    });


    $(document).off('change','#quantity_pack_actual_id');
    $(document).on("change","#quantity_pack_actual_id",function()
    {

        $('#expected_mf_id').html('');
        $('#current_stock_mf').html('');
        $('#actual_mf_id').val('');
        $('#expected_f_id').html('');
        $('#current_stock_f').html('');
        $('#actual_f_id').val('');
        $('#expected_sticker_id').html('');
        $('#current_stock_sticker').html('');
        $('#actual_sticker_id').val('');

        var pack_size=parseFloat($('option:selected', $("#pack_size_id")).html());
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