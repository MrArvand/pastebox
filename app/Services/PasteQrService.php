<?php

declare(strict_types=1);

namespace App\Services;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

final class PasteQrService
{
    private const MODULE_DARK = '#111208';
    private const MODULE_LIGHT = '#d6ff3e';

    public function svgForUrl(string $url): string
    {
        $moduleValues = [];
        foreach (QROutputInterface::DEFAULT_MODULE_VALUES as $type => $isDark) {
            $moduleValues[$type] = $isDark ? self::MODULE_LIGHT : self::MODULE_DARK;
        }

        $options = new QROptions([
            'outputInterface' => QRMarkupSVG::class,
            'outputBase64' => false,
            'eccLevel' => EccLevel::M,
            'scale' => 5,
            'drawLightModules' => true,
            'svgAddXmlHeader' => true,
            'moduleValues' => $moduleValues,
        ]);

        return (string) (new QRCode($options))->render($url);
    }
}
