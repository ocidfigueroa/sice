<?php
/**
 * Descripcion: Controlador que se encarga de la gestión de los parámetros del sistema
 *
 * @category    
 * @package     Controllers  
 */
Load::model('parametros');

class ParametrosController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Parámetros del Sistema';
    }
    
    /**
     * Método principal
     */
    public function index() {
        Redirect::toAction('listar');
    }
    
    /**
     * Método para listar
     */
    public function listar($order='order.id.asc', $page='page.1') { 
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $parametros = new Parametros();
        $this->parametros = $parametros->getListadoParametros('todos', $order, $page);        
        $this->order = $order;        
        $this->page_title = 'Listado de Parámetros del Sistema';
    }
    
    /**
     * Método para agregar
     */
    public function agregar() {
        if(Input::hasPost('parametros')) {
            if(parametros::setParametros('create', Input::post('parametros'), array('estado'=>Parametros::ACTIVO))){
                Flash::valid('El parámetro se ha registrado correctamente!');
                return Redirect::toAction('listar');
            }          
        }
        $this->page_title = 'Agregar parámetro';
    }
    
    /**
     * Método para editar
     */
    public function editar($key) {        
        if(!$id = Security::getKey($key, 'upd_parametros', 'int')) {
            return Redirect::toAction('listar');
        }        
        
        $parametros = new Parametros();
        if(!$parametros->find_first($id)) {
            Flash::error('Lo sentimos, no se pudo establecer la información del parámetro');
            return Redirect::toAction('listar');
        }
        
        if(Input::hasPost('parametros')) {                                     
            if(parametros::setParametros('update', Input::post('parametros'), array('id'=>$id))){
                Flash::valid('El parámetro se ha actualizado correctamente!');
                return Redirect::toAction('listar');
            }            
        }
            
        $this->parametros = $parametros;
        $this->page_title = 'Actualizar parámetros';                
        
    }
    
    /**
     * Método para inactivar/reactivar
     */
    public function estado($tipo, $key) {
        if(!$id = Security::getKey($key, $tipo.'_parametros', 'int')) {
            return Redirect::toAction('listar');
        }        
        
        $parametros = new Parametros();
        if(!$parametros->find_first($id)) {
            Flash::error('Lo sentimos, no se pudo establecer la información del parámetro');            
        } 
        
        return Redirect::toAction('listar');
    }
    
    /**
     * Método para ver
     */
    public function ver($key, $order='order.id.asc', $page='page.1') { 
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;     
        if(!$id = Security::getKey($key, 'show_parametros', 'int')) {
            return Redirect::toAction('listar');
        }        
        
        $parametros = new Parametros();
        if(!$parametros->find_first($id)) {
            Flash::error('Lo sentimos, no se pudo establecer la información del parámetro');
            return Redirect::toAction('listar');
        }        
        
        /**
        * $usuario = new Usuario();
        * $this->usuarios = $usuario->getUsuarioPorparametros($parametros->id, $order, $page);
        
        * $this->parametros = $parametros;
        * $this->order = $order;        
        * $this->page_title = 'Información del parámetros';
        * $this->key = $key;
        */        
    }
    
}

