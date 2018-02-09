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
$result=Query_helper::get_info($CI->config->item('table_sms_setup_print'),'*',array('controller ="' .$this->controller_url.'"','method ="details_print"'),1);
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
        for($page=0;$page<$num_pages;$page++)
        {
            ?>
            <div class="page page_no_<?php echo $page; ?>" style="width:<?php echo $width;?>px;height:<?php echo $height; ?>px;position: relative;">
                <img src="<?php echo $header_image;  ?>" style="width: 100%">
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <div class="row show-grid">
                            <div class="col-xs-6">
                                <label class="control-label pull-right">ID :</label>
                            </div>
                            <div class="col-xs-6">
                                <?php echo Barcode_helper::get_barcode_stock_out($item['id']);?>
                            </div>
                        </div>
                        <div class="row show-grid">
                            <div class="col-xs-3">

                            </div>
                            <div class="col-xs-9">
                                <img src="<?php echo site_url('barcode/index/stock_in/'.$item['id']);  ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="row show-grid">
                            <div class="col-xs-6">
                                <label class="control-label pull-right">Purpose :</label>
                            </div>
                            <div class="col-xs-6">
                                <?php echo $item['purpose']; ?>
                            </div>
                        </div><div class="row show-grid">
                            <div class="col-xs-6">
                                <label class="control-label pull-right">Date :</label>
                            </div>
                            <div class="col-xs-6">
                                <?php echo System_helper::display_date($item['date_stock_in']);?>
                            </div>
                        </div>
                    </div>
                </div>
                <table style="width:<?php echo $width;?>px;">
                    <thead>
                        <tr>
                            <th style="min-width: 50px;border: 1px solid #000000;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                            <th style="min-width: 50px;border: 1px solid #000000;"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                            <th style="min-width: 50px;border: 1px solid #000000;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                            <th style="min-width: 50px;border: 1px solid #000000;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                            <th style="min-width: 50px;border: 1px solid #000000;"><?php echo $CI->lang->line('LABEL_WAREHOUSE'); ?></th>
                            <th class="text-right" style="min-width: 50px;border: 1px solid #000000;"><?php echo $CI->lang->line('LABEL_QUANTITY'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    for($index=$page*$row_per_page;($index<(($page+1)*$row_per_page))&&($index<sizeof($items));$index++)
                    {
                        ?>
                        <tr>
                            <td style="border: 1px solid #000000">
                                <label><?php echo $items[$index]['crop_name']; ?></label>
                            </td>
                            <td style="border: 1px solid #000000">
                                <label><?php echo $items[$index]['crop_type_name']; ?></label>
                            </td>
                            <td style="border: 1px solid #000000">
                                <label><?php echo $items[$index]['variety_name']; ?></label>
                            </td>
                            <td style="border: 1px solid #000000">
                                <label><?php if($items[$index]['pack_size_id']==0){echo 'Bulk';}else{echo $items[$index]['pack_size_name'];} ?></label>
                            </td>
                            <td style="border: 1px solid #000000">
                                <label><?php echo $items[$index]['ware_house_name']; ?></label>
                            </td>
                            <td style="border: 1px solid #000000" class="text-right">
                                <label><?php echo $items[$index]['quantity']; ?></label>
                            </td>
                        </tr>
                    <?php
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