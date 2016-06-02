<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Logall {

	public function __construct()
	{
		$this->CI=& get_instance();
		$this->CI->load->model('Auditmodel','am',true);	//Load the Auditmodel.php
	}
 
    public function Audit_ssn_start($logarray)	//To call this function when Login (Session Start)
    {
    	$data['user_id']=$logarray['user'];
    	$datetime=date('Y-m-d H:i:s');
    	$data['date']=date('Y-m-d', strtotime($datetime));
    	$data['time']=date('H:i:s', strtotime($datetime));
    	$session_id=$this->CI->am->insert_audit_ssn($data);
    	return $session_id;
    }
    
    public function Audit_start($logarray)	//Call the function when the transaction start
    {
    	//$CI=& get_instance();
    	//$CI->load->model('Auditmodel');
    	if (!EMPTY($logarray['session']['session_id']))
    	{
    		//$data['user_id']=$logarray['user'];
    		$datetime=date('Y-m-d H:i:s');
    		$data['date']=date('Y-m-d', strtotime($datetime));
    		$data['time']=date('H:i:s', strtotime($datetime));
    		//$session_id=$CI->Auditmodel->insert_audit_ssn($data);
    		//$session_id=$this->CI->am->insert_audit_ssn($data);
    		$session_id=$logarray['session'];				//to get current session_id
    		foreach ($session_id as $headers => $rows)
    		{
    			//$header=$header.','.$headers.',';
    			$session_id=$rows;							
    			//$row=$row.','.$rows.',';
    		}
    		//$this->pr($logarray);
    		
    		$tran_seq_no=$this->Audit_trn($session_id,$logarray);		//to save the transactions.
    		$table_seq_no=$this->Audit_tbl_insert($session_id,$tran_seq_no,$logarray); //to save the table,db and primarykey
    		if($logarray['status']=='insert')// if the transaction is insert
    		{
    			$this->Audit_fld_insert($session_id, $tran_seq_no, $table_seq_no, $logarray);
    		}
    		else if($logarray['status']=='update')// if the transaction is update
    		{
    			 $this->Audit_fld_update($session_id, $tran_seq_no, $table_seq_no, $logarray);
    		}
    		else if($logarray['status']=='delete')
    		{
    			 
    		}
    	}
    }
    
    public function Audit_trn($session_id,$logarray)
    {
    	//$CI=& get_instance();
    	//$CI->load->model('Auditmodel');
    		$data['session_id']=$session_id;
    		$tran_seq_no=$this->CI->am->last_tran_seq_no();
    		$data['tran_seq_no']=((int)$tran_seq_no[0]->tran_seq_no)+1;
    		$datetime=date('Y-m-d H:i:s');
    		$data['date']=date('Y-m-d', strtotime($datetime));
    		$data['time']=date('H:i:s', strtotime($datetime));
    		$data['task_id']='Controller='.(string)$logarray['controller'].', Function='.(string)$logarray['function'];
    		//$this->pr($data);
    		$this->CI->am->insert_audit_trn($data);
    		
    	return $data['tran_seq_no'];
    }
    
    public function Audit_tbl_insert($session_id,$tran_seq_no,$logarray)
    {
    	//$CI=& get_instance();
    	//$CI->load->model('Auditmodel');
    	$data['session_id']=$session_id;
    	$data['tran_seq_no']=$tran_seq_no;
    	$table_seq_no=$this->CI->am->last_table_seq_no();
    	$data['table_seq_no']=((int)$table_seq_no[0]->table_seq_no)+1;
    	$data['base_name']=(string)$logarray['dbname'];
    	$data['table_name']=(string)$logarray['table'];
    	$data['pkey']='pkey';
    	$data['pkey']=$this->CI->am->get_p_key((string)$logarray['table']);
    	
    	//$CI->Auditmodel->audit_tbl_insert($data);
    	$this->CI->am->audit_tbl_insert($data);
    	return $data['table_seq_no'];
    }
    
    public function Audit_fld_insert($session_id,$tran_seq_no,$table_seq_no,$logarray)
    {
    	//$CI=& get_instance();
    	//$CI->load->model('Auditmodel');
    	$data['session_id']=$session_id;
    	$data['tran_seq_no']=$tran_seq_no;
    	$data['table_seq_no']=$table_seq_no;
    	//$data['field_id']=$logarray['data'];
    	//$data['old_value']=NULL;
    	//$newdata;
    	$newdata=$logarray['data'];
    	$header='';
    	$row='';
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
    	
    	$this->CI->am->audit_fld_insert($data);
    	//return $data['table_seq_no'];
    }
    
    public function Audit_fld_update($session_id,$tran_seq_no,$table_seq_no,$logarray)
    {
    	//$CI=& get_instance();
    	//$CI->load->model('Auditmodel');
    	$data['session_id']=$session_id;
    	$data['tran_seq_no']=$tran_seq_no;
    	$data['table_seq_no']=$table_seq_no;
    	$header='';
    	$row;$oldrow;
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
    	//$CI->Auditmodel->audit_tbl_insert($data);
    	$this->CI->am->audit_fld_insert($data);
    	//return $data['table_seq_no'];
    }
    
      
    function pr($data)
    {
    	echo "<pre>";
    	print_r($data);
    	echo "</pre>";
    }
}