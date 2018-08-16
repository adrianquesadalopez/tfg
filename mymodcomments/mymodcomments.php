<?php

require_once(dirname(__FILE__).'/classes/MyModComment.php');

class MyModComments extends Module
{
	public function __construct() //constructor con los datos de el modulo
	 {
		 $this->name = 'mymodcomments';
		 $this->tab = 'front_office_features';
		 $this->version = '2.0';
		 $this->bootstrap = true;
		 $this->author = 'Adri Quesada';
		 $this->displayName = $this->l('Comentarios y valoraciones de productos');
		 $this->description = $this->l('Con este modulo los clientes podran valorar y ratificar 
		 los productos para dar una mayor credibilidad a tu negocio');
		 $this->ps_versions_compliancy = array('min'=>'1.5');
		 parent::__construct();
	 }
	 
	/* public function onCLickOption($type, $href = false)
	 {
	 	
	 	return "return confirm('Confirmas el reseteo del modulo?');";
	 }*/
	 public function onCLickOption($type, $href = false)
	 {

	 	$confirm_reset = $this->l('Reseteando el modulo perderas todos los comentarios de la basename(
	 		de datos, estás seguro?)');
	 	$reset_callback = "return mymodcomments_reset('".addslashes($confirm_reset)."');";

	 	$matchType = array(
	 		'reset' => "return confirm('Confirmas el reseteo del modulo?');",
	 		'delete' => "return confirm('Confirmas la eliminación del módulo?')",
	 		'disable' => "return confirm('Confirmas deshabilitar el módulo?')",
	 	);

	 	if(isset($matchType[$type])) {
	 		return $matchType[$type];
	 	}
	 	return '';
	 }
	 public function install() //instalamos y ponemos el hook en
	{
		 if(!parent::install())
		 return false; //añade el modulo en la tabla ps9w_module de la tabla SQL, SINO NO SERA INSTALABLE
		
		 $sql_file= dirname(__FILE__).'/install/install.sql';
		if(!$this->loadSQLFile($sql_file)) 
		return false;

	if (!$this->installTab('AdminCatalog', 'AdminMyModComments', 'MyMod Comments'))
			return false;

		 if (!$this->registerHook('displayProductTabContent') ||
			!$this->registerHook('displayBackOfficeHeader') ||
			!$this->registerHook('displayAdminProductsExtra'))
			return false;

		Configuration::updateValue('MYMOD_GRADES','1');
		Configuration::updateValue('MYMOD_COMMENTS','1');
		 return true;
	}

	public function uninstall() //instalamos y ponemos el hook en
	{
		 if(!parent::uninstall())
		 return false; //añade el modulo en la tabla ps9w_module de la tabla SQL, SINO NO SERA INSTALABLE
		
		/* $sql_file= dirname(__FILE__).'/install/uninstall.sql';
		if(!$this->loadSQLFile($sql_file)) 
		return false;*/
		 
		 if (!$this->uninstallTab('AdminMyModComments'))
			return false;

		Configuration::deleteByName('MYMOD_GRADES');
		Configuration::deleteByName('MYMOD_COMMENTS');

		 return true;
	}

	public function installTab($parent, $class_name, $name)
	{
		// Create new admin tab
		$tab = new Tab();
		$tab->id_parent = (int)Tab::getIdFromClassName($parent);
		$tab->name = array();
		foreach (Language::getLanguages(true) as $lang)
			$tab->name[$lang['id_lang']] = $name;
		$tab->class_name = $class_name;
		$tab->module = $this->name;
		$tab->active = 1;
		return $tab->add();
	}

	public function uninstallTab($class_name)
	{
		// Retrieve Tab ID
		$id_tab = (int)Tab::getIdFromClassName($class_name);

		// Load tab
		$tab = new Tab((int)$id_tab);

		// Delete it
		return $tab->delete();
	}

	public function loadSQLFile($sql_file)
	{

		$sql_content= file_get_contents($sql_file);
		$sql_content= str_replace(`ps9w_`, _DB_PREFIX_, $sql_content);

		$sql_requests= preg_split("/;\s*[\r\n]+/",$sql_content);

		$result= true;
		Foreach ($sql_requests as $request) {
			if(!empty($request))
				$result &= Db::getInstance()->execute(trim($request));
		}
		return $result;
	}
	public function processConfiguration()
	{ //comprovamos si el boton de enviar ha sido clickado
	 	if (Tools::isSubmit('mymod_pc_form')) //boton de enviar(submit) , issubmit mira si la clave 
		{
			$enable_grades = Tools::getValue('enable_grades'); //recupera la información de la clave pasada en el parametro
			$enable_comments = Tools::getValue('enable_comments');//ídem
			Configuration::updateValue('MYMOD_GRADES', $enable_grades); //guarda la configuración en la tabla de configuraciones, primero la clave y el parametro
			Configuration::updateValue('MYMOD_COMMENTS', $enable_comments);
			$this->context->smarty->assign('confirmation', 'ok'); //mensaje de confirmacion
		}
	 	
	}
	 public function assignConfiguration()// damos los valores de configuracion a las variables del .tpl
	{
		 $enable_grades = Configuration::get('MYMOD_GRADES');
		 $enable_comments = Configuration::get('MYMOD_COMMENTS');
		 $this->context->smarty->assign('enable_grades', $enable_grades);
		 $this->context->smarty->assign('enable_comments',$enable_comments);
	}
	 public function getContent()
	 { //llama a las dos rutinas anteriores y muestra el getContent.tpl, es el unico metodo que prestashop llama cuando entramos en la configuracion de un modulo, el retorno sera el contenido mostrado en la pantalla
	 	$this->processConfiguration();
	 	$this->assignConfiguration();
	 	return $this->display(__FILE__,'getContent.tpl');
	 }
	public function processProductTabContent() //rutina para grabar comentarios en la bd
	{
		 if (Tools::isSubmit('mymod_pc_submit_comment'))
		 {
			 $id_product = Tools::getValue('id_product');
			 $grade = Tools::getValue('grade');
			 $comment = Tools::getValue('comment');
			 $firstname = Tools::getValue('firstname');
			 $lastname= Tools::getValue('lastname');
			 $insert = array(
			 'id_product' => (int)$id_product,
			 'grade' => (int)$grade,
			 'comment' => pSQL($comment),
			 'date_add' => date('Y-m-d H:i:s'),
			 'firstname' => pSQL($firstname),
			 'lastname' => pSQL($lastname),
			 );
			 Db::getInstance()->insert('mymod_comment', $insert);
			$this->context->smarty->assign('new_comment_posted', 'true');

		 }
	}
	public function assignProductTabComment() //para mostrar css i jvs en los comentarios
	{
	 $this->context->controller->addCSS($this->_path.'views/css/
		 mymodcomments.css', 'all');
		$this->context->controller->addJS($this->_path.'views/js/
		 mymodcomments.js');
		$this->context->smarty->assign('enable_grades', $enable_grades);
		$this->context->smarty->assign('enable_comments',
		 $enable_comments);
		$this->context->smarty->assign('comments', $comments);
	}


	public function assignProductTabContent() //para mostrar los comentarios
	{
	$this->context->controller->addCSS($this->_path.'views/css/star-rating.css', 'all');
	$this->context->controller->addJS($this->_path.'views/js/star-rating.js');
	
	 $enable_grades = Configuration::get('MYMOD_GRADES');
	 $enable_comments = Configuration::get('MYMOD_COMMENTS');
	 $id_product = Tools::getValue('id_product');
	 $comments = Db::getInstance()->executeS('SELECT * FROM
	 '._DB_PREFIX_.'mymod_comment WHERE id_product =
	 '.(int)$id_product);
	 $this->context->smarty->assign('enable_grades', $enable_grades);
	 $this->context->smarty->assign('enable_comments',
	 $enable_comments);
	 $this->context->smarty->assign('comments', $comments);
	 $this->processProductTabContent();
	} 
	
	public function getHookController($hook_name)
	{
		// Include the controller file
		require_once(dirname(__FILE__).'/controllers/hook/'. $hook_name.'.php');

		// Build dynamically the controller name
		$controller_name = $this->name.$hook_name.'Controller';

		// Instantiate controller
		$controller = new $controller_name($this, __FILE__, $this->_path);

		// Return the controller
		return $controller;
	}
	
	public function hookDisplayProductTabContent($params)//lanza el displayProductTabContent.tpl para que se muestre la posibilidad de poner reseñas en el front-end
	{
	 $this->processProductTabContent();
	 $this->assignProductTabContent();
	 return $this->display(__FILE__, 'displayProductTabContent.tpl');
	}
	public function hookDisplayBackOfficeHeader($params)//lanza el DisplayBackOfficeHeader.tpl para que se muestre la confirmacion de la accion del onclick
	{
	 if(Tools::getValue('controller') != 'AdminModules') 
	 	return '';
	 $this->context->smarty->assign('pc_base_dir', __PS_BASE_URI__.'modules/'.$this->name.'/');
	 return $this->display(__FILE__,'displayBackOfficeHeader.tpl');
	}

	public function hookDisplayAdminProductsExtra($params)
	{
		$controller = $this->getHookController('displayAdminProductsExtra');
		return $controller->run();
	}

	public function hookDisplayAdminCustomers($params)
	{
		$controller = $this->getHookController('displayAdminCustomers');
		return $controller->run();
	}
	 
}
?>