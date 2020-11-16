<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2fcc311bfb2e34a2b1afcdd8d500f1e2
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Faker\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Faker\\' => 
        array (
            0 => __DIR__ . '/..' . '/fzaninotto/faker/src/Faker',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2fcc311bfb2e34a2b1afcdd8d500f1e2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2fcc311bfb2e34a2b1afcdd8d500f1e2::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
