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
	
	
	
	function test_log()
	{
		$this->db->select('cr.*');
		$this->db->from('ox_cr_list as cr');
		$this->db->order_by('cr_id','desc');
		$this->db->limit(1);
		//$this->db->where('u.user_id',$u);
		//$this->db->where('u.password',$p);
		$query = $this->db->get();
		return $query->result();
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
	
	function audit_tbl_insert($data)
	{
		$this->db->insert('audit_tbl',$data);
		
	}
	
	function audit_fld_insert($data)
	{
		$this->db->insert('audit_fld',$data);
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
		return $query->result();
	}
	
	function last_table_seq_no()
	{
	
		$this->db->select('tab.*');
		$this->db->from('audit_tbl as tab');
		$this->db->order_by('table_seq_no','desc');
		$this->db->limit(1);
		//$this->db->where('u.user_id',$u);
		//$this->db->where('u.password',$p);
		$query = $this->db->get();
		return $query->result();
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
	
	function save_data($table,$data)
	{
	
		//$this->db->insert($table,$data);
		if($this->db->insert($table,$data)){
			
			return 1;
		}else{
			return 0;
		}
	}
	
	function update_cr($table,$con,$data){
		$this->db->where($con);
		if($this->db->update($table,$data)){
			return 1;
		}else{
			return 0;
		}
	}
	
	function selectcr($cr_id)
	{
		if(is_array($cr_id) && $_SERVER['REQUEST_METHOD'] == 'POST')
		{
			
			
			//$this->pr($_SERVER['REQUEST_METHOD']);
			//$this->pr($_SESSION['radio']);
			
			if($_SESSION['radio']=='CR_ID')
			{
				
				if(!empty($cr_id['cr_id_search']))
				{
					$this->db->select('cr.*');
					$this->db->from('ox_cr_list as cr');
					$this->db->like('cr.cr_id', $cr_id['cr_id_search']);
					$query=$this->db->get();
					return $query->result();
				}
			}
			else if($cr_id['radiovalue']=='CR_ID_Range')
			{
				
				if(empty($cr_id['cr_id_from']) || empty($cr_id['cr_id_to']))
				{
					
					$this->db->select('cr.*');
					$this->db->from('ox_cr_list as cr');
					if(!empty($cr_id['cr_id_from']))
					{
						$this->db->where('cr.cr_id >',$cr_id['cr_id_from']);
							
					}
					else if(!empty($cr_id['cr_id_to']))
					{
							
						$cr_id_to=implode('', $cr_id);
						//$this->pr($cr_id_to);
						$this->db->where('cr.cr_id >',$cr_id_to);
							
					}
					$query=$this->db->get();
					return $query->result();
				}
				else
				{
					
					$this->db->select('cr.*');
					$this->db->from('ox_cr_list as cr');
					$this->db->where('cr.cr_id >=',$cr_id['cr_id_from']);
					$this->db->where('cr.cr_id <=',$cr_id['cr_id_to']);
					$query=$this->db->get();
					return $query->result();
				
				}
			}
			else if($_SESSION['radio']=='Submitted_Date')
			{
				//$this->pr($_SESSION['radio']);
				//exit();
				if(!empty($cr_id['submit_date']))
				{
					//$this->pr($_SESSION['radio']);
					
					$this->db->select('cr.*');
					$this->db->from('ox_cr_list as cr');
					$this->db->where('cr.cr_submitted',$cr_id['submit_date']);
					$query=$this->db->get();
					return $query->result();
				}
			}
			else if($_SESSION['radio']=='Requestor')
			{
				//$this->pr($_SESSION['radio']);
				//exit();
				if(!empty($cr_id['request']))
				{
					$this->db->select('cr.*');
					$this->db->from('ox_cr_list as cr');
					$this->db->like('cr.cr_requestor', $cr_id['request']);
					$query=$this->db->get();
					return $query->result();
				}
			}
			else if($_SESSION['radio']=='Processed_Date')
			{
				//$this->pr($_SESSION['radio']);
				//exit();
				if(!empty($cr_id['process_date']))
				{
					$this->db->select('cr.*');
					$this->db->from('ox_cr_list as cr');
					$this->db->where('cr.cr_status_processed',$cr_id['process_date']);
					$query=$this->db->get();
					return $query->result();
				}
			}
			else if($_SESSION['radio']=='Processed_By')
			{
				//$this->pr($_SESSION['radio']);
				//exit();
				if(!empty($cr_id['process_by']))
				{
					$this->db->select('cr.*');
					$this->db->from('ox_cr_list as cr');
					$this->db->where('cr.cr_processed_by',$cr_id['process_by']);
					$query=$this->db->get();
					return $query->result();
				}
			}
					
		}
		else
		{
			$this->db->select('cr.*');
			$this->db->from('ox_cr_list as cr');
			$this->db->where('cr.cr_id',$cr_id);
			
			$query=$this->db->get();
			return $query->result();
		}
	}
	
	function getcr()
	{
	
		$this->db->select('cr.*');
		$this->db->from('ox_cr_list as cr');
		$this->db->order_by('cr_id','desc');
		$this->db->limit(20);
		//$this->db->where('u.user_id',$u);
		//$this->db->where('u.password',$p);
		$query = $this->db->get();
		return $query->result();
	}
	
	function lastcr()
	{
	
		$this->db->select('cr.*');
		$this->db->from('ox_cr_list as cr');
		$this->db->order_by('cr_id','desc');
		$this->db->limit(1);
		//$this->db->where('u.user_id',$u);
		//$this->db->where('u.password',$p);
		$query = $this->db->get();
		return $query->result();
	}
}