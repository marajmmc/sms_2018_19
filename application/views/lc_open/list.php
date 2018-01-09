<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line("ACTION_NEW"),
        'href'=>site_url($CI->controller_url.'/index/add')
    );
}
if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_EDIT'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit')
    );
}
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array(
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference')
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
    'href'=>site_url($CI->controller_url.'/index/list')
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
        <!--<div class="col-xs-2 ">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="id" <?php /*if($items['id']){echo 'checked';}*/?> checked><span class="label label-success"><?php /*echo $CI->lang->line('ID'); */?></span></label>
            </div>
        </div>-->
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
                <label><input type="checkbox" class="system_jqx_column" value="price_total_currency" <?php if($items['price_total_currency']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_TOTAL_CURRENCY'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="other_cost_currency" <?php if($items['other_cost_currency']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_OTHER_COST_CURRENCY'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="status_expense" <?php if($items['status_expense']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('STATUS'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="status_release" <?php if($items['status_release']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_RELEASE_STATUS'); ?></span></label>
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
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'fiscal_year_name', type: 'string' },
                { name: 'month_name', type: 'string' },
                { name: 'date_opening', type: 'string' },
                { name: 'date_expected', type: 'string' },
                { name: 'principal_name', type: 'string' },
                { name: 'currency_name', type: 'string' },
                { name: 'lc_number', type: 'string' },
                { name: 'consignment_name', type: 'string' },
                { name: 'price_total_currency', type: 'string' },
                { name: 'other_cost_currency', type: 'string' },
                { name: 'status_expense', type: 'string' },
                { name: 'status_release', type: 'string' }
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
                pagesize:20,
                pagesizeoptions: ['20', '50', '100', '200','300','500'],
                selectionmode: 'singlerow',
                altrows: true,
                autoheight: true,
                autorowheight: true,
                columnsreorder: true,
                columns:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?>', dataField: 'fiscal_year_name',filtertype: 'list', hidden: <?php echo $items['fiscal_year_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_MONTH'); ?>', dataField: 'month_name',filtertype: 'list', width:80, hidden: <?php echo $items['month_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_OPENING'); ?>', dataField: 'date_opening', width:90, hidden: <?php echo $items['date_opening']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_EXPECTED'); ?>', dataField: 'date_expected', width:90, hidden: <?php echo $items['date_expected']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_PRINCIPAL_NAME'); ?>', dataField: 'principal_name',filtertype: 'list', hidden: <?php echo $items['principal_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CURRENCY_NAME'); ?>', dataField: 'currency_name',filtertype: 'list', hidden: <?php echo $items['currency_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_LC_NUMBER'); ?>', dataField: 'lc_number', hidden: <?php echo $items['lc_number']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CONSIGNMENT_NAME'); ?>', dataField: 'consignment_name', hidden: <?php echo $items['consignment_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_TOTAL_CURRENCY'); ?>', dataField: 'price_total_currency', width:50, hidden: <?php echo $items['price_total_currency']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_OTHER_COST_CURRENCY'); ?>', dataField: 'other_cost_currency', width:50, hidden: <?php echo $items['other_cost_currency']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('STATUS'); ?>', dataField: 'status_expense',cellsalign: 'center',filtertype: 'list', width:80, hidden: <?php echo $items['status_expense']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_RELEASE_STATUS'); ?>', dataField: 'status_release',cellsalign: 'center',filtertype: 'list', width:80, hidden: <?php echo $items['status_release']?0:1;?>}
                ]
            });
    });
</script>
