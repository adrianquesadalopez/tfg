<?php

class MyModComments extends Module
{
	public function __construct() //constructor con los datos de el modulo
	 {
		 $this->name = 'mymodcomments';
		 $this->tab = 'front_office_features';
		 $this->version = '1.0';
		 $this->bootstrap = true;
		 $this->author = 'Adri Quesada';
		 $this->displayName = $this->l('Comentarios y valoraciones de productos');
		 $this->description = $this->l('Con este modulo los clientes podran valorar y ratificar 
		 los productos para dar una mayor credibilidad a tu negocio');
		 parent::__construct();
	 }
	 
	 public function install() //instalamos y ponemos el hook en
	{
		 parent::install(); //añade el modulo en la tabla ps9w_module de la tabla SQL, SINO NO SERA INSTALABLE
		 $this->registerHook('displayProductTabContent'); //registerHOOK necesita el id del modulo, por eso se llama primero a install
		 return true;
	}
	
	 
}