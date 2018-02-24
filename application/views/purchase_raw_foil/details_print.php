<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'onClick'=>"window.print()"
    );
}

$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<?php
$width=8.27*100;
$height=11.69*100/2;
$header_image=base_url('images/print/header.jpg');
$footer_image=base_url('images/print/footer.jpg');
$result=Query_helper::get_info($CI->config->item('table_system_setup_print'),'*',array('controller ="' .$this->controller_url.'"','method ="details_print"'),1);
if($result)
{
    $width=$result['width']*100;
    $height=$result['height']*100;
    $header_image=$CI->config->item('system_base_url_picture_setup_print').$result['image_header_location'];
    $footer_image=$CI->config->item('system_base_url_picture_setup_print').$result['image_footer_location'];
}
?>

<div id="system_print_container" style="width:<?php echo $width;?>px;">
    <div style="width:<?php echo $width;?>px;height:<?php echo $height; ?>px;position: relative;">
        <img src="<?php echo $header_image;  ?>" style="width: 100%">
        <div class="row show-grid">
            <div class="col-xs-4">
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ID');?>:</label>
                    </div>
                    <div class="col-xs-6">
                        <?php echo Barcode_helper::get_barcode_raw_foil_purchase($item['id']);?>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6 text-right">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_BARCODE');?>:</label>
                    </div>
                    <div class="col-xs-6">
                        <img src="<?php echo site_url('barcode/index/raw_master_purchase/'.$item['id']);  ?>">
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_SUPPLIER_NAME');?>:</label>
                    </div>
                    <div class="col-xs-6">
                        <?php echo $item['supplier_name'];?>
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CHALLAN_NUMBER');?>:</label>
                    </div>
                    <div class="col-xs-6">
                        <?php echo $item['challan_number']; ?>
                    </div>
                </div>

            </div>
            <div class="col-xs-4">
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_CHALLAN');?>:</label>
                    </div>
                    <div class="col-xs-6">
                        <?php echo System_helper::display_date($item['date_challan']);?>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_RECEIVE');?>:</label>
                    </div>
                    <div class="col-xs-6">
                        <?php echo System_helper::display_date($item['date_receive']);?>
                    </div>
                </div>

            </div>
        </div>

        <table style="width:<?php echo $width;?>px;" class="system_table_report_container">
            <thead>
            <tr>
                <th rowspan="2" style="width: 5px"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                <th rowspan="2" class="text-center"><?php echo $CI->lang->line('LABEL_NUMBER_OF_REEL');?></th>
                <th class="text-center" colspan="3"><?php echo $CI->lang->line('LABEL_QUANTITY');?></th>
            </tr>
            <tr>
                <th class="text-right"><?php echo $CI->lang->line('LABEL_QUANTITY_SUPPLY');?> (<?php echo $CI->lang->line('LABEL_KG');?>)</th>
                <th class="text-right"><?php echo $CI->lang->line('LABEL_QUANTITY_RECEIVE');?> (<?php echo $CI->lang->line('LABEL_KG');?>)</th>
                <th class="text-right"><?php echo $CI->lang->line('LABEL_QUANTITY_DIFFERENCE');?> (<?php echo $CI->lang->line('LABEL_KG');?>)</th>
            </tr>
            </thead>
            <tbody>

                <tr>
                    <td>1</td>
                    <td class="text-right"><?php echo $item['number_of_reel'];?></td>
                    <td class="text-right"><?php echo number_format($item['quantity_supply'],3,'.','');?></td>
                    <td class="text-right"><?php echo number_format($item['quantity_receive'],3,'.','');?></td>
                    <td class="text-right"><?php echo number_format(($item['quantity_receive']-$item['quantity_supply']),3,'.','');?></td>
                </tr>
                <tr>
                    <td colspan="1"><?php echo $this->lang->line('LABEL_TOTAL')?></td>
                    <td class="text-right"><?php echo $item['number_of_reel'];?></td>
                    <td class="text-right"><?php echo number_format($item['quantity_supply'],3,'.','');?></td>
                    <td class="text-right"><?php echo number_format($item['quantity_receive'],3,'.','');?></td>
                    <td class="text-right"><?php echo number_format(($item['quantity_receive']-$item['quantity_supply']),3,'.','');?></td>
                </tr>


<!--                <tr>-->
<!--                    <td colspan="5" class="text-right"><label class="control-label">--><?php //echo $this->lang->line('LABEL_TOTAL')?><!--</label></td>-->
<!--                    <td class="text-right"><label class="control-label">--><?php //echo $total_number_of_reel;?><!--</label></td>-->
<!--                    <td class="text-right"><label class="control-label">--><?php //echo number_format($quantity_total_supply,3,'.','');?><!--</label></td>-->
<!--                    <td class="text-right"><label class="control-label">--><?php //echo number_format($quantity_total_receive,3,'.','');?><!--</label></td>-->
<!--                    <td class="text-right"><label class="control-label">--><?php //echo number_format($quantity_total_difference,3,'.','');?><!--</label></td>-->
<!--                </tr>-->

                <?php if($item['remarks']){?>
                    <tr>
                        <td colspan="21">
                            <strong><?php echo $CI->lang->line('LABEL_REMARKS');?>: </strong><?php echo nl2br($item['remarks']);?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <img src="<?php echo $footer_image;  ?>" style="width: 100%;position: absolute;left 0px;bottom: 0px;">
    </div>
</div>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>