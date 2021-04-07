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
use yii\helpers\Json;
use yii\data\ArrayDataProvider;

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
                        'actions' => ['index', 'user-booking','delete'],
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

    public function actionUserBooking()
    {
        $db_mssql = Yii::$app->mssql;
        $db = Yii::$app->db;

        $user_bookings = $db_mssql->createCommand(//ข้อมูลผู้ป่วยนัดหมาย ฐานข้อมูลโรงพยาบาล
            'SELECT
                    Appoint.app_type,
                    Appoint.appoint_date, 
                    Appoint.appoint_time_from, 
                    Appoint.appoint_time_to, 
                    Appoint.appoint_note, 
                    Appoint.pre_dept_code, 
                    Appoint.maker, 
                    Appoint.app_reserv, 
                    Appoint.keyin_time, 
                    Appoint.phone,
                    Appoint.status_in, 
                    Appoint.AR_ID,
                    DOCC.docCode,
                    DOCC.docName,
                    DOCC.docLName,
                    DEPT.deptDesc,
                    DEPTGROUP.DeptGroup,
                    DEPTGr.DeptGrDesc,
                    PATIENT.firstName,
                    PATIENT.lastName,
                    REPLACE(Appoint.doctor, \' \', \'\') as doctor,
                    REPLACE(Appoint.CID, \' \', \'\') as CID,
                    REPLACE(Appoint.hn, \' \', \'\') as hn
                FROM
                    dbo.Appoint
                INNER JOIN dbo.DOCC ON Appoint.doctor = DOCC.docCode
                INNER JOIN dbo.DEPT ON Appoint.pre_dept_code = DEPT.deptCode
                INNER JOIN dbo.DEPTGROUP ON DEPT.deptCode = DEPTGROUP.deptCode
                INNER JOIN dbo.DEPTGr ON DEPTGROUP.DeptGroup = DEPTGr.DeptGroup
                LEFT JOIN dbo.PATIENT ON Appoint.hn = PATIENT.hn
                WHERE
                Appoint.status_in = \'m\' 
                ORDER BY Appoint.keyin_time DESC
                '
        )->queryAll();

        $bookings = [];
        foreach ($user_bookings as $key => $user_booking) {
            if (empty($user_booking['hn'])) {
                $patient = (new \yii\db\Query()) //ข้อมูลลงทะเบียนใช้งาน udh connect
                    ->select(['*'])
                    ->from('tbl_patient')
                    ->where([
                        'id_card' => $user_booking['CID']
                    ])
                    ->one($db);
                $bookings[] = ArrayHelper::merge($user_booking, [
                    'firstName' => ArrayHelper::getValue($patient, 'first_name', '-'),
                    'lastName' => ArrayHelper::getValue($patient, 'last_name', '-'),
                ]);
            } else {
                $bookings[] = $user_booking;
            }
        }


        $provider = new ArrayDataProvider([
            'allModels' => $bookings,
            'pagination' => [
                'pageSize' => false,
            ],
        ]);

        return $this->render('_form_user_booking',[
            'provider' => $provider
        ]);
    }
}
