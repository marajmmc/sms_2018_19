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
                <?php
                 foreach($system_preference_items as $key=>$item)
                 {
                 if((substr($key,-3)=='pkt') || (substr($key,-2)=='kg'))
                 {
                 ?>
                { name: '<?php echo $key ?>', type: 'number' },
                <?php
                 }
                 else
                 {
                    ?>
                { name: '<?php echo $key ?>', type: 'string' },
                <?php
                }
             }
            ?>
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
            if (record.variety_name=="Total Type")
            {
                if(!((column=='crop_name')||(column=='crop_type_name')))
                {
                    element.css({ 'background-color': system_report_color_type,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                }
            }
            else if (record.crop_type_name=="Total Crop")
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
            if(column.substr(-3)=='pkt')
            {
                if(value==0)
                {
                    element.html('');
                }
                else
                {
                    element.html(get_string_quantity(value));
                }
            }
            else if(column.substr(-2)=='kg')
            {
                if(value==0)
                {
                    element.html('');
                }
                else
                {
                    element.html(get_string_kg(value));
                }
            }
            else if(column.substr(0,6)=='amount')
            {
                if(value==0)
                {
                    element.html('');
                }
                else
                {
                    element.html(get_string_amount(value));
                }
            }

            return element[0].outerHTML;

        };

        var aggregates=function (total, column, element, record)
        {
            //console.log(record);
            //console.log(record['warehouse_5_pkt']);
            if(record.crop_name=="Grand Total")
            {
                return record[element];

            }
            return total;
        };
        var aggregatesrenderer=function (aggregates)
        {
            var text=aggregates['total'];
            if(((aggregates['total']=='0.00')||(aggregates['total']=='')))
            {
                text='';
            }
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +text+'</div>';

        };
        var aggregatesrenderer_pkt=function (aggregates)
        {
            var text='';
            if(!((aggregates['total']=='0')||(aggregates['total']=='')))
            {
                text=get_string_quantity(aggregates['total']);
            }

            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +text+'</div>';

        };
        var aggregatesrenderer_kg=function (aggregates)
        {
            var text='';
            if(!((aggregates['total']=='0.000')||(aggregates['total']=='')))
            {
                text=get_string_kg(aggregates['total']);
            }
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +text+'</div>';
        };
        var aggregatesrenderer_amount=function (aggregates)
        {
            var text='';
            if(!((aggregates['total']=='0.00')||(aggregates['total']=='')))
            {
                text=get_string_amount(aggregates['total'],2);
            }

            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +text+'</div>';

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
                columns:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['crop_name']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['crop_type_name']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['variety_name']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?>', dataField: 'pack_size',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['pack_size']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_PRICE_UNIT'); ?>', dataField: 'amount_price_unit',pinned:true,width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_price_unit']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_STOCK_TOTAL_PKT'); ?>', dataField: 'stock_total_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['stock_total_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_pkt},
                    { text: '<?php echo $CI->lang->line('LABEL_STOCK_TOTAL_KG'); ?>', dataField: 'stock_total_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['stock_total_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_TOTAL'); ?>', dataField: 'amount_total',width:'120',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_total']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_STOCK_HQ_PKT'); ?>', dataField: 'stock_hq_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['stock_hq_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_pkt},
                    { text: '<?php echo $CI->lang->line('LABEL_STOCK_HQ_KG'); ?>', dataField: 'stock_hq_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['stock_hq_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_HQ'); ?>', dataField: 'amount_hq',width:'120',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_hq']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_STOCK_OUTLET_PKT'); ?>', dataField: 'stock_outlet_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['stock_outlet_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_pkt},
                    { text: '<?php echo $CI->lang->line('LABEL_STOCK_OUTLET_KG'); ?>', dataField: 'stock_outlet_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['stock_outlet_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_OUTLET'); ?>', dataField: 'amount_outlet',width:'120',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_outlet']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_STOCK_TO_PKT'); ?>', dataField: 'stock_to_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['stock_to_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_pkt},
                    { text: '<?php echo $CI->lang->line('LABEL_STOCK_TO_KG'); ?>', dataField: 'stock_to_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['stock_to_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_TO'); ?>', dataField: 'amount_to',width:'120',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_to']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_STOCK_TR_PKT'); ?>', dataField: 'stock_tr_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['stock_tr_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_pkt},
                    { text: '<?php echo $CI->lang->line('LABEL_STOCK_TR_KG'); ?>', dataField: 'stock_tr_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['stock_tr_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_TR'); ?>', dataField: 'amount_tr',width:'120',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_tr']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_STOCK_TS_PKT'); ?>', dataField: 'stock_ts_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['stock_ts_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_pkt},
                    { text: '<?php echo $CI->lang->line('LABEL_STOCK_TS_KG'); ?>', dataField: 'stock_ts_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['stock_ts_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_TS'); ?>', dataField: 'amount_ts',width:'120',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_ts']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_amount}

                ]
            });
    });
</script>
