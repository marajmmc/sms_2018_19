function system_preset(params={})
{
    system_resized_image_files=[];
    if(params.controller!==undefined)
    {
        // controller condition code
    }
}

function system_off_events()
{
    /*Common*/
    $(document).off('change','#warehouse_id');
    $(document).off('change','#warehouse_id_source');
    $(document).off('change','#warehouse_id_destination');
    $(document).off('change','#crop_id');
    $(document).off('change','#crop_type_id');
    $(document).off('change','#variety_id');
    $(document).off('change','#pack_size_id');
    $(document).off("change","#fiscal_year_id");

    $(document).off("change",".warehouse_id");
    $(document).off('change','.warehouse_id_source');
    $(document).off('change','.warehouse_id_destination');
    $(document).off("change",".crop_id");
    $(document).off("change",".crop_type_id");
    $(document).off("change",".variety_id");
    $(document).off("change",".pack_size_id");

    $(document).off("change","#items_container .crop_id");
    $(document).off("change","#items_container .crop_type_id");
    $(document).off('change','#items_container .variety_id');
    $(document).off('change','#items_container .pack_size_id');

    $(document).off('change', '#division_id');
    $(document).off('change', '#zone_id');
    $(document).off('change', '#territory_id');
    $(document).off('change', '#district_id');

    $(document).off('change', '.division_id');
    $(document).off('change', '.zone_id');
    $(document).off('change', '.territory_id');
    $(document).off('change', '.district_id');

    $(document).off('change', '#customer_id');

    $(document).off('change', '#outlet_id');
    $(document).off('change', '#outlet_id_source');
    $(document).off('change', '#outlet_id_destination');

    $(document).off("click", ".system_button_add_more");
    $(document).off('click','.system_button_add_delete');
    $(document).off("click", ".pop_up");

    $(document).off('change','#purpose');

    $(document).off("click", ".task_action_all");
    $(document).off("click", ".task_header_all");

    $(document).off('input','.amount');

    $(document).off('input', '#items_container .quantity_approve');
    $(document).off('input', '#items_container .quantity_request');

    /*SMS */
    /*$(document).off('input','#quantity_convert');
    $(document).off('input','#quantity_packet_actual');
    $(document).off('input','#price_complete_other_variety_taka');
    $(document).off('change','#principal_id');
    $(document).off('input','#items_container .quantity_open');
    $(document).off('change','#items_container .price_unit_currency');
    $(document).off('input','#price_open_other_currency');
    $(document).off('input','.quantity_receive');
    $(document).off('input','#quantity_receive');
    $(document).off('input','.quantity_release');
    $(document).off('input','#price_release_other_currency');
    $(document).off('change','#supplier_id');
    $(document).off('input','#price_unit_tk');
    $(document).off('input','.number_of_reel');
    $(document).off('input','.quantity_supply');
    $(document).off('change','.price_unit_tk');
    $(document).off('change','#packing_item');*/

}