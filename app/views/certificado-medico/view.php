<?php

use app\models\SysRrhhEmpleados;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */

$this->title = 'Certificado Médico';
$this->params['breadcrumbs'][] = ['label' => 'Certificado Médicos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-med-certficado-medico-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_sys_rrhh_cedula',
            [
                'label'=>'Nombres',
                'attribute'=>'nombres',
                'value'=> function($model){
                    
                    $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                    return $empleado->nombres;
                    
                } ,
            ],    
            [
                    'label'=>'Entidad Emisora',
                    'attribute'=>'entidad_emisora',
                    'value'=> function($model){
                    
                    $entidad = 'OTROS';
                    
                    if($model->entidad_emisora == 'I'):
                    
                        $entidad = 'IESS';
                    
                    elseif($model->entidad_emisora == 'M'):
                    
                        $entidad = 'MPS';
                    
                    elseif($model->entidad_emisora == 'P'):
                    
                        $entidad = 'PARTICULAR';
                    
                    endif;
                    
                    
                    return $entidad;
                    
                    } ,
            ],
            [
                'label'=>'Tipo',
                'attribute'=>'tipo',
                'value'=> function($model){
                return $model->tipo == 'H' ? 'HORAS' : 'DIAS';
                } ,
            ],
            'fecha_ini',
            'fecha_fin',
        
            'diagnostico',
        
            //'usuario_creacion',
            //'fecha_creacion',
            //'usuario_actualizacion',
            //'fecha_actualizacion',
            //'anulado',
        ],
    ]) ?>

</div>
