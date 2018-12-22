<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
/*if((isset($CI->permissions['action7']) && ($CI->permissions['action7']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );
}*/
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));


?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_forward');?>" method="post">
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
        echo $CI->load->view("info_basic",'',true);
        echo $CI->load->view("info_basic",array('accordion'=>array('header'=>'+LC Info','div_id'=>'accordion_lc_info','data'=>$info_lc)),true);
        ?>
        <div class="clearfix"></div>
        <div style="overflow-x: auto;" class="row show-grid">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="widget-header text-center" colspan="21">LC (<?php echo Barcode_helper::get_barcode_lc($item['id']);?>) Product & Price Details :: (Forwarded: <?php echo $item['status_open_forward']?>)</th>
                </tr>
                <tr>
                    <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                    <th class="label-info" style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th class="label-info text-center" style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                    <th class="bg-danger text-right" style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                    <th class="bg-danger text-right" style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_QUANTITY_OPEN_KG'); ?></th>
                    <th class="bg-danger text-right" style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_UNIT');?></th>
                    <th class="bg-danger text-right" style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_TOTAL');?></th>
                </tr>
                </thead>
                <tbody id="items_container">
                <?php
                $quantity_open_total=0;
                $quantity_open_kg=0;
                $price_open_currency=0;
                foreach($items as $index=>$value)
                {
                    if($value['pack_size_id']==0)
                    {
                        $quantity_open_kg=$value['quantity_open'];
                    }
                    else
                    {
                        $quantity_open_kg=(($value['quantity_open']*$value['pack_size'])/1000);
                    }
                    $price_open_currency=($value['quantity_open']*$value['price_unit_currency']);
                    $quantity_open_total+=$value['quantity_open'];
                    ?>
                    <tr>
                        <td><label><?php echo $value['crop_name'];?></label></td>
                        <td><label><?php echo $value['crop_type_name'];?></label></td>
                        <td><label><?php echo $value['variety_name']; ?> (<?php echo $value['variety_name_import']; ?>)</label></td>
                        <td class="text-center"> <label><?php if($value['pack_size_id']==0){echo 'Bulk';}else{echo $value['pack_size'];} ?></label></td>
                        <td class="text-right"><label><?php echo $value['quantity_open']; ?></label></td>
                        <td class="text-right"><label><?php echo number_format($quantity_open_kg,3,'.',''); ?></label></td>
                        <td class="text-right"><label><?php echo number_format($value['price_unit_currency'],2); ?></label></td>
                        <td class="text-right">
                            <label class="control-label price_open_currency" id="price_open_currency_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                <?php echo number_format($price_open_currency,2); ?>
                            </label>
                        </td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="4" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL_KG')?></th>
                    <th class="text-right"><label class="control-label"><?php echo $quantity_open_total;?></label></th>
                    <th class="text-right"><label class="control-label"><?php echo number_format(($item['quantity_open_kg']),3,'.','');?></label></th>
                    <th class="text-right"><?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_TOTAL')?></th>
                    <th class="text-right"><label class="control-label"><?php echo number_format($item['price_open_variety_currency'],2);?></label></th>
                </tr>
                <tr>
                    <th colspan="7" class="text-right"><?php echo $CI->lang->line('LABEL_PRICE_OPEN_OTHER_CURRENCY');?></th>
                    <th class="text-right">
                        <label class="control-label"><?php echo number_format($item['price_open_other_currency'],2);?></label>
                    </th>
                </tr>
                <tr>
                    <th colspan="7" class="text-right"><?php echo $CI->lang->line('LABEL_GRAND_TOTAL_CURRENCY');?></th>
                    <th class="text-right">
                        <label class="control-label" id="lbl_price_total_currency"> <?php echo number_format(($item['price_open_other_currency']+$item['price_open_variety_currency']),2)?></label>
                    </th>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">AWB Date<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[date_awb]" id="date_awb" class="form-control datepicker date_large" value="" readonly/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">AWB Number<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[awb_number]" id="awb_number" class="form-control" value="" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_LC_FORWARD');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status_open_forward" class="form-control" name="item[status_open_forward]">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <option value="<?php echo $this->config->item('system_status_yes')?>"><?php echo $this->config->item('system_status_yes')?></option>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-sm-4 col-xs-4">
                <div class="action_button">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form" data-message-confirm="Are You Sure LC Forward?">Forward</button>
                </div>
            </div>
            <div class="col-sm-4 col-xs-4">

            </div>
        </div>

    </div>
    <div class="clearfix"></div>
</form>
<script>
    $(document).ready(function()
    {
        system_off_events();
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        //$(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+2"});
        $(".date_large").datepicker({dateFormat : display_date_format});
    });
</script>