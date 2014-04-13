<?php
	class Modelo_update extends CI_Model{
		public function __construct()
		{
			parent::__construct();
			$this->load->database();
		}
		
		public function actualiza($num1,$num2){
		
$data = array(
               't' => $num1,
               'h' => $num2,
              
            );

$this->db->where('id', 1);
$this->db->update('space', $data); 
		
		}
		
		public function listado(){
		
		$result=$this->db->get("space")->result();
		return $result;
		}
		
		public function info($x,$y){
		
		$this->db->select("t,h");
		$this->db->where('x',$x);
		$this->db->where('y',$y);
		$result=$this->db->get("space")->row();
		return $result;
		
		
		}
	
	}
?>