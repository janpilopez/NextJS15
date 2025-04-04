<?php 
use app\models\SysRrhhEmpleados;
use yii\bootstrap\Html;

$haberes    = 0;
$descuentos = 0;
$neto       = 0;

$datos = (new \yii\db\Query())->select(['concepto','tipo','unidad','cantidad','rol_mov.valor'])
  ->from('sys_rrhh_empleados_rol_mov rol_mov')
  ->innerjoin('sys_rrhh_conceptos conceptos', 'rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
  ->where("rol_mov.id_sys_empresa = '{$id_sys_empresa}'")
  ->andWhere("rol_mov.anio= '{$anio}'")
  ->andwhere("rol_mov.mes='{$mes}'")
  ->andwhere("rol_mov.periodo='{$periodo}'")
   ->andwhere("rol_mov.id_sys_rrhh_cedula='{$id_sys_rrhh_cedula}'")
  ->orderby('orden')
  ->all(SysRrhhEmpleados::getDb());
                                            
 if(count($datos)> 0):  
 
 
 $empleado =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=>  $id_sys_rrhh_cedula])->andwhere(['id_sys_empresa'=> $id_sys_empresa])->one();
 
 ?>
 <div class ="detalle-rol">
	 <table id="tabledetallerol" class="table table-bordered" style="background-color: white; font-size: 11px; width: 100%;">
         <tr class = "info">
              <td colspan= "4"><b><?= Html::a($empleado->nombres, ['asistencia', 'anio'=> $anio, 'mes'=> $mes, 'periodo'=> $periodo, 'id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula, 'id_sys_empresa'=> $id_sys_empresa ], ['target'=> '_blank'] )?> </b></td>
          </tr>  
          <tr class = "info">
              <th width = "40%">Concepto</th>
              <th width = "20%">Tipo</th>
              <th width = "20%">Cantidad</th>
              <th width = "20%">Valor</th>
          </tr>    
         <?php 
         
          $haberes    = 0;
          $descuentos =  0;
                                        
          foreach ($datos as $data):
                                       
         ?>    
           <tr>
               <td><?= $data['concepto']?></td>
               <td><?= $data['tipo'] == 'I'? 'Haber': 'Descuento'?></td>
               <td><?= $data['cantidad']?></td>
               <td><?= number_format($data['valor'], 2, '.', ',') ?></td>
               <?php 
                  if($data['tipo'] == 'I'):
                  
                      $haberes     = $haberes +  $data['valor'];    
                  
                  else:    
                  
                      $descuentos = $descuentos +  $data['valor'];   
                  
                  endif;
                ?>
           </tr>
        <?php endforeach;?>   
          <tr>
             <?php 
             
             if($periodo != 90):
                    $neto =  $haberes - $descuentos;
             else:
                    $neto =  $haberes + $descuentos;
             endif;
             
             ?>
            <td colspan="3" style="text-align: right"><b>Neto Recibir:</b></td><td><?= number_format($neto, 2, '.', ',')?></td>
          </tr>  
    </table>
 </div>
 <?php endif;?>