<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form class="hidden-print" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_BARCODE');?> :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo Barcode_helper::get_barcode_stock_out($item['id']);?>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE');?> :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo System_helper::display_date($item['date_stock_in']);?>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="purpose" class="control-label pull-right"><?php echo $this->lang->line('LABEL_PURPOSE'); ?> :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo $item['purpose']; ?>
            </div>

        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="remarks" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?> :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
               <?php echo $item['remarks']; ?>
            </div>
        </div>

        <div style="overflow-x: auto;" class="row show-grid" id="order_items_container">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_WAREHOUSE'); ?></th>
                    <th class="text-right" style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CURRENT_STOCK'); ?></th>
                    <th class="text-right" style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_QUANTITY'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($stock_in_varieties as $index=>$si_variety)
                {
                    ?>
                    <tr>
                        <td>
                            <label><?php echo $si_variety['crop_name']; ?></label>
                        </td>
                        <td>
                            <label><?php echo $si_variety['crop_type_name']; ?></label>
                        </td>
                        <td>
                            <label><?php echo $si_variety['variety_name']; ?></label>
                        </td>
                        <td>
                            <label><?php if($si_variety['pack_size_id']==0){echo 'Bulk';}else{echo $si_variety['pack_size_name'];} ?></label>
                        </td>
                        <td>
                            <label><?php echo $si_variety['ware_house_name']; ?></label>
                        </td>
                        <td class="text-right">
                            <label><?php $current_stock=System_helper::get_variety_stock(array($si_variety['variety_id'])); if(isset($current_stock)){echo $current_stock[$si_variety['variety_id']][$si_variety['pack_size_id']][$si_variety['warehouse_id']]['current_stock'];}else{echo 0;}?></label>
                        </td>
                        <td class="text-right">
                            <label><?php echo $si_variety['quantity']; ?></label>
                        </td>
                    </tr>
                <?php
                }
                ?>
                </tbody>

            </table>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<?php
$width=800;
$header_image=base_url('images/print/header.jpg');
$footer_image=base_url('images/print/footer.jpg');
?>
<div id="system_report_container" style="width: <?php echo $width; ?>px;">
    <table>
        <thead>
        <tr>
            <th colspan="7">
                <img src="<?php echo $header_image;  ?>" style="width: 100%">

            </th>
        </tr>
<!--        <tr>-->
<!--            <th colspan="7">-->
<!--                <div class="row show-grid">-->
<!--                    <div class="col-xs-8">-->
<!--                        <div class="row show-grid">-->
<!--                            <div class="col-xs-6">-->
<!--                                <label class="control-label pull-right">Purpose :</label>-->
<!--                            </div>-->
<!--                            <div class="col-xs-6">-->
<!--                                --><?php //echo $item['purpose']; ?>
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="col-xs-4">-->
<!--                        <div class="row show-grid">-->
<!--                            <div class="col-xs-6">-->
<!--                                <label class="control-label pull-right">ID :</label>-->
<!--                            </div>-->
<!--                            <div class="col-xs-6">-->
<!--                                --><?php //echo Barcode_helper::get_barcode_stock_out($item['id']);?>
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="row show-grid">-->
<!--                            <div class="col-xs-6">-->
<!--                                <label class="control-label pull-right">Date :</label>-->
<!--                            </div>-->
<!--                            <div class="col-xs-6">-->
<!--                                --><?php //echo System_helper::display_date($item['date_stock_in']);?>
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </th>-->
<!--        </tr>-->
        <tr>
            <th style="min-width: 50px;border: 1px solid #000000;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
            <th style="min-width: 50px;border: 1px solid #000000;"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?></th>
            <th style="min-width: 50px;border: 1px solid #000000;"><?php echo $CI->lang->line('LABEL_VARIETY'); ?></th>
            <th style="min-width: 50px;border: 1px solid #000000;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
            <th style="min-width: 50px;border: 1px solid #000000;"><?php echo $CI->lang->line('LABEL_WAREHOUSE'); ?></th>
            <th class="text-right" style="min-width: 50px;border: 1px solid #000000;"><?php echo $CI->lang->line('LABEL_CURRENT_STOCK'); ?></th>
            <th class="text-right" style="min-width: 50px;border: 1px solid #000000;"><?php echo $CI->lang->line('LABEL_QUANTITY'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($stock_in_varieties as $index=>$si_variety)
        {
            ?>
            <tr>
                <td  style="border: 1px solid #000000">
                    <label><?php echo $si_variety['crop_name']; ?></label>
                </td>
                <td  style="border: 1px solid #000000">
                    <label><?php echo $si_variety['crop_type_name']; ?></label>
                </td>
                <td  style="border: 1px solid #000000">
                    <label><?php echo $si_variety['variety_name']; ?></label>
                </td>
                <td  style="border: 1px solid #000000">
                    <label><?php if($si_variety['pack_size_id']==0){echo 'Bulk';}else{echo $si_variety['pack_size_name'];} ?></label>
                </td>
                <td  style="border: 1px solid #000000">
                    <label><?php echo $si_variety['ware_house_name']; ?></label>
                </td>
                <td class="text-right"  style="border: 1px solid #000000">
                    <label><?php $current_stock=System_helper::get_variety_stock(array($si_variety['variety_id'])); if(isset($current_stock)){echo $current_stock[$si_variety['variety_id']][$si_variety['pack_size_id']][$si_variety['warehouse_id']]['current_stock'];}else{echo 0;}?></label>
                </td>
                <td class="text-right"  style="border: 1px solid #000000">
                    <label><?php echo $si_variety['quantity']; ?></label>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
        <tfoot style="bottom: 0;">
        <tr>
            <td colspan="7">
                <img src="<?php echo $footer_image;  ?>" style="width: 100%">
            </td>
        </tr>
        </tfoot>

    </table>
</div>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>