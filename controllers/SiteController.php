<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\httpclient\Client;
use yii\web\HttpException;
use yii\httpclient\Exception;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $session = Yii::$app->session;
        $profile = $session->get('user');
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        try {
            $userId = 'Ue5337b220743f592158018e2a0423ff3';
            $richMenuId = 'richmenu-349a649ee1b2e2f659ae2da8e24df4ef';
            $dataRichMenu = '';
            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl('https://api.line.me/v2/bot/user/' . $userId . '/richmenu/' . $richMenuId)
                ->addHeaders(['content-type' => 'application/json'])
                ->addHeaders(['Authorization' => 'Bearer uLF9THsOlQfvth3Y7bvLym0ZwPoEliKF7MszmJq4aymKwWJfYpknJ/zmWwOZsNzgrDXU0+Y7KGMrxCPi79NX1/g3iSeY5Mva1olEL4cwoJtDdznKV+7MjYP89tW6BO8/A//QjXTcoB6BdDt6ooFzB1GUYhWQfeY8sLGRXgo3xvw='])
                ->send();
            if ($response->isOk) {
                $dataRichMenu = $response->data;
                print_r($dataRichMenu);
            }
        } catch (Exception $e) {
            print_r($e->getMessage());
        }

        //return $this->render('about');
    }

    public function actionClearCache()
    {
        $frontendAssetPath = \Yii::getAlias('@app') . '/web/assets/';

        $this->recursiveDelete($frontendAssetPath);

        if (\Yii::$app->cache->flush()) {
            \Yii::$app->session->setFlash('crudMessage', 'Cache has been flushed.');
        } else {
            \Yii::$app->session->setFlash('crudMessage', 'Failed to flush cache.');
        }

        return \Yii::$app->getResponse()->redirect(Yii::$app->getRequest()->referrer);
    }

    public static function recursiveDelete($path)
    {
        if (is_file($path)) {
            return @unlink($path);
        } elseif (is_dir($path)) {
            $scan = glob(rtrim($path, '/') . '/*');
            foreach ($scan as $index => $newPath) {
                self::recursiveDelete($newPath);
            }
            return @rmdir($path);
        }
    }

    public function actionLinkRichMenu($userId,$richMenuId,$token)
    {
        try {
            // $userId = 'Ue5337b220743f592158018e2a0423ff3';
            // $richMenuId = 'richmenu-349a649ee1b2e2f659ae2da8e24df4ef';
            $dataRichMenu = '';
            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl('https://api.line.me/v2/bot/user/' . $userId . '/richmenu/' . $richMenuId)
                ->addHeaders(['content-type' => 'application/json'])
                ->addHeaders(['Authorization' => 'Bearer '.$token])
                ->send();
            if ($response->isOk) {
                $dataRichMenu = $response->data;
                print_r($dataRichMenu);
            }
        } catch (Exception $e) {
            print_r($e->getMessage());
        }

        //return $this->render('about');
    }
}
