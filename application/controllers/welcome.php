<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('url'));
		//$this->load->model(array('categorias_model','modelo_login','modelo_perfil'));
		$this->load->model('modelo_update');
		//$this->load->library('session');
	}


	public function index()

	{
	
	
	
//	$this->load->view("sss");
		$this->load->view("xd");
		
			
	}
	

}