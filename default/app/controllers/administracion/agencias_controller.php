<?php

Load::models('agencias');

class AgenciasController extends BackendController {
	
	# Método que se ejecuta antes de cualquier acción
	 
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Gestión de agencias';
    }


	# Método inicial. Redirecciona a la acción 'listar'
	
	public function index() 
    {
    	Redirect::toAction('listar');
    }
	
	
	# Método para listar todas las agencias
	
	public function listar($order='order.id.asc', $page='page.1') 
    {
    	$page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        
       	$this->page_title = 'Listado de agencias';
		
		$this->order = $order;
		
		
		$agencias = new Agencias();
		
		$this->agencias = $agencias->getListadoAgencias($order, $page);
		
    }
	
	
	# Método para crear una nueva agencia
	
	public function agregar()
	{
		if(Input::hasPost('agencia')) {
            if(Agencias::setAgencia('create', Input::post('agencia'), NULL)){
                if(APP_AJAX) {
                    Flash::valid('La agencia se ha creado correctamente!');
                } else {
                    Flash::valid('La agencia se ha creado correctamente!');
                }
                return Redirect::toAction('listar');
            }
        }
        $this->page_title = 'Agregar agencia';	
	}
	
	
	# Método para editar una agencia
     
    public function editar($key) {
        if(!$id = Security::getKey($key, 'upd_agencia', 'int')) {
            return Redirect::toAction('listar');
        }

        $agencia = new Agencias();
        if(!$agencia->find_first($id)) {
            Flash::error('Lo sentimos, pero no se ha podido rescatar la información de la agencia');
            return Redirect::toAction('listar');
        }

        if(Input::hasPost('agencia')) {
            if(Agencias::setAgencia('update', Input::post('agencia'), array('id'=>$id))){
                if(APP_AJAX) {
                    Flash::valid('La agencia se ha actualizado correctamente!');
                } else {
                    Flash::valid('La agencia se ha actualizado correctamente!');
                }
                return Redirect::toAction('listar');
            }
        }

        $this->agencia = $agencia;
        $this->page_title = 'Actualizar agencia';

    }
	
	
	# Método para inactivar/reactivar agencia
     
    public function estado($tipo, $key) {
        if(!$id = Security::getKey($key, $tipo.'_agencia', 'int')) {
            return Redirect::toAction('listar');
        }

        $agencia = new Agencias();
        if(!$agencia->find_first($id)) {
            Flash::error('Lo sentimos, pero no se ha podido obtener la información de la agencia');
        } else {
            if($tipo=='inactivar' && $agencia->activo == Agencias::INACTIVO) {
                Flash::info('La agencia ya se encuentra inactiva');
            } else if($tipo=='reactivar' && $agencia->activo == Agencias::ACTIVO) {
                Flash::info('La agencia ya se encuentra activa');
            } else {
                $estado = ($tipo=='inactivar') ? Agencias::INACTIVO : Agencias::ACTIVO;
                if(Agencias::setAgencia('update', $agencia->to_array(), array('id'=>$id, 'activo'=>$estado))){
                    ($estado==Agencias::ACTIVO) ? Flash::valid('La agencia se ha reactivado correctamente!') : Flash::valid('La agencia se ha inactivado correctamente!');
                }
            }
        }

        return Redirect::toAction('listar');
    }
	
	
	
	# Método para eliminar una agencia
     
    public function eliminar($key) {
        if(!$id = Security::getKey($key, 'eliminar_agencia', 'int')) {
            return Redirect::toAction('listar');
        }

        $agencia = new Agencias();
        if(!$agencia->find_first($id)) {
            Flash::error('Lo sentimos, pero no se ha podido obtener la información de la agencia');
            return Redirect::toAction('listar');
        }
        
        try {
            if($agencia->delete()) {
                Flash::valid('La agencia se ha eliminado correctamente!');
            } else {
                Flash::warning('Lo sentimos, pero esta agencia no se pudo eliminar.');
            }
        } catch(KumbiaException $e) {
            Flash::error('Esta agencia no se puede eliminar.');
        }

        return Redirect::toAction('listar');
    }
	
	
	# Método para buscar agencia
     
    public function buscar($field='nombre', $value='none', $order='order.id.asc', $page=1) {        
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $field = (Input::hasPost('field')) ? Input::post('field') : $field;
        $value = (Input::hasPost('field')) ? Input::post('value') : $value;
        
        $agencia = new Agencias();            
        $agencias = $agencia->getAjaxAgencia($field, $value, $order, $page);
        
        if(empty($agencias->items)) {
            Flash::info('No se han encontrado registros');
        }
        $this->agencias = $agencias;
        $this->order = $order;
        $this->field = $field;
        $this->value = $value;
        $this->page_title = 'Búsqueda de agencias del sistema';        
    }
	
}