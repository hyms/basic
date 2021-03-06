<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "recibo".
 *
 * @property integer $idRecibo
 * @property string $codigo
 * @property integer $secuencia
 * @property integer $fk_idSucursal
 * @property string $detalle
 * @property string $nombre
 * @property string $ciNit
 * @property double $monto
 * @property double $acuenta
 * @property string $fechaRegistro
 * @property integer $fk_idUser
 * @property string $codigoVenta
 * @property integer $tipoRecibo
 * @property integer $fk_idMovimientoCaja
 * @property string $observaciones
 *
 * @property MovimientoCaja $fkIdMovimientoCaja
 * @property Sucursal $fkIdSucursal
 * @property User $fkIdUser
 */
class Recibo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recibo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['secuencia', 'fk_idUser', 'tipoRecibo', 'fk_idMovimientoCaja'], 'integer'],
            [['detalle', 'nombre', 'ciNit', 'monto', 'fechaRegistro', 'fk_idUser', 'tipoRecibo', 'fk_idMovimientoCaja'], 'required'],
            [['monto', 'acuenta'], 'number'],
            [['fechaRegistro'], 'safe'],
            [['codigo', 'codigoVenta'], 'string', 'max' => 100],
            [['detalle'], 'string', 'max' => 500],
            [['nombre'], 'string', 'max' => 50],
            [['ciNit'], 'string', 'max' => 20],
            [['observaciones'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idRecibo' => 'Id Recibo',
            'codigo' => 'Codigo',
            'secuencia' => 'Secuencia',
            'fk_idSucursal' => 'Fk Id Sucursal',
            'detalle' => 'Detalle',
            'nombre' => 'Nombre',
            'ciNit' => 'Ci Nit',
            'monto' => 'Monto',
            'acuenta' => 'Acuenta',
            'fechaRegistro' => 'Fecha Registro',
            'fk_idUser' => 'Fk Id User',
            'codigoVenta' => 'Codigo Venta',
            'tipoRecibo' => 'Tipo Recibo',
            'fk_idMovimientoCaja' => 'Fk Id Movimiento Caja',
            'observaciones' => 'Observaciones',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFkIdMovimientoCaja()
    {
        return $this->hasOne(MovimientoCaja::className(), ['idMovimientoCaja' => 'fk_idMovimientoCaja']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFkIdSucursal()
    {
        return $this->hasOne(Sucursal::className(), ['idSucursal' => 'fk_idSucursal']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFkIdUser()
    {
        return $this->hasOne(User::className(), ['idUser' => 'fk_idUser']);
    }
}
