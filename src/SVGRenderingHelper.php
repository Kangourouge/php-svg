<?php

class SVGRenderingHelper {

    private $image, $imageWidth, $imageHeight;
    private $strokeWidth;
    private $state, $stateStack;



    public function __construct($image, $imageWidth, $imageHeight) {

        $this->image = $image;
        $this->imageWidth = $imageWidth;
        $this->imageHeight = $imageHeight;

        $this->state = array(
            'x' => 0,
            'y' => 0,
            'opacity' => 1
        );
        $this->stateStack = array(&$this->state);

    }





    public function getWidth() {
        return $this->imageWidth;
    }

    public function getHeight() {
        return $this->imageHeight;
    }





    public function setStrokeWidth($strokeWidth) {
        $this->strokeWidth = $strokeWidth;
        imagesetthickness($this->image, $strokeWidth);
    }





    public function push() {
        // create copy
        $this->state = $this->state;
        $this->stateStack[] = $this->state;
    }

    public function pop() {
        if (count($this->stateStack) === 1)
            throw new RuntimeException("No more states to pop");
        $this->state = array_pop($this->stateStack);
    }





    public function translate($x, $y) {
        $this->state['x'] += $x;
        $this->state['y'] += $y;
    }





    public function scaleOpacity($value) {
        $this->state['opacity'] *= $value;
        if ($this->state['opacity'] < 0)
            $this->state['opacity'] = 0;
        elseif ($this->state['opacity'] > 1)
            $this->state['opacity'] = 1;
    }

    // multiplies the alpha channel of the given color with the given factor
    // example: color has 0.5 opacity, factor is 0.2 => result is 0.1
    // the color is a "normal" GD color integer, e.g. 0x7F000000 for fully transparent black
    private static function _multiplyColorAlpha($color, $alphaFactor) {
        $rgb = $color & 0x00FFFFFF;
        $a = ($color & 0xFF000000) >> 24;
        $a = 0x7F - 0x7F*$alphaFactor + $a*$alphaFactor;
        return $rgb | ($a << 24);
    }





    public function drawRect($x, $y, $width, $height, $color) {
        $x += $this->state['x'];
        $y += $this->state['y'];
        $color = self::_multiplyColorAlpha($color, $this->state['opacity']);
        imagerectangle($this->image, $x, $y, $x + $width - 1, $y + $height - 1, $color);
    }

    public function fillRect($x, $y, $width, $height, $color) {
        $x += $this->state['x'];
        $y += $this->state['y'];
        $color = self::_multiplyColorAlpha($color, $this->state['opacity']);
        imagefilledrectangle($this->image, $x, $y, $x + $width - 1, $y + $height - 1, $color);
    }





    public function drawEllipse($cx, $cy, $rx, $ry, $color) {
        $cx += $this->state['x'];
        $cy += $this->state['y'];
        $color = self::_multiplyColorAlpha($color, $this->state['opacity']);
        // imageellipse ignores imagesetthickness; draw arc instead
        imagearc($this->image, $cx, $cy, $rx * 2, $ry * 2, 0, 360, $color);
    }

    public function fillEllipse($cx, $cy, $rx, $ry, $color) {
        $cx += $this->state['x'];
        $cy += $this->state['y'];
        $color = self::_multiplyColorAlpha($color, $this->state['opacity']);
        imagefilledellipse($this->image, $cx, $cy, $rx * 2, $ry * 2, $color);
    }





    public function drawLine($x1, $y1, $x2, $y2, $color) {
        $x1 += $this->state['x'];
        $y1 += $this->state['y'];
        $x2 += $this->state['x'];
        $y2 += $this->state['y'];
        $color = self::_multiplyColorAlpha($color, $this->state['opacity']);
        imageline($this->image, $x1, $y1, $x2, $y2, $color);
    }





    public function drawPolygon($points, $numpoints, $color) {
        for ($i=0, $n=count($points); $i<$n; $i++) {
            $points[$i] += $this->state[$i % 2 === 0 ? 'x' : 'y'];
        }
        $color = self::_multiplyColorAlpha($color, $this->state['opacity']);
        imagepolygon($this->image, $points, $numpoints, $color);
    }

    public function fillPolygon($points, $numpoints, $color) {
        for ($i=0, $n=count($points); $i<$n; $i++) {
            $points[$i] += $this->state[$i % 2 === 0 ? 'x' : 'y'];
        }
        $color = self::_multiplyColorAlpha($color, $this->state['opacity']);
        imagefilledpolygon($this->image, $points, $numpoints, $color);
    }



    public function drawPolyline($points, $numpoints, $color) {

        $color = self::_multiplyColorAlpha($color, $this->state['opacity']);

        for ($i=1; $i<$numpoints; $i++) {
            $x1 = $this->state['x'] + $points[($i-1) * 2];
            $y1 = $this->state['y'] + $points[($i-1) * 2 + 1];
            $x2 = $this->state['x'] + $points[$i * 2];
            $y2 = $this->state['y'] + $points[$i * 2 + 1];
            imageline($this->image, $x1, $y1, $x2, $y2, $color);
        }

    }

}