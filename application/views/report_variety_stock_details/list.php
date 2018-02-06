<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
if(isset($CI->permissions['action4'])&&($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5'])&&($CI->permissions['action5']==1))
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
    $action_buttons[]=array
    (
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference')
    );
}
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
        //var grand_total_color='#AEC2DD';
        var grand_total_color='#AEC2DD';

        var url = "<?php echo base_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'crop_name', type: 'string' },
                { name: 'crop_type', type: 'string' },
                { name: 'variety', type: 'string' },
                { name: 'barcode', type: 'string' },
                { name: 'pack_size', type: 'numeric' },
                { name: 'starting_stock', type: 'string' },
                { name: 'total_stock_in', type: 'string' },
                //{ name: 'stock_in', type: 'string' },
                //{ name: 'excess', type: 'string' },
                { name: 'total_stock_out', type: 'string' },
                //{ name: 'sales_return', type: 'string' },
                //{ name: 'sales_bonus', type: 'string' },
                //{ name: 'sales_return_bonus', type: 'string' },
                //{ name: 'short', type: 'string' },
                //{ name: 'rnd', type: 'string' },
                //{ name: 'sample', type: 'string' },
                //{ name: 'demonstration', type: 'string' },
                { name: 'current_stock', type: 'string' },
                //{ name: 'current_price', type: 'string' },
                //{ name: 'current_total_price', type: 'string' }
            ],
            id: 'id',
            url: url,
            type: 'POST',
            data:JSON.parse('<?php echo json_encode($options);?>')
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            // console.log(defaultHtml);

            if (record.variety_name=="Total Type")
            {
                if(!((column=='crop_name')||(column=='crop_type_name')))
                {
                    element.css({ 'background-color': '#6CAB44','margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                }
            }
            else if (record.crop_type_name=="Total Crop")
            {


                if((column!='crop_name'))
                {
                    element.css({ 'background-color': '#0CA2C5','margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});

                }

            }
            else if (record.crop_name=="Grand Total")
            {

                element.css({ 'background-color': grand_total_color,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});

            }
            else
            {
                element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }

            return element[0].outerHTML;

        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var aggregates=function (total, column, element, record)
        {
            if(record.crop_name=="Grand Total")
            {
                //console.log(element);
                return record[element];

            }
            return total;
            //return grand_starting_stock;
        };
        var aggregatesrenderer=function (aggregates)
        {
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+grand_total_color+';">' +aggregates['total']+'</div>';

        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                height:'350px',
                source: dataAdapter,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                enabletooltips: true,
                showaggregates: true,
                showstatusbar: true,
                rowsheight: 35,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',width: '130',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer,hidden: <?php echo $system_preference_items['crop_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'crop_type',width: '130',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer,hidden: <?php echo $system_preference_items['crop_type']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY'); ?>', dataField: 'variety',width: '130',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer,hidden: <?php echo $system_preference_items['variety']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_BARCODE'); ?>', dataField: 'barcode',width: '130',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer,hidden: <?php echo $system_preference_items['barcode']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?>', dataField: 'pack_size',cellsalign: 'right',width: '100',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer,hidden: <?php echo $system_preference_items['pack_size']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_STARTING_STOCK'); ?>', dataField: 'starting_stock',width:'200',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer,hidden: <?php echo $system_preference_items['starting_stock']?0:1;?>},
                    //{ text: 'Stock In', dataField: 'stock_in',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    //{ text: 'Excess',hidden:true, dataField: 'excess',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    //{ text: 'Sales', dataField: 'sales',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    //{ text: 'Short',hidden:true, dataField: 'short',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    //{ text: 'Rnd Sample',hidden:true, dataField: 'rnd',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    //{ text: 'Sample',hidden:true, dataField: 'sample',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    //{ text: 'Demonstration',hidden:true, dataField: 'demonstration',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_TOTAL_STOCK_IN'); ?>', dataField: 'total_stock_in',width:'150',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer,hidden: <?php echo $system_preference_items['total_stock_in']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_TOTAL_STOCK_OUT'); ?>', dataField: 'total_stock_out',width:'150',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer,hidden: <?php echo $system_preference_items['total_stock_out']?0:1;?>},

                    { text: '<?php echo $CI->lang->line('LABEL_CURRENT_STOCK'); ?>', dataField: 'current_stock',width:'150',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer,hidden: <?php echo $system_preference_items['current_stock']?0:1;?>}
                    //{ text: 'Current unit Price', dataField: 'current_price',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    //{ text: 'Current Stock Price', dataField: 'current_total_price',width:'150',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer}
                ]
            });
    });
</script>