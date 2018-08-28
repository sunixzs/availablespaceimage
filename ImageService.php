<?php

namespace Sunixzs\Availablespaceimage;

use Imanee\Imanee;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Service for Image Manipulation.
 */
class ImageService
{
    public $imanee;
    public $source_dir;
    public $output_dir;
    public $filesystem;
    public $prefix;
    public $completed = array();

    const DEFAULT_PREFIX = '/_thumbs';

    /**
     * Constructor
     *
     * @param Imanee        $imanee         Performs the required image manipulations
     * @param string        $source_dir     Where to find the images
     * @param string        $output_dir     Where to save the images
     * @param string|null   $prefix         subdirectory under output_dir to save the images (Default: '/_thumbs')
     * @param Filesystem    $filesystem     Filesystem class for doing filesystem things
     */
    public function __construct(Imanee $imanee, $source_dir, $output_dir, $prefix, Filesystem $filesystem)
    {
        $this->imanee     = $imanee;
        $this->source_dir = rtrim($source_dir, '/');
        $this->output_dir = rtrim($output_dir, '/');
        $this->prefix     = $prefix ? rtrim($prefix, '/') : static::DEFAULT_PREFIX;
        $this->filesystem = $filesystem;
    }

    /**
     * Prepare the output directory.
     *
     * This makes sure we have somewhere to put the thumbnails once we've generated them.
     */
    protected function prepOutputDir()
    {
        if (!is_dir($this->output_dir . $this->prefix)) {
            $this->filesystem->mkdir($this->output_dir . $this->prefix);
        }
    }

    /**
     * creates an image block
     *
     * @param string $image
     * @param string $alt
     * @param string $title
     * @param integer $width0
     * @param integer $width1
     * @param integer $width2
     * @param integer $width2
     * @return string html img tag
     */
    public function available_space_image($image, $alt="", $title = "", $width0 = 480, $width1 = 768, $width2 = 1280, $width3 = 1600)
    {
        $imagesize = getimagesize($this->source_dir . '/' . $image);
        if (!($imagesize[0] && $imagesize[1])) {
            return "ERROR: could not get image size for " . $this->source_dir . '/' . $image;
        }

        $originalWidth = $imagesize[0];
        $originalHeight = $imagesize[1];

        $this->prepOutputDir();

        $images = [];

        if ($width0) {
            $images[$width0] = $this->createImage($image, $width0, $originalWidth, $originalHeight);
        }
        if ($width1) {
            $images[$width1] = $this->createImage($image, $width1, $originalWidth, $originalHeight);
        }
        if ($width2) {
            $images[$width2] = $this->createImage($image, $width2, $originalWidth, $originalHeight);
        }
        if ($width3) {
            $images[$width3] = $this->createImage($image, $width3, $originalWidth, $originalHeight);
        }

        if (count($images) === 0) {
            return "";
        }

        $html .= "<img ";
        if ($title) {
            $html .= " title=\"" . htmlentities($title) . "\"";
        }
        $html .= " alt=\"" . htmlentities($alt) . "\"";
        $html .= " data-method=\"available-space-image\"";
        $i = 0;
        foreach ($images as $width => $source) {
            if ($i === 0) {
                $html .= " data-default-width=\"" . $width . "\"";
                $html .= " src=\"" . $source . "\"";
            } else {
                $html .= " data-src-" . $width . "=\"" . $source . "\"";
            }
            $i++;
        }

        $html .= " />";
        return new \Twig_Markup( $html, 'UTF-8' );
    }

    /**
     * Resizes an image
     *
     * @param [type] $image
     * @param [type] $width
     * @param [type] $defaultWidth
     * @param [type] $defaultHeight
     * @return void
     */
    protected function createImage($image, $width, $defaultWidth, $defaultHeight) {
        // no sense duplicating work - only process image if it doesn't already exist
        if (!isset($this->completed[$image][$width]['filename'])) {
            $height = (integer) ($defaultWidth / $width * $defaultHeight);
            $this->imanee->load($this->source_dir . '/' . $image)->resize($width, $height, "transparent");
            $patinfo = pathinfo($image);

            $image_name = vsprintf(
                '%s-%s-%sx%s.%s',
                array(
                    $patinfo["filename"],
                    substr(md5($image), 0, 8),
                    $width,
                    $height,
                    strtolower($this->imanee->getFormat())
                )
            );

            // write the thumbnail to disk
            file_put_contents(
                $this->output_dir . $this->prefix . '/' . $image_name,
                $this->imanee->output()
            );
            $this->completed[$image][$width]['filename'] = $image_name;
        }

        return $this->prefix . '/' . $this->completed[$image][$width]['filename'];
    }
}
