  <?php
    //funciones 
   //funcion para justificar la inasistencia
use app\models\SysRrhhCuadrillasJornadasMov;
use app\models\SysRrhhEmpleadosPermisos;
use app\models\SysRrhhFeriados;
use app\models\SysRrhhMarcacionesEmpleados;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhSoextrasEmpleados;

function getJustificacion($fecha, $id_sys_rrhh_cedula)
    {
        
        $jornada =  ObtenerTipoJornada('001', $id_sys_rrhh_cedula, $fecha);
        $dia = date("N", strtotime($fecha));
          
        if($jornada == 'N'):
          
            if($dia >= 1  && $dia <= 5):
              
                return JustificacionNormal($fecha, $id_sys_rrhh_cedula, $jornada);
          
            else:
              
                return "DIA DE DESCANSO";
              
            endif;
  
        else:
          
            $agenda = SysRrhhCuadrillasJornadasMov::find()->select(['isnull(id_sys_rrhh_jornada,100)'])->Where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])->andWhere(['fecha_laboral'=> $fecha])->orderBy(['fecha_registro'=> SORT_DESC])->scalar();
          
            if($agenda > 0 ):
              
                if(intval($agenda) == 100):  
                                           
                    $permiso  = getPermiso($fecha, $id_sys_rrhh_cedula);
                              
                if ($permiso):
                              
                    return trim($permiso['comentario']);
                
                else:
                     //revisar si esta de vacaciones
                    $vacaciones = getVacaciones($fecha, $id_sys_rrhh_cedula);
                                  
                    if($vacaciones):
                                      
                        return 'GOZO DE VACACIONES';
                                      
                    else:
                                      
                        return 'DIA LIBRE';
                                  
                    endif;
                              
                endif;
                              
            else:
                //si esta libre validamos por justificacion normal 
                return JustificacionNormal($fecha, $id_sys_rrhh_cedula, 'N');
                      
        endif;
              
            else:
                //si no tiene una agenda valida si es feriado 
                $feriado = getFeriado($fecha);
                      
                if($feriado):
                    //verificamos si la fecha es feriado
                    return  $feriado->feriado;

                else:
                    
                    return 'FALTA';
                
                endif;
                      
            endif;

        endif;
}
   
function JustificacionNormal($fecha, $cedula, $tipo){
        
        $permiso  = getPermiso($fecha, $cedula); 
        
        if ($permiso) :
        
            return  trim($permiso['comentario']);
        
        else:
        //verificamos si esta de vacaciones
        
            $vacaciones =  getVacaciones($fecha, $cedula);
       
                if($vacaciones):
                
                    return 'GOZO DE VACACIONES';
                
                else:
                
                    if($tipo == 'N'):       
                
                        $feriado = getFeriado($fecha);
                    
                            if($feriado):
                                //verificamos si la fecha es feriado
                                return $feriado->feriado;
                        
                            else:
                        
                                return 'FALTA';
                            
                            endif;
                        
                        else:
                           
                            return 'FALTA';
                             
                        endif;
                        
                    endif;

                endif;        
}

function getLibre($id_sys_rrhh_cedula , $fecha){
        
        $agenda = SysRrhhCuadrillasJornadasMov::find()
        ->select(['isnull(id_sys_rrhh_jornada,100)'])
        ->where(['id_sys_empresa'=> '001'])
        ->andWhere(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])
        ->andWhere(['fecha_laboral'=> $fecha])
        ->orderBy(['fecha_registro'=> SORT_DESC])
        ->scalar();
        
        if($agenda > 0 ):
            
            if(intval($agenda) == 100):
                  return true;
            endif;
            
        endif;
        
        return false;
        
    }
//Obtenemos permisos 
function getPermiso($fecha, $cedula){
        
       return  (new \yii\db\Query())
        ->select('*')
        ->from("sys_rrhh_empleados_permisos pemp")
        ->innerjoin("sys_rrhh_permisos p", "pemp.id_sys_rrhh_permiso = p.id_sys_rrhh_permiso")
        ->where("'{$fecha}' >= fecha_ini")
        ->andwhere("'{$fecha}' <= fecha_fin")
        ->andwhere("id_sys_rrhh_cedula like '%{$cedula}%'")
        ->andwhere("estado_permiso <>  'N'")
        ->orderby("nivel")
        ->one(SysRrhhEmpleadosPermisos::getDb());
        
    }
function BuscaPermiso($fecha, $cedula){
        
        $permiso = (new \yii\db\Query())
        ->select(["comentario"])
        ->from("sys_rrhh_empleados_permisos pemp")
        ->innerjoin("sys_rrhh_permisos p", "pemp.id_sys_rrhh_permiso = p.id_sys_rrhh_permiso")
        ->where("'{$fecha}' >= fecha_ini")
        ->andwhere("'{$fecha}' <= fecha_fin")
        ->andwhere("id_sys_rrhh_cedula like '%{$cedula}%'")
        ->andwhere("estado_permiso <> 'N'")
        ->orderby("nivel")
        ->one(SysRrhhEmpleadosPermisos::getDb());
        
            if($permiso):
            
                return $permiso['comentario'];
            
            endif;
        
        return  '';
    }
 //Obrenemos vacaciones 
