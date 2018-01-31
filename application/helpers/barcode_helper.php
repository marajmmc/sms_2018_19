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

    //LC
    public static function get_barcode_lc($increment_id)
    {
        return 'LC'.str_pad($increment_id,6,0,STR_PAD_LEFT);
    }

  //  Added By saiful

    // Raw Materials
    public static function get_barcode_purchase_master($increment_id)
    {
        return 'PM'.str_pad($increment_id,6,0,STR_PAD_LEFT);
    }
    public static function get_barcode_purchase_foil($increment_id)
    {
        return 'PF'.str_pad($increment_id,6,0,STR_PAD_LEFT);
    }
    public static function get_barcode_purchase_sticker($increment_id)
    {
        return 'PS'.str_pad($increment_id,6,0,STR_PAD_LEFT);
    }
    public static function get_barcode_stock_in_master($increment_id)
    {
        return 'MI'.str_pad($increment_id,6,0,STR_PAD_LEFT);
    }
    public static function get_barcode_stock_in_sticker($increment_id)
    {
        return 'MS'.str_pad($increment_id,6,0,STR_PAD_LEFT);
    }
}
