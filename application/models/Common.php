<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Class name: Common
 * @description :  This class contains all functions which will be used to show error message, warnings and success message
 * @author: Ajay Singh
 */
class Common extends CI_Model {
  /*
   * @description :  This function called automatically when we call this call function or use any class variable
   * @Method name: __construct
   */
  public function __construct(){
    parent  :: __construct();
  }
  /*
   * @parameter : table, data
   * @description :  This function is developed for insert
   * @Method name: insert
   */
  public function insert($table , $data){
     
      $this->db->insert($table, $data);  
      //echo $this->db->last_query();
      return $this->db->insert_id();
  }
  /*
   * @parameter : table, condition
   * @description :  This function is developed for listing
   * @Method name: _getList
   */
  public function _getList($table, $condition='',$orderby='',$column='') {
    if(!empty($table)){
      $this->db->from($table);
      if($condition){
        $this->db->where($condition);
      }
      if($orderby){
        $this->db->order_by($orderby);
      }
      $query = $this->db->get();  
      
      if($query->num_rows() > 0){
        return $query->result_array();
      }else{
        return false;
      }
    }   
  }
  /*
  * @parameter : table, condition
  * @description :  This function is developed for get by
  * @Method name: _getById
  */
  public function _getById($table ,$condition='' ){
    if(!empty($table)){
      $this->db->from($table);
      $this->db->where($condition);
      $query = $this->db->get();
      if($query->num_rows() > 0){
        return $query->row_array();
      }else{
        return false;
      }
    }
  }
  /*
  * @parameter : table, condition , where
  * @description :  This function is developed to update data
  * @Method name: update
  */
  public function update($table,$condition,$data){
    $this->db->update($table, $data, $condition); 
    return $this->db->affected_rows();
  }
  /*
  * @parameter : table, condition 
  * @description :  This function is developed to delete data
  * @Method name: delete_by_id
  */
  public function delete_by_id($table,$condition){
    $this->db->where($condition);
    $this->db->delete($table);
    return $this->db->affected_rows();
  }

      
   /*
  * @parameter : table, condition 
  * @description :  This function is developed to delete data
  * @Method name: delete_by_id
  */
  public function check_by_id($table,$condition){

  $result =array();
    $this->db->from($table);
      $this->db->where($condition);
      $query = $this->db->get();
      if($query->num_rows() > 0){
         $result = $query->row_array();
        return $result;
      }else{
        return $result;
      }
  }
  /*
  * @parameter : query 
  * @description :  This function is developed to customer query
  * @Method name: customeQuery
  */
  public function customeQuery($query){
    $result =array();
    $queryResult = $this->db->query($query);
    $result = $queryResult->result_array();
    if($result){
      return $result;
    }else{
      return $result;
    }
  }
  /*
  * @parameter : since_time 
  * @description :  This function is developed to time diffrence 
  * @Method name: getTimeDifference
  */
  function getTimeDifference($since_time)
  {
    $current_time = date('Y-m-d H.i.s');
    $to_time = strtotime($current_time);
    $from_time = strtotime($since_time);
    $difference=$to_time - $from_time;

    $second = 1;
    $minute = 60*$second;
    $hour   = 60*$minute;
    $day    = 24*$hour;

    $days   = floor($difference/$day);
    $hours = floor(($difference%$day)/$hour);
    $minutes = floor((($difference%$day)%$hour)/$minute);
    $seconds= floor(((($difference%$day)%$hour)%$minute)/$second);

    if($days!='')
    {
        $returnTime = date('M j, Y', strtotime($since_time));;
    }
    elseif($days=='' && $hours!='')
    {
        $returnTime = $hours.' '.'Hrs'.' '.'Ago';
    }
    elseif($minutes!='')
    {
        $returnTime = $minutes.' '.'Min'.' '.'Ago';
    }
    elseif($seconds!='')
    {
        $returnTime = $seconds.' '.'Sec'.' '.'Ago';
    }
    else
    {
        $returnTime='Just Now';
    }
    return $returnTime;
  }

}