function getVacaciones($fecha, $cedula){
        
    return (new \yii\db\Query())
        ->select('*')
        ->from("sys_rrhh_vacaciones_solicitud")
        ->where("'{$fecha}' >= fecha_inicio and   '{$fecha}'<= fecha_fin")
        ->andwhere("id_sys_rrhh_cedula = '{$cedula}'")
        ->andwhere("tipo = 'G'")
        ->andwhere("estado_solicitud = 'A'")
        ->one(SysRrhhCuadrillasJornadasMov::getDb());
    
}
//obtenemos Feriado
function getFeriado($fecha){
        
    return  SysRrhhFeriados::find()->Where(['fecha'=> $fecha ])->one();
        
}
//calculo de horas extras noctrurnas  
function gethoras25 ($entrada, $salida, $cedula, $fecha){
    
        $ini25 = $fecha.' 19:00:00';
        //$intermedio = date('Y-m-d', strtotime($fecha."+ 1 days")). ' 00:40:00';
        $fin25 = date('Y-m-d', strtotime($fecha."+ 1 days")). ' 06:00:00';
       
        //$horatrabas  =  getTotalhoras($entrada, $salida);
        //$horatotales =  '08:30:00';
        //print_r($entrada);

        //if($horatrabas > $horatotales):
            
            if($salida > $ini25):
            
                if( $entrada > $ini25 && $salida < $fin25):
                
                    return getTotalhoras($entrada, $salida);
                
                elseif($entrada > $ini25 && $salida > $fin25):
                
                    return getTotalhoras($entrada, $fin25);
                
                elseif ($entrada < $ini25 && $salida < $fin25):
                
                    //if($salida > $intermedio){
                    //    return getTotalhoras($intermedio, $salida); 
                    //};
                    //if($salida < $fin25){
                    return getTotalhoras($ini25, $salida);
                    //};

                elseif($entrada < $ini25 && $salida > $fin25):
                
                    return getTotalhoras($ini25, $fin25);
                
                endif;
            
            endif;
       
        //endif;
     
      /* $ini1      =    date('Y-m-d', strtotime($entrada)).' 19:00:00';
         $fin1      =    date('Y-m-d', strtotime($entrada)).' 00:00:00';
        
         $ini2      =    date('Y-m-d', strtotime($salida)).' 00:01:00';
         $fin2      =    date('Y-m-d', strtotime($salida)).' 06:00:00';
      
        
         $ini3      =    date('Y-m-d', strtotime($salida)).' 19:00:00';
   
      
         $horanormal  =  date('Y-m-d', strtotime($fecha)).' '.gethora_normal($cedula, $fecha, $entrada, $salida);
         $hsalida     =  getSalidaLaboral($entrada, $salida, $horanormal);
        
        
        if($salida > $ini1 && $salida < $fin1):
        
        
               return getTotalhoras($ini1, $salida);
        
        
        elseif($salida > $ini2 && $salida < $fin2):
        
              if($entrada > $ini1):
                 
                  return getTotalhoras($entrada, $salida);
              
              else:
              
                  return getTotalhoras($ini1, $salida);
              
              endif;
              
        elseif($salida > $fin2):
          
           if($entrada > $ini1 && $hsalida > $fin2):
           
                return getTotalhoras($entrada, $fin2);
        
           elseif($entrada < $ini2 && $hsalida < $fin2):
           
                 return getTotalhoras($ini1, $fin2);
           
           elseif($salida >  $ini3):
           
                 return getTotalhoras($ini3, $salida);
           
           endif;
         
        
        endif;
        
  
      */
    return "00:00:00";
       
}
//calculo de horas extras del 50 %   
function gethoras50($entrada,$salida, $sdesayuno, $salmuerzo, $smerienda, $cedula, $fecha, $feriado){
    
    $ini50 = $fecha.' 06:00:00';
    $fin50 = $fecha.' 23:59:00';
    $entradadesayuno = '00:00:00';
    $thorasdesayuno  = '00:00:00';
    $entradaalmuerzo = '00:00:00';
    $thorasalmuerzo  = '00:00:00';
    $entradamerienda = '00:00:00';
    $thorasmerienda  = '00:00:00';
                   
    $horatrabas  =  date('Y-m-d', strtotime($fecha)).' '.getTotalhoras($entrada, $salida);
    $horanormal  =  date('Y-m-d', strtotime($fecha)).' '.gethora_normal($cedula, $fecha, $entrada, $salida);

    $dia             =  date("N", strtotime($fecha));
    $tiempo          =  0;
    
    if(date('Y-m-d', strtotime($entrada)) ==  date('Y-m-d', strtotime($salida))):

        $lunchs =  obtenerComidasDiarias($cedula,$fecha);

    else:

        $lunchs =  obtenerComidasDiarias($cedula,$salida);
    
    endif;
    
    
    foreach($lunchs as $item):

        if($dia == 6 || $dia == 7):

            $ingreso =  date('H:i:s', strtotime($entrada));

            if($ingreso < $item['hora'] && $item['id_sys_rrhh_comedor'] == 1):

                $entradadesayuno = date('H:i:s', strtotime($item['hora']));

                $id_lunch = obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                if($sdesayuno != "00:00:00"){

                    $thorasdesayuno = getTotalhorascomedor($entradadesayuno, $sdesayuno);

                    if($thorasdesayuno > $id_lunch['tiempo_descuento']){

                        $tiempo += floatval(HorasToDecimal($thorasdesayuno));

                    }else{

                        $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                    }

                }else{

                    $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                }

            elseif($item['id_sys_rrhh_comedor'] == 2):

                $entradaalmuerzo = date('H:i:s', strtotime($item['hora']));

                $id_lunch = obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                if($salmuerzo != "00:00:00"){

                    $thorasalmuerzo = getTotalhorascomedor($entradaalmuerzo, $salmuerzo);

                    if($thorasalmuerzo > $id_lunch['tiempo_descuento']){

                        $tiempo += floatval(HorasToDecimal($thorasalmuerzo));

                    }else{

                        $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                    }

                }else{

                    $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                }

            elseif($item['id_sys_rrhh_comedor'] == 3):

                $entradamerienda = date('H:i:s', strtotime($item['hora']));
                
                $id_lunch = obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                if($smerienda != "00:00:00"){

                    $thorasmerienda = getTotalhorascomedor($entradamerienda, $smerienda);

                    if($thorasmerienda > $id_lunch['tiempo_descuento']){

                        $tiempo += floatval(HorasToDecimal($thorasmerienda));

                    }else{

                        $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                    }

                }else{

                    $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                }
            
            endif;

        else:

            if($item['id_sys_rrhh_comedor'] == 1):

                $entradadesayuno = date('H:i:s', strtotime($item['hora']));

                $id_lunch = obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                if($sdesayuno != "00:00:00"){

                    $thorasdesayuno = getTotalhorascomedor($entradadesayuno, $sdesayuno);

                    if($thorasdesayuno > $id_lunch['tiempo_descuento']){

                        $tiempo += floatval(HorasToDecimal($thorasdesayuno));

                    }else{

                        $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                    }

                }else{

                    $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                }

            elseif($item['id_sys_rrhh_comedor'] == 2):

                $entradaalmuerzo = date('H:i:s', strtotime($item['hora']));

                $id_lunch = obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                if($salmuerzo != "00:00:00"){

                    $thorasalmuerzo = getTotalhorascomedor($entradaalmuerzo, $salmuerzo);

                    if($thorasalmuerzo > $id_lunch['tiempo_descuento']){

                        $tiempo += floatval(HorasToDecimal($thorasalmuerzo));

                    }else{

                        $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                    }

                }else{

                    $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                }

            elseif($item['id_sys_rrhh_comedor'] == 3):

                $entradamerienda = date('H:i:s', strtotime($item['hora']));
                
                $id_lunch = obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                if($smerienda != "00:00:00"){

                    $thorasmerienda = getTotalhorascomedor($entradamerienda, $smerienda);

                    if($thorasmerienda > $id_lunch['tiempo_descuento']){

                        $tiempo += floatval(HorasToDecimal($thorasmerienda));

                    }else{

                        $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                    }

                }else{

                    $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                }
            
            endif;

        endif;

    endforeach;
    
              
    if($horatrabas > $horanormal):
             
        $dia = date("N", strtotime($salida));

        $diaLibre = getDiaLibre($cedula, $fecha);

        if($feriado == false):

            if(!$diaLibre):
                
                if($dia >= 1 && $dia <= 5):
                        
                    if(date('Y-m-d', strtotime($entrada)) == date('Y-m-d', strtotime($salida))):

                        if($lunchs):

                            return restarMinutosLunch(DecimaltoHoras(number_format($tiempo, 2, '.', '')),getTotalhoras($horanormal, $horatrabas));
            
                        else:
            
                            return getTotalhoras($horanormal, $horatrabas);
            
                        endif;
                                
                    else:
        
                        $ini50 =  date('Y-m-d', strtotime($fecha."+ 1 days")).' 06:00:00';
        
                        $agendamiento = obtenerAgendamiento($cedula, $fecha);
                                    
                        if($agendamiento):
        
                            if($agendamiento['salida'] >= $ini50):

                                if($lunchs):

                                    return restarMinutosLunch(DecimaltoHoras(number_format($tiempo, 2, '.', '')),getTotalhoras(date("Y-m-d H:i:s",strtotime( $agendamiento['salida'])),$salida));
                    
                                else:
                    
                                    return  getTotalhoras(date("Y-m-d H:i:s",strtotime( $agendamiento['salida'])),$salida);
                    
                                endif;
                    
                            endif;   
                            
                            if($lunchs):

                                return restarMinutosLunch(DecimaltoHoras(number_format($tiempo, 2, '.', '')),getTotalhoras($ini50,$salida));
                
                            else:
                
                                return  getTotalhoras($ini50,$salida);
                
                            endif;
                            
                        endif;
                            
                                
                        if ($salida > $fin50 && $salida < $ini50):
                                
                            $salidanormal = suma_horas(date('H:i:s', strtotime($entrada)), "08:30:00");

                            if($lunchs):

                                return restarMinutosLunch(DecimaltoHoras(number_format($tiempo, 2, '.', '')),getTotalhoras($fecha.' '.$salidanormal, $fin50));
                
                            else:
                
                                return getTotalhoras($fecha.' '.$salidanormal, $fin50);
                
                            endif;
                                        
                        elseif ($salida > $ini50):
                                
                            //Aqui sumar la suma de horas extras del 50% hasta las 23.59 y despues de 06:00:00
                            //Revisar 
                                
                            $salidanormal = suma_horas(date('H:i:s', strtotime($entrada)), "08:30:00");

                            if($lunchs):

                                return restarMinutosLunch(DecimaltoHoras(number_format($tiempo, 2, '.', '')),getTotalhoras($fecha.' '.$salidanormal, $fin50));
                
                            else:
                
                                return getTotalhoras($fecha.' '.$salidanormal, $fin50);
                
                            endif;
                                
                            //return getTotalhoras($ini50, $salida);
                                
                        endif;  
                    endif;
                endif; 
            endif;   
        endif;

    endif;
         
    return '00:00:00';
}
 //restar minutos lunchs   
 function restarMinutosLunch ($tiempo,$totalhoras){

    if($tiempo < $totalhoras):
     
        $fechaUno=new DateTime($tiempo);
        $fechaDos=new DateTime($totalhoras);
         
        $dateInterval = $fechaUno->diff($fechaDos);
         
        return  $dateInterval->format('%H:%i:%s');
    
    endif;

    return '00:00:00';
  }
 //calculo de horas extras del 100 %  
 function gethoras100($entrada, $salida, $sdesayuno, $salmuerzo, $smerienda, $cedula, $fecha, $feriado, $agendamiento){
        
    $horatrabas      =  date('Y-m-d', strtotime($entrada)).' '.getTotalhoras($entrada, $salida); //horas trabajadas
    $horanormal      =  date('Y-m-d', strtotime($entrada)).' '."08:00:00"; //horas normal
    $hsalida         =  getSalidaLaboral($entrada, $salida, $horanormal);
    $dia             =  date("N", strtotime($fecha));
    $ini100          =  date('Y-m-d', strtotime($fecha."+ 1 days")).' 00:00:00';
    $fin100          =  date('Y-m-d', strtotime($fecha."+ 1 days")).' 06:00:00';
    $tiempo          =  0;
    $lunchs          =  [];
    $entradadesayuno = '00:00:00';
    $thorasdesayuno  = '00:00:00';
    $entradaalmuerzo = '00:00:00';
    $thorasalmuerzo  = '00:00:00';
    $entradamerienda = '00:00:00';
    $thorasmerienda  = '00:00:00';
    
    if(date('Y-m-d', strtotime($entrada)) ==  date('Y-m-d', strtotime($salida))):

        $lunchs =  obtenerComidasDiarias($cedula,$fecha);

    else:

        if($salida < $fin100):

            $lunchs =  obtenerComidasDiarias($cedula,$fecha);
        
        else:

            $lunchs =  obtenerComidasDiarias($cedula,$salida);

        endif;
    
    endif;
    
    if($lunchs):

        foreach($lunchs as $item):

            if($dia == 6 || $dia == 7):

                $ingreso =  date('H:i:s', strtotime($entrada));

                if($ingreso < $item['hora'] && $item['id_sys_rrhh_comedor'] == 1):

                    $entradadesayuno = date('H:i:s', strtotime($item['hora']));

                    $id_lunch = obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                    if($sdesayuno != "00:00:00"){

                        $thorasdesayuno = getTotalhorascomedor($entradadesayuno, $sdesayuno);

                        if($thorasdesayuno > $id_lunch['tiempo_descuento']){
    
                            $tiempo += floatval(HorasToDecimal($thorasdesayuno));
    
                        }else{
    
                            $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));
    
                        }

                    }else{

                        $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                    }

                elseif($item['id_sys_rrhh_comedor'] == 2):

                    $entradaalmuerzo = date('H:i:s', strtotime($item['hora']));

                    $id_lunch = obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                    if($salmuerzo != "00:00:00"){

                        $thorasalmuerzo = getTotalhorascomedor($entradaalmuerzo, $salmuerzo);

                        if($thorasalmuerzo > $id_lunch['tiempo_descuento']){
    
                            $tiempo += floatval(HorasToDecimal($thorasalmuerzo));
    
                        }else{
    
                            $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));
    
                        }

                    }else{

                        $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                    }

                elseif($item['id_sys_rrhh_comedor'] == 3):

                    $entradamerienda = date('H:i:s', strtotime($item['hora']));
                    
                    $id_lunch = obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                    if($smerienda != "00:00:00"){

                        $thorasmerienda = getTotalhorascomedor($entradamerienda, $smerienda);

                        if($thorasmerienda > $id_lunch['tiempo_descuento']){
    
                            $tiempo += floatval(HorasToDecimal($thorasmerienda));
    
                        }else{
    
                            $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));
    
                        }

                    }else{

                        $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                    }
                
                endif;

            else:

                if($item['id_sys_rrhh_comedor'] == 1):

                    $entradadesayuno = date('H:i:s', strtotime($item['hora']));

                    $id_lunch = obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                    if($sdesayuno != "00:00:00"){

                        $thorasdesayuno = getTotalhorascomedor($entradadesayuno, $sdesayuno);

                        if($thorasdesayuno > $id_lunch['tiempo_descuento']){
    
                            $tiempo += floatval(HorasToDecimal($thorasdesayuno));
    
                        }else{
    
                            $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));
    
                        }

                    }else{

                        $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                    }

                elseif($item['id_sys_rrhh_comedor'] == 2):

                    $entradaalmuerzo = date('H:i:s', strtotime($item['hora']));

                    $id_lunch = obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                    if($salmuerzo != "00:00:00"){

                        $thorasalmuerzo = getTotalhorascomedor($entradaalmuerzo, $salmuerzo);

                        if($thorasalmuerzo > $id_lunch['tiempo_descuento']){
    
                            $tiempo += floatval(HorasToDecimal($thorasalmuerzo));
    
                        }else{
    
                            $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));
    
                        }

                    }else{

                        $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                    }

                elseif($item['id_sys_rrhh_comedor'] == 3):

                    $entradamerienda = date('H:i:s', strtotime($item['hora']));
                    
                    $id_lunch = obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                    if($smerienda != "00:00:00"){

                        $thorasmerienda = getTotalhorascomedor($entradamerienda, $smerienda);

                        if($thorasmerienda > $id_lunch['tiempo_descuento']){
    
                            $tiempo += floatval(HorasToDecimal($thorasmerienda));
    
                        }else{
    
                            $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));
    
                        }

                    }else{

                        $tiempo += floatval(HorasToDecimal($id_lunch['tiempo_descuento']));

                    }
                
                endif;

            endif;

        endforeach;
    
    endif;
        
    //Encaso de estar agendado calcular horas del 100%
    $agendamiento = obtenerAgendamiento($cedula, $fecha);

    $diaLibre = getDiaLibre($cedula, $fecha);
       
        //Horas del 100% horarios Lunea Vienres 
        if($feriado != null ):
        
            if($lunchs):

                return restarMinutosLunch(DecimaltoHoras(number_format($tiempo, 2, '.', '')),getTotalhoras($entrada, $salida));

            else:

                return getTotalhoras($entrada, $salida);

            endif;
            
        // Horas del 100% sin agendamiento
        elseif($diaLibre):
                
            if($dia == 6  || $dia == 7):
                    
                if($lunchs):
                    return restarMinutosLunch(DecimaltoHoras(number_format($tiempo, 2, '.', '')),getTotalhoras($entrada, $salida));
                else:
                    return getTotalhoras($entrada, $salida);
                endif;
                
            else:
            
                if($lunchs):
                    return restarMinutosLunch(DecimaltoHoras(number_format($tiempo, 2, '.', '')),getTotalhoras($entrada, $salida));
                else:
                    return getTotalhoras($entrada, $salida);
                endif;
                
            endif;
            
        //Horas del 100% sábados y domingos 
        elseif($dia == 6  || $dia == 7):
        
            if($agendamiento):

                $diasalida =  date("N", strtotime($salida));
            
                if($lunchs):

                    if($diasalida == 6 || $diasalida == 7):
                        
                        return restarMinutosLunch(DecimaltoHoras(number_format($tiempo, 2, '.', '')),getTotalhoras($horanormal, $horatrabas));

                    endif;

                else:
                    
                    if($diasalida == 6 || $diasalida == 7):

                        return getTotalhoras($horanormal, $horatrabas);

                    endif;

                endif;
            
            else:
                
                if($lunchs):
                    return restarMinutosLunch(DecimaltoHoras(number_format($tiempo, 2, '.', '')),getTotalhoras($entrada, $salida));
                else:
                    return getTotalhoras($entrada, $salida);
                endif;
                
            endif;
            
                
        else:
        
                //Horas del 100% de lunes a Viernes 00:00:00  y 06:00:00 
                /*   $calcular100 = true;
        
                if($agendamiento):
                
                    $calcular100 = false;
                    
                endif;
            
                
                if($calcular100 == true):
                */
            
            if($horatrabas > $horanormal && date('Y-m-d', strtotime($entrada)) !=  date('Y-m-d', strtotime($salida))):
                
                if($hsalida < $fin100):
                            
                    if($hsalida < $ini100):
                                
                        if($salida > $fin100):
                                    
                            return  getTotalhoras($ini100, $fin100);
                                    
                        elseif($salida < $fin100):
                                    
                            return getTotalhoras($ini100, $salida);
                                    
                        endif;
                            
                    else: 
                                
                        if($salida > $fin100):
                                    
                            return  getTotalhoras($hsalida, $fin100);
                                
                        elseif($salida < $fin100):
                                        
                            return restarMinutosLunch(DecimaltoHoras(number_format($tiempo, 2, '.', '')),getTotalhoras($hsalida, $salida));
                                        
                        endif;
                                    
                    endif;
                            
                endif;
                        
            endif;
                                   
        endif;
                 
        //endif;
        
    return "00:00:00";
    
}
//calculo de horas 
function getTotalhoras($entrada, $salida){
        
    if ( $salida > $entrada): 
        
        $fechaini = date('Y-m-d', strtotime($entrada));
        $fechafin = date('Y-m-d', strtotime($salida));

        $arrayentrada =  explode(':', date('H:i:s', strtotime($entrada) ));
        $arraysalida =  explode(':', date('H:i:s', strtotime($salida) ));
                     
                     
        if ($fechaini ==  $fechafin):
                     
            $totalhoras = 0;
            $totalmin   = 0;
                     
            $horaentra  = $arrayentrada[0];
            $minentrada = $arrayentrada[1];
            $horasalida = $arraysalida[0];
            $minsalida  = $arraysalida[1];
                     
            $minentrada = 60 - $minentrada;
            $horaentra++;
                     
            $totalmin = $minentrada + $minsalida;
                     
            if ($totalmin >= 60):
                     
                $totalmin   = $totalmin - 60;
                $horasalida++;
                     
            endif;
                     
                $totalhoras =  $horasalida - $horaentra;
                     
                return str_pad($totalhoras, 2, "0", STR_PAD_LEFT).':'.str_pad($totalmin, 2, "0", STR_PAD_LEFT).':00';
        
            else:
              
                $totalhoras = 0;
                $totalmin   = 0;
                     
                $horaentra  = $arrayentrada[0];
                $minentrada = $arrayentrada[1];
                $horasalida = $arraysalida[0];
                $minsalida  = $arraysalida[1];
                            
                $minentrada = 60 - $minentrada;
                $horaentra++;
                $horaentra = 24 - $horaentra;
                $totalmin = $minentrada + $minsalida;
                     
                if ($totalmin >= 60):
                     
                    $totalmin   = $totalmin - 60;
                    $horasalida++;
                         
                endif;
                     
                    $totalhoras = $horasalida + $horaentra;
                     
                    return str_pad($totalhoras, 2, "0", STR_PAD_LEFT).':'.str_pad($totalmin, 2, "0", STR_PAD_LEFT).':00';
             
            endif;
              
        endif;
         
    return "00:00:00";
       
}  
//rendordear minutos para el calculo de horas extras     
function getRendonminutos($hora){
        
        //revisar redondeo de horas extras 
       
        $array   = explode(':', $hora);
        $min     = intval($array[1]);
        $horas   = $array[0];
        
        if ($min >= 0){
            
            if ($min < 15){
                
                return $horas.':00:00';
            }
            elseif ($min >= 15 && $min < 30){
                
                return $horas.':15:00';
            }
            /*elseif ($min >= 10 && $min < 15){
                
                return $horas.':10:00';
            }*/
            elseif ($min >= 30 && $min < 45){
                
                return $horas.':30:00';
            }
            /*elseif ($min >= 20 && $min < 25){
                
                return $horas.':20:00';
            }*/
            elseif ($min >= 45 && $min < 60){
                
                return $horas.':45:00';
            }
            /*elseif ($min >= 30 && $min < 35){
                
                return $horas.':30:00';
            }*/
            //elseif ($min >= 35 && $min < 45){
                
            //    return $horas.':40:00';
            //}
            /*elseif ($min >= 40 && $min < 45){
                
                return $horas.':40:00';
            }*/
            //elseif ($min >= 45 && $min < 55){
                
            //    return $horas.':50:00';
            //}
            /*elseif ($min >= 50 && $min < 55){
                
                return $horas.':50:00';
            }*/
            //elseif ($min >= 55 && $min < 60){
                
            //    return $horas.':55:00';
            //}
          
        }else{
            
            if($horas > 0):
                 $horas = intval($horas) - 1;
                 return str_pad($horas, 2, "0", STR_PAD_LEFT).':45:00';
            endif;
            
        }
        return $horas.':00:00';
        
    }
