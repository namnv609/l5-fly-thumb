<?php

/**
 *
 * driver: Image processing extension (GD or Imagick)
 * quality: Thumbnail quality (0-100)
 * output_dir: Path to thumbnail will be stored
 * base_dir: Path to folder contain image need to resize
 * no_photo: Alternate image when error
 * watermark:
 *     * image: Path to watermark image
 *     * position: Position of watermark
 *         * top-left
 *         * top
 *         * top-right
 *         * left
 *         * center
 *         * right
 *         * bottom-left
 *         * bottom
 *         * bottom-right
 * cache_time: Time to cache image (second). When time is expired. Thumbnail will re-resize (0 is never re-resize thumbnail)
 * size_restriction: List allowed thumbnail sizes (If you wish to allow all sizes, please give an empty array)
 *
 */

return [
    'driver' => 'gd',
    'quality' => 80,
    'output_dir' => '',
    'base_dir' => '',
    'no_photo' => '',
    'watermark' => [
        'image' => '',
        'position' => 'center'
    ],
    'cache_time' => 0,
    'size_restriction' => [],
];
