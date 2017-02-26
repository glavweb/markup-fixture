<?php

/*
 * This file is part of the MarkupFixture package.
 *
 * (c) Andrey Nilov <nilov@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Glavweb\MarkupFixture\Manager;

use Glavweb\MarkupFixture\Helper\MarkupFixtureHelper;

/**
 * Class MarkupFixtureManager
 *
 * @package Glavweb\MarkupFixture
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class MarkupFixtureManager
{
    /**
     * @var MarkupFixtureHelper
     */
    private $markupFixtureHelper;

    /**
     * @var array
     */
    private $fixtureObjects;

    /**
     * MarkupFixtureManager constructor.
     *
     * @param MarkupFixtureHelper $markupFixtureHelper
     * @param array               $fixtureObjects
     */
    public function __construct(MarkupFixtureHelper $markupFixtureHelper, $fixtureObjects = [])
    {
        $this->markupFixtureHelper = $markupFixtureHelper;
        $this->fixtureObjects      = $fixtureObjects;
    }

    /**
     * @param string $className
     * @return array
     */
    public function get($className)
    {
        if (!$this->fixtureObjects[$className]['class']) {
            throw new \RuntimeException(sprintf('The fixture object for class name "%s" not found', $className));
        }

        $fixture = $this->fixtureObjects[$className];

        return $this->markupFixtureHelper->prepareFixtureForMarkup($fixture);
    }
}
