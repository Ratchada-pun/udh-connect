<?php

namespace app\modules\app\controllers;

use Yii;
use app\models\TblPatient;
use app\models\TblPatientSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * SettingController implements the CRUD actions for TblPatient model.
 */

class SettingController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $params = Yii::$app->request->get('TblPatientSearch', []);
        $searchModel = new TblPatientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // $query = (new \yii\db\Query())
        //     ->select([
        //         'tbl_patient.id',
        //         'CONCAT(tbl_patient.first_name,\' \',tbl_patient.last_name) AS fullname',
        //         'tbl_patient.hn',
        //         'tbl_patient.brith_day',
        //         'tbl_patient.phone_number',
        //         'tbl_patient.user_type'
        //     ])
        //     ->from('tbl_patient')
        //     ->orderBy('id DESC');
        // if (ArrayHelper::getValue($params, 'fullname')) {
        //     $query->andWhere(['like', 'tbl_patient.first_name', ArrayHelper::getValue($params, 'fullname')]);
        //     $query->andWhere(['like', 'tbl_patient.last_name', ArrayHelper::getValue($params, 'fullname')]);
        // }
        // $dataProvider = new ActiveDataProvider([
        //     'query' => $query,
        //     'pagination' => [
        //         'pageSize' => false,
        //     ],
        //     'key' => 'id'
        // ]);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing TblPatient model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */



    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    /**
     * Finds the TblPatient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TblPatient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblPatient::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
