<?php
/**
 * MailWatch2
 * Copyright (C) 2015 Manuel Dalla Lana <endelwar@aregar.it>
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

use MailWatch\Setup\Requirement;
use MailWatch\Setup\MailWatchRequirements;

require __DIR__ . '/../vendor/autoload.php';

$lineSize = 70;
$mailwatchRequirements = new MailWatchRequirements();
$iniPath = $mailwatchRequirements->getPhpIniConfigPath();

echo_title('MailWatch2 Requirements Checker');

echo '> PHP is using the following php.ini file:' . PHP_EOL;
if ($iniPath) {
    echo_style('green', '  ' . $iniPath);
} else {
    echo_style('warning', '  WARNING: No configuration file (php.ini) used by PHP!');
}

echo PHP_EOL . PHP_EOL;

echo '> Checking MailWatch2 requirements:' . PHP_EOL . '  ';

$messages = array();
foreach ($mailwatchRequirements->getRequirements() as $req) {
    /** @var $req Requirement */
    if ($helpText = get_error_message($req, $lineSize)) {
        echo_style('red', 'E');
        $messages['error'][] = $helpText;
    } else {
        echo_style('green', '.');
    }
}

$checkPassed = empty($messages['error']);

foreach ($mailwatchRequirements->getRecommendations() as $req) {
    if ($helpText = get_error_message($req, $lineSize)) {
        echo_style('yellow', 'W');
        $messages['warning'][] = $helpText;
    } else {
        echo_style('green', '.');
    }
}

if ($checkPassed) {
    echo_block('success', 'OK', 'Your system is ready to run MailWatch2');
} else {
    echo_block('error', 'ERROR', 'Your system is not ready to run MailWatch2');

    echo_title('Fix the following mandatory requirements', 'red');

    foreach ($messages['error'] as $helpText) {
        echo ' * ' . $helpText . PHP_EOL;
    }
}

if (!empty($messages['warning'])) {
    echo_title('Optional recommendations to improve your Setup', 'yellow');

    foreach ($messages['warning'] as $helpText) {
        echo ' * ' . $helpText . PHP_EOL;
    }
}

echo PHP_EOL;
echo_style('title', 'Note');
echo '  The command console could use a different php.ini file' . PHP_EOL;
echo_style('title', '~~~~');
echo '  than the one used with your web server. To be on the' . PHP_EOL;
echo '      safe side, please check the requirements from your web' . PHP_EOL;
echo '      server using the ';
echo_style('yellow', 'public/check.php');
echo ' script.' . PHP_EOL;
echo PHP_EOL;

exit($checkPassed ? 0 : 1);

function get_error_message(Requirement $requirement, $lineSize)
{
    if ($requirement->isFulfilled()) {
        return;
    }

    $errorMessage = wordwrap($requirement->getTestMessage(), $lineSize - 3, PHP_EOL . '   ') . PHP_EOL;
    $errorMessage .= '   > ' . wordwrap($requirement->getHelpText(), $lineSize - 5, PHP_EOL . '   > ') . PHP_EOL;

    return $errorMessage;
}

function echo_title($title, $style = null)
{
    $style = $style ?: 'title';

    echo PHP_EOL;
    echo_style($style, $title . PHP_EOL);
    echo_style($style, str_repeat('~', strlen($title)) . PHP_EOL);
    echo PHP_EOL;
}

function echo_style($style, $message)
{
    // ANSI color codes
    $styles = array(
        'reset' => "\033[0m",
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'error' => "\033[37;41m",
        'success' => "\033[37;42m",
        'title' => "\033[34m",
    );
    $supports = has_color_support();

    echo ($supports ? $styles[$style] : '') . $message . ($supports ? $styles['reset'] : '');
}

function echo_block($style, $title, $message)
{
    $message = ' ' . trim($message) . ' ';
    $width = strlen($message);

    echo PHP_EOL . PHP_EOL;

    echo_style($style, str_repeat(' ', $width) . PHP_EOL);
    echo_style($style, str_pad(' [' . $title . ']', $width, ' ', STR_PAD_RIGHT) . PHP_EOL);
    echo_style($style, str_pad($message, $width, ' ', STR_PAD_RIGHT) . PHP_EOL);
    echo_style($style, str_repeat(' ', $width) . PHP_EOL);
}

function has_color_support()
{
    static $support;

    if (null === $support) {
        if (DIRECTORY_SEPARATOR == '\\') {
            $support = false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI');
        } else {
            $support = function_exists('posix_isatty') && @posix_isatty(STDOUT);
        }
    }

    return $support;
}
