<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Lib_sinfix {
function mess($n){
 $mes=['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']; 
 return $mes[$n];
}
function datapickerhtml(){
?>
<div class="input-group">
<span class="input-group-addon"><i class="icon ion-calendar tx-16 lh-0 op-6"></i></span>
<input type="text" class="form-control fc-datepicker" placeholder="MM/DD/YYYY">
</div>
<?php    
}
function datapickerjs(){
?>
 <script type="text/javascript">
$(function(){
  $(".fc-datepicker").datepicker({
    showOtherMonths:!0,selectOtherMonths:!0
  });
  $.datepicker.regional.es={
    closeText:"Cerrar",prevText:"<Ant",nextText:"Sig>",
    currentText:"Hoy",
    monthNames:"Enero Febrero Marzo Abril Mayo Junio Julio Agosto Septiembre Octubre Noviembre Diciembre".split(" "),
    monthNamesShort:"Ene Feb Mar Abr May Jun Jul Ago Sep Oct Nov Dic".split(" "),
    dayNames:"Domingo Lunes Martes Mi\u00e9rcoles Jueves Viernes S\u00e1bado".split(" "),
    dayNamesShort:"Dom Lun Mar Mi\u00e9 Juv Vie S\u00e1b".split(" "),
dayNamesMin:"Do Lu Ma Mi Ju Vi S\u00e1".split(" "),
weekHeader:"Sm",
dateFormat:"dd/mm/yy",
firstDay:1,isRTL:!1,
showMonthAfterYear:!1,yearSuffix:""};$.datepicker.setDefaults($.datepicker.regional.es)});
</script>
<?php    
}
  function fecha_hora($h){
    $hora=explode(" ",$h);
    $f=explode("-",$hora[0]);
    return $hora[1]." ".$f[2]."/".$f[1]."/".$f[0];
  }
  function fecha_hora_a($separador,$fecha){
    if($fecha != null) {
      $fasig=explode(" ", $fecha);
      $fasigg=explode("-", $fasig[0]); 
      $rfecha=$fasigg[2].$separador.$fasigg[1].$separador.$fasigg[0]; 
      return array(0=>$rfecha,1=>$fasig[1]);
    }
  }
function hora_sql(){
  return date("Y-m-d H:i:s");  
  }
  function sumar_fecha ($dias,$fecha)
  {
    $nuevafecha = strtotime ( $dias , strtotime ( $fecha ) ) ;
    return date ( 'd-m-Y' , $nuevafecha );
  }
  function sumar_fecha_bd ($dias,$fecha)
  {
    $nuevafecha = strtotime ( $dias , strtotime ( $fecha ) ) ;
    return date ( 'Y-m-d' , $nuevafecha );
  }
  function valida($data){
    return strtolower(str_replace(" ", "", $data));
  }
  function permiso_ruta($tipo_usuario){
    $re=explode(",",$tipo_usuario);
    foreach ($re as $valor) { 
      if($_SESSION["id_usuario_tipo"]==$valor)
      {
        header("location:index.php");exit;}
      }
  }
    
}