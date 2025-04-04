<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhFeriados */

$this->title = 'Feriados';
$this->params['breadcrumbs'][] = ['label' => 'Feriados', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_feriado, 'url' => ['view', 'id_sys_rrhh_feriado' => $model->id_sys_rrhh_feriado, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-feriados-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
