Icelus
======

![Build Status](https://travis-ci.org/beryllium/icelus.svg)

> _Icelus, otherwise known as "Scaled Sculpin", are a small fish native to the North Pacific._

Icelus is a quick and easy thumbnail generator for your Sculpin-based websites and blogs.

Requirements
------------

Icelus requires:

* PHP 5.4+
* Imagick extension (installable via apt-get, pecl, or yum)
* Imanee library ([imanee.io](http://imanee.io) - fetched automatically by Composer)

Installation
------------

If you are using the Phar-based Sculpin utility, you can create or modify a sculpin.json file in your project root and add `"beryllium/icelus"` to the `"requires"` block. Then, run `sculpin install` or `sculpin update` to fetch the required dependencies.

    {
      "requires": {
         "beryllium/icelus": "*"
      }
    }
    
Alternatively, if you are using a Composer-based sculpin installation, you should simply be able to run `composer require beryllium/icelus` to get things rolling.

Once the library is installed, you have to tell Sculpin how to load it. You can do this by creating or modifying a `app/SculpinKernel.php` file to resemble the following:

    <?php
    
    class SculpinKernel extends \Sculpin\Bundle\SculpinBundle\HttpKernel\AbstractKernel
    {
        protected function getAdditionalSculpinBundles()
        {
            return array(
                'Beryllium/Icelus/IcelusBundle',
            );
        }
    }

__Note:__ The class name should be a string, not an object instantiation. (This differs from the way Symfony 2 configures bundles.)

Usage
-----

Icelus exposes a `thumbnail` function in Twig, which you can use either on its own or by creating Twig macros to customize the output.

___thumbnail(image, width, height, crop)___

* __image__ (string): The relative path to the image in the `source/` folder.
* __width__ (int): Maximum width, in pixels
* __height__ (int): Maximum height, in pixels
* __crop__ (bool): False will fit the whole image inside the provided dimensions. True will crop the image from the center. Default: __FALSE__

Inline Example:

    <a href="image.jpg"><img src="{% thumbnail('image.jpg', 100, 100) %}"></a>
    
Macro Example:

    index.html:
    
    {% import '_macros.html.twig' as m %}
    
    <h1>Gone Fishin'!</h1>
    {{ m.small_thumbnail('image.jpg', 'A picture from my fishing trip') }}
    
    
    _macros.html.twig: 
    
    {% macro small_thumbnail(image, caption) %}
      <a href="{{ image }}">
        <img src="{% thumbnail(image, 100, 100) %}">
        <br>
        <em>{{ caption }}</em>
      </a>
    {% endmacro %}
    
A service called `icelus.service` is also added to the Sculpin dependency injection container, which you can use in your own Sculpin extensions. 

For raw access to the underlying Imanee library, the service is named `icelus.imanee`. If you need to go deeper, you can then retrieve an Imagick instance using `$imanee->getIMResource()`.

Technically speaking, this extension could also be used as a Symfony 2 bundle. This has not been tested, but experimentation is welcome.

Future Plans
------------

I would like for Icelus to expose more features of the underlying Imanee library, particularly with regard to watermarks and drawing text onto images. Imanee's support for animated gifs could possibly also be advantageous in some way.

I would also like for Icelus to be compatible with a wide variety of PHP frameworks and workflows. I've concentrated on having it as a Twig extension, but it could also work with other template systems and even Markdown-style parsers.