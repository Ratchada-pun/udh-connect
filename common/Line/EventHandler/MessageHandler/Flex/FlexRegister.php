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

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FlexRegister
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
                    ->setStyles(
                        BubbleStylesBuilder::builder()
                            ->setHero(
                                BlockStyleBuilder::builder()
                                    ->setBackgroundColor('#fafafa')
                            )
                    )
            );
    }

    private static function createHeroBlock()
    {
        return ImageComponentBuilder::builder()
            ->setUrl('https://udhconnect.info/images/udh_logo.png')
            ->setSize(ComponentImageSize::LG)
            ->setAspectRatio(ComponentImageAspectRatio::R20TO13)
            ->setAspectMode(ComponentImageAspectMode::FIT)
        ;
    }

    private static function createBodyBlock()
    {
        $title = TextComponentBuilder::builder()
            ->setText('ไม่พบข้อมูลการลงทะเบียน')
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::SM)
            ->setAlign(ComponentAlign::CENTER)
            ->setColor('#ef5350');

        $title2 = TextComponentBuilder::builder()
            ->setText('กรุณาลงทะเบียนเพื่อใช้งาน')
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::LG)
            ->setAlign(ComponentAlign::CENTER);

        $review = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
        // ->setMargin(ComponentMargin::LG)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([
                TextComponentBuilder::builder()
                    ->setText('หมายเหตุ')
                    ->setSize(ComponentFontSize::XS)
                    ->setColor('#aaaaaa'),
                TextComponentBuilder::builder()
                    ->setText('ผู้ป่วยเก่า หมายถึงผู้ป่วยที่เคยใช้บริการ')
                    ->setMargin(ComponentMargin::XS)
                    ->setWrap(true)
                    ->setOffsetStart('lg')
                    ->setSize(ComponentFontSize::XS)
                    ->setColor('#aaaaaa'),
            ]);

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
        // ->setBackgroundColor('#fafafa')
        // ->setPaddingAll('8%')
            ->setContents([$title, $title2, $review]);
    }

    private static function createFooterBlock()
    {
        $oldButton = ButtonComponentBuilder::builder()
            ->setStyle(ComponentButtonStyle::PRIMARY)
            ->setHeight(ComponentButtonHeight::SM)
            ->setAction(
                new UriTemplateActionBuilder(
                    'ลงทะเบียนผู้ป่วยเก่า',
                    'https://liff.line.me/1653428124-5P8ad2x3/app/register/create-new-user?user=old',
                    new AltUriBuilder('https://liff.line.me/1653428124-5P8ad2x3/app/register/create-new-user?user=old')
                )
            );
        $newButton = ButtonComponentBuilder::builder()
            ->setStyle(ComponentButtonStyle::PRIMARY)
            ->setHeight(ComponentButtonHeight::SM)
            ->setAction(
                new UriTemplateActionBuilder(
                    'ลงทะเบียนผู้ป่วยใหม่',
                    'https://liff.line.me/1653428124-5P8ad2x3/app/register/create-new-user?user=new',
                    new AltUriBuilder('https://liff.line.me/1653428124-5P8ad2x3/app/register/create-new-user?user=new')
                )
            );
        $spacer = new SpacerComponentBuilder(ComponentSpaceSize::SM);

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setSpacing(ComponentSpacing::SM)
            ->setFlex(0)
        // ->setBackgroundColor('#fafafa')
        // ->setBorderColor('#e0e0e0')
        // ->setBorderWidth('1px')
            ->setContents([$oldButton, $newButton, $spacer]);
    }
}
