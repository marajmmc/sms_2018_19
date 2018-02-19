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
$row_per_page=20;
$header_image=base_url('images/print/header.jpg');
$footer_image=base_url('images/print/footer.jpg');
$result=Query_helper::get_info($CI->config->item('table_system_setup_print'),'*',array('controller ="' .$this->controller_url.'"','method ="details_print"'),1);
if($result)
{
    $width=$result['width']*100;
    $height=$result['height']*100;
    $row_per_page=$result['row_per_page'];
    $header_image=$CI->config->item('system_base_url_picture_setup_print').$result['image_header_location'];
    $footer_image=$CI->config->item('system_base_url_picture_setup_print').$result['image_footer_location'];
}

$total_records=sizeof($items);
$num_pages=ceil($total_records/$row_per_page);

?>

<div id="system_print_container" style="width:<?php echo $width;?>px;">
    <?php
        $quantity_total_kg=0;
        for($page=0;$page<$num_pages;$page++)
        {
            ?>
            <div class="page page_no_<?php echo $page; ?>" style="width:<?php echo $width;?>px;height:<?php echo $height; ?>px;position: relative;">
                <img src="<?php echo $header_image;  ?>" style="width: 100%">
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <div class="row show-grid">
                            <div class="col-xs-6">
                                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ID');?>:</label>
                            </div>
                            <div class="col-xs-6">
                                <?php echo Barcode_helper::get_barcode_stock_in($item['id']);?>
                            </div>
                        </div>
                        <div class="row show-grid">
                            <div class="col-xs-6 text-right">
                                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_BARCODE');?>:</label>
                            </div>
                            <div class="col-xs-6">
                                <img src="<?php echo site_url('barcode/index/stock_in/'.$item['id']);  ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="row show-grid">
                            <div class="col-xs-6">
                                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PURPOSE');?> :</label>
                            </div>
                            <div class="col-xs-6">
                                <?php echo $item['purpose']; ?>
                            </div>
                        </div><div class="row show-grid">
                            <div class="col-xs-6">
                                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE');?> :</label>
                            </div>
                            <div class="col-xs-6">
                                <?php echo System_helper::display_date($item['date_stock_in']);?>
                            </div>
                        </div>
                    </div>
                </div>
                <table style="width:<?php echo $width;?>px;" class="system_table_report_container">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 5px;"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                            <!--<th rowspan="2"><?php /*echo $CI->lang->line('LABEL_CROP_NAME'); */?></th>-->
                            <th rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                            <th rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                            <th rowspan="2" style="width: 5px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                            <th rowspan="2"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME'); ?></th>
                            <th colspan="2" class="text-center"><?php echo $CI->lang->line('LABEL_QUANTITY'); ?></th>
                        </tr>
                        <tr>
                            <th class="text-right" ><?php echo $CI->lang->line('LABEL_PACK'); ?></th>
                            <th class="text-right" ><?php echo $CI->lang->line('LABEL_KG'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $serial=0;
                    for($index=$page*$row_per_page;($index<(($page+1)*$row_per_page))&&($index<sizeof($items));$index++)
                    {
                        $serial=($index+1);
                        $quantity_total_kg+=($items[$index]['quantity']/1000);
                        ?>
                        <tr>
                            <td><?php echo $serial; ?></td>
                            <td><?php echo $items[$index]['crop_name']; ?></td>
                            <!--<td><?php /*echo $items[$index]['crop_type_name']; */?></td>-->
                            <td><?php echo $items[$index]['variety_name']; ?></td>
                            <td><?php if($items[$index]['pack_size_id']==0){echo 'Bulk';}else{echo $items[$index]['pack_size'];} ?></td>
                            <td><?php echo $items[$index]['ware_house_name']; ?></td>
                            <td class="text-right"><?php if($items[$index]['pack_size_id']==0){echo 0;}else{echo $items[$index]['quantity'];} ?></td>
                            <td class="text-right"> <?php echo $items[$index]['quantity']/1000; ?> </td>
                        </tr>
                    <?php
                        if($total_records==$index+1)
                        {
                            ?>
                            <tr>
                                <td colspan="5" class="text-right"><label class="control-label"><?php echo $this->lang->line('LABEL_TOTAL')?></label></td>
                                <td class="text-right"><label class="control-label"><?php echo number_format($item['quantity_total'],3,'.','');?></label></td>
                                <td class="text-right"><label class="control-label"><?php echo number_format($quantity_total_kg,3,'.','');?></label></td>
                            </tr>
                            <?php
                            if($item['remarks'])
                            {
                                ?>
                                <tr>
                                    <td colspan="21">
                                        <strong><?php echo $CI->lang->line('LABEL_REMARKS');?>: </strong><?php echo $item['remarks'];?>
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