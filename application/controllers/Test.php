<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {
        $source_tables=array(
            'lc'=>'arm_sms_2018_19.sms_lc_open'

        );
        $destination_tables=array(
            'lc'=>'arm_sms_2018_19.sms_lc_open'
        );
        $results=Query_helper::get_info($source_tables['lc'],'*',array());
        $total=0;
        foreach($results as $result)
        {
            $month_id=date('n',$result['date_opening']);
            if($month_id!=$result['month_id'])
            {
                $total++;
                $data=array();
                $data['month_id'] = $month_id;
                Query_helper::update($destination_tables['lc'],$data, array('id='.$result['id']), false);

                //echo $result['id'].'-'.$month_id.'-'.$result['month_id'].'<br>';
            }
        }
        echo $total.'updated';

    }

}
