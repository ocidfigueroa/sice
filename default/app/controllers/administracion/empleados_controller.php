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
        Redirect::toAction('listar');
    }
    
    /**
     * Método para listar
     */    
    public function listar($order='order.id.asc', $page='page.1') { 
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $usuario = new Empleados();
        $this->usuarios = $usuario->getListado('todos', $order, $page);
        $this->order = $order;        
        $this->page_title = 'Listado de Empleados';
    }
    
    public function getListado($estado, $order='', $page=0) {
        $columns = 'usuario.*, perfil.perfil, estado_usuario.estado_usuario, estado_usuario.descripcion';
        $join = self::getInnerEstado();
        $join.= 'INNER JOIN perfil ON perfil.id = usuario.perfil_id ';        
        $conditions = "usuario.perfil_id != ".Perfil::SUPER_USUARIO;//Por el super usuario
                
        $order = $this->get_order($order, 'nombre', array(                        
            'login' => array(
                'ASC'=>'usuario.login ASC, usuario.nombre ASC, usuario.apellido DESC', 
                'DESC'=>'usuario.login DESC, usuario.nombre DESC, usuario.apellido DESC'
            ),
            'nombre' => array(
                'ASC'=>'usuario.nombre ASC, usuario.apellido DESC', 
                'DESC'=>'usuario.nombre DESC, usuario.apellido DESC'
            ),
            'apellido' => array(
                'ASC'=>'usuario.apellido ASC, usuario.nombre ASC', 
                'DESC'=>'usuario.apellido DESC, usuario.nombre DESC'
            ),
            'email' => array(
                'ASC'=>'usuario.email ASC, usuario.apellido ASC, usuario.nombre ASC', 
                'DESC'=>'usuario.email DESC, usuario.apellido DESC, usuario.nombre DESC'
            ),
            'estado_usuario' => array(
                'ASC'=>'estado_usuario.estado_usuario ASC, usuario.apellido ASC, usuario.nombre ASC', 
                'DESC'=>'estado_usuario.estado_usuario DESC, usuario.apellido DESC, usuario.nombre DESC'
            )
        ));
        
        if($estado == 'activos') {
            $conditions.= " AND estado_usuario.estado_usuario = '".EstadoUsuario::ACTIVO."'";
        } else if($estado == 'bloqueados') {
            $conditions.= " AND estado_usuario.estado_usuario = '".EstadoUsuario::BLOQUEADO."'";
        }          
        
        if($page) {
            return $this->paginated("columns: $columns", "join: $join", "conditions: $conditions", "order: $order", "page: $page");
        } else {
            return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "order: $order");
        }  
    }

}
