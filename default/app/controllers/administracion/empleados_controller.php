<?php
/**
 *
 * Descripcion: Controlador para la mantencion de los empleados
 *
 * @category    
 * @package     Controllers 
 */
Load::models('empleados');
class EmpleadosController extends BackendController {
    
    public $page_module = 'Administracion';
    
    public $page_title = 'Empleados';
    
    public function index() { 
        
    }

}
