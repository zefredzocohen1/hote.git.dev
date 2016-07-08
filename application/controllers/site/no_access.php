<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class No_access extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($module_id='')
    {
        $data['module_id'] = $module_id;
        $this->load->view('errors/no_access', isset($data) ? ($data) : null);


    }

    function checkUserName($user_name){

        $where = array();
        $where = array('user_name'=>$user_name);
        $check = $this->user_model->check_exists($where);
        if($check > 0){
            $this->form_validation->set_message('checkUserName','{field} đã tồn tại.');
            return false;
        }else{
            return true;
        }
    }
    function checkEmail($email){
        
        $where = array();
        $where = array('email'=>$email);
        $check = $this->user_model->check_exists($where);
        if($check > 0){
            $this->form_validation->set_message('checkEmail','{field} đã tồn tại.');
            return false;
        }else{
            return true;
        }
    }
    public function create(){  
        $this->load->model("role_model");
        $this->load->library('form_validation');
        $this->load->helper('form');
        $role_id = 3;

        $input = array();
        $input['order'] = array('role_id','ASC');
        $list_role = $this->role_model->get_list($input);
        $data['list_role'] = $list_role;
        if(count($_POST)>0){

//
//            $this->form_validation->set_rules('last_name', 'Họ', 'trim|required');
//
//            $this->form_validation->set_rules('first_name','Tên', 'trim|required');
//            $this->form_validation->set_rules('user_name','Tên đăng nhập', 'trim|required|min_length[5]|max_length[12]|callback_checkUserName');
//            $this->form_validation->set_rules('password','Mật khẩu', 'trim|required|min_length[6]|max_length[12]');
//            $this->form_validation->set_rules('email','Email', 'trim|required|valid_email|callback_checkEmail');
//
//            if($this->form_validation->run()){

                $last_name  = $this->input->post('last_name');
                $first_name = $this->input->post('first_name');
                $user_name  = $this->input->post('user_name');
                $password   = md5($this->input->post('password'));
                $email      = $this->input->post('email');
                $role_id    = $role_id;
                $created    = date('Y:m:d H:i:s');

                $data = array(
                    'last_name' => $last_name,
                    'first_name' => $first_name,
                    'user_name' => $user_name,
                    'password' => $password,
                    'email' => $email,
                    'role_id' => $role_id,  
                );
                if($this->user_model->create($data)){
                    $this->session->set_userdata($data);
                    $this->session->set_flashdata('message', 'Thêm dữ liệu thành công!');
                }else{
                    $this->session->set_flashdata('message', 'Thêm dữ liệu thất bại!');
                }
                redirect(base_url('site/home/index'));
//            }
        }
        $data['title'] = 'Đăng kí tài khoản';
        $data['temp'] = ('site/home/index');
        $this->load->view('site/layout', isset($data) ? ($data) : null);
    }

    function edit(){
        $this->load->model("role_model");

        $this->load->library('form_validation');
        $this->load->helper('form');

        $id = $this->uri->rsegment('3');
        $id = (int)$id;

        $input = array();
        $input['order'] = array('role_id','ASC');
        $list_role = $this->role_model->get_list($input);
        $data['list_role'] = $list_role;

        $info = $this->user_model->get_info($id);

        if(!$info){

            $this->session->set_flashdata('message', 'Không tồn tại bản ghi!');
            redirect(admin_url('user'));
        }else{

            $data['info'] = $info;
        }

        if($this->input->post()){

            $this->form_validation->set_rules('last_name', 'Họ', 'trim|required');

            $this->form_validation->set_rules('first_name','Tên', 'trim|required');
            

            if($this->form_validation->run()){

                $last_name  = $this->input->post('last_name');
                $first_name = $this->input->post('first_name');
                $ozganzation= $this->input->post('ozganzation');
                $role_id    = $this->input->post('role_id');
                $updated    = date('Y:m:d H:i:s');

                $data = array(
                    'last_name' => $last_name,
                    'first_name' => $first_name,
                    'ozganzation' => $ozganzation,
                    'role_id' => $role_id,  
                    'updated'=>$updated 
                );

                if($this->user_model->update($id,$data)){

                    $this->session->set_flashdata('message', 'Cập nhật dữ liệu thành công!');
                }else{
                    $this->session->set_flashdata('message', 'Cập nhật dữ liệu thất bại!');
                }

                redirect(admin_url('user/index'));

            }
        }

        $data['title'] = 'Chỉnh sửa tài khoản';
        $data['temp'] = ('admin/user/edit');
        $this->load->view('admin/layout', isset($data) ? ($data) : null);
    }

    function view_account(){
        if(!is_null($this->session->userdata('adminLogin'))){
            $adminLogin = $this->session->userdata('adminLogin');
            // pre($adminLogin);
        }

        $data['title'] = 'Thông tin tài khoản';
        $data['temp'] = ('admin/user/view_account');
        $this->load->view('admin/layout', isset($data) ? ($data) : null);
    }

    function edit_account(){

        $data['title'] = 'Chỉnh sửa tài khoản';
        $data['temp'] = ('admin/user/edit_account');
        $this->load->view('admin/layout', isset($data) ? ($data) : null);
    }

    function status(){

        $id = $this->input->post('id');
        $id = (int)$id;

        $statusInfo = $this->user_model->get_info($id,'status');
        if(!$statusInfo){

            $this->session->set_flashdata('message', 'Không tồn tại bản ghi!');
            redirect(admin_url('user/index'));
        }else{
            if($statusInfo->status == 1){
                $data = array(
                    'status'=>0,
                );
            }else{
                $data = array(
                    'status'=>1,
                );
            }
        }
        if($this->user_model->update($id, $data)){
            
        }
    }

    function delete(){

        $id = $this->uri->rsegment(3);
        $id = (int)$id;
        $info = $this->user_model->get_info($id);
        if(!$info){
            $this->session->set_flashdata("message", 'Không tồn tại bản ghi');
            redirect(admin_url('user'));
        }
        if($this->user_model->delete($id)){
            $this->session->set_flashdata("message", 'Đã xóa dữ liệu');
            redirect(admin_url('user'));
        }
    }

    function logout(){
        if($this->session->userdata('adminLogin')){
            $this->session->sess_destroy();
        }
        redirect(admin_url('login'));
    }


}