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

namespace MailWatch\Test;

use MailWatch\Template\TwigRenderer;

class TwigRendererTest extends \PHPUnit_Framework_TestCase
{
    private $renderer;

    protected function setUp()
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../fixtures/template');
        $twig = new \Twig_Environment($loader);

        $twig->addExtension(new \Twig_Extensions_Extension_I18n());

        $this->renderer = new TwigRenderer($twig);
    }

    public function testPlainText()
    {
        $this->assertEquals(
            'Hello World!',
            $this->renderer->render('plaintest', array())
        );
    }

    public function testHtmlText()
    {
        $this->assertEquals(
            '<b>Hello</b> <i>World</i><span style="color: #abc;">!</span>',
            $this->renderer->render('htmltest', array())
        );
    }

    public function testVarsSubstitution()
    {
        $vars = array(
            'vars' => array(
                'hello' => 'Hello',
                'world' => 'World'
            )
        );
        $this->assertEquals(
            'Hello World!',
            $this->renderer->render('variabletest', $vars)
        );
    }
}
