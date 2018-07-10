<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_helper
{
    public static function get_variety_stock($variety_ids=array())
    {
        $CI =& get_instance();
        $CI->db->from($CI->config->item('table_sms_stock_summary_variety'));
        if(sizeof($variety_ids)>0)
        {
            $CI->db->where_in('variety_id',$variety_ids);
        }
        $results=$CI->db->get()->result_array();
        $stocks=array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']][$result['warehouse_id']]=$result;
        }
        return $stocks;
    }

    public static function get_raw_stock($variety_ids=array())
    {
        $CI =& get_instance();
        $CI->db->from($CI->config->item('table_sms_stock_summary_raw'));
        if(sizeof($variety_ids)>0)
        {
            $CI->db->where_in('variety_id',$variety_ids);
        }
        $results=$CI->db->get()->result_array();
        $stocks=array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']][$result['packing_item']]=$result;
        }
        return $stocks;
    }

    public static function transfer_wo_variety_stock_info($id=0)
    {
        $CI =& get_instance();
        /* HQ stock */
        $CI->db->from($CI->config->item('table_sms_stock_summary_variety').' stock_summary_variety');
        $CI->db->select('SUM(stock_summary_variety.current_stock) current_stock, stock_summary_variety.variety_id, stock_summary_variety.pack_size_id');
        $CI->db->join($CI->config->item('table_login_setup_classification_pack_size').' pack','pack.id=stock_summary_variety.pack_size_id','LEFT');
        $CI->db->select('pack.name pack_size');
        //$CI->db->where('stock_summary_variety.current_stock > 0');
        $CI->db->where('stock_summary_variety.pack_size_id > 0');
        $CI->db->group_by('stock_summary_variety.variety_id, stock_summary_variety.pack_size_id');
        $results=$CI->db->get()->result_array();

        /*Initiate variable */
        $two_variety_info=array();
        foreach($results as $result)
        {

            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['pack_size']=$result['pack_size'];
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available_pkt']=$result['current_stock'];
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available']=(($result['current_stock']*$result['pack_size'])/1000);
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['quantity_min']=0;
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['quantity_max']=0;
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['quantity_max_transferable']=0;
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_outlet']=0;
        }

        /* calculate available stock */
        $CI->db->from($CI->config->item('table_sms_transfer_wo').' transfer_wo');
        $CI->db->join($CI->config->item('table_sms_transfer_wo_details').' transfer_wo_details','transfer_wo_details.transfer_wo_id=transfer_wo.id AND transfer_wo_details.status="'.$CI->config->item('system_status_active').'"','INNER');
        $CI->db->select('SUM(transfer_wo_details.quantity_approve) quantity_approve, transfer_wo_details.variety_id, transfer_wo_details.pack_size_id');
        $CI->db->where('transfer_wo.status',$CI->config->item('system_status_active'));
        $CI->db->where('transfer_wo.status_approve',$CI->config->item('system_status_approved'));
        $CI->db->where('transfer_wo.status_delivery',$CI->config->item('system_status_pending'));
        $CI->db->group_by('transfer_wo_details.variety_id, transfer_wo_details.pack_size_id');
        $results=$CI->db->get()->result_array();
        foreach($results as $result)
        {
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available']=($two_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available']-(($result['quantity_approve']*$two_variety_info[$result['variety_id']][$result['pack_size_id']]['pack_size'])/1000));
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available_pkt']=($two_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available_pkt']-$result['quantity_approve']);
        }

        /* min max stock */
        $results=Query_helper::get_info($CI->config->item('table_pos_setup_stock_min_max'), array('*'),array('customer_id='.$id));
        foreach($results as $result)
        {
            if(isset($two_variety_info[$result['variety_id']][$result['pack_size_id']]))
            {
                $two_variety_info[$result['variety_id']][$result['pack_size_id']]['quantity_min']=$result['quantity_min'];
                $two_variety_info[$result['variety_id']][$result['pack_size_id']]['quantity_max']=$result['quantity_max'];
                $two_variety_info[$result['variety_id']][$result['pack_size_id']]['quantity_max_transferable']=$result['quantity_max'];
            }
        }

        /* outlet stock */
        $CI->db->from($CI->config->item('table_pos_stock_summary_variety').' pos_stock_summary_variety');
        $CI->db->select('SUM(pos_stock_summary_variety.current_stock) current_stock, pos_stock_summary_variety.variety_id, pos_stock_summary_variety.pack_size_id');
        $CI->db->where('pos_stock_summary_variety.outlet_id',$id);
        $CI->db->group_by('pos_stock_summary_variety.variety_id, pos_stock_summary_variety.pack_size_id');
        $results=$CI->db->get()->result_array();

        foreach($results as $result)
        {
            $current_stock_kg=(($result['current_stock']*$two_variety_info[$result['variety_id']][$result['pack_size_id']]['pack_size'])/1000);
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_outlet']=$current_stock_kg;
            if(!(($two_variety_info[$result['variety_id']][$result['pack_size_id']]['quantity_max_transferable']-$current_stock_kg)>0))
            {
                $two_variety_info[$result['variety_id']][$result['pack_size_id']]['quantity_max_transferable']=0;
            }
            else
            {
                $two_variety_info[$result['variety_id']][$result['pack_size_id']]['quantity_max_transferable']-=$current_stock_kg;
            }
        }
        return $two_variety_info;
    }

    public static function get_variety_stock_outlet($outlet_id, $variety_ids=array())
    {
        $CI =& get_instance();
        $CI->db->from($CI->config->item('table_pos_stock_summary_variety').' pos_stock_summary_variety');
        $CI->db->where('pos_stock_summary_variety.outlet_id',$outlet_id);
        if(sizeof($variety_ids)>0)
        {
            $CI->db->where_in('variety_id',$variety_ids);
        }
        $results=$CI->db->get()->result_array();
        $stocks=array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]=$result;
        }
        return $stocks;
    }

    public static function transfer_ow_variety_stock_info($outlet_id=0)
    {
        $CI =& get_instance();
        $tow_variety_info=array();
        $CI->db->from($CI->config->item('table_pos_stock_summary_variety').' pos_stock_summary_variety');
        $CI->db->select('SUM(pos_stock_summary_variety.current_stock) current_stock, pos_stock_summary_variety.variety_id, pos_stock_summary_variety.pack_size_id');
        $CI->db->join($CI->config->item('table_login_setup_classification_pack_size').' pack','pack.id=pos_stock_summary_variety.pack_size_id','LEFT');
        $CI->db->select('pack.name pack_size');
        $CI->db->where('pos_stock_summary_variety.outlet_id',$outlet_id);
        $CI->db->group_by('pos_stock_summary_variety.variety_id, pos_stock_summary_variety.pack_size_id');
        $results=$CI->db->get()->result_array();
        foreach($results as $result)
        {
            $tow_variety_info[$result['variety_id']][$result['pack_size_id']]['pack_size']=$result['pack_size'];
            $tow_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available']=(($result['current_stock']*$result['pack_size'])/1000);
            $tow_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available_pkt']=$result['current_stock'];
        }

        /* calculate available stock */
        $CI->db->from($CI->config->item('table_sms_transfer_ow').' transfer_ow');
        $CI->db->join($CI->config->item('table_sms_transfer_ow_details').' transfer_ow_details','transfer_ow_details.transfer_ow_id=transfer_ow.id AND transfer_ow_details.status="'.$CI->config->item('system_status_active').'"','INNER');
        $CI->db->select('SUM(transfer_ow_details.quantity_approve) quantity_approve, transfer_ow_details.variety_id, transfer_ow_details.pack_size_id');
        $CI->db->where('transfer_ow.outlet_id',$outlet_id);
        $CI->db->where('transfer_ow.status',$CI->config->item('system_status_active'));
        $CI->db->where('transfer_ow.status_approve',$CI->config->item('system_status_approved'));
        $CI->db->where('transfer_ow.status_delivery',$CI->config->item('system_status_pending'));
        $CI->db->group_by('transfer_ow_details.variety_id, transfer_ow_details.pack_size_id');
        $results=$CI->db->get()->result_array();
        foreach($results as $result)
        {
            $tow_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available']=($tow_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available']-(($result['quantity_approve']*$tow_variety_info[$result['variety_id']][$result['pack_size_id']]['pack_size'])/1000));
            $tow_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available_pkt']=($tow_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available_pkt']-$result['quantity_approve']);
        }
        return $tow_variety_info;
    }

    public static function transfer_oo_variety_stock_info($outlet_id=0)
    {
        $CI =& get_instance();
        $too_variety_info=array();
        $CI->db->from($CI->config->item('table_pos_stock_summary_variety').' pos_stock_summary_variety');
        $CI->db->select('SUM(pos_stock_summary_variety.current_stock) current_stock, pos_stock_summary_variety.variety_id, pos_stock_summary_variety.pack_size_id');
        $CI->db->join($CI->config->item('table_login_setup_classification_pack_size').' pack','pack.id=pos_stock_summary_variety.pack_size_id','LEFT');
        $CI->db->select('pack.name pack_size');
        $CI->db->where('pos_stock_summary_variety.outlet_id',$outlet_id);
        $CI->db->group_by('pos_stock_summary_variety.variety_id, pos_stock_summary_variety.pack_size_id');
        $results=$CI->db->get()->result_array();
        foreach($results as $result)
        {
            $too_variety_info[$result['variety_id']][$result['pack_size_id']]['pack_size']=$result['pack_size'];
            $too_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available_pkt']=$result['current_stock'];
            $too_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available_kg']=(($result['current_stock']*$result['pack_size'])/1000);
        }

        /* calculate available stock */
        $CI->db->from($CI->config->item('table_sms_transfer_oo').' transfer_oo');
        $CI->db->join($CI->config->item('table_sms_transfer_oo_details').' transfer_oo_details','transfer_oo_details.transfer_oo_id=transfer_oo.id AND transfer_oo_details.status="'.$CI->config->item('system_status_active').'"','INNER');
        $CI->db->select('SUM(transfer_oo_details.quantity_approve) quantity_approve, transfer_oo_details.variety_id, transfer_oo_details.pack_size_id');
        $CI->db->where('transfer_oo.outlet_id_source',$outlet_id);
        $CI->db->where('transfer_oo.status',$CI->config->item('system_status_active'));
        $CI->db->where('transfer_oo.status_approve',$CI->config->item('system_status_approved'));
        $CI->db->where('transfer_oo.status_delivery',$CI->config->item('system_status_pending'));
        $CI->db->group_by('transfer_oo_details.variety_id, transfer_oo_details.pack_size_id');
        $results=$CI->db->get()->result_array();
        foreach($results as $result)
        {
            $too_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available_pkt']=($too_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available_pkt']-$result['quantity_approve']);
            $too_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available_kg']=($too_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available_kg']-(($result['quantity_approve']*$too_variety_info[$result['variety_id']][$result['pack_size_id']]['pack_size'])/1000));
        }
        return $too_variety_info;
    }
}
