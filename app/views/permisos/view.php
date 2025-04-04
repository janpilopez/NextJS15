<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\SysRrhhPermisos;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosPermisos */

$this->title = 'Empleados Permisos';
$this->params['breadcrumbs'][] = ['label' => 'Empleados Permisos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-empleados-permisos-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_sys_rrhh_empleados_permiso',
            'id_sys_rrhh_cedula',
            [
                
                'attribute'=>'id_sys_rrhh_permiso',
                'value'=> function($model){
                    
                
                  $permiso = SysRrhhPermisos::find()->where(['id_sys_rrhh_permiso'=> $model->id_sys_rrhh_permiso])->one();
                  
                  return $permiso['permiso'];
                
                
                }
                ],
            'fecha_ini',
            
            [
                
                'attribute'=>'hora_ini',
                'value'=> function($model){
                    return date('H:i', strtotime($model->hora_ini));
                }
                ],
                
            'fecha_fin',
            [
                
                'attribute'=>'hora_fin',
                'value'=> function($model){
                    return date('H:i', strtotime($model->hora_fin));
                }
                
            ],
        //  'transaccion_usuario',
           // 'estado',
           
            [
                'label'=> 'Tipo Jornada',
                'attribute'=>'tipo',
                'value'=> function($model){
                return  $model->tipo == 'C' ? 'Completa' : 'Parcial';
                }
                ],
         
                'comentario',
          
        ],
    ]) ?>

    <?php if($model->estado_permiso == 'P'):?>
   		<?= Html::a('Aprobar Permiso', ['aprobar',  'id_sys_rrhh_empleados_permiso'=>$model->id_sys_rrhh_empleados_permiso,'id_sys_empresa'=> $model->id_sys_empresa], ['class' => 'btn btn-success']) ?>
        <?= Html::a('No Aprobar Permiso', ['anular',  'id_sys_rrhh_empleados_permiso'=>$model->id_sys_rrhh_empleados_permiso,'id_sys_empresa'=> $model->id_sys_empresa], ['class' => 'btn btn-danger']) ?>
   	<?php endif;?>
</div>
