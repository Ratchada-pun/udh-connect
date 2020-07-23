<?php
namespace app\controllers;

use app\models\TblPatient;
use common\Line\EventHandler\FollowEventHandler;
use common\Line\EventHandler\MessageHandler\AudioMessageHandler;
use common\Line\EventHandler\MessageHandler\Flex\FlexContact;
use common\Line\EventHandler\MessageHandler\Flex\FlexDepartment;
use common\Line\EventHandler\MessageHandler\Flex\FlexQueueStatus;
use common\Line\EventHandler\MessageHandler\Flex\FlexRegister;
use common\Line\EventHandler\MessageHandler\Flex\FlexRegisterSuccess;
use common\Line\EventHandler\MessageHandler\Flex\FlexSampleRestaurant;
use common\Line\EventHandler\MessageHandler\Flex\FlexSampleShopping;
use common\Line\EventHandler\MessageHandler\ImageMessageHandler;
use common\Line\EventHandler\MessageHandler\LocationMessageHandler;
use common\Line\EventHandler\MessageHandler\StickerMessageHandler;
use common\Line\EventHandler\MessageHandler\TextMessageHandler;
use common\Line\EventHandler\MessageHandler\VideoMessageHandler;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\AudioMessage;
use LINE\LINEBot\Event\MessageEvent\ImageMessage;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\UnknownMessage;
use LINE\LINEBot\Event\MessageEvent\VideoMessage;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\HttpException;

class LineController extends Controller
{
   // Line หลัก
    const LINE_SIGNATURE = 'X-Line-Signature';
    const LINEBOT_CHANNEL_TOKEN = 'FWZ3P4fRrEXOmhyQtiQFp+TXeSSrkQwGdt3zvp1TezV9gYOruopsbo4YDBjoIKSoWzd/Yx/Ow/8xT0Elwvv6N+akUpPXtdMOdi5NN+t8BMHiVFWoDopJLEn0fUJSg0Rink0gBjXMSwcKIoI6FmoaQQdB04t89/1O/w1cDnyilFU=';
    const LINEBOT_CHANNEL_SECRET = '4950aef914a00bbaa4bf69850e001e1f';



    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['webhook'],
                        'allow' => true,
                        'roles' => ['@', '?'],
                    ],
                    [
                        'actions' => ['flex-message'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'webhook' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if ($action->id == 'webhook') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionWebhook()
    {
        $request = Yii::$app->request;
        $headers = $request->headers;

        /** @var \Monolog\Logger $logger */
        $logger = new Logger('line');
        $logger->pushProcessor(new UidProcessor());
        $logger->pushHandler(new RotatingFileHandler(Yii::getAlias('@runtime/logs/line.log'), 0, Logger::DEBUG));

        $httpClient = new CurlHTTPClient(self::LINEBOT_CHANNEL_TOKEN);
        /** @var LINEBot $bot */
        $bot = new LINEBot($httpClient, ['channelSecret' => self::LINEBOT_CHANNEL_SECRET]);

        $signature = $headers->get(self::LINE_SIGNATURE);
        if (empty($signature)) {
            $logger->info('Signature is missing');
            throw new BadRequestHttpException('Signature is missing');
        }

        try {
            $events = $bot->parseEventRequest($request->getRawBody(), $signature);
        } catch (InvalidSignatureException $e) {
            $logger->info($e->getMessage());
            throw new HttpException(400, $e->getMessage());
        } catch (InvalidEventRequestException $e) {
            $logger->info($e->getMessage());
            throw new HttpException(400, $e->getMessage());
        }

        foreach ($events as $event) {
            /** @var EventHandler $handler */
            $handler = null;
            if ($event instanceof TextMessage) {
                $handler = new TextMessageHandler($bot, $logger, $request, $event);
            } elseif ($event instanceof StickerMessage) {
                $handler = new StickerMessageHandler($bot, $logger, $event);
            } elseif ($event instanceof LocationMessage) {
                $handler = new LocationMessageHandler($bot, $logger, $event);
            } elseif ($event instanceof ImageMessage) {
                $handler = new ImageMessageHandler($bot, $logger, $request, $event);
            } elseif ($event instanceof AudioMessage) {
                $handler = new AudioMessageHandler($bot, $logger, $request, $event);
            } elseif ($event instanceof VideoMessage) {
                $handler = new VideoMessageHandler($bot, $logger, $request, $event);
            }elseif($event instanceof FollowEvent){
                $handler = new FollowEventHandler($bot, $logger, $event);
            }
             elseif ($event instanceof UnknownMessage) {
                $logger->info(sprintf(
                    'Unknown message type has come [message type: %s]',
                    $event->getMessageType()
                ));
            } else {
                // Unexpected behavior (just in case)
                // something wrong if reach here
                $logger->info(sprintf(
                    'Unexpected event type has come, something wrong [class name: %s]',
                    get_class($event)
                ));
                continue;
            }

            $handler->handle();
        }
        return 'OK';
    }

    public function actionFlexMessage()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        // $flexMessageBuilder = FlexSampleShopping::get();
        // $flexMessageBuilder = FlexSampleRestaurant::get();
        // $flexMessageBuilder = FlexQueueStatus::get();
        // return $flexMessageBuilder->buildMessage();
        // $items = FlexQueueStatus::getDataItems('327442');
        // $component = \Yii::createObject([
        //     'class' => FlexQueueStatus::className(),
        //     'userId' => 3,
        //     'hn' => '327442',
        //     'items' => $items
        // ]);
        // $flexMessageBuilder = $component->get();
        // $flexMessageBuilder = FlexRegister::get();
        $model = TblPatient::findOne(['line_id' => 'Ue5337b220743f592158018e2a0423ff3']);
        $flexMessageBuilder = FlexRegisterSuccess::get($model);
        // $flexMessageBuilder = FlexDepartment::get();
        return $flexMessageBuilder->buildMessage();
    }


}
