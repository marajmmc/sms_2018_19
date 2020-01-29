<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_DETAILS'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/details')
    );
}
if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Update Rate Receive',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_rate_receive')
    );
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Update Rate Complete',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_rate_complete')
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
                { name: 'fiscal_year', type: 'string' },
                { name: 'month', type: 'string' },
                { name: 'date_opening', type: 'string' },
                { name: 'date_receive', type: 'string' },
                { name: 'principal_name', type: 'string' },
                { name: 'currency_name', type: 'string' },
                { name: 'lc_number', type: 'string' },
                { name: 'quantity_open_kg', type: 'number' },
                { name: 'status_open', type: 'string' },
                { name: 'number_of_variety', type: 'number' },
                { name: 'number_of_variety_rate_receive', type: 'number' },
                { name: 'number_of_variety_rate_receive_difference', type: 'number' },
                { name: 'number_of_variety_rate_complete', type: 'number' },
                { name: 'number_of_variety_rate_complete_difference', type: 'number' }
            ],
            id: 'id',
            type: 'POST',
            url: url
        };

        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            if(column.substr(-2)=='kg')
            {
                if(value==0)
                {
                    element.html('');
                }
                else
                {
                    element.html(number_format(value,3,'.',''));
                }
            }
            //element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px'});
            if((column=='number_of_variety_rate_receive'))
            {
                if(record.number_of_variety_rate_receive_difference!=0)
                {
                    element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px',background:'#F99797'});
                }
            }
            if((column=='number_of_variety_rate_complete'))
            {
                if((record.status_open!="<?php echo $this->config->item('system_status_complete')?>")||(record.number_of_variety_rate_complete_difference!=0))
                {
                        element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px',background:'#F99797'});

                }
            }

            return element[0].outerHTML;

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
                height: '350px',
                columnsreorder: true,
                enablebrowserselection: true,
                columns:
                    [
                        { text: '<?php echo $CI->lang->line('LABEL_BARCODE'); ?>', dataField: 'barcode', width:80, hidden: <?php echo $system_preference_items['barcode']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?>', dataField: 'fiscal_year',filtertype: 'list', width:65, hidden: <?php echo $system_preference_items['fiscal_year']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_MONTH'); ?>', dataField: 'month',filtertype: 'list', width:80, hidden: <?php echo $system_preference_items['month']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_OPENING'); ?>', dataField: 'date_opening', width:100, hidden: <?php echo $system_preference_items['date_opening']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_RECEIVE'); ?>', dataField: 'date_receive', width:100, hidden: <?php echo $system_preference_items['date_receive']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_PRINCIPAL_NAME'); ?>', dataField: 'principal_name',filtertype: 'list', width:180, hidden: <?php echo $system_preference_items['principal_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_CURRENCY_NAME'); ?>', dataField: 'currency_name',filtertype: 'list', width:80, hidden: <?php echo $system_preference_items['currency_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_LC_NUMBER'); ?>', dataField: 'lc_number',width:120,hidden: <?php echo $system_preference_items['lc_number']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_OPEN_KG'); ?>', dataField: 'quantity_open_kg', cellsalign: 'right',cellsrenderer: cellsrenderer, width:100, hidden: <?php echo $system_preference_items['quantity_open_kg']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_VARIETY'); ?>', dataField: 'number_of_variety', cellsalign: 'right', width:140,cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['number_of_variety']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_VARIETY_RATE_RECEIVE'); ?>', dataField: 'number_of_variety_rate_receive', cellsalign: 'right', width:140,cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['number_of_variety_rate_receive']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_VARIETY_RATE_RECEIVE_DIFFERENCE'); ?>', dataField: 'number_of_variety_rate_receive_difference', cellsalign: 'right', width:140,cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['number_of_variety_rate_receive_difference']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_VARIETY_RATE_COMPLETE'); ?>', dataField: 'number_of_variety_rate_complete', cellsalign: 'right', width:140,cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['number_of_variety_rate_complete']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_VARIETY_RATE_COMPLETE_DIFFERENCE'); ?>', dataField: 'number_of_variety_rate_complete_difference', cellsalign: 'right', width:140,cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['number_of_variety_rate_complete_difference']?0:1;?>}
                        /*,
                        { text: '<?php echo $CI->lang->line('LABEL_STATUS_RELEASE');?>', dataField: 'status_release', width:70,filtertype: 'list',cellsalign: 'center', hidden: <?php echo $system_preference_items['status_release']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_STATUS_RECEIVED');?>', dataField: 'status_received', width:70,filtertype: 'list',cellsalign: 'center', hidden: <?php echo $system_preference_items['status_received']?0:1;?>}*/
                    ]
            });
    });
</script>
