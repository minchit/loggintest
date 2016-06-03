<?php
class Auditmodel extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function pr($data =null){
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}

	function insert_audit_trn($data)
	{
		$this->db->insert('audit_trn', $data);
		//$insert_id = $this->db->insert_id();
		//$this->pr($data);
		//exit();
		//return  $insert_id;
	}
	
	function insert_audit_ssn($data)
	{
		$this->db->insert('audit_ssn', $data);
		$insert_id = $this->db->insert_id();
	
		return  $insert_id;
	}
	
	function insert_log($data)
	{
		$this->db->insert('audit_trn',$data);
	}
	
	function audit_login_fail($data)
	{
		$this->db->insert('audit_login_failure',$data);
	}
	
	function last_tran_seq_no()
	{
	
		$this->db->select('aud.*');
		$this->db->from('audit_trn as aud');
		$this->db->order_by('tran_seq_no','desc');
		$this->db->limit(1);
		//$this->db->where('u.user_id',$u);
		//$this->db->where('u.password',$p);
		
		$query = $this->db->get();
		if($query->result()==NULL)
		{
			return 0;
		}
		else 
		{
			return $query->result();
		}
				
	}

	function get_p_key($table)
	{
		$fields = $this->db->field_data($table);
		$p_key;
		foreach ($fields as $field)
		{
			if($field->primary_key!='primary_key1')
			{
				//echo '<br/>'.'name'.$field->name.'<br/>';
				//echo 'type'.$field->type.'<br/>';
				//echo 'max_length'.$field->max_length.'<br/>';
				//echo 'primary_key'.$field->primary_key.'<br/>';
				$p_key=$field->name;
			}
			//echo '<br/>'.'name'.$field->name.'<br/>';
			//echo 'type'.$field->type.'<br/>';
			//echo 'max_length'.$field->max_length.'<br/>';
			//echo 'primary_key'.$field->primary_key.'<br/>';
			//$p_key=$field->primary_key;
		}
		
		return $p_key;
	}
	
	function selectprimary($primary1,$table)
	{
		$this->db->select('*');
			$this->db->from($table);
			$this->db->where($primary1);
			
			$query=$this->db->get();
		return $query->result_array();
	}
	
	function save_data($table,$data)
	{
	
		//$this->db->insert($table,$data);
		if($this->db->insert($table,$data)){
			
			return 1;
		}else{
			return 0;
		}
	}
}