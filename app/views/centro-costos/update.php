<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmCcostos */

$this->title = 'Centro de Costos';
$this->params['breadcrumbs'][] = ['label' => 'Centro de Costos', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_adm_ccosto, 'url' => ['view', 'id_sys_adm_ccosto' => $model->id_sys_adm_ccosto, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-adm-ccostos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
