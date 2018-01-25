<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'LC Release',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit')
    );

    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Release Complete',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/release_complete')
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
if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
{
    $action_buttons[]=array(
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference')
    );
}
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list')
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
    <?php
    if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
    {
        ?>
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <div class="col-xs-2 ">
                <div class="checkbox">
                    <label><input type="checkbox" class="system_jqx_column" value="barcode" <?php if($items['barcode']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_BARCODE'); ?></span></label>
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
                    <label><input type="checkbox" class="system_jqx_column" value="price_other_cost_total_release_currency" <?php if($items['price_other_cost_total_release_currency']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_OTHER_COST_CURRENCY'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-2">
                <div class="checkbox">
                    <label><input type="checkbox" class="system_jqx_column" value="quantity_total_release_kg" <?php if($items['quantity_total_release_kg']){echo 'checked';}?> value="1"><span class="label label-success">KG</span></label>
                </div>
            </div>
            <div class="col-xs-2">
                <div class="checkbox">
                    <label><input type="checkbox" class="system_jqx_column" value="price_variety_total_release_currency" <?php if($items['price_variety_total_release_currency']){echo 'checked';}?> value="1"><span class="label label-success">Variety (Currency)</span></label>
                </div>
            </div>
            <div class="col-xs-2">
                <div class="checkbox">
                    <label><input type="checkbox" class="system_jqx_column" value="price_total_release_currency" <?php if($items['price_total_release_currency']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_TOTAL_CURRENCY'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-2">
                <div class="checkbox">
                    <label><input type="checkbox" class="system_jqx_column" value="status_release" <?php if($items['status_release']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $this->lang->line('STATUS')?></span></label>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
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
                { name: 'barcode', type: 'string' },
                { name: 'fiscal_year_name', type: 'string' },
                { name: 'month_name', type: 'string' },
                { name: 'date_opening', type: 'string' },
                { name: 'date_expected', type: 'string' },
                { name: 'principal_name', type: 'string' },
                { name: 'currency_name', type: 'string' },
                { name: 'lc_number', type: 'string' },
                { name: 'consignment_name', type: 'string' },
                { name: 'price_other_cost_total_release_currency', type: 'string' },
                { name: 'quantity_total_release_kg', type: 'string' },
                { name: 'price_variety_total_release_currency', type: 'string' },
                { name: 'price_total_release_currency', type: 'string' },
                { name: 'status_release', type: 'string' }
            ],
            id: 'id',
            type: 'POST',
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
                        { text: '<?php echo $CI->lang->line('LABEL_BARCODE'); ?>', dataField: 'barcode',filtertype: 'list', width:80, hidden: <?php echo $items['barcode']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?>', dataField: 'fiscal_year_name',filtertype: 'list', width:65, hidden: <?php echo $items['fiscal_year_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_MONTH'); ?>', dataField: 'month_name',filtertype: 'list', width:60, hidden: <?php echo $items['month_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_OPENING'); ?>', dataField: 'date_opening', width:90, hidden: <?php echo $items['date_opening']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_EXPECTED'); ?>', dataField: 'date_expected', width:90, hidden: <?php echo $items['date_expected']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_PRINCIPAL_NAME'); ?>', dataField: 'principal_name',filtertype: 'list', width:80, hidden: <?php echo $items['principal_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_CURRENCY_NAME'); ?>', dataField: 'currency_name',filtertype: 'list', width:80, hidden: <?php echo $items['currency_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_LC_NUMBER'); ?>', dataField: 'lc_number', hidden: <?php echo $items['lc_number']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_CONSIGNMENT_NAME'); ?>', dataField: 'consignment_name', width:150, hidden: <?php echo $items['consignment_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_OTHER_COST_CURRENCY'); ?>', dataField: 'price_other_cost_total_release_currency', width:100, cellsalign: 'right',  hidden: <?php echo $items['price_other_cost_total_release_currency']?0:1;?>},
                        { text: 'KG', dataField: 'quantity_total_release_kg', width:100, cellsalign: 'right', hidden: <?php echo $items['quantity_total_release_kg']?0:1;?>},
                        { text: 'Variety (Currency)', dataField: 'price_variety_total_release_currency', cellsalign: 'right', width:100, hidden: <?php echo $items['price_variety_total_release_currency']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_TOTAL_CURRENCY');?>', dataField: 'price_total_release_currency', cellsalign: 'right', width:100, hidden: <?php echo $items['price_total_release_currency']?0:1;?>},
                        { text: '<?php echo $this->lang->line('STATUS')?>', dataField: 'status_release',cellsalign: 'center',filtertype: 'list', width:65, hidden: <?php echo $items['status_release']?0:1;?>}
                    ]
            });
    });
</script>