//horas 25 aprobadas 
function get25estado($cedula, $fechamarcacion){
         
        $model = SysRrhhMarcacionesEmpleados::find()
        ->where(['id_sys_rrhh_cedula'=> $cedula])
        ->andWhere(['fecha_laboral'=> $fechamarcacion])
        ->andWhere(['pago25'=> 1])->one();
         
        if($model){
            return true;
        }
        return false;
}
//horas valor 25
function get25valor($cedula, $fechamarcacion){        
        $model = SysRrhhMarcacionesEmpleados::find()->select("horas25")
        ->where(['id_sys_rrhh_cedula'=> $cedula])
        ->andWhere(['fecha_laboral'=> $fechamarcacion])
        ->andWhere(['pago25'=> 1])->scalar();
        
        return $model;
}
function get50estado($cedula, $fechamarcacion){
        
        $model = SysRrhhMarcacionesEmpleados::find()
        ->where(['id_sys_rrhh_cedula'=> $cedula])
        ->andWhere(['fecha_laboral'=> $fechamarcacion])
        ->andWhere(['pago50'=> 1])->one();
        
        if($model){
            return true;
        }
        return false;
}
function get50valor($cedula, $fechamarcacion){
        
        $model = SysRrhhMarcacionesEmpleados::find()->select("horas50")
        ->where(['id_sys_rrhh_cedula'=> $cedula])
        ->andWhere(['fecha_laboral'=> $fechamarcacion])
        ->andWhere(['pago50'=> 1])->scalar();
        
         return $model;
    }
