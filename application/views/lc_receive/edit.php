<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );
}
$action_buttons[]=array
(
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

        <div class="col-md-12">
            <table class="table table-bordered table-responsive system_table_details_view">
                <thead>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right">Release Completed By</label></th>
                    <th class="bg-danger header_value"><label class="control-label"><?php echo $item['user_full_name']?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right">Release Completed Time</label></th>
                    <th class="bg-danger header_value"><label class="control-label"><?php echo System_helper::display_date_time($item['date_release_completed']);?></label></th>
                </tr>
                </thead>
            </table>
            <table class="table table-bordered table-responsive system_table_details_view">
                <thead>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_FISCAL_YEAR');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['fiscal_year']?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRINCIPAL_NAME');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['principal_name'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_MONTH');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo date("F", mktime(0, 0, 0,  $item['month_id'],1, 2000));?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_LC_NUMBER');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['lc_number'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_OPENING');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_opening']);?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CONSIGNMENT_NAME');?></label></th>
                    <th class=" header_value" colspan="3"><label class="control-label"><?php echo $item['consignment_name'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_EXPECTED');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_expected']);?></label></th>

                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_OPEN');?></label></th>
                    <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_open']);?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_RELEASE');?></label></th>
                    <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_release']);?></label></th>
                </tr>
                </thead>
            </table>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_RECEIVE');?> <span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[date_receive]" id="date_receive" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_receive']);?>" readonly="readonly" />
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_PACKING_LIST');?> <span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[date_packing_list]" id="date_packing_list" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_packing_list']);?>" readonly="readonly" />
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NUMBER_PACKING_LIST');?> <span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[packing_list_number]" id="packing_list_number" class="form-control" value="<?php echo $item['packing_list_number'];?>" />
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NUMBER_LOT');?> <span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[lot_number]" id="lot_number" class="form-control" value="<?php echo $item['lot_number'];?>" />
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_RECEIVE');?> </label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <textarea name="item[remarks_receive]" id="remarks_receive" class="form-control" ><?php echo $item['remarks_receive'];?></textarea>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row show-grid">
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="widget-header text-center" colspan="21">LC (<?php echo Barcode_helper::get_barcode_lc($item['id']);?>) Product & Price Details  :: ( Receive Status: <?php echo $item['status_receive']?> )</th>
                    </tr>
                    <tr>
                        <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                        <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                        <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_CARTON_NUMBER')?></th>
                        <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_CARTON_SIZE')?></th>
                        <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME'); ?></th>
                        <th class="label-primary text-center" colspan="2">Release Information</th>
                        <th class="label-warning text-center" colspan="2">Receive Information</th>
                        <th class="label-success text-center" colspan="2">Deference Information</th>
                    </tr>
                    <tr>
                        <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                        <th class="label-primary text-center"><?php echo $CI->lang->line('KG');?></th>
                        <th class="label-warning text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                        <th class="label-warning text-center"><?php echo $CI->lang->line('KG');?></th>
                        <th class="label-success text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                        <th class="label-success text-center"><?php echo $CI->lang->line('KG');?></th>
                    </tr>
                    </thead>
                    <?php
                    if(!empty($items))
                    {
                        ?>
                        <tbody>
                        <?php
                        $quantity_total_release=0;
                        $quantity_total_release_kg=0;
                        $quantity_total_receive=0;
                        $quantity_total_receive_kg=0;
                        foreach($items as $index=>$data)
                        {
                            if($data['revision_receive_count']==0)
                            {
                                $quantity_receive=$data['quantity_release'];
                            }
                            else
                            {
                                $quantity_receive=$data['quantity_receive'];
                            }
                            if($data['pack_size_id']==0)
                            {
                                $quantity_release_kg=$data['quantity_release'];
                                $quantity_receive_kg=$quantity_receive;
                            }
                            else
                            {
                                $quantity_release_kg=(($data['pack_size']*$data['quantity_release'])/1000);
                                $quantity_receive_kg=(($data['pack_size']*$quantity_receive)/1000);
                            }
                            $quantity_total_release+=$data['quantity_release'];
                            $quantity_total_release_kg+=$quantity_release_kg;
                            $quantity_total_receive+=$quantity_receive;
                            $quantity_total_receive_kg+=$quantity_receive_kg;
                            ?>
                            <tr>
                                <td>
                                    <strong class="text-success"><?php echo $data['variety_name']?> (<?php echo $data['variety_name_import']?>)</strong>
                                    <input type="hidden" name="items[<?php echo $index+1;?>][variety_id]" value="<?php echo $data['variety_id']; ?>">
                                </td>
                                <td class="text-center">
                                    <?php if($data['pack_size']==0){echo "Bulk";}else{echo $data['pack_size'];}?>
                                    <input type="hidden" name="items[<?php echo $index+1;?>][pack_size_id]" id="pack_size_id_<?php echo $index+1;?>" value="<?php echo $data['pack_size_id']; ?>" class="pack_size_id" data-pack-size-name="<?php if($data['pack_size']==0){echo 0;}else{echo $data['pack_size'];}?>">
                                </td>
                                <td>
                                    <input type="text" value="<?php echo $data['carton_number_receive']; ?>" class="form-control text-right" id="carton_number_receive_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][carton_number_receive]">
                                </td>
                                <td>
                                    <input type="text" value="<?php echo $data['carton_size_receive']; ?>" class="form-control float_type_positive" id="carton_size_receive_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][carton_size_receive]">
                                </td>
                                <td>
                                    <select class="form-control receive_warehouse_id" name="items[<?php echo $index+1;?>][receive_warehouse_id]" id="receive_warehouse_id_<?php echo $index+1;?>">
                                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                        <?php
                                        foreach($warehouses as $warehouse)
                                        {?>
                                            <option value="<?php echo $warehouse['value']?>" <?php if($warehouse['value']==$data['receive_warehouse_id']){echo "selected='selected'";}?>><?php echo $warehouse['text'];?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td class="text-right"><label class="control-label" for="" id="quantity_release_<?php echo $index+1;?>"><?php echo $data['quantity_release']?></label></td>
                                <td class="text-right"><label class="control-label" for="" id="quantity_release_kg_<?php echo $index+1;?>"><?php echo number_format($quantity_release_kg,3,'.','')?></label></td>
                                <td>
                                    <input type="text" value="<?php echo $quantity_receive; ?>" class="form-control float_type_positive quantity_receive" id="quantity_receive_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][quantity_receive]">
                                </td>
                                <td class="text-right" >
                                    <label class="control-label quantity_receive_kg" id="quantity_receive_kg_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                        <?php echo number_format($quantity_receive_kg,3,'.',''); ?>
                                    </label>
                                </td>
                                <td class="text-right"><label class="control-label" id="quantity_deference_<?php echo $index+1;?>" for=""><?php echo ($data['quantity_release']-$data['quantity_receive'])?></label></td>
                                <td class="text-right"><label class="control-label" id="quantity_deference_kg_<?php echo $index+1;?>" for=""><?php echo number_format(($quantity_release_kg-$quantity_receive_kg),3,'.','')?></label></td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="6" class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_KG')?></th>
                            <th class="text-right"><label class="control-label" id="lbl_quantity_total_release_kg"><?php echo number_format($quantity_total_release_kg,3,'.','');?></label></th>
                            <th class="text-right"><label class="control-label" id="lbl_quantity_total_receive"><?php echo $quantity_total_receive;?></label></th>
                            <th class="text-right"><label class="control-label" id="lbl_quantity_total_receive_kg"><?php echo number_format($quantity_total_receive_kg,3,'.','');?></label></th>
                            <th class="text-right"><label class="control-label" id="lbl_quantity_total_deference"><?php echo ($quantity_total_release-$quantity_total_receive);?></label></th>
                            <th class="text-right"><label class="control-label" id="lbl_quantity_total_deference_kg"><?php echo number_format(($quantity_total_release_kg-$quantity_total_receive_kg),3,'.','');?></label></th>
                            <th colspan="2">&nbsp;</th>
                        </tr>
                        </tfoot>
                    <?php
                    }
                    else
                    {
                        ?>
                        <tfoot>
                        <tr>
                            <td class="widget-header text-center" colspan="21"><strong><?php echo $CI->lang->line('NO_DATA_FOUND');?></strong></td>
                        </tr>
                        </tfoot>
                    <?php
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    $(document).ready(function()
    {
        $(".datepicker").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "c-2:c+2"});

        $(document).off('input','.quantity_receive');
        $(document).on('input', '.quantity_receive', function()
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            var quantity_release=parseFloat($('#quantity_release_'+current_id).html().replace(/,/g,''));
            var quantity_release_kg=parseFloat($('#quantity_release_kg_'+current_id).html().replace(/,/g,''));
            if(isNaN(quantity_release))
            {
                quantity_release=0;
            }
            if(isNaN(quantity_release_kg))
            {
                quantity_release_kg=0;
            }
            var quantity_receive=parseFloat($(this).val());
            var quantity_receive_kg=0;
            if(isNaN(quantity_receive))
            {
                quantity_receive=0;
            }
            var pack_size=parseFloat($("#pack_size_id_"+current_id).attr('data-pack-size-name'));
            if(pack_size==0)
            {
                quantity_receive_kg=quantity_receive;
            }
            else
            {
                quantity_receive_kg=parseFloat((pack_size*quantity_receive)/1000);
            }
            $("#quantity_receive_kg_"+current_id).html(number_format(quantity_receive_kg,3,'.',''));
            $("#quantity_deference_"+current_id).html((quantity_release-quantity_receive));
            $("#quantity_deference_kg_"+current_id).html(number_format((quantity_release_kg-quantity_receive_kg),3,'.',''));
            calculate_total();
        })
        function calculate_total()
        {
            var quantity_total_release=0;
            var quantity_total_receive=0;
            var quantity_total_receive_kg=0;
            $('.quantity_receive').each(function(index, element)
            {
                var current_id=parseInt($(this).attr('data-current-id'));
                var quantity_release=parseFloat($('#quantity_release_'+current_id).html().replace(/,/g,''));
                if(isNaN(quantity_release))
                {
                    quantity_release=0;
                }
                var quantity_receive=parseFloat($(this).val());
                if(isNaN(quantity_receive))
                {
                    quantity_receive=0;
                }
                var quantity_receive_kg=parseFloat($('#quantity_receive_kg_'+current_id).html().replace(/,/g,''));
                if(isNaN(quantity_receive_kg))
                {
                    quantity_receive_kg=0;
                }
                quantity_total_release+=quantity_release;
                quantity_total_receive+=quantity_receive;
                quantity_total_receive_kg+=quantity_receive_kg;
            });
            var quantity_total_release_kg=$("#lbl_quantity_total_release_kg").html().replace(/,/g,'');
            $('#lbl_quantity_total_receive').html(quantity_total_receive);
            $('#lbl_quantity_total_receive_kg').html(number_format(quantity_total_receive_kg,3,'.',''));
            $('#lbl_quantity_total_deference').html((quantity_total_release-quantity_total_receive));
            $('#lbl_quantity_total_deference_kg').html(number_format((quantity_total_release_kg-quantity_total_receive_kg),3,'.',''));
        }
    })
</script>
