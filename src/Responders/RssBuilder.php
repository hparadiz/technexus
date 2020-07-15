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

class RssBuilder extends TwigBuilder
{
    public function __construct($template, $data = [], $headers = [])
    {
        $this->contentType = 'application/atom+xml';
        parent::__construct($template, $data, $headers);
    }
}
