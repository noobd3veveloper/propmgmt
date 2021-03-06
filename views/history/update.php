<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\History */

//$this->title = 'Update History';
$this->title = 'Update History:' . $model->tenant->getFullName();
$this->params['breadcrumbs'][] = ['label' => 'Tenant', 'url' => ['tenant/view', 'id' => $model->tenantID]];

//$this->params['breadcrumbs'][] = ['label' => 'Histories', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->historyID, 'url' => ['view', 'id' => $model->historyID]];
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="history-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
