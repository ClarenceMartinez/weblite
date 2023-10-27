<?php

class M_diseno extends CI_Model {

    function __construct() {
        parent::__construct();
    } 
 
    public function changeEstadoEnAprobacionFromEjecucionNoLicencia($itemplan,$idUsuario,$fechaActual) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $dataUpdate = array(
                'idEstadoPlan' => ID_ESTADO_PLAN_EN_LICENCIA,
                'idUsuarioLog' => $idUsuario,
                'fechaLog' => $fechaActual,
                'descripcion' => 'DISEÑO - BANDEJA EJECUCIÓN - SIN LIC'
            );
            $this->db->where('itemplan', $itemplan);
            $this->db->update('planobra', $dataUpdate);
            if ($this->db->trans_status() === false) {
                throw new Exception('Hubo un error al actualizar el estadoplan.');
            } else {
                $dataUpdate = array(
                    'idEstadoPlan' => ID_ESTADO_PLAN_EN_APROBACION,
                    'idUsuarioLog' => $idUsuario,
                    'fechaLog' => $fechaActual,
                    'descripcion' => 'NO REQUIERE LICENCIA'
                );
                $this->db->where('itemplan', $itemplan);
                $this->db->update('planobra', $dataUpdate);
                if ($this->db->trans_status() === false) {
                    throw new Exception('Hubo un error al actualizar el estadoplan.');
                } else {
                    $data['error'] = EXIT_SUCCESS;
                    $data['msj'] = 'Se actualizó correctamente!';
                }
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    public function liquidarDiseno($id,$arrayData) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try {
            $this->db->where('id', $id);
            $this->db->update('diseno', $arrayData);
            if ($this->db->trans_status() === false) {
                throw new Exception('Hubo un error al actualizar la tabla diseno.');
            } else {
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizó correctamente!';
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

}
