<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosPeriodoVacaciones */

$this->title = 'Vacaciones';
$this->params['breadcrumbs'][] = ['label' => 'Vacaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-empleados-periodo-vacaciones-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_sys_rrhh_empleados_periodo_vacaciones',
            'dias_disponibles',
            'dias_otorgados',
            'estado',
            'id_sys_rrhh_cedula',
            'id_sys_adm_periodo_vacaciones',
          
            
            
            
            
        ],
    ]) ?>

</div>
