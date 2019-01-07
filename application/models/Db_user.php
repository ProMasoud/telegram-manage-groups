<?php

/**
 * Auhtor: Masoud Tavakkoli
 */
class Db_user extends CI_Model
{

    function set_user($result)
    {
        $data = array(
            'user_tel_id' => $result['message']['from']['id'],
        );
        if (isset($result['message']['from']['first_name'])) {
            $data['fname'] = $result['message']['from']['first_name'];
        }
        if (isset($result['message']['from']['last_name'])) {
            $data['lname'] = $result['message']['from']['last_name'];
        }
        if (isset($result['message']['from']['username'])) {
            $data['user_tel_username'] = $result['message']['from']['username'];
        }


        $result = $this->new_user_insert($data);
        if ($result == 1) {
            return TRUE;
        }else {
            return FALSE;
        }
    }


    function new_user_insert($data){
        $query = $this->db->query("SELECT * FROM users WHERE user_tel_id =?", $data['user_tel_id']);
        $update_info = $this->db->query("SELECT users.id FROM users WHERE users.user_tel_id = ?",$data['user_tel_id'])->row_array();

        if ($query->num_rows() == 0) {
              $data['created_at'] = time();
              $data['updated_at'] = time();
              $this->db->insert('users' , $data);
              return TRUE;
        }else {
              $data['updated_at'] = time();
              $this->db->update('users',$data,array('id' => $update_info['id']));
              return FALSE;
        }
    }
}