function get100estado($cedula, $fechamarcacion){
        
        $model = SysRrhhMarcacionesEmpleados::find()
        ->where(['id_sys_rrhh_cedula'=> $cedula])
        ->andWhere(['fecha_laboral'=> $fechamarcacion])
        ->andWhere(['pago100'=> 1])->one();
        
        if($model){
            return true;
        }
        return false;
    }
function get100valor($cedula, $fechamarcacion){
        
         $model = SysRrhhMarcacionesEmpleados::find()->select("horas100")
        ->where(['id_sys_rrhh_cedula'=> $cedula])
        ->andWhere(['fecha_laboral'=> $fechamarcacion])
        ->andWhere(['pago100'=> 1])->scalar();
        
         return $model;
    }
//inicio de la jornadar
function getInicioJornada($tipo, $cedula, $fechaentrada){
        
         $datos    = [];  
        
         $fecha    = date('Y-m-d', strtotime($fechaentrada));
         
         $entrada  = date('H:i:s', strtotime($fechaentrada));
        
         $permiso  = (new \yii\db\Query())
         ->select('*')
         ->from("sys_rrhh_empleados_permisos")
         ->where("'{$fecha}' >= fecha_ini")
         ->andwhere("'{$fecha}' <= fecha_fin")
         ->andwhere("id_sys_rrhh_cedula like '%{$cedula}%'")
         ->all(SysRrhhEmpleadosPermisos::getDb());
         
         if(count($permiso)== 0){
             
                if($tipo == 'N'){
                    
                    $datos =  (new \yii\db\Query())
                    ->select(["abs(datediff(minute,hora_inicio, '{$entrada}')) min","cast(hora_inicio as varchar(8)) entrada"])
                    ->from("sys_rrhh_empleados_horario horemp")
                    ->innerJoin("sys_rrhh_horario_cab hor","horemp.id_sys_rrhh_horario  = hor.id_sys_rrhh_horario_cab")
                    ->where("id_sys_rrhh_cedula ='{$cedula}'")
                    ->orderby("min")
                    ->all(SysRrhhCuadrillasJornadasMov::getDb());
               
                      
            
                }else{
                    
                     $datos =  (new \yii\db\Query())
                    ->select(["cast(hora_inicio as varchar(8)) entrada"])
                    ->from("sys_rrhh_cuadrillas_jornadas_mov mov")
                    ->innerJoin("sys_rrhh_horario_cab hor","mov.id_sys_rrhh_jornada  = hor.id_sys_rrhh_horario_cab")
                    ->where("fecha_laboral = '{$fecha}'")
                    ->andwhere("id_sys_rrhh_cedula ='{$cedula}'")
                    ->orderBy("fecha_registro desc")
                    ->all(SysRrhhCuadrillasJornadasMov::getDb());
                    
                }
         }
       
        if (count($datos)> 0){
            
            return date('Y-m-d', strtotime($fechaentrada)). ' '.$datos[0]['entrada'];
            
        }else{
            
            return $fechaentrada;
        }
       
        
    }
