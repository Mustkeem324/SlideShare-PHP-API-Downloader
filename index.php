<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $url=$_GET['url']; //request get to url
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $text = $url;
    }
    else{
        echo "Invalid URL provided check url";
        exit();
    }
function imagelink($text){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://server1.slidesharedown.net/get-images?url='.$text,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'authority: server1.slidesharedown.net',
            'accept: */*',
            'accept-language: en-US,en;q=0.9',
            'content-type: application/pdf',
            'dnt: 1',
            'origin: https://www.slidesharedown.net',
            'referer: https://www.slidesharedown.net/',
            'sec-ch-ua: "Chromium";v="118", "Google Chrome";v="118", "Not=A?Brand";v="99"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Linux"',
            'sec-fetch-dest: empty',
            'sec-fetch-mode: cors',
            'sec-fetch-site: same-site',
            'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $arrayData = json_decode($response, true);
    $lastIndex = count($arrayData['images']) - 1;
    $arrayData['type'] = 'pdf';
    $modifiedJsonData = json_encode($arrayData, JSON_PRETTY_PRINT);
    return $modifiedJsonData;
}

//pdfextract
function pdfextract($postimage){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://server1.slidesharedown.net/get-slide',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>''.$postimage.'',
        CURLOPT_HTTPHEADER => array(
            'authority: server1.slidesharedown.net',
            'accept: */*',
            'accept-language: en-US,en;q=0.9',
            'content-type: application/json',
            'dnt: 1',
            'origin: https://www.slidesharedown.net',
            'referer: https://www.slidesharedown.net/',
            'sec-ch-ua: "Chromium";v="118", "Google Chrome";v="118", "Not=A?Brand";v="99"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Linux"',
            'sec-fetch-dest: empty',
            'sec-fetch-mode: cors',
            'sec-fetch-site: same-site',
            'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    //echo $response;
    $data=json_decode($response,true);
    $urlpdf=$data['url'];
    return $urlpdf;
}

function slidesharepdf($text){
    $postimage = imagelink($text);
    $urlpdf=pdfextract($postimage);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://server1.slidesharedown.net/dl-slide?url='.$urlpdf,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'authority: server1.slidesharedown.net',
            'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'accept-language: en-US,en;q=0.9',
            'cookie: _ga=GA1.1.1410147571.1702103372; _ga_748M6B57KF=GS1.1.1702108096.2.1.1702108111.0.0.0',
            'dnt: 1',
            'referer: https://www.slidesharedown.net/',
            'sec-ch-ua: "Chromium";v="118", "Google Chrome";v="118", "Not=A?Brand";v="99"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Linux"',
            'sec-fetch-dest: document',
            'sec-fetch-mode: navigate',
            'sec-fetch-site: same-site',
            'upgrade-insecure-requests: 1',
            'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    //echo $response;
    $folderName = "slidesharepdf";
    $outputFilePath = __DIR__ . DIRECTORY_SEPARATOR . $folderName;

    if (!is_dir($outputFilePath)) {
        mkdir($outputFilePath, 0777, true);
    }
    if ($response !== false) {
        $timestamp = time(); // Get current timestamp
        $uniqueFilename = "output_" . $timestamp . ".pdf"; // Append timestamp to the filename
        $outputFile = $outputFilePath . DIRECTORY_SEPARATOR . $uniqueFilename;
        file_put_contents($outputFile, $response);
    } 
    if (file_exists($outputFile)) {
        $outputFilereal = new CURLFile(realpath($outputFile));
        header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
        return readfile($outputFile);
    }
}
slidesharepdf($text);
}
?>
