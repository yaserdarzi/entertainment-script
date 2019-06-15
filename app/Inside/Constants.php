<?php


namespace App\Inside;

class Constants
{
    //App
    Const APP = "entertainment";

    //Status
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_DEACTIVATE = 'deactivate';
    const STATUS_RETURN_BUY = 'return_buy';

    //Role
    Const ROLE_ADMIN = "admin";
    Const ROLE_SALES_MAN = "sales_man";
    Const ROLE_COUNTER_MAN = "counter_man";

    //Database
    const PRODUCT_DB = 'product';
    const PRODUCT_SUPPLIER_DB = 'product_supplier';
    const PRODUCT_GALLERY_DB = 'product_gallery';
    const PRODUCT_VIDEO_DB = 'product_video';
    const PRODUCT_EPISODE_DB = 'product_episode';


    //Type Percent
    const TYPE_PERCENT_PERCENT = "percent";
    const TYPE_PERCENT_PRICE = "price";

    //Extension Check File
    const PHOTO_TYPE = ["image/gif", "image/jpeg", "image/jpg", "image/png", "image/PNG", "image/GIF", 'image/*'];
    const VIDEO_TYPE = ["video/x-flv", "video/mp4", "application/x-mpegURL", "video/MP2T", "video/3gpp", "video/quicktime",
        "video/x-msvideo", "video/x-ms-wmv", "avi", "swf", "flv", "wmv", "application/octet-stream",
        "video/quicktime", "video/MP2T", "video/3gpp", "video/x-msvideo", "video/x-ms-wmv", "video/x-ms-wmv",
        "video/x-matroska", "video/mpeg", "application/x-shockwave-flash", "video/webm", "video/mov", 'video/*'];
    const AUDIO_TYPE = ["audio/mpeg", "audio/x-wav", "audio/ogg", "audio/mp4", "audio/midi", "audio/basic", "audio/adpcm", "audio//s3m", "audio/mp3",
        "audio/silk", "audio/webm", "audio/m4a"];

    //Room Status
    const ROOM_STATUS_EMPTY = 'empty';
    const ROOM_STATUS_FULL = 'full';
    const ROOM_STATUS_RESERVABLE = 'reservable';
    const ROOM_STATUS_UNDEFINED = 'undefined';

}
