<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Home extends CI_Controller {



    private $message;

    private $user_id;

    private $chat_id;

    private $group_id = "-1001180529942";

    private $message_id;

    private $message_text;

    private $new_member;

    private $left_member;

    private $sticker;

    private $photo;

    private $file;

    private $audio;

    private $forward;

    private $voice;

    private $video;

    private $document;

    private $gif;

    private $link;

    private $send_allowd = ['sticker', 'photo', "video", "gif", "link", "voice", "forward", "file", "audio", "chat", 'addbot'];



    function __construct()

    {

        parent::__construct();

        date_default_timezone_set("Asia/Tehran");

        $this->message = json_decode(file_get_contents('php://input'),TRUE);

        $this->message_id = $this->message['message']['message_id'];

        $this->message_text = trim($this->message['message']['text']);

        $this->user_id = $this->message['message']['from']['id'];

        $this->chat_id = $this->message['message']['chat']['id'];
        // $this->chat_id = "-1001158158969";

        $this->new_member = $this->message['message']['new_chat_members'];

        $this->sticker = $this->message['message']['sticker'];

        $this->photo = $this->message['message']['photo'];

        $this->gif = $this->message['message']['document']['mime_type'];

        $this->link = $this->message['message']['caption_entities'] || $this->message['message']['entities'];

        $this->file = $this->message['message']['document'];

        $this->audio = $this->message['message']['audio'];

        $this->forward = $this->message['message']['forward_from_chat'];

        $this->voice = $this->message['message']['voice'];

        $this->video = $this->message['message']['video'];

        $this->left_member = $this->message['message']['left_chat_member'];

        $this->contact = $this->message['message']['contact'] ? $this->message['message']['contact'] : null;

    }



	public function index()

	{
// 		file_get_contents("https://api.telegram.org/bot563281132:AAFim8ZLj5kzQ32_6T-_iosgRGZTFhYGAxk/sendMessage?chat_id=116032859&parse_mode=HTML&text=<code>".json_encode([$this->message])."</code>");
// 		exit;

        //-------------------------     Initiolize user in database     -------------------------
        if (!$this->message['callback_query']) {
            $this->db_user->set_user($this->message);            
        }
        //---------------------------------------------------------------------------------------
        
		if ($this->new_member) {

            $names = array_map(function ($item)
            {
                $user = $this->db->query("SELECT * FROM group_users WHERE telegram_id = ? AND group_tel_id = ?" , [$item['id'], $this->chat_id])->row_array();

                if ($user) {
                	if ($item['is_bot']) {
                		$settings = $this->db->query("SELECT * FROM settings WHERE group_id = ? and name='add_bot_send_allowed'", $this->chat_id)->row_array();

			            if (!$settings['value']) {
			                $this->send->kickChatMember([$this->message['message']['chat']['id'], $item['id']]);
			                $this->send('بات ها اجازه افزودن به گروه را ندارند');
			            }exit();
                	}else {
	                    $left_user = [

	                        'is_left' => 0,

	                        'left_time' => "",

	                        'added_time' => time()

	                    ];

	                    $this->db->update('group_users', $left_user, ['id' => $user['id']]);
	                }
                }else {

                	if ($item['is_bot']) {
                		$settings = $this->db->query("SELECT * FROM settings WHERE group_id = ? and name='add_bot_send_allowed'", $this->chat_id)->row_array();

			            if (!$settings['value']) {
			                $this->send->kickChatMember([$this->message['message']['chat']['id'], $item['id']]);
			                $this->send('بات ها اجازه افزودن به گروه را ندارند');
			            }exit();
                	}else {
                		$new_user = [
	                        'user_tel_id' => $item['id'],
	                        'created_at' => time(),
	                        'updated_at' => time(),
	                        'fname' => $item['first_name'] ? $item['first_name'] : "",
	                        'lname' => $item['last_name'] ? $item['last_name'] : "",
	                        'user_tel_username' => $item['username'] ? $item['username'] : ''
	                    ];

	                    $this->db->insert('users', $new_user);
	                    
	                    

	                    $new_group_user = [

	                        'telegram_id' => $item['id'],

	                        'added_by_id' => $this->user_id,

	                        'added_time' => time(),

	                        "group_tel_id" => $this->chat_id
	                    ];

	                    $this->db->insert('group_users', $new_group_user);
                	}
                }

                return $item['first_name'];
                
            }, $this->new_member);

            $user_invites = $this->db->query("SELECT COUNT(*) as count FROM group_users WHERE added_by_id = ? AND is_left = 0 AND group_tel_id = ?", [$this->user_id , $this->chat_id])->row_array()['count'];            
            $user_invites_left = $this->db->query("SELECT COUNT(*) as count FROM group_users WHERE added_by_id = ? AND is_left = 1 AND group_tel_id = ?", [$this->user_id, $this->chat_id ])->row_array()['count'];

            if (count($names) > 1) {
                $text_reply = "سلام دوستان عزیز"."\nبه گروه {$this->message['message']['chat']['title']} خوش‌آمدید\n\n🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷\n\nمجموعه دعوت شده‌ها توسط {$this->message['message']['from']['first_name']} {$this->message['message']['from']['last_name']}: " . ( $user_invites + $user_invites_left )." \nتعداد لفت داده ها: {$user_invites_left}  تعداد مفید: {$user_invites}\n\n🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷";                
            }else {
                $text_reply = "{$this->new_member[0]['first_name']} - [@".$this->new_member[0]['username']."]"."\nبه گروه {$this->message['message']['chat']['title']} خوش‌آمدید\n\n🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷\n\nمجموعه دعوت شده‌ها توسط {$this->message['message']['from']['first_name']} {$this->message['message']['from']['last_name']}: " . ( $user_invites + $user_invites_left )." \nتعداد لفت داده ها: {$user_invites_left}\n  تعداد مفید: {$user_invites}\n\n🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷";
            }

            $last_of_message = $this->db->query("SELECT * FROM settings where name='last_of_message'")->row_array()['value'];

            $text_reply .= "\n\n" . $last_of_message;

            $this->send($text_reply);
        }



        if ($this->left_member) {

            $user = $this->db->query("SELECT * FROM group_users WHERE telegram_id = ? AND group_tel_id = ?" , [$this->left_member['id'], $this->chat_id])->row_array();

            $left_user = [

                'is_left' => 1,

                'left_time' => time()

            ];

            $this->db->update('group_users', $left_user, ['id' => $user['id']]);

        }
            

        if ($this->message['message']['entities'][0]['type'] && $this->message['message']['entities'][0]['type'] == 'mention' && $this->message_text[0] == '@') {

            $admins = $this->send->get_admins($this->chat_id);            
            if (in_array($this->user_id, $admins)) {

                $user = $this->db->query("SELECT * FROM users WHERE user_tel_username = ?", substr(trim($this->message_text), 1))->row_array();

                $user_invites = $this->db->query("SELECT COUNT(*) as count FROM group_users WHERE added_by_id = ? AND is_left = 0 AND group_tel_id = ?", [$user['user_tel_id'], $this->chat_id])->row_array()['count'];

                $user_invites_left = $this->db->query("SELECT COUNT(*) as count FROM group_users WHERE added_by_id = ? AND is_left = 1 AND group_tel_id = ?", [$user['user_tel_id'], $this->chat_id ])->row_array()['count'];

                $text_reply = "{$user['fname']} - [@".$user['user_tel_username']."]

🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷

مجموع دعوت های {$user['fname']} : ". ($user_invites + $user_invites_left) ."
تعداد لفت داده‌ها: {$user_invites_left}
تعداد مفید: {$user_invites}

🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷";
                $this->send($text_reply);

            }

        }

        if (
            $this->message['message']['entities'][0]['type'] && 
            $this->message['message']['entities'][0]['type'] == 'text_mention'
        ) {

            $admins = $this->send->get_admins($this->chat_id);            

            if (in_array($this->user_id, $admins)) {

                $user_invites = $this->db->query("SELECT COUNT(*) as count FROM group_users WHERE added_by_id = ? AND is_left = 0 AND group_tel_id = ?", [$this->message['message']['entities'][0]['user']['id'], $this->chat_id ] )->row_array()['count'];

                $user_invites_left = $this->db->query("SELECT COUNT(*) as count FROM group_users WHERE added_by_id = ? AND is_left = 1 AND group_tel_id = ?", [$this->message['message']['entities'][0]['user']['id'], $this->chat_id ] )->row_array()['count'];

                $text_reply = "{$this->message['message']['entities'][0]['user']['first_name']} - [@{$this->message['message']['entities'][0]['user']['username']}]

🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷

 مجموع دعوت های {$this->message['message']['entities'][0]['user']['first_name']} : ". $user_invites + $user_invites_left ."
‎تعداد لفت داده ها: {$user_invites_left}
تعداد مفید: {$user_invites}

🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷🇹🇷🇮🇷";

                $this->send($text_reply);

            }

        }


        if ($this->message_text == '/invite') {
            $admins = $this->send->get_admins($this->group_id);   
            
            if (in_array($this->user_id, $admins)) {
                
                $invites = $this->db->query("SELECT *, COUNT(*) as count FROM `group_users`, users WHERE `added_by_id` = users.user_tel_id AND `group_tel_id` = ? AND `is_left` = 0 GROUP BY `added_by_id` ORDER BY count desc LIMIT 100", [$this->group_id])->result_array();
  
                $text = "لیست کل دعوت کنندگان\n\n";

                foreach ($invites as $key => $value) {
                    if ($value['fname']) {
                        $text .= "#n ". ($key + 1) ." - " . $value["fname"]. " " . $value['lname'] . " (@{$value['user_tel_username']}) => " . $value['count']."\n";
                    }
                }

                $this->send($text);
            }
            
        }

        if (substr($this->message_text ,0 , 12) == "/lastmessage" ) {
            $admins = $this->send->get_admins($this->chat_id);  
            
            if (in_array($this->user_id, $admins)) {
                $settings = $this->db->query("SELECT * FROM settings where name='last_of_message'")->row_array();
                $this->db->update('settings', ['value' => substr($this->message_text , 12)], ['id' => $settings['id']]);

                $text_reply = "با موفقیت ثبت شد";

                $this->send($text_reply);
            }

        }

        if ($this->sticker) {
            $settings = $this->db->query("SELECT * FROM settings WHERE group_id = ? and name='sticker_send_allowed'", $this->chat_id)->row_array();

            if (!$settings['value']) {
                $this->send->deleteMessage($this->message['message']['chat']['id'], $this->message['message']['message_id']);
            }
        }

        if ($this->photo) {
            $settings = $this->db->query("SELECT * FROM settings WHERE group_id = ? and name='photo_send_allowed'", $this->chat_id)->row_array();

            if (!$settings['value']) {
                $this->send->deleteMessage($this->message['message']['chat']['id'], $this->message['message']['message_id']);
            }
        }

        if ($this->voice) {
            $settings = $this->db->query("SELECT * FROM settings WHERE group_id = ? and name='voice_send_allowed'", $this->chat_id)->row_array();

            if (!$settings['value']) {
                $this->send->deleteMessage($this->message['message']['chat']['id'], $this->message['message']['message_id']);
            }
        }
        

        if ($this->forward) {
            $settings = $this->db->query("SELECT * FROM settings WHERE group_id = ? and name='forward_send_allowed'", $this->chat_id)->row_array();

            if (!$settings['value']) {
                $this->send->deleteMessage($this->message['message']['chat']['id'], $this->message['message']['message_id']);
            }
        }

        if ($this->file) {
            $settings = $this->db->query("SELECT * FROM settings WHERE group_id = ? and name='file_send_allowed'", $this->chat_id)->row_array();

            if (!$settings['value']) {
                $this->send->deleteMessage($this->message['message']['chat']['id'], $this->message['message']['message_id']);
            }
        }

        if ($this->gif == 'image/gif' || $this->gif == "video/mp4") {
            $settings = $this->db->query("SELECT * FROM settings WHERE group_id = ? and name='gif_send_allowed'", $this->chat_id)->row_array();

            if (!$settings['value']) {
                $this->send->deleteMessage($this->message['message']['chat']['id'], $this->message['message']['message_id']);
            }
        }

        if ($this->link) {        	
        	array_map(function ($item)
        	{
        		if ($item['type'] == 'text_link' || $item['type'] == 'url') {

        			$settings = $this->db->query("SELECT * FROM settings WHERE group_id = ? and name='link_send_allowed'", $this->chat_id)->row_array();

		            if (!$settings['value']) {
		                $this->send->deleteMessage($this->message['message']['chat']['id'], $this->message['message']['message_id']);
		            }
        		}
        	}, $this->message['message']['caption_entities'] ? $this->message['message']['caption_entities'] : $this->message['message']['entities']);
        }

        if ($this->audio) {
            $settings = $this->db->query("SELECT * FROM settings WHERE group_id = ? and name='audio_send_allowed'", $this->chat_id)->row_array();

            if (!$settings['value']) {
                $this->send->deleteMessage($this->message['message']['chat']['id'], $this->message['message']['message_id']);
            }
        }

        if ($this->video) {
            $settings = $this->db->query("SELECT * FROM settings WHERE group_id = ? and name='video_send_allowed'", $this->chat_id)->row_array();

            if (!$settings['value']) {
                $this->send->deleteMessage($this->message['message']['chat']['id'], $this->message['message']['message_id']);
            }
        }

        if ($this->message_text == '/setting') {

            $admins = $this->send->get_admins($this->group_id);  
            
            if (!in_array($this->user_id, $admins)) {
                return false;
            }
            
            //  file_get_contents("https://api.telegram.org/bot328312279:AAEgIRpW8HDF8RB3gQR9KdU6_tm8feZSSkI/sendMessage?chat_id=116032859&parse_mode=HTML&text=<code>".json_encode([$this->message])."</code>");
            
            $settings = $this->db->query("SELECT * FROM settings WHERE group_id = ?" , $this->group_id )->result_array();
            


            $fields = [];
            $dfgdf = [];
            foreach ($settings as $key => $value) {
                if (in_array(explode("_", $value['name'])[0], $this->send_allowd)) {
                    $fields[] = explode("_", $value['name'])[0] . "-" . ($value['value'] ? "✅" : "⛔️");
                    $dfgdf[] = explode("_", $value['name'])[0];
                }
            }

            if (count($this->send_allowd) % 2 == 1) {
            	$layout = array_fill(0,(count($this->send_allowd) - 1)/ 2, 2);
            	array_push($layout, 1);
            }
            elseif (count($this->send_allowd) % 2 == 0) {
            	$layout = array_fill(0,(count($this->send_allowd))/ 2, 2);
            }

            $keyboard = [
                $fields,
                $layout,
                array_fill(0,count($this->send_allowd),"callback_data"),
                $dfgdf,                
            ];

                    
            

            $this->send("برای ممنوعیت ارسال یا اجازه ارسال روی هر کدام کلیک کنید", $this->chat_id, $keyboard);
        }
        

        if ($this->message['callback_query']) {
            if (in_array($this->message['callback_query']['data'], $this->send_allowd)) {
                $settings = $this->db->query("SELECT * FROM settings WHERE name = ? AND group_id = ?" , [ $this->message['callback_query']['data'].'_send_allowed' , $this->group_id])->row_array();
                // $settings = $this->db->query("SELECT * FROM settings WHERE name = ? AND group_id = ?" , [ $this->message['callback_query']['data'].'_send_allowed' , $this->message['callback_query']['message']['chat']['id']])->row_array();
                $insert = [
                    'value' => !$settings['value'],
                ];

                $dgdfg = $this->db->update('settings', $insert, ['id' => $settings['id']]);

                $settings = $this->db->query("SELECT * FROM settings WHERE group_id = ?" , $this->group_id )->result_array();
                $fields = [];
                $dfgdf = [];
                foreach ($settings as $key => $value) {
                    if (in_array(explode("_", $value['name'])[0], $this->send_allowd)) {
                        $fields[] = explode("_", $value['name'])[0] . "-" . ($value['value'] ? "✅" : "⛔️");
                        $dfgdf[] = explode("_", $value['name'])[0];
                    }
                }

                if (count($this->send_allowd) % 2 == 1) {
	            	$layout = array_fill(0,(count($this->send_allowd) - 1)/ 2, 2);
	            	array_push($layout, 1);
	            }
	            elseif (count($this->send_allowd) % 2 == 0) {
	            	$layout = array_fill(0,(count($this->send_allowd))/ 2, 2);
	            }

	            $keyboard = [
	                $fields,
	                $layout,
	                array_fill(0,count($this->send_allowd),"callback_data"),
	                $dfgdf,                
	            ];
                $this->send->editReply(
                    $this->message['callback_query']['message']['chat']['id'], 
                    $this->message['callback_query']['message']['message_id'],
                    $this->markups->inline_keyboard($keyboard)
                );
                
            }
        }

        if ($this->message_text == "/detectLeft") {
            $this->send("لطفا کمی صبر کنید");            
            $lefts = $this->detectLeft();
            $text = "تعداد لفت داده ها از سری پیش:" . count($lefts);
            $this->send($text);
        }

        if ($this->message) {
            $admins = $this->send->get_admins($this->group_id);  

            if (in_array($this->user_id, $admins)) {
                return false;
            }
            $settings = $this->db->query("SELECT * FROM settings WHERE group_id = ? and name='chat_send_allowed'", $this->chat_id)->row_array();

            if (!$settings['value']) {
                $this->send->deleteMessage($this->message['message']['chat']['id'], $this->message['message']['message_id']);
            }
        }
    }
    
    function detectLeft()
    {
        $users = $this->db->query("SELECT * FROM group_users where group_tel_id = ? AND is_left='0' ", $this->group_id )->result_array();
        $is_left = []; 
        $i = 0;       
        foreach ($users as $key => $value) {
            // $result[] = json_decode(file_get_contents("https://api.telegram.org/bot563281132:AAFim8ZLj5kzQ32_6T-_iosgRGZTFhYGAxk/getChatMember?chat_id=-1001180529942&user_id=". $value['telegram_id']), true);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://api.telegram.org/bot563281132:AAFim8ZLj5kzQ32_6T-_iosgRGZTFhYGAxk/getChatMember");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                        "chat_id=". $this->group_id ."&user_id=". $value['telegram_id']);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($ch);
            curl_close ($ch);
            $result = json_decode($server_output, true);
            if ($result['ok'] == false) {
                $is_left[$i]['data'] = $value; 
                $is_left[$i]['updated'] = $this->db->update('group_users', ['is_left' => '1'], ['id' => $value['id']]); 
                $i++;
            }
        }

        return $is_left;
        
    }



    public function send($text_reply = "asdas", $id = '', $reply_markup = null)
    {

        $id = $id ? $id : $this->chat_id;

        $message_info = array(

	      'chat_id' => $id,

	      'text' => urlencode($text_reply),

        );
        
        if ($reply_markup) {
            $message_info['reply_markup'] = $this->markups->inline_keyboard($reply_markup);
        }

        // file_get_contents("https://api.telegram.org/bot328312279:AAEgIRpW8HDF8RB3gQR9KdU6_tm8feZSSkI/sendMessage?chat_id={$id}&parse_mode=HTML&text=<code>".json_encode($message_info)."</code>");            

	    $this->send->sendMessage($message_info);

    }

}

