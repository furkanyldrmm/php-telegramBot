<?php

include('simple_html_dom.php');

class TelegramBot
{


    const API_URL = 'https://api.telegram.org/bot';
    public $token;
    public $chatId;


    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @param $method
     * @param $posts
     */
    public function request($method, $posts)
    {


        $ch = curl_init();
        $url = self::API_URL . $this->token . '/' . $method;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($posts));

        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return $result;

    }

    /**
     * @return mixed
     */
    public function getData()
    {
        $data = json_decode(file_get_contents('php://input'));
        $this->chatId = $data->message->chat->id;

        return $data->message;
    }

    /**
     * @param $message
     * @return bool|string
     */

    public function sendMessage($message)
    {

        return $this->request('sendMessage', [
            'chat_id' => $this->chatId,
            'text' => $message
        ]);

    }

    public function setWebhook($url)
    {

        return $this->request('setWebhook', [
            'url' => $url
        ]);
    }
}

function getNumber($value){
    $value_convert= number_format($value);

    return $value_convert;

}

function getEczane($p_il, $p_ilce)
{

    $ilce = urlencode($p_ilce);
    $il = urlencode($p_il);
    $apiKey = 'apikey 7Mkf8bTq5xEaOzJT1wm60M:0RVQpJAOOvUqio5VJPCAJu';
    $headers = array(
        'content-type: application/json',
        'authorization: ' . $apiKey
    );


    $process = curl_init('https://api.collectapi.com/health/dutyPharmacy?ilce=' . $ilce . '&il=' . $il);
    curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($process, CURLOPT_TIMEOUT, 30);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, true);

    $return = curl_exec($process);
    curl_close($process);
    $veri = json_decode($return, true);


    $result = "--Nöbetçi Eczane Listesi-- \n\n";

    foreach ($veri['result'] as $eczane) {
        $row = "Eczane adı: " . $eczane['name'] . "
        Adres:" . $eczane['address'] . "
        Telefon:" . $eczane["phone"];

        $result .= $row . "\n\n";
    }
    return ($result);
}


function getCorona()
{
    $veri = json_decode(file_get_contents('https://api.apify.com/v2/key-value-stores/28ljlt47S5XEd1qIi/records/LATEST?disableRedirect=true'), true);
    $newDate = date("d-m-Y", strtotime($veri["lastUpdatedAtSource"]));
    $sonuc = "Test sayisi: " . getNumber($veri["dailyTested"]) . "\nVaka sayısı:" . getNumber($veri["dailyInfected"]) . "\nÖlüm sayısı:" . getNumber($veri["dailyDeceased"]) . "\nTarih:" . $newDate;
    return $sonuc;
}


    $telegram = new TelegramBot();
    $telegram->setToken('1699294738:AAE1euR7RPlRSh8trpNzLSnR-9V80uo0Ung');
    $telegram->getData();
    //$a=getDenem();

    //preg_match_all('@<td class="color-up text-bold">(.*?)</td>@si',$a,$sonuc);

    //$cikti=implode("",$sonuc[1]);

    $data = $telegram->getData();

    $pieces = explode(" ", $data->text);


if ($data->text == 'covid') {
    $telegram->sendMessage(getCorona());

} else if ($pieces[0] == 'eczane') {
    if ($pieces[1] && $pieces[2]) {
        $veri = getEczane($pieces[1], $pieces[2]);
        $telegram->sendMessage($veri);


    }


} else {

    $telegram->sendMessage("üzgünüm yardımcı olamıyorum");


}

?>