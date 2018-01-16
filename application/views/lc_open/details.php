<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_CLEAR"),
        'id'=>'button_action_clear',
        'data-form'=>'#save_form'
    );
}
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="col-md-12">
            <table class="table table-bordered table-responsive ">
                <thead>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_FISCAL_YEAR');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['fiscal_year_name']?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_MONTH');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo date("F", mktime(0, 0, 0,  $item['month_id'],1, 2000));?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_OPENING');?></label></th>
                    <th><label class="control-label"><?php echo System_helper::display_date($item['date_opening']);?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_EXPECTED');?></label></th>
                    <th><label class="control-label"><?php echo System_helper::display_date($item['date_expected']);?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRINCIPAL_NAME');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['principal_name'];?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_LC_NUMBER');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['lc_number'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CURRENCY_NAME');?></label></th>
                    <th><label class="control-label"><?php echo $item['currency_name'];?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CONSIGNMENT_NAME');?></label></th>
                    <th><label class="control-label"><?php echo $item['consignment_name'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_OTHER_COST_CURRENCY');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo number_format($item['other_cost_currency'],2);?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['remarks'];?></label></th>
                </tr>
                </thead>

            </table>
        </div>

        <div class="clearfix"></div>
        <div class="row show-grid">
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="widget-header text-center" colspan="21">LC (<?php echo $item['lc_number'];?>) Product & Price Details :: (Forwarded: <?php echo $item['status_forward']?>)</th>
                        </tr>
                        <tr>
                            <th class="bg-danger" style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY'); ?></th>
                            <th class="bg-danger text-center" style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                            <th class="bg-danger text-right" style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                            <th class="bg-danger text-right" style="min-width: 100px;">KG</th>
                            <th class="bg-danger text-right" style="min-width: 100px;">Unit Price (Currency)</th>
                            <th class="bg-danger text-right" style="min-width: 150px;">Total Price (Currency)</th>
                        </tr>
                    </thead>
                    <?php
                    if(!empty($items))
                    {
                        ?>
                        <tbody>
                        <?php
                        $total_kg='0.000';
                        $total_currency='0.00';
                        $grand_total_currency='0.00';
                        foreach($items as $data)
                        {
                            $item_per_kg='0.000';
                            $item_per_currency='0.000';
                            if($data['quantity_type_id']==0)
                            {
                                $item_per_kg = number_format(($data['quantity_order']),3);
                            }
                            else
                            {
                                $item_per_kg = number_format((($data['pack_size_name']*$data['quantity_order'])/1000),3);
                            }
                            $item_per_currency=number_format(($data['quantity_order']*$data['price_currency']),2);
                            $total_kg+=$item_per_kg;
                            $total_currency+=($data['quantity_order']*$data['price_currency']);
                            ?>
                            <tr>
                                <td><strong class="text-success"><?php echo $data['variety_name_import']?></strong></td>
                                <td class="text-center"><?php if($data['pack_size_name']==0){echo "Bulk";}else{echo $data['pack_size_name'];}?></td>
                                <td class="text-right"><?php echo $data['quantity_order']?></td>
                                <td class="text-right"><?php echo $item_per_kg?></td>
                                <td class="text-right"><?php echo $data['price_currency']?></td>
                                <td class="text-right"><?php echo $item_per_currency?></td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="3" class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_KG')?></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($total_kg,3);?></label></th>
                            <th class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_CURRENCY')?></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($total_currency,2);?></label></th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right"><?php echo $this->lang->line('LABEL_OTHER_COST_CURRENCY')?></th>
                            <th class="text-right">
                                <label class="control-label"><?php echo number_format($item['other_cost_currency'],2)?></label>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right"><?php echo $this->lang->line('LABEL_GRAND_TOTAL_CURRENCY')?></th>
                            <th class="text-right">
                                <label class="control-label">
                                    <?php
                                    $grand_total_currency=($total_currency+$item['other_cost_currency']);
                                    echo number_format($grand_total_currency,2);?>
                                </label>
                            </th>
                        </tr>
                        </tfoot>
                    <?php
                    }
                    else
                    {
                        ?>
                        <tfoot>
                            <tr>
                                <td class="widget-header text-center" colspan="21"><strong>Data Not Found</strong></td>
                            </tr>
                        </tfoot>
                    <?php
                    }
                    ?>
                </table>
            </div>

        </div>
    </div>
    <div class="clearfix"></div>
</form>

