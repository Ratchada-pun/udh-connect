<?php

namespace common\Line\EventHandler\MessageHandler\Flex;

use LINE\LINEBot\Constant\Flex\BubleContainerSize;
use LINE\LINEBot\Constant\Flex\ComponentAlign;
use LINE\LINEBot\Constant\Flex\ComponentBorderWidth;
use LINE\LINEBot\Constant\Flex\ComponentButtonHeight;
use LINE\LINEBot\Constant\Flex\ComponentButtonStyle;
use LINE\LINEBot\Constant\Flex\ComponentFontSize;
use LINE\LINEBot\Constant\Flex\ComponentFontWeight;
use LINE\LINEBot\Constant\Flex\ComponentGravity;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectMode;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectRatio;
use LINE\LINEBot\Constant\Flex\ComponentImageSize;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentSpaceSize;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BlockStyleBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BubbleStylesBuilder;
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
use yii\helpers\ArrayHelper;

class FlexDepartment
{
    /**
     * Create sample restaurant flex message
     *
     * @return \LINE\LINEBot\MessageBuilder\FlexMessageBuilder
     */
    public static function get()
    {
        return FlexMessageBuilder::builder()
            ->setAltText('นัดหมายแพทย์')
            ->setContents(
                BubbleContainerBuilder::builder()
                    ->setHero(self::createHeroBlock())
                    ->setBody(self::createBodyBlock())
                    ->setFooter(self::createFooterBlock())
                   // ->setSize(BubleContainerSize::GIGA)  // ขนาด flex
                    ->setSize(BubleContainerSize::MEGA)
                    ->setStyles(
                        BubbleStylesBuilder::builder()
                            ->setHero(
                                BlockStyleBuilder::builder()
                                    ->setBackgroundColor('#fce4ec')
                            )
                    )
            );
    }


    private static function createHeroBlock()
    {
        return ImageComponentBuilder::builder()
            ->setUrl('https://udhconnect.info/images/udh_logo.png')
            ->setSize("lg")
            ->setAspectRatio(ComponentImageAspectRatio::R20TO13)
            ->setAspectMode(ComponentImageAspectMode::FIT)
            ->setOffsetTop("sm");
            
    }

    private static function createBodyBlock()
    {
        $title = TextComponentBuilder::builder()
            ->setText('เลือกแผนกที่ต้องการนัดหมาย')
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::LG)
            ->setAlign(ComponentAlign::START);

        $separator = SeparatorComponentBuilder::builder()
            ->setMargin(ComponentMargin::XXL);

        $contents = [];

