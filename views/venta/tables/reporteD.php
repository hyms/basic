<?php
    use kartik\grid\GridView;

    $columns = [
        [
            'header'=>'Usuario',
            'attribute'=>function($model){
                if(empty($model->fkIdUserV))
                    return "";
                return $model->fkIdUserV->nombre;
            },
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
            //'pageSummary'=>'Total',
        ],
        [
            'header'=>'Responsable',
            'attribute'=>'responsable',
        ],
        [
            'header'=>'Trabajo',
            'format'=>'raw',
            'value'=>function($model){
                $trabajos = $model->ordenDetalles;
                $string ="";
                foreach($trabajos as $key=>$trabajo)
                {
                    $string =$string."<p>".$trabajo->trabajo."</p>";
                }
                return $string;
            },
        ],
        [
            'header'=>'Placa',
            'format'=>'raw',
            'value'=>function($model){
                $trabajos = $model->ordenDetalles;
                $string ="";
                foreach($trabajos as $key=>$trabajo)
                {
                    $string =$string."<p>".$trabajo->fkIdProductoStock->fkIdProducto->formato."</p>";
                }
                return $string;
            },
        ],
        [
            'header'=>'Costo',
            'format'=>'raw',
            'value'=>function($model){
                $trabajos = $model->ordenDetalles;
                $string ="";
                foreach($trabajos as $key=>$trabajo)
                {
                    $string =$string."<p>".$trabajo->costo."</p>";
                }
                return $string;
            },
        ],
        [
            'header'=>'Q/Arm',
            'format'=>'raw',
            'value'=>function($model){
                $trabajos = $model->ordenDetalles;
                $string ="";
                foreach($trabajos as $key=>$trabajo)
                {
                    $string =$string."<p>".$trabajo->adicional."</p>";
                }
                return $string;
            },
        ],
        [
            'header'=>'Total',
            'format'=>'raw',
            'value'=>function($model){
                $trabajos = $model->ordenDetalles;
                $string ="";
                foreach($trabajos as $key=>$trabajo)
                {
                    $string =$string."<p>".$trabajo->total."</p>";
                }
                return $string;
            },
        ],
        [
            'header'=>'Desc',
            'attribute'=>function($model){
                return (empty($model->montoDescuento))?0:$model->montoDescuento;
            },
        ],
        [
            'header'=>'Cobrar',
            'attribute'=>'montoVenta',
            'pageSummary'=>true,
        ],
        [
            'header'=>'Cancelado',
            'attribute'=>function($model) use($fechaStart,$fechaEnd) {
                $pagos = \app\models\MovimientoCaja::find()
                    ->where(['idParent' => $model->fk_idMovimientoCaja])
                    ->andWhere(['tipoMovimiento' => 0])
                    ->andWhere(['between', 'time', $fechaStart . ' 00:00:00', $fechaEnd . ' 23:59:59'])
                    ->andWhere(['not between', 'time', date("Y-m-d",strtotime($model->fkIdMovimientoCaja->time)) . ' 00:00:00', date("Y-m-d",strtotime($model->fkIdMovimientoCaja->time)) . ' 23:59:59'])
                    ->all();
                $total = 0;
                foreach ($pagos as $pago) {
                    $total += $pago->monto;
                }

                return $total;
            },
            'pageSummary'=>true,
        ],
        [
            'header'=>'Saldo',
            'attribute'=>function($model) use ($fechaStart,$fechaEnd){
                $pagos = \app\models\MovimientoCaja::find()
                    ->where(['idParent' => $model->fk_idMovimientoCaja])
                    ->andWhere(['between', 'time', $fechaStart . ' 00:00:00', $fechaEnd . ' 23:59:59'])
                    ->andWhere(['tipoMovimiento' => 0])
                    ->all();
                $total = 0;
                foreach ($pagos as $pago) {
                    $total += $pago->monto;
                }
                return ($model->montoVenta-($model->fkIdMovimientoCaja->monto+$total));
            },
            'pageSummary'=>true,
        ],
        [
            'header'=>'Factura',
            'attribute'=>function($model){
                return (empty($model->factura))?"":$model->factura;
            },
        ],
        [
            'header'=>'Obs',
            'value'=>function($model) {
                return ($model->observacionesCaja);
            },
        ],
        [
            'header'=>'Fecha',
            'format'=>'raw',
            'value'=>function($model) use ($fechaStart,$fechaEnd){
                $pagos = \app\models\MovimientoCaja::find()
                    ->where(['idParent' => $model->fk_idMovimientoCaja])
                    ->andWhere(['tipoMovimiento' => 0])
                    ->andWhere(['between', 'time', $fechaStart . ' 00:00:00', $fechaEnd . ' 23:59:59'])
                    ->andWhere(['not between', 'time', date("Y-m-d",strtotime($model->fkIdMovimientoCaja->time)) . ' 00:00:00', date("Y-m-d",strtotime($model->fkIdMovimientoCaja->time)) . ' 23:59:59'])
                    ->all();
                $fecha = "";
                foreach ($pagos as $pago) {
                    $fecha = $fecha . "<p>" .date("Y-m-d/H:i", strtotime($pago->time)) . "</p>";
                }
                return $fecha;
            },
        ],
        [
            'header'=>'Monto',
            'format'=>'raw',
            'value'=>function($model) use ($fechaStart,$fechaEnd){
                $pagos = \app\models\MovimientoCaja::find()
                    ->where(['idParent' => $model->fk_idMovimientoCaja])
                    ->andWhere(['tipoMovimiento' => 0])
                    ->andWhere(['between', 'time', $fechaStart . ' 00:00:00', $fechaEnd . ' 23:59:59'])
                    ->andWhere(['not between', 'time', date("Y-m-d",strtotime($model->fkIdMovimientoCaja->time)) . ' 00:00:00', date("Y-m-d",strtotime($model->fkIdMovimientoCaja->time)) . ' 23:59:59'])
                    ->all();
                $fecha = "";
                foreach ($pagos as $pago) {
                    $fecha = $fecha . "<p>" . $pago->monto . "</p>";
                }
                return $fecha;
            },
        ],
    ];
    echo GridView::widget([
                              'dataProvider' => $data,
                              //'filterModel' => $search,
                              'columns' => $columns,
                              // set your toolbar
                              'toolbar' =>  [
                                  '{export}',
                                  '{toggleData}',
                              ],
                              // set export properties
                              'export' => [
                                  'fontAwesome' => true
                              ],
                              // parameters from the demo form
                              'bordered' => true,
                              'condensed' => true,
                              'responsive' => true,
                              'hover' => true,
                              'showPageSummary' => true,
                              'panel' => [
                                  'type' => GridView::TYPE_PRIMARY,
                                  'heading' => 'ordenes',
                              ],
                              'exportConfig' => [
                                  GridView::EXCEL => [
                                      'label' => 'Excel',
                                      'filename' => 'Reporte',
                                      'alertMsg' => 'El EXCEL se generara para la descarga.',
                                  ],
                                  GridView::PDF => [
                                      'label' => 'PDF',
                                      'filename' => 'Reporte',
                                      'alertMsg' => 'El PDF se generara para la descarga.',
                                  ],
                              ],
                          ]);