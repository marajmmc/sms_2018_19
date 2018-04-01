<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
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
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_STOCK_IN');?><span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[date_stock_in]" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_stock_in']);?>" readonly/>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="purpose" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PURPOSE'); ?></label>

            </div>
            <?php
            if($item['purpose'])
            {
                ?>
                <label for="purpose" class="control-label"><?php echo $item['purpose']; ?></label>
            <?php
            }
            else
            {
                ?>
                <div class="col-sm-4 col-xs-8">
                    <select id="purpose" name="item[purpose]" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                        <option value="<?php echo $CI->config->item('system_purpose_variety_stock_in');?>" <?php if(isset($item['purpose'])){if($item['purpose']==$CI->config->item('system_purpose_variety_stock_in')){echo "selected";}}?>><?php echo $CI->lang->line('LABEL_STOCK_IN');?></option>
                        <option value="<?php echo $CI->config->item('system_purpose_variety_excess');?>" <?php if(isset($item['purpose'])){if($item['purpose']==$CI->config->item('system_purpose_variety_excess')){echo "selected";}}?>><?php echo $CI->lang->line('LABEL_EXCESS');?></option>
                    </select>
                </div>
            <?php } ?>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="remarks" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks]" id="remarks" class="form-control"><?php echo $item['remarks'] ?></textarea>
            </div>
        </div>

        <div style="overflow-x: auto;" class="row show-grid" id="order_items_container">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th class="text-right" style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                    <th class="text-right" style="min-width: 150px;">Current Stock (Pcs)</th>
                    <th class="text-right" style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_QUANTITY'); ?> (Pcs)</th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('ACTION'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($stock_in_sticker as $index=>$sticker)
                {
                    ?>
                    <tr>
                        <td>
                            <label><?php echo $sticker['crop_name']; ?></label>
                        </td>
                        <td>
                            <label><?php echo $sticker['crop_type_name']; ?></label>
                        </td>
                        <td>
                            <label><?php echo $sticker['variety_name']; ?></label>
                            <input type="hidden"  id="variety_id<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][variety_id]" value="<?php echo $sticker['variety_id']; ?>" />
                        </td>
                        <td class="text-right"  >
                            <label><?php echo $sticker['pack_size'];?></label>
                            <input type="hidden" id="pack_size_id<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][pack_size_id]" value="<?php echo $sticker['pack_size_id']; ?>" />

                        </td>
                        <td class="text-right">
                            <label><?php $current_stock=Stock_helper::get_raw_stock(array($sticker['variety_id'])); if(isset($current_stock)){echo $current_stock[$sticker['variety_id']][$sticker['pack_size_id']][$CI->config->item('system_sticker')]['current_stock'];}else{echo 0;}?></label>
                        </td>
                        <td class="text-right">
                            <input type="text" id="quantity_<?php echo $index+1;?>" value="<?php echo $sticker['quantity']; ?>" class="form-control text-right float_type_positive quantity" data-current-id="<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][quantity]">
                        </td>
                    </tr>
                <?php
                }
                ?>
                </tbody>

            </table>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4">
                <button type="button" class="btn btn-warning system_button_add_more" data-current-id="<?php echo sizeof($stock_in_sticker);?>"><?php echo $CI->lang->line('LABEL_ADD_MORE');?></button>
            </div>
            <div class="col-xs-4">

            </div>
        </div>

    </div>

    <div class="clearfix"></div>
</form>

