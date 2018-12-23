<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array
(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);

$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/release_complete/'.$item['id'])
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_release_complete');?>" method="post">
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
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_LC_RELEASE');?> </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo nl2br($item['remarks_release']);?></label>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row show-grid">
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="widget-header text-center" colspan="21">LC (<?php echo Barcode_helper::get_barcode_lc($item['id']);?>) Product & Price Details  :: ( Release Status: <?php echo $item['status_release']?> )</th>
                    </tr>
                    <tr>
                        <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                        <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                        <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_UNIT');?></th>
                        <th class="label-primary text-center" colspan="3"><?php echo $CI->lang->line('LABEL_QUANTITY_ORDER');?></th>
                        <th class="label-warning text-center" colspan="3"><?php echo $CI->lang->line('LABEL_QUANTITY_SUPPLY');?></th>
                    </tr>
                    <tr>
                        <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_PACK'); ?>/<?php echo $CI->lang->line('LABEL_KG');?></th>
                        <th class="label-primary text-center"><?php echo $CI->lang->line('KG');?></th>
                        <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_TOTAL');?></th>

                        <th class="label-warning text-center"><?php echo $CI->lang->line('LABEL_PACK'); ?>/<?php echo $CI->lang->line('LABEL_KG');?></th>
                        <th class="label-warning text-center"><?php echo $CI->lang->line('KG');?></th>
                        <th class="label-warning text-center"><?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_TOTAL');?></th>
                    </tr>
                    </thead>
                    <?php
                    if(!empty($items))
                    {
                        ?>
                        <tbody>
                        <?php
                        $quantity_total_open=0;
                        $quantity_total_open_kg=0;
                        $quantity_total_release=0;
                        $quantity_total_release_kg=0;
                        foreach($items as $index=>$data)
                        {
                            $quantity_release=$data['quantity_release'];
                            $price_release_currency=($data['quantity_release']*$data['price_unit_currency']);
                            if($data['pack_size_id']==0)
                            {
                                $quantity_open_kg=$data['quantity_open'];
                                $quantity_release_kg=$quantity_release;
                            }
                            else
                            {
                                $quantity_open_kg=(($data['pack_size']*$data['quantity_open'])/1000);
                                $quantity_release_kg=(($data['pack_size']*$quantity_release)/1000);
                            }
                            $quantity_total_open+=$data['quantity_open'];
                            $quantity_total_open_kg+=$quantity_open_kg;
                            $quantity_total_release+=$quantity_release;
                            $quantity_total_release_kg+=$quantity_release_kg;
                            ?>
                            <tr>
                                <td><strong class="text-success"><?php echo $data['variety_name']?> (<?php echo $data['variety_name_import']?>)</strong></td>
                                <td class="text-center"> <?php if($data['pack_size']==0){echo "Bulk";}else{echo $data['pack_size'];}?></td>
                                <td class="text-center"><?php echo $data['price_unit_currency']?></td>
                                <td class="text-right"><label class="control-label"><?php echo $data['quantity_open']?></label></td>
                                <td class="text-right"><label class="control-label"><?php echo number_format($quantity_open_kg,3,'.','')?></label></td>
                                <td class="text-right"><label class="control-label"><?php echo number_format(($data['quantity_open']*$data['price_unit_currency']),2)?></label></td>
                                <td class="text-right"><label class="control-label"><?php echo $quantity_release; ?></label></td>
                                <td class="text-right"><label class="control-label"><?php echo number_format($quantity_release_kg,3,'.',''); ?></label></td>
                                <td class="text-right"><label class="control-label"><?php echo number_format($price_release_currency,2); ?></label></td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="3" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL_KG')?> & <?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_TOTAL')?></th>
                            <th class="text-right"><label class="control-label"><?php echo $quantity_total_open;?></label></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($quantity_total_open_kg,3,'.','');?></label></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($item['price_open_variety_currency'],2);?></label></th>
                            <th class="text-right"><label class="control-label"><?php echo $quantity_total_release;?></label></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($quantity_total_release_kg,3,'.','');?></label></th>
                            <th class="text-right"><label class="control-label"> <?php echo number_format($item['price_release_variety_currency'],2); ?></label></th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right"><?php echo $CI->lang->line('LABEL_OTHER_COST_CURRENCY')?></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($item['price_open_other_currency'],2)?></label></th>
                            <th colspan="2">&nbsp;</th>
                            <th class="text-right"> <?php echo number_format($item['price_release_other_currency'],2); ?> </th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right"><?php echo $CI->lang->line('LABEL_GRAND_TOTAL_CURRENCY')?></th>
                            <th class="text-right">
                                <label class="control-label"><?php echo number_format(($item['price_open_other_currency']+$item['price_open_variety_currency']),2);?></label>
                            </th>
                            <th colspan="2">&nbsp;</th>
                            <th class="text-right"> <label class="control-label"><?php echo number_format(($item['price_release_other_currency']+$item['price_release_variety_currency']),2);?></label></th>
                        </tr>
                        <tr>
                            <th colspan="8" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL_TAKA')?></th>
                            <th class="text-right"><?php echo number_format($item['price_release_other_variety_taka'],2);?></th>
                        </tr>
                        </tfoot>
                    <?php
                    }
                    else
                    {
                        ?>
                        <tfoot>
                        <tr>
                            <td class="widget-header text-center" colspan="21"><strong><?php echo $CI->lang->line('NO_DATA_FOUND');?></strong></td>
                        </tr>
                        </tfoot>
                    <?php
                    }
                    ?>
                </table>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">LC Release Date<span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[date_release]" id="date_release" class="form-control datepicker date_large" value="" readonly/>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">LC Release<span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <select id="status_release" class="form-control" name="item[status_release]">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                        <option value="<?php echo $this->config->item('system_status_complete')?>"><?php echo $this->config->item('system_status_complete')?></option>
                    </select>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">

                </div>
                <div class="col-sm-4 col-xs-4">
                    <div class="action_button">
                        <button id="button_action_save" type="button" class="btn" data-form="#save_form" data-message-confirm="Are You Want To LC Release?">LC Released</button>
                    </div>
                </div>
                <div class="col-sm-4 col-xs-4">

                </div>
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