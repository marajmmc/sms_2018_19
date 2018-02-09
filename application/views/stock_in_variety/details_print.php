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
//$width=8.27*100;
$height=8.27*100;
//$height=11.69*100/2;
$width=11.69*100/2;
$total_records=sizeof($items);
$row_per_page=20;
$num_pages=ceil($total_records/$row_per_page);
$header_image=base_url('images/print/header.jpg');
$footer_image=base_url('images/print/footer.jpg');
?>

<div id="system_print_container" style="width:<?php echo $width;?>px;">
    <?php
        for($page=0;$page<$num_pages;$page++)
        {
            ?>
            <div class="page page_no_<?php echo $page; ?>" style="width:<?php echo $width;?>px;height:<?php echo $height; ?>px;position: relative;">
                <img src="<?php echo $header_image;  ?>" style="width: 100%">
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