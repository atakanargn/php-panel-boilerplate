<?

function iConverterTurkish($str, $dir = "upper")
{
    $low = array('ı', 'i', 'ğ', 'ü', 'ş', 'ö', 'ç');
    $up = array('I', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç');
    return $dir == "upper" ?
        str_replace($low, $up, $str) :
        str_replace($up, $low, $str);
}

function ucfirst_tr($str)
{
    $tmp = preg_split("//u", $str, 2, PREG_SPLIT_NO_EMPTY);
    return
        strtoupper(iConverterTurkish($tmp[0], "upper")) .
        strtolower(iConverterTurkish($tmp[1], "lower"));
}

?>