function getFinJornada($tipo, $cedula, $fechasalida){
        
        $datos    = [];
        
        $fecha    = date('Y-m-d', strtotime($fechasalida));
        
        $salida   = date('H:i:s', strtotime($fechasalida));
        
        $permiso  = (new \yii\db\Query())
        ->select('*')
        ->from("sys_rrhh_empleados_permisos")
        ->where("'{$fecha}' >= fecha_ini")
        ->andwhere("'{$fecha}' <= fecha_fin")
        ->andwhere("id_sys_rrhh_cedula like '%{$cedula}%'")
        ->all(SysRrhhEmpleadosPermisos::getDb());
        
        if(count($permiso)== 0){
            
            if($tipo == 'N'){
                
                $datos =  (new \yii\db\Query())
                ->select(["abs(datediff(minute ,hora_fin, '{$salida}')) min","cast(hora_fin as varchar(8)) salida"])
                ->from("sys_rrhh_empleados_horario horemp")
                ->innerJoin("sys_rrhh_horario_cab hor","horemp.id_sys_rrhh_horario  = hor.id_sys_rrhh_horario_cab")
                ->where("id_sys_rrhh_cedula ='{$cedula}'")
                 ->orderby("min")
                ->all(SysRrhhCuadrillasJornadasMov::getDb());

            }else{
                
                $datos =  (new \yii\db\Query())
                ->select(["cast(hora_fin as varchar(8)) salida"])
                ->from("sys_rrhh_cuadrillas_jornadas_mov mov")
                ->innerJoin("sys_rrhh_horario_cab hor","mov.id_sys_rrhh_jornada  = hor.id_sys_rrhh_horario_cab")
                ->where("fecha_laboral = '{$fecha}'")
                ->andwhere("id_sys_rrhh_cedula ='{$cedula}'")
                ->orderBy("fecha_registro desc")
                ->all(SysRrhhCuadrillasJornadasMov::getDb());
            }
        }
        
        if (count($datos)> 0){
            
            return date('Y-m-d', strtotime($fechasalida)). ' '.$datos[0]['salida'];
            
        }
            
            return $fechasalida;
        
    }
