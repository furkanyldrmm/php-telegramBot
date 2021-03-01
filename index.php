<?php

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
    public function request($method,$posts){


        $ch = curl_init();
        $url=self::API_URL.$this->token .'/'.$method;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($posts));

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
        $data=json_decode(file_get_contents('php://input'));
        $this->chatId=$data->message->chat->id;

        return $data->message;
    }

    /**
     * @param $message
     * @return bool|string
     */

    public function sendMessage($message){

        return $this->request('sendMessage',[
            'chat_id'=>$this->chatId,
            'text'=>$message
        ]);

    }

    public function setWebhook($url){

        return $this->request('setWebhook',[
            'url'=>$url
        ]);
    }





}
$telegram=new TelegramBot();
$telegram->setToken('1699294738:AAE1euR7RPlRSh8trpNzLSnR-9V80uo0Ung');
$text=$telegram->getData();

if($text->text=='hello'){
    $telegram->sendMessage('siktirmk');


}






?>

