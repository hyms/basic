<?php
    use kartik\grid\GridView;

    $columns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        //'header'=>'Codigo',
        'value'=>'codigo',
        'attribute'=>'codigo',
        //'filter'=>CHtml::activeTextField($producto, 'codigo',array("class"=>"form-control")),
    ],
    [
        'header'=>'Cod Pers.',
        'value'=>'codigoPersonalizado',
        //'filter'=>CHtml::activeTextField($producto, 'codigoPersonalizado',array("class"=>"form-control")),
    ],
    [
        'header'=>'Material',
        'value'=>'material',
        //'filter'=>CHtml::activeDropDownList($producto,'material',CHtml::listData(Producto::model()->findAll(array('group'=>'material','select'=>'material')),'material','material'),array("class"=>"form-control",'empty'=>'')),
    ],
    [
        'header'=>'Detalle Producto',
        'value'=>function ($model) {
            return $model->formato . ' ' . $model->dimension;
        },
        //'filter'=>CHtml::activeTextField($producto, 'descripcion',array("class"=>"form-control")),
    ],
    //['class' => 'yii\grid\ActionColumn'],
];

echo GridView::widget([

    'dataProvider'=> $producto,
    'filterModel' => $search,
    'columns' => $columns,
    'pjax'=>true,
    //'headerRowOptions'=>['class'=>'kartik-sheet-style'],
    //'filterRowOptions'=>['class'=>'kartik-sheet-style'],
    'toolbar' =>  [
        '{export}',
        '{toggleData}',
    ],
    'export' => [
        'fontAwesome' => true
    ],
    //'bordered' => true,
    'condensed' => true,
    'hover'=>true,
    'panel' => [
        'type' => GridView::TYPE_DEFAULT,
        'heading' => 'Productos',
    ],
    'persistResize' => true,
    'exportConfig' => [
        GridView::EXCEL => [
            'label' => 'Excel',
            'filename' => 'Productos',
            'alertMsg' => 'El EXCEL se generara para la descarga.',
        ],
        GridView::PDF => [
            'label' => 'PDF',
            'filename' => 'Productos',
            'alertMsg' => 'El PDF se generara para la descarga.',
        ],
        ]
]);
