<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmMandos */

$this->title = 'Niveles Organizacionales';
$this->params['breadcrumbs'][] = ['label' => 'Niveles Organizaciones', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_adm_mando, 'url' => ['view', 'id_sys_adm_mando' => $model->id_sys_adm_mando, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-adm-mandos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
