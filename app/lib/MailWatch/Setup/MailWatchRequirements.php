<?php
/**
 * MailWatch2
 * Copyright (C) 2015 Manuel Dalla Lana <endelwar@aregar.it>
 *
 * DISCLAIMER:
 *
 *   This file was adapted from the original FrameworkRequirements distributed by
 *   the Symfony team in the SensioDistributionBundle.
 *
 *   (c) Fabien Potencier <fabien@symfony.com>
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program.
 * If not, see <http://www.gnu.org/licenses/>.
 */

namespace MailWatch\Setup;

/**
 * This class specifies all requirements and optional recommendations that are necessary to run MailWatch.
 *
 * @author Tobias Schultze <http://tobion.de>
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Manuel Dalla Lana <endelwar@aregar.it>
 */
class MailWatchRequirements extends RequirementCollection
{
    const REQUIRED_PHP_VERSION = '5.3.9';

    /**
     * Constructor that initializes the requirements.
     */
    public function __construct()
    {
        /* mandatory requirements follow */
        $installedPhpVersion = PHP_VERSION;

        $this->addRequirement(
            version_compare($installedPhpVersion, self::REQUIRED_PHP_VERSION, '>='),
            sprintf('PHP version must be at least %s (%s installed)', self::REQUIRED_PHP_VERSION, $installedPhpVersion),
            sprintf(
                'You are running PHP version "<strong>%s</strong>", but MailWatch needs at least PHP "<strong>%s</strong>" to run.
                Before using MailWatch, upgrade your PHP installation, preferably to the latest version.',
                $installedPhpVersion,
                self::REQUIRED_PHP_VERSION
            ),
            sprintf(
                'Install PHP %s or newer (installed version is %s)',
                self::REQUIRED_PHP_VERSION,
                $installedPhpVersion
            )
        );

        $this->addRequirement(
            version_compare($installedPhpVersion, '5.3.16', '!='),
            'PHP version must not be 5.3.16 as MailWatch won\'t work properly with it due to PHP bug #62715',
            'Install PHP 5.3.17 or newer (or downgrade to an earlier PHP version)'
        );

        $this->addRequirement(
            is_dir(__DIR__ . '/../../../../vendor/composer'),
            'Vendor libraries must be installed',
            'Vendor libraries are missing. Install composer following instructions from <a href="http://getcomposer.org/">http://getcomposer.org/</a>. ' .
            'Then run "<strong>php composer.phar install</strong>" to install them.'
        );

        $cacheDir = __DIR__ . '/../../../../cache';
        $this->addRequirement(
            is_writable($cacheDir),
            'cache/ directory must be writable',
            'Change the permissions of "<strong>cache/</strong>" directory so that the web server can write into it.'
        );

        $dataDir = __DIR__ . '/../../../../data';
        $this->addRequirement(
            is_writable($dataDir),
            'data/ directory must be writable',
            'Change the permissions of "<strong>data/</strong>" directory so that the web server can write into it.'
        );

        $this->addPhpIniRequirement(
            'date.timezone',
            true,
            false,
            'date.timezone setting must be set',
            'Set the "<strong>date.timezone</strong>" setting in php.ini<a href="#phpini">*</a> (like Europe/Rome).'
        );

        if (version_compare($installedPhpVersion, self::REQUIRED_PHP_VERSION, '>=')) {
            $timezones = array();
            foreach (\DateTimeZone::listAbbreviations() as $abbreviations) {
                foreach ($abbreviations as $abbreviation) {
                    $timezones[$abbreviation['timezone_id']] = true;
                }
            }
            $this->addRequirement(
                isset($timezones[@date_default_timezone_get()]),
                sprintf(
                    'Configured default timezone "%s" must be supported by your installation of PHP',
                    @date_default_timezone_get()
                ),
                'Your default timezone is not supported by PHP. Check for typos in your <strong>php.ini</strong> file and have a look at the list of deprecated timezones at <a href="http://php.net/manual/en/timezones.others.php">http://php.net/manual/en/timezones.others.php</a>.'
            );
        }

        $this->addRequirement(
            function_exists('session_start'),
            'session_start() must be available',
            'Install and enable the <strong>session</strong> extension.'
        );

        $this->addPhpIniRequirement('detect_unicode', false);

        $this->addRequirement(
            function_exists('mb_strlen'),
            'mb_strlen() should be available',
            'Install and enable the <strong>mbstring</strong> extension.'
        );

        if (extension_loaded('mbstring')) {
            $this->addPhpIniRequirement(
                'mbstring.func_overload',
                create_function('$cfgValue', 'return (int) $cfgValue === 0;'),
                true,
                'string functions should not be overloaded',
                'Set "<strong>mbstring.func_overload</strong>" to <strong>0</strong> in php.ini<a href="#phpini">*</a> to disable function overloading by the mbstring extension.'
            );
        }

        $this->addRequirement(
            function_exists('utf8_decode'),
            'utf8_decode() should be available',
            'Install and enable the <strong>XML</strong> extension.'
        );
        $this->addRequirement(
            function_exists('filter_var'),
            'filter_var() should be available',
            'Install and enable the <strong>filter</strong> extension.'
        );

        $this->addRequirement(
            class_exists('PDO'),
            'PDO must be installed',
            'Install <strong>PDO</strong> (mandatory for database connection).'
        );
        if (class_exists('PDO')) {
            $drivers = \PDO::getAvailableDrivers();
            $this->addRequirement(
                count($drivers) > 0,
                sprintf(
                    'PDO must have some drivers installed (currently available: %s)',
                    count($drivers) ? implode(', ', $drivers) : 'none'
                ),
                'Install <strong>PDO drivers</strong> (mandatory for database connection).'
            );
        }

        // Recommendations
        $this->addRecommendation(
            version_compare($installedPhpVersion, '5.4.0', '!='),
            'You should not use PHP 5.4.0 due to the PHP bug #61453',
            'MailWatch might not work properly due to the PHP bug #61453 ("Cannot dump definitions which have method calls"). Install PHP 5.4.1 or newer.'
        );

        $this->addRecommendation(
            function_exists('iconv'),
            'iconv() should be available',
            'Install and enable the <strong>iconv</strong> extension.'
        );

        $this->addRecommendation(
            extension_loaded('intl'),
            'intl extension should be available',
            'Install and enable the <strong>intl</strong> extension (used for validators).'
        );
        if (extension_loaded('intl')) {
            // in some WAMP server installations, new Collator() returns null
            $this->addRecommendation(
                null !== new \Collator('it_IT'),
                'intl extension should be correctly configured',
                'The intl extension does not behave properly. This problem is typical on PHP 5.3.X x64 WIN builds.'
            );
            // check for compatible ICU versions (only done when you have the intl extension)
            if (defined('INTL_ICU_VERSION')) {
                $version = INTL_ICU_VERSION;
            } else {
                $reflector = new \ReflectionExtension('intl');
                ob_start();
                $reflector->info();
                $output = strip_tags(ob_get_clean());
                preg_match('/^ICU version +(?:=> )?(.*)$/m', $output, $matches);
                $version = $matches[1];
            }
            $this->addRecommendation(
                version_compare($version, '4.0', '>='),
                'intl ICU version should be at least 4+',
                'Upgrade your <strong>intl</strong> extension with a newer ICU version (4+).'
            );
            $this->addPhpIniRecommendation(
                'intl.error_level',
                create_function('$cfgValue', 'return (int) $cfgValue === 0;'),
                true,
                'intl.error_level should be 0 in php.ini',
                'Set "<strong>intl.error_level</strong>" to "<strong>0</strong>" in php.ini<a href="#phpini">*</a> to inhibit the messages when an error occurs in ICU functions.'
            );
        }

        $this->addPhpIniRecommendation('short_open_tag', false);
        $this->addPhpIniRecommendation('magic_quotes_gpc', false, true);
        $this->addPhpIniRecommendation('register_globals', false, true);
        $this->addPhpIniRecommendation('session.auto_start', false);

        $accelerator =
            (extension_loaded('eaccelerator') && ini_get('eaccelerator.enable'))
            ||
            (extension_loaded('apc') && ini_get('apc.enabled'))
            ||
            (extension_loaded('Zend Optimizer+') && ini_get('zend_optimizerplus.enable'))
            ||
            (extension_loaded('Zend OPcache') && ini_get('opcache.enable'))
            ||
            (extension_loaded('xcache') && ini_get('xcache.cacher'));
        $this->addRecommendation(
            $accelerator,
            'a PHP accelerator should be installed',
            'Install and/or enable a <strong>PHP accelerator</strong> (highly recommended for performance).'
        );
    }
}
