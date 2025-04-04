<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\SysRrhhEmpleados;
use app\models\SysMedTipoMotivo;

/* @var $this yii\web\View */

$this->title = 'Turno # '.$model->numero;
$this->params['breadcrumbs'][] = ['label' => 'Turnos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-med-turno-medico-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
        'id_sys_rrhh_cedula',
         [
          'attribute'=>'nombres',
          'value'=> function($model){
              
                $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                if($empleado):
                    return $empleado->nombres;
                else: 
                    return  "S/N";
                endif;
                
                }
         ],
         [
              'attribute'=>'id_sys_med_tipo_motivo',
              'value'=> function($model){
              
              $motivo = SysMedTipoMotivo::find()->where(['id'=> $model->id_sys_med_tipo_motivo])->one();
              return $motivo->tipo;
              
              }
          ],
          'fecha',
          [
              'attribute'=>'ini_atencion',
              'value'=> function($model){
                 return date('H:i:s', strtotime($model->ini_atencion));
              }
           ],
           [
               'attribute'=>'fin_atencion',
               'value'=> function($model){
                  if($model->fin_atencion != null):
                    return date('H:i:s', strtotime($model->fin_atencion));
                  else:
                    return "00:00:00";
                  endif;
               }
           ],
           [
               'attribute'=>'comentario',
               'value'=> function($model){
                  if($model->comentario == null):
                     return "S/N";
                  endif;
               }
           ],
         
        ],
    ]) ?>

</div>
