<?php

namespace Relocity\PhpPolylabel;

class Cell
{
	public $x;
	public $y;
	public $h;
	public $d;
	public $max;

	public function __construct($x, $y, $h, $polygon)
    {
		$this->x = $x; // cell center x
		$this->y = $y; // cell center y
		$this->h = $h; // half the cell size
		$this->d = $this->pointToPolygonDist($x, $y, $polygon);
		$this->max = $this->d + $this->h * M_SQRT2;
	}

    // signed distance from point to polygon outline (negative if point is outside)
    private function pointToPolygonDist( $x, $y, $polygon )
    {
        $inside = false;
        $minDistSq = INF;

        for( $k = 0; $k < count($polygon); $k++ ) {
            $ring = $polygon[$k];

            for( $i = 0, $len = count($ring), $j = $len - 1; $i < $len; $j = $i++ ) {
                $a = $ring[$i];
                $b = $ring[$j];

                if(($a[1] > $y !== $b[1] > $y) &&
                    ($x < ($b[0] - $a[0]) * ($y - $a[1]) / ($b[1] - $a[1]) + $a[0])) $inside = !$inside;

                $minDistSq = min($minDistSq, $this->getSegDistSq($x, $y, $a, $b));
            }
        }

        return $minDistSq === 0 ? 0 : ($inside ? 1 : -1) * sqrt($minDistSq);
    }
    // get squared distance from a point to a segment
    private function getSegDistSq($px, $py, $a, $b)
    {
        $x = $a[0];
        $y = $a[1];
        $dx = $b[0] - $x;
        $dy = $b[1] - $y;

        if( $dx > 0 || $dy > 0 ) {
            $t = (($px - $x) * $dx + ($py - $y) * $dy) / ($dx * $dx + $dy * $dy);

            if( $t > 1 ) {
                $x = $b[0];
                $y = $b[1];
            } else if( $t > 0 ) {
                $x += $dx * $t;
                $y += $dy * $t;
            }
        }

        $dx = $px - $x;
        $dy = $py - $y;

        return $dx * $dx + $dy * $dy;
    }
}