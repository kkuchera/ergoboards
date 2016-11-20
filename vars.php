<?php
    $cols = "image_url, image_width, image_height, image_l_url, title, design, size, switch, alignment, backlight, usb_hub, cable, formatted_price, product_url, tented, contour, asin, image_l_width, image_l_height, id";
    $all_params = array("title", "design", "size", "switch", "alignment", "tented", "contour", "backlight", "usb_hub", "cable", "price");

    $design_str = array("NS", "C", "SP", "FS");
    $size_str = array("FS", "TKL", "CMP", "KL");
    $switch_str = array("M", "C", "A", "T", "BS");
    $align_str = array("A", "S");
    $tented_str = array("NT", "T", "FT");
    $contour_str = array("F", "C");
    $backl_str = array("N", "Y");
    $cable_str = array("ND", "D", "W");


    $design_str_full = array("non-split", "curved", "fixed-split", "full-split");
    $size_str_full = array("full size", "tenkeyless", "compact", "keyless");
    $switch_str_full = array("membrane rubber dome", "cherry mx/clones", "alps/clones", "topre", "buckling spring");
    $align_str_full = array("asymmetrical", "symmetrical");
    $tented_str_full = array("non-tented", "tented", "fully-tentable");
    $contour_str_full = array("flat", "contoured");
    $backl_str_full = array("no", "yes");
    $cable_str_full = array("non-detachable", "detachable", "wireless");

    $maxrows = 20;
?>
