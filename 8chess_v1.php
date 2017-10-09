<?php

class Common {
    protected $map = array();

    public function __construct(){
        $this->initMap();
    }

    public function initMap(){
        for($i=0; $i<8; $i++){
            for($j=0; $j<8; $j++){
                for($k=0; $k<8; $k++){
                    $this->map[$i][$j][$k] = null;
                }
            }
        }
    }

    public function getMap(){
        $map = array();
        for($i=0; $i<8; $i++){
            for($j=0; $j<8; $j++){
                if(array_sum($this->map[$i][$j]) > 0){
                    $map[$i][$j] = true;
                } else {
                    $map[$i][$j] = null;
                }
            }
        }
        return $map;
    }

    public function printZone(){
        $map = $this->map;
        echo '<pre>';
        for($i=0;$i<8;$i++){
            for($y=0;$y<8;$y++){
                if(array_sum($map[$i][$y]) > 0){
                    echo '1';
                } else {
                    echo '0';
                }
            }
            echo PHP_EOL;
        }
        echo '</pre>';
    }
}

class Map extends Common {
    private $points = array();

    public function __construct(){
        parent::__construct();
    }

    public function getEmptyPoint(){
        $emptyPoint = array();
        for($i=0; $i<8; $i++){
            for($j=0; $j<8; $j++){
                if(array_sum($this->map[$i][$j]) == 0){
                    $emptyPoint[] = array($i, $j);
                }
            }
        }
        return $emptyPoint;
    }

    public function getHash(){
        $hash = '';
        for($i=0; $i<8; $i++){
            for($j=0; $j<8; $j++){
                if(array_sum($this->map[$i][$j]) == 0) {
                    $hash .= '0';
                } else {
                    $hash .= '1';
                }
            }
        }
        return $hash;
    }

    public function addPoint($point, $level){
        $pMap = $point->getMap();
        for($i=0; $i<8; $i++){
            for($j=0; $j<8; $j++){
                $this->map[$i][$j][$level] = $pMap[$i][$j];
            }
        }
        $this->points[$level] = $point->getCord();
    }

    public function delPoint($point, $level){
        for($i=0; $i<8; $i++){
            for($j=0; $j<8; $j++){
                $this->map[$i][$j][$level] = null;
            }
        }
        unset($this->points[$level]);
    }
}

class Point extends Common{
    public $x;
    public $y;

    public function __construct($x, $y){
        parent::__construct();
        $this->x = $x;
        $this->y = $y;
        $this->setZone();
    }

    public function getCord(){
        return array(
            $this->x,
            $this->y
        );
    }

    public function setZone(){
        for($i=0; $i<8; $i++){
            $this->map[$i][$this->y][0] = true;
            $this->map[$this->x][$i][0] = true;
            if($this->x+$i < 8  && $this->y+$i < 8) {
                $this->map[$this->x+$i][$this->y+$i][0] = true;
            }
            if($this->x-$i >= 0 && $this->y-$i >= 0) {
                $this->map[$this->x-$i][$this->y-$i][0] = true;
            }
        }
    }
}

class Chess {
    public $variant = array();
    public $testIter = 0;
    public $testDeepIter = 0;
    public $badVariant = array();

    public function run(){
        $map = new Map();

        $this->testLevel($map);

        echo 'Variants: '.count($this->variant);
    }


    public function testLevel($map, $level = 0){
        $this->testIter++;
        $emptyPoints = $map->getEmptyPoint();
        //if($this->testIter > 10000) { echo count($this->variant); exit;}
        $goodVar = 0;

        if(isset($this->badVariant[$map->getHash()])){
            return $goodVar;
        }
        if($level < 2){echo ' '.$level.'-'.count($emptyPoints).' ';} ;
        if($level < 2){echo '[';}
        foreach ($emptyPoints as $key => $emptyPoint) {
            if($level < 7){ if($level < 2){echo $key.'/';}
            $point = new Point($emptyPoint[0], $emptyPoint[1]);

            $map->addPoint($point, $level);
                $goodVar += $this->testLevel($map, $level+1);
            } else {
                $this->variant[$map->getHash()] += 1;
                $goodVar++;
            }
            $map->delPoint($point, $level);

            unset($point);
        }
        if($level < 2){echo']';}
        if($level < 7 && $goodVar == 0){
            $this->badVariant[$map->getHash()] += 1;
        }

        return $goodVar;
    }
}

$chess = new Chess();

$chess->run();
