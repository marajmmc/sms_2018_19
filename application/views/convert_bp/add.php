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

    <div style="display: none;" class="row show-grid" id="expected_quantity_packet_container">
        <div class="col-xs-4">
            <label for="expected_quantity_packet" class="control-label pull-right">Expected Packet Quantity</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label id="expected_quantity_packet_id" class="control-label"></label>
        </div>
    </div>
    <div style="display: none;" class="row show-grid" id="actual_quantity_packet_container">
        <div class="col-xs-4">
            <label for="actual_quantity_packet" class="control-label pull-right">Actual Packet Quantity<span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8" id="actual_quantity_packet_id_input_container">
            <input type="text" data-master-foil-per-pack="" data-common-foil-per-pack="" data-sticker-per-pack="" name="item[actual_quantity_packet]" id="actual_quantity_packet_id" class="form-control float_type_positive" value=""/>
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
            <input type="text" name="item[actual_mf]" id="actual_mf_id" class="form-control float_type_positive" value=""/>
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
            <input type="text" name="item[actual_f]" id="actual_f_id" class="form-control float_type_positive" value=""/>
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
            <input type="text" name="item[actual_sticker]" id="actual_sticker_id" class="form-control float_type_positive" value=""/>
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
        $("#crop_type_id").val("");
        $("#variety_id").val("");
        $("#warehouse_id_source").val("");
        $("#current_stock_id").text("");
        $("#convert_quantity_id").val("");
        $("#warehouse_id_destination").val("");
        $("#pack_size_id").val("");
        $('#expected_quantity_packet_id').html("");
        $('#actual_quantity_packet_id').val("");
        $('#expected_mf_id').html("");
        $('#actual_mf_id_input_container').val("");
        $('#expected_f_id').html("");
        $('#actual_f_id_input_container').val("");
        $('#expected_sticker_id').html("");
        $('#actual_sticker_id_input_container').val("");
        $("#remarks_id").val("");

        var crop_id=$('#crop_id').val();
        $('#crop_type_id_container').hide();
        $('#variety_id_container').hide();
        $('#warehouse_id_source_container').hide();
        $('#current_stock_container').hide();
        $('#convert_quantity_id_container').hide();
        $('#warehouse_id_destination_container').hide();
        $('#pack_size_id_container').hide();
        $('#expected_quantity_packet_container').hide();
        $('#actual_quantity_packet_container').hide();
        $('#expected_mf_container').hide();
        $('#actual_mf_container').hide();
        $('#expected_f_container').hide();
        $('#actual_f_container').hide();
        $('#expected_sticker_container').hide();
        $('#actual_sticker_container').hide();
        $('#remarks_id_container').hide();
        if(crop_id>0)
        {
            $('#crop_type_id_container').show();
            $('#crop_type_id').html(get_dropdown_with_select(system_types[crop_id]));
        }
    });

    $(document).off('change','#crop_type_id');
    $(document).on("change","#crop_type_id",function()
    {
        $("#variety_id").val("");
        $("#warehouse_id_source").val("");
        $("#current_stock_id").text("");
        $("#convert_quantity_id").val("");
        $("#warehouse_id_destination").val("");
        $("#pack_size_id").val("");
        $('#expected_quantity_packet_id').html("");
        $('#actual_quantity_packet_id').val("");
        $('#expected_mf_id').html("");
        $('#actual_mf_id_input_container').val("");
        $('#expected_f_id').html("");
        $('#actual_f_id_input_container').val("");
        $('#expected_sticker_id').html("");
        $('#actual_sticker_id_input_container').val("");
        $("#remarks_id").val("");

        var crop_type_id=$('#crop_type_id').val();
        $('#variety_id_container').hide();

        $('#warehouse_id_source_container').hide();
        $('#current_stock_container').hide();
        $('#convert_quantity_id_container').hide();
        $('#warehouse_id_destination_container').hide();
        $('#pack_size_id_container').hide();
        $('#expected_quantity_packet_container').hide();
        $('#actual_quantity_packet_container').hide();
        $('#expected_mf_container').hide();
        $('#actual_mf_container').hide();
        $('#expected_f_container').hide();
        $('#actual_f_container').hide();
        $('#expected_sticker_container').hide();
        $('#actual_sticker_container').hide();
        $('#remarks_id_container').hide();
        if(crop_type_id>0)
        {
            $('#variety_id_container').show();
            $('#variety_id').html(get_dropdown_with_select(system_varieties[crop_type_id]));
        }
    });

    $(document).off('change','#variety_id');
    $(document).on("change","#variety_id",function()
    {
        $("#warehouse_id_source").val("");
        $("#current_stock_id").text("");
        $("#convert_quantity_id").val("");
        $("#warehouse_id_destination").val("");
        $("#pack_size_id").val("");
        $('#expected_quantity_packet_id').html("");
        $('#actual_quantity_packet_id').val("");
        $('#expected_mf_id').html("");
        $('#actual_mf_id_input_container').val("");
        $('#expected_f_id').html("");
        $('#actual_f_id_input_container').val("");
        $('#expected_sticker_id').html("");
        $('#actual_sticker_id_input_container').val("");
        $("#remarks_id").val("");

        var variety_id=$('#variety_id').val();
        $('#warehouse_id_source_container').hide();
        $('#current_stock_container').hide();
        $('#convert_quantity_id_container').hide();
        $('#warehouse_id_destination_container').hide();
        $('#pack_size_id_container').hide();
        $('#expected_quantity_packet_container').hide();
        $('#actual_quantity_packet_container').hide();
        $('#expected_mf_container').hide();
        $('#actual_mf_container').hide();
        $('#expected_f_container').hide();
        $('#actual_f_container').hide();
        $('#expected_sticker_container').hide();
        $('#actual_sticker_container').hide();
        $('#remarks_id_container').hide();
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
        $("#current_stock_id").text("");
        $("#convert_quantity_id").val("");
        $("#warehouse_id_destination").val("");
        $("#pack_size_id").val("");
        $('#expected_quantity_packet_id').html("");
        $('#actual_quantity_packet_id').val("");
        $('#expected_mf_id').html("");
        $('#actual_mf_id_input_container').val("");
        $('#expected_f_id').html("");
        $('#actual_f_id_input_container').val("");
        $('#expected_sticker_id').html("");
        $('#actual_sticker_id_input_container').val("");
        $("#remarks_id").val("");

        $('#pack_size_id_container').hide();
        $('#expected_quantity_packet_container').hide();
        $('#actual_quantity_packet_container').hide();
        $('#expected_mf_container').hide();
        $('#actual_mf_container').hide();
        $('#expected_f_container').hide();
        $('#actual_f_container').hide();
        $('#expected_sticker_container').hide();
        $('#actual_sticker_container').hide();
        $('#current_stock_container').hide();
        $('#convert_quantity_id_container').hide();
        $('#remarks_id_container').hide();
        $('#warehouse_id_destination_container').hide();

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

    $(document).off('change','#convert_quantity_id');
    $(document).on("change","#convert_quantity_id",function()
    {

        $("#pack_size_id").val("");
        $('#expected_quantity_packet_id').html("");
        $('#actual_quantity_packet_id').val("");
        $('#expected_mf_id').html("");
        $('#actual_mf_id_input_container').val("");
        $('#expected_f_id').html("");
        $('#actual_f_id_input_container').val("");
        $('#expected_sticker_id').html("");
        $('#actual_sticker_id_input_container').val("");
        $("#remarks_id").val("");

        $('#actual_quantity_packet_id').attr('data-master-foil-per-pack','');
        $('#actual_quantity_packet_id').attr('data-common-foil-per-pack','');
        $('#actual_quantity_packet_id').attr('data-sticker-per-pack','');

        var convert_quantity=parseFloat($('#convert_quantity_id').val());

        if(convert_quantity>0)
        {
            $('#warehouse_id_destination_container').show();

            // Have to code from here .......

        }else
        {
            $('#warehouse_id_destination_container').hide();
            $('#pack_size_id_container').hide();
            $('#expected_quantity_packet_container').hide();
            $('#actual_quantity_packet_container').hide();
            $('#expected_mf_container').hide();
            $('#actual_mf_container').hide();
            $('#expected_f_container').hide();
            $('#actual_f_container').hide();
            $('#expected_sticker_container').hide();
            $('#actual_sticker_container').hide();
        }

    });

    $(document).off('change','#warehouse_id_destination');
    $(document).on("change","#warehouse_id_destination",function()
    {
        $("#pack_size_id").val("");
        $('#expected_quantity_packet_id').html("");
        $('#actual_quantity_packet_id').val("");
        $('#expected_mf_id').html("");
        $('#actual_mf_id_input_container').val("");
        $('#expected_f_id').html("");
        $('#actual_f_id_input_container').val("");
        $('#expected_sticker_id').html("");
        $('#actual_sticker_id_input_container').val("");
        $("#remarks_id").val("");

        $('#pack_size_id_container').hide();
        $('#expected_quantity_packet_container').hide();
        $('#actual_quantity_packet_container').hide();
        $('#expected_mf_container').hide();
        $('#actual_mf_container').hide();
        $('#expected_f_container').hide();
        $('#actual_f_container').hide();
        $('#expected_sticker_container').hide();
        $('#actual_sticker_container').hide();

        var warehouse_id_destination=$('#warehouse_id_destination').val();
        if(warehouse_id_destination>0)
        {
            $('#pack_size_id_container').show();
        }
    });

    $(document).off('change','#pack_size_id');
    $(document).on("change","#pack_size_id",function()
    {
        $('#expected_quantity_packet_id').html("");
        $('#actual_quantity_packet_id').val("");
        $('#expected_mf_id').html("");
        $('#current_stock_mf').html("");
        $('#current_stock_sticker').html("");
        $('#current_stock_f').html("");
        $('#actual_mf_id').val("");
        $('#actual_f_id').val("");
        $('#actual_sticker_id').val("");
        $('#actual_mf_id_input_container').val("");
        $('#expected_f_id').html("");
        $('#actual_f_id_input_container').val("");
        $('#expected_sticker_id').html("");
        $('#actual_sticker_id_input_container').val("");
        $('#expected_mf_id_in_pack_size_change').val("");
        $('#expected_f_id_in_pack_size_change').val("");
        $('#expected_sticker_id_in_pack_size_change').val("");
        $("#remarks_id").val("");

        $('#actual_quantity_packet_id').attr('data-master-foil-per-pack','');
        $('#actual_quantity_packet_id').attr('data-common-foil-per-pack','');
        $('#actual_quantity_packet_id').attr('data-sticker-per-pack','');

        var variety_id=$('#variety_id').val();
        var pack_size_id=$('#pack_size_id').val();
        var convert_quantity=$('#convert_quantity_id').val();

        $('#expected_quantity_packet_container').hide();
        $('#actual_quantity_packet_container').hide();
        $('#expected_mf_container').hide();
        $('#actual_mf_container').hide();
        $('#expected_f_container').hide();
        $('#actual_f_container').hide();
        $('#expected_sticker_container').hide();
        $('#actual_sticker_container').hide();

        if(pack_size_id>0)
        {
            $.ajax({
                url: base_url+"<?php echo $CI->controller_url?>/check_variety_raw_config/",
                type: 'POST',
                datatype: "JSON",
                data:{variety_id:variety_id,pack_size_id:pack_size_id,convert_quantity:convert_quantity},
                success: function (data, status)
                {
                    if(data['unit_quantity_master_foil']>0)
                    {
                        $('#expected_quantity_packet_container').show();
                        $('#actual_quantity_packet_container').show();
                        $('#expected_mf_container').show();
                        $('#actual_mf_container').show();

                        $('#expected_quantity_packet_id').html(data['expected_quantity_packet']);
                        $('#actual_quantity_packet_id').val(data['expected_quantity_packet']);
                        $('#expected_mf_id').html(data['expected_quantity_mf']);
                        $('#actual_mf_id').val(data['expected_quantity_mf']);
                        $('#current_stock_mf').html(data['stock_current_mf']);

                        //$("#pack_size_id")).(attr('data-pack-size-name').val(data['expected_quantity_mf']));
                        $('#actual_quantity_packet_id').attr('data-master-foil-per-pack',data['unit_quantity_master_foil']);
                        $('#actual_quantity_packet_id').attr('data-common-foil-per-pack',data['unit_quantity_foil']);
                        $('#actual_quantity_packet_id').attr('data-sticker-per-pack',data['unit_quantity_sticker']);


                    }
                    else if(data['expected_quantity_f']>0 && data['expected_quantity_sticker']>0)
                    {
                        $('#expected_quantity_packet_container').show();
                        $('#actual_quantity_packet_container').show();
                        $('#expected_f_container').show();
                        $('#actual_f_container').show();
                        $('#expected_sticker_container').show();
                        $('#actual_sticker_container').show();

                        $('#expected_quantity_packet_id').html(data['expected_quantity_packet']);
                        $('#actual_quantity_packet_id').val(data['expected_quantity_packet']);
                        $('#expected_f_id').html(data['expected_quantity_f']);
                        $('#actual_f_id').val(data['expected_quantity_f']);
                        $('#expected_sticker_id').html(data['expected_quantity_sticker']);
                        $('#actual_sticker_id').val(data['expected_quantity_sticker']);
                        $('#current_stock_f').html(data['stock_current_f']);
                        $('#current_stock_sticker').html(data['stock_current_sticker']);

                        $('#actual_quantity_packet_id').attr('data-master-foil-per-pack',data['unit_quantity_master_foil']);
                        $('#actual_quantity_packet_id').attr('data-common-foil-per-pack',data['unit_quantity_foil']);
                        $('#actual_quantity_packet_id').attr('data-sticker-per-pack',data['unit_quantity_sticker']);
                    }
                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });
        }
    });

    $(document).off('change','#actual_quantity_packet_id');
    $(document).on("change","#actual_quantity_packet_id",function()
    {
        var expected_quantity_packet_id=$('#expected_quantity_packet_id').html();
        var expected_mf_id=$('#expected_mf_id_in_pack_size_change').val();
        if(isNaN(expected_mf_id))
        {
            expected_mf_id=0;
        }
        var expected_f_id=$('#expected_f_id_in_pack_size_change').val();
        if(isNaN(expected_f_id))
        {
            expected_f_id=0;
        }
        var expected_sticker_id=$('#expected_sticker_id_in_pack_size_change').val();
        if(isNaN(expected_sticker_id))
        {
            expected_sticker_id=0;
        }
        var actual_quantity_packet_id=$('#actual_quantity_packet_id').val();

        if(expected_mf_id>0)
        {
            var required_unit_mf=((expected_mf_id*1000)/expected_quantity_packet_id);
            var required_mf=((actual_quantity_packet_id*required_unit_mf)/1000);
            $("#expected_mf_id").html(required_mf);
            $("#actual_mf_id").val(required_mf);
        }
        if(expected_f_id>0 && expected_sticker_id>0)
        {
            var required_unit_f=((expected_f_id*1000)/expected_quantity_packet_id);
            var required_foil=((actual_quantity_packet_id*required_unit_f)/1000);

            var required_unit_sticker=(expected_sticker_id/expected_quantity_packet_id);
            var required_sticker=(actual_quantity_packet_id*required_unit_sticker);

            $("#expected_f_id").html(required_foil);
            $("#actual_f_id").val(required_foil);

            $("#expected_sticker_id").html(required_sticker);
            $("#actual_sticker_id").val(required_sticker);
        }
    });


});
</script>