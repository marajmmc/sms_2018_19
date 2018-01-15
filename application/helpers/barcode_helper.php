<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barcode_helper
{
    public static function get_barcode_stock_in($increment_id)
    {
        return 'SI'.str_pad($increment_id,6,0,STR_PAD_LEFT);
    }
    public static function get_barcode_stock_out($increment_id)
    {
        return 'SO'.str_pad($increment_id,6,0,STR_PAD_LEFT);
    }
    public static function get_barcode_transfer_warehouse_to_warehouse($increment_id)
    {
        return 'TW'.str_pad($increment_id,6,0,STR_PAD_LEFT);
    }
    public static function get_barcode_transfer_warehouse_to_outlet($increment_id)
    {
        return 'TS'.str_pad($increment_id,6,0,STR_PAD_LEFT);
    }
    public static function get_barcode_transfer_outlet_to_warehouse($increment_id)
    {
        return 'TR'.str_pad($increment_id,6,0,STR_PAD_LEFT);
    }
    public static function get_barcode_transfer_outlet_to_outlet($increment_id)
    {
        return 'TO'.str_pad($increment_id,6,0,STR_PAD_LEFT);
    }
    //it will be transferred into barcode helper

}
