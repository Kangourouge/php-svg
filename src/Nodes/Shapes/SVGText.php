<?php

namespace SVG\Nodes\Shapes;

use SVG\Nodes\SVGNode;
use SVG\Rasterization\SVGRasterizer;

/**
 * Represents the SVG tag 'text'.
 * Has the special attributes x, y.
 */
class SVGText extends SVGNode
{
    const TAG_NAME = 'text';

    /** @var string $content */
    private $content;

    /**
     * @param string|null $content
     * @param string|null $x Point x coordinate.
     * @param string|null $y Point y coordinate.
     */
    public function __construct($content, $x = null, $y = null)
    {
        parent::__construct();

        $this->content = $content;
        $this->setAttribute('x', $x);
        $this->setAttribute('y', $y);
    }

    /**
     * @return string The x coordinate.
     */
    public function getX()
    {
        return $this->getAttribute('x');
    }

    /**
     * Sets the x coordinate.
     *
     * @param string $x The new coordinate.
     *
     * @return $this This node instance, for call chaining.
     */
    public function setX($x)
    {
        return $this->setAttribute('x', $x);
    }

    /**
     * @return string The y coordinate.
     */
    public function getY()
    {
        return $this->getAttribute('y');
    }

    /**
     * Sets the y coordinate.
     *
     * @param string $y The new coordinate.
     *
     * @return $this This node instance, for call chaining.
     */
    public function setY($y)
    {
        return $this->setAttribute('y', $y);
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return SVGText
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function rasterize(SVGRasterizer $rasterizer)
    {
    }
}
