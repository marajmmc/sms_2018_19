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
$width=8.27*100;
$height=11.69*100/2;
$row_per_page=20;
$header_image=base_url('images/print/header.jpg');
$footer_image=base_url('images/print/footer.jpg');
$result=Query_helper::get_info($CI->config->item('table_system_setup_print'),'*',array('controller ="' .$this->controller_url.'"','method ="details_print"'),1);
if($result)
{
    $width=$result['width']*100;
    $height=$result['height']*100;
    $row_per_page=$result['row_per_page'];
    $header_image=$CI->config->item('system_base_url_picture').$result['image_header_location'];
    $footer_image=$CI->config->item('system_base_url_picture').$result['image_footer_location'];
}

$total_records=sizeof($items);
$num_pages=ceil($total_records/$row_per_page);
?>
<div id="system_print_container" style="width:<?php echo $width;?>px;">
    <?php
    $quantity_total=0;
    for($page=0;$page<$num_pages;$page++)
    {
        ?>
        <div class="page page_no_<?php echo $page; ?>" style="width:<?php echo $width;?>px;height:<?php echo $height; ?>px;position: relative;">
            <img src="<?php echo $header_image;  ?>" style="width: 100%">
            <div class="row show-grid">
                <div class="col-xs-6">
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ID');?>:</label>
                        </div>
                        <div class="col-xs-6">
                            <?php echo Barcode_helper::get_barcode_raw_master_stock_out($item['id']);?>
                        </div>
                    </div>
                    <div class="row show-grid">
                        <div class="col-xs-6 text-right">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_BARCODE');?>:</label>
                        </div>
                        <div class="col-xs-6">
                            <img src="<?php echo site_url('barcode/index/raw_master_stock_out/'.$item['id']);  ?>">
                        </div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PURPOSE');?>:</label>
                        </div>
                        <div class="col-xs-6">
                            <?php echo $item['purpose']; ?>
                        </div>
                    </div>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE');?>:</label>
                        </div>
                        <div class="col-xs-6">
                            <?php echo System_helper::display_date($item['date_stock_out']); ?>
                        </div>
                    </div>
                </div>
            </div>
            <table style="width:<?php echo $width;?>px;" class="system_table_report_container">
                <thead>
                <th class="text-right" style="width: 30px;"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                <th><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                <th><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                <th><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                <th class="text-right"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                <th class="text-right"><?php echo $CI->lang->line('LABEL_QUANTITY'); ?> (<?php echo $CI->lang->line('LABEL_KG');?>)</th>
                </thead>
                <tbody>
                <?php
                for($index=$page*$row_per_page;($index<(($page+1)*$row_per_page))&&($index<sizeof($items));$index++)
                {
                    $data=$items[$index];
                    $quantity_total+=$data['quantity'];
                    ?>
                    <tr>
                        <td class="text-right"><?php echo $index+1;?></td>
                        <td><?php echo $data['crop_name'];?></td>
                        <td><?php echo $data['crop_type_name'];?></td>
                        <td><?php echo $data['variety_name'];?></td>
                        <td class="text-right"><?php echo $data['pack_size'];?></td>
                        <td class="text-right"><?php echo number_format($data['quantity'],3,'.','');?></td>
                    </tr>
                    <?php
                    if($total_records==$index+1)
                    {
                        ?>
                        <tr>
                            <td  class="text-right" colspan="5"><label class="control-label"><?php echo $CI->lang->line('LABEL_TOTAL_KG');?></label></td>
                            <td class="text-right"><label class="control-label"><?php echo number_format($quantity_total,3,'.','');?></label></td>
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
                    }
                }
                ?>
                </tbody>
            </table>
            <img src="<?php echo $footer_image;  ?>" style="width: 100%;position: absolute;left 0px;bottom: 0px;">

        </div>
    <?php
    }
    ?>

</div>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>