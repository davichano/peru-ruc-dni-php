<?php

namespace Davichano\DNI;

class Engine
{

    private $apiKeyOCR;


    private $cookies = [];
    private $options;

    public function __construct($apiKeyOCR)
    {
        $this->apiKeyOCR = $apiKeyOCR;
        $this->options = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Host: ww4.essalud.gob.pe:7777',
                    'Upgrade-Insecure-Requests: 1',
                    'User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36'
                ]
            ]
        ];
    }


    public function search_data($dni)
    {
        $captchaCode = $this->validateImageCaptcha(5);
        if (!$captchaCode) return false;
        $this->options['http']['header']['Origin'] = "http://ww4.essalud.gob.pe:7777";
        $this->options['http']['header']['Referer'] = "http://ww4.essalud.gob.pe:7777/acredita/";
        $context = stream_context_create($this->options);

        $urlTemp = str_replace(["{0}", "{1}"], [$dni, $captchaCode], Endpoints::urlSearch);
        $searchResponse = file_get_contents($urlTemp, false, $context);

        if (!$searchResponse) {
            echo "Error buscando datos: " . error_get_last()['message'];
        } else {
            $HTMLContent = html_entity_decode($searchResponse);
            $pattern = '/<input[^>]*name="nom"[^>]*value="([^"]+)"/';
            preg_match($pattern, $HTMLContent, $matches);
            if (isset($matches[1])) {
                return explode(",", $matches[1]);
            }
        }
        return false;
    }

    private function validateImageCaptcha($tryCounter = 3)
    {

        $tryFlag = 0;
        while ($tryFlag < $tryCounter + 1) {
            $tryFlag++;


            if (!empty($this->cookies)) {
                $cookieHeader = 'Cookie: ' . http_build_query($this->cookies, '', '; ');
                $this->options['http']['header'][] = $cookieHeader;
            }

            $context = stream_context_create($this->options);
            $answerImageRequest = file_get_contents(Endpoints::urlCaptcha, false, $context);
            if ($answerImageRequest === false) {
                echo "Error leyendo el captcha: " . error_get_last()['message'];
            } else {
                $image = imagecreatefromstring($answerImageRequest);
                imagefilter($image, IMG_FILTER_GRAYSCALE);
                ob_start();
                imagejpeg($image);
                $data = ob_get_clean();
                $size = strlen($data);
                $captchaCode = $this->imgToText($data, $size);
                imagedestroy($image);
                if ($captchaCode && strlen($captchaCode) == 5)
                    return $captchaCode;
            }
        }
        return false;
    }

    private function imgToText($image, $size)
    {
        $base64Image = base64_encode($image);

        $postData = [
            'base64Image' => 'data:image/jpg;base64,' . $base64Image,
            'language' => 'eng',
            'isOverlayRequired' => 'false',
            'scale' => 'true',
            'OCREngine' => 2
        ];

        $headers = [
            'apikey: ' . $this->apiKeyOCR
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, Endpoints::urlOCR);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error al enviar la solicitud OCR: ' . curl_error($ch);
        }
        curl_close($ch);
        $answer = json_decode($response, true);
        if ($answer['IsErroredOnProcessing']) return false;
        $text = strtoupper($answer['ParsedResults'][0]['ParsedText']);
        return str_replace(["\r", "\n", 'L', 'J', 'Q', 'S', '/', 'B', '&', ' ', ',', '.', ']'], ["", "", '1', '1', '0', '5', '7', '8', '8', '', '', '', ''], $text);
    }
}