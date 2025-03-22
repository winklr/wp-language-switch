<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita2c1302f8431dacf5612b9ff825eba40
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WPLangSwitch\\' => 13,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WPLangSwitch\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita2c1302f8431dacf5612b9ff825eba40::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita2c1302f8431dacf5612b9ff825eba40::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita2c1302f8431dacf5612b9ff825eba40::$classMap;

        }, null, ClassLoader::class);
    }
}
