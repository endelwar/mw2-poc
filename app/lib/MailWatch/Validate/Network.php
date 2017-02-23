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

namespace MailWatch\Validate;

class Network
{
    private $validHostnameRegex = "^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$";

    /**
     * @param string $ip
     * @return bool
     */
    public function isIp($ip)
    {
        return !(filter_var($ip, FILTER_VALIDATE_IP) === false);
    }

    /**
     * @param string $hostname
     * @return bool
     */
    public function isHostname($hostname)
    {
        return preg_match('#' . $this->validHostnameRegex . '#', $hostname) > 0;
    }

    /**
     * @param string $string
     * @return bool
     */
    public function isIpOrHostname($string)
    {
        if ($this->isHostname($string)) {
            return true;
        } elseif ($this->isIp($string)) {
            return true;
        }

        return false;
        //return $this->isIp($string) || $this->isHostname($string);
    }

    /**
     * @param int $portNumber
     * @return bool
     */
    public function isValidPort($portNumber)
    {
        return (1 <= $portNumber) && ($portNumber <= 65535);
    }
}
