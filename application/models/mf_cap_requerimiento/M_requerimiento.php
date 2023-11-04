<?php

class M_requerimiento extends CI_Model
{

    //http://www.codeigniter.com/userguide3/database/results.html
    function __construct()
    {
        parent::__construct();
    }

    function actualizarRequerimiento($codigo_requerimiento, $arrayData)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $this->db->where('codigo_requerimiento', $codigo_requerimiento);
            $this->db->update('cap_requerimiento', $arrayData);
            if ($this->db->affected_rows() <= 0) {
                throw new Exception('Error al actualizar en la tabla cap_requerimiento!!');
            } else {
                $data['msj'] = 'Se actualizó correctamente el requerimiento!!';
                $data['error'] = EXIT_SUCCESS;
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }
}
