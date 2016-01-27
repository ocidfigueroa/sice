<?php

class Agencias extends ActiveRecord {
	
	# Constante para definir una agencia como activa
     
    const ACTIVO = 1;

    # Constante para definir una agencia como inactiva
     
    const INACTIVO = 0;
	
	
	public function getListadoAgencias($order='', $page=0) {
        $columns = 'agencias.*';

		$order = $this->get_order($order, 'id', array(
            'id' => array(
                'ASC'  => 'id ASC',
                'DESC' => 'id DESC'
            )
        ));


	    if($page) {
            return $this->paginated("columns: $columns", "per_page: 5", "order: $order", "page: $page");
        } else {
            return $this->find("columns: $columns", "order: $order");
        }  
    }
	
	
	/**
     * Método para crear/modificar un objeto de base de datos
     *
     * @param string $medthod: create, update
     * @param array $data: Data para autocargar el modelo
     * @param array $optData: Data adicional para autocargar
     *
     * return object ActiveRecord
     */
    public static function setAgencia($method, $data, $optData=null) {
        $obj = new Agencias($data); //Se carga los datos con los de las tablas
        if($optData) { //Se carga información adicional al objeto
            $obj->dump_result_self($optData);
        }
		
        return ($obj->$method()) ? $obj : FALSE;
    }
	
	# Método para buscar agencias
     
    public function getAjaxAgencia($field, $value, $order='', $page=0) {
        $value = Filter::get($value, 'string');
        if( strlen($value) <= 2 OR ($value=='none') ) {
            return NULL;
        }
        $columns = 'agencias.*';
        
        $order = $this->get_order($order, 'id', array(                        
            'id' => array(
                'ASC'=>'agencias.id ASC', 
                'DESC'=>'agencias.id DESC'
            )
        ));
        
        //Defino los campos habilitados para la búsqueda
        $fields = array('nombre', 'direccion', 'activo');
        if(!in_array($field, $fields)) {
            $field = 'nombre';
        }                
        
        $conditions = " $field LIKE '%$value%'";

        if($page) {
            return $this->paginated("columns: $columns", "per_page: 5", "conditions: $conditions", "order: $order", "page: $page");
        } else {
            return $this->find("columns: $columns", "per_page: 5", "conditions: $conditions", "order: $order");
        }  
    }
	
}