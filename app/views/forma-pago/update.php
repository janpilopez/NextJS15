<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhFormaPago */

$this->title = 'Formas de Pagos';
$this->params['breadcrumbs'][] = ['label' => 'Forma de  Pagos', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_forma_pago, 'url' => ['view', 'id_sys_rrhh_forma_pago' => $model->id_sys_rrhh_forma_pago, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-forma-pago-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
