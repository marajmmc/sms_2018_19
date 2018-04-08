<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transfer extends CI_Controller
{
    public function index()
    {
        //$this->stock();
        $this->transfer_wo();
    }
    private function stock()
    {

        $crop_warehouse[1]=3;
        $crop_warehouse[2]=3;
        $crop_warehouse[3]=3;
        $crop_warehouse[4]=3;
        $crop_warehouse[5]=3;
        $crop_warehouse[6]=3;
        $crop_warehouse[7]=3;//
        $crop_warehouse[8]=3;//
        $crop_warehouse[9]=3;
        $crop_warehouse[10]=3;
        $crop_warehouse[11]=3;
        $crop_warehouse[12]=3;
        $crop_warehouse[26]=3;//
        $crop_warehouse[27]=3;
        $crop_warehouse[28]=3;
        $crop_warehouse[29]=3;//
        $crop_warehouse[30]=3;//

        $crop_warehouse[13]=2;
        $crop_warehouse[14]=2;
        $crop_warehouse[15]=2;
        $crop_warehouse[16]=2;
        $crop_warehouse[17]=2;
        $crop_warehouse[18]=2;
        $crop_warehouse[19]=2;
        $crop_warehouse[20]=2;
        $crop_warehouse[23]=2;
        $crop_warehouse[25]=2;

        $crop_warehouse[21]=1;
        $crop_warehouse[22]=1;
        $crop_warehouse[24]=1;

        $source_tables=array(
            'table_stockin_varieties'=>'arm_ems.ems_stockin_varieties',
            'table_varieties'=>'arm_ems.ems_varieties',
            'table_crop_types'=>'arm_ems.ems_crop_types',
            'table_stockin_excess_inventory'=>'arm_ems.ems_stockin_excess_inventory',
            'table_stockout'=>'arm_ems.ems_stockout',
            'table_sales_po'=>'arm_ems.ems_sales_po',
            'table_sales_po_details'=>'arm_ems.ems_sales_po_details',
            'table_login_setup_classification_pack_size'=>'arm_login_2018_19.login_setup_classification_pack_size',
            'outlet_stock'=>$this->config->item('table_pos_stock_summary_variety')
        );
        $destination_tables=array(
            'table_sms_stock_in_variety'=>'arm_sms_2018_19.sms_stock_in_variety',
            'table_sms_stock_in_variety_details'=>'arm_sms_2018_19.sms_stock_in_variety_details',
            'table_sms_stock_summary_variety'=>'arm_sms_2018_19.sms_stock_summary_variety'
        );

        //stock in
        $this->db->from($source_tables['table_stockin_varieties'].' stv');
        $this->db->select('stv.variety_id,stv.pack_size_id');
        $this->db->select('SUM(stv.quantity) stock_in');
        $this->db->join($source_tables['table_varieties'].' v','v.id =stv.variety_id','INNER');
        $this->db->join($source_tables['table_crop_types'].' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->select('crop_type.crop_id crop_id');
        $this->db->group_by(array('stv.variety_id','stv.pack_size_id'));


        $this->db->order_by('stv.variety_id');
        $this->db->order_by('stv.pack_size_id');
        $this->db->where('stv.status',$this->config->item('system_status_active'));
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['crop_id']=$result['crop_id'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_in']=$result['stock_in'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['excess']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_out']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['sales']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['outlet_in']=0;
        }

        //excess
        $this->db->from($source_tables['table_stockin_excess_inventory'].' ste');
        $this->db->select('ste.variety_id,ste.pack_size_id');
        $this->db->select('SUM(ste.quantity) excess');
        $this->db->group_by(array('ste.variety_id','ste.pack_size_id'));
        $this->db->where('ste.status',$this->config->item('system_status_active'));
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            if(isset($stocks[$result['variety_id']][$result['pack_size_id']]))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]['excess']=$result['excess'];
            }
        }
        //stock out
        $this->db->from($source_tables['table_stockout'].' sout');
        $this->db->select('sout.variety_id,sout.pack_size_id');
        $this->db->select('SUM(sout.quantity) stockout');
        $this->db->group_by(array('sout.variety_id','sout.pack_size_id'));
        $this->db->where('sout.status',$this->config->item('system_status_active'));
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            if(isset($stocks[$result['variety_id']][$result['pack_size_id']]))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]['stock_out']=$result['stockout'];
            }

        }
        //sales-sales return
        $this->db->from($source_tables['table_sales_po_details'].' spd');
        $this->db->select('spd.variety_id,spd.pack_size_id');
        $this->db->select('SUM(spd.quantity-spd.quantity_return) sales');
        $this->db->join($source_tables['table_sales_po'].' sp','sp.id =spd.sales_po_id','INNER');

        $this->db->group_by(array('spd.variety_id','spd.pack_size_id'));

        $this->db->where('sp.status_approved','Approved');
        $this->db->where('spd.revision',1);
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            if(isset($stocks[$result['variety_id']][$result['pack_size_id']]))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]['sales']=$result['sales'];
            }
        }
        //outlet instock
        $this->db->from($source_tables['outlet_stock'].' outlet_stock');
        $this->db->select('SUM(outlet_stock.in_wo) outlet_in');
        $this->db->select('outlet_stock.variety_id,outlet_stock.pack_size_id');
        $this->db->group_by(array('outlet_stock.variety_id','outlet_stock.pack_size_id'));
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            if(isset($stocks[$result['variety_id']][$result['pack_size_id']]))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]['outlet_in']=$result['outlet_in'];
            }
        }

        $time=System_helper::get_time('31-05-2016');
        $pack_size=array();
        $results=Query_helper::get_info($source_tables['table_login_setup_classification_pack_size'],array('id value','name text'),array());
        foreach($results as $result)
        {
            $pack_size[$result['value']]=$result['text'];
        }
        $quantity_total=0;
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($stocks as $variety_id=>$variety_stock)
        {
            foreach($variety_stock  as $pack_size_id=>$stock)
            {

                //insert to stockin variety details table
                $data=array();
                $data['stock_in_id']=1;
                $data['variety_id']=$variety_id;
                $data['pack_size_id']=$pack_size_id;
                $data['warehouse_id']=$crop_warehouse[$stock['crop_id']];
                $data['quantity']=$stock['stock_in']+$stock['excess']-$stock['stock_out']-$stock['sales']+$stock['outlet_in'];
                $data['revision']=1;
                $data['date_created']=$time;
                $data['user_created']=2;
                Query_helper::add($destination_tables['table_sms_stock_in_variety_details'],$data,false);


                $quantity_total+=(($pack_size[$pack_size_id]*$data['quantity'])/1000);

                //insert to current stock table
                $data=array();
                $data['variety_id']=$variety_id;
                $data['pack_size_id']=$pack_size_id;
                $data['warehouse_id']=$crop_warehouse[$stock['crop_id']];
                $data['in_stock_in']=$stock['stock_in']+$stock['excess']-$stock['stock_out']-$stock['sales']+$stock['outlet_in'];
                $data['out_wo']=$stock['outlet_in'];
                $data['current_stock']=$stock['stock_in']+$stock['excess']-$stock['stock_out']-$stock['sales'];
                Query_helper::add($destination_tables['table_sms_stock_summary_variety'],$data,false);
            }

            //insert all varieties
        }
        $data=array();
        $data['date_stock_in']=$time;
        $data['remarks']='initial stock';
        $data['purpose']='Stock-In';
        $data['quantity_total']=$quantity_total;
        $data['revision']=1;
        $data['date_created']=$time;
        $data['user_created']=2;
        Query_helper::add($destination_tables['table_sms_stock_in_variety'],$data,false);

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status()===true)
        {
            echo "Transfer Stock completed";
        }
        else
        {
            echo "Transfer Stock Failed";
        }
    }
    private function transfer_wo()
    {
        $crop_warehouse[1]=3;
        $crop_warehouse[2]=3;
        $crop_warehouse[3]=3;
        $crop_warehouse[4]=3;
        $crop_warehouse[5]=3;
        $crop_warehouse[6]=3;
        $crop_warehouse[7]=3;//
        $crop_warehouse[8]=3;//
        $crop_warehouse[9]=3;
        $crop_warehouse[10]=3;
        $crop_warehouse[11]=3;
        $crop_warehouse[12]=3;
        $crop_warehouse[26]=3;//
        $crop_warehouse[27]=3;
        $crop_warehouse[28]=3;
        $crop_warehouse[29]=3;//
        $crop_warehouse[30]=3;//

        $crop_warehouse[13]=2;
        $crop_warehouse[14]=2;
        $crop_warehouse[15]=2;
        $crop_warehouse[16]=2;
        $crop_warehouse[17]=2;
        $crop_warehouse[18]=2;
        $crop_warehouse[19]=2;
        $crop_warehouse[20]=2;
        $crop_warehouse[23]=2;
        $crop_warehouse[25]=2;

        $crop_warehouse[21]=1;
        $crop_warehouse[22]=1;
        $crop_warehouse[24]=1;

        $source_tables=array(
            'outlet_stock'=>$this->config->item('table_pos_stock_summary_variety'),
            'table_varieties'=>'arm_ems.ems_varieties',
            'table_crop_types'=>'arm_ems.ems_crop_types',
            'pack_sizes'=>$this->config->item('table_login_setup_classification_pack_size')
        );
        $destination_tables=array(
            'to'=>$this->config->item('table_sms_transfer_wo'),
            'to_details'=>$this->config->item('table_sms_transfer_wo_details'),
            'to_details_history'=>$this->config->item('table_sms_transfer_wo_details_histories'),
            'courier'=>$this->config->item('table_sms_transfer_wo_courier_details')
        );
        $results=Query_helper::get_info($source_tables['pack_sizes'],'*',array());
        $pack_size=array();
        foreach($results as $result)
        {
            $pack_size[$result['id']]=$result['name'];
        }

        $this->db->from($source_tables['outlet_stock'].' outlet_stock');
        $this->db->select('outlet_stock.*');
        $this->db->join($source_tables['table_varieties'].' v','v.id =outlet_stock.variety_id','INNER');
        $this->db->join($source_tables['table_crop_types'].' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->select('crop_type.crop_id crop_id');
        $results=$this->db->get()->result_array();
        $stock_outlet=array();
        foreach($results as $result)
        {
            $stock_outlet[$result['outlet_id']][$result['variety_id']][$result['pack_size_id']]=$result;
        }
        $i=0;
        $time=System_helper::get_time('31-05-2016');
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($stock_outlet as $outlet_id=>$to_info)
        {
            $i++;
            $quantity_total_request=0;
            foreach($to_info as $variety_id=>$variety_info)
            {
                foreach($variety_info as $pack_size_id=>$info)
                {
                    $quantity_total_request+=($info['in_wo']*$pack_size[$pack_size_id]);
                    //insert into details table
                    $data=array();
                    $data['transfer_wo_id']=$i;
                    $data['variety_id']=$variety_id;
                    $data['pack_size_id']=$pack_size_id;
                    $data['pack_size']=$pack_size[$pack_size_id];
                    $data['quantity_request']=$info['in_wo'];
                    $data['quantity_approve']=$info['in_wo'];
                    $data['quantity_receive']=$info['in_wo'];
                    $data['warehouse_id']=$crop_warehouse[$info['crop_id']];
                    Query_helper::add($destination_tables['to_details'],$data,false);

                    //insert into details history table
                    $data=array();
                    $data['transfer_wo_id']=$i;
                    $data['variety_id']=$variety_id;
                    $data['pack_size_id']=$pack_size_id;
                    $data['pack_size']=$pack_size[$pack_size_id];
                    $data['quantity']=$info['in_wo'];
                    $data['warehouse_id']=$crop_warehouse[$info['crop_id']];
                    $data['revision']=1;
                    $data['date_created']=$time;
                    $data['user_created']=2;
                    Query_helper::add($destination_tables['to_details_history'],$data,false);
                }
            }
            //insert into to table
            $data=array();
            $data['id']=$i;
            $data['outlet_id']=$outlet_id;
            $data['date_request']=$time;
            $data['quantity_total_request_kg']=($quantity_total_request/1000);
            $data['remarks_request']='Initial Transfer Request By System';
            $data['status_request']=$this->config->item('system_status_forwarded');
            $data['status_request']=$this->config->item('system_status_forwarded');
            $data['date_created_request']=$time;
            $data['user_created_request']=2;
            $data['date_updated_forward']=$time;
            $data['user_updated_forward']=2;
            $data['date_approve']=$time;
            $data['quantity_total_approve_kg']=$data['quantity_total_request_kg'];
            $data['remarks_approve']='Initial Transfer Approve By System';
            $data['status_approve']=$this->config->item('system_status_approved');
            $data['date_updated_approve_forward']=$time;
            $data['user_updated_approve_forward']=2;
            $data['date_delivery']=$time;
            $data['remarks_challan']='--';
            $data['remarks_delivery']='Initial Transfer Delivered By System';
            $data['status_delivery']=$this->config->item('system_status_delivered');
            $data['date_updated_delivery_forward']=$time;
            $data['user_updated_delivery_forward']=2;
            $data['date_receive']=$time;
            $data['quantity_total_receive_kg']=$data['quantity_total_request_kg'];
            $data['status_receive']=$this->config->item('system_status_received');
            Query_helper::add($destination_tables['to'],$data,false);
            //insert into courier
            $data=array();
            $data['transfer_wo_id']=$i;
            $data['date_delivery']=$time;
            $data['date_challan']=$time;
            $data['challan_no']=Barcode_helper::get_barcode_transfer_warehouse_to_outlet($i);
            $data['courier_id']=1;
            $data['remarks']='Initial Transfer By System';
            $data['date_updated']=$time;
            $data['user_updated']=2;
            Query_helper::add($destination_tables['courier'],$data,false);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status()===true)
        {
            echo "Transfer To completed";
        }
        else
        {
            echo "Transfer To Failed";
        }
    }
}
