<?php

class Statemente_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取关联多选框选项数据
     * @param int $field_id
     * @param string $sql_condition
     * @param string $searched
     * @return array|bool
     */
    public function get_checkbox_options($field_id=0,$sql_condition='', $searched='')
    {
        if(empty($field_id))
        {
            return false;
        }
        if(empty($searched))
        {
            $sql_condition=" AND serv_accept_time >='".strtotime(date("Y-m-d 00:00:00",time()))."' AND serv_accept_time<='".strtotime(date("Y-m-d 23:59:59",time()))."'";
        }
        else
        {
            $sql_condition=$sql_condition;
        }

        $options = array();
        $this->db_read->where('fields_id',$field_id);
        $this->db_read->select("id,fields_id,parent_id,name,type");
        $query = $this->db_read->get('est_fields_jl');
        $result = $query->result_array();

        if($result)
        {

            //获取统计数据
            $checkbox_data = $this->get_checkbox_data($field_id,$sql_condition);
            //整理数据
            $soncount = array();
            foreach($result as $v)
            {
                $soncount[$v['parent_id']][]=$v['id'];
            }

            foreach($result AS $key => $value)
            {
                if($value['type']==1)
                {
                    $options[$value["type"]][$value["id"]] = array('name'=>$value["name"],'p_id'=>$value["parent_id"],'soncount'=>empty($soncount[$value['id']])?1:(count($soncount[$value['id']])+1));
                }
                elseif($value['type']==2)
                {
                    $num = 0;
                    $ratio = '0%';
                    if(!empty($checkbox_data[$value["id"]]))
                    {
                        $num = $checkbox_data[$value["id"]];
                        $ratio = empty($checkbox_data['total'])?$ratio:(round($num/$checkbox_data['total'],4)*100).'%';
                    }
                    $options[$value["type"]][$value["id"]] = array('name'=>$value["name"],'p_id'=>$value["parent_id"],'num'=>$num,'ratio'=>$ratio);
                }
            }

        }
        return $options;
    }

    /**
     * 获取统计数据
     * @param int $field_id
     * @param $sql_condition
     * @return array|bool
     */
    public function get_checkbox_data($field_id=0,$sql_condition)
    {
        if(empty($field_id))
        {
            return false;
        }
        $data = array();
        $this->load->model('field_confirm_model');
        $field_info = $this->field_confirm_model->get_one_field_info($field_id);
        if(!empty($field_info['fields']))
        {
            $sql = "select ".$field_info['fields']."_2 AS serv FROM est_service WHERE ".$field_info['fields']."_2!='' AND ".$field_info['fields']."_2!='0' ".$sql_condition;
            $query = $this->db_read->query($sql);
            $result = $query->result_array();

            if($result)
            {
                $data_id_str = '';
                foreach($result as $value)
                {
                    $data_id_str .= $value['serv'];
                }
                $data_id_str = rtrim($data_id_str, ",");
                $data_id = explode(',', $data_id_str);
                $data = array_count_values($data_id);//判断选项ID出现次数
                $data['total'] = count($data_id);
            }
        }
        return $data;
    }

}