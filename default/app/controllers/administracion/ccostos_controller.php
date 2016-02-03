<?php

Load::models('ccostos');

class CcostosController extends BackendController {
	
	# Método que se ejecuta antes de cualquier acción
	 
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Gestión de Centros de Costos';
    }


	# Método inicial. Redirecciona a la acción 'listar'
	
	public function index() 
    {
    	Redirect::toAction('listar');
    }
	
	
	# Método para listar todos los ccostos
	
	public function listar($order='order.id.asc', $page='page.1') 
    {
    	$page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        
       	$this->page_title = 'Listado de Centros de Costos';
		
		$this->order = $order;
		
		
		$ccostos = new CCostos();
		
		$this->ccostos = $ccostos->getListadoCcostos($order, $page);
		
    }
	
	
	# Método para crear una nueva ccostos
	
	public function agregar()
	{
		if(Input::hasPost('ccostos')) {
            if(Ccostos::setCcostos('create', Input::post('ccostos'), NULL)){
                if(APP_AJAX) {
                    Flash::valid('El Centro de Costo se ha creado correctamente!');
                } else {
                    Flash::valid('El Centro de Costo se ha creado correctamente!');
                }
                return Redirect::toAction('listar');
            }
        }
        $this->page_title = 'Agregar Centro de Costo';	
	}
	
	
	# Método para editar una centro de costos
     
    public function editar($key) {
        if(!$id = Security::getKey($key, 'upd_ccostos', 'int')) {
            return Redirect::toAction('listar');
        }

        $ccostos = new Ccostos();
        if(!$ccostos->find_first($id)) {
            Flash::error('Lo sentimos, pero no se ha podido rescatar la información del centro de costo');
            return Redirect::toAction('listar');
        }

        if(Input::hasPost('ccostos')) {
            if(CCostos::setCcostos('update', Input::post('ccostos'), array('id'=>$id))){
                if(APP_AJAX) {
                    Flash::valid('El centro de costo se ha actualizado correctamente!');
                } else {
                    Flash::valid('El centro de costo se ha actualizado correctamente!');
                }
                return Redirect::toAction('listar');
            }
        }

        $this->ccostos = $ccostos;
        $this->page_title = 'Actualizar ccostos';

    }
	
	
	# Método para inactivar/reactivar centro de costo
     
    public function estado($tipo, $key) {
        if(!$id = Security::getKey($key, $tipo.'_ccostos', 'int')) {
            return Redirect::toAction('listar');
        }

        $ccostos = new Ccostos();
        if(!$ccostos->find_first($id)) {
            Flash::error('Lo sentimos, pero no se ha podido obtener la información del centro de costo');
        } else {
            if($tipo=='inactivar' && $ccostos->activo == Ccostos::INACTIVO) {
                Flash::info('El centro de costo ya se encuentra inactivo');
            } else if($tipo=='reactivar' && $ccostos->activo == Ccostos::ACTIVO) {
                Flash::info('El centro de costo ya se encuentra activo');
            } else {
                $estado = ($tipo=='inactivar') ? Ccostos::INACTIVO : Ccostos::ACTIVO;
                if(CCostos::setCcostos('update', $ccostos->to_array(), array('id'=>$id, 'activo'=>$estado))){
                    ($estado==Ccostos::ACTIVO) ? Flash::valid('El centro de costo se ha reactivado correctamente!') : Flash::valid('El centro de costo se ha inactivado correctamente!');
                }
            }
        }

        return Redirect::toAction('listar');
    }
	
	
	
	# Método para eliminar un centro de costo
     
    public function eliminar($key) {
        if(!$id = Security::getKey($key, 'eliminar_ccostos', 'int')) {
            return Redirect::toAction('listar');
        }

        $ccostos = new Ccostos();
        if(!$ccostos->find_first($id)) {
            Flash::error('Lo sentimos, pero no se ha podido obtener la información del centro de costo');
            return Redirect::toAction('listar');
        }
        
        try {
            if($ccostos->delete()) {
                Flash::valid('El centro de costo se ha eliminado correctamente!');
            } else {
                Flash::warning('Lo sentimos, pero este centro de costo no se pudo eliminar.');
            }
        } catch(KumbiaException $e) {
            Flash::error('Este centro de costo no se puede eliminar.');
        }

        return Redirect::toAction('listar');
    }
	
	
	# Método para buscar centro de costo
     
    public function buscar($field='nombre', $value='none', $order='order.id.asc', $page=1) {        
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $field = (Input::hasPost('field')) ? Input::post('field') : $field;
        $value = (Input::hasPost('field')) ? Input::post('value') : $value;
        
        $ccostos = new Ccostos();            
        $ccostos = $ccostos->getAjaxCcostos($field, $value, $order, $page);
        
        if(empty($ccostos->items)) {
            Flash::info('No se han encontrado registros');
        }
        $this->ccostos = $ccostos;
        $this->order = $order;
        $this->field = $field;
        $this->value = $value;
        $this->page_title = 'Búsqueda de centros de costos del sistema';        
    }
	
}