        $DeptGroups = Yii::$app->mssql->createCommand( //รายชื่อแผนกหลัก
            'SELECT
                REPLACE(dbo.DEPTGr.DeptGroup, \' \', \'\') as DeptGroup,
                REPLACE(dbo.DEPTGr.DeptGrDesc, \' \', \'\') as DeptGrDesc
            FROM
                dbo.DEPTGROUP
                INNER JOIN dbo.DEPTGr ON dbo.DEPTGr.DeptGroup = dbo.DEPTGROUP.DeptGroup 
                '
        )->queryAll();

        $boxImageContents = [];
        $boxTextContents = [];

        $items = [];
        $ids = [];
        foreach ($DeptGroups as $key => $item) {
            if (!ArrayHelper::isIn($item['DeptGroup'], $ids)) {
                $ids[] = $item['DeptGroup'];
                $items[] = $item;
            }
        }


        foreach ($items as $key => $item) {
            $boxImageContents[] = BoxComponentBuilder::builder()
                ->setLayout(ComponentLayout::VERTICAL)
                ->setCornerRadius('20px')
                ->setBorderWidth(ComponentBorderWidth::MEDIUM)
                ->setContents([
                    ImageComponentBuilder::builder()
                        ->setUrl('https://www.udhconnect.info/images/menu3.png')
                        ->setSize(ComponentImageSize::LG)
                        ->setAspectMode(ComponentImageAspectMode::COVER)
                        ->setAlign(ComponentAlign::CENTER)
                        ->setGravity(ComponentGravity::CENTER)

                ])
                ->setAction(
                    new UriTemplateActionBuilder(
                        'เลือกแผนกอื่นๆ >',
                        'https://liff.line.me/1654023325-EkWmY9PA/app/appoint/create-sub-department?id=' . $item['DeptGroup'],
                        new AltUriBuilder('https://liff.line.me/1654023325-EkWmY9PA/app/appoint/create-sub-department?id=' . $item['DeptGroup'])
                    )
                );

            $boxTextContents[]   = TextComponentBuilder::builder()
                ->setText($item['DeptGrDesc'])
                ->setSize(ComponentFontSize::SM)
                ->setAlign(ComponentAlign::CENTER)
                ->setColor('#aaaaaa')
                ->setGravity(ComponentGravity::TOP)
                ->setMargin(ComponentMargin::XS)
                ->setWrap(true)
                ->setWeight(ComponentFontWeight::BOLD)
                ->setAction(
                    new UriTemplateActionBuilder(
                        'เลือกแผนกอื่นๆ >',
                        'https://liff.line.me/1654023325-EkWmY9PA/app/appoint/create-sub-department?id='.$item['DeptGroup'],
                        new AltUriBuilder('https://liff.line.me/1654023325-EkWmY9PA/app/appoint/create-sub-department?id='.$item['DeptGroup'])
                    )
                );

            if (count($boxImageContents) === 3 || $key + 1 === count($items)) {
                $boximage =  BoxComponentBuilder::builder()
                    ->setLayout(ComponentLayout::HORIZONTAL)
                    ->setContents($boxImageContents);
                $contents[] = $boximage;
                $boxImageContents = [];
            }
            if (count($boxTextContents) === 3 || $key + 1 === count($items)) {
                $boxtext =  BoxComponentBuilder::builder()
                    ->setLayout(ComponentLayout::HORIZONTAL)
                    ->setContents($boxTextContents);
                $contents[] = $boxtext;
                $boxTextContents = [];
            }
        }

        // for ($i = 0; $i < 2; $i++) {
        //     $boximage =  BoxComponentBuilder::builder()
        //         ->setLayout(ComponentLayout::HORIZONTAL)
        //         ->setContents([
        //             BoxComponentBuilder::builder()
        //                 ->setLayout(ComponentLayout::VERTICAL)
        //                 ->setCornerRadius('20px')
        //                 ->setBorderWidth(ComponentBorderWidth::MEDIUM)
        //                 ->setContents([
        //                     ImageComponentBuilder::builder()
        //                         ->setUrl('https://www.udhconnect.info/images/iconfinder_3_hospital_2774749.png')
        //                         ->setSize(ComponentImageSize::FULL)
        //                         ->setAspectMode(ComponentImageAspectMode::COVER)
        //                         ->setAlign(ComponentAlign::CENTER)
        //                         ->setGravity(ComponentGravity::CENTER)
        //                 ]),
        //             BoxComponentBuilder::builder()
        //                 ->setLayout(ComponentLayout::VERTICAL)
        //                 ->setCornerRadius('20px')
        //                 ->setBorderWidth(ComponentBorderWidth::MEDIUM)
        //                 ->setContents([
        //                     ImageComponentBuilder::builder()
        //                         ->setUrl('https://www.udhconnect.info/images/iconfinder_3_hospital_2774749.png')
        //                         ->setSize(ComponentImageSize::FULL)
        //                         ->setAspectMode(ComponentImageAspectMode::COVER)
        //                         ->setAlign(ComponentAlign::CENTER)
        //                         ->setGravity(ComponentGravity::CENTER)
        //                 ]),
        //             BoxComponentBuilder::builder()
        //                 ->setLayout(ComponentLayout::VERTICAL)
        //                 ->setCornerRadius('20px')
        //                 ->setBorderWidth(ComponentBorderWidth::MEDIUM)
        //                 ->setContents([
        //                     ImageComponentBuilder::builder()
        //                         ->setUrl('https://www.udhconnect.info/images/iconfinder_3_hospital_2774749.png')
        //                         ->setSize(ComponentImageSize::FULL)
        //                         ->setAspectMode(ComponentImageAspectMode::COVER)
        //                         ->setAlign(ComponentAlign::CENTER)
        //                         ->setGravity(ComponentGravity::CENTER)
        //                 ])
        //         ]);
        //     $contents[] = $boximage;

        //     $box_text =  BoxComponentBuilder::builder()
        //         ->setLayout(ComponentLayout::HORIZONTAL)
        //         ->setContents([
        //             TextComponentBuilder::builder()
        //                 ->setText('แผนกอุบัติเหตุฉุกเฉิน')
        //                 ->setSize(ComponentFontSize::SM)
        //                 ->setAlign(ComponentAlign::CENTER)
        //                 ->setColor('#aaaaaa')
        //                 ->setGravity(ComponentGravity::TOP)
        //                 ->setMargin(ComponentMargin::XS)
        //                 ->setWrap(true),
        //             TextComponentBuilder::builder()
        //                 ->setText('แผนกศัลยกรรมกระดูก')
        //                 ->setSize(ComponentFontSize::SM)
        //                 ->setAlign(ComponentAlign::CENTER)
        //                 ->setColor('#aaaaaa')
        //                 ->setGravity(ComponentGravity::TOP)
        //                 ->setMargin(ComponentMargin::XS)
        //                 ->setWrap(true),
        //             TextComponentBuilder::builder()
        //                 ->setText('แผนกกุมารเวชกรรม')
        //                 ->setSize(ComponentFontSize::SM)
        //                 ->setAlign(ComponentAlign::CENTER)
        //                 ->setColor('#aaaaaa')
        //                 ->setGravity(ComponentGravity::TOP)
        //                 ->setMargin(ComponentMargin::XS)
        //                 ->setWrap(true),
        //         ]);
        //     $contents[] = $box_text;

        //     $box_separator = SeparatorComponentBuilder::builder()
        //         ->setColor('#905c44')
        //         ->setMargin(ComponentMargin::SM);
        //     $contents[] = $box_separator;
        // }


        $review = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setContents($contents);

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setContents([$title, $separator,  $review]);
    }

    private static function createFooterBlock()
    {
        $separator = SeparatorComponentBuilder::builder()
            ->setColor('#905c44')
            ->setMargin(ComponentMargin::SM);

        $button = ButtonComponentBuilder::builder()
            ->setStyle(ComponentButtonStyle::LINK)
            ->setHeight(ComponentButtonHeight::SM)
            ->setMargin(ComponentMargin::XS)
            ->setAction(
                new UriTemplateActionBuilder(
                    'เลือกแผนกอื่นๆ >',
                    'https://liff.line.me/1654023325-EkWmY9PA/app/appoint/create-department',
                    new AltUriBuilder('https://liff.line.me/1654023325-EkWmY9PA/app/appoint/create-department')
                )
            );
        // $spacer = new SpacerComponentBuilder(ComponentSpaceSize::XXL);

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setContents([$separator, $button]);
    }
}
