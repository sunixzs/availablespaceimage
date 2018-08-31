<?php

namespace Sunixzs\Availablespaceimage;

use Twig_Extension;

/**
 * Exposes a "available_space_image" function to Twig templates
 */
class TwigImageExtension extends Twig_Extension
{
    public $service = null;

    public function __construct($service)
    {
        $this->service = $service;
    }

    public function getName()
    {
        return 'image_extension';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('available_space_image', array($this->service, 'available_space_image')),
            new \Twig_SimpleFunction('thumbnail', array($this->service, 'thumbnail'))
        );
    }
}
