<?php
class M_login extends CI_Model
{
    //http://www.codeigniter.com/userguide3/database/results.html
    public function __construct()
    {
        parent::__construct();

    }

    public function getUserInfo($usuario)
    {
        $Query = "SELECT	u.usuario, u.clave, u.id_usuario, u.nombre_completo, u.ape_paterno, u.idEmpresaColab, ec.empresaColabDesc
                    FROM 	usuario u
                    INNER JOIN empresacolab ec ON (ec.idEmpresaColab = u.idEmpresaColab)
                    WHERE   u.estado    =   1
					 AND CASE WHEN ? IS NOT NULL THEN UPPER(u.usuario) = UPPER(?)
								  ELSE true END";
			//		AND     u.id_usuario_global = ?";

        $result = $this->db->query($Query, array($usuario, $usuario));
        log_message('error', $this->db->last_query());
        if ($result->row() != null) { 
              return $result->row_array();
        } else {
            return null;
        }
    }	

    public function getPermisosRoute($idUsuario)
    {
         
        $Query = " SELECT   distinct pe.id_padre 
                    FROM    usuario u, usuario_x_perfil uxp, perfil p, permisos_x_perfil pxp, permisos pe
                    WHERE   u.id_usuario    = uxp.id_usuario
                    AND     uxp.id_perfil   = p.id_perfil
                    AND     p.id_perfil     = pxp.id_perfil
                    AND     pxp.id_permiso  = pe.id_permiso
                    -- AND     pe.estado = 1
                    AND     u.id_usuario    = ?
                    AND     pe.nivel        = 4";         

        $result = $this->db->query($Query, array($idUsuario));
        return $result->result_array();
    }  

    public function getPermisoByIdPermiso($idPermiso)
    {         
        $Query = "SELECT * FROM permisos where id_permiso = ?";      
        $result = $this->db->query($Query, array($idPermiso));
        if ($result->row() != null) { 
            return $result->row_array();
      } else {
          return null;
      }
    }
 
    public function getPermisosByIdPadre($idPadre)
    {
         
        $Query = " SELECT distinct pe.* FROM permisos pe where   pe.id_padre = ?";       
        $result = $this->db->query($Query, array($idPadre));
        return $result->result_array();
    }

    public function getPermisosByIdPadreAndUsuario($idUsuario, $idPadre)
    {
         
        $Query = "SELECT  distinct pe.*
        FROM    usuario u, usuario_x_perfil uxp, perfil p, permisos_x_perfil pxp, permisos pe
        WHERE   u.id_usuario    = uxp.id_usuario
        AND     uxp.id_perfil   = p.id_perfil
        AND     p.id_perfil     = pxp.id_perfil
        AND     pxp.id_permiso  = pe.id_permiso
        -- AND     pe.estado = 1
        AND     u.id_usuario    = ?
        AND     pe.id_padre 	= ?";       
        $result = $this->db->query($Query, array($idUsuario, $idPadre));
        return $result->result_array();
    }

    public function getPerfilesDescByIdUsuario($idUsuario)
    {
        $Query = "SELECT	group_concat(p.desc_perfil) as perfiles, group_concat(p.id_perfil) as id_perfiles
                    FROM 	usuario u, usuario_x_perfil uxp, perfil p
                    WHERE   u.id_usuario    = uxp.id_usuario
                    and     uxp.id_perfil   = p.id_perfil
                    and     u.id_usuario    = ?";
        $result = $this->db->query($Query, array($idUsuario));
        if ($result->row() != null) { 
            return $result->row_array();
        } else {
            return null;
        }
    }
    
    public function getPermisoByIdPadreAndUsuarioN4($idUsuario, $idPermiso)
    {
         
        $Query = "SELECT distinct  pe.*
        FROM    usuario u, usuario_x_perfil uxp, perfil p, permisos_x_perfil pxp, permisos pe
        WHERE   u.id_usuario    = uxp.id_usuario
        AND     uxp.id_perfil   = p.id_perfil
        AND     p.id_perfil     = pxp.id_perfil
        AND     pxp.id_permiso  = pe.id_permiso
        -- AND     pe.estado = 1
        AND     u.id_usuario    = ?
        AND     pe.id_permiso 	= ?";       
        $result = $this->db->query($Query, array($idUsuario, $idPermiso));
        if ($result->row() != null) { 
            return $result->row_array();
        } else {
            return null;
        }
    }
}