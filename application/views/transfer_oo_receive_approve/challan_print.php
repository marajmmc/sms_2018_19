<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK").' to Pending List',
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK").' to All list',
    'href'=>site_url($CI->controller_url.'/index/list_all')
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
$result=Query_helper::get_info($CI->config->item('table_system_setup_print'),'*',array('controller ="' .$this->controller_url.'"','method ="challan_print"'),1);
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
    $quantity_approve_total=0;
    $quantity_approve_total_kg=0;
    for($page=0;$page<$num_pages;$page++)
    {
        ?>
        <div class="page page_no_<?php echo $page; ?>" style="width:<?php echo $width;?>px;height:<?php echo $height; ?>px;position: relative;">
            <img src="<?php echo $header_image;  ?>" style="width: 100%">
            <div class="row show-grid">
                <div class="col-xs-6">
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right">Challan No:</label>
                        </div>
                        <div class="col-xs-6">
                            <?php echo Barcode_helper::get_barcode_transfer_outlet_to_outlet($item['id']);?>
                        </div>
                    </div>
                    <div class="row show-grid">
                        <div class="col-xs-6 text-right">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_BARCODE');?>:</label>
                        </div>
                        <div class="col-xs-6">
                            <img src="<?php echo site_url('barcode/index/transfer_outlet_to_outlet/'.$item['id']);  ?>">
                        </div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right">Challan Date:</label>
                        </div>
                        <div class="col-xs-6">
                            <?php echo System_helper::display_date($item['date_challan']); ?>
                        </div>
                    </div>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right">Courier Name: </label>
                        </div>
                        <div class="col-xs-6">
                            <?php echo $item['courier_name']; ?>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="row show-grid">
                        <div class="col-xs-3">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME');?> Name: </label>
                        </div>
                        <div class="col-xs-9">
                            <?php echo $item['outlet_name']; ?><br/>
                            <?php
                            if($item['place_destination'])
                            {
                                echo $item['place_destination'].'<br />';
                            }
                            ?>
                            <?php echo $item['outlet_phone']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <table style="width:<?php echo $width;?>px;" class="system_table_report_container">
                <thead>
                <tr>
                    <th rowspan="2" class="text-right" style="width: 30px;"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                    <th rowspan="2" style="width: 100px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th rowspan="2" style="width: 100px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                    <th rowspan="2" style="width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th rowspan="2" class="text-right" style="width: 30px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                    <th colspan="2" class="text-center"><?php echo $CI->lang->line('LABEL_QUANTITY'); ?></th>
                </tr>
                <tr>
                    <th class="text-right" style="width: 80px;"><?php echo $CI->lang->line('LABEL_PACK');?></th>
                    <th class="text-right" style="width: 80px;"><?php echo $CI->lang->line('LABEL_KG');?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                for($index=$page*$row_per_page;($index<(($page+1)*$row_per_page))&&($index<sizeof($items));$index++)
                {
                    $data=$items[$index];
                    $quantity_approve_total+=$data['quantity_approve'];
                    $quantity_approve_total_kg+=(($data['quantity_approve']*$data['pack_size'])/1000);
                    ?>
                    <tr>
                        <td class="text-right"><?php echo $index+1;?></td>
                        <td><?php echo $data['crop_name'];?></td>
                        <td><?php echo $data['crop_type_name'];?></td>
                        <td><?php echo $data['variety_name'];?></td>
                        <td class="text-right"><?php echo $data['pack_size'];?></td>
                        <td class="text-right"><?php echo $data['quantity_approve'];?></td>
                        <td class="text-right"><?php echo number_format((($data['quantity_approve']*$data['pack_size'])/1000),3,'.','');?></td>
                    </tr>
                    <?php
                    if($total_records==$index+1)
                    {
                        ?>
                        <tr>
                            <td  class="text-right" colspan="5"><label class="control-label"><?php echo $CI->lang->line('LABEL_TOTAL');?></label></td>
                            <td class="text-right"><label class="control-label"><?php echo $quantity_approve_total;?></label></td>
                            <td class="text-right"><label class="control-label"><?php echo number_format($quantity_approve_total_kg,3,'.','');?></label></td>
                        </tr>
                        <?php
                        if($item['remarks_challan'])
                        {
                            ?>
                            <tr>
                                <td colspan="21">
                                    <strong><?php echo $CI->lang->line('LABEL_REMARKS_CHALLAN');?>: </strong><?php echo nl2br($item['remarks_challan']);?>
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