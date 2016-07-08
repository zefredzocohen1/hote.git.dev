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
        $id_decode = $this->config->item('encode_id');
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
        die;
        $config = get_config_email($this->config->item('address_email'),$this->config->item('pass_email'));
        echo $this->email->print_debugger();
        echo 'Đặt phòng thành công';
        echo $this->sendEmail($this, 'lehai04.1991.9@gmail.com', 'Email thông báo đặt phòng', 'email đặt phòng thành công từ người quản trị đến người đặt phòng',$config);
        echo $this->sendEmail($this, $this->config->item('address_email'), 'Email thông báo đặt phòng', 'email đặt phòng thành công từ hệ thống đến người quản trị ',$config);
        echo $this->sendEmail($this, 'zefredzocohen@gmail.com', 'Email thông báo đặt phòng', 'email đặt phòng thành công từ hệ thống đến Đối tác ',$config);
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