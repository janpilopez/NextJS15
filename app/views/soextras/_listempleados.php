
<?php
use app\models\SysRrhhEmpleadosLunch;
use yii\helpers\Html;
$holgura =  15;
//listado de funciones de calculos
echo $this->render('funciones');


$meses =  Yii::$app->params['meses'];
$dias =   Yii::$app->params['dias'];

class FilterColumn {
    private $colName;
    
    function __construct($colName) {
        $this->colName = $colName;
    }
    
    function getValues($i) {
        return $i[$this->colName];
    }
}

class FilterData {
    private $colName;
    private $value;
    
    function __construct($colName, $value) {
        $this->colName = $colName;
        $this->value = $value;
    }
    
    function getFilter($i) {
        return $i[$this->colName] == $this->value;
    }
}

?>
<style>
#tablemodal_filter > label{
    width:100%;
}
.table__no-margin{
    margin-bottom: 0px;
}
</style>
<script>
function seleccionTr(tr){
	
	 let ant = [...document.querySelectorAll("#tablemodal > tbody > tr input[type='checkbox']")]
	 ant.map((m)=>{
		 
		    if(m.checked){
			 
		    	m.parentElement.parentElement.classList.add("seleccion")
		 	  // tr.classList.remove("seleccion")
		    }else{
			    
		    	m.parentElement.parentElement.classList.remove("seleccion")
			    }
			})
}
$('#tablemodal').DataTable( {
    "scrollY":        "60vh",
    "scrollX":        "100%",
    "scrollCollapse": true,
    "paging":         false,
    "ordering": false,
})

$("#tablemodal > tbody > tr").bind('dblclick', function(){
	seleccionTr(this)
	//selectOrden(this.firstElementChild.innerHTML.trim())
})

$("#tablemodal > tbody > tr").bind('click', function(){
	seleccionTr(this)
})

$("#btnCancelar").on('click', (e)=>{
	e.preventDefault()
	$("#modalempleados").modal("hide")
})

$("#btnAgregar").on('click', (e)=>{
	e.preventDefault()
	
	 let ant = [...document.querySelectorAll("#tablemodal > tbody > tr input[type='checkbox']")]
	 ant.map((m)=>{
		 
		    if(m.checked){

		    	insertarfila(m.id,m.value,m.attributes.h50.value,m.attributes.h100.value,m.attributes.p50.value,m.attributes.p100.value);
		    	m.parentElement.parentElement.classList.add("seleccion")
				
		    }
		    
			})
		
	$("#modalempleados").modal("hide")
})

$(document).ready(function() {
   $("#select").click(function() {
      $(':checkbox').not(this).prop('checked', this.checked);
	  	let ant = [...document.querySelectorAll("#tablemodal > tbody > tr input[type='checkbox']")]
		ant.map((m)=>{
				
			if(m.checked){
		    	m.parentElement.parentElement.classList.add("seleccion")
		 	  // tr.classList.remove("seleccion")
		    }else{
		    	m.parentElement.parentElement.classList.remove("seleccion")
			}		
		})
   });
});

