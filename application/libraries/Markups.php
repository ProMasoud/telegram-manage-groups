<?php

/**
 * Author: Masoud Tavakkoli
 */
class Markups
{
    function reply_keyboard($keyboard)
    {
        $layout = $keyboard[1];
        $fields = $keyboard[0];
        $contact = $keyboard[2] ? $keyboard[2] : FALSE;
        $location = $keyboard[3] ? $keyboard[3] : FALSE;
        $keyboard_fields = array();

        if (count($fields) != array_sum($layout)) {
            return FALSE;
        }


        $m = 0;
        for ($i=1; $i <= count($layout); $i++) {
            for ($j=1; $j <= $layout[$i - 1]; $j++) {
                $keyboard_fields[$i-1][] = array('text' => $fields[$m] , 'request_contact' => $contact[$m] , 'request_location' => $location[$m]);
                $m++;
            }
        }

        $reply_markup = array(
            "keyboard" => $keyboard_fields,
            "resize_keyboard" => TRUE,
            "one_time_keyboard" => TRUE
        );

        return json_encode($reply_markup);
    }

    function inline_keyboard($inline)
    {
        $layout = $inline[1];
        $fields = $inline[0];
        $method = $inline[2] ? $inline[2] : "url";
        $res = $inline[3];

        $keyboard_fields = array();

        if (count($fields) != array_sum($layout)) {
            return FALSE;
        }


        $m = 0;
        for ($i=1; $i <= count($layout); $i++) {
            for ($j=1; $j <= $layout[$i - 1]; $j++) {
                $keyboard_fields[$i-1][] = array('text' => $fields[$m], $method[$m] => $res[$m]);
                $m++;
            }
        }


        $reply_markup = array(
            "inline_keyboard" => $keyboard_fields,
        );
        return json_encode($reply_markup);
    }
}
