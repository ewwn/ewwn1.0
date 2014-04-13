<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Simula extends CI_Controller { 

		public function __construct()
	{
		parent::__construct();
		//$this->load->library('session');
			$this->load->helper(array('url'));
		
		$this->load->model(array('modelo_update'));
		
	}
		public function update(){
		
		
		if(!empty($_POST)){

		//print_r($_POST);
$this->modelo_update->actualiza($_POST['lat'],$_POST['long']);		
		}
		
		}
		
		public function compara(){
		
		$datos=$this->modelo_update->listado();
	
		if(!empty($_POST)){
		
	
		if($datos){
	
		foreach($datos as $datos){
		
		if($datos->x==$_POST['lat'] and $datos->y==$_POST['lon']){
		$a=$this->modelo_update->info($_POST['lat'],$_POST['lon']);
	
	$aux=array('t' => $a->t,'h' => $a->h,'status' => 1);
	echo json_encode($aux);

		
		}
		}
		
		
		}
		
		
		}
		
		}

	

}
?>