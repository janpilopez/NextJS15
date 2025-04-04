<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhFormaPago */

$this->title = 'Formas de Pagos';
$this->params['breadcrumbs'][] = ['label' => 'Formas de Pagos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Registrar';
?>
<div class="sys-rrhh-forma-pago-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
      
    ]) ?>

</div>
