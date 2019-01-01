<?php
/**
 * This file is part of kennynguyeenx/facebook-sticker.
 *
 * (c) Kenny Nguyen <kennynguyeenx@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Kennynguyeenx\FacebookSticker;


use Imagick;

/**
 * Class FacebookSticker
 * @package Kennynguyeenx\FacebookSticker
 */
class FacebookSticker
{
    /**
     * @param string $srcImagePath
     * @param string $dstImagePath
     * @param int $numOfRows
     * @param int $numOfCols
     * @param int $numOfFrames
     * @throws FacebookStickerException
     * @throws \ImagickException
     */
    public function createAnimatedSticker(
        string $srcImagePath,
        string $dstImagePath,
        int $numOfRows,
        int $numOfCols,
        int $numOfFrames
    ) {
        if ($numOfRows * $numOfCols * $numOfFrames === 0 || empty($srcImagePath) || empty($dstImagePath)) {
            throw new FacebookStickerException('Missing parameters');
        }

        if (!file_exists($srcImagePath) || !is_readable($srcImagePath)) {
            throw new FacebookStickerException('Source image file does not exist or is not readable');
        }

        $source = @imagecreatefrompng($srcImagePath);
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $tempImageWidth = intval(round($sourceWidth / $numOfCols));
        $tempImageHeight = intval(round($sourceHeight / $numOfRows));
        $animatedGif = new Imagick();
        $animatedGif->setFormat('GIF');
        $x = 0;

        for ($row = 0; $row < $numOfRows; ++$row) {
            for ($col = 0; $col < $numOfCols; ++$col) {
                if ($x++ >= $numOfFrames) {
                    break;
                }
                $tempImage = @imagecreatetruecolor($tempImageWidth, $tempImageHeight);
                imagealphablending($tempImage, false);
                imagesavealpha($tempImage, true);
                imagecopyresized(
                    $tempImage,
                    $source,
                    0,
                    0,
                    $col * $tempImageWidth,
                    $row * $tempImageHeight,
                    $tempImageWidth,
                    $tempImageHeight,
                    $tempImageWidth,
                    $tempImageHeight
                );

                imagepng($tempImage, 'cache/temp_image_' . $row . '_' . $col . '.png');
                imagedestroy($tempImage);
                $image = new Imagick();
                $image->readImage('cache/temp_image_' . $row . '_' . $col . '.png');
                $animatedGif->addImage($image);
                $animatedGif->setImageDelay(10);
                $animatedGif->setImageDispose(2);
            }
        }

        $animatedGif->writeImages('cache/animated.gif', true);
        $animatedGif->clear();
        $animatedGif->destroy();
        copy('cache/animated.gif', $dstImagePath);
    }
}
