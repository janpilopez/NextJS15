

<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\SysRrhhCuadrillasJornadasMov;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhHorarioCab;
use app\models\SysRrhhJornadasCab;


/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhCuadrillasJornadasCab */

$this->title =  'Codigo '.$model->id_sys_rrhh_cuadrillas_jornadas_cab;
$this->params['breadcrumbs'][] = ['label' => 'Agendamiento Laboral', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="sys-rrhh-cuadrillas-jornadas-cab-view">
   <div class= 'row'> 
     <div class ='col-md-12'>
        <?php
       if ($model):
       
       $fechainicio = $model->fecha_inicio;
       $fechafin    = $model->fecha_fin;
       $contdias = 1 ;
      
       $dias =  array('Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo');
       
       for($i=$fechainicio;$i<=$fechafin;$i = date("Y-m-d", strtotime($i ."+ 1 days"))){ 
                    
            $contdias++;
        }
        
         $modeldetalle = SysRrhhCuadrillasJornadasMov::find()->select(['sys_rrhh_cuadrillas_jornadas_mov.id_sys_rrhh_cedula', 'nombres'])
        ->joinWith('sysRrhhEmpleado')
        ->andWhere(['id_sys_rrhh_cuadrillas_jornadas_cab'=> $model->id_sys_rrhh_cuadrillas_jornadas_cab])
        ->distinct()
        ->orderBy('nombres asc')
        ->all();
       
        
        ?>
         <table class = 'table table-bordered' style='font-size: 10px; padding:5px;'>
          <thead>
            <tr><th style="text-align: center;" colspan= "<?= $contdias ?>">Agendamiento semana # <?= $model->semana?> de <?= $model->fecha_inicio?> hasta <?= $model->fecha_fin?></th></tr>
             <tr>
             <th>Nombres</th>
                 <?php for($i=$fechainicio;$i<=$fechafin;$i = date("Y-m-d", strtotime($i ."+ 1 days"))){ ?> 
                  <th><?= $dias[date('N', strtotime($i)) -1].'-'.date('j', strtotime($i))?></th>    
                 <?php } ?>
            </tr> 
          </thead>
          <tbody>
          <?php foreach ($modeldetalle as $data ) : ?>
            <tr>
                
                <td><?= $data->sysRrhhEmpleado->nombres;?></td>
                
                 <?php for($i=$fechainicio;$i<=$fechafin;$i = date("Y-m-d", strtotime($i ."+ 1 days"))){  
                            
                     
                             //revisar si esta de vacaciones
                             $vacaciones =  [];
                             $vacaciones = (new \yii\db\Query())
                             ->select('*')
                             ->from("sys_rrhh_vacaciones_solicitud")
                             ->where("'{$i}' >= fecha_inicio and   '{$i}'<= fecha_fin")
                             ->andwhere("id_sys_rrhh_cedula = '{$data['id_sys_rrhh_cedula']}'")
                             ->all(SysRrhhCuadrillasJornadasMov::getDb());
                             
                             if(count($vacaciones) > 0 ):
                             
                                   $color = '#06FCE9';
                             
                             else:
                             
                                     $jornada = [];
                                     $jornada = (new \yii\db\Query())
                                     ->select('id_sys_rrhh_jornada')
                                     ->from("sys_rrhh_cuadrillas_jornadas_mov mov")
                                     ->where("fecha_laboral = '{$i}'")
                                     ->andwhere("mov.id_sys_rrhh_cedula = '{$data['id_sys_rrhh_cedula']}'")
                                     ->limit(1)
                                     ->orderBy("fecha_registro desc")
                                     ->all(SysRrhhCuadrillasJornadasMov::getDb());
                                       
                                     if ( count($jornada) > 0):
                                       
                                         $horario  = SysRrhhHorarioCab::find()
                                         ->where(['estado'=> 'A'])
                                         ->andWhere(['id_sys_rrhh_horario_cab'=>$jornada[0]['id_sys_rrhh_jornada']])
                                         ->andwhere(['id_sys_empresa'=> '001'])->one();
                                     
                                         if ($horario):
                                              $color =  $horario->color;
                                         else:
                                              $color = 'white';
                                         endif;
                                         
                                         
                                     else:
                                        $color = '#777416';
                                     endif;
                             
                             endif;
                                 
                           /*  $jornada = [];
                             $jornada = (new \yii\db\Query())
                             ->select('id_sys_rrhh_jornada')
                             ->from("sys_rrhh_cuadrillas_jornadas_mov mov")
                             ->where("fecha_laboral = '{$i}'")
                             ->andwhere("mov.id_sys_rrhh_cedula = '{$data['id_sys_rrhh_cedula']}'")
                              ->limit(1)
                              ->orderBy("fecha_registro desc")
                             ->all(SysRrhhCuadrillasJornadasMov::getDb());
                              
                               if ( count($jornada) > 0) {
                                    
                                  
                                    $horario  = SysRrhhHorarioCab::find()
                                   ->where(['estado'=> 'A'])
                                   ->andWhere(['id_sys_rrhh_horario_cab'=>$jornada[0]['id_sys_rrhh_jornada']])
                                   ->andwhere(['id_sys_empresa'=> '001'])->one();
                                   
                                   if($horario){
                                       $color =  $horario->color;
                                   }else{
                                       
                                       //revisar si esta de vacaciones 
                                       $vacaciones =  [];
                                       $vacaciones = (new \yii\db\Query())
                                       ->select('*')
                                       ->from("sys_rrhh_vacaciones_solicitud")
                                       ->where("'{$i}' >= fecha_inicio and   '{$i}'<= fecha_fin")
                                       ->andwhere("id_sys_rrhh_cedula = '{$data['id_sys_rrhh_cedula']}'")
                                       ->all(SysRrhhCuadrillasJornadasMov::getDb());
                                 
                                       if(count($vacaciones) > 0 ){
 
                                       }else{
                                           $color = 'white';
                                       }
                                   }

                                }else {
                                    
                                     //revisar si esta de vacaciones 
                                    $vacaciones =  [];
                                    $vacaciones = (new \yii\db\Query())
                                    ->select('*')
                                    ->from("sys_rrhh_vacaciones_solicitud")
                                    ->where("'{$i}' >= fecha_inicio and   '{$i}'<= fecha_fin")
                                    ->andwhere("id_sys_rrhh_cedula = '{$data['id_sys_rrhh_cedula']}'")
                                    ->all(SysRrhhCuadrillasJornadasMov::getDb());
                                    
                                    if(count($vacaciones) > 0 ){
                                        
                                        $color = '#06FCE9';
                                        
                                    }else{
                                        $color = 'white';
                                    }
                            
                                    
                                };
                                
                            */
                                
                          ?>
                           <td width = '88px;' bgcolor="<?php echo $color;?>"></td> 
                 <?php  } ?>
            </tr>
            <?php endforeach;?> 
          </tbody>
         </table>
          <?php endif; ?> 
     </div>
   </div>
   <br>
   <div class= 'row'>
      <div class= 'col-md-3'>
              <table width= '100%' class = 'table table-bordered' style='font-size: 10px;'>
                 <thead>
                    <tr><th>HORARIOS LABORALES</th></tr>
                 </thead>
                 <tbody> 
                   <?php 
                   
                   $horarios =SysRrhhHorarioCab::find()->where(['estado'=> 'A'])->all();
                   
                   foreach ($horarios as $horario){ ?> 
                    <tr><td bgcolor= "<?= $horario->color?>"><?= $horario->horario?></td></tr>
                   <?php  
                   }
                   ?>
                   <tr><td bgcolor= "#06FCE9">VACACIONES</td></tr>
                   <tr><td>LIBRE</td></tr>
                 </tbody>
             </table>
      </div>
   </div>
</div>
