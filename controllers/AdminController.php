<?php

    namespace app\controllers;

    use app\components\SGCaja;
    use app\components\SGOperation;
    use app\components\SGProducto;
    use app\components\SGRecibo;
    use app\models\Caja;
    use app\models\CajaSearch;
    use app\models\Cliente;
    use app\models\ClienteSearch;
    use app\models\ImprentaTipoTrabajo;
    use app\models\MovimientoCaja;
    use app\models\MovimientoCajaSearchUserCaja;
    use app\models\OrdenCTP;
    use app\models\Producto;
    use app\models\ProductoSearch;
    use app\models\ProductoStock;
    use app\models\ProductoStockSearch;
    use app\models\Recibo;
    use app\models\ReciboSearch;
    use app\models\Sucursal;
    use app\models\SucursalSearch;
    use app\models\TipoCliente;
    use app\models\User;
    use app\models\UserSearch;
    use kartik\mpdf\Pdf;
    use Yii;
    use yii\data\ActiveDataProvider;
    use yii\data\ArrayDataProvider;
    use yii\filters\AccessControl;
    use yii\web\Controller;

    class AdminController extends Controller
    {
        public $layout = "admin";

        public function behaviors()
        {
            return [
                'access' => [
                    'class' => AccessControl::className(),
                    //'only'  => ['*'],
                    'rules' => [
                        [
                            //'actions' => ['*'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                /*'verbs'  => [
                    'class'   => VerbFilter::className(),
                    'actions' => [
                        'logout' => ['post'],
                    ],
                ],*/
            ];
        }

        public function actions()
        {
            return [
                'error' => [
                    'class' => 'yii\web\ErrorAction',
                ],
                'captcha' => [
                    'class' => 'yii\captcha\CaptchaAction',
                    'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                ],
            ];
        }

        public function init()
        {
            if (!empty(Yii::$app->user->identity)) {
                if(Yii::$app->user->identity->role !=1 && Yii::$app->user->identity->role !=2)
                {
                    return $this->redirect(Yii::$app->homeUrl);
                }
            }
            parent::init();
        }

        public function actionIndex()
        {
            return $this->render('index');
        }

        public function actionProducto()
        {
            $get = Yii::$app->request->get();
            if (isset($get['op'])) {
                switch ($get['op']) {
                    case "new":
                        $render = "new";
                        $producto = new Producto();
                        if ($producto->load(Yii::$app->request->post())) {
                            if ($producto->save()) {
                                SGProducto::initStock($producto->idProducto);
                                return $this->redirect(['admin/producto', 'op' => 'list']);
                            }
                        }
                        return $this->render('producto', ['r' => $render, 'producto' => $producto]);
                    case "list":
                        $search = new ProductoSearch();
                        $producto = $search->search(Yii::$app->request->queryParams);
                        return $this->render('producto', ['r' => "list", 'producto' => $producto, 'search' => $search]);
                    case "edit":
                        break;
                    case "del":
                        break;
                    case "add":
                        $submenu = Sucursal::find()->all();
                        if (isset($get['id'])) {
                            $search = new ProductoStockSearch();
                            $productos = $search->search(yii::$app->request->queryParams);
                            $productos->query
                                ->andWhere(['is', 'fk_idSucursal', null]);
                            if (isset($get['producto'])) {
                                SGProducto::initStock($get['producto'], $get['id']);
                            }
                            return $this->render('producto', ['r' => 'addRemove', 'productos' => $productos, 'search' => $search, 'idSucursal' => $get['id'], 'submenu' => $submenu]);
                        } else {
                            return $this->render('producto', ['r' => 'addRemove', 'submenu' => $submenu]);
                        }
                    case "rem":
                        $submenu = Sucursal::find()->all();
                        if (isset($get['producto']) && isset($get['id'])) {
                            $almacen = ProductoStock::findOne(['idProductoStock' => $get['producto']]);
                            if ($almacen->cantidad > 0) {
                                $almacen0 = ProductoStock::find()
                                    ->andWhere(['is', 'fk_idSucursal', null])
                                    ->andWhere(['fk_idProducto' => $almacen->fk_idProducto])
                                    ->one();
                                $almacen0->cantidad += $almacen->cantidad;
                                $almacen0->save();
                            }
                            if (!$almacen->delete()) {
                                $almacen->enable = false;
                            }
                            return $this->redirect(array('admin/producto', 'op' => 'add', 'id' => $get['id']));
                        } else {
                            return $this->render('producto', ['r' => 'addRemove', 'submenu' => $submenu]);
                        }
                        break;
                }
            }
            return $this->render('producto');
        }

        public function actionStock()
        {
            $get = Yii::$app->request->get();
            $submenu = Sucursal::find()->all();
            if (isset($get['op'])) {
                switch ($get['op']) {
                    case "list":
                        $search = new ProductoStockSearch;
                        $productos = $search->search(yii::$app->request->queryParams);
                        if ($get['id'] == 0) {
                            $productos->query
                                ->andWhere(['is', 'fk_idSucursal', null]);
                            $nombre = "Deposito";
                        } else {
                            $productos->query
                                ->andWhere(['fk_idSucursal' => $get['id']]);
                            foreach ($submenu as $item) {
                                if ($item->idSucursal == $get['id']) {
                                    $nombre = $item->nombre;
                                    break;
                                }
                            }
                        }
                        return $this->render('producto', ['r' => 'stocks', 'submenu' => $submenu, 'productos' => $productos, 'search' => $search, 'nombre' => $nombre]);
                        break;
                    case "add":
                        if (isset($get['id'])) {
                            $almacen = ProductoStock::findOne(['idProductoStock' => $get['id']]);
                            $deposito = null;
                            if (!empty($almacen->fkIdSucursal)) {
                                $deposito = ProductoStock::find()
                                    ->andWhere(['fk_idProducto' => $almacen->fk_idProducto])
                                    ->andWhere(['fk_idSucursal' => $almacen->fkIdSucursal->fk_idParent])
                                    ->one();
                            }
                            $model = SGProducto::movimientoStockCompra(null, $almacen, "Añadir a Stock", $deposito);
                            $post = Yii::$app->request->post();
                            if (isset($post['MovimientoStock'])) {
                                $model->attributes = $post['MovimientoStock'];
                                $almacen->cantidad += $model->cantidad;
                                if ($deposito != null) {
                                    $deposito->cantidad -= $model->cantidad;
                                }
                                if ($model->save()) {
                                    $almacen->save();
                                    if ($deposito != null) {
                                        $deposito->save();
                                    }
                                    echo "done";
                                    Yii::$app->end();
                                }
                            }

                            return $this->renderAjax('forms/add_reduce', array('model' => $model, 'almacen' => $almacen, 'deposito' => $deposito));
                        }
                        break;
                }
            }
            return $this->render('producto', ['r' => 'stocks', 'submenu' => $submenu]);
        }

        /*public function actionCosto()
        {
            $get     = Yii::$app->request->get();
            $submenu = Sucursal::find()->all();
            if (isset($get['op'])) {
                switch ($get['op']) {
                    case 'list':
                        if (isset($get['id'])) {
                            $placas = ProductoStock::findAll(['fk_idSucursal' => $get['id']]);
                            return $this->render('producto', ['r' => 'costo', 'submenu' => $submenu, 'placas' => $placas]);
                        }
                        break;
                    case 'precio':
                        if(isset($get['id'])) {
                            $placa        = ProductoStock::findOne(['idProductoStock' => $get['id']]);
                            $clienteTipos = TipoCliente::find()->all();
                            $cantidades   = precios::getCantidades();
                            //$cantidades   = precios::getDatoPrecioOrden('cantidad', $placa->idProductoStock);
                            $horas        = precios::getHoras();
                            //$horas        = precios::getDatoPrecioOrden('hora', $placa->idProductoStock);

                            $precio = new precios;
                            $precio->verify($placa->idProductoStock);
                            $post   = Yii::$app->request->post();
                            if (isset($post['PrecioProductoOrden'])) {
                                $precio->pullPrecios($post['PrecioProductoOrden']);
                                $precio->save();
                                //$precio->update($post['cantidad'], $cantidades, $post['hora'], $horas);
                                if ($precio->success) {
                                    echo "done";
                                    Yii::$app->end();
                                }
                            }
                            return $this->renderAjax('forms/preciosCTP', array('clienteTipos' => $clienteTipos, 'placa' => $placa, 'cantidades' => $cantidades, 'horas' => $horas, 'model' => $precio->model));
                        }
                        break;
                }
            }
            return $this->render('producto', ['r' => 'costo', 'submenu' => $submenu]);
        }*/

        public function actionConfig()
        {
            $get = Yii::$app->request->get();

            if (isset($get['op'])) {
                switch ($get['op']) {
                    case 'sucursal':
                        if (isset($get['frm'])) {
                            $sucursal = New Sucursal();
                            if (isset($get['id']))
                                $sucursal = Sucursal::findOne(['idSucursal' => $get['id']]);
                            if ($sucursal->load(Yii::$app->request->post())) {
                                if ($sucursal->save()) {
                                    return "done";
                                }
                            }
                            return $this->renderAjax('forms/sucursal', ['model' => $sucursal]);
                        }
                        $search     = new SucursalSearch();
                        $sucursales = $search->search(Yii::$app->request->queryParams);
                        return $this->render('config', ['r' => 'sucursales', 'sucursales' => $sucursales]);
                        break;
                    case 'user':
                        if (isset($get['frm'])) {
                            $user = new User();
                            if (isset($get['id']))
                                $user = User::findOne(['idUser' => $get['id']]);
                            if ($user->load(Yii::$app->request->post())) {
                                if ($user->idUser != null) {
                                    $userBpk = User::findOne($user->idUser);
                                    if ($userBpk->password != $user->password)
                                        $user->password = md5($user->password);
                                } else {
                                    $user->password = md5($user->password);
                                    $user->fechaRegistro = date('Y-m-d H:i:s');
                                    $user->fechaAcceso = date('Y-m-d H:i:s');
                                }
                                $user->auth_key = md5($user->password);
                                if ($user->save()) {
                                    return "done";
                                }
                            }
                            return $this->renderAjax('forms/user', ['model' => $user]);
                        }
                        $search   = new UserSearch();
                        $usuarios = $search->search(Yii::$app->request->queryParams);
                        return $this->render('config', ['r' => 'usuarios', 'usuarios' => $usuarios]);
                        break;
                    case 'caja':
                        if (isset($get['frm'])) {
                            $caja = new Caja();
                            if (isset($get['id']))
                                $caja = Caja::findOne(['idCaja' => $get['id']]);
                            if ($caja->load(Yii::$app->request->post())) {
                                if ($caja->save()) {
                                    return "done";
                                }
                            }
                            return $this->renderAjax('forms/caja', ['model' => $caja]);
                        }

                        $search   = new CajaSearch();
                        $cajas = $search->search(Yii::$app->request->queryParams);
                        return $this->render('config', ['r' => 'cajas', 'cajas' => $cajas]);
                        break;
                    case "imprenta":
                        if(isset($get['imp'])) {
                            switch ($get['imp']) {
                                case 'tdt':
                                    if (isset($get['id'])) {
                                        $model = new ImprentaTipoTrabajo();
                                        if (is_numeric($get['id'])) {
                                            $model = ImprentaTipoTrabajo::findOne($get['id']);
                                        }
                                        if ($model->load(Yii::$app->request->post())) {
                                            if ($model->save()) {
                                                return "done";
                                            }
                                        }
                                        return $this->renderAjax('forms/imprentaTipoTrabajo', ['model' => $model]);
                                    }
                                    $models = ImprentaTipoTrabajo::find();
                                    $search = new ActiveDataProvider(['query' => $models]);
                                    return $this->render('config', ['r' => 'imprenta', 'imp' => $get['imp'], 'search' => $search, 'models' => $models]);
                                    break;
                            }
                            return $this->render('config', ['r' => 'imprenta']);
                        }
                        return $this->render('config', ['r' => 'imprenta']);
                        break;
                }
            }
            return $this->render('config');
        }

        public function actionReport()
        {
            $post = Yii::$app->request->get();
            if (isset($post['tipo']) && isset($post['fechaStart']) && isset($post['fechaEnd']) && isset($post['sucursal'])) {
                if (!empty($post['fechaStart']) && !empty($post['fechaEnd']) && !empty($post['sucursal'])) {
                    if ($post['tipo'] == "pd") {
                        $idCaja = Caja::find()->andWhere(['fk_idSucursal'=>$post['sucursal']])->one();
                        $deudas = MovimientoCaja::find()
                            ->andWhere(['tipoMovimiento' => 0])
                            ->andWhere(['fk_idCajaDestino' => $idCaja->idCaja])
                            ->andWhere(['between', 'time', $post['fechaStart'] . ' 00:00:00', $post['fechaEnd'] . ' 23:59:59'])
                            ->select('idParent')
                            ->groupBy('idParent')
                            ->all();
                        $venta = [];
                        foreach ($deudas as $deuda) {
                            $tmp =  MovimientoCaja::find()
                                ->andWhere(['idParent'=>$deuda->idParent])
                                ->all();
                            $orden = null;
                            foreach($tmp as $key => $item) {
                                $orden = OrdenCTP::find()
                                    ->andWhere(['fk_idMovimientoCaja' => $item->idParent])
                                    ->andWhere(['NOT LIKE', 'fechaCobro', date('Y-m-d', strtotime($item->time))]);
                                $orden->joinWith('fkIdCliente');
                                if (!empty($post['clienteNegocio'])) {
                                    $orden->andWhere(['cliente.nombreNegocio' => $post['clienteNegocio']]);
                                }
                                if (!empty($post['clienteResponsable'])) {
                                    $orden->andWhere(['cliente.nombreResponsable' => $post['clienteResponsable']]);
                                }
                                if ($post['factura'] != "") {
                                    $orden->andWhere(['cfSF' => $post['factura']]);
                                }
                                $orden = $orden->one();
                                if (!empty($orden))
                                    break;
                            }
                            if (!empty($orden))
                                array_push($venta, $orden);
                        }
                        $data = new ArrayDataProvider([
                                                          'allModels' => $venta,
                                                      ]);
                        $r = "deuda";
                    } else {
                        $post['fechaStart'] = date('Y-m-d', strtotime($post['fechaStart']));
                        $post['fechaEnd'] = date('Y-m-d', strtotime($post['fechaEnd']));
                        $venta = OrdenCTP::find();
                        $venta->andWhere(['OrdenCTP.fk_idSucursal' => $post['sucursal']]);
                        $venta->joinWith('fkIdCliente');
                        if (!empty($post['clienteNegocio'])) {
                            $venta->andWhere(['like','cliente.nombreNegocio', '%'.$post['clienteNegocio'].'%']);
                        }
                        if (!empty($post['clienteResponsable'])) {
                            $venta->andWhere(['like','cliente.nombreResponsable' , '%'.$post['clienteResponsable'].'%']);
                        }
                        if ($post['factura']!="") {
                            $venta->andWhere(['cfSF' => $post['factura']]);
                        }
                        if ($post['tipo'] == "v")
                            $venta->andWhere(['!=', 'estado', '1']);

                        if ($post['tipo'] == "d")
                            $venta->andWhere(['estado' => '2']);

                        $venta->andWhere(['between', 'fechaCobro', $post['fechaStart'] . ' 00:00:00', $post['fechaEnd'] . ' 23:59:59']);
                        $venta->orderBy(['correlativo' => SORT_ASC]);

                        //$data = $venta->all();
                        $r = "table";
                        if ($post['tipo'] == "im") {
                            $venta->andWhere(['IS NOT', 'factura', NULL]);
                            $venta->orderBy(['factura' => SORT_ASC]);
                            $r = 'impuesto';
                        }

                        $data = new ActiveDataProvider([
                                                           'query' => $venta,
                                                       ]);
                    }
                    return $this->render('reporte', [
                        'r' => $r,
                        'clienteNegocio' => $post['clienteNegocio'],
                        'clienteResponsable' => $post['clienteResponsable'],
                        'fechaStart' => $post['fechaStart'],
                        'fechaEnd' => $post['fechaEnd'],
                        'sucursal' => $post['sucursal'],
                        'factura' => $post['factura'],
                        'data' => $data,
                    ]);

                    //$mPDF1->WriteHTML($this->renderPartial('prints/report', array('data' => $data, 'deuda' => $deuda), true));
                    //Yii::app()->end();
                } else
                    return $this->render('reporte', [
                        'clienteNegocio' => '',
                        'clienteResponsable' => '',
                        'fechaStart' => $post['fechaStart'],
                        'fechaEnd' => $post['fechaEnd'],
                        'sucursal' => $post['sucursal'],
                        'factura' => '',
                    ]);
            } else
                return $this->render('reporte', [
                    'clienteNegocio' => '',
                    'clienteResponsable' => '',
                    'fechaStart' => '',
                    'fechaEnd' => '',
                    'sucursal' => '',
                    'factura' => '',
                ]);
        }

        public function actionPlacas()
        {
            $post = Yii::$app->request->get();
            if (isset($post['tipo']) && isset($post['fechaStart']) && isset($post['sucursal'])) {
                if (!empty($post['fechaStart']) && !empty($post['sucursal'])) {
                    $total = 0;
                    if ($post['tipo'] == "a") {
                        $ordenes = OrdenCTP::find()
                            ->andWhere(['between', 'fechaGenerada', $post['fechaStart'] . ' 00:00:00', $post['fechaStart'] . ' 23:59:59'])
                            ->andWhere(['fk_idSucursal' => $post['sucursal']])
                            ->orderBy(['fechaGenerada' => SORT_ASC]);
                        if (isset($post['tipoOrden']) && $post['tipoOrden'] != "")
                            $ordenes->andWhere(['tipoOrden' => $post['tipoOrden']]);
                        $ordenes = $ordenes->all();
                        $placas = ProductoStock::find()
                            ->joinWith('fkIdProducto')
                            ->andWhere(['fk_idSucursal' => $post['sucursal']])
                            ->orderBy(['formato' => SORT_ASC, 'dimension' => SORT_ASC])
                            ->all();
                        $tipo = [
                            0 => "Orden de Trabajo",
                            1 => "Orden Interna",
                            2 => "Reposicion",
                        ];
                        $data = [];

                        foreach ($ordenes as $orden) {
                            if ($orden->tipoOrden == 0) {
                                if ($orden->estado == 1)
                                    continue;
                            }
                            $row = [
                                'fecha' => $orden->fechaGenerada,
                                'cliente' => ((empty($orden->fk_idCliente)) ? $orden->responsable : $orden->fkIdCliente->nombreNegocio),
                                'orden' => ($orden->tipoOrden == 0) ? $orden->correlativo : $orden->codigoServicio,
                                'tipo' => $tipo[$orden->tipoOrden],
                                'estado' => $orden->estado
                            ];
                            foreach ($placas as $key => $placa) {
                                $row[$placa->fkIdProducto->formato] = 0;
                            }
                            $row['observaciones'] = "";
                            if ($orden->estado >= 0) {
                                foreach ($orden->ordenDetalles as $detalle) {
                                    $row[$detalle->fkIdProductoStock->fkIdProducto->formato] += $detalle->cantidad;
                                    $total += $detalle->cantidad;
                                }
                            } else {
                                $row['observaciones'] = "<span class=\"text-danger\">Anulado</span>";
                            }
                            if ($orden->tipoOrden != 0) {
                                if ($orden->tipoOrden == 2) {
                                    if (!empty($row['observaciones']))
                                        $row['observaciones'] = $row['observaciones'] . "-";
                                    $row['observaciones'] = $row['observaciones'] . "<span class=\"text-warning\">" . ((is_array(SGOperation::tiposReposicion($orden->tipoRepos))) ? '' : SGOperation::tiposReposicion($orden->tipoRepos)) . "</span>" . ((empty($orden->attribuible)) ? "" : "-" . $orden->attribuible);
                                    $row['observaciones'] = $row['observaciones'] . "- <span class=\"text-info\">Orden ".((empty($orden->fk_idParent))?$orden->codDependiente:$orden->fkIdParent->correlativo). "</span>";
                                }
                                if (!empty($row['observaciones']))
                                    $row['observaciones'] = $row['observaciones'] . "-";
                                $row['observaciones'] = $row['observaciones'] . $orden->observaciones;
                            }
                            array_push($data, $row);
                        }
                        $data = new ArrayDataProvider([
                            'allModels' => $data,
                            'pagination'=>false,
                        ]);
                        $r = "all";
                    }
                    if ($post['tipo'] == "f") {
                        $placa = [];
                        $data  = new ArrayDataProvider([
                                                           'allModels'  => $placa,
                                                       ]);
                        $r     = "formato";
                    }
                    return $this->render('placas', [
                        'r'          => $r,
                        'fechaStart' => $post['fechaStart'],
                        'sucursal'   => $post['sucursal'],
                        'tipoOrden'  => $post['tipoOrden'],
                        'data'       => $data,
                        'total'      => $total,
                    ]);
                }
                return $this->render('placas', [
                    'fechaStart' => $post['fechaStart'],
                    'sucursal'   => $post['sucursal'],
                    'tipoOrden'  => '',
                ]);
            }
            return $this->render('placas', [
                'fechaStart' => '',
                'sucursal'   => '',
                'tipoOrden'  => '',
            ]);
        }

        public function actionCliente()
        {
            $get = Yii::$app->request->get();
            if (isset($get['id'])) {
                $model = Cliente::findOne($get['id']);
                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(['admin/cliente']);
                } else {
                    return $this->renderAjax('forms/cliente', [
                        'model' => $model,
                    ]);
                }
            }
            if (isset($get['op'])) {
                if ($get['op'] == "new") {
                    $model                = new Cliente();
                    $model->fechaRegistro = date("Y-m-d H:i:s");
                    if ($model->load(Yii::$app->request->post()) && $model->save()) {
                        return $this->redirect(['admin/cliente']);
                    } else {
                        return $this->renderAjax('forms/cliente', [
                            'model' => $model,
                        ]);
                    }
                }
            }
            $search   = new ClienteSearch();
            $clientes = $search->search(Yii::$app->request->queryParams);
            return $this->render('cliente', ['r' => 'list', 'clientes' => $clientes, 'search' => $search]);
        }

        public function actionArqueos()
        {
            $get = Yii::$app->request->get();
            if (isset($get['op'])) {
                switch ($get['op']) {
                    case "chica":
                        $search = new MovimientoCajaSearchUserCaja();
                        $cchica = $search->search(yii::$app->request->queryParams);
                        $cchica->query
                            ->andWhere(['tipoMovimiento' => 2]);
                        //->andWhere(['fk_idCajaOrigen' => $this->idCaja]);
                        if (isset($get['CajaChica'])) {
                            $cchica->attributes = $get['CajaChica'];
                        }
                        return $this->render('caja', ['r' => 'cajaChica', 'cajasChicas' => $cchica, 'search' => $search]);
                        break;
                    case "recibo":
                        $search = new ReciboSearch();
                        $recibos = $search->search(yii::$app->request->queryParams);
                        $recibos->query->addOrderBy(['fechaRegistro' => SORT_DESC]);
                        return $this->render('caja', ['r' => 'recibos', 'recibos' => $recibos, 'search' => $search]);
                        break;
                    case "arqueos":
                        $search = new MovimientoCajaSearchUserCaja();
                        $arqueos = $search->search(Yii::$app->request->queryParams);
                        $arqueos->query
                            ->andWhere(['tipoMovimiento' => 3])
                            ->orderBy(["time" => SORT_DESC]);
                        return $this->render('caja', ['r' => 'arqueos', 'arqueos' => $arqueos, 'search' => $search]);
                        break;
                    case "arqueo":
                        $sucursales = Sucursal::find()->all();
                        if (isset($get['ic'])) {
                            $arqueo = new MovimientoCaja();
                            $caja = Caja::findOne(['fk_idSucursal' => $get['ic']]);
                            $d = date("d");
                            $end = date("Y-m-d H:i:s");

                            $variables = SGCaja::getSaldo($caja->idCaja, $end, false, false);

                            return $this->render('caja',
                                [
                                    'r' => 'arqueo',
                                    'sucursales' => $sucursales,
                                    'saldo' => $variables['saldo'],
                                    'arqueo' => $arqueo,
                                    'caja' => $caja,
                                    'fecha' => date('Y-m-d H:i:s', strtotime($end)),
                                    'ventas' => $variables['ventas'],
                                    'deudas' => $variables['deudas'],
                                    'recibos' => $variables['recibos'],
                                    'cajas' => $variables['cajas'],
                                    'dia' => $d,
                                ]);
                            break;
                        }
                        return $this->render('caja', ['r' => 'arqueo', 'sucursales' => $sucursales]);

                    case "arqueos":
                        return $this->render('caja');
                        break;
                    case "admin":
                        $caja = Caja::find()->where(['is', 'fk_idSucursal', NULL])->one();
                        $post = Yii::$app->request->post();
                        if(isset($post['fecha'])) {
                            if ($post['fecha'] != '') {
                                $d = date("d");
                                $end = $post['fecha'];

                                $variables = SGCaja::getSaldo($caja->idCaja, $end, false, false,true);

                                return $this->render('caja',
                                    [
                                        'r' => 'admin',
                                        'saldo' => $variables['saldo'],
                                        'arqueo' => $variables['arqueos'],
                                        'caja' => $caja,
                                        'fecha' => date('Y-m-d H:i:s', strtotime($post['fecha'])),
                                        'recibos' => $variables['recibos'],
                                        'cajas' => $variables['cajas'],
                                        'dia' => $d,
                                    ]);
                                break;
                            }
                        }
                    return $this->render('caja',['r' => 'admin','fecha' => '']);
                }
            }
            return $this->render('caja');
        }

        public function actionPrint()
        {
            $get = Yii::$app->request->get();
            if (isset($get['op']) && isset($get['id'])) {
                switch ($get['op']) {
                    case "registro":
                        $arqueoTmp = MovimientoCaja::findOne(['idMovimientoCaja' => $get['id']]);
                        $variables = SGCaja::getSaldo($arqueoTmp->fk_idCajaOrigen, $arqueoTmp->time, false, ['arqueo' => $arqueoTmp->fechaCierre]);
                        $content   = $this->renderPartial('prints/registroDiario',
                                                          array(
                                                              'saldo'   => $variables['saldo'],
                                                              'fecha'   => $arqueoTmp->fechaCierre,
                                                              'arqueo'  => $arqueoTmp,
                                                              'ventas'  => $variables['ventas'],
                                                              'recibos' => $variables['recibos'],
                                                              'cajas'   => $variables['cajas'],
                                                              'deudas'  => $variables['deudas'],
                                                          ));
                        $title     = 'Registro Diario ' . date("d-m-Y", strtotime($arqueoTmp->fechaCierre));
                        break;

                }
                $pdf = new Pdf([
                                   // set to use core fonts only
                                   'mode'         => Pdf::MODE_CORE,
                                   'format'       => Pdf::FORMAT_LETTER,
                                   'orientation'  => Pdf::ORIENT_PORTRAIT,
                                   'destination'  => Pdf::DEST_BROWSER,
                                   'content'      => $content,
                                   // format content from your own css file if needed or use the
                                   // enhanced bootstrap css built by Krajee for mPDF formatting
                                   'cssFile'      => '@webroot/css/bootstrap.min.journal.css',
                                   // set mPDF properties on the fly
                                   'marginLeft'   => 9, // margin_left. Sets the page margins for the new document.
                                   'marginRight'  => 9, // margin_right
                                   'marginTop'    => 8, // margin_top
                                   'marginBottom' => 8, // margin_bottom
                                   'marginHeader' => 9, // margin_header
                                   'marginFooter' => 9, // margin_footer
                                   'options'      => ['title' => $title],
                               ]);

                // return the pdf output as per the destination setting
                return $pdf->render();
            }
        }

        public function actionRecibos()
        {
            $get = yii::$app->request->get();
            if (empty($get['op'])) {
                $search                = new ReciboSearch();
                $recibos               = $search->search(yii::$app->request->queryParams);
                $recibos->query->andWhere(['fk_idSucursal'=>$this->idSucursal]);
                return $this->render('recibo', ['r' => 'recibos', 'recibos' => $recibos, 'search' => $search]);
            } else {
                $sucursal ='';
                if (isset($get['id'])){
                    $recibo = Recibo::findOne(['idRecibo' => $get['id']]);
                    if(!empty($recibo->fk_idSucursal))
                        $sucursal = $recibo->fkIdSucursal->nombre;
                }
                else {
                    $recibo                = new Recibo();
                    //$recibo->fk_idSucursal = $this->idSucursal;
                    $recibo->fechaRegistro = date("Y-m-d H:i:s");
                    $recibo->fk_idUser     = Yii::$app->user->id;
                }
                if($recibo->tipoRecibo == null) {
                    if ($get['op'] == 'i') {
                        $recibo->tipoRecibo = 1;
                    } else {
                        $recibo->tipoRecibo = 0;
                    }
                }

                $post = yii::$app->request->post();
                if ($recibo->load($post)) {
                    $op   = new SGRecibo();
                    if(isset($post['sucursal'])) {
                        $sucursal = $post['sucursal'];
                        $recibo->fk_idSucursal = $sucursal;
                        if ($sucursal == '')
                            $caja = Caja::find()->where(['is','fk_idSucursal',NULL])->one();
                        else
                            $caja = Caja::find()->where(['fk_idSucursal' => $sucursal])->one();
                        if (!empty($caja)) {
                            $data = $op->grabar(['recibo' => $recibo, 'caja' => $caja]);
                            if ($op->success)
                                return 'done';
                        }
                    }
                }

                return $this->renderAjax('forms/recibo', ['recibo' => $recibo,'sucursal'=>$sucursal]);
            }
        }
    }
