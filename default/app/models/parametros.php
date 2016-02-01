<?php
/**
 *
 * Descripcion: Clase que gestiona los parámetros del sistema
 *
 * @category
 * @package     Models
 */

class Parametros extends ActiveRecord {
    
    //Se desabilita el logger para no llenar el archivo de "basura"
    public $logger = FALSE;
    
    /**
     * Constante para definir el parametros de Super Usuario
     */
    const SUPER_USUARIO = 1;
    
    /**
     * Constante para definir un parametros como activo
     */
    const ACTIVO = 1;
    
    /**
     * Constante para definir un parametros como inactivo
     */
    const INACTIVO = 2;
    
    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {
        // $this->has_many('usuario');
        // $this->has_many('recurso_parametros');
    }
    
    /**
     * Método para obtener el listado de los parámetros del sistema
     * @param type $estado
     * @param type $order
     * @param type $page
     * @return type
     */
    public function getListadoParametros($estado='todos', $order='', $page=0) {                   
        $columns = 'parametros.*';        
        
		  if($estado=='acl') {
            $conditions = " AND parametros.estado = ".self::ACTIVO;
        } else {
        	   $conditions = "1 = 1";
        	   
            if($estado!='todos') {
                $conditions = ($estado==self::ACTIVO) ? " AND estado=".self::ACTIVO : " AND estado=".self::INACTIVO;                
            }
        }                
                
        $order = $this->get_order($order, array(            
            'codigo' => array(
                'ASC' => 'parametros.codigo ASC, parametros.codigo_hijo ASC',
                'DESC' => 'parametros.codigo DESC, parametros.codigo_hijo DESC'
            ),
            'codigo_hijo' => array(
                'ASC' => 'parametros.codigo_hijo ASC, parametros.codigo ASC',
                'DESC' => 'parametros.codigo_hijo DESC, parametros.codigo DESC'
            ),
            'Descripcion' => array(
                'ASC' => 'parametros.descripcion ASC',
                'DESC' => 'parametros.descripcion DESC'
            )
             
        ));
        
        // $group = '';
        
        if($page) {            
            // return $this->paginated("columns: $columns", "join: $join", "conditions: $conditions", "group: $group", "order: $order", "page: $page");
            return $this->paginated("columns: $columns", "conditions: $conditions", "order: $order", "page: $page");
        }
        
        return $this->find("columns: $columns", "conditions: $conditions", "order: $order");
    }
    
    public function getListadoCombo($codigo=0){
      $columns = 'codigo_hijo, descripcion';
      $conditions = 'codigo_hijo > 0 and codigo = '.$codigo;
      return $this->find("columns: $columns", "conditions: $conditions");
          
    }
    
    /**
     * Método para crear/modificar un objeto de base de datos
        // return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "group: $group", "order: $order");
     * 
     * @param string $medthod: create, update
     * @param array $data: Data para autocargar el modelo
     * @param array $optData: Data adicional para autocargar
     * 
     * return object ActiveRecord
     */
    public static function setParametros($method, $data, $optData=null) {        
        $obj = new Parametros($data); //Se carga los datos con los de las tablas        
        if($optData) { //Se carga información adicional al objeto
            $obj->dump_result_self($optData);
        }
                     
        //Verifico que no exista otro parametros, y si se encuentra inactivo lo active
        // $conditions = empty($obj->id) ? "parametros = '$obj->parametros'" : "parametros = '$obj->parametros' AND id != '$obj->id'";
        // $old = new parametros();
        // if($old->find_first($conditions)) {            
            //Si existe y se intenta crear pero si no se encuentra activo lo activa
            // if($method=='create' && $old->estado != parametros::ACTIVO) {
                // $obj->id        = $old->id;
                // $obj->estado    = parametros::ACTIVO;
                // $method         = 'update';
            // } else {
                // Flash::info('Ya existe un parametros registrado bajo ese nombre.');
                // return FALSE;
            // }
        return ($obj->$method()) ? $obj : FALSE;
    }
    
    /**
     * Callback que se ejecuta antes de guardar/modificar
     */
    public function before_save() {
        $this->codigo       = Filter::get($this->codigo, 'numeric');
        $this->codigo_hijo  = Filter::get($this->codigo_hijo, 'numeric');
        // $this->plantilla    = (!empty($this->plantilla)) ? DwUtils::getSlug($this->plantilla, '_') : 'default';
        if(!empty($this->id)) {
            if($this->id == Parametros::SUPER_USUARIO) {
                Flash::warning('Lo sentimos, pero este parámetro no se puede editar.');
                return 'cancel';
            }
        }
        $path = APP_PATH.'views/_shared/templates/backend/'.$this->plantilla.'.phtml';
        //Verifico si se encuentra el template
        // if(!is_file($path)) {
            // Flash::error('Lo sentimos, pero no hemos podidio ubicar la plantilla '.$this->plantilla); 
            // return 'cancel';
        // }
    }
    
    /**
     * Callback que se ejecuta después de guardar/modificar un parámetro
     */
    protected function after_save() {
        // $data = array();
        // $data[] = Recurso::DASHBOARD.'-'.$this->id;
        // if(!Recursoparametros::setRecursoparametros($data)) {
        //     Flash::info("No se ha podido establcer el recurso 'dashboard' preestablecido al parametros.");
        //     return 'cancel';
        // }
        // $data = array();
        // $data[] = Recurso::MI_CUENTA.'-'.$this->id;
        // if(!Recursoparametros::setRecursoparametros($data)) {
        //     Flash::info("No se ha podido establcer el recurso 'Mi Cuenta' preestablecido al parametros.");
        //     return 'cancel';
        // }
    }


    /**
     * Método para obtener los recursos de un parametros
     * @param type $parametros
     * @return type
     */
    // public function getRecursos($parametros){        
    //     $columnas = "recurso.*";
    //     $join = "INNER JOIN recurso_parametros ON parametros.id = recurso_parametros.parametros_id ";
    //     $join.= "INNER JOIN recurso ON recurso.id = recurso_parametros.recurso_id ";
    //     $conditions = "parametros.id = '$parametros'";
    //     return $this->find("columns: $columnas" , "join: $join", "conditions: $conditions");
    // }
    
}
?>
