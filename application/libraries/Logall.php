<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Logall {

	public function __construct()
	{
		$this->CI=& get_instance();
		$this->CI->load->model('Auditmodel','am',true);	//Load the Auditmodel.php
		//$this->CI->load->library('form_validation');
		
	}
	
	public function Login_fail($logarry)
	{
		$datetime=date('Y-m-d H:i:s');
		$data['date']=date('Y-m-d', strtotime($datetime));
		$data['time']=date('H:i:s', strtotime($datetime));
		$data['username']=$logarry['username'];
		$data['password']=$logarry['password'];
		$data['ipaddress']=$logarry['ipaddress'];
		$this->CI->am->audit_login_fail($data);
	}
	
 
    public function Audit_ssn_start($logarray)	//To call this function when Login (Session Start)
    {
    	$data['user_id']=$logarray['user'];
    	//$data['email']=$logarray['email'];
    	$datetime=date('Y-m-d H:i:s');
    	$data['date']=date('Y-m-d', strtotime($datetime));
    	$data['time']=date('H:i:s', strtotime($datetime));
    	$data['ipaddress']=$logarray['ipaddress'];
    	$session_id=$this->CI->am->insert_audit_ssn($data);
    	
    	return $session_id;
    }
    
    public function Audit_start($logarray)	//Call the function when the transaction start
    {
    	$session_id = session_id();
    	$this->pr($session_id);
    	$session_id=$logarray['session'];
    	$this->pr($session_id);
    	$this->pr($logarray);
    	
    	if (!EMPTY($logarray['session']['session_id']))
    	{
    		//$data['user_id']=$logarray['user'];
    		foreach ($session_id as $headers => $rows)
    		{
    			//$header=$header.','.$headers.',';
    			$session_id=$rows;							
    			//$row=$row.','.$rows.',';
    		}
    		$this->pr($session_id);
    		$this->Audit_insert_All($session_id,$logarray);
    		//exit();
    	}
    }
    
    public function Audit_insert_All($session_id,$logarray)
    {
    	$data['session_id']=$session_id;
    	$tran_seq_no=$this->CI->am->last_tran_seq_no();
    	if($tran_seq_no==0)
    	{
    		$data['tran_seq_no']=1;
    	}
    	else
    	{
    		$tran_seq_no=$this->CI->am->last_tran_seq_no();
    		$data['tran_seq_no']=((int)$tran_seq_no[0]->tran_seq_no)+1;
    	}
    	$this->pr($tran_seq_no);
		//exit();
    	$datetime=date('Y-m-d H:i:s');
    	$data['date']=date('Y-m-d', strtotime($datetime));
    	$data['time']=date('H:i:s', strtotime($datetime));
    	$data['task_id']='Controller='.(string)$logarray['controller'].', Function='.(string)$logarray['function'];
    	$data['base_name']=(string)$logarray['dbname'];
    	$data['table_name']=(string)$logarray['table'];
    	$data['pkey']='pkey';
    	$data['pkey']=$this->CI->am->get_p_key((string)$logarray['table']);
    	
    	
    	$newdata=$logarray['data'];
    	$header='';
    	$row='';
    	$oldrow='';
    	if($logarray['status']=='insert')// if the transaction is insert
    	{
	    	foreach ($newdata as $headers => $rows)
	    	{
	    		if(!EMPTY($rows))
	    		{
	    			$header=$header.','.$headers.',';
	    			$row=$row.','.$rows.',';
	    		}
	    	}
	    	$data['field_id']=$header;
	    	$data['new_value']=$row;
    	}
    	else if($logarray['status']=='update')// if the transaction is update
    	{
	    	foreach ($logarray['data'] as $headers => $rows)
	    	{
	    		if(!EMPTY($rows))
	    		{
	    			$header=$header.','.$headers.',';
	    			$row=$row.','.$rows.',';
	    		}
	    	}
	    	foreach ($logarray['old_data'] as $headers => $rows)
	    	{
	    		if(!EMPTY($rows))
	    		{
	    			//$header=$header.','.$headers.',';
	    			$oldrow=$row.','.$rows.',';
	    		}
	    	}
	    	$data['field_id']=$header;
	    	$data['old_value']=$oldrow;
	    	$data['new_value']=$row;
    	}
    	else if($logarray['status']=='delete')
    	{
    	
    	}
    	$this->CI->am->insert_log($data);
    }

      
    function pr($data)
    {
    	echo "<pre>";
    	print_r($data);
    	echo "</pre>";
    }
}