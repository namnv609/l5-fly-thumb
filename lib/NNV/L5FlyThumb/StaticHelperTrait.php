<?php

namespace NNV\L5FlyThumb;

use Illuminate\Support\Facades\File;
use Log;

trait StaticHelperTrait
{
    /**
     * Check path is file
     *
     * @param  string $filePath File path
     * @return boolean True or false
     */
    private function isFile($filePath)
    {
        return File::isFile($filePath);
    }

    /**
     * Check path is directory
     *
     * @param  string $filePath Directory path
     * @return boolean True or false
     */
    private function isDirectory($dirPath)
    {
        return File::isDirectory($dirPath);
    }

    /**
     * Get file last modified
     *
     * @param  string $filePath File path
     * @return int Last modified time
     */
    private function lastModified($filePath)
    {
        return File::lastModified($filePath);
    }

    /**
     * Get file content
     *
     * @param  string $filePath File path
     * @return mixed File content
     */
    private function getFileContent($filePath)
    {
        return File::get($filePath);
    }

    /**
     * Make directory
     *
     * @param  string $dirPath Directory path
     * @return bool True or false
     */
    private function makeDirectory($dirPath)
    {
        return File::makeDirectory($dirPath);
    }

    private function writeLog($message)
    {
        return Log::error($message);
    }
}
