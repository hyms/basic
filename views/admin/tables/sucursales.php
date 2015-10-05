<?php
    use kartik\grid\GridView;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\Pjax;

    echo Html::beginTag('div',['class'=>'panel panel-default']);

    echo Html::tag('div', Html::tag('strong','Sucursales',['class'=>'panel-title']), ['class'=>'panel-heading']);

    echo Html::beginTag('div',['class'=>'panel-body']);
    echo Html::button('Nueva Sucursal',
                      [
                          'class'=>'btn btn-default',
                          'onclick' => 'clickmodal("' . Url::to(['admin/config','op'=>'sucursal','frm'=>true]) . '","Nueva Sucursal")',
                          'data-toggle' => "modal",
                          'data-target' => "#modal"
                      ]);
    echo Html::endTag('div');

    $columns = [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'header'=>'Nombre',
            'value'=>'nombre',
        ],
        [
            'header'=>'Descripcion',
            'value'=>'descripcion',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template'=>'{update}',
            'buttons'=>[
                'update'=>function($url,$model) {
                    return Html::a(Html::tag('span', '',
                                             [
                                                 'class' => 'glyphicon glyphicon-import',
                                             ]
                                   ) . ' Modificar',
                                   "#",
                                   [
                                       'onclick'     => 'clickmodal("' . Url::to(['admin/config', 'op' => 'sucursal', 'id' => $model->idSucursal, 'frm' => true]) . '","Modificar Sucursal")',
                                       'data-toggle' => "modal",
                                       'data-target' => "#modal"
                                   ]);
                },
            ]
        ],
    ];

    Pjax::begin(['id' => 'sucursales']);
    echo GridView::widget([
                              'dataProvider'=> $sucursales,
                              //'filterModel' => $search,
                              'columns' => $columns,
                              'responsive'=>true,
                              'condensed'=>true,
                              'hover'=>true,
                              'bordered'=>false,
                          ]);
    Pjax::end();
    echo Html::endTag('div');
