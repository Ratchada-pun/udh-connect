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
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BlockStyleBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BubbleStylesBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\SpacerComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\Uri\AltUriBuilder;

class FlexContact
{
    /**
     * Create sample restaurant flex message
     *
     * @return \LINE\LINEBot\MessageBuilder\FlexMessageBuilder
     */
    public static function get()
    {
        return FlexMessageBuilder::builder()
            ->setAltText('ลงทะเบียนผู้ป่วย')
            ->setContents(
                BubbleContainerBuilder::builder()
                    ->setHero(self::createHeroBlock())
                    ->setBody(self::createBodyBlock())
                    ->setFooter(self::createFooterBlock())
                    ->setSize(BubleContainerSize::MEGA)
                    // ->setStyles(
                    //     BubbleStylesBuilder::builder()
                    //         ->setHero(
                    //             BlockStyleBuilder::builder()
                    //                 ->setBackgroundColor('#fafafa')
                    //         )
                    // )
            );
    }

    private static function createHeroBlock()
    {
        return ImageComponentBuilder::builder()
            ->setUrl('https://udhconnect.info/images/call-center.png')
            ->setSize(ComponentImageSize::FULL)
            ->setAspectRatio(ComponentImageAspectRatio::R20TO13)
            ->setAspectMode(ComponentImageAspectMode::FIT)
            ->setOffsetTop("sm");
    }

    private static function createBodyBlock()
    {
        $title = TextComponentBuilder::builder()
            ->setText('ติดต่อสอบถาม')
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::XL)
            ->setAlign(ComponentAlign::CENTER);

        $review = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setMargin(ComponentMargin::LG)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([
                BoxComponentBuilder::builder()
                    ->setLayout(ComponentLayout::BASELINE)
                    ->setSpacing(ComponentSpacing::SM)
                    ->setContents([
                        TextComponentBuilder::builder()
                            ->setText('ที่อยู่')
                            ->setSize(ComponentFontSize::SM)
                            ->setColor('#aaaaaa')
                            ->setFlex(1),
                        TextComponentBuilder::builder()
                            ->setText('33 ถ.เพาะนิยม ต.หมากแข้ง อำเภอเมืองอุดรธานี จังหวัดอุดรธานี')
                            ->setWrap(true)
                            ->setSize(ComponentFontSize::XS)
                            ->setColor('#666666')
                            ->setFlex(5),
                    ]),

                BoxComponentBuilder::builder()
                    ->setLayout(ComponentLayout::BASELINE)
                    ->setSpacing(ComponentSpacing::SM)
                    ->setContents([
                        TextComponentBuilder::builder()
                            ->setText('Tel')
                            ->setSize(ComponentFontSize::SM)
                            ->setColor('#aaaaaa')
                            ->setFlex(1),
                        TextComponentBuilder::builder()
                            ->setText('042-245-555')
                            ->setWrap(true)
                            ->setSize(ComponentFontSize::XS)
                            ->setColor('#666666')
                            ->setFlex(5),
                    ])
            ]);

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            // ->setBackgroundColor('#fafafa')
            // ->setPaddingAll('8%')
            ->setContents([$title,  $review]);
    }

    private static function createFooterBlock()
    {
        $oldButton = ButtonComponentBuilder::builder()
            ->setStyle(ComponentButtonStyle::PRIMARY)
            ->setHeight(ComponentButtonHeight::SM)
            ->setAction(
                new UriTemplateActionBuilder(
                    'CALL',
                    'tel:042245555',
                    new AltUriBuilder('tel:042245555')
                )
            );
        $spacer = new SpacerComponentBuilder(ComponentSpaceSize::SM);

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setSpacing(ComponentSpacing::SM)
            ->setFlex(0)
            ->setContents([$oldButton, $spacer]);
    }
}
