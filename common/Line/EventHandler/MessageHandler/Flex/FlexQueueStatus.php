<?php

namespace common\Line\EventHandler\MessageHandler\Flex;

use LINE\LINEBot\Constant\Flex\BubleContainerSize;
use LINE\LINEBot\Constant\Flex\ComponentAlign;
use LINE\LINEBot\Constant\Flex\ComponentButtonHeight;
use LINE\LINEBot\Constant\Flex\ComponentButtonStyle;
use LINE\LINEBot\Constant\Flex\ComponentFontSize;
use LINE\LINEBot\Constant\Flex\ComponentFontWeight;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectMode;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectRatio;
use LINE\LINEBot\Constant\Flex\ComponentImageSize;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentSpaceSize;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\MessageBuilder\Flex\BlockStyleBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BubbleStylesBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\SeparatorComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\SpacerComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\Uri\AltUriBuilder;
use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

class FlexQueueStatus extends BaseObject
{
    public $userId;
    public $hn;
    public $items = [];

    // private $_items = [];

    public function __construct($config = [])
    {
        // ... initialization before configuration is applied
        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
        // $this->_items = $this->getDataItems();
        // [
        //     [
        //         'queue_no' => 'A001',
        //         'doctor_name' => 'แพทย์ ทดสอบ1',
        //         'department' => 'ศัลยกรรม',
        //         'queue_date' => Yii::$app->formatter->asDate('now', 'php: d M Y'),
        //         'queue_time' => '10:00 น.',
        //         'counter_service_no' => '1',
        //     ],
        //     [
        //         'queue_no' => 'B001',
        //         'doctor_name' => 'แพทย์ ทดสอบ2',
        //         'department' => 'ศัลยกรรมทั่วไป',
        //         'queue_date' => Yii::$app->formatter->asDate('now', 'php: d M Y'),
        //         'queue_time' => '11:00 น.',
        //         'counter_service_no' => '2',
        //     ],
        // ];
    }

    /**
     * Create sample shopping flex message
     *
     * @return \LINE\LINEBot\MessageBuilder\FlexMessageBuilder
     */
    public function get()
    {
        return FlexMessageBuilder::builder()
            ->setAltText('สถานะคิว')
            ->setContents(
                BubbleContainerBuilder::builder()
                    ->setHero($this->createHeroBlock())
                    ->setBody($this->createBodyBlock())
                    ->setFooter($this->createFooterBlock())
                    ->setSize(BubleContainerSize::MEGA)
                    ->setStyles(
                        BubbleStylesBuilder::builder()
                            ->setHero(
                                BlockStyleBuilder::builder()
                                    ->setBackgroundColor('#fafafa')
                            )
                    )
            );
    }

    private function createHeroBlock()
    {
        return ImageComponentBuilder::builder()
            ->setUrl('https://udhconnect.info/images/udh_logo.png')
            ->setSize(ComponentImageSize::LG)
            ->setAspectRatio(ComponentImageAspectRatio::R20TO13)
            ->setAspectMode(ComponentImageAspectMode::FIT);
    }

    private function createBodyBlock()
    {
        // รายละเอียดคิวของคุณ
        $title = TextComponentBuilder::builder()
            ->setText('รายการคิวของคุณ')
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::SM)
            ->setAlign(ComponentAlign::CENTER)
            ->setColor('#1DB446');

