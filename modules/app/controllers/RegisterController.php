<?php

namespace app\modules\app\controllers;

use Yii;
use app\models\TblPatient;
use app\models\TblPatientSearch;
use common\components\Util;
use common\Line\EventHandler\MessageHandler\Flex\FlexRegisterSuccess;
use kartik\form\ActiveForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\httpclient\Client;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * RegisterController implements the CRUD actions for TblPatient model.
 */
class RegisterController extends Controller
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
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['search-patient', 'check-patient', 'patient-mobile-app'],
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (in_array($action->id, ['search-patient', 'check-patient', 'patient-mobile-app'])) {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Lists all TblPatient models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TblPatientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblPatient model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TblPatient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TblPatient();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblPatient model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
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

    public function actionCreateNewUser()
    {
        $session = Yii::$app->session;
        // if ($session->get('user')) {
        //     return $this->redirect(['/app/appoint/create-department']);
        // }
        $model = new TblPatient();
        $user = Yii::$app->request->get('user');
        $request = Yii::$app->request;
        $model->scenario = TblPatient::SCENARIO_LINE;

        // if($session->get('user')) {
        //     return $this->redirect(['/app/appoint/create-department']);
        // }

        if ($model->load(Yii::$app->request->post())) {
            $response = Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $formdata = $request->post('TblPatient', []);
            $posted = $request->post();

            $isRegisted = TblPatient::findOne(['id_card' => $formdata['id_card']]) !== null;

            if ($isRegisted) {
                throw new HttpException(422, "มีการลงทะเบียนข้อมูลในระบบแล้ว ไม่สามารถลงทะเบียนซ้ำได้");
            }

            if (TblPatient::findOne(['line_id' => $posted['userId']]) !== null) {
                throw new HttpException(422, "มีการลงทะเบียนข้อมูลในระบบแล้ว ไม่สามารถลงทะเบียนซ้ำได้");
            }

            $day = isset($formdata['day']) ? $formdata['day'] : null;
            $month = isset($formdata['month']) ? $formdata['month'] : null;
            $year = isset($formdata['year']) ? $formdata['year'] : null;
            $brith_day = $year . '-' . $month . '-' . $day; // yyyy-mm-dd
            $model->brith_day = $brith_day;
            //$model->user_type = isset($formdata['user_type']) ? $formdata['user_type'] : null;
            //$model->hn = isset($posted['hn']) ? $posted['hn'] : null;
            $model->line_id = isset($posted['userId']) ? $posted['userId'] : null;
            if ($model->save()) {
                $userId = $model->line_id;
                //$richMenuId = 'richmenu-968dd0ce9b38909ff89aa2c5e5386845';
                $richMenuId = 'richmenu-7351dbcc12fcf979942367a8422087b3';
                $dataRichMenu = '';
                $client = new Client();
                //Unlink rich menu from user
                $unlinkresponse = $client->createRequest()
                    ->setMethod('DELETE')
                    ->setUrl('https://api.line.me/v2/bot/user/' . $userId . '/richmenu')
                    ->addHeaders(['content-type' => 'application/json'])
                    ->addHeaders(['Authorization' => 'Bearer FWZ3P4fRrEXOmhyQtiQFp+TXeSSrkQwGdt3zvp1TezV9gYOruopsbo4YDBjoIKSoWzd/Yx/Ow/8xT0Elwvv6N+akUpPXtdMOdi5NN+t8BMHiVFWoDopJLEn0fUJSg0Rink0gBjXMSwcKIoI6FmoaQQdB04t89/1O/w1cDnyilFU='])
                    ->send();
                //Link rich menu to user
                $response = $client->createRequest()
                    ->setMethod('POST')
                    ->setUrl('https://api.line.me/v2/bot/user/' . $userId . '/richmenu/' . $richMenuId)
                    ->addHeaders(['content-type' => 'application/json'])
                    ->addHeaders(['Authorization' => 'Bearer FWZ3P4fRrEXOmhyQtiQFp+TXeSSrkQwGdt3zvp1TezV9gYOruopsbo4YDBjoIKSoWzd/Yx/Ow/8xT0Elwvv6N+akUpPXtdMOdi5NN+t8BMHiVFWoDopJLEn0fUJSg0Rink0gBjXMSwcKIoI6FmoaQQdB04t89/1O/w1cDnyilFU='])
                    ->send();
                if ($response->isOk) {
                    $dataRichMenu = $response->data;
                }
                $session->set('user', $model->getAttributes());
                $model = TblPatient::findOne($model->id);
                $FlexMessage = FlexRegisterSuccess::get($model);
                return [
                    'model' => $model,
                    'success' => true,
                    'message' => 'ลงทะเบียนสำเร็จ!',
                    'dataRichMenu' => $dataRichMenu,
                    'FlexMessage' => $FlexMessage->buildMessage()

                ];
            } else {
                return [
                    'success' => false,
                    'validate' => ActiveForm::validate($model)
                ];
            }
        }
        if ($user == 'old') {
            return $this->render('_form_old_user', [
                'model' => $model,
                'user_type' => $user
            ]);
        }
        return $this->render('form_new_user', [
            'model' => $model,
            'user_type' => $user
        ]);
    }


    public function actionPolicy($user)
    {
        return $this->render('_policy', [
            'action' => $this->id,
            'url' => ['/app/register/create-new-user', 'user' => $user]
        ]);
    }


    public function actionSearchPatient()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $db_mssql = Yii::$app->mssql;
        $fliterKey = $request->post('filter');


        if (mb_strlen($fliterKey) == 13) {
            // $query = $db_mssql->createCommand('SELECT
            //         dbo.v_patient_detail.hn,
            //         REPLACE(dbo.v_patient_detail.CardID, \' \', \'\') as CardID,
            //         dbo.v_patient_detail.titleCode,
            //         REPLACE( dbo.v_patient_detail.firstName, \' \', \'\') as firstName,
            //         REPLACE(dbo.v_patient_detail.lastName, \' \', \'\') as lastName,
            //         dbo.v_patient_detail.phone,
            //         dbo.v_patient_detail.mobilephone,
            //         dbo.v_patient_detail.age,
            //         dbo.v_patient_detail.bday

            //         FROM
            //         dbo.v_patient_detail
            //         WHERE
            //         dbo.v_patient_detail.hn = :hn
            //     ')
            //     ->bindValues([
            //         ':hn' => sprintf("% 7s", $fliterKey)
            //     ])
            //     ->queryOne();
            $query = $db_mssql->createCommand('SELECT TOP
                    1 
                    
                    REPLACE( dbo.PATIENT.hn, \' \', \'\') as hn,
                    PTITLE.titleName,
                    REPLACE( dbo.PATIENT.firstName, \' \', \'\') as firstName,
                    REPLACE(dbo.PATIENT.lastName, \' \', \'\') as lastName,
                    dbo.PATIENT.phone,
                    dbo.PATIENT.birthDay,
                    dbo.PATIENT.titleCode,
                    REPLACE(dbo.PatSS.CardID, \' \', \'\') as CardID,
                    CONVERT (
                    datetime,
                    SUBSTRING ( CONVERT ( CHAR, dbo.PATIENT.birthDay - 5430000 ), 1, 4 ) + (
                    CASE
                    WHEN SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 5, 2 ) = \'00\' THEN
                    \'07\' ELSE SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 5, 2 ) 
                        END 
                    ) + (
                        CASE
					WHEN SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 7, 2 ) = \'00\' THEN
					\'01\' ELSE SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 7, 2 ) 
				END 
				    ) 
			    ) AS bday                   
                FROM
                    dbo.PATIENT
                    INNER JOIN dbo.PatSS ON dbo.PatSS.hn = dbo.PATIENT.hn 
                    LEFT JOIN dbo.PTITLE ON PATIENT.titleCode = PTITLE.titleCode 
                WHERE
                dbo.PatSS.CardID = :CardID')
                ->bindValues([
                    ':CardID' => $fliterKey
                ])
                ->queryOne();

            if ($query) {
                $patient = TblPatient::findOne(['id_card' => $fliterKey]);
                if ($patient) {
                    return [
                        'success' => true,
                        'redirect' => Url::to(['/app/appoint/create-department']),
                        'message' => ''
                    ];
                }
            }
        } else {
            // $query = $db_mssql->createCommand('SELECT
            //         dbo.v_patient_detail.hn,
            //         REPLACE(dbo.v_patient_detail.CardID, \' \', \'\') as CardID,
            //         dbo.v_patient_detail.titleCode,
            //         REPLACE( dbo.v_patient_detail.firstName, \' \', \'\') as firstName,
            //         REPLACE(dbo.v_patient_detail.lastName, \' \', \'\') as lastName,
            //         dbo.v_patient_detail.phone,
            //         dbo.v_patient_detail.mobilephone,
            //         dbo.v_patient_detail.age,
            //         dbo.v_patient_detail.bday

            //         FROM
            //         dbo.v_patient_detail
            //         WHERE
            //         dbo.v_patient_detail.CardID LIKE :CardID
            //     ')
            //     ->bindValues([
            //         ':CardID' => '%'.$fliterKey.'%'
            //     ])
            //     ->queryOne();
            $query = $db_mssql->createCommand('SELECT TOP
                    1 
                    
                    REPLACE( dbo.PATIENT.hn, \' \', \'\') as hn,
                    PTITLE.titleName,
                    REPLACE( dbo.PATIENT.firstName, \' \', \'\') as firstName,
                    REPLACE(dbo.PATIENT.lastName, \' \', \'\') as lastName,
                    dbo.PATIENT.phone,
                    dbo.PATIENT.birthDay,
                    dbo.PATIENT.titleCode,
                    REPLACE(dbo.PatSS.CardID, \' \', \'\') as CardID,
                    CONVERT (
                    datetime,
                    SUBSTRING ( CONVERT ( CHAR, dbo.PATIENT.birthDay - 5430000 ), 1, 4 ) + (
                    CASE
                    WHEN SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 5, 2 ) = \'00\' THEN
                    \'07\' ELSE SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 5, 2 ) 
                        END 
                    ) + (
                        CASE
					WHEN SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 7, 2 ) = \'00\' THEN
					\'01\' ELSE SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 7, 2 ) 
				END 
				    ) 
			    ) AS bday 

                FROM
                    dbo.PATIENT
                    INNER JOIN dbo.PatSS ON dbo.PatSS.hn = dbo.PATIENT.hn
                    LEFT JOIN dbo.PTITLE ON PATIENT.titleCode = PTITLE.titleCode 
                WHERE
                    dbo.PATIENT.hn = :hn')
                ->bindValues([
                    ':hn' => Util::sprintf($fliterKey, 7)
                ])
                ->queryOne();

            if ($query) {
                $patient = TblPatient::findOne(['hn' => $fliterKey]);
                if ($patient) {
                    return [
                        'success' => true,
                        'redirect' => Url::to(['/app/appoint/create-department']),
                        'message' => '',
                        'patient' => $query
                    ];
                }
            }
        }

        if (!$query) {
            return [
                'success' => false,
                'redirect' => Url::to(['/app/register/create-new-user', 'user' => 'new']),
                'message' => 'ไม่พบข้อมูล',
                'patient' => $query
            ];
        }
        return [
            'success' => true,
            'redirect' => Url::to(['/app/register/create-new-user', 'user' => 'old', 'q' => $fliterKey]),
            'message' => '',
            'patient' => $query
        ];
    }

    public function actionPatient($q)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $db_mssql = Yii::$app->mssql;
        $fliterKey = $q;
        if (mb_strlen($fliterKey) == 13) {
            // $query = $db_mssql->createCommand('SELECT
            //         dbo.v_patient_detail.hn,
            //         REPLACE(dbo.v_patient_detail.CardID, \' \', \'\') as CardID,
            //         dbo.v_patient_detail.titleCode,
            //         REPLACE( dbo.v_patient_detail.firstName, \' \', \'\') as firstName,
            //         REPLACE(dbo.v_patient_detail.lastName, \' \', \'\') as lastName,
            //         dbo.v_patient_detail.phone,
            //         dbo.v_patient_detail.mobilephone,
            //         dbo.v_patient_detail.age,
            //         dbo.v_patient_detail.bday

            //         FROM
            //         dbo.v_patient_detail
            //         WHERE
            //         dbo.v_patient_detail.hn = :hn
            //     ')
            //     ->bindValues([
            //         ':hn' => sprintf("% 7s", $fliterKey)
            //     ])
            //     ->queryOne();
            $query = $db_mssql->createCommand('SELECT TOP
                    1 REPLACE( dbo.PATIENT.hn, \' \', \'\') as hn,
                    REPLACE( dbo.PATIENT.firstName, \' \', \'\') as firstName,
                    REPLACE(dbo.PATIENT.lastName, \' \', \'\') as lastName,
                    dbo.PATIENT.phone,
                    dbo.PATIENT.birthDay,
                    dbo.PATIENT.titleCode,
                    REPLACE(dbo.PatSS.CardID, \' \', \'\') as CardID,
                    CONVERT (
                    datetime,
                    SUBSTRING ( CONVERT ( CHAR, dbo.PATIENT.birthDay - 5430000 ), 1, 4 ) + (
                    CASE
                    WHEN SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 5, 2 ) = \'00\' THEN
                    \'07\' ELSE SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 5, 2 ) 
                        END 
                    ) + (
                        CASE
					WHEN SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 7, 2 ) = \'00\' THEN
					\'01\' ELSE SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 7, 2 ) 
				END 
				    ) 
			    ) AS bday                   
                FROM
                    dbo.PATIENT
                    INNER JOIN dbo.PatSS ON dbo.PatSS.hn = dbo.PATIENT.hn 
                WHERE
                dbo.PatSS.CardID = :CardID')
                ->bindValues([
                    ':CardID' => $fliterKey
                ])
                ->queryOne();
        } else {
            // $query = $db_mssql->createCommand('SELECT
            //         dbo.v_patient_detail.hn,
            //         REPLACE(dbo.v_patient_detail.CardID, \' \', \'\') as CardID,
            //         dbo.v_patient_detail.titleCode,
            //         REPLACE( dbo.v_patient_detail.firstName, \' \', \'\') as firstName,
            //         REPLACE(dbo.v_patient_detail.lastName, \' \', \'\') as lastName,
            //         dbo.v_patient_detail.phone,
            //         dbo.v_patient_detail.mobilephone,
            //         dbo.v_patient_detail.age,
            //         dbo.v_patient_detail.bday

            //         FROM
            //         dbo.v_patient_detail
            //         WHERE
            //         dbo.v_patient_detail.CardID LIKE :CardID
            //     ')
            //     ->bindValues([
            //         ':CardID' => '%'.$fliterKey.'%'
            //     ])
            //     ->queryOne();
            $query = $db_mssql->createCommand('SELECT TOP
                    1 REPLACE( dbo.PATIENT.hn, \' \', \'\') as hn,
                    REPLACE( dbo.PATIENT.firstName, \' \', \'\') as firstName,
                    REPLACE(dbo.PATIENT.lastName, \' \', \'\') as lastName,
                    dbo.PATIENT.phone,
                    dbo.PATIENT.birthDay,
                    dbo.PATIENT.titleCode,
                    REPLACE(dbo.PatSS.CardID, \' \', \'\') as CardID,
                    CONVERT (
                    datetime,
                    SUBSTRING ( CONVERT ( CHAR, dbo.PATIENT.birthDay - 5430000 ), 1, 4 ) + (
                    CASE
                    WHEN SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 5, 2 ) = \'00\' THEN
                    \'07\' ELSE SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 5, 2 ) 
                        END 
                    ) + (
                        CASE
					WHEN SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 7, 2 ) = \'00\' THEN
					\'01\' ELSE SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 7, 2 ) 
				END 
				    ) 
			    ) AS bday 

                FROM
                    dbo.PATIENT
                    INNER JOIN dbo.PatSS ON dbo.PatSS.hn = dbo.PATIENT.hn 
                WHERE
                    dbo.PATIENT.hn = :hn')
                ->bindValues([
                    ':hn' => Util::sprintf($fliterKey, 7)
                ])
                ->queryOne();
        }

        if (!$query) {
            throw new HttpException(404, 'ไม่พบข้อมูล กรุณาติดต่อโรงพยาบาลอุดรธาณีเพื่อ .');
        }


        return $query;
    }


    public function actionCheckPatient($q)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $db_mssql = Yii::$app->mssql;
        if (mb_strlen($q) == 13) {
            $query = $db_mssql->createCommand('SELECT TOP
                    1 
                    
                    REPLACE( dbo.PATIENT.hn, \' \', \'\') as hn,
                    REPLACE( PTITLE.titleName, \' \', \'\') as title_name,
                    REPLACE( dbo.PATIENT.firstName, \' \', \'\') as first_name,
                    REPLACE(dbo.PATIENT.lastName, \' \', \'\') as last_name,
                    REPLACE(dbo.PATIENT.phone, \' \', \'\') as phone,
                    -- dbo.PATIENT.birthDay as birth_day,
                    REPLACE(dbo.PatSS.CardID, \' \', \'\') as card_id,
                    CONVERT (
                    datetime,
                    SUBSTRING ( CONVERT ( CHAR, dbo.PATIENT.birthDay - 5430000 ), 1, 4 ) + (
                    CASE
                    WHEN SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 5, 2 ) = \'00\' THEN
                    \'07\' ELSE SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 5, 2 ) 
                        END 
                    ) + (
                        CASE
					WHEN SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 7, 2 ) = \'00\' THEN
					\'01\' ELSE SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 7, 2 ) 
				END 
				    ) 
			    ) AS birth_day                   
                FROM
                    dbo.PATIENT
                    INNER JOIN dbo.PatSS ON dbo.PatSS.hn = dbo.PATIENT.hn 
                    LEFT JOIN dbo.PTITLE ON PATIENT.titleCode = PTITLE.titleCode 
                WHERE
                dbo.PatSS.CardID = :CardID')
                ->bindValues([
                    ':CardID' => $q
                ])
                ->queryOne();
        } else {
            $query = $db_mssql->createCommand('SELECT TOP
                    1 
                    REPLACE( dbo.PATIENT.hn, \' \', \'\') as hn,
                    REPLACE( PTITLE.titleName, \' \', \'\') as title_name,
                    REPLACE( dbo.PATIENT.firstName, \' \', \'\') as first_name,
                    REPLACE(dbo.PATIENT.lastName, \' \', \'\') as last_name,
                    REPLACE(dbo.PATIENT.phone, \' \', \'\') as phone,
                    -- dbo.PATIENT.birthDay as birth_day,
                    REPLACE(dbo.PatSS.CardID, \' \', \'\') as card_id,
                    CONVERT (
                    datetime,
                    SUBSTRING ( CONVERT ( CHAR, dbo.PATIENT.birthDay - 5430000 ), 1, 4 ) + (
                    CASE
                    WHEN SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 5, 2 ) = \'00\' THEN
                    \'07\' ELSE SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 5, 2 ) 
                        END 
                    ) + (
                        CASE
					WHEN SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 7, 2 ) = \'00\' THEN
					\'01\' ELSE SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 7, 2 ) 
				END 
				    ) 
			    ) AS birth_day 

                FROM
                    dbo.PATIENT
                    INNER JOIN dbo.PatSS ON dbo.PatSS.hn = dbo.PATIENT.hn
                    LEFT JOIN dbo.PTITLE ON PATIENT.titleCode = PTITLE.titleCode 
                WHERE
                    dbo.PATIENT.hn = :hn')
                ->bindValues([
                    ':hn' => Util::sprintf($q, 7)
                ])
                ->queryOne();
        }

        if ($query) {
            $query = ArrayHelper::merge($query, [
                'birth_day' => $query['birth_day'] ? Yii::$app->formatter->asDate($query['birth_day'], 'php:Y-m-d') : null
            ]);
        }

        return $query;
    }

    public function actionPatientMobileApp()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new TblPatient();
        $model->scenario = TblPatient::SCENARIO_MOBILE;

        $rawdata = \Yii::$app->getRequest()->getRawBody();
        $bodyParams = $rawdata ? Json::decode($rawdata) : [];
        $hn = ArrayHelper::getValue($bodyParams, 'hn', null);
        if (($patient = TblPatient::findOne(['hn' => $hn])) != null && $hn) {
            return $patient;
        }
        $id_card = ArrayHelper::getValue($bodyParams, 'id_card', null);
        if (($patient = TblPatient::findOne(['id_card' => $id_card])) != null && $id_card) {
            return $patient;
        }
        $model->user_type = !empty($hn) ? 'old' : 'new';
        if ($model->load($bodyParams, '') && $model->save()) {
            return $model;
        } else {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(400);
            return ActiveForm::validate($model);
        }
    }
}
