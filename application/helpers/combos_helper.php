<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


    if(!function_exists('__buildComboAuto')) {
        function __buildComboAuto($itemplan) {
            $CI =& get_instance();
            $arrayPlacas = $CI->m_utils->getAllAutoByItemplan($itemplan);
            $cmb = null;
            $selected = '';
            foreach($arrayPlacas AS $row) {
                if($row['flgPlaca'] == 1) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $cmb .= '<option value="'.$row['placa'].'" '.$selected.'>'.utf8_decode($row['placa']).'</option>';
            }
            return $cmb;
        }
    }

    if(!function_exists('__buildComboSubProyectoCV')) {
        function __buildComboSubProyectoCV($arraySub, $itemplan) {
            $CI =& get_instance();
            $arraySub = $CI->m_utils->getSubProyectoById($arraySub, null, $itemplan);
            $cmb = null;
            $selected = '';
            //$cmb .= '<option value="">Seleccionar</option>';
            foreach($arraySub AS $row) {
                if($row['flgSubSelected'] == 1) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $cmb .= '<option value="'.$row['idSubProyecto'].'" '.$selected.'>'.$row['subproyectoDescDos'].'</option>';
            }
            return $cmb;
        }
    }

    if(!function_exists('__buildComboFase')) {
        function __buildComboFase() {
            $CI =& get_instance();
            $arrayFase = $CI->m_utils->getFaseAll();
            $cmb = null;
            $selected = '';
            $cmb .= '<option value="">Seleccionar</option>';
            foreach($arrayFase AS $row) {
                $cmb .= '<option value="'.$row['idFase'].'" '.$selected.'>'.$row['faseDesc'].'</option>';
            }
            return $cmb;
        }
    }

    if(!function_exists('__buildComboComplejidad')) {
        function __buildComboComplejidad($codigo_po) {
            $CI =& get_instance();
            //$arrayFase = $CI->m_utils->getComplejidad($itemplan);
            $arrayData = $CI->m_utils->getComplejidadByCodigoPo($codigo_po);
            $cmb = null;
            $selected = '';
            //$cmb .= '<option value="">Seleccionar</option>';
            foreach($arrayData AS $row) {
                if($row['flgSelected']) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $cmb .= '<option value="'.$row['idTipoComplejidad'].'" '.$selected.'>'.$row['complejidadDesc'].'</option>';
            }
            return $cmb;
        }
    }

    if(!function_exists('__buildComboEstacionByItemplan')) {
        function __buildComboEstacionByItemplan($itemplan, $idEstacion, $flgCambioPo=null) {
            $CI =& get_instance();
            $arrayEstacion = $CI->m_utils->getEstacionByItemplanCmb($itemplan, $idEstacion, $flgCambioPo);
            $cmb = '<option value="">Seleccionar</option>';
            foreach($arrayEstacion AS $row) {
                $cmb .= '<option value="'.$row['idEstacion'].'">'.utf8_decode($row['estacionDesc']).'</option>';
            }
            return $cmb;
        }
    }

    if(!function_exists('__buildCmbCodigoPoByEstacionItemplan')) {
        function __buildCmbCodigoPoByEstacionItemplan($itemplan, $idEstacion) {
            $CI =& get_instance();
            $arrayEstacion = array();
            $arrayEstacion = $CI->m_utils->getCodigoPoByEstacionItemplan($itemplan, $idEstacion);

            if(count($arrayEstacion) > 0) {
                $cmb = '<option value="">Seleccionar</option>';
                foreach($arrayEstacion AS $row) {
                    $cmb .= '<option value="'.$row['codigo_po'].'">'.$row['codigo_po'].'</option>';
                }
            } else {
                $cmb = null;
            }
            return $cmb;
        }
    }

    if(!function_exists('__buildCmbMdf')) {
        function __buildCmbMdf() {
            $CI =& get_instance();
            $arrayCentral = array();
            $arrayCentral = $CI->m_utils->getAllCentral();

            if(count($arrayCentral->result()) > 0) {
                $cmb = '<option value="">Seleccionar</option>';
                foreach($arrayCentral->result() AS $row){
                    $cmb .= '<option value="'.$row->idCentral.'">'.utf8_decode($row->tipoCentralDesc).'</option>';
                }
            } else {
                $cmb = null;
            }
            return $cmb;
        }
    }

    if(!function_exists('__buildCmbMotivo')) {
        function __buildCmbMotivo($flgTipo, $flgSisego=null) {
            $CI =& get_instance();
            
            $array = array();
            // EL $flgSisego SIRVE PARA INDICARME CUANDO NECESITO EL CAMPO idSisego y se va a concatenar en el combo.
            $array = $CI->m_utils->getMotivoAll($flgTipo);

            if(count($array) > 0) {
                $cmb = '<option value="">Seleccionar</option>';
                foreach($array AS $row){
                    if($flgSisego == 1) {
                        $cmb .= '<option value="'.$row->idMotivo.'|'.$row->idSisego.'">'.utf8_decode($row->motivoDesc).'</option>';
                    } else {
                        $cmb .= '<option value="'.$row->idMotivo.'">'.utf8_decode($row->motivoDesc).'</option>';                
                    }
                }
            } else {
                $cmb = null;
            }
            return $cmb;
        }
    }

    if(!function_exists('__buildCmbContratosAll')) {
        function __buildCmbContratosAll() {
            $CI =& get_instance();
            
            $array = array();
            // EL $flgSisego SIRVE PARA INDICARME CUANDO NECESITO EL CAMPO idSisego y se va a concatenar en el combo.
            $array = $CI->m_utils->getContratosAll();

            if(count($array) > 0) {
                $cmb = '<option value="">Seleccionar</option>';
                foreach($array AS $row){
                    $cmb .= '<option value="'.$row['id_contratos'].'">'.utf8_decode($row['nombre']).'</option>';
                }
            } else {
                $cmb = null;
            }
            return $cmb;
        }
    }

    if(!function_exists('__buildCmbEmpresaColab')) {
        function __buildCmbEmpresaColab($flg_solicitud_usua_siom=null) {
            $CI =& get_instance();
            
            $array = array();
            // EL $flgSisego SIRVE PARA INDICARME CUANDO NECESITO EL CAMPO idSisego y se va a concatenar en el combo.
            $array = $CI->m_utils->getAllEmpresaColab($flg_solicitud_usua_siom);

            if(count($array) > 0) {
                $cmb = '<option value="">Seleccionar</option>';
                foreach($array AS $row){
                    $cmb .= '<option value="'.$row['idEmpresaColab'].'">'.utf8_decode($row['empresaColabDesc']).'</option>';
                }
            } else {
                $cmb = null;
            }
            return $cmb;
        }
    }

    if(!function_exists('__buildCmbZona')) {
        function __buildCmbZona() {
            $CI =& get_instance();
            
            $array = array();
            // EL $flgSisego SIRVE PARA INDICARME CUANDO NECESITO EL CAMPO idSisego y se va a concatenar en el combo.
            $arrayZonal = $CI->m_utils->getZona(FLG_CONTRATO_ZONA_USUARIO_SIOM);
            $cmb = null;
            foreach($arrayZonal AS $row) {
                $cmb.='<option value="'.$row['id_zona'].'">'.$row['nombre'].'</option>';
            }
            return $cmb;
        }
    }

    if(!function_exists('__buildCmbPerfil')) {
        function __buildCmbPerfil($flg_solicitud_usua_siom=null) {
            $CI =& get_instance();
            
            $array = array();
        
            $array = $CI->m_utils->getPerfilAll($flg_solicitud_usua_siom);

            if(count($array) > 0) {
                $cmb = '<option value="">Seleccionar</option>';
                foreach($array AS $row){
                    $cmb .= '<option value="'.$row['id_perfil'].'">'.utf8_decode($row['desc_perfil']).'</option>';
                }
            } else {
                $cmb = null;
            }
            return $cmb;
        }
    }

    if(!function_exists('__buildComboTipoSolicitudRpa')) {
        function __buildComboTipoSolicitudRpa($id, $onchange, $flgAnioActual, $countPepBianual) {
            $CI =& get_instance();
            $arrayTipoSolicitud = $CI->m_utils->getTipoSolicitud();
            $cmb = null;
            foreach($arrayTipoSolicitud AS $row) {
                if($flgAnioActual == 1) {
                    $cmb.='<option ';
                    $cmb.= 'value="'.$row->idTipoSolicitud.'">'.$row->descripcion.'</option>';
                } else {

                    if($countPepBianual > 0) {
                        $cmb.='<option ';
                        $cmb.= 'value="'.$row->idTipoSolicitud.'">'.$row->descripcion.'</option>';
                    } else {
                        if($row->idTipoSolicitud == 4) {
                            $cmb.='<option ';
                            $cmb.= 'value="'.$row->idTipoSolicitud.'">'.$row->descripcion.'</option>';
                        } 
                    }
                }
            }
            return __cmbHTML($cmb, $id, $onchange, 'form-control');
        }
    }

    if(!function_exists('__buildCmbPlanificacionItem')) {
        function __buildCmbPlanificacionItem($idSubProyecto, $idFase, $id_plan_mes = null) {
            $CI =& get_instance();
            
            $array = array();
        
            $array = $CI->m_utils->getDataPlanificacionItem($idSubProyecto, $idFase);

            $cmb = '<option value="">Seleccionar</option>';
            foreach($array AS $row){
                $selected = null;
                
                if($id_plan_mes == $row['id_plan']) {
                    $selected = 'selected';
                }
                $cmb .= '<option value="'.$row['id_plan'].'" '.$selected.'>'.utf8_decode($row['nombre_plan']).'</option>';
            }

            return $cmb;
        }
    }

    if(!function_exists('__buildCmbMes')) {
        function __buildCmbMes() {
            $CI =& get_instance();
            
            $array = array();
        
            $array = $CI->m_utils->getMesAll();

            $cmb = '<option value="">Seleccionar</option>';
            foreach($array AS $row){
                $cmb .= '<option value="'.$row['id_mes'].'">'.utf8_decode($row['nombre']).'</option>';
            }

            return $cmb;
        }
    }

    if(!function_exists('__buildCmbFase')) {
        function __buildCmbFase() {
            $CI =& get_instance();
            
            $array = array();
        
            $array = $CI->m_utils->getFaseAll();

            $cmb = '<option value="">Seleccionar</option>';
            foreach($array AS $row){
                $selected = null;
                if($row['anio']) {
                    $selected = 'selected';
                }
                $cmb .= '<option value="'.$row['idFase'].'" '.$selected.'>'.utf8_decode($row['faseDesc']).'</option>';
            }

            return $cmb;
        }
    }

    if(!function_exists('__buildCmbItemplanMadre')) {
        function __buildCmbItemplanMadre($idProyecto, $idSubProyecto, $idEstado) {
            $CI =& get_instance();
            
            $array = array();
        
            $array = $CI->m_utils->getDataItemplanMadre($idProyecto, $idSubProyecto, $idEstado);

            $cmb = '<option value="">Seleccionar Itemplan Madre</option>';
            foreach($array AS $row){
                $cmb .= '<option value="'.$row['itemplan_m'].'">'.$row['itemplan_m'].'</option>';
            }

            return $cmb;
        }
    }

    if(!function_exists('__buildCmbItemplanMadreRefo')) {
        function __buildCmbItemplanMadreRefo() {
            $CI =& get_instance();
            
            $array = array();
        
            $array = $CI->m_utils->getDataItemplanMadreRefo();

            $cmb = '<option value="">Seleccionar Itemplan Madre</option>';
            foreach($array AS $row){
                $cmb .= '<option value="'.$row['itemplan'].'">'.$row['itemplan'].'</option>';
            }

            return $cmb;
        }
    }

    if(!function_exists('__buildComboEstacionNewAll')) {
        function __buildComboEstacionNewAll() {
            $CI =& get_instance();
            $arrayEstacion = $CI->m_utils->getEstacionCmb(1);
            $cmb = null;
            $selec = null;
            $cmb = '<option value="">Seleccionar</option>';
            foreach($arrayEstacion AS $row) {
                $cmb .= '<option value="'.$row->idEstacion.'" '.$selec.'>'.utf8_decode($row->estacionDesc).'</option>';
            }
            return $cmb;
        }
    }

    if(!function_exists('__buildComboTipoIncidente')) {
        function __buildComboTipoIncidente($id_modulo) {
            $CI =& get_instance();
            $arrayTipos = $CI->m_utils->getTipoIncidentes($id_modulo);
            $cmb = null;
            $selec = null;
            $cmb = '<option value="">Seleccionar</option>';
            foreach($arrayTipos AS $row) {
                $cmb .= '<option value="'.$row['id_tipo_incidente'].'" '.$selec.'>'.utf8_decode($row['descripcion']).'</option>';
            }
            return $cmb;
        }
    }
        
        if(!function_exists('__buildComboEBCs')) {
            function __buildComboEBCs($departamento) {
                $CI =& get_instance();
                $arrayEbc = $CI->m_utils->getEbcByDistrito($departamento);
                $cmb = null;

                $cmb = '<option value="">Seleccionar</option>';
                foreach($arrayEbc AS $row) {
                    $cmb .= '<option value="'.$row['codigo'].'">'.utf8_decode($row['nom_estacion']).'</option>';
                }
                return $cmb;
            }
        }
        
        if(!function_exists('__buildComboPromotor')) {
            function __buildComboPromotor($idSubProyecto, $estado) {
                $CI =& get_instance();
                $arrayData = $CI->m_utils->getPromotorAllSubProyecto($estado, $idSubProyecto);
                $cmb = null;

                $cmb = '<option value="">Seleccionar</option>';
                $selected = null;
                foreach($arrayData AS $row) {
                    if($row['flg_selected'] == 1) {
                        $selected ='selected ';
                    } else {
                        $selected = null;
                    }
                    
                    $cmb .= '<option value="'.$row['id_promotor'].'" '.$selected.'>'.utf8_decode($row['nom_promotor']).'</option>';
                }
                return $cmb;
            }
        }
        
        if(!function_exists('__buildComboPreClasificacionItemfault')) {
            function __buildComboPreClasificacionItemfault($estado) {
                $CI =& get_instance();
                $arrayData = $CI->m_utils->getPreClasificacionItemfault($estado);
                $cmb = null;

                $cmb = '<option value="">Seleccionar</option>';
                $selected = null;
                foreach($arrayData AS $row) {
                    // if($row['flg_selected'] == 1) {
                        // $selected ='selected ';
                    // } else {
                        // $selected = null;
                    // }
                    
                    $cmb .= '<option value="'.$row['id_pre_clas_solicitud_itemfault'].'" '.$selected.'>'.utf8_decode($row['nom_pre_clas_solicitud']).'</option>';
                }
                return $cmb;
            }
        }
        
        if(!function_exists('__buildComboClasificacionItemfault')) {
            function __buildComboClasificacionItemfault($estado, $idPreClasSOlicitud, $flgAfectacion) {
                $CI =& get_instance();
                $arrayData = $CI->m_utils->getClasificacionItemfault($estado, $idPreClasSOlicitud, $flgAfectacion);
                $cmb = null;

                $cmb = '<option value="">Seleccionar</option>';
                $selected = null;
                foreach($arrayData AS $row) {
                    // if($row['flg_selected'] == 1) {
                        // $selected ='selected ';
                    // } else {
                        // $selected = null;
                    // }
                    $cmb .= '<option value="'.$row['id_clas_solicitud_itemfault'].'" '.$selected.'>'.utf8_decode($row['nom_clas_solicitud']).'</option>';
                }
                return $cmb;
            }
        }
        
        if(!function_exists('__buildComboTipoRegItemfault')) {
            function __buildComboTipoRegItemfault($id_tipo_reg) {
                $CI =& get_instance();
                $arrayData = $CI->m_utils->getTipoRegistroItemfault($id_tipo_reg);
                $cmb = null;

                $cmb = '<option value="">Seleccionar</option>';
                $selected = null;
                foreach($arrayData AS $row) {
                    $cmb .= '<option value="'.$row['id_tipo_reg_itemfault'].'" '.$selected.'>'.utf8_decode($row['nom_tipo_reg_itemfault']).'</option>';
                }
                return $cmb;
            }
        }
        
        if(!function_exists('__buildComboClasificacionRedItemfault')) {
            function __buildComboClasificacionRedItemfault() {
                $CI =& get_instance();
                $arrayData = $CI->m_utils->getClasificacionRedItemfault();
                $cmb = null;

                $cmb = '<option value="">Seleccionar</option>';
                $selected = null;
                foreach($arrayData AS $row) {
                    $cmb .= '<option value="'.$row['id_clasificacion_red_servicio'].'" '.$selected.'>'.utf8_decode($row['nom_red_clasificacion_red']).'</option>';
                }
                return $cmb;
            }
        }
        
        if(!function_exists('__buildComboServicioByClasificacionItemfault')) {
            function __buildComboServicioByClasificacionItemfault($idClasfRed) {
                $CI =& get_instance();
                $arrayData = $CI->m_utils->getServicioItemfaultByIdClasifRed($idClasfRed);
                $cmb = null;

                $cmb = '<option value="">Seleccionar</option>';
                $selected = null;
                foreach($arrayData AS $row) {
                    $cmb .= '<option value="'.$row['idServicio'].'" '.$selected.'>'.utf8_decode($row['servicioDesc']).'</option>';
                }
                return $cmb;
            }
        }
        
        if(!function_exists('__buildCmbMdfPqt')) {
            function __buildCmbMdfPqt($idEmpresaColab) {
                $CI =& get_instance();
                $arrayCentral = array();
                $arrayCentral = $CI->m_utils->getAllCentralPqt($idEmpresaColab);

                if(count($arrayCentral->result()) > 0) {
                    $cmb = '<option value="">Seleccionar</option>';
                    foreach($arrayCentral->result() AS $row){
                        $cmb .= '<option value="'.$row->idCentral.'">'.utf8_decode($row->tipoCentralDesc).'</option>';
                    }
                } else {
                    $cmb = null;
                }
                return $cmb;
            }
        }
        
        if(!function_exists('__buildCmbZonalMantenimiento')) {
            function __buildCmbZonalMantenimiento($idZonal, $estado) {
                $CI =& get_instance();
                $arrayCentral = array();
                $arrayZonal = $CI->m_utils->getZonalMantenimiento($idZonal, $estado);

                if(count($arrayZonal) > 0) {
                    $cmb = '<option value="">Seleccionar</option>';
                    foreach($arrayZonal AS $row){
                        $cmb .= '<option value="'.$row['id_zonal_mant'].'">'.utf8_decode($row['zonal_mant_desc']).'</option>';
                    }
                } else {
                    $cmb = null;
                }
                return $cmb;
            }
        }
        
        if(!function_exists('__buildCmbJefatura')) {
            function __buildCmbJefatura() {
                $CI =& get_instance();
                $arrayCentral = array();
                $arrayJefatura = $CI->m_utils->getJefaturaTB();

                if(count($arrayJefatura) > 0) {
                    $cmb = '<option value="">Seleccionar</option>';
                    foreach($arrayJefatura AS $row){
                        $cmb .= '<option value="'.$row->idJefatura.'">'.utf8_decode($row->descripcion).'</option>';
                    }
                } else {
                    $cmb = null;
                }
                return $cmb;
            }
        }
        
        if(!function_exists('__buildComboElementoRedServicio')) {
            function __buildComboElementoRedServicio($idRedServicio) {
                $CI =& get_instance();
                $arrayData = $CI->m_utils->getElementoRedServicioByIdRedServicio($idRedServicio);
                $cmb = null;

                $cmb = '<option value="">Seleccionar</option>';
                $selected = null;
                foreach ($arrayData as $row) {
                    $cmb .= '<option value="' . $row['idServicioElemento'] . '">' . $row['elementoDesc'] . '</option>';
                }
                
                return $cmb;
            }
        }
        
        if(!function_exists('__buildComboPepsNoBolsaPep')) {
            function __buildComboPepsNoBolsaPep() {
                $CI =& get_instance();
                $arrayData = $CI->m_utils->getPepSapNoBolsaPep();
                $cmb = null;

                // $cmb = '<option value="">Seleccionar</option>';
                $selected = null;
                foreach ($arrayData as $row) {
                    $cmb .= '<option value="' . $row['pep1'] . '">' . $row['pep1'] . '</option>';
                }
                
                return $cmb;
            }
        }

        if(!function_exists('__buildComboUsuarioAll')) {
            function __buildComboUsuarioAll() {
                $CI =& get_instance();
                $arrayData = $CI->m_utils->getUsuarioAll();
                $cmb = null;

                $cmb = '<option value="">Seleccionar</option>';
                $selected = null;
                foreach ($arrayData as $row) {
                    $cmb .= '<option value="' . $row['id_usuario'] . '">' .utf8_decode($row['nombre']). '</option>';
                }
                
                return $cmb;
            }
        }
        
    if (!function_exists('__cmbHTML2')) {
        function __cmbHTML2($html, $id, $onchange, $class, $nombre, $multiple, $flgOption)
        {
            $cmbHtml = '<select id="' . $id . '" name="' . $id . '" class="' . $class . '" ' . ($onchange != null ? 'onchange="' . $onchange . '"' : '') . '' . ($multiple == null ? '' : $multiple) . ' >
                            ' . ($flgOption == null ? '<option value="">Seleccionar ' . $nombre . '</option>' : '') . '
                            ' . $html . '
                        </select>';
            return $cmbHtml;
        }
    }

    if (!function_exists('__buildComboMotivoCVSC')) {
        function __buildComboMotivoCVSC($idMotivo = null)
        {
            $CI = &get_instance();
            $listaMotivos = $CI->m_agenda_cv->getMotivosCVSC();
            $html = null;
            if($listaMotivos != null){
                foreach ($listaMotivos as $row) {
                    $selected = ($row->id == $idMotivo) ? 'selected' : null;
                    $html .= '<option value="' . $row->id . '" ' . $selected . ' >' . $row->desc_motivo . '</option>';
                }
            }
            return $html;
        }
    }

    if (!function_exists('__buildComboMotivoCance')) {
        function __buildComboMotivoCance($idMotivo = null)
        {
            $CI = &get_instance();
            $listaMotivos = $CI->m_bandeja_consulta->getMotivosCancelacion();
            $html = null;
            foreach ($listaMotivos as $row) {
                $selected = ($row->idMotivo == $idMotivo) ? 'selected' : null;
                $html .= '<option value="' . $row->idMotivo . '" ' . $selected . ' >' .($row->motivoDesc) . '</option>';
            }
            return $html;
        }
    }

    if(!function_exists('__buildComboSubProyectoAll')) {
        function __buildComboSubProyectoAll($idProyecto, $idTipoPlanta) {
            $CI =& get_instance();
            $arrayData = $CI->m_utils->getSubProyectoAll($idProyecto, $idTipoPlanta);
            $cmb = null;

            $cmb = '<option value="">Seleccionar</option>';
            $selected = null;
            foreach ($arrayData as $row) {
                $cmb .= '<option value="' . $row['idSubProyecto']. '">' .utf8_decode($row['subProyectoDesc']). '</option>';
            }
            return $cmb;
        }
    }

    if(!function_exists('__buildProyectoAll')) {
        function __buildProyectoAll($idProyecto, $idTipoPlanta) {
            $CI =& get_instance();
            $arrayProyecto = $CI->m_utils->getProyectoAll($idProyecto, $idTipoPlanta);
            $cmb = null;
            $cmb .= '<option value="">Seleccionar<option>';
            foreach($arrayProyecto AS $row) {
                $cmb .= '<option value="'.$row['idProyecto'].'">'.$row['proyectoDesc'].'</option>';
            }
            
            return $cmb;
        }
    }

    if (!function_exists('__buildComboInversion')) {
        function __buildComboInversion($idEmpresaColab) {
            $CI = &get_instance();
            $arrayInversion = $CI->m_utils->getCodigoInversionByEmpresaColab($idEmpresaColab);
            $html = '<option value="">Seleccionar Codigo Inversion</option>';
            $selected = null;
            foreach ($arrayInversion as $row) {
                $html .= '<option value="' . $row['codigoInversion'] . '" ' . $selected . ' >' . $row['orden_inversion'] . ' ('.$row['codigoInversion'].')</option>';
            }
            return $html;
        }
    }

    if (!function_exists('__buildComboBandaHorariaCV')) {
        function __buildComboBandaHorariaCV($idBandaHoraria = null)
        {
            $CI = &get_instance();
            $listaBandaHoraria = $CI->m_agenda_cv->getAllBandaHorariaCV();
            $html = null;
            foreach ($listaBandaHoraria as $row) {
                $selected = ($row->id == $idBandaHoraria) ? 'selected' : null;
                $html .= '<option value="' . $row->id . '" ' . $selected . ' >' .($row->horaInicio . ' - ' . $row->horaFin) . '</option>';
            }
            return $html;
        }
    }

    if (!function_exists('__buildComboMotivoCancelaCita')) {
        function __buildComboMotivoCancelaCita($idMotivo = null)
        {
            $CI = &get_instance();
            $listaMotivo = $CI->m_agenda_cv->getMotivoCancela();
            $html = null;
            foreach ($listaMotivo as $row) {
                $selected = ($row->idMotivo == $idMotivo) ? 'selected' : null;
                $html .= '<option value="' . $row->idMotivo . '" ' . $selected . ' >' .$row->descripcion. '</option>';
            }
            return $html;
        }
    }

    if (!function_exists('__buildComboMotivoReagendaCita')) {
        function __buildComboMotivoReagendaCita($idMotivo = null)
        {
            $CI = &get_instance();
            $listaMotivo = $CI->m_agenda_cv->getAllMotivoReagendar();
            $html = null;
            foreach ($listaMotivo as $row) {
                $selected = ($row->idMotivo == $idMotivo) ? 'selected' : null;
                $html .= '<option value="' . $row->idMotivo . '" ' . $selected . ' >' .$row->descripcion. '</option>';
            }
            return $html;
        }
    }
	
    if (!function_exists('__buildCmbTipoEntidad')) {
        function __buildCmbTipoEntidad($idTipoEntidad = null)
        {
            $CI = &get_instance();
            $arrayDataTipoEntidad = $CI->m_utils->getTipoEntidadAll();
            $html = null;
            $html = '<option value="">Seleccionar Tipo</option>';
            foreach ($arrayDataTipoEntidad as $row) {
                $selected = ($row['idTipoEntidad'] == $idTipoEntidad) ? 'selected' : null;
                $html .= '<option value="' . $row['idTipoEntidad'] . '" ' . $selected . ' >' . $row['tipoEntidadDesc'] . '</option>';
            }
            return $html;
        }
    }

    if (!function_exists('__buildCmbDistrito')) {
        function __buildCmbDistrito($idDistrito = null)
        {
            $CI = &get_instance();
            $arrayDataDistrito = $CI->m_utils->getDistritoAll();
            $html = null;
            $html = '<option value="">Seleccionar Tipo</option>';
            foreach ($arrayDataDistrito as $row) {
                $selected = ($row['idDistrito'] == $idDistrito) ? 'selected' : null;
                $html .= '<option value="' . $row['idDistrito'] . '" ' . $selected . ' >' . $row['distritoDesc'] . '</option>';
            }
            return $html;
        }
    }

    if (!function_exists('__buildComboEntidades')) {
        function __buildComboEntidades($itemplan,$idEstacion,$idEntidad = null)
        {
            $CI = &get_instance();
            $listaMotivo = $CI->m_utils->getEntidades($itemplan,$idEstacion);
            $html = null;
            // $html .= '<option value="">NO REQUIERE LICENCIA</option>';
            $hasEntidad = null;
            // $html .= '<option value="" '.($hasEntidad).'>NO REQUIERE LICENCIA</option>';
            $html2 = null;
            foreach ($listaMotivo as $row) {
                $selected = ($row->idEntidad == $idEntidad) ? 'selected' : null;
                if($row->marcado == 1){
                    $disabled = 'disabled';
                    $hasEntidad = true;
                }else{
                    $disabled = null;
                }
                $html2 .= '<option value="' . $row->idEntidad . '" ' . $selected . ' ' . $disabled . ' >' .$row->entidadDesc. '</option>';
            }

            $html .= '<option value="" '.($hasEntidad ? 'disabled' : null).'>NO REQUIERE LICENCIA</option>'.$html2;

            return $html;
        }
    }

    if (!function_exists('__buildComboMotivoSeguiCV')) {
        function __buildComboMotivoSeguiCV($idEstadoPlan,$itemplan)
        {
            $CI = &get_instance();
            $listaMotivo = $CI->m_utils->getMotivoSeguiByIdEstadoPlan($idEstadoPlan,$itemplan);
            $html = null;
            foreach ($listaMotivo as $row) {
                $selected = null;
                $html .= '<option value="' . $row->id . '" ' . $selected . ' >' .$row->desc_motivo. '</option>';
            }
            return $html;
        }
    }
	
	if (!function_exists('__buildCmbPorcentaje')) {
        function __buildCmbPorcentaje($porcentaje)
        {
            $CI = &get_instance();
            $arrayPorcentaje = $CI->m_utils->getPorcentajeLiqui();
            $html = null;
            foreach ($arrayPorcentaje as $row) {
                $selected = ($row['porcentaje'] == $porcentaje) ? 'selected' : null;
                $html .= '<option value="' . $row['porcentaje'] . '" ' . $selected . ' >' .$row['porcentaje']. '%</option>';
            }
            return $html;
        }
    }
	
	 if (!function_exists('__buildComboEstadoPlan')) {
        function __buildComboEstadoPlan()
        {
            $CI = &get_instance();
            $listaEstadoPlan = $CI->m_utils->getAllEstadoPlan();
            $html = '<option value="">Seleccionar<option>';
            foreach ($listaEstadoPlan as $row) {
                $selected = null;
                $html .= '<option value="' . $row->idEstadoPlan . '" ' . $selected . ' >' .$row->estadoPlanDesc. '</option>';
            }
            return $html;
        }
    }

