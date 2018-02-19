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
    public static function get_barcode_raw_master_purchase($increment_id)
    {
        return 'RMP'.str_pad($increment_id,5,0,STR_PAD_LEFT); //Raw Master Purchase
    }
    public static function get_barcode_raw_foil_purchase($increment_id)
    {
        return 'RFP'.str_pad($increment_id,5,0,STR_PAD_LEFT); //Raw Foil Purchase
    }
    public static function get_barcode_raw_sticker_purchase($increment_id)
    {
        return 'RSP'.str_pad($increment_id,5,0,STR_PAD_LEFT); //Raw Sticker Purchase
    }
    public static function get_barcode_raw_master_stock_in($increment_id)
    {
        return 'RMI'.str_pad($increment_id,5,0,STR_PAD_LEFT); //Raw Master Stock In
    }
    public static function get_barcode_raw_sticker_stock_in($increment_id)
    {
        return 'RSI'.str_pad($increment_id,5,0,STR_PAD_LEFT); //Raw Sticker Stock In
    }
    public static function get_barcode_raw_foil_stock_in($increment_id)
    {
        return 'RFI'.str_pad($increment_id,5,0,STR_PAD_LEFT); //Raw Foil Stock In
    }
    public static function get_barcode_raw_master_stock_out($increment_id)
    {
        return 'RMO'.str_pad($increment_id,5,0,STR_PAD_LEFT); //Raw Master Stock Out
    }
    public static function get_barcode_raw_sticker_stock_out($increment_id)
    {
        return 'RSO'.str_pad($increment_id,5,0,STR_PAD_LEFT); //Raw Sticker Stock Out
    }
    public static function get_barcode_raw_foil_stock_out($increment_id)
    {
        return 'RFO'.str_pad($increment_id,5,0,STR_PAD_LEFT); //Raw Sticker Stock Out
    }

    //Convert
    public static function get_barcode_convert_bulk_to_packet($increment_id)
    {
        return 'CBP'.str_pad($increment_id,5,0,STR_PAD_LEFT); //Raw Sticker Stock Out
    }

}
