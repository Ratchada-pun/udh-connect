<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblPatient;

/**
 * TblPatientSearch represents the model behind the search form of `app\models\TblPatient`.
 */
class TblPatientSearch extends TblPatient
{
    public $fullname;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['first_name', 'last_name', 'id_card', 'hn', 'brith_day', 'phone_number', 'created_at', 'line_id', 'user_type','fullname'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = TblPatient::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $brith_day = $this->brith_day;
        if($this->brith_day){
            $brith_day = explode("/", $this->brith_day);
            $brith_day = $brith_day[2].'-'.$brith_day[1].'-'.$brith_day[0];
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'brith_day' => $brith_day,
            'created_at' => $this->created_at,
        ]);

        $query->orFilterWhere(['like', 'first_name', $this->fullname])
            ->orFilterWhere(['like', 'last_name', $this->fullname])
            ->andFilterWhere(['like', 'id_card', $this->id_card])
            ->andFilterWhere(['like', 'hn', $this->hn])
            ->andFilterWhere(['like', 'phone_number', $this->phone_number])
            ->andFilterWhere(['like', 'line_id', $this->line_id])
            ->andFilterWhere(['like', 'user_type', $this->user_type]);

        return $dataProvider;
    }
}
