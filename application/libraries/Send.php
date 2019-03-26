<?php
define('token', "123456:your_key_api-bot"); // @botfather https://telegram.me/botfather with the following text: /newbot

class Send{

    private $url = "https://api.telegram.org/bot";
    private $CI;

   	function __construct() {
       $this->CI =& get_instance();
       $this->CI->load->database();
   	}

    function kickChatMember($data)
    {
        $url = $this->url.token.'/kickChatMember?chat_id='.$data[0].'&user_id='.$data[1]."&until_date=".$data[2];

        $callback = json_decode(file_get_contents($url), TRUE);

        return $callback;

    }

    function sendLocation($data)
    {
        $url = $this->url.token.'/sendLocation?chat_id='.$data[0].'&latitude='.$data[1][0]."&longitude=".$data[1][1];

        if ($data[2]) {
            $url .= "&reply_markup=".$data[2];
        }

        $callback = json_decode(file_get_contents($url), TRUE);

        return $callback;

    }
    function sendMessage($message_info){

        // $message_info = array(
        //   'chat_id' => '163549885',
        //   'text' => "لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می باشد. کتابهای زیادی در شصت و سه درصد گذشته، حال و آینده شناخت فراوان جامعه و متخصصان را می طلبد تا با نرم افزارها شناخت بیشتری را برای طراحان رایانه ای علی الخصوص طراحان خلاقی و فرهنگ پیشرو در زبان فارسی ایجاد کرد. در این صورت می توان امید داشت که تمام و دشواری موجود در ارائه راهکارها و شرایط سخت تایپ به پایان رسد وزمان مورد نیاز شامل حروفچینی دستاوردهای اصلی و جوابگوی سوالات پیوسته اهل دنیای موجود طراحی اساسا مورد استفاده قرار گیرد.",
        //   'parse_mode' => 'HTML',
        //   'disable_web_page_preview' => 'TRUE',
        //   'disable_notification' => 'TRUE',
        //   'reply_to_message_id' => '248',
        //   'reply_markup' => ''
        //  );

        $url = $this->url.token.'/sendMessage?chat_id='.$message_info['chat_id'].'&text='.$message_info['text'];
        $url .= '&parse_mode=HTML';
        // if (!empty($message_info['disable_notification'])) {
          // $url .= '&disable_notification='.$message_info['disable_notification'];
          $url .= '&disable_notification=TRUE';
        // }
        if (isset($message_info['reply_to_message_id'])) {
          $url .= '&reply_to_message_id='.$message_info['reply_to_message_id'];
        }
        if (isset($message_info['reply_markup'])) {
          $url .= '&reply_markup='.$message_info['reply_markup'];
        }
        if (isset($message_info['disable_web_page_preview'])) {
          $url .= '&disable_web_page_preview='.$message_info['disable_web_page_preview'];
        }else {
          $url .= '&disable_web_page_preview=TRUE';
        }

        $lastmessage = $this->CI->db->query("SELECT * FROM settings where name='last_message_id' and group_id = ?", $message_info['chat_id'])->row_array();
        $this->deleteMessage($lastmessage['group_id'], $lastmessage['value']);

        $callback = json_decode(file_get_contents($url), TRUE);

        $lastmessage1 = $this->CI->db->update('settings' , ['value' => $callback['result']['message_id']], ['id' => $lastmessage['id'] ]);        

        return $callback;
    }

    function sendPhoto($message_info){

        // $message_info = array(){
        //   'chat_id' => '163549885',
        //   'photo' => 'AgADBAADjagxG4tDGFKH6-AULr4U9qtnZxkABBxgESa6OY_koLcDAAEC'
        //   'caption' => 'HTMLsdfdsfdsfsd',
        //   'disable_notification' => 'TRUE',
        //   'reply_to_message_id' => '248',
        //   'reply_markup' => ''
        // }


        $url = $this->url.token.'/sendPhoto?chat_id='.$message_info['chat_id'];
        $url .= '&photo='.$message_info['photo'];
        if (isset($message_info['disable_notification'])) {
          $url .= '&disable_notification='.$message_info['disable_notification'];
        }else {
          $url .= '&disable_notification=TRUE';
        }
        if (isset($message_info['reply_to_message_id'])) {
          $url .= '&reply_to_message_id='.$message_info['reply_to_message_id'];
        }
        if (isset($message_info['reply_markup'])) {
          $url .= '&reply_markup='.$message_info['reply_markup'];
        }
        if (isset($message_info['caption'])) {
          $url .= '&caption='.$message_info['caption'];
        }
        	$callback = json_decode(file_get_contents($url), TRUE);

        return $callback;
    }

    function get_admins($chat_id)
    {
        $url = $this->url . token . "/getChatAdministrators?chat_id=" . $chat_id;

        $callback = json_decode(file_get_contents($url), TRUE)['result'];
        // return $callback;

        $admins = array_map(function ($item) {
            return $item['user']['id'];
        },$callback);

        return $admins;
	}
	
	function deleteMessage($chat_id, $message_id)
	{
		$callback = file_get_contents($this->url.token."/deleteMessage?chat_id={$chat_id}&message_id=". $message_id);		
		return $callback;
  }
  
  function editReply($chat_id, $message_id, $reply_markup)
  {
    $url = $this->url.token."/editMessageReplyMarkup?chat_id={$chat_id}&message_id=". $message_id;
    $url .= "&reply_markup=".$reply_markup;
    $callback = file_get_contents($url);		
		return true;
  }
}
