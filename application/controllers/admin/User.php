<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'AdminHome.php';
class User extends AdminHome
{
    public function __construct()
    {
        parent::__construct(get_class());
        $this->load->model('user_model');
    }

    public function index()
    {
        $this->load->model("role_model");
        $input = array();
        $input['order'] = array('role_id','ASC');
        $list_role = $this->role_model->get_list($input);
        $data['list_role'] = $list_role;

        $this->load->library('pagination');
        $total = $this->user_model->get_total();

        $config = array();
        $config["total_rows"] = $total;
        $config['base_url'] = base_url('admin/user/index');
        $config['per_page'] = 15;
        $config['uri_segment'] = 4;
        $config['next_link'] = 'Trang kế tiếp';
        $config['prev_link'] = 'Trang trước';
        $config['use_page_numbers'] = TRUE;
        $this->pagination->initialize($config);
        if($this->uri->segment('4') && $this->uri->segment('4') > 1){
            $segment = $this->uri->segment('4');
        }else{
            $segment = 1;
        }

        $segment = (int)$segment;
        $start = ($segment - 1)*$config['per_page'];
        $pagination_link = $this->pagination->create_links();
        $data['pagination_link'] = $pagination_link;

        $message = $this->session->flashdata();
        $data['message'] = $message;

        $input = array();
        $input['limit'] = array($config['per_page'], $start);
        $input['order'] = array('user_id','DESC');

      
        /*-- Lọc user_name--*/
        $user_name = $this->input->get('user_name');
        if($user_name){
            $input['like'] = array('user_name', $user_name);
        }
          /*-- Lọc ozganzation--*/
        $ozganzation = $this->input->get('ozganzation');

        if($ozganzation){
            $input['or_like'] = array('ozganzation', $ozganzation);
        }
        /*-- Lọc role--*/
        $role_id = $this->input->get('role');
        $role_id = (int)$role_id;
        if($role_id){
             $input['where']['user.role_id'] = $role_id;
        }

        //Lay session adminLogin de list user != adminLogin
        if(!is_NULL($this->session->userdata('adminLogin'))){
            $adminLogin = $this->session->userdata('adminLogin');
            $input['where']['user_id !='] = $adminLogin['user_id'];
        }
        $list = $this->user_model->getList($input);
        $data['total'] = $total;
        $data['list'] = $list;

        $data['title'] = 'Danh sách tài khoản';
        $data['temp'] = 'admin/user/index';
        $this->load->view('admin/layout', isset($data) ? ($data) : null);


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

        $input = array();
        $input['order'] = array('role_id','ASC');
        $list_role = $this->role_model->get_list($input);
        $data['list_role'] = $list_role;

        if($this->input->post()){


            $this->form_validation->set_rules('last_name', 'Họ', 'trim|required');

            $this->form_validation->set_rules('first_name','Tên', 'trim|required');
            $this->form_validation->set_rules('user_name','Tên đăng nhập', 'trim|required|min_length[5]|max_length[12]|callback_checkUserName');
            $this->form_validation->set_rules('password','Mật khẩu', 'trim|required|min_length[6]|max_length[12]');
            $this->form_validation->set_rules('password_comfirm','Mật khẩu xác nhận', 'trim|required|min_length[6]|max_length[12]|matches[password]');
            $this->form_validation->set_rules('email','Email', 'trim|required|valid_email|callback_checkEmail');

            if($this->form_validation->run()){

                $last_name  = $this->input->post('last_name');
                $first_name = $this->input->post('first_name');
                $ozganzation= $this->input->post('ozganzation'); //tên cơ quan, tổ chức
                $user_name  = $this->input->post('user_name');
                $password   = md5($this->input->post('password'));
                $email      = $this->input->post('email');
                $role_id    = $this->input->post('role_id');
                $created    = date('Y:m:d H:i:s');

                $data = array(
                    'last_name' => $last_name,
                    'first_name' => $first_name,
                    'ozganzation' => $ozganzation,
                    'user_name' => $user_name,
                    'password' => $password,
                    'email' => $email,
                    'role_id' => $role_id,    
                    'created'=>$created 
                );

                if($this->user_model->create($data)){

                    $this->session->set_flashdata('message', 'Thêm dữ liệu thành công!');
                }else{
                    $this->session->set_flashdata('message', 'Thêm dữ liệu thất bại!');
                }
                redirect(admin_url('user/index'));
            }
        }
        $data['title'] = 'Đăng kí tài khoản';
        $data['temp'] = ('admin/user/create');
        $this->load->view('admin/layout', isset($data) ? ($data) : null);
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