if (!function_exists('__buildCmbTipoSolVr')) {
        function __buildCmbTipoSolVr($only_devolucion)
        {
            $CI = &get_instance();
            if($only_devolucion){
                $arrayTipoSolVr = $CI->m_solicitud_Vr->getOnlyDevolucion();
            }else{
                $arrayTipoSolVr = $CI->m_solicitud_Vr->getAllTipoSolVr();
            }
            
            $html = null;
            foreach ($arrayTipoSolVr as $row) {
                $selected = null;
                $html .= '<option value="' . $row['id'] . '" ' . $selected . ' >' . $row['nombreTipoSolicitud'] . '</option>';
            }
            return $html;
        }
    }

    if (!function_exists('__buildComboJefaturaSap')) {
        function __buildComboJefaturaSap()
        {
            $CI = &get_instance();
            $arrayJefaturaSap = $CI->m_solicitud_Vr->getJefaturaSapCmb();
            $html = null;
            foreach ($arrayJefaturaSap as $row) {
                $selected = null;
                $html .= '<option value="' . $row['idJefatura'] . '" ' . $selected . ' >' . $row['descripcion'] . '</option>';
            }
            return $html;
        }
    }

    if (!function_exists('__buildComboEECC')) {
        function __buildComboEECC()
        {
            $CI = &get_instance();
            $arrayEECC = $CI->m_solicitud_Vr->getAllEECCForGestionVr();
            $html = null;
            foreach ($arrayEECC as $row) {
                $selected = null;
                $html .= '<option value="' . $row['idEmpresaColab'] . '" ' . $selected . ' >' . $row['empresaColabDesc'] . '</option>';
            }
            return $html;
        }
    }

    if (!function_exists('__buildComboEstadoSolVr')) {
        function __buildComboEstadoSolVr()
        {
            $CI = &get_instance();
            $arrayEstadoSolVr = array(
                array('id' => 0, 'descripcion' => 'ATENCIÓN PENDIENTE'),
                array('id' => 1, 'descripcion' => 'ATENCIÓN PARCIAL'),
                array('id' => 2, 'descripcion' => 'ATENCIÓN TOTAL'),
                array('id' => 3, 'descripcion' => 'ATENCIÓN RECHAZADA'),
            );
            $html = null;
            foreach ($arrayEstadoSolVr as $row) {
                $selected = null;
                $html .= '<option value="' . $row['id'] . '" ' . $selected . ' >' . $row['descripcion'] . '</option>';
            }
            return $html;
        }
    }
	
	if (!function_exists('__buildComboMotivoCancelaIP')) {
        function __buildComboMotivoCancelaIP($idMotivo = null)
        {
            $CI = &get_instance();
            $listaMotivo = $CI->m_utils->getMotivoCancelaIP();
            $html = null;
            foreach ($listaMotivo as $row) {
                $selected = ($row->idMotivo == $idMotivo) ? 'selected' : null;
                $html .= '<option value="' . $row->idMotivo . '" ' . $selected . ' >' .$row->motivoDesc. '</option>';
            }
            return $html;
        }
    }

    if (!function_exists('__buildComboEECCElec')) {
        function __buildComboEECCElec()
        {
            $CI = &get_instance();
            $arrayEECC = $CI->m_utils->getEmpresaElectricaAll();
            $html = '<option value="">Seleccionar</option>';
            foreach ($arrayEECC as $row) {
                $selected = null;
                $html .= '<option value="' . $row['idEmpresaElec'] . '" ' . $selected . ' >' . $row['empresaElecDesc'] . '</option>';
            }
            return $html;
        }
    }

    if (!function_exists('__buildComboTipoReq')) {
        function __buildComboTipoReq($idTipoReq)
        {
            $CI = &get_instance();
            $arrayData = $CI->m_utils->getTipoRequerimientoAll($idTipoReq);
            $html = '<option value="">Seleccionar</option>';
            foreach ($arrayData as $row) {
                $html .= '<option value="' . $row['id_tipo_requerimiento'] . '" >' . $row['requerimientoDesc'] . '</option>';
            }
            return $html;
        }
    }

    if (!function_exists('__buildComboTipoProyecto')) {
        function __buildComboTipoProyecto($idTipoProy)
        {
            $CI = &get_instance();
            $arrayData = $CI->m_utils->getTipoProyectoAll($idTipoProy);
            $html = '<option value="">Seleccionar</option>';
            foreach ($arrayData as $row) {
                $html .= '<option value="' . $row['id_tipo_proyecto'] . '" >' . $row['tipoProyectoDesc'] . '</option>';
            }
            return $html;
        }
    }

    if (!function_exists('__buildComboAreaReq')) {
        function __buildComboAreaReq($idAreaReq)
        {
            $CI = &get_instance();
            $arrayData = $CI->m_utils->getAreaReqAll($idAreaReq);
            $html = '<option value="">Seleccionar</option>';
            foreach ($arrayData as $row) {
                $html .= '<option value="' . $row['id_area_requerimiento'] . '" >' . $row['areaRequeDesc'] . '</option>';
            }
            return $html;
        }
    }

    if (!function_exists('__buildComboMotivoByAreaReq')) {
        function __buildComboMotivoByAreaReq($idAreaReq)
        {
            $CI = &get_instance();
            $arrayData = $CI->m_utils->getMotivoReqByAreaReq($idAreaReq);
            $html = '<option value="">Seleccionar</option>';
            foreach ($arrayData as $row) {
                $html .= '<option value="' . $row['id_motivo_requerimiento'] . '" >' . $row['motivoDesc'] . '</option>';
            }
            return $html;
        }
    }

    if(!function_exists('__buildCmbMdfOnlyCentral')) {
        function __buildCmbMdfOnlyCentral() {
            $CI =& get_instance();
            $arrayCentral = array();
            $arrayCentral = $CI->m_utils->getAllCentralByTipoCentral(1);//1 = MDF, 2 = NODO, 3 = EBC

            if(count($arrayCentral->result()) > 0) {
                $cmb = '<option value="">Seleccionar</option>';
                foreach($arrayCentral->result() AS $row){
                    $cmb .= '<option value="'.$row->idCentral.'">'.utf8_decode($row->codigo .' - ' .$row->centralDesc).'</option>';
                }
            } else {
                $cmb = null;
            }
            return $cmb;
        }
    }

    if (!function_exists('__buildComboCentralByTipoRed')) {
        function __buildComboCentralByTipoRed($tipoRed,$idEECC)
        {
            $CI = &get_instance();
            if($tipoRed == 1){
                $listaCentral = $CI->m_utils->getAllCentralPqt($idEECC);
            }else{
                $listaCentral = $CI->m_utils->getAllCentralPqtCotizacionSisego($idEECC);
            }
            
            $html = null;
            foreach ($listaCentral->result() as $row) {
                $selected = null;
                $html .= '<option value="' . $row->idCentral . '" ' . $selected . ' >' .($row->tipoCentralDesc) . '</option>';
            }
            return $html;
        }
    }

    if (!function_exists('__buildComboCentralToCotiB2b')) {
        function __buildComboCentralToCotiB2b($idEECC)
        {
            $CI = &get_instance();            
            $listaCentral = $CI->m_utils->getCentraoToCotib2b($idEECC);
            $html = null;
            foreach ($listaCentral->result() as $row) {
                $selected = null;
                $html .= '<option value="' . $row->idCentral . '" ' . $selected . ' >' .($row->tipoCentralDesc) . '</option>';
            }
            return $html;
        }
    }
    
    if(!function_exists('__buildComboFaciByCentral')) {
        function __buildComboFaciByCentral($idCentral) {
            $CI =& get_instance();
            $arrayEbc = $CI->m_utils->getFacilidadXCentral($idCentral);
            $cmb = null;

            $cmb = '<option value="">Seleccionar</option>';
            foreach($arrayEbc->result() AS $row) {
                $cmb .= '<option value="'.$row->facilidad.'">'.utf8_decode($row->facilidad).'</option>';
            }
            return $cmb;
        }
    }

    if(!function_exists('__buildProyectoAllCreateIP')) {
        function __buildProyectoAllCreateIP($idProyecto, $idTipoPlanta) {
            $CI =& get_instance();
            $arrayProyecto = $CI->m_utils->getProyectoAllCreateIP($idProyecto, $idTipoPlanta);
            $cmb = null;
            $cmb .= '<option value="">Seleccionar<option>';
            foreach($arrayProyecto AS $row) {
                $cmb .= '<option value="'.$row['idProyecto'].'">'.$row['proyectoDesc'].'</option>';
            }
            
            return $cmb;
        }
    }

    if (!function_exists('__buildComboMotivCancelar')) {
        function __buildComboMotivCancelar($tipo) {
            $CI = &get_instance();
            $arrayInversion = $CI->m_utils->getMotivoCancelaIPByTipo($tipo);
            $html = '<option value="">Seleccionar Motivo<ption>';
            $selected = null;
            foreach ($arrayInversion as $row) {
                $html .= '<option value="' . $row->idMotivo . '" ' . $selected . ' >' . $row->motivoDesc.'</option>';
            }
            return $html;
        }
    }

    if (!function_exists('__buildComboEstadosToCanTrunSus')) {
        function __buildComboEstadosToCanTrunSus($idEstadoPlanActual, $idEstadoPlanAnterior, $idTipoPlanta, $idSubProyecto) {
            $CI = &get_instance();
            $arrayInversion = $CI->m_utils->getEstadosValidosToCanTrunSus($idEstadoPlanActual, $idEstadoPlanAnterior, $idTipoPlanta, $idSubProyecto);
            $html = '<option value="">Seleccionar Estado<ption>';
            $selected = null;
            foreach ($arrayInversion as $row) {
                $html .= '<option value="' . $row->idEstadoPlan . '" ' . $selected . ' >' . $row->estadoPlanDesc.'</option>';
            }
            return $html;
        }
    }
	
	if(!function_exists('__buildComboDivCauOyM')) {
        function __buildComboDivCauOyM() {
            $CI =& get_instance();
            $arrayData = $CI->m_utils->getAllDivCauOyM();
            $cmb = null;

            $cmb = '<option value="">Seleccionar</option>';
            $selected = null;
            foreach ($arrayData as $row) {
                $cmb .= '<option value="' . $row['codigo']. '">' .utf8_decode($row['codigo']). '</option>';
            }
            return $cmb;
        }
    }
