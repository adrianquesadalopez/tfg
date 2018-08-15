<?php

class AdminMyModCommentsController extends ModuleAdminController
{
	public function __construct()
	{
		// Set variables
		$this->table = 'mymod_comment';
		$this->className = 'MyModComment';
		$this->fields_list = array(
			'id_mymod_comment' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'firstname' => array('title' => $this->l('Firstname'), 'width' => 120),
			'lastname' => array('title' => $this->l('Lastname'), 'width' => 140),
			'product_name' => array('title' => $this->l('Product'), 'width' => 100, 'filter_key' => 'pl!name'),
			'grade' => array('title' => $this->l('Grade'), 'align' => 'right', 'width' => 80, 'filter_key' => 'a!grade'),
			'comment' => array('title' => $this->l('Comment'), 'search' => false),
			'date_add' => array('title' => $this->l('Date add'), 'type' => 'date'),
		);

		// Set fields form for form view
		$this->context = Context::getContext();
		$this->context->controller = $this;
		$this->fields_form = array(
			'legend' => array('title' => $this->l('Add / Edit Comment')),
			'input' => array(
				array('type' => 'text', 'label' => $this->l('Nombre'), 'name' => 'firstname', 'size' => 30, 'required' => true),
				array('type' => 'text', 'label' => $this->l('Apellido'), 'name' => 'lastname', 'size' => 30, 'required' => true),
				/*array('type' => 'text', 'label' => $this->l('E-mail'), 'name' => 'email', 'size' => 30, 'required' => true),
				array('type' => 'select', 'label' => $this->l('Producto'), 'name' => 'id_product', 'required' => true, 'default_value' => 1, 'options' => array('query' => Product::getProducts($this->context->cookie->id_lang, 1, 1000, 'name', 'ASC'), 'id' => 'id_product', 'name' => 'name')),*/
				array('type' => 'text', 'label' => $this->l('Valoración'), 'name' => 'grade', 'size' => 30, 'required' => true, 'desc' => $this->l('Grade must be between 1 and 5')),
				array('type' => 'textarea', 'label' => $this->l('Comment'), 'name' => 'comment', 'cols' => 50, 'rows' => 5, 'required' => false),
			),
			'submit' => array('title' => $this->l('Guardar'))
		);

		// Enable bootstrap
		$this->bootstrap = true;

		// Call of the parent constructor method
		parent::__construct();

		// Update the SQL request of the HelperList
		$this->_select = "pl.`name` as product_name, CONCAT(a.`grade`, '/5') as grade_display";
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = a.`id_product` AND pl.`id_lang` = '. (int)$this->context->language->id.')';

		// Add actions
		$this->addRowAction('view');
		$this->addRowAction('delete');
		$this->addRowAction('edit');

		// Add bulk actions
		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Eliminar seleccionados'),
				'confirm' => $this->l('Quieres eliminar los ítmes seleccionados?'),
			),
			'disable' => array(
				'text' => $this->l('Deshabilitar seleccionados'), 'confirm' => $this->l('Quieres deshabilitar los ítmes seleccionados?'),
			),
			'enable' => array(
				'text' => $this->l('Habilitar seleccionados'), 'confirm' => $this->l('Quieres habilitar los ítmes seleccionados?'),
			),
			'myaction' => array(
				'text' => $this->l('My Action'), 'confirm' => $this->l('Are you sure?'),
			)
		);

		// Define meta and toolbar title
		$this->meta_title = $this->l('Comments on Product');
		//if (Tools::getIsset('viewmymod_comment'))
		//	$this->meta_title = $this->l('View comment').' #'. Tools::getValue('id_mymod_comment');
		$this->toolbar_title[] = $this->meta_title;
	}

	protected function processBulkMyAction()
	{
		Tools::dieObject($this->boxes);
	}

	public function renderView()
	{
		
		$tpl = $this->context->smarty->createTemplate(dirname(__FILE__).'/../../views/templates/admin/view.tpl');
		$tpl->assign('mymodcomment', $this->object);
		return $tpl->fetch();
	}
}