function getHorasJornada($cedula,$fechaentrada, $entrada, $salida){
        
    $datos = [];   
                
    $entrada = date('H:i:s', strtotime($entrada));
    $salida =  date('H:i:s', strtotime($salida));
                            
    $datos =  (new \yii\db\Query())
    ->select(["cast(hora_inicio as varchar(5)) inicio","cast(hora_fin as varchar(5)) fin"])
    ->from("sys_rrhh_cuadrillas_jornadas_mov mov")
    ->innerJoin("sys_rrhh_horario_cab hor","mov.id_sys_rrhh_jornada  = hor.id_sys_rrhh_horario_cab")
    ->where("fecha_laboral = '{$fechaentrada}'")
    ->andwhere("id_sys_rrhh_cedula ='{$cedula}'")
    ->orderBy("fecha_registro desc")
    ->all(SysRrhhCuadrillasJornadasMov::getDb());
                
    if(count($datos) == 0) {
                    
        $datos =  (new \yii\db\Query())
        ->select(["abs(datediff(minute ,hora_inicio, '{$entrada}')) + abs(datediff(minute ,hora_fin, '{$salida}')) min","cast(hora_inicio as varchar(5)) inicio","cast(hora_fin as varchar(5)) fin"])
        ->from("sys_rrhh_empleados_horario horemp")
        ->innerJoin("sys_rrhh_horario_cab hor","horemp.id_sys_rrhh_horario  = hor.id_sys_rrhh_horario_cab")
        ->where("id_sys_rrhh_cedula ='{$cedula}'")
        ->orderby("min")
        ->all(SysRrhhCuadrillasJornadasMov::getDb());
        
    }
                 
    if(count($datos) > 0 ){
                     
        $arrayinicio =  explode(':',$datos[0]['inicio']); 
        $arrayfin    =  explode(':',$datos[0]['fin']); 
        $hora_ini    =  intval($arrayinicio[0]);
        $min_ini     =  intval($arrayinicio[1]);
        $hora_fin    =  intval($arrayfin[0]);
        $min_fin     =  intval($arrayfin[1]);
                     
        if($hora_fin > $hora_ini):
                     
            $min_ini = 60 - $min_ini;
            $hora_ini++;
                         
            $totalmin = $min_ini + $min_fin;
                         
                if ($totalmin >= 60):
                         
                    $totalmin   = $totalmin - 60;
                    $hora_fin++;
                         
                endif;
                         
            $totalhoras =  $hora_fin - $hora_ini;
                         
            return str_pad($totalhoras, 2, "0", STR_PAD_LEFT).':'.str_pad($totalmin, 2, "0", STR_PAD_LEFT).':00';
                    
        else:
                       
            $min_ini = 60 - $min_ini;
            $hora_ini++;
            $hora_ini = 24 - $hora_ini;
            $totalmin = $min_ini + $min_fin;
                         
            if ($totalmin >= 60):
                         
                $totalmin   = $totalmin - 60;
                $hora_fin++;
                         
            endif;
                         
                $totalhoras = $hora_ini + $hora_fin;
                         
                    return str_pad($totalhoras, 2, "0", STR_PAD_LEFT).':'.str_pad($totalmin, 2, "0", STR_PAD_LEFT).':00';
                      
                endif;
                     
            }
                 
    return "00:00:00";
                 
}
function gethora_normal($cedula,$fechaentrada, $entrada, $salida){
        
       /*  $datos = [];
        
        $entrada = date('H:i:s', strtotime($entrada));
        $salida =  date('H:i:s', strtotime($salida));
        
        
        $datos =  (new \yii\db\Query())
        ->select(['hora_normales'])
        ->from("sys_rrhh_cuadrillas_jornadas_mov mov")
        ->innerJoin("sys_rrhh_horario_cab hor","mov.id_sys_rrhh_jornada  = hor.id_sys_rrhh_horario_cab")
        ->where("fecha_laboral = '{$fechaentrada}'")
        ->andwhere("id_sys_rrhh_cedula ='{$cedula}'")
        ->orderBy("fecha_registro desc")
        ->all(SysRrhhCuadrillasJornadasMov::getDb());
       
        if(count($datos) > 0 ) {
            
            return $datos[0]['hora_normales'];
            
        }else{
            
             $datos =  (new \yii\db\Query())
            ->select(["abs(datediff(minute ,hora_inicio, '{$entrada}')) + abs(datediff(minute ,hora_fin, '{$salida}')) min","hora_normales"])
            ->from("sys_rrhh_empleados_horario horemp")
            ->innerJoin("sys_rrhh_horario_cab hor","horemp.id_sys_rrhh_horario  = hor.id_sys_rrhh_horario_cab")
            ->where("id_sys_rrhh_cedula ='{$cedula}'")
            ->orderby("min")
            ->all(SysRrhhCuadrillasJornadasMov::getDb());
            
            if(count($datos) > 0 ){
                
                return $datos[0]['hora_normales'];
                
            }
            
            return '08:30:00';
        }
        */
      
         /*if ($cedula == '1310801707') :
            return '06:30:00';
         endif;
         */
        return '08:00:00';
    }
function getSalidaLaboral($entrada, $salida, $horanormal){
        
    $arrayentrada   =  explode(':', date('H:i:s', strtotime($entrada)));
    $arraynormal    =  explode(':', date('H:i:s', strtotime($horanormal)));
            
            $horas          = intval($arrayentrada[0]) + intval($arraynormal[0]);
            $mintutos       = intval($arraynormal[1]) + intval($arrayentrada[1]);
        
            if($horas >= 24):
            
                $horas   = $horas - 24;
                $fecha  = date('Y-m-d', strtotime($salida));
            
                if($mintutos >= 60) :
                
                       $horas ++;
                       $mintutos = $mintutos - 60;
                
                endif;
            
        
            else:
        
                if($mintutos >= 60) :
                
                    $horas ++;
                    $mintutos = $mintutos - 60;
                
                endif;
                
                if($horas >= 24 ):
                    $horas   = $horas - 24;
                    $fecha  = date('Y-m-d', strtotime($salida));
                else:
                     $fecha  = date('Y-m-d', strtotime($entrada));
                endif;
                
          endif;
        
       return  $fecha.' '.str_pad($horas, 2, "0", STR_PAD_LEFT).':'.str_pad($mintutos, 2, "0", STR_PAD_LEFT);
          
    }
