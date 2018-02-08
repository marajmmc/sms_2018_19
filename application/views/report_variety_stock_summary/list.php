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
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'crop_name', type: 'string' },
                { name: 'crop_type', type: 'string' },
                { name: 'variety', type: 'string' },
                { name: 'pack_size', type: 'string' },
                <?php
                foreach($warehouses as $warehouse_id=>$warehouse)
                {
                ?>
                { name: 'warehouse_<?php echo $warehouse_id?>_pkt', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse_id?>_kg', type: 'string' },
            <?php
                }
                ?>
                { name: 'current_stock_pkt', type: 'string' },
                { name: 'current_stock_kg', type: 'string' }
            ],
            id: 'id',
            type: 'POST',
            url: url,
            data:JSON.parse('<?php echo json_encode($options);?>')
        };

        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
             //console.log(defaultHtml);
            if (record.variety_name=="Type Total")
            {
                if(!((column=='crop_name')||(column=='type_name')))
                {
                    element.css({ 'background-color': system_report_color_type,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                }
            }
            else if (record.crop_type=="Crop Total")
            {
                if(column!='crop_name')
                {
                    element.css({ 'background-color': system_report_color_crop,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                }
            }
            else if (record.crop_name=="Grand Total")
            {

                element.css({ 'background-color': system_report_color_grand,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});

            }
            else
            {
                element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }

            return element[0].outerHTML;

        };

        var aggregates=function (total, column, element, record)
        {
            if(record.crop_name=="Grand Total")
            {
                //console.log(record.current_stock_kg);
                return record[element];

            }
            return total;
            //return grand_starting_stock;
        };
        var aggregatesrenderer=function (aggregates)
        {
            //console.log(aggregates);
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +aggregates['total']+'</div>';

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

                filterable: false,
                sortable: false,
                showfilterrow: false,
                pageable: false,
                pagesize:50,
                pagesizeoptions: ['20', '50', '100', '200','300','500'],
                selectionmode: 'singlerow',
                autoheight: false,
                autorowheight: false,
                columns:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',filtertype: 'list',pinned:true, width:'100',cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer, hidden: <?php echo $system_preference_items['crop_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'crop_type',filtertype: 'list',cellsrenderer: cellsrenderer,pinned:true,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer, hidden: <?php echo $system_preference_items['crop_type']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY'); ?>', dataField: 'variety',filtertype: 'list',cellsrenderer: cellsrenderer,pinned:true,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer, hidden: <?php echo $system_preference_items['variety']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?>', dataField: 'pack_size',filtertype: 'list',cellsrenderer: cellsrenderer,pinned:true,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer, hidden: <?php echo $system_preference_items['pack_size']?0:1;?>},
                    <?php
                foreach($warehouses as $warehouse_id=>$warehouse)
                {
                ?>
                    { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_PKT'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_pkt',cellsrenderer: cellsrenderer, aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer, width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_KG'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_kg',cellsrenderer: cellsrenderer, aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer, width:'100'},
                    <?php
                        }
                        ?>
                    { text: '<?php echo $CI->lang->line('LABEL_CURRENT_STOCK_PKT'); ?>', dataField: 'current_stock_pkt',cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer, hidden: <?php echo $system_preference_items['current_stock_pkt']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CURRENT_STOCK_KG'); ?>', dataField: 'current_stock_kg',cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer, hidden: <?php echo $system_preference_items['current_stock_kg']?0:1;?>}
                ]
            });
    });
</script>
