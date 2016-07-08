<?php
class MY_Lang extends CI_Lang{
    function MY_Lang(){
        parent::__construct();
    }
    function switch_to($lang){
        $CI = &get_instance();
        if(is_string($lang)){
            $CI->config->set_item('language',$lang);
//            $loaded = $this->is_loaded;
            $this->is_loaded = array();
//            foreach ($loaded as $file){
                $this->load(array('block','config','side_bar'));
//            }
        }
    } 
}
