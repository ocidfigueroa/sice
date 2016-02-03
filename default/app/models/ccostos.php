<?php

class Ccostos extends ActiveRecord {
	
	# Constante para definir un centro de costo como activo
     
    const ACTIVO = 1;

    # Constante para definir un centro de costo como inactivo
     
    const INACTIVO = 0;
	
	
	public function getListadoCcostos($order='', $page=0) {
        $columns = 'ccostos.*';

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
    public static function setCcostos($method, $data, $optData=null) {
        $obj = new Ccostos($data); //Se carga los datos con los de las tablas
        if($optData) { //Se carga información adicional al objeto
            $obj->dump_result_self($optData);
        }
		
        return ($obj->$method()) ? $obj : FALSE;
    }
	
	# Método para buscar centros de costos
     
    public function getAjaxCcostos($field, $value, $order='', $page=0) {
        $value = Filter::get($value, 'string');
        if(  ($value=='none') ) {
            return NULL;
        }
        $columns = 'ccostos.*';
        
        $order = $this->get_order($order, 'id', array(                        
            'id' => array(
                'ASC'=>'ccostos.id ASC', 
                'DESC'=>'ccostos.id DESC'
            )
        ));
        
        //Defino los campos habilitados para la búsqueda
        $fields = array('codigo', 'nombre', 'activo');
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