<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
/*if((isset($CI->permissions['action7']) && ($CI->permissions['action7']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'data-message-confirm'=>'Are you sure to save this data?',
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );
}*/
$action_buttons[]=array
(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);

$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/expense_complete/'.$item['id'])
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_expense_complete');?>" method="post">
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
        echo $CI->load->view("info_basic",array('accordion'=>array('header'=>'+LC Info','div_id'=>'accordion_lc_info','collapse'=>'in','data'=>$info_lc)),true);
        ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRICE_RELEASE_OTHER_VARIETY_TAKA');?> </label>
            </div>
            <div class="col-sm-4 col-xs-8 text-right">
                <label class="control-label"><?php echo number_format($item['price_release_other_variety_taka'],2);?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRICE_COMPLETE_OTHER_VARIETY_TAKA');?> </label>
            </div>
            <div class="col-sm-4 col-xs-8 text-right">
                <label class="control-label"><?php echo number_format($item['price_complete_other_variety_taka'],2);?></label>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row show-grid">
            <div style="overflow-x: auto;" class="col-xs-offset-2 col-xs-8">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Serial</th>
                        <th>Expense Head</th>
                        <th class="text-right">Amount (Taka)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $serial=1;
                    $price_complete_dc_taka=0;
                    $amount=0;
                    foreach($items as $row)
                    {
                        $amount=isset($dc[$row['id']])?$dc[$row['id']]:0;
                        $price_complete_dc_taka+=$amount;
                        ?>
                        <tr>
                            <td><?php echo $serial?></td>
                            <td><?php echo $row['name']?></td>
                            <td class="text-right">
                                <label for=""><?php echo number_format($amount,2);?></label>
                            </td>
                        </tr>
                        <?php
                        ++$serial;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="2" class="text-right">Total Expense(Taka): </th>
                        <th class="text-right">
                            <label class="control-label" id="lbl_price_complete_dc_taka"><?php echo number_format($price_complete_dc_taka,2)?></label>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-right"><?php echo $CI->lang->line('LABEL_PRICE_COMPLETE_OTHER_VARIETY_TAKA');?>: </th>
                        <th class="text-right">
                            <label class="control-label" id="lbl_price_complete_other_variety_taka"><?php echo number_format($item['price_complete_other_variety_taka'],2)?></label>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-right">Grand Total (Taka): </th>
                        <th class="text-right">
                            <label class="control-label" id="lbl_grand_total"><?php echo number_format(($item['price_complete_other_variety_taka']+$price_complete_dc_taka),2)?></label>
                        </th>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">LC Completed<span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <select id="status_open" class="form-control" name="item[status_open]">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                        <option value="<?php echo $this->config->item('system_status_complete')?>"><?php echo $this->config->item('system_status_complete')?></option>
                    </select>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_LC_EXPENSE');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo nl2br($item['remarks_expense']);?></label>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">

                </div>
                <div class="col-sm-4 col-xs-4">
                    <div class="action_button">
                        <button id="button_action_save" type="button" class="btn" data-form="#save_form" data-message-confirm="Are You Sure LC Completed?">LC Completed</button>
                    </div>
                </div>
                <div class="col-sm-4 col-xs-4">

                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>