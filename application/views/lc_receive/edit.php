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
            <table class="table table-bordered table-responsive ">
                <thead>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_FISCAL_YEAR');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['fiscal_year_name']?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_MONTH');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo date("F", mktime(0, 0, 0,  $item['month_id'],1, 2000));?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_OPENING');?></label></th>
                    <th><label class="control-label"><?php echo System_helper::display_date($item['date_opening']);?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_EXPECTED');?></label></th>
                    <th><label class="control-label"><?php echo System_helper::display_date($item['date_expected']);?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRINCIPAL_NAME');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['principal_name'];?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_LC_NUMBER');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['lc_number'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CONSIGNMENT_NAME');?></label></th>
                    <th><label class="control-label"><?php echo $item['consignment_name'];?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['remarks'];?></label></th>
                </tr>
                </thead>

            </table>
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
                        <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY'); ?></th>
                        <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                        <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_WAREHOUSE'); ?></th>
                        <th class="label-primary text-center" colspan="2">Release Information</th>
                        <th class="label-warning text-center" colspan="2">Receive Information</th>
                    </tr>
                    <tr>
                        <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                        <th class="label-primary text-center">KG</th>
                        <th class="label-warning text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                        <th class="label-warning text-center">KG</th>
                    </tr>
                    </thead>
                    <?php
                    if(!empty($items))
                    {
                        ?>
                        <tbody>
                        <?php
                        $quantity_total_release_kg=0;
                        $quantity_total_receive_kg=0;
                        foreach($items as $index=>$data)
                        {
                            if($item['revision_receive_count']==0)
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
                                $quantity_release_kg=(($data['pack_size_name']*$data['quantity_release'])/1000);
                                $quantity_receive_kg=(($data['pack_size_name']*$quantity_receive)/1000);
                            }
                            $quantity_total_release_kg+=$quantity_release_kg;
                            $quantity_total_receive_kg+=$quantity_receive_kg;
                            ?>
                            <tr>
                                <td>
                                    <strong class="text-success"><?php echo $data['variety_name']?> (<?php echo $data['variety_name_import']?>)</strong>
                                    <input type="hidden" name="items[<?php echo $index+1;?>][variety_id]" value="<?php echo $data['variety_id']; ?>">
                                </td>
                                <td class="text-center">
                                    <?php if($data['pack_size_name']==0){echo "Bulk";}else{echo $data['pack_size_name'];}?>
                                    <input type="hidden" name="items[<?php echo $index+1;?>][pack_size_id]" id="pack_size_id_<?php echo $index+1;?>" value="<?php echo $data['pack_size_id']; ?>" class="pack_size_id" data-pack-size-name="<?php if($data['pack_size_name']==0){echo 0;}else{echo $data['pack_size_name'];}?>">
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
                                <td class="text-right"><label class="control-label" for=""><?php echo number_format($data['quantity_release'],3)?></label></td>
                                <td class="text-right"><label class="control-label" for=""><?php echo number_format($quantity_release_kg,3)?></label></td>
                                <td>
                                    <input type="text" value="<?php echo $quantity_receive; ?>" class="form-control float_type_positive quantity_receive" id="quantity_receive_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][quantity_receive]">
                                </td>
                                <td class="text-right" >
                                    <label class="control-label quantity_receive_kg" id="quantity_receive_kg_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                        <?php echo number_format($quantity_receive_kg,3); ?>
                                    </label>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="4" class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_KG')?></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($quantity_total_release_kg,3);?></label></th>
                            <th>&nbsp;</th>
                            <th class="text-right"><label class="control-label" id="lbl_quantity_total_receive_kg"><?php echo number_format($quantity_total_receive_kg,3);?></label></th>
                        </tr>
                        </tfoot>
                    <?php
                    }
                    else
                    {
                        ?>
                        <tfoot>
                        <tr>
                            <td class="widget-header text-center" colspan="21"><strong>Data Not Found</strong></td>
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
        $(document).off('input','.quantity_receive');
        $(document).on('input', '.quantity_receive', function()
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            var quantity_receive_kg=0;
            var quantity_receive=parseFloat($(this).val());
            var price_unit_lc_currency=parseFloat($("#price_unit_lc_currency_"+current_id).val());
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
            $("#quantity_receive_kg_"+current_id).html(number_format(quantity_receive_kg,3));

            var quantity_total_kg=0;
            $('.quantity_receive_kg').each(function(index, element)
            {
                var current_id=parseInt($(this).attr('data-current-id'));
                quantity_total_kg+=parseFloat($('#quantity_receive_kg_'+current_id).html().replace(/,/g,''));
            });
            $('#lbl_quantity_total_receive_kg').html(number_format(quantity_total_kg,3));
        })
    })
</script>