</script>
<div class="row">
	<div class="col-xs-12">
		<table id="tablemodal" class="table table-bordered table-condensed table__no-margin" style="width:100%; background-color: white; font-size: 11px;">
    		<thead>
				<th colspan= "4" class="text-right">
					Marcar / Desmarcar
				</th>
					<th><input type="checkbox" id="select">
				</th>
                <tr>
                   <th>Cédula</th>
                   <th>Nombres</th>
				   <th>H50(R)</th>
				   <th>H100(R)</th>
                   <th>Seleccionar</tr>
                </tr>
        	</thead>
        	<tbody>
        		<?php 
					
					$data =  array_unique(array_map(array(new FilterColumn("fecha"), 'getValues'), $datos));
					$cont = 0;
					$totalHoras = 0;
					
					foreach ($data as $index => $fecha):
						
						$fechaAsistencia = array_filter($datos, array(new FilterData("fecha", $fecha), 'getFilter'));
                  
						$dataAsistencia =  array_unique(array_map(array(new FilterColumn("id_sys_rrhh_cedula"), 'getValues'), $fechaAsistencia));
						
						foreach ($dataAsistencia as $index2 => $id_sys_rrhh_cedula):
                  
							$entrada        = '00:00:00';
							$salida         = '00:00:00';
							$thoras         = '00:00:00';
							$h25            = '00:00:00';
							$h50            = '00:00:00';
							$h100           = '00:00:00';
							$entrada_desayuno  = '00:00:00';
							$salidadesayuno   = '00:00:00';
							$entrada_almuerzo  = '00:00:00';
							$salidaalmuerzo   = '00:00:00';
							$entrada_merienda  = '00:00:00';
							$salidamerienda   = '00:00:00';
							$p50            = 0;
							$p100           = 0;
							$contador = 0;
                          	$contador2 = 0;

							$marcaciones = array_filter($fechaAsistencia, array(new FilterData("id_sys_rrhh_cedula", $id_sys_rrhh_cedula), 'getFilter'));
							
							if($marcaciones[$index2]['fecha_marcacion'] != null):

								foreach ($marcaciones as $marcacion):

									if($marcacion['tipo'] == 'E'):

										$contador += 1;

									endif;

									if($marcacion['tipo'] == 'S'):

										$contador += 1;

									endif;

									if($marcacion['tipo'] == 'SD'):

										$contador2 += 1;

									endif;

									if($marcacion['tipo'] == 'SA'):

										$contador2 += 1;

									endif;

									if($marcacion['tipo'] == 'SM'):

										$contador2 += 1;

									endif;

								endforeach;
                                    
								if($contador == 1 or $contador > 2):
								
									if($marcaciones[$index2]['permiso'] != null):
									
										 $observacion = $marcaciones[$index2]['permiso'];
									
									else:
										 $errorMarcacion = true;
										 $observacion = 'Error Marcación. El usuario tiene una o mas marcaciones';
									
									endif;
								
								endif;

								if($contador2 >= 1):

									if($contador == 0):

										if($marcaciones[$index2]['agendamiento'] == 0):
										
											$observacion = 'DIA LIBRE';
														
										elseif($marcaciones[$index2]['permiso'] != null):
										
											$observacion = $marcaciones[$index2]['permiso'];
										
										elseif($marcaciones[$index2]['vacaciones'] == 1):
										
											$observacion = 'GOZO DE VACACIONES';
										
										elseif ($marcaciones[$index2]['feriado'] != null):
										
										
											$observacion = $marcaciones[$index2]['feriado'];
										
										else :
										
										
										
											if($marcaciones[$index2]['agendamiento'] > 0 ):
											
												$observacion = 'FALTA';
											
											else:
											
												$dia =  date("N", strtotime($marcaciones[$index2]['fecha']));
												
												if($dia >= 1 && $dia <= 5):
												
													$observacion = 'FALTA';
												
												else:
												
													$observacion = 'DIA DE DESCANZO';
												
												endif;
												
											endif;

										endif;
									
									endif;

								endif;
								
								foreach ($marcaciones as $marcacion):

									$comidas = SysRrhhEmpleadosLunch::find()->where(['id_sys_rrhh_cedula'=>$id_sys_rrhh_cedula])->andWhere(['fecha'=>$marcacion['fecha_marcacion']])->all();
                                            
                                    if($comidas):

                                        foreach($comidas as $index3 => $item):

                                            if($item->id_sys_rrhh_comedor == 1):

                                                $entrada_desayuno = date('H:i:s', strtotime($item->hora));
                                                                
                                            elseif($item->id_sys_rrhh_comedor == 2):

                                                $entrada_almuerzo = date('H:i:s', strtotime($item->hora));
                                                                
                                            else:
                                                                    
                                                $entrada_merienda = date('H:i:s', strtotime($item->hora));

                                            endif;
                                                            
                                        endforeach;

                                    endif;
								
									if($marcacion['tipo'] == 'E'):
									
										$entrada =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));
										$fecha_ent = $marcacion['fecha_marcacion'];
										
									elseif($marcacion['tipo'] == 'S'):
										
										$salida  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));
										$fecha_sal = $marcacion['fecha_marcacion'];
											
									elseif($marcacion['tipo'] == 'SD'):
											
										$salidadesayuno  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));
				
									elseif($marcacion['tipo'] == 'SA'):
					
										$salidaalmuerzo  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));
				
									elseif($marcacion['tipo'] == 'SM'):
					
										$salidamerienda  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));
				
									endif;
								
								endforeach;
								
								if( $contador  == 2):
								
							   
											$thoras = getTotalhoras($fecha_ent, $fecha_sal);
										
											if($thoras != "00:00:00"):
											
													if($marcaciones[$index2]['permiso'] != null):
													
														 $observacion = $marcaciones[$index2]['permiso'];
													
													endif;
												
													//Calcular horas extras 
												   
													//Horas extras 50
													
													if($marcaciones[$index2]['h50'] > 0):
													
														 $h50  = DecimaltoHoras($marcaciones[$index2]['h50']);
													else:
														 $h50  = getRendonminutos(gethoras50($fecha_ent, $fecha_sal,$salidadesayuno,$salidaalmuerzo,$salidamerienda,$marcaciones[$index2]['id_sys_rrhh_cedula'], $marcaciones[$index2]['fecha'],$marcaciones[$index2]['feriado']));
													endif;
													
													//Horas extras 100
													if ($marcaciones[$index2]['h100'] > 0):
													
														 $h100  = DecimaltoHoras($marcaciones[$index2]['h100']);
													else:
													
														$h100  = getRendonminutos(gethoras100($fecha_ent, $fecha_sal,$salidadesayuno,$salidaalmuerzo,$salidamerienda,$marcaciones[$index2]['id_sys_rrhh_cedula'], $marcaciones[$index2]['fecha'], $marcaciones[$index2]['feriado'],$marcaciones[$index2]['agendamiento']));
														
													endif;
													
											else:
												
												$errorMarcacion = true;
												$observacion = 'Error Marcación. El usuario tiene una o mas marcaciones';
											
											endif;
									  
								endif;
								
							else:
							
									if($marcaciones[$index2]['agendamiento'] == 0):
									
										$observacion = 'DIA LIBRE';
													  
									elseif($marcaciones[$index2]['permiso'] != null):
									
										$observacion = $marcaciones[$index2]['permiso'];
									
									elseif($marcaciones[$index2]['vacaciones'] == 1):
									
										$observacion = 'GOZO DE VACACIONES';
									
									elseif ($marcaciones[$index2]['feriado'] != null):
									
									
										$observacion = $marcaciones[$index2]['feriado'];
									
									else :
									
									
									
										if($marcaciones[$index2]['agendamiento'] > 0 ):
										
											$observacion = 'FALTA';
										
										else:
										
											$dia =  date("N", strtotime($marcaciones[$index2]['fecha']));
											
											if($dia >= 1 && $dia <= 5):
											
												$observacion = 'FALTA';
											
											else:
											
												$observacion = 'DIA DE DESCANZO';
											
											endif;
										
										
										endif;
									
									
									endif;
							
							endif;
						if($h50 != '00:00:00' || $h100 != '00:00:00'):
				 ?>
        			<tr>
        				<td><?= $marcaciones[$index2]['id_sys_rrhh_cedula'] ?></td>
        				<td><?= $marcaciones[$index2]['nombres'] ?></td>
						<td><?= $h50 ?></td>
						<td><?= $h100 ?></td>
        				<td style ="text-align:center;"><input type="checkbox" id="<?= $marcaciones[$index2]['id_sys_rrhh_cedula'] ?>" value="<?= $marcaciones[$index2]['nombres'] ?>" h50="<?= $h50 ?>" h100="<?= $h100 ?>" p50="<?= $p50 ?>" p100="<?= $p100 ?>"></td>
        			</tr>
        		
				<?php endif;
				endforeach; ?>
				<?php endforeach; ?>
        	</tbody>
		</table>
	</div>
	<div class="col-xs-12 text-center">
		<?= Html::a("Aceptar", "", ['id'=>'btnAgregar','class'=>"btn btn-success", 'style'=>'width:45%']) ?>
		<?= Html::a("Cancelar", "", ['id'=>'btnCancelar','class'=>"btn btn-danger", 'style'=>'width:45%']) ?>
	</div>
</div>
