<?php  

function insertar_log($data)
{

    $ci =& get_instance();
    $ci->db->insert('log', $data);

}


function insertar_logMatrizSeguimiento($data)
{

    $ci =& get_instance();
    $ci->db->insert('matrizseguimiento_log', $data);

}

function insertar_logMatrizJumpeo($data)
{

    $ci =& get_instance();
    $ci->db->insert('matrizjumpeo_history', $data);

}


function pre($array)
{
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}


function mayusculas($string)
{
    return strtoupper($string);
}

function verifyDate($fecha)
{
    preg_match('/(\d{1,2})+(-)+(\d{2})+(-)+(\d{4})/', $fecha, $salida);
    if(count($salida)>=1){
        $salida = array_values(array_diff($salida,['-']));
        if(!in_array($salida[2],range(1,12))){ return false; }
        if(!in_array($salida[3],range(1900,2500))){ return false; }
        if(!in_array($salida[1],range(1,cal_days_in_month(CAL_GREGORIAN, $salida[2], $salida[3])))){ return false; }
        return true;
    }else{
        return false;
    }
}

function validateDate($date, $format = 'Y-m-d')
{
    // pre($date);
    if (!is_numeric($date))
    {
        return false;
    }
    return true;
}
?>