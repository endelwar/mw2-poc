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

namespace MailWatch\Controller;

use MailWatch\Validate\Network;

class Setup extends Base
{
    public function createEnvFile()
    {
        if ($this->request->getMethod() === 'POST') {
            var_dump($this->request->getParameter('db'));
            $raw_db = $this->request->getParameter('db');
            if ($errors = $this->validateEnvDbData($raw_db)) {
                //write file

                //redirect

                return;
            } else {
                var_dump($errors);
                $render_vars['errors'] = $errors;
            }
        }
        $render_vars['htmltitle'] = 'Setup';
        $html = $this->renderer->render('setup/form_db_data', $render_vars);
        $this->response->setContent($html);
    }

    /**
     * @param array $db
     * @return array|bool
     */
    private function validateEnvDbData(array $db)
    {
        $errors = array();
        $validDriver = array('mysql', 'postgresql');

        // check database driver
        if (!isset($db['driver']) || !in_array($db['driver'], $validDriver, true)) {
            // ko
            $errors['driver'] = true;
        }

        $networkValidator = new Network();

        // check hostname validity
        if (!$networkValidator->isIpOrHostname($db['host'])) {
            $errors['hostname'] = true;
        }

        // check port range
        if (!$networkValidator->isValidPort($db['port'])) {
            $errors['port'] = true;
        }

        if (empty($errors)) {
            return true;
        }

        return $errors;
    }

    public function createDatabase()
    {
        $html = $this->renderer->render('setup/dbinfo', array('htmltitle' => 'Setup'));
        $this->response->setContent($html);
    }
}
