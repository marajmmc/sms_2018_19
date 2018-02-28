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

        <?php
        if($item['id']>0)
        {
            ?>
            <div class="row show-grid">
                <?php
                if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
                {
                    ?>
                    <div class="row show-grid">
                        <div class="col-xs-4">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_TRANSFER');?><span style="color:#FF0000">*</span></label>
                        </div>
                        <div class="col-sm-4 col-xs-8">
                            <input type="text" name="item[date_transfer]" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_transfer']);?>"/>
                        </div>
                    </div>
                <?php
                }
                else
                {
                    ?>
                    <div class="col-sm-4 col-xs-8">
                        <?php echo System_helper::display_date($item['date_transfer']);?>
                    </div>
                <?php
                }
                ?>
            </div>
        <?php
        }
        else
        {
            ?>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_TRANSFER');?><span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[date_transfer]" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_transfer']);?>"/>
                </div>
            </div>
        <?php
        }
        ?>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="crop_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($item['id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $item['crop_name']?></label>
                <?php
                }
                else
                {
                    ?>
                    <select name="crop_id" id="crop_id" class="form-control">
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
                <?php
                }
                ?>
            </div>
        </div>
        <div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="crop_type_id_container">
            <div class="col-xs-4">
                <label for="crop_type_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <?php
            if($item['id'])
            {
                ?>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $item['crop_type_name']?></label>
                </div>
            <?php
            }
            else
            {
                ?>
                <div class="col-sm-4 col-xs-8">
                    <select name="crop_type_id" id="crop_type_id" class="form-control">

                    </select>
                </div>
            <?php
            }
            ?>
        </div>

        <div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="variety_id_container">
            <div class="col-xs-4">
                <label for="variety_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <?php
            if($item['id']>0)
            {
                ?>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $item['variety_name']?></label>
                </div>
            <?php
            }
            else
            {
                ?>
                <div class="col-sm-4 col-xs-8">
                    <select id="variety_id" name="item[variety_id]" class="form-control">

                    </select>
                </div>
            <?php
            }
            ?>
        </div>

        <div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="pack_size_id_container">
            <div class="col-xs-4">
                <label for="pack_size_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PACK_SIZE');?><span style="color:#FF0000">*</span></label>
            </div>
            <?php
            if($item['id']>0)
            {
                ?>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php if($item['pack_size_id']==0){echo 'Bulk';}else{echo $item['pack_size'];}?></label>
                </div>
            <?php
            }
            else
            {
                ?>
                <div class="col-sm-4 col-xs-8">
                    <select id="pack_size_id" name="item[pack_size_id]" class="form-control">

                    </select>
                </div>
            <?php
            }
            ?>
        </div>

        <div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="warehouse_id_source_container">
            <div class="col-xs-4">
                <label for="warehouse_id_source" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME_SOURCE');?><span style="color:#FF0000">*</span></label>
            </div>
            <?php if($item['id']>0)
            {
                ?>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $item['warehouse_name_source']?></label>
                </div>

            <?php
            }
            else
            {
                ?>
                <div class="col-sm-4 col-xs-8">
                    <select id="warehouse_id_source" name="item[warehouse_id_source]" class="form-control">

                    </select>
                </div>
            <?php
            }
            ?>
        </div>

        <div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="current_stock_container">
            <div class="col-xs-4">
                <label for="current_stock_id" class="control-label pull-right"><?php echo $this->lang->line('LABEL_CURRENT_STOCK');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label id="current_stock_id"><?php echo $item['current_stock'];?></label>
            </div>
        </div>

        <div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="warehouse_id_destination_container">
            <div class="col-xs-4">
                <label for="warehouse_id_destination" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME_DESTINATION');?><span style="color:#FF0000">*</span></label>
            </div>
            <?php
            if($item['id']>0)
            {
                ?>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $item['warehouse_name_destination']?></label>
                </div>
            <?php
            }
            else
            {
                ?>
                <div class="col-sm-4 col-xs-8">
                    <select id="warehouse_id_destination" name="item[warehouse_id_destination]" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        foreach($warehouse_destinations as $warehouse_destination)
                        {
                            ?>
                            <option value="<?php echo $warehouse_destination['value']?>"><?php echo $warehouse_destination['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            <?php
            }
            ?>
        </div>

        <div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="quantity_transfer_id_container">
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label for="quantity_transfer" class="control-label pull-right">Transfer <?php echo $this->lang->line('LABEL_QUANTITY_KG_PACK');?><span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[quantity_transfer]" id="quantity_transfer_id" class="form-control float_type_positive" value="<?php echo $item['quantity_transfer'];?>"/>
                </div>
            </div>
        </div>

        <div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="remarks_id_container">
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label for="remarks" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <textarea name="item[remarks]" id="remarks_id" class="form-control"><?php echo $item['remarks'] ?></textarea>
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

        $(".datepicker").datepicker({dateFormat : display_date_format});

        $(document).off('change','#crop_id');
        $(document).on("change","#crop_id",function()
        {
            $("#crop_type_id").val("");
            $("#variety_id").val("");
            $("#pack_size_id").val("");
            $("#warehouse_id_source").val("");
            $("#current_stock_id").text("");
            $("#warehouse_id_destination").val("");
            $("#quantity_transfer_id").val("");
            $("#remarks_id").val("");
            var crop_id=$('#crop_id').val();
            $('#crop_type_id_container').hide();
            $('#variety_id_container').hide();
            $('#pack_size_id_container').hide();
            $('#warehouse_id_source_container').hide();
            $('#current_stock_container').hide();
            $('#warehouse_id_destination_container').hide();
            $('#quantity_transfer_id_container').hide();
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
            $("#pack_size_id").val("");
            $("#warehouse_id_source").val("");
            $("#current_stock_id").text("");
            $("#warehouse_id_destination").val("");
            $("#quantity_transfer_id").val("");
            $("#remarks_id").val("");
            var crop_type_id=$('#crop_type_id').val();
            $('#variety_id_container').hide();
            $('#pack_size_id_container').hide();
            $('#warehouse_id_source_container').hide();
            $('#current_stock_container').hide();
            $('#warehouse_id_destination_container').hide();
            $('#quantity_transfer_id_container').hide();
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
            $("#pack_size_id").val("");
            $("#warehouse_id_source").val("");
            $("#current_stock_id").text("");
            $("#warehouse_id_destination").val("");
            $("#quantity_transfer_id").val("");
            $("#remarks_id").val("");
            var variety_id=$('#variety_id').val();
            $('#pack_size_id_container').hide();
            $('#warehouse_id_source_container').hide();
            $('#current_stock_container').hide();
            $('#warehouse_id_destination_container').hide();
            $('#quantity_transfer_id_container').hide();
            $('#remarks_id_container').hide();
            if(variety_id>0)
            {
                $('#pack_size_id_container').show();
                $.ajax({
                    url: base_url+"<?php echo $CI->controller_url?>/get_pack_size/",
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

        $(document).off('change','#pack_size_id');
        $(document).on("change","#pack_size_id",function()
        {
            $("#warehouse_id_source").val("");
            $("#current_stock_id").text("");
            $("#warehouse_id_destination").val("");
            $("#quantity_transfer_id").val("");
            $("#remarks_id").val("");
            var variety_id=$('#variety_id').val();
            var pack_size_id=$('#pack_size_id').val();
            if(pack_size_id == '')
            {
                $('#warehouse_id_source_container').hide();
                $('#current_stock_container').hide();
                $('#warehouse_id_destination_container').hide();
                $('#quantity_transfer_id_container').hide();
                $('#remarks_id_container').hide();
            }
            else
            {
                $('#warehouse_id_source_container').show();
                $('#current_stock_container').hide();
                $('#warehouse_id_destination_container').hide();
                $('#quantity_transfer_id_container').hide();
                $('#remarks_id_container').hide();
                $.ajax({
                    url: base_url+"<?php echo $CI->controller_url?>/get_warehouse_source/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{variety_id:variety_id,pack_size_id:pack_size_id},
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
            $("#warehouse_id_destination").val("");
            $("#quantity_transfer_id").val("");
            $("#remarks_id").val("");
            var variety_id=$('#variety_id').val();
            var pack_size_id=$('#pack_size_id').val();
            var warehouse_id_source=$('#warehouse_id_source').val();
            if(warehouse_id_source>0)
            {
                $('#current_stock_container').show();
                $('#quantity_transfer_id_container').show();
                $('#remarks_id_container').show();
                $('#warehouse_id_destination_container').show();

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
                        $("#current_stock_id").text(data);
                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#current_stock_container').hide();
                $('#quantity_transfer_id_container').hide();
                $('#remarks_id_container').hide();
                $('#warehouse_id_destination_container').hide();
            }
        });
    });
</script>