        $titlehn = TextComponentBuilder::builder()
            ->setText('HN ' . $this->hn)
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::LG)
            ->setAlign(ComponentAlign::CENTER)
            ->setColor('#aaaaaa');

        $separator = SeparatorComponentBuilder::builder()->setMargin(ComponentMargin::LG);

        $bodyContents = [];

        foreach ($this->items as $key => $item) {
            // หมายเลขคิว
            $queue = TextComponentBuilder::builder()
                ->setText('คิว ' . $item['queue_no'])
                ->setWeight(ComponentFontWeight::BOLD)
                ->setSize(ComponentFontSize::XL)
                ->setColor('#ec407a');
            $bodyContents[] = $queue;

            // แพทย์
            $doctor = BoxComponentBuilder::builder()
                ->setLayout(ComponentLayout::BASELINE)
                ->setContents([
                    TextComponentBuilder::builder()
                        ->setText('แพทย์:')
                        ->setWeight(ComponentFontWeight::BOLD)
                        ->setMargin(ComponentMargin::SM)
                        ->setFlex(0),
                    TextComponentBuilder::builder()
                        ->setText(!empty($item['doctor_name']) ? $item['doctor_name'] : '-')
                        ->setSize(ComponentFontSize::SM)
                        ->setAlign(ComponentAlign::END)
                        ->setColor('#aaaaaa'),
                ]);
            $bodyContents[] = $doctor;

            // แผนก
            $department = BoxComponentBuilder::builder()
                ->setLayout(ComponentLayout::BASELINE)
                ->setContents([
                    TextComponentBuilder::builder()
                        ->setText('แผนก:')
                        ->setWeight(ComponentFontWeight::BOLD)
                        ->setMargin(ComponentMargin::SM)
                        ->setFlex(0),
                    TextComponentBuilder::builder()
                        ->setText($item['department'])
                        ->setSize(ComponentFontSize::SM)
                        ->setAlign(ComponentAlign::END)
                        ->setColor('#aaaaaa'),
                ]);
            $bodyContents[] = $department;

            // วันที่
            $appointdate = BoxComponentBuilder::builder()
                ->setLayout(ComponentLayout::BASELINE)
                ->setContents([
                    TextComponentBuilder::builder()
                        ->setText('วันที่:')
                        ->setWeight(ComponentFontWeight::BOLD)
                        ->setMargin(ComponentMargin::SM)
                        ->setFlex(0),
                    TextComponentBuilder::builder()
                        ->setText($item['queue_date'])
                        ->setSize(ComponentFontSize::SM)
                        ->setAlign(ComponentAlign::END)
                        ->setColor('#aaaaaa'),
                ]);
            $bodyContents[] = $appointdate;

            // เวลา
            $appointtime = BoxComponentBuilder::builder()
                ->setLayout(ComponentLayout::BASELINE)
                ->setContents([
                    TextComponentBuilder::builder()
                        ->setText('เวลา:')
                        ->setWeight(ComponentFontWeight::BOLD)
                        ->setMargin(ComponentMargin::SM)
                        ->setFlex(0),
                    TextComponentBuilder::builder()
                        ->setText($item['queue_time'] . ' น.')
                        ->setSize(ComponentFontSize::SM)
                        ->setAlign(ComponentAlign::END)
                        ->setColor('#aaaaaa'),
                ]);
            $bodyContents[] = $appointtime;

            // ห้องตรวจ
            $counter = BoxComponentBuilder::builder()
                ->setLayout(ComponentLayout::BASELINE)
                ->setContents([
                    TextComponentBuilder::builder()
                        ->setText('ห้องตรวจ/ช่องบริการ:')
                        ->setWeight(ComponentFontWeight::BOLD)
                        ->setMargin(ComponentMargin::SM)
                        ->setFlex(0),
                    TextComponentBuilder::builder()
                        ->setText(!empty($item['counter_service_name']) ? $item['counter_service_name'] : '-')
                        ->setSize(ComponentFontSize::SM)
                        ->setAlign(ComponentAlign::END)
                        ->setColor('#aaaaaa'),
                ]);
            $bodyContents[] = $counter;

            // สถานะคิว
            $status = BoxComponentBuilder::builder()
                ->setLayout(ComponentLayout::BASELINE)
                ->setContents([
                    TextComponentBuilder::builder()
                        ->setText('สถานะคิว:')
                        ->setWeight(ComponentFontWeight::BOLD)
                        ->setMargin(ComponentMargin::SM)
                        ->setFlex(0),
                    TextComponentBuilder::builder()
                        ->setText(!empty($item['queue_status_name']) ? $item['queue_status_name'] : '-')
                        ->setSize(ComponentFontSize::SM)
                        ->setAlign(ComponentAlign::END)
                        ->setColor($item['queue_status_id'] == 4 ? '#1DB446' : '#aaaaaa'),
                ]);
            $bodyContents[] = $status;
            if ($key + 1 < count($this->items)) {
                $bodyContents[] = $separator;
            }
        }
        $review = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setMargin(ComponentMargin::MD)
            ->setContents($bodyContents);

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            // ->setPaddingAll('8%')
            ->setPaddingTop('xs')
            ->setContents([$titlehn, $title, $review]);
    }

    private function createFooterBlock()
    {
        $contact = TextComponentBuilder::builder()
            ->setText('ติดต่อสอบถามเจ้าหน้าที่ โทร 042-245-555')
            ->setSize(ComponentFontSize::XXS)
            ->setColor('#aaaaaa')
            ->setWrap(true)
            ->setAlign(ComponentAlign::CENTER)
            ->setMargin(ComponentMargin::NONE)
            ->setOffsetBottom('sm');

        $button = ButtonComponentBuilder::builder()
            ->setStyle(ComponentButtonStyle::PRIMARY)
            ->setHeight(ComponentButtonHeight::SM)
            ->setColor('#ec407a')
            ->setAction(
                new UriTemplateActionBuilder(
                    'ติดตามสถานะคิว',
                    'https://liff.line.me/1654023325-EkWmY9PA/app/appoint/queue-status?hn=' . $this->hn,
                    new AltUriBuilder('https://liff.line.me/1654023325-EkWmY9PA/app/appoint/queue-status?hn=' . $this->hn)
                )
            );
        // $spacer = new SpacerComponentBuilder(ComponentSpaceSize::XXL);

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setSpacing(ComponentSpacing::SM)
            ->setFlex(0)
            ->setBackgroundColor('#fafafa')
            // ->setBorderColor('#e0e0e0')
            ->setBorderWidth('1px')
            ->setContents([$contact, $button]);
    }

    public static function getDataItems($hn)
    {
        $startDate = Yii::$app->formatter->asDate('now', 'php:Y-m-d 00:00:00');
        $endDate = Yii::$app->formatter->asDate('now', 'php:Y-m-d 23:59:59');
        $couters = (new \yii\db\Query())
            ->select(['tbl_counter_service.*'])
            ->from('tbl_counter_service')
            ->all(Yii::$app->db_queue);
        $map_couters = ArrayHelper::map($couters, 'counter_service_id', 'counter_service_name');
        $rows = (new \yii\db\Query())
            ->select([
                'tbl_queue.queue_no',
                'tbl_doctor.doctor_title',
                'tbl_doctor.doctor_name',
                'tbl_service.service_name as department',
                'DATE_FORMAT(tbl_queue.created_at,\'%d %M %Y\') as queue_date',
                'TIME_FORMAT(tbl_queue_detail.created_at,\'%H:%i\') as queue_time',
                'tbl_counter_service.counter_service_no',
                'tbl_counter_service.counter_service_name',
                'tbl_queue_status.queue_status_id',
                'tbl_queue_status.queue_status_name',
                'tbl_queue.created_at',
                'tbl_queue_detail.counter_service_id as counter_service_id1'
            ])
            ->from('tbl_queue_detail')
            ->innerJoin('tbl_queue', 'tbl_queue.queue_id = tbl_queue_detail.queue_id')
            ->innerJoin('tbl_service', 'tbl_service.service_id = tbl_queue_detail.service_id')
            ->innerJoin('tbl_service_group', 'tbl_service_group.service_group_id = tbl_service.service_group_id')
            ->innerJoin('tbl_queue_type', 'tbl_queue_type.queue_type_id = tbl_queue.queue_type_id')
            ->innerJoin('tbl_service_type', 'tbl_service_type.service_type_id = tbl_queue_detail.service_type_id')
            ->innerJoin('tbl_coming_type', 'tbl_coming_type.coming_type_id = tbl_queue.coming_type_id')
            ->innerJoin('tbl_queue_status', 'tbl_queue_status.queue_status_id = tbl_queue_detail.queue_status_id')
            ->leftJoin('tbl_doctor', 'tbl_doctor.doctor_id = tbl_queue_detail.doctor_id')
            ->leftJoin('tbl_caller', 'tbl_queue_detail.queue_detail_id = tbl_caller.queue_detail_id')
            ->leftJoin('tbl_counter_service', 'tbl_caller.counter_service_id = tbl_counter_service.counter_service_id')
            ->leftJoin('tbl_appoint', 'tbl_appoint.appoint_id = tbl_queue.appoint_id')
            ->innerJoin('`profile`', '`profile`.user_id = tbl_queue.created_by')
            ->innerJoin('tbl_patient', 'tbl_patient.patient_id = tbl_queue.patient_id')
            ->leftJoin('file_storage_item', 'file_storage_item.ref_id = tbl_patient.patient_id')
            ->where(['tbl_patient.hn' => $hn])
            ->andWhere(['between', 'tbl_queue_detail.created_at', $startDate, $endDate])
            ->andWhere('tbl_queue_detail.queue_status_id <> :queue_status_id', [':queue_status_id' => 5])
            ->groupBy('tbl_queue_detail.queue_detail_id')
            ->orderBy('tbl_queue.created_at ASC')
            ->all(Yii::$app->db_queue);

        $items = [];
        foreach ($rows as $key => $item) {
            $items[] = ArrayHelper::merge($item, [
                'queue_date' => Yii::$app->formatter->asDate($item['created_at'], 'php:d M ') . (Yii::$app->formatter->asDate($item['created_at'], 'php:Y') + 543),
                'counter_service_name' => empty($item['counter_service_id1']) ? $item['counter_service_name'] : ArrayHelper::getValue($map_couters, $item['counter_service_id1'], '')
            ]);
        }
        return $items;
    }
}
