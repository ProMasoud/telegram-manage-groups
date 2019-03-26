<?php

/**
 * Auhtor: Masoud Tavakkoli
 */
class Db_user extends CI_Model
{
    public function set_user($result, $edit)
    {
        $data = array(
            'user_tel_id' => $result[$edit]['from']['id'],
        );
        if (isset($result[$edit]['from']['first_name'])) {
            $data['fname'] = $result[$edit]['from']['first_name'];
        }
        if (isset($result[$edit]['from']['last_name'])) {
            $data['lname'] = $result[$edit]['from']['last_name'];
        }
        if (isset($result[$edit]['from']['username'])) {
            $data['user_tel_username'] = $result[$edit]['from']['username'];
        }


        $result = $this->new_user_insert($data);
        if ($result == 1) {
            return true;
        } else {
            return false;
        }
    }


    public function new_user_insert($data)
    {
        $query = $this->db->query("SELECT * FROM users WHERE user_tel_id =?", $data['user_tel_id']);
        $update_info = $this->db->query("SELECT users.id FROM users WHERE users.user_tel_id = ?", $data['user_tel_id'])->row_array();

        if ($query->num_rows() == 0) {
            $data['created_at'] = time();
            $data['updated_at'] = time();
            $this->db->insert('users', $data);
            return true;
        } else {
            $data['updated_at'] = time();
            $this->db->update('users', $data, array('id' => $update_info['id']));
            return false;
        }
    }
}
