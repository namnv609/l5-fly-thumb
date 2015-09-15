<?php

namespace NNV\L5FlyThumb;

use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class L5FlyThumb
{

    use StaticHelperTrait;

    /**
     * @var $config
     */
    private $config;

    /**
     * @var Intervention\Image\ImageManager
     */
    private $imageManager;

    /**
     * @var $outputDir
     */
    private $outputDir;

    /**
     * @var $baseDir
     */
    private $baseDir;

    /**
     * @var $noPhoto
     */
    private $noPhoto;

    /**
     * @var $imageQuality
     */
    private $imageQuality;

    /**
     * @var $watermark
     */
    private $watermark;

    /**
     * @var $cacheTime;
     */
    private $cacheTime;

    /**
     * @var $sizeRestriction
     */
    private $sizeRestriction;

    public function __construct()
    {
        $this->config           = app()['config'];

        $this->imageManager     = new ImageManager([
            'driver' => $this->config->get('l5flythumb.driver', 'gd')
        ]);

        $this->outputDir        = $this->config->get('l5flythumb.output_dir');
        $this->baseDir          = $this->config->get('l5flythumb.base_dir');
        $this->noPhoto          = $this->config->get('l5flythumb.no_photo');
        $this->imageQuality     = $this->config->get('l5flythumb.quality');
        $this->watermark        = $this->config->get('l5flythumb.watermark');
        $this->cacheTime        = $this->config->get('l5flythumb.cache_time');
        $this->sizeRestriction  = $this->config->get('l5flythumb.size_restriction');
    }

    /**
     * Resize image
     *
     * @param  int $width Thumbnail width
     * @param  int $height Thumbnail height
     * @param  string $fileName Source file name
     * @return \Response Image content
     */
    public function resize($width, $height, $fileName)
    {
        $destFile = $this->createThumb($width, $height, $fileName);
        $fileContent = $this->getFileContent($destFile);
        $file = new SymfonyFile($destFile);

        return response()->make($fileContent, 200, [
            'content-type' => $file->getMimeType()
        ]);
    }

    /**
     * Create thumbnail
     *
     * @param  int $width Image width
     * @param  int $height Image height
     * @param  string $fileName Source file name
     * @return string Path to file resized
     */
    private function createThumb($width, $height, $fileName)
    {
        if (!$this->isAllowSize($width, $height)) {
            return $this->noPhoto;
        }

        $inputFilePath = sprintf('%s/%s', $this->baseDir, $fileName);
        $destFilePath = $this->getDestFilePath($width, $height, $fileName);

        if ($this->isFile($destFilePath) && !$this->fileExpired($destFilePath)) {
            return $destFilePath;
        } elseif (!$this->isFile($inputFilePath)) {
            return $this->noPhoto;
        }

        $thumbImage = $this->imageManager->make($inputFilePath);

        if ($this->watermark['image'] && $this->isFile($this->watermark['image'])) {
            $thumbImage = $this->addWatermark($thumbImage);
        }

        $thumbImage->resize($width, $height);

        try {
            $thumbImage->save($destFilePath, $this->imageQuality);
        } catch (\Exception $e) {
            $this->writeLog($e->getMessage());

            $destFilePath = $this->noPhoto;
        }

        return $destFilePath;
    }

    /**
     * Get destination file path
     *
     * @param  int $width Thumbnail width
     * @param  int $height Thumbnail height
     * @param  int $fileName Source file name
     * @return string Destination thumbnail path
     */
    private function getDestFilePath($width, $height, $fileName)
    {
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $thumbFolder = sprintf('%sx%s', $width, $height);
        $destDir = sprintf('%s/%s', $this->outputDir, $thumbFolder);

        if (!$this->isDirectory($destDir)) {
            $this->makeDirectory($destDir);
        }

        $thumbFileName = sprintf('%s.%s', md5($fileName), $fileExtension);

        return sprintf('%s/%s', $destDir, $thumbFileName);
    }

    /**
     * Add watermark to image
     *
     * @param object $imageObj Intervention image object
     * @return object Intervention image object
     */
    private function addWatermark($imageObj)
    {
        $imageObj->insert($this->watermark['image'], $this->watermark['position']);

        return $imageObj;
    }

    /**
     * Check cache is expired
     *
     * @param  string $filePath File path
     * @return bool File is exprired
     */
    private function fileExpired($filePath)
    {
        $now = time();
        $lastModified = $this->lastModified($filePath);

        if ($this->cacheTime === 0) {
            return false;
        }

        return $this->cacheTime < ($now - $lastModified);
    }

    /**
     * Check thumbnail allowd sizes
     *
     * @param  int $width Image width
     * @param  int $height Image height
     * @return bool Size is allowed
     */
    private function isAllowSize($width, $height)
    {
        $sizeStr = sprintf('%sx%s', $width, $height);

        if (count($this->sizeRestriction) === 0) {
            return true;
        }

        return in_array($sizeStr, $this->sizeRestriction);
    }
}
