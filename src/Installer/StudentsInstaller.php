<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Installer
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Installer;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

/**
 * Provides cross-platform application installation.
 */
class StudentsInstaller extends Installer
{

    public static function installDependencies(Event $event)
    {
        // Directory creation.
        self::createDirectory(__DIR__ . '/../../public/media/javascript/jquery');
        self::createDirectory(__DIR__ . '/../../public/media/bootstrap/css');
        self::createDirectory(__DIR__ . '/../../public/media/bootstrap/fonts');

        // Moving files
        self::copyFile(__DIR__ . '/../../vendor/twbs/bootstrap/dist/css/bootstrap.min.css',
            __DIR__ . '/../../public/media/bootstrap/css/bootstrap.min.css');

        self::copyDirectory(__DIR__ . '/../../vendor/twbs/bootstrap/dist/fonts/',
          __DIR__ . '/../../public/media/bootstrap/fonts/');

        self::copyFile(__DIR__ . '/../../vendor/components/jquery/jquery.min.js',
            __DIR__ . '/../../public/media/javascript/jquery/jquery.min.js');
    }
}