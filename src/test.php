<?php
// Hedeflenen URL
$url = 'https://www.neilpryde.com/collections/sails';

// cURL ile sayfayı al
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);

// cURL hata kontrolü
if ($response === false) {
    die('Error fetching data: ' . curl_error($curl));
}

echo '' . $response . '';

// cURL kapat
curl_close($curl);

// DOMDocument oluştur
$dom = new DOMDocument();

// HTML içeriğini yükle (cURL'den gelen)
$internalErrors = libxml_use_internal_errors(true); // Hata raporlarını kapatalım
$dom->loadHTML($response);
libxml_use_internal_errors($internalErrors); // Hata raporlarını geri açalım

// DOMXPath oluştur
$xpath = new DOMXPath($dom);

// Belirli bir div elementine erişmek için XPath ifadesi
$xpathExpression = '//div[@class="collection__results"]';

// XPath ile belirli div elementini seç
$divElements = $xpath->query($xpathExpression);

// Seçilen div elementini yazdır
foreach ($divElements as $divElement) {
    // Div içeriğini HTML olarak almak için:
    $divContent = $dom->saveHTML($divElement);

    // Div içeriğini ekrana yazdır
    echo $divContent;
}
?>