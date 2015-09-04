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
 * Represents a single PHP requirement, e.g. an installed extension.
 * It can be a mandatory requirement or an optional recommendation.
 * There is a special subclass, named PhpIniRequirement, to check a php.ini configuration.
 *
 * @author Tobias Schultze <http://tobion.de>
 */
class Requirement
{
    private $fulfilled;
    private $testMessage;
    private $helpText;
    private $helpHtml;
    private $optional;

    /**
     * Constructor that initializes the requirement.
     *
     * @param bool $fulfilled Whether the requirement is fulfilled
     * @param string $testMessage The message for testing the requirement
     * @param string $helpHtml The help text formatted in HTML for resolving the problem
     * @param string|null $helpText The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
     * @param bool $optional Whether this is only an optional recommendation not a mandatory requirement
     */
    public function __construct($fulfilled, $testMessage, $helpHtml, $helpText = null, $optional = false)
    {
        $this->fulfilled = (bool)$fulfilled;
        $this->testMessage = (string)$testMessage;
        $this->helpHtml = (string)$helpHtml;
        $this->helpText = null === $helpText ? strip_tags($this->helpHtml) : (string)$helpText;
        $this->optional = (bool)$optional;
    }

    /**
     * Returns whether the requirement is fulfilled.
     *
     * @return bool true if fulfilled, otherwise false
     */
    public function isFulfilled()
    {
        return $this->fulfilled;
    }

    /**
     * Returns the message for testing the requirement.
     *
     * @return string The test message
     */
    public function getTestMessage()
    {
        return $this->testMessage;
    }

    /**
     * Returns the help text for resolving the problem.
     *
     * @return string The help text
     */
    public function getHelpText()
    {
        return $this->helpText;
    }

    /**
     * Returns the help text formatted in HTML.
     *
     * @return string The HTML help
     */
    public function getHelpHtml()
    {
        return $this->helpHtml;
    }

    /**
     * Returns whether this is only an optional recommendation and not a mandatory requirement.
     *
     * @return bool true if optional, false if mandatory
     */
    public function isOptional()
    {
        return $this->optional;
    }
}
