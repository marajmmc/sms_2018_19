<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array(
        'label'=>'Pending LC',
        'href'=>site_url($CI->controller_url.'/index/list')
    );
}
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_DETAILS'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/details_all_lc')
    );
}
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array(
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference_all_lc')
    );
}
if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5']) && ($CI->permissions['action5']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'class'=>'button_action_download',
        'data-title'=>"Download"
    );
}

$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list_all')
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_LOAD_MORE"),
    'id'=>'button_jqx_load_more'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="col-xs-12" style="margin-bottom: 20px;">
        <div class="col-xs-2 ">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="barcode" <?php if($items['barcode']){echo 'checked';}?>><span class="label label-success"><?php echo $CI->lang->line('LABEL_BARCODE'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="fiscal_year_name" <?php if($items['fiscal_year_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="month_name" <?php if($items['month_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_MONTH'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="date_opening" <?php if($items['date_opening']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_DATE_OPENING'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="date_expected" <?php if($items['date_expected']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_DATE_EXPECTED'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="principal_name" <?php if($items['principal_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_PRINCIPAL_NAME'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="currency_name" <?php if($items['currency_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_CURRENCY_NAME'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="lc_number" <?php if($items['lc_number']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_LC_NUMBER'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="consignment_name" <?php if($items['consignment_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_CONSIGNMENT_NAME'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="price_other_cost_total_currency" <?php if($items['price_other_cost_total_currency']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_OTHER_COST_CURRENCY'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="quantity_total_kg" <?php if($items['quantity_total_kg']){echo 'checked';}?> value="1"><span class="label label-success">KG<?php //echo $CI->lang->line('LABEL_TOTAL_CURRENCY'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="price_variety_total_currency" <?php if($items['price_variety_total_currency']){echo 'checked';}?> value="1"><span class="label label-success">Variety Currency<?php //echo $CI->lang->line('LABEL_OTHER_COST_CURRENCY'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="price_total_currency" <?php if($items['price_total_currency']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_TOTAL_CURRENCY'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="status_forward" <?php if($items['status_forward']){echo 'checked';}?> value="1"><span class="label label-success">Forwarded<?php //echo $CI->lang->line('LABEL_TOTAL_CURRENCY'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="status_forward" <?php if($items['status_forward']){echo 'checked';}?> value="1"><span class="label label-success">Forwarded<?php //echo $CI->lang->line('LABEL_TOTAL_CURRENCY'); ?></span></label>
            </div>
        </div>
    </div>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_all');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'barcode', type: 'string' },
                { name: 'fiscal_year_name', type: 'string' },
                { name: 'month_name', type: 'string' },
                { name: 'date_opening', type: 'string' },
                { name: 'date_expected', type: 'string' },
                { name: 'principal_name', type: 'string' },
                { name: 'currency_name', type: 'string' },
                { name: 'lc_number', type: 'string' },
                { name: 'consignment_name', type: 'string' },
                { name: 'price_other_cost_total_currency', type: 'string' },
                { name: 'quantity_total_kg', type: 'string' },
                { name: 'price_variety_total_currency', type: 'string' },
                { name: 'price_total_currency', type: 'string' },
                { name: 'status_forward', type: 'string' }
            ],
            id: 'id',
            url: url
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                source: dataAdapter,
                pageable: true,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                pagesize:50,
                pagesizeoptions: ['20', '50', '100', '200','300','500'],
                selectionmode: 'singlerow',
                altrows: true,
                autoheight: true,
                autorowheight: true,
                columnsreorder: true,
                columns:
                    [
                        { text: '<?php echo $CI->lang->line('ID'); ?>', dataField: 'id',filtertype: 'list', hidden: true},
                        { text: '<?php echo $CI->lang->line('LABEL_BARCODE'); ?>', dataField: 'barcode',filtertype: 'list', hidden: <?php echo $items['barcode']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?>', dataField: 'fiscal_year_name',filtertype: 'list', hidden: <?php echo $items['fiscal_year_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_MONTH'); ?>', dataField: 'month_name',filtertype: 'list', width:80, hidden: <?php echo $items['month_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_OPENING'); ?>', dataField: 'date_opening', width:90, hidden: <?php echo $items['date_opening']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_EXPECTED'); ?>', dataField: 'date_expected', width:90, hidden: <?php echo $items['date_expected']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_PRINCIPAL_NAME'); ?>', dataField: 'principal_name',filtertype: 'list', hidden: <?php echo $items['principal_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_CURRENCY_NAME'); ?>', dataField: 'currency_name',filtertype: 'list', hidden: <?php echo $items['currency_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_LC_NUMBER'); ?>', dataField: 'lc_number', hidden: <?php echo $items['lc_number']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_CONSIGNMENT_NAME'); ?>', dataField: 'consignment_name', hidden: <?php echo $items['consignment_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_OTHER_COST_CURRENCY'); ?>', dataField: 'price_other_cost_total_currency', width:50, hidden: <?php echo $items['price_other_cost_total_currency']?0:1;?>},
                        { text: 'KG', dataField: 'quantity_total_kg', width:50, hidden: <?php echo $items['quantity_total_kg']?0:1;?>},
                        { text: 'Variety Currency', dataField: 'price_variety_total_currency', width:50, hidden: <?php echo $items['price_variety_total_currency']?0:1;?>},
                        { text: 'Total Currency', dataField: 'price_total_currency', width:50, hidden: <?php echo $items['price_total_currency']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_LC_FORWARD'); ?>', dataField: 'status_forward',cellsalign: 'center',filtertype: 'list', width:80}
                    ]
            });
    });
</script>