<div id="system_content_add_more" style="display: none;">
    <table>
        <tbody>
        <tr>
            <td>
                <select class="form-control crop_id">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <?php
                    foreach($crops as $crop)
                    {?>
                        <option value="<?php echo $crop['value']?>"><?php echo $crop['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </td>
            <td>
                <div style="display: none;" class="crop_type_id_container">
                    <select class="form-control crop_type_id">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    </select>
                </div>
            </td>
            <td>
                <div style="display: none;" class="variety_id_container">
                    <select class="form-control variety_id">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    </select>
                </div>
            </td>
            <td>
                <div style="display: none;" class="pack_size_id_container">
                    <select class="form-control pack_size_id">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                        <?php
                        foreach($packs as $pack)
                        {?>
                            <option value="<?php echo $pack['value']?>"><?php echo $pack['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </td>
            <td class="text-right">
                <label class="stock_current">&nbsp;</label>
            </td>
            <td class="text-right">
                <input type="text" class="form-control text-right quantity float_type_positive" value=""/>
            </td>
            <td>
                <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script type="text/javascript">

    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $(".datepicker").datepicker({dateFormat : display_date_format});
        $(document).off("click", ".system_button_add_more");
        $(document).on("click", ".system_button_add_more", function(event)
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            current_id=current_id+1;
            $(this).attr('data-current-id',current_id);
            var content_id='#system_content_add_more table tbody';

            $(content_id+' .crop_id').attr('id','crop_id_'+current_id);
            $(content_id+' .crop_id').attr('data-current-id',current_id);

            $(content_id+' .crop_type_id').attr('id','crop_type_id_'+current_id);
            $(content_id+' .crop_type_id').attr('data-current-id',current_id);
            $(content_id+' .crop_type_id_container').attr('id','crop_type_id_container_'+current_id);

            $(content_id+' .variety_id').attr('id','variety_id_'+current_id);
            $(content_id+' .variety_id').attr('data-current-id',current_id);
            $(content_id+' .variety_id').attr('name','items['+current_id+'][variety_id]');
            $(content_id+' .variety_id_container').attr('id','variety_id_container_'+current_id);

            $(content_id+' .pack_size_id').attr('id','pack_size_id_'+current_id);
            $(content_id+' .pack_size_id').attr('data-current-id',current_id);
            $(content_id+' .pack_size_id').attr('name','items['+current_id+'][pack_size_id]');
            $(content_id+' .pack_size_id_container').attr('id','pack_size_id_container_'+current_id);

            $(content_id+' .stock_current').attr('id','stock_current_'+current_id);
            $(content_id+' .stock_current').attr('data-current-id',current_id);

            $(content_id+' .quantity').attr('id','quantity_'+current_id);
            $(content_id+' .quantity').attr('data-current-id',current_id);
            $(content_id+' .quantity').attr('name','items['+current_id+'][quantity]');

            var html=$(content_id).html();
            $("#order_items_container tbody").append(html);
            $(content_id+' .crop_id').removeAttr('id');
            $(content_id+' .crop_type_id').removeAttr('id');
            $(content_id+' .crop_type_id_container').removeAttr('id');
            $(content_id+' .variety_id').removeAttr('id');
            $(content_id+' .variety_id_container').removeAttr('id');
            $(content_id+' .pack_size_id').removeAttr('id');
            $(content_id+' .pack_size_id_container').removeAttr('id');
            $(content_id+' .stock_current').removeAttr('id');
            $(content_id+' .quantity').removeAttr('id');
        });

        $(document).off("click", ".system_button_add_delete");
        $(document).on("click", ".system_button_add_delete", function(event)
        {
            $(this).closest('tr').remove();
        });

        $(document).off("change",".crop_id");
        $(document).on("change",".crop_id",function()
        {
            var active_id=$(this).attr('data-current-id');
            $("#crop_type_id_"+active_id).val("");
            $("#variety_id_"+active_id).val("");
            $("#pack_size_id_"+active_id).val("");
            $("#stock_current_"+active_id).html("");
            $("#quantity_"+active_id).val("");
            var crop_id=$('#crop_id_'+active_id).val();
            $('#variety_id_container_'+active_id).hide();
            $('#pack_size_id_container_'+active_id).hide();
            if(crop_id>0)
            {
                if(system_types[crop_id]!==undefined)
                {
                    $('#crop_type_id_container_'+active_id).show();
                    $('#crop_type_id_'+active_id).html(get_dropdown_with_select(system_types[crop_id]));
                }
                else
                {
                    $('#crop_type_id_container_'+active_id).hide();
//                    $('#crop_type_id_container').hide();
                }
            }
            else
            {
                $('#crop_type_id_container_'+active_id).hide();
            }
        });

        $(document).off("change",".crop_type_id");
        $(document).on("change",".crop_type_id",function()
        {
            var active_id=$(this).attr('data-current-id');

            $("#variety_id_"+active_id).val("");
            $("#pack_size_id_"+active_id).val("");
            $("#stock_current_"+active_id).html("");
            $("#quantity_"+active_id).val("");
            var crop_type_id=$('#crop_type_id_'+active_id).val();
            $('#pack_size_id_container_'+active_id).hide();
            if(crop_type_id>0)
            {
                if(system_varieties[crop_type_id]!==undefined)
                {
                    $('#variety_id_container_'+active_id).show();
                    $('#variety_id_'+active_id).html(get_dropdown_with_select(system_varieties[crop_type_id]));
                }
                else
                {
                    $('#variety_id_container_'+active_id).hide();
                }
            }
            else
            {
                $('#variety_id_container_'+active_id).hide();
            }
        });

        $(document).off("change",".variety_id");
        $(document).on("change",".variety_id",function()
        {
            var active_id=$(this).attr('data-current-id');

            $("#pack_size_id_"+active_id).val("");
            $("#stock_current_"+active_id).html("");
            $("#quantity_"+active_id).val("");
            var variety_id=$('#variety_id_'+active_id).val();

            if(variety_id>0)
            {
                $('#pack_size_id_container_'+active_id).show();
            }
            else
            {
                $('#pack_size_id_container_'+active_id).hide();
            }
        });

        $(document).off("change",".pack_size_id");
        $(document).on("change",".pack_size_id",function()
        {
            var active_id=$(this).attr('data-current-id');

            $("#stock_current_"+active_id).html("");
            $("#quantity_"+active_id).val("");
            var variety_id=$('#variety_id_'+active_id).val();
            var pack_size_id=$('#pack_size_id_'+active_id).val();
            var packing_item='<?php echo $CI->config->item('system_sticker')?>';

            if(variety_id>0 && pack_size_id!='' && packing_item!='')
            {
                $.ajax({
                    url: "<?php echo site_url('common_controller/get_raw_current_stock'); ?>",
                    type: 'POST',
                    datatype: "JSON",
                    data:{
                        variety_id:variety_id,
                        pack_size_id:pack_size_id,
                        packing_item:packing_item,
                        html_container_id:'#stock_current_'+active_id
                    },
                    success: function (data, status)
                    {
                        $("#stock_current_"+active_id).text(data);
                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");
                    }
                });
            }
        });
    });
</script>