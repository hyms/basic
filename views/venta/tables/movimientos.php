<?php
    use kartik\grid\GridView;

    $columns = [
        [
            'class' => '\kartik\grid\SerialColumn'
        ],
        [
            'header'=>'Factura',
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>["S/Factura","C/Factura"],
            'filterWidgetOptions'=>[
                'pluginOptions'=>['allowClear'=>true],
            ],
            'filterInputOptions'=>['placeholder'=>'Seleccionar'],
            'format'=>'raw',
            'value'=>function($model) {
                return (($model->cfSF) ? "C/Factura" : "S/Factura");
            },
            'attribute'=>'cfSF',
        ],
        [
            'header'=>'Estado',
            'value'=>function($model) {
                return ($model->estado!=1)?(($model->estado==0)?"Cancelado":(($model->estado<0)?"Anulado":"Deuda")):"";
            },
            'attribute'=>'estado',
            'format'=>'raw',
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>["-1"=>"Anulado","0"=>"Cancelado","1"=>"Pendiente","2"=>"Deuda"],
            'filterWidgetOptions'=>[
                'pluginOptions'=>['allowClear'=>true],
            ],
            'filterInputOptions'=>['placeholder'=>'Seleccionar'],
        ],
        [
            'header'=>'Correlativo',
            'attribute'=>'correlativo',
        ],
        [
            'header'=>'Cliente',
            'attribute'=>function($model)
            {
                if(empty($model->fkIdCliente))
                    return "";
                return $model->fkIdCliente->nombreNegocio;
            },
            'pageSummary'=>'Total',
        ],
        [
            'header'=>'Monto de la Venta',
            'value'=>'montoVenta',
            'pageSummary'=>true,
        ],
        [
            'header'=>'Monto Pagado',
            'value'=>function($model){
                $tmp = $model->fkIdMovimientoCaja;
                if(empty($model->fkIdMovimientoCaja))
                    return "";
                $montos = \app\models\MovimientoCaja::find()
                    ->where(['idParent'=>$model->fk_idMovimientoCaja])->all();
                $monto = $tmp->monto;
                foreach($montos as $item)
                {
                    $monto += $item->monto;
                }
                return $monto;
            },
            'pageSummary'=>true,
            //'filter'=>Html::activeTextField($ordendes, 'montoPagado',array("class"=>"form-control input-sm")),
        ],
        [
            'header'=>'Fecha Venta',
            'attribute'=>'fechaCobro',
        ],
        [
            'class'=>'kartik\grid\ActionColumn',
            'template'=>'{print}',
            'buttons'=>[
                /*'update'=>function($url,$model){
                    $options = array_merge([
                                               //'class'=>'btn btn-success',
                                               'data-original-title'=>'Modificar',
                                               'data-toggle'=>'tooltip',
                                               'title'=>''
                                           ]);
                    $url = Url::to(['venta/venta','id'=>$model->idOrdenCTP]);
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                },*/
                'print'=>function($url,$model){
                    $options = array_merge([
                                               //'class'=>'btn btn-success',
                                               'data-original-title'=>'Imprimir',
                                               'data-toggle'=>'tooltip',
                                               'title'=>''
                                           ]);
                    $url = \yii\helpers\Url::to(['venta/print','op'=>'orden','id'=>$model->idOrdenCTP]);
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-print"></span>', $url, $options);
                },
            ]
        ],
    ];

    echo GridView::widget([
                              'dataProvider' => $ordenes,
                              'filterModel' => $search,
                              'columns' => $columns,
                              // set your toolbar
                              'toolbar' =>  [
                                  '{export}',
                                  '{toggleData}',
                              ],
                              // set export properties
                              'export' => [
                                  'fontAwesome' => true,
                                  'target'=>GridView::TARGET_BLANK,
                              ],
                              // parameters from the demo form
                              'bordered' => true,
                              'condensed' => true,
                              'responsive' => true,
                              'hover' => true,
                              'showPageSummary' => true,
                              'panel' => [
                                  'type' => GridView::TYPE_DEFAULT,
                                  'heading' => 'ordenes',
                              ],
                              'exportConfig' => [
                                  GridView::EXCEL => [
                                      'label' => 'Excel',
                                      'filename' => 'Reporte Movimientos',
                                      'alertMsg' => 'El EXCEL se generara para la descarga.',
                                      'showPageSummary' => true,
                                  ],
                                  GridView::PDF => [
                                      'label' => 'PDF',
                                      'filename' => 'Reporte Movimientos',
                                      'alertMsg' => 'El PDF se generara para la descarga.',
                                      'config' => [
                                          'format' => 'Letter-L',
                                          'marginTop' => 5,
                                          'marginBottom' => 5,
                                          'marginLeft' => 5,
                                          'marginRight' => 5,
                                      ]
                                  ],
                              ],
                          ]);
