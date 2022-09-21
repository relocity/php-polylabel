<?php

namespace Relocity\PhpPolylabel;

use Relocity\PhpPolylabel\Cell;
use Relocity\PhpPolylabel\CellQueue;

class Polylabel
{
    public function getCenter($polygon, $precision = 1.0)
    {
        // find the bounding box of the outer ring
        for( $i = 0; $i < count($polygon[0]); $i++ ) {
            $p = $polygon[0][$i];
            if( !$i || $p[0] < $minX ) $minX = $p[0];
            if( !$i || $p[1] < $minY ) $minY = $p[1];
            if( !$i || $p[0] > $maxX ) $maxX = $p[0];
            if( !$i || $p[1] > $maxY ) $maxY = $p[1];
        }

        $width = $maxX - $minX;
        $height = $maxY - $minY;
        $cellSize = min($width, $height);
        $h = $cellSize / 2;

        if( $cellSize === 0 ) {
            return [
                'x' => $minX,
                'y' => $minY,
                'distance' => 0
            ];
        }

        // a priority queue of cells in order of their "potential" (max distance to polygon)
        $cellQueue = new CellQueue();

        // cover polygon with initial cells
        for( $x = $minX; $x < $maxX; $x += $cellSize ) {
            for( $y = $minY; $y < $maxY; $y += $cellSize ) {
                $cellQueue->push(new Cell($x + $h, $y + $h, $h, $polygon));
            }
        }

        // take centroid as the first best guess
        $bestCell = $this->getCentroidCell($polygon);

        // second guess: bounding box centroid
        $bboxCell = new Cell($minX + $width / 2, $minY + $height / 2, 0, $polygon);
        if( $bboxCell->d > $bestCell->d) {
            $bestCell = $bboxCell;
        }

        $numProbes = $cellQueue->length();

        while( $cellQueue->length() ) {
            // pick the most promising cell from the queue
            $cell = $cellQueue->pop();

            // update the best cell if we found a better one
            if( $cell->d > $bestCell->d ) {
                $bestCell = $cell;
            }

            // do not drill down further if there's no chance of a better solution
            if( $cell->max - $bestCell->d <= $precision ) {
                continue;
            }

            // split the cell into four cells
            $h = $cell->h / 2;
            $cellQueue->push(new Cell($cell->x - $h, $cell->y - $h, $h, $polygon));
            $cellQueue->push(new Cell($cell->x + $h, $cell->y - $h, $h, $polygon));
            $cellQueue->push(new Cell($cell->x - $h, $cell->y + $h, $h, $polygon));
            $cellQueue->push(new Cell($cell->x + $h, $cell->y + $h, $h, $polygon));
            $numProbes += 4;
        }
        
        return [
            'x' => $bestCell->x,
            'y' => $bestCell->y,
            'distance' => $bestCell->d
        ];
    }

    // get polygon centroid
    private function getCentroidCell($polygon)
    {
        $area = 0;
        $x = 0;
        $y = 0;
        $points = $polygon[0];

        for( $i = 0, $len = count($points), $j = $len - 1; $i < $len; $j = $i++ ) {
            $a = $points[$i];
            $b = $points[$j];
            $f = $a[0] * $b[1] - $b[0] * $a[1];
            $x += ($a[0] + $b[0]) * $f;
            $y += ($a[1] + $b[1]) * $f;
            $area += $f * 3;
        }

        if( $area === 0 ) {
            return new Cell($points[0][0], $points[0][1], 0, $polygon);
        }

        return new Cell($x / $area, $y / $area, 0, $polygon);
    }
}