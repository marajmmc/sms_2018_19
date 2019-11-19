<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_lc_variety_csv extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $user=User_helper::get_user();
        if(!$user)
        {
            echo 'Please login and try again';
            die();
        }
        $this->language_labels();
    }
    private function language_labels()
    {
        $this->lang->language['LABEL_LC_BARCODE']="Barcode(s)";
        $this->lang->language['LABEL_LC_LAST_BARCODE']="Last Barcode";
        $this->lang->language['LABEL_QUANTITY_OPEN']="Order Qty (Kg)";
        $this->lang->language['LABEL_QUANTITY_RELEASE']="Release Qty (Kg)";
        $this->lang->language['LABEL_QUANTITY_RECEIVE']="Receive Qty (Kg)";
        $this->lang->language['LABEL_QUANTITY_PKT']='Quantity (pkt)';
        $this->lang->language['LABEL_QUANTITY_KG']='Quantity (kg)';
    }

    public function system_list($crop_id=0,$crop_type_id=0,$variety_id=0,$pack_size_id=0,$status_received='',$status_open='',$principal_id=0,$date_start=0,$date_end=0,$date_type='',$report_type='')
    {
        $user=User_helper::get_user();
        $method='';
        $preference_headers=array();
        //$preference_headers['id']= 1;
        $preference_headers['crop_name']= 1;
        $preference_headers['crop_type_name']= 1;
        $preference_headers['variety_name']= 1;
        $preference_headers['pack_size']= 1;
        if($report_type=='lc')
        {
            $method='list_variety';
            $preference_headers['lc_barcode']= 1;
            $preference_headers['quantity_open']= 1;
            $preference_headers['quantity_release']= 1;
            $preference_headers['quantity_receive']= 1;
        }
        elseif($report_type=='quantity')
        {
            $method='list_quantity';
            $preference_headers['quantity_pkt']= 1;
            $preference_headers['quantity_kg']= 1;
        }
        else
        {

        }

        $preference= System_helper::get_preference($user->user_id,'report_lc_variety',$method,$preference_headers);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=variety_wise_lc_report.csv');
        $handle=fopen('php://output', 'w');
        $row=array();
        foreach($preference as $column=>$value)
        {
            if($value==1)
            {
                $row[]=$this->lang->line('LABEL_'.strtoupper($column));
            }
        }
        fputcsv($handle,$row);

        if($report_type=='lc')
        {
            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->select("v.id variety_id,v.name variety_name");
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->where('v.whose','ARM');
            if($crop_id>0)
            {
                $this->db->where('crop.id',$crop_id);
                if($crop_type_id>0)
                {
                    $this->db->where('crop_type.id',$crop_type_id);
                    if($variety_id>0)
                    {
                        $this->db->where('v.id',$variety_id);
                    }
                }
            }
            $this->db->order_by('crop.ordering','ASC');
            $this->db->order_by('crop.id','ASC');
            $this->db->order_by('crop_type.ordering','ASC');
            $this->db->order_by('crop_type.id','ASC');
            $this->db->order_by('v.ordering','ASC');
            $this->db->order_by('v.id','ASC');

            $varieties=$this->db->get()->result_array();
            $variety_ids=array();
            $variety_ids[0]=0;
            foreach($varieties as $result)
            {
                $variety_ids[$result['variety_id']]=$result['variety_id'];
            }

            $this->db->from($this->config->item('table_sms_lc_open').' lc');
            $this->db->select('lc.*');

            $this->db->join($this->config->item('table_sms_lc_details').' details','details.lc_id = lc.id','INNER');
            $this->db->select('details.variety_id, details.pack_size_id, details.quantity_open, details.quantity_release, details.quantity_receive');

            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack_size','pack_size.id = details.pack_size_id','LEFT');
            $this->db->select("pack_size.id pack_size_id,IF(`details`.`pack_size_id` = 0,'Bulk',pack_size.`name`) pack_size");

            $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.date_start <= lc.date_opening AND fy.date_end>lc.date_opening','INNER');
            $this->db->select('fy.name fiscal_year');

            $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lc.principal_id','INNER');
            $this->db->select('principal.name principal_name');

            $this->db->join($this->config->item('table_login_setup_currency').' currency','currency.id = lc.currency_id','INNER');
            $this->db->select('currency.name currency_name');

            $this->db->where_in('details.variety_id',$variety_ids);
            $this->db->where('details.quantity_open >0');
            $this->db->where('lc.status_open_forward',$this->config->item('system_status_yes'));
            $this->db->where('lc.status_release',$this->config->item('system_status_complete'));
            $this->db->where('lc.'.$date_type.'>='.$date_start.' and lc.'.$date_type.'<='.$date_end);
            if($pack_size_id>0)
            {
                $this->db->where('details.pack_size_id',$pack_size_id);
            }
            if($status_received)
            {
                $this->db->where('lc.status_receive',$status_received);
            }
            if($status_open)
            {
                $this->db->where('lc.status_open',$status_open);
            }
            else
            {
                $this->db->where('lc.status_open !=',$this->config->item('system_status_delete'));
            }

            if($principal_id)
            {
                $this->db->where('lc.principal_id',$principal_id);
            }
            $this->db->group_by('lc.id,details.variety_id,details.pack_size_id');
            $results=$this->db->get()->result_array();
            $variety_lc=array();
            $variety_lc_info=array();
            foreach($results as $result)
            {
                $variety_lc[$result['variety_id']][$result['pack_size_id']]=$result;
                $variety_lc_info[$result['variety_id']][$result['pack_size_id']][]=$result;
            }

            foreach($varieties as $variety)
            {
                if(isset($variety_lc[$variety['variety_id']]))
                {
                    foreach($variety_lc[$variety['variety_id']] as $details)
                    {
                        if(isset($variety_lc_info[$variety['variety_id']][$details['pack_size_id']]))
                        {
                            $lc_info=$variety_lc_info[$variety['variety_id']][$details['pack_size_id']];
                            for($i=0; $i<sizeof($lc_info); $i++)
                            {
                                $row=array();
                                if($preference['crop_name']==1)
                                {
                                    $row[]=$variety['crop_name'];
                                }
                                if($preference['crop_type_name']==1)
                                {
                                    $row[]=$variety['crop_type_name'];
                                }
                                if($preference['variety_name']==1)
                                {
                                    $row[]=$variety['variety_name'];
                                }
                                if($preference['pack_size']==1)
                                {
                                    $row[]=$details['pack_size'];
                                }
                                if($preference['lc_barcode']==1)
                                {
                                    $row[]=Barcode_helper::get_barcode_lc($lc_info[$i]['id']);
                                }
                                if($preference['quantity_open']==1)
                                {
                                    if($details['pack_size_id']==0)
                                    {
                                        $row[]=$lc_info[$i]['quantity_open'];
                                    }
                                    else
                                    {
                                        $row[]=($details['pack_size']*$lc_info[$i]['quantity_open'])/1000;
                                    }
                                }
                                if($preference['quantity_release']==1)
                                {
                                    if($details['pack_size_id']==0)
                                    {
                                        $row[]=$lc_info[$i]['quantity_release'];
                                    }
                                    else
                                    {
                                        $row[]=($details['pack_size']*$lc_info[$i]['quantity_release'])/1000;
                                    }
                                }
                                if($preference['quantity_receive']==1)
                                {
                                    if($details['pack_size_id']==0)
                                    {
                                        $row[]=$lc_info[$i]['quantity_receive'];
                                    }
                                    else
                                    {
                                        $row[]=($details['pack_size']*$lc_info[$i]['quantity_receive'])/1000;
                                    }
                                }
                                fputcsv($handle,$row);
                            }
                        }
                    }
                }
            }
        }
        elseif($report_type=='quantity')
        {
            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->select("v.id variety_id,v.name variety_name");
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->where('v.whose','ARM');
            if($crop_id>0)
            {
                $this->db->where('crop.id',$crop_id);
                if($crop_type_id>0)
                {
                    $this->db->where('crop_type.id',$crop_type_id);
                    if($variety_id>0)
                    {
                        $this->db->where('v.id',$variety_id);
                    }
                }
            }
            $this->db->order_by('crop.ordering','ASC');
            $this->db->order_by('crop.id','ASC');
            $this->db->order_by('crop_type.ordering','ASC');
            $this->db->order_by('crop_type.id','ASC');
            $this->db->order_by('v.ordering','ASC');
            $this->db->order_by('v.id','ASC');

            $varieties=$this->db->get()->result_array();
            $variety_ids=array();
            $variety_ids[0]=0;
            foreach($varieties as $result)
            {
                $variety_ids[$result['variety_id']]=$result['variety_id'];
            }

            $this->db->from($this->config->item('table_sms_lc_open').' lc');
            $this->db->select('lc.*');

            $this->db->join($this->config->item('table_sms_lc_details').' details','details.lc_id = lc.id','INNER');
            $this->db->select('details.variety_id, details.pack_size_id, SUM(details.quantity_receive) quantity_pkt, SUM((pack.name*details.quantity_receive)/1000) quantity_kg');

            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id = details.pack_size_id','LEFT');
            $this->db->select("pack.id pack_size_id,IF(`details`.`pack_size_id` = 0,'Bulk',pack.`name`) pack_size");

            $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.date_start <= lc.date_opening AND fy.date_end>lc.date_opening','INNER');
            $this->db->select('fy.name fiscal_year');

            $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lc.principal_id','INNER');
            $this->db->select('principal.name principal_name');

            $this->db->join($this->config->item('table_login_setup_currency').' currency','currency.id = lc.currency_id','INNER');
            $this->db->select('currency.name currency_name');


            $this->db->where_in('details.variety_id',$variety_ids);
            $this->db->where('details.quantity_open >0');
            $this->db->where('lc.status_open_forward',$this->config->item('system_status_yes'));
            $this->db->where('lc.status_release',$this->config->item('system_status_complete'));
            $this->db->where('lc.'.$date_type.'>='.$date_start.' and lc.'.$date_type.'<='.$date_end);
            $this->db->group_by('details.variety_id,details.pack_size_id');
            if($pack_size_id>0)
            {
                $this->db->where('details.pack_size_id',$pack_size_id);
            }
            if($status_received)
            {
                $this->db->where('lc.status_receive',$status_received);
            }
            if($status_open)
            {
                $this->db->where('lc.status_open',$status_open);
            }
            else
            {
                $this->db->where('lc.status_open !=',$this->config->item('system_status_delete'));
            }

            if($principal_id)
            {
                $this->db->where('lc.principal_id',$principal_id);
            }
            $results=$this->db->get()->result_array();
            $variety_lc=array();
            foreach($results as $result)
            {
                $variety_lc[$result['variety_id']][$result['pack_size_id']]=$result;
            }
            $items=array();
            foreach($varieties as $variety)
            {
                if(isset($variety_lc[$variety['variety_id']]))
                {
                    foreach($variety_lc[$variety['variety_id']] as $details)
                    {
                        $row=array();
                        if($preference['crop_name']==1)
                        {
                            $row[]=$variety['crop_name'];
                        }
                        if($preference['crop_type_name']==1)
                        {
                            $row[]=$variety['crop_type_name'];
                        }
                        if($preference['variety_name']==1)
                        {
                            $row[]=$variety['variety_name'];
                        }
                        if($preference['pack_size']==1)
                        {
                            $row[]=$details['pack_size'];
                        }
                        if($preference['quantity_pkt']==1)
                        {
                            if($details['pack_size_id']==0)
                            {
                                $row[]='';
                                //$row[]=$details['quantity_pkt'];
                            }
                            else
                            {
                                $row[]=$details['quantity_pkt'];
                                //$row[]=$details['quantity_kg'];
                            }
                        }
                        if($preference['quantity_kg']==1)
                        {
                            if($details['pack_size_id']==0)
                            {
                                //$row[]='';
                                $row[]=$details['quantity_pkt'];
                            }
                            else
                            {
                                //$row[]=$details['quantity_pkt'];
                                $row[]=$details['quantity_kg'];
                            }
                        }

                        /*if($details['pack_size_id']==0)
                        {
                            $row[]='';
                            $row[]=$details['quantity_pkt'];
                        }
                        else
                        {
                            $row[]=$details['quantity_pkt'];
                            $row[]=$details['quantity_kg'];
                        }*/
                        fputcsv($handle,$row);
                    }
                }
            }
        }
        else
        {

        }
        fclose($handle);
    }

}
