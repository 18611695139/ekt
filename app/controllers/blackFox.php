<?php
header("Content-Type:text/html;charset=utf-8");
class BlackFox extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 验证黑狐;
     */
    public function signin()
    {
        //这个以后通过接口获取企业信息；
        $vcc_data = array(
            'db_main'=>'192.168.1.4',
            'db_slave'=>'192.168.1.4',
            'db_name'=>'ekt_382',
            'db_user'=>'root',
            'db_pwd'=>'passw0rd',//由于本地为空；方便测试
        );
        $this->session->set_userdata($vcc_data);
        $this->load->model('user_model');
        $this->load->model('login_model');
        $vcc_code = $this->input->get('vcc_code');
        $user_num = $this->input->get('user_num','');
        $digest  = $this->input->get('digest');
        $pho_num = $this->input->get('pho_num');
        $nonce = $this->input->get('nonce');
        //先根据user_num 查出 user_id;
        $password = $this->user_model->getUserPassByNum($user_num); //通过user_num 查询密码；
        if (empty($password)) {
            exit("验证失败");
        }
        $checkDigest = base64_encode(sha1(md5($nonce).$password.$vcc_code.$user_num.$pho_num));
        if ($checkDigest == $digest) {
            $this->input->set_cookie('pho_num',$pho_num,86500);
            $this->input->set_cookie('user_num',$user_num,86500);
            $this->input->set_cookie('vcc_code',$vcc_code,86500);
            $sign_res = $this->login_model->check_login($vcc_code,$user_num,$password,$pho_num); //调用系统自身登陆方法
            if($sign_res['signin'])
            {
                log_action(1,"登录系统");
                header("Location:index.php?c=main");
            }
        }
        exit("验证失败");
    }
}
