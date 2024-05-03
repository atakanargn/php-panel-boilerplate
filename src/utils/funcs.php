<?
function _t($parameter, $language = 'tr')
{
    // PDO alındı
    global $pdo;

    try {
        $sql = "SELECT i18n_value FROM public.i18n_words 
                WHERE i18n_name = :name AND i18n_language = :language";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'name' => $parameter,
            'language' => $language
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            $sql = "INSERT INTO public.i18n_words (i18n_name,i18n_language,i18n_value,i18n_new)VALUES(:name,:language,:value,1);";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name' => $parameter,
                'language' => $language,
                'value' => $parameter
            ]);
            return $parameter;
        }
        return $result['i18n_value'];
    } catch (PDOException $e) {
        // Hata durumunda işle
        die("İşlem hatası: " . $e->getMessage());
    }
}

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