<?php

namespace NNV;

use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class FlyThumb
{
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

    public function __construct()
    {
        $this->imageManager = new ImageManager([
            'driver' => config('flythumb.driver', 'gd')
        ]);

        $this->outputDir    = config('flythumb.output_dir');
        $this->baseDir      = config('flythumb.base_dir');
        $this->noPhoto      = config('flythumb.no_photo');
        $this->imageQuality = config('flythumb.quality');
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
        $fileContent = File::get($destFile);
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
        $inputFilePath = sprintf('%s/%s', $this->baseDir, $fileName);
        $destFilePath = $this->getDestFilePath($width, $height, $fileName);

        if (File::isFile($destFilePath)) {
            return $destFilePath;
        } elseif (!File::isFile($inputFilePath)) {
            return $this->noPhoto;
        }

        $thumbImage = $this->imageManager->make($inputFilePath);
        $thumbImage->resize($width, $height);

        try {
            $thumbImage->save($destFilePath, $this->imageQuality);
        } catch (\Exception $e) {
            \Log::info('FlyThumb error: ', $e->getMessage());

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

        if (!File::isDirectory($destDir)) {
            File::makeDirectory($destDir);
        }

        $thumbFileName = sprintf('%s.%s', md5($fileName), $fileExtension);

        return sprintf('%s/%s', $destDir, $thumbFileName);
    }
}
