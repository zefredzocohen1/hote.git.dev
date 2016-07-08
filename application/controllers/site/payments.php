<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Hashids\Hashids;
class payments extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mail_template_model');
        $this->load->model('mail_history_model');
        $this->load->library('email');
         $this->load->model('order_room_model');
//         $this->load->model('mail_history_model');
    }

    public function index()
    {
        
    }
    function book($id=''){
        //check điều kiện
        $user_id = $this->session->userdata('user_id');
        if(!isset($user_id)||$user_id==''){
            redirect(base_url());
        }
        if($id=''){
            redirect(base_url());
        }
        $id_decode = $this->config->item('encode_id')->decode($id);;
        if(count($id_decode)<=0){
            redirect(base_url());
        }else{
            $id_encode = $id;
        }
        
        $checkin = $this->input->get('checkin')?$this->input->get('checkin'):'';
        $checkout = $this->input->get('checkout')?$this->input->get('checkout'):'';
        $guests = $this->input->get('guests')?$this->input->get('guests'):'';
        if($checkin==''||$checkout==''||$guests==''){
            redirect(base_url().'room/room_detail/'.$id_encode);
        }
        // thanh toán online
        // thêm vào db
        $input = array(
            'where'=>array(
                    'post_room_id'=>$id_decode[0],
                )
            );
        $date1 = new DateTime();
        $date2 = new DateTime();
        $dateNow = new DateTime();
        $data['checkin'] = $date1->setDate(
                date('Y',  strtotime(str_replace('/', '-', $checkin))), 
                date('m',  strtotime(str_replace('/', '-', $checkin))), 
                date('d',  strtotime(str_replace('/', '-', $checkin)))
            );
        $data['guests'] = $guests;
        $data['checkout'] = $date2->setDate(
                date('Y',  strtotime(str_replace('/', '-', $checkout))), 
                date('m',  strtotime(str_replace('/', '-', $checkout))), 
                date('d',  strtotime(str_replace('/', '-', $checkout)))
            );
        if($data['checkin']>$data['checkout']){
            redirect(base_url().'room/room_detail/'.$data['id_encode']);
        }
        if($data['checkin']<  $dateNow||$data['checkout']<$dateNow){
            redirect(base_url().'room/room_detail/'.$data['id_encode']);
        }
        $prices = $this->post_room_model->get_row($input);
        $data['name_room'] = $prices->post_room_name;
        //giá 1 đêm
        $data['price_night_vn'] = $prices->price_night_vn;
        $data['max_guest'] = $prices->num_guest;
        // giá vượt quá số người
        $data['price_guest_more_vn'] = $prices->price_guest_more_vn;
        //phí dọn dẹp
        $data['clearning_fee_vn'] = $prices->clearning_fee_vn;
        $data['sub_day'] = $data['checkout']->diff($data['checkin']);
        $data['distance_day'] = $data['sub_day']->days+1;
        $data['price_all_night'] = $data['distance_day']*$data['price_night_vn'];
        if($guests<=$data['max_guest']){
            $data['guest_change'] = 0;
        }
        else{
            $data['guest_change'] = $guests-$data['max_guest'];
        }
        if($data['guest_change']<=0){
            $data['price_all_night_add_fee'] = $data['price_all_night']+$data['clearning_fee_vn'];
        }else{
            $data['price_all_night_add_fee'] = $data['price_all_night']+$data['clearning_fee_vn']+($data['guest_change'])*$data['price_guest_more_vn'];
        }
         $data_insert = array(
             'post_room_id'=>$id_decode[0],
             'user_id'=>$user_id,
             'payment_type'=>$data['price_all_night_add_fee'],
             'checkin'=>$data['checkin'],
             'checkout'=>$data['checkout'],
             'guests'=>$data['guests'],
         );
         $this->order_room_model->create($data_insert);
        //gửi email
        $input = array();
        $input = array(
            'where'=>array(
                    'user_id'=>$user_id,
                )
            );
        $data['doitac'] = $this->user_model->get_row(array('where'=>array('user_id'=>$prices->user_id)));
        $data['user'] = $this->user_model->get_row($input);
        $config = get_config_email($this->config->item('address_email'),$this->config->item('pass_email'));
        echo $this->email->print_debugger();
//        $email_contact$this->email->get_row()
        echo $this->sendEmail($this, $data['user']->email, 'Email thông báo đặt phòng', 'email đặt phòng thành công từ người quản trị đến người đặt phòng',$config);
        echo $this->sendEmail($this, $this->config->item('address_email'), 'Email thông báo đặt phòng', 'email đặt phòng thành công từ hệ thống đến người quản trị ',$config);
        echo $this->sendEmail($this, $data['doitac']->email, 'Email thông báo đặt phòng', 'email đặt phòng thành công từ hệ thống đến Đối tác ',$config);
    }
    
    function sendEmail(&$mail_object, $mailTo,$mailSubject,$content,$config){
        $this->email->initialize($config);
        $this->email->from($this->config->item('address_email'), $this->config->item('name_company'));
        $mail_object->email->to($mailTo); 
        $mail_object->email->set_mailtype("html");
        $mail_object->email->subject($mailSubject);
//         $body = $this->load->view('email_config_order.php',$data,TRUE);
        $mail_object->email->message($content);   
        return $mail_object->email->send();
    }


}