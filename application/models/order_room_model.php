<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Order_room_model extends MY_Model
	{
            var $table = 'order';
            var $key = 'order_id';

            function get_list_room(){
                $this->db->from('order');
                $this->db->join('user','user.user_id=order.user_id');
                $this->db->join('post_room','post_room.post_room_id = order.post_room_id');
                return $this->db->get();
            }
	}
?>