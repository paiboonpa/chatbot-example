<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4921bd51215958df6c27fa58d806b98a
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LINE\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LINE\\' => 
        array (
            0 => __DIR__ . '/..' . '/linecorp/line-bot-sdk/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4921bd51215958df6c27fa58d806b98a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4921bd51215958df6c27fa58d806b98a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit4921bd51215958df6c27fa58d806b98a::$classMap;

        }, null, ClassLoader::class);
    }
}