function getMinutoslunch($cedula,$fechaentrada, $entrada, $salida){
        
 
            if($entrada > $salida ):    
            
                    $datos = [];
                    
                    $entrada = date('H:i:s', strtotime($entrada));
                    $salida =  date('H:i:s', strtotime($salida));
                    
                    $datos =  (new \yii\db\Query())
                    ->select(['hora_lunch'])
                    ->from("sys_rrhh_cuadrillas_jornadas_mov mov")
                    ->innerJoin("sys_rrhh_horario_cab hor","mov.id_sys_rrhh_jornada  = hor.id_sys_rrhh_horario_cab")
                    ->where("fecha_laboral = '{$fechaentrada}'")
                    ->andwhere("id_sys_rrhh_cedula ='{$cedula}'")
                    ->orderBy("fecha_registro desc")
                    ->all(SysRrhhCuadrillasJornadasMov::getDb());
                    
                    if(count($datos) > 0 ) {
                        
                        $array =  explode(':',  $datos[0]['hora_lunch']); 
               
                        return intval($array[1]);
                        
                    }else{
                        
                        $datos =  (new \yii\db\Query())
                        ->select(["abs(datediff(minute ,hora_inicio, '{$entrada}')) + abs(datediff(minute ,hora_fin, '{$salida}')) min","hora_lunch"])
                        ->from("sys_rrhh_empleados_horario horemp")
                        ->innerJoin("sys_rrhh_horario_cab hor","horemp.id_sys_rrhh_horario  = hor.id_sys_rrhh_horario_cab")
                        ->where("id_sys_rrhh_cedula ='{$cedula}'")
                        ->orderby("min")
                        ->all(SysRrhhCuadrillasJornadasMov::getDb());
                        
                        if(count($datos) > 0 ){
                            
                           // $array =  explode(':',  $datos[0]['hora_lunch']);
                           // return intval($array[1]);
                           
                            return 1;
         
                        }
                    }
                    
                endif;
                
            return 0;
    }
function orderMultiDimensionalArray ($toOrderArray, $field, $inverse = false) {
        $position = array();
        $newRow = array();
        foreach ($toOrderArray as $key => $row) {
            $position[$key]  = $row[$field];
            $newRow[$key] = $row;
        }
        //$position = array_unique($position);
        if ($inverse) {
            arsort($position);
        }
        else {
            asort($position);
        }
        $returnArray = array();
        foreach ($position as $key => $pos) {
            $returnArray[] = $newRow[$key];
        }
        
        return $returnArray;
    }
//obtener tipo de jornada
function  ObtenerTipoJornada($empresa, $cedula, $fecha){
   
    
        $agendamiento = (new \yii\db\Query())->select(["*"])
        ->from("sys_rrhh_cuadrillas_jornadas_mov")
        ->where("id_sys_rrhh_cedula  = '{$cedula}'")
        ->andwhere("fecha_laboral = '{$fecha}'")
        ->one(SysRrhhEmpleados::getDb());
          
        if($agendamiento):
            return 'R';
        endif;
         return 'N';
        
         
        
    }
function suma_horas($hora1,$hora2){
        
        $hora1=explode(":",$hora1);
        $hora2=explode(":",$hora2);
        $temp=0;
       
        //sumo minutos
        $minutos=(int)$hora1[1]+(int)$hora2[1]+$temp;
        $temp=0;
        while($minutos>=60){
            $minutos=$minutos-60;
            $temp++;
        }
        
        //sumo horas
        $horas=(int)$hora1[0]+(int)$hora2[0]+$temp;
        
        if($horas<10):
            $horas= '0'.$horas;
        endif;
            
        if($minutos<10):
            $minutos= '0'.$minutos;
         endif;
                            
       $sum_hrs = $horas.':'.$minutos.':00';
                    
       return ($sum_hrs);
                    
    }
function restar_horas($hora1,$hora2){
        
        $temp1 = explode(":",$hora1);
        $temp_h1 = (int)$temp1[0];
        $temp_m1 = (int)$temp1[1];
        $temp_s1 = (int)$temp1[2];
        $temp2 = explode(":",$hora2);
        $temp_h2 = (int)$temp2[0];
        $temp_m2 = (int)$temp2[1];
        $temp_s2 = (int)$temp2[2];
        
        // si $hora2 es mayor que la $hora1, invierto
        if( $temp_h1 < $temp_h2 ){
            $temp  = $hora1;
            $hora1 = $hora2;
            $hora2 = $temp;
        }
        /* si $hora2 es igual $hora1 y los minutos de
         $hora2 son mayor que los de $hora1, invierto*/
        elseif( $temp_h1 == $temp_h2 && $temp_m1 < $temp_m2){
            $temp  = $hora1;
            $hora1 = $hora2;
            $hora2 = $temp;
        }
        /* horas y minutos iguales, si los segundos de
         $hora2 son mayores que los de $hora1,invierto*/
        elseif( $temp_h1 == $temp_h2 && $temp_m1 == $temp_m2 && $temp_s1 < $temp_s2){
            $temp  = $hora1;
            $hora1 = $hora2;
            $hora2 = $temp;
        }
        
        $hora1=explode(":",$hora1);
        $hora2=explode(":",$hora2);
        $temp_horas = 0;
        $temp_minutos = 0;
        
    
            //resto minutos
            $minutos=0;
            
            if( (int)$hora1[1] < (int)$hora2[1] ){
                $temp_horas = -1;
                $minutos = ( (int)$hora1[1] + 60 ) - (int)$hora2[1] + $temp_minutos;
            }
            else
                $minutos =  (int)$hora1[1] - (int)$hora2[1] + $temp_minutos;
                
                //resto horas
                $horas = (int)$hora1[0]  - (int)$hora2[0] + $temp_horas;
                
                if($horas<10)
                    $horas= '0'.$horas;
                    
                    if($minutos<10)
                        $minutos= '0'.$minutos;
                    
                            
                            $rst_hrs = $horas.':'.$minutos.':00';
                            
                            return ($rst_hrs);
                            
    }

    function getTotalhorascomedor($entrada, $salida){
        
        if ( $salida > $entrada): 
            
            $fechaini = date('Y-m-d', strtotime($entrada));
            $fechafin = date('Y-m-d', strtotime($salida));
    
            $arrayentrada =  explode(':', date('H:i:s', strtotime($entrada) ));
            $arraysalida =  explode(':', date('H:i:s', strtotime($salida) ));
                         
                         
            if ($fechaini ==  $fechafin):
                         
                $totalhoras = 0;
                $totalmin   = 0;
                         
                $horaentra  = $arrayentrada[0];
                $minentrada = $arrayentrada[1];
                $segentrada = $arrayentrada[2];
                $horasalida = $arraysalida[0];
                $minsalida  = $arraysalida[1];
                $segsalida  = $arraysalida[2];
                         
                if($horaentra != $horasalida):
                
                    $minentrada = 60 - $minentrada;
                    $horaentra++;
            
                    $totalmin = $minentrada + $minsalida;
            
                    $segentrada = 60 - $segentrada;
                        
                    $totalseg = $segentrada + $segsalida;
    
                else:
    
                    if($minentrada != $minsalida):
    
                        $minentrada = 60 - $minentrada;
                        $horaentra++;
            
                        $totalmin = $minentrada + $minsalida;
            
                        $segentrada = 60 - $segentrada;
                        
                        $totalseg = $segentrada + $segsalida;
                    else:
    
                        $minentrada = 60 - $minentrada;
                        $horaentra++;
            
                        $totalmin = $minentrada + $minsalida;
                        
                        $totalseg = $segsalida - $segentrada;
    
                    endif;
    
                endif;
    
                if ($totalseg >= 60):
    
                    $totalseg = $totalseg - 60;
    
                    if ($totalmin >= 60):
                         
                        $totalmin   = $totalmin - 60;
                        $horasalida++;
                             
                    endif;
    
                    $totalmin++;
    
                endif;
                         
                if ($totalmin >= 60):
                         
                    $totalmin   = $totalmin - 60;
                    $horasalida++;
                         
                endif;
                         
                    $totalhoras =  $horasalida - $horaentra;
                         
                    return str_pad($totalhoras, 2, "0", STR_PAD_LEFT).':'.str_pad($totalmin, 2, "0", STR_PAD_LEFT).':'.str_pad($totalseg, 2, "0", STR_PAD_LEFT).'';
             
            endif;
                  
        endif;
             
        return "00:00:00";
           
    }
