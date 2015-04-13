<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ReciboSearch represents the model behind the search form about `app\models\Recibo`.
 */
class ReciboSearch extends Recibo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idRecibo', 'secuencia', 'fk_idSucursal', 'fk_idUser', 'fk_idServicio', 'tipoRecibo', 'fk_idMovimientoCaja'], 'integer'],
            [['codigo', 'detalle', 'nombre', 'ciNit', 'fechaRegistro', 'codigoVenta', 'obseraciones'], 'safe'],
            [['saldo', 'monto', 'acuenta'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Recibo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'idRecibo' => $this->idRecibo,
            'secuencia' => $this->secuencia,
            'fk_idSucursal' => $this->fk_idSucursal,
            'saldo' => $this->saldo,
            'monto' => $this->monto,
            'acuenta' => $this->acuenta,
            'fechaRegistro' => $this->fechaRegistro,
            'fk_idUser' => $this->fk_idUser,
            'fk_idServicio' => $this->fk_idServicio,
            'tipoRecibo' => $this->tipoRecibo,
            'fk_idMovimientoCaja' => $this->fk_idMovimientoCaja,
        ]);

        $query->andFilterWhere(['like', 'codigo', $this->codigo])
            ->andFilterWhere(['like', 'detalle', $this->detalle])
            ->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'ciNit', $this->ciNit])
            ->andFilterWhere(['like', 'codigoVenta', $this->codigoVenta])
            ->andFilterWhere(['like', 'obseraciones', $this->obseraciones]);

        return $dataProvider;
    }
}
