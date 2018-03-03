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
            <div class="col-xs-6">
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ID');?>:</label>
                    </div>
                    <div class="col-xs-6">
                        <?php echo Barcode_helper::get_barcode_convert_bulk_to_packet($item['id']);?>
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
            </div>
            <div class="col-xs-6">
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_CONVERT');?>:</label>
                    </div>
                    <div class="col-xs-6">
                        <?php echo System_helper::display_date($item['date_convert']);?>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">&nbsp;</div>
                    <div class="col-xs-6">&nbsp;</div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-xs-6">
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CROP_NAME');?>:</label>
                    </div>
                    <div class="col-xs-6">
                        <?php echo $item['crop_name']?>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CROP_TYPE_NAME');?>:</label>
                    </div>
                    <div class="col-xs-6">
                        <?php echo $item['crop_type_name']?>
                    </div>
                </div>
            </div>
        </div>

        <table style="width:<?php echo $width;?>px;" class="system_table_report_container">
            <thead>
            <tr>
                <th>Variety Name</th>
                <th><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME_SOURCE');?></th>
                <th class="text-right"><?php echo $CI->lang->line('LABEL_QUANTITY');?></th>
                <th><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME_DESTINATION');?></th>
                <th class="text-right"><?php echo $CI->lang->line('LABEL_PACK_SIZE');?></th>
                <th class="text-right">Quantity (Packet)</th>
                <?php
                if($item['quantity_master_foil_actual']>0)
                {
                    ?>
                    <th class="text-right">Master Foil (KG)</th>
                <?php
                }
                ?>
                <?php
                if($item['quantity_foil_actual']>0)
                {
                    ?>
                    <th class="text-right">Common Foil (KG)</th>
                <?php
                }
                ?>
                <?php
                if($item['quantity_sticker_actual']>0)
                {
                    ?>
                    <th class="text-right">Sticker (pcs)</th>
                <?php
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo $item['variety_name'];?></td>
                <td><?php echo $item['warehouse_name_source'];?></td>
                <td class="text-right"><?php echo $item['quantity_convert'];?></td>
                <td><?php echo $item['warehouse_name_destination'];?></td>
                <td class="text-right"><?php echo $item['pack_size'];?></td>
                <td class="text-right"><?php echo $item['quantity_packet_actual']?></td>
                <?php
                if($item['quantity_master_foil_actual']>0)
                {
                    ?>
                    <td class="text-right"><?php echo number_format($item['quantity_master_foil_actual'],3,'.','');?></td>
                <?php
                }
                ?>
                <?php
                if($item['quantity_foil_actual']>0)
                {
                    ?>
                    <td class="text-right"><?php echo number_format($item['quantity_foil_actual'],3,'.','');?></td>
                <?php
                }
                ?>
                <?php
                if($item['quantity_sticker_actual']>0)
                {
                    ?>
                    <td class="text-right"><?php echo $item['quantity_sticker_actual']?></td>
                <?php
                }
                ?>
            </tr>
            <?php
            if($item['remarks'])
            {
                ?>
                <tr>
                    <td colspan="21">
                        <strong><?php echo $CI->lang->line('LABEL_REMARKS');?>: </strong><?php echo nl2br($item['remarks']);?>
                    </td>
                </tr>
            <?php
            }
            ?>
            <?php
            if($item['remarks_delete'])
            {
                ?>
                <tr>
                    <td colspan="21">
                        <strong>Delete Reason: </strong><?php echo nl2br($item['remarks_delete']);?>
                    </td>
                </tr>
            <?php
            }
            ?>
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