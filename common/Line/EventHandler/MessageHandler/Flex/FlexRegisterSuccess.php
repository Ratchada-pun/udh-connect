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
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BlockStyleBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BubbleStylesBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\SpacerComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\Uri\AltUriBuilder;
use yii\base\Component;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FlexRegisterSuccess
{
    /**
     * Create sample restaurant flex message
     *
     * @return \LINE\LINEBot\MessageBuilder\FlexMessageBuilder
     */


    public static function get($profile)
    {
        return FlexMessageBuilder::builder()
            ->setAltText('ลงทะเบียนผู้ป่วย')
            ->setContents(
                BubbleContainerBuilder::builder()
                    ->setHero(self::createHeroBlock())
                    ->setBody(self::createBodyBlock($profile))
                    ->setFooter(self::createFooterBlock())
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
            ->setSize("3xl")
            ->setAspectRatio(ComponentImageAspectRatio::R20TO13)
            ->setAspectMode(ComponentImageAspectMode::FIT)
            ->setOffsetTop("sm");
            
    }


    private static function createBodyBlock($profile)
    {

        $title = TextComponentBuilder::builder()
            ->setText('UDH Connect')
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::XL)
            ->setAlign(ComponentAlign::CENTER);

        $review = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setMargin(ComponentMargin::LG)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([
                TextComponentBuilder::builder()
                    ->setText('ลงทะเบียนสำเร็จ!')
                    ->setSize(ComponentFontSize::XXL)
                    ->setColor('#1DB446')
                    ->setAlign(ComponentAlign::CENTER)
            ]);

        $profile = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setMargin(ComponentMargin::XXL)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([
                BoxComponentBuilder::builder()
                    ->setLayout(ComponentLayout::HORIZONTAL)
                    ->setContents([
                        TextComponentBuilder::builder()
                            ->setText('ชื่อ-นามสุกล')
                            ->setColor('#aaaaaa')
                            ->setSize(ComponentFontSize::MD)
                            ->setFlex(0),
                        TextComponentBuilder::builder()
                            ->setText($profile['first_name'].' '.$profile['last_name'])
                            ->setWrap(true)
                            ->setColor('#666666')
                            ->setSize(ComponentFontSize::MD)
                            ->setAlign(ComponentAlign::END)
                    ]),
                BoxComponentBuilder::builder()
                    ->setLayout(ComponentLayout::HORIZONTAL)
                    ->setContents([
                        TextComponentBuilder::builder()
                            ->setText('ประเภท')
                            ->setColor('#aaaaaa')
                            ->setSize(ComponentFontSize::MD)
                            ->setFlex(0),
                        TextComponentBuilder::builder()
                            ->setText($profile['user_type'] == 'new' ? 'ผู้ป่วยใหม่' : 'ผู้ป่วยเก่า')
                            ->setWrap(true)
                            ->setColor('#666666')
                            ->setSize(ComponentFontSize::MD)
                            ->setAlign(ComponentAlign::END)
                    ]),
                BoxComponentBuilder::builder()
                    ->setLayout(ComponentLayout::HORIZONTAL)
                    ->setContents([
                        TextComponentBuilder::builder()
                            ->setText('โทรศัพท์')
                            ->setColor('#aaaaaa')
                            ->setSize(ComponentFontSize::MD)
                            ->setFlex(0),
                        TextComponentBuilder::builder()
                            ->setText(empty($profile['phone_number']) ? '-' : $profile['phone_number'])
                            ->setWrap(true)
                            ->setColor('#666666')
                            ->setSize(ComponentFontSize::MD)
                            ->setAlign(ComponentAlign::END)
                    ]),
                BoxComponentBuilder::builder()
                    ->setLayout(ComponentLayout::HORIZONTAL)
                    ->setContents([
                        TextComponentBuilder::builder()
                            ->setText('วันที่ลงทะเบียน')
                            ->setColor('#aaaaaa')
                            ->setSize(ComponentFontSize::MD)
                            ->setFlex(0),
                        TextComponentBuilder::builder()
                            ->setText(\Yii::$app->formatter->asDate($profile['created_at'], 'php:d-m-Y'))
                            ->setWrap(true)
                            ->setColor('#666666')
                            ->setSize(ComponentFontSize::MD)
                            ->setAlign(ComponentAlign::END)
                    ]),

            ]);


        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            // ->setBackgroundColor('#fafafa')
            // ->setPaddingAll('8%')
            ->setContents([$title, $review, $profile]);
    }

    // private static function createFooterBlock()
    // {
    //     $text = ButtonComponentBuilder::builder()
    //     ->setStyle(ComponentButtonStyle::PRIMARY)
    //     ->setHeight(ComponentButtonHeight::SM)
    //     ->setAction(
    //         new MessageTemplateActionBuilder(
    //             'ไปที่นัดหมายแพทย์',
    //             'นัดหมายแพทย์'
    //         )
    //     );

    //     $spacer = new SpacerComponentBuilder(ComponentSpaceSize::SM);

    //     return BoxComponentBuilder::builder()
    //         ->setLayout(ComponentLayout::VERTICAL)
    //         ->setSpacing(ComponentSpacing::MD)
    //         ->setContents([$text, $spacer]);

    // }


    // private static function createFooterBlock()
    // {
    //     $text = ButtonComponentBuilder::builder()
    //     ->setStyle(ComponentButtonStyle::PRIMARY)
    //     ->setHeight(ComponentFontSize::MD)
    //     ->setAction(
    //         new MessageTemplateActionBuilder('นัดหมายแพทย์','นัดหมาย')
    //     );

    //     $spacer = new SpacerComponentBuilder(ComponentSpaceSize::SM);

    //     return BoxComponentBuilder::builder()
    //         ->setLayout(ComponentLayout::VERTICAL)
    //         ->setSpacing(ComponentSpacing::SM)
    //         ->setFlex(0)
    //         ->setContents([$text, $spacer]);
    // }
}