function getFechaIngreso($id_sys_rrhh_cedula){
        
      return  (new \yii\db\Query())
        ->select(["fecha_ingreso"])
        ->from("sys_rrhh_empleados_contratos")
        ->where("id_sys_rrhh_cedula ='{$id_sys_rrhh_cedula}'")
        ->orderby("fecha_ingreso desc")
        ->one(SysRrhhEmpleados::getDb());
        
    }
//Decimal a Horas 
function DecimaltoHoras($valor){
        
    if($valor > 0):
    
        $array = explode('.', trim($valor));
        $h     = floatval($array[0]);
        $m     = floatval($array[1]) * 0.60;
        return  str_pad($h, 2, "0", STR_PAD_LEFT).':'.str_pad(intval(round($m)), 2, "0", STR_PAD_LEFT).':00';
   
        
    else:
    
        return "00:00:00";
    
    endif;
}

function DecimaltoHorasExtras($valor){
        
    if($valor > 0):
    
        $array = explode('.', trim($valor));
        $h     = floatval($array[0]);
        if(empty($array[1])){
            $m = floatval(0) * 0.60;
        }else{
            if($array[1]<10){
                $tiempo = str_pad($array[1],2,'0', STR_PAD_RIGHT);
                $m = floatval($tiempo) * 0.60;
            }else{
                $m = floatval($array[1]) * 0.60;
            }
            
        }
        //$m     = floatval(50) * 0.60; 
        return  str_pad($h, 2, "0", STR_PAD_LEFT).':'.str_pad(intval(round($m)), 2, "0", STR_PAD_LEFT).':00';
        //return $valor;
    else:
    
        return "00:00:00";
    
    endif;
}
function obtenerAgendamiento($cedula, $fecha_laboral){
    
    return (new \yii\db\Query())
    ->select(["*"])
    ->from("[dbo].[agendamiento]")
    ->where("id_sys_rrhh_cedula ='{$cedula}'")
    ->andWhere("fecha_laboral = '{$fecha_laboral}'")
    ->orderBy("fecha_registro desc")
    ->one(SysRrhhEmpleados::getDb());
    
    
}
function HorasToDecimal($hora){
    
    $array = explode(':', trim($hora));
    $h     = floatval($array[0]);
    $m     = floatval($array[1]);
    
    if ($h != 0 || $m != 0 ):
    
        if($m > 0 ):
            
            $m = $m/60;
        endif;
        
        return $h+$m;
        
    else: 
      return 0;
    endif;
}
function getDiaLibre($cedula, $fecha_laboral){
    
    return  (new \yii\db\Query())->select(["*"])
    ->from("sys_rrhh_cuadrillas_jornadas_mov")
    ->where("id_sys_rrhh_cedula  = '{$cedula}'")
    ->andWhere("id_sys_rrhh_jornada is null")
    ->andwhere("fecha_laboral = '{$fecha_laboral}'")
    ->one(SysRrhhEmpleados::getDb());
   
}

function getDataSolicitudHoras($id){
    
    $db =  $_SESSION['db'];
    
    return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDetalleSolicitudHorasExtras]  @id_solicitud = '{$id}'")->queryAll(); 
}

function ObtenerDatosMarcacion($cedula,$fecha){
        
    $db =  $_SESSION['db'];
    
    return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerAsistenciaLaboralEmpleadosXCedula]  @fecha_ini = '{$fecha}',  @fecha_fin = '{$fecha}', @cedula = '$cedula'")->queryOne(); 
    
}

function ObtenerDatosMarcacionHoras($cedula,$fecha){
        
    $db =  $_SESSION['db'];
    
    return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerAsistenciaLaboralEmpleadosXCedulaHoras]  @fecha_ini = '{$fecha}',  @fecha_fin = '{$fecha}', @cedula = '$cedula'")->queryOne(); 
    
}

function obtenerDatosMarcacionySolicitudes($fecha_ini, $fecha_fin, $id_sys_adm_area, $id_sys_adm_departamento,$cedula){
       
    $db =  $_SESSION['db'];
    
    if (($id_sys_adm_area != null) && ( $id_sys_adm_departamento == null)) :
    
        return  Yii::$app->$db->createCommand("exec [dbo].[ObtenerAsistenciaLaboralEmpleadosXCedulaHoras] @fecha_ini = '{$fecha_ini}', @fecha_fin = '{$fecha_fin}', @id_sys_adm_area = {$id_sys_adm_area},@cedula = '{$cedula}'")->queryAll();
   
    elseif (($id_sys_adm_area != null) && ( $id_sys_adm_departamento != null)):
    
        return  Yii::$app->$db->createCommand("exec [dbo].[ObtenerAsistenciaLaboralEmpleadosXCedulaHoras] @fecha_ini = '{$fecha_ini}', @fecha_fin = '{$fecha_fin}', @id_sys_adm_area = {$id_sys_adm_area}, @id_sys_adm_departamento = {$id_sys_adm_departamento},@cedula = '{$cedula}'")->queryAll();
    else:
    
        return  Yii::$app->$db->createCommand("exec [dbo].[ObtenerAsistenciaLaboralEmpleadosXCedulaHoras] @fecha_ini = '{$fecha_ini}', @fecha_fin = '{$fecha_fin}',@cedula = '{$cedula}'")->queryAll();
    
    endif;
   
}

function ObtenerDepartamentoEmpleado($cedula){
    $db =  $_SESSION['db'];

    $empleado = SysRrhhEmpleados::find()->Where(['id_sys_rrhh_cedula'=> $cedula])->one();

    $cargo = $empleado['id_sys_adm_cargo'];
    
    return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDepartamentoXEmpleado]  @cargo = '$cargo'")->queryOne(); 
}

function obtenerComidasDiarias($cedula,$fecha){
    
    $db =  $_SESSION['db'];
   
    return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerComidasDiariasEmpleado] @cedula = '{$cedula}', @fecha = '{$fecha}'")->queryAll(); 

}

function obtenerTiempoLunch($idcomedor){
    
    $db =  $_SESSION['db'];
   
    return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerTiempoComedor] @idcomedor = '{$idcomedor}'")->queryOne(); 

}

?>