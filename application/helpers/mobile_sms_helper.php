<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mobile_sms_helper
{
    public static $API_URL='http://bangladeshsms.com/smsapi';
    public static $API_KEY='C20023045bb88951db45b5.36134381';
    public static $API_SENDER_ID_NON_MASKING='8804445629106';
    public static $API_SENDER_ID_MALIK_SEEDS='Malik Seeds';
    public static $API_SENDER_ID_BEEZTOLA='Beejtala';
    //$type= text for normal SMS/unicode for Bangla SMS
    public static function send_sms($sender_id,$contacts,$msg,$type='unicode')
    {
        $CI =& get_instance();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, Mobile_sms_helper::$API_URL);

        curl_setopt($ch, CURLOPT_POST,TRUE);
        $data = array();
        $data['api_key']=Mobile_sms_helper::$API_KEY;
        $data['senderid']=$sender_id;
        $data['type']=$type;
        $data['contacts']=$contacts;
        $data['msg']=$msg;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);//wait for response
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); //timeout in seconds 2min
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data=array();
        $data['sender_id']=$sender_id;
        $data['contacts']=$contacts;
        $data['msg']=$msg;
        $data['status_http']=$http_status;
        $data['status_sms']=$response;
        $data['date_string']=System_helper::display_date_time(time());
        Query_helper::add($CI->config->item('table_system_history_mobile_sms'), $data,false);
        return array('status_http'=>$http_status,'sms_response'=>$response);
    }
}
