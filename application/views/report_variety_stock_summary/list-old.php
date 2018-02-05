<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();

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
/*if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
{
    $action_buttons[]=array
    (
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference')
    );
}*/
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
    <?php
    if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
    {
        $CI->load->view('preference',array('system_preference_items'=>$system_preference_items));
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
                { name: 'price_open_other_currency', type: 'string' },
                { name: 'quantity_open_kg', type: 'string' },
                { name: 'price_open_variety_currency', type: 'string' }
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
                    { text: '<?php echo $CI->lang->line('LABEL_BARCODE'); ?>', dataField: 'barcode', width:80, hidden: <?php echo $system_preference_items['barcode']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_FISCAL_YEAR_NAME'); ?>', dataField: 'fiscal_year_name',filtertype: 'list', width:65, hidden: <?php echo $system_preference_items['fiscal_year_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_MONTH_NAME'); ?>', dataField: 'month_name',filtertype: 'list', width:60, hidden: <?php echo $system_preference_items['month_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_OPENING'); ?>', dataField: 'date_opening', width:90, hidden: <?php echo $system_preference_items['date_opening']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_EXPECTED'); ?>', dataField: 'date_expected', width:90, hidden: <?php echo $system_preference_items['date_expected']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_PRINCIPAL_NAME'); ?>', dataField: 'principal_name',filtertype: 'list', width:180, hidden: <?php echo $system_preference_items['principal_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CURRENCY_NAME'); ?>', dataField: 'currency_name',filtertype: 'list', width:80, hidden: <?php echo $system_preference_items['currency_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_LC_NUMBER'); ?>', dataField: 'lc_number', hidden: <?php echo $system_preference_items['lc_number']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CONSIGNMENT_NAME'); ?>', dataField: 'consignment_name', width:150, hidden: <?php echo $system_preference_items['consignment_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_PRICE_OPEN_OTHER_CURRENCY'); ?>', dataField: 'price_open_other_currency', width:100, cellsalign: 'right',  hidden: <?php echo $system_preference_items['price_open_other_currency']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_OPEN_KG'); ?>', dataField: 'quantity_open_kg', width:100, cellsalign: 'right', hidden: <?php echo $system_preference_items['quantity_open_kg']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_PRICE_OPEN_VARIETY_CURRENCY'); ?>', dataField: 'price_open_variety_currency', cellsalign: 'right', width:100, hidden: <?php echo $system_preference_items['price_open_variety_currency']?0:1;?>}
                ]
            });
    });
</script>
