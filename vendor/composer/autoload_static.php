<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit706dfcf78963b503d4f25ff24b491ed0
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Auryn\\' => 6,
            'ANCENC\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Auryn\\' => 
        array (
            0 => __DIR__ . '/..' . '/rdlowrey/auryn/lib',
        ),
        'ANCENC\\' => 
        array (
            0 => __DIR__ . '/../..' . '/server',
        ),
    );

    public static $prefixesPsr0 = array (
        'M' => 
        array (
            'Mustache' => 
            array (
                0 => __DIR__ . '/..' . '/mustache/mustache/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit706dfcf78963b503d4f25ff24b491ed0::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit706dfcf78963b503d4f25ff24b491ed0::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit706dfcf78963b503d4f25ff24b491ed0::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit706dfcf78963b503d4f25ff24b491ed0::$classMap;

        }, null, ClassLoader::class);
    }
}
