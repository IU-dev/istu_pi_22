<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit799689eb2fd546cbfbae573b50107d97
{
    public static $prefixLengthsPsr4 = array (
        'Z' => 
        array (
            'Zend\\Escaper\\' => 13,
        ),
        'P' => 
        array (
            'PhpOffice\\PhpWord\\' => 18,
            'PhpOffice\\Common\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Zend\\Escaper\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-escaper/src',
        ),
        'PhpOffice\\PhpWord\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice/phpword/src/PhpWord',
        ),
        'PhpOffice\\Common\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice/common/src/Common',
        ),
    );

    public static $classMap = array (
        'PclZip' => __DIR__ . '/..' . '/pclzip/pclzip/pclzip.lib.php',
        'SimpleXLSX' => __DIR__ . '/..' . '/shuchkin/simplexlsx/src/SimpleXLSX.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit799689eb2fd546cbfbae573b50107d97::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit799689eb2fd546cbfbae573b50107d97::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit799689eb2fd546cbfbae573b50107d97::$classMap;

        }, null, ClassLoader::class);
    }
}
