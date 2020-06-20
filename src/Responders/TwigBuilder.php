<?php
/**
 * This file is part of the Divergence package.
 *
 * (c) Henry Paradiz <henry.paradiz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace technexus\Responders;

use technexus\App;
use Divergence\Responders\TwigBuilder as RespondersTwigBuilder;

class TwigBuilder extends RespondersTwigBuilder
{
    public function __construct($template, $data = [], $headers = [])
    {
        $data['App'] = App::$App;
        $this->template = $template;
        $this->data = $data;
        $this->headers = $headers;
    }
}
