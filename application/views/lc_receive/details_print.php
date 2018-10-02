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
    $header_image=$CI->config->item('system_base_url_picture').$result['image_header_location'];
    $footer_image=$CI->config->item('system_base_url_picture').$result['image_footer_location'];
}

$total_records=sizeof($items);
$num_pages=ceil($total_records/$row_per_page);

?>
<div id="system_print_container" style="width:<?php echo $width;?>px;">
    <?php
    $quantity_total_release=0;
    $quantity_total_release_kg=0;
    $quantity_total_receive=0;
    $quantity_total_receive_kg=0;
    for($page=0;$page<$num_pages;$page++)
    {
        ?>
        <div class="page page_no_<?php echo $page; ?>" style="width:<?php echo $width;?>px;height:<?php echo $height; ?>px;position: relative;">
            <img src="<?php echo $header_image;  ?>" style="width: 100%">
            <div class="row show-grid">
                <div class="col-xs-4">
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ID');?>:</label>
                        </div>
                        <div class="col-xs-6">
                            <?php echo Barcode_helper::get_barcode_lc($item['id']);?>
                        </div>
                    </div>
                    <div class="row show-grid">
                        <div class="col-xs-6 text-right">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_BARCODE');?>:</label>
                        </div>
                        <div class="col-xs-6">
                            <img src="<?php echo site_url('barcode/index/lc/'.$item['id']);  ?>">
                        </div>
                    </div>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SUPPLIER_NAME');?>:</label>
                        </div>
                        <div class="col-xs-6">
                            <?php echo $item['principal_name'];?>
                        </div>
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_LC_NUMBER');?>:</label>
                        </div>
                        <div class="col-xs-6">
                            <?php echo $item['lc_number']; ?>
                        </div>
                    </div>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NUMBER_PACKING_LIST');?>:</label>
                        </div>
                        <div class="col-xs-6">
                            <?php echo $item['packing_list_number']; ?>
                        </div>
                    </div>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NUMBER_LOT');?>:</label>
                        </div>
                        <div class="col-xs-6">
                            <?php echo $item['lot_number']; ?>
                        </div>
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_RECEIVE');?>:</label>
                        </div>
                        <div class="col-xs-6">
                            <?php echo System_helper::display_date($item['date_receive']);?>
                        </div>
                    </div>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_PACKING_LIST');?>:</label>
                        </div>
                        <div class="col-xs-6">
                            <?php echo System_helper::display_date($item['date_packing_list']);?>
                        </div>
                    </div>
                </div>
            </div>
            <table style="width:<?php echo $width;?>px;" class="system_table_report_container">
                <thead>
                <tr>
                    <th rowspan="2" class="text-right" style="width: 30px;"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                    <th rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                    <th rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th rowspan="2"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                    <th rowspan="2"><?php echo $CI->lang->line('LABEL_CARTON_NUMBER')?></th>
                    <th rowspan="2"><?php echo $CI->lang->line('LABEL_CARTON_SIZE')?></th>
                    <th colspan="2" class="text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_SUPPLY');?></th>
                    <th colspan="2" class="text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_RECEIVE');?></th>
                    <th colspan="2" class="text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_DIFFERENCE');?></th>
                </tr>
                <tr>
                    <th class="text-right"><?php echo $CI->lang->line('LABEL_PACK'); ?>/<?php echo $CI->lang->line('LABEL_KG');?></th>
                    <th class="text-right"><?php echo $CI->lang->line('LABEL_KG');?></th>
                    <th class="text-right"><?php echo $CI->lang->line('LABEL_PACK'); ?>/<?php echo $CI->lang->line('LABEL_KG');?></th>
                    <th class="text-right"><?php echo $CI->lang->line('LABEL_KG');?></th>
                    <th class="text-right"><?php echo $CI->lang->line('LABEL_PACK'); ?>/<?php echo $CI->lang->line('LABEL_KG');?></th>
                    <th class="text-right"><?php echo $CI->lang->line('LABEL_KG');?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $serial=0;
                for($index=$page*$row_per_page;($index<(($page+1)*$row_per_page))&&($index<sizeof($items));$index++)
                {
                    $serial=($index+1);
                    $data=$items[$index];
                    if($data['pack_size_id']==0)
                    {
                        $quantity_release_kg=$data['quantity_release'];
                        $quantity_receive_kg=$data['quantity_receive'];
                    }
                    else
                    {
                        $quantity_release_kg=(($data['pack_size']*$data['quantity_release'])/1000);
                        $quantity_receive_kg=(($data['pack_size']*$data['quantity_receive'])/1000);
                    }
                    $quantity_total_release+=$data['quantity_release'];
                    $quantity_total_release_kg+=$quantity_release_kg;
                    $quantity_total_receive+=$data['quantity_receive'];;
                    $quantity_total_receive_kg+=$quantity_receive_kg;
                    ?>
                    <tr>
                        <td class="text-right"><?php echo $serial; ?></td>
                        <td><?php echo $data['crop_name']?></td>
                        <td><?php echo $data['crop_type_name']?></td>
                        <td><?php echo $data['variety_name']?> (<?php echo $data['variety_name_import']?>)</td>
                        <td class="text-center"> <?php if($data['pack_size']==0){echo "Bulk";}else{echo $data['pack_size'];}?></td>
                        <td class="text-right"><?php echo $data['carton_number_receive']; ?></td>
                        <td class="text-right"><?php echo $data['carton_size_receive']; ?></td>
                        <td class="text-right"><?php echo $data['quantity_release'];?></td>
                        <td class="text-right"><?php echo number_format($quantity_release_kg,3,'.','')?></td>
                        <td class="text-right"><?php echo $data['quantity_receive']; ?></td>
                        <td class="text-right" ><?php echo number_format($quantity_receive_kg,3,'.',''); ?></td>
                        <td class="text-right"><?php echo ($data['quantity_release']-$data['quantity_receive'])?></td>
                        <td class="text-right"><?php echo number_format(($quantity_release_kg-$quantity_receive_kg),3,'.','')?></td>
                    </tr>
                    <?php
                    if($total_records==$index+1)
                    {
                        ?>
                        <tr>
                            <td colspan="7" class="text-right"><label class="control-label"><?php echo $CI->lang->line('LABEL_TOTAL')?></label></td>
                            <td class="text-right"><label class="control-label"><?php echo $quantity_total_release;?></label></td>
                            <td class="text-right"><label class="control-label"><?php echo number_format($quantity_total_release_kg,3,'.','');?></label></td>
                            <td class="text-right"><label class="control-label"><?php echo $quantity_total_receive;?></label></td>
                            <td class="text-right"><label class="control-label"><?php echo number_format($quantity_total_receive_kg,3,'.','');?></label></td>
                            <td class="text-right"><label class="control-label"><?php echo ($quantity_total_release-$quantity_total_receive);?></label></td>
                            <td class="text-right"><label class="control-label"><?php echo number_format(($quantity_total_release_kg-$quantity_total_receive_kg),3,'.','');?></label></td>
                        </tr>
                        <?php
                        if($item['remarks_receive'])
                        {
                            ?>
                            <tr>
                                <td colspan="21">
                                    <strong><?php echo $CI->lang->line('LABEL_REMARKS');?>: </strong><?php echo nl2br($item['remarks_receive']);?>
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