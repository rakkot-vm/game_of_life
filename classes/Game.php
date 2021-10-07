<?php
namespace classes;

class Game
{
    const UniverseSize = 25;

    private array $outputColors = [
        'died' => 47,
        'alive' => 46,
    ];
    private float $delayIteration = 0.08;
    private float $iterationCount = 75;

    private array $currentUniverse;

    public function __construct()
    {
        $this->setEmptyUniverse();
        $this->setGlide();
    }

    public function start()
    {
        $play = true;
        for ($i = 0; $play; $i++) {
            $this->printUniverse();

            for ($iT = microtime(true); microtime(true) < $iT+$this->delayIteration;){}

            if ($i && $i % $this->iterationCount == 0) {
                $play = false;
            } else {
                $this->recalcUniverse();

                system('clear');
            }
        }
    }

    private function setEmptyUniverse()
    {
        $emptyLine = array_fill(0, self::UniverseSize, false);
        $this->currentUniverse = array_fill(0, self::UniverseSize, $emptyLine);
    }

    private function setGlide()
    {
        $center = ceil(self::UniverseSize / 2);

        $this->currentUniverse[$center - 1][$center] = true;

        $this->currentUniverse[$center][$center + 1] = true;

        $this->currentUniverse[$center + 1][$center - 1] = true;
        $this->currentUniverse[$center + 1][$center]     = true;
        $this->currentUniverse[$center + 1][$center + 1] = true;
    }

    private function recalcUniverse()
    {
        $nextUniverse = $this->currentUniverse;

        foreach ($this->currentUniverse as $lineNumber => $line) {
            foreach ($line as $elemNumber => $elem) {
                $neighborsCount = $this->getAliveNeighborsCount($lineNumber, $elemNumber);

                $nextUniverse[$lineNumber][$elemNumber] = $this->getNewState($neighborsCount, $elem);
            }
        }

        $this->currentUniverse = $nextUniverse;
    }

    private function getNewState(int $neighborsCount, bool $currentState): bool
    {
        if ($currentState) {
            $state = $neighborsCount < 4 && $neighborsCount > 1;
        } else {
            $state = $neighborsCount == 3;
        }

        return $state;
    }

    private function getAliveNeighborsCount(int $line, int $elem): int
    {
        $aliveCount = 0;

        if ($line > 0) {
            $aliveCount += (int)($elem && $this->currentUniverse[$line-1][$elem-1]);
            $aliveCount += (int)($this->currentUniverse[$line-1][$elem]);
            $aliveCount += (int)($elem+1 < self::UniverseSize && $this->currentUniverse[$line-1][$elem+1]);
        }

        $aliveCount += (int)($elem && $this->currentUniverse[$line][$elem-1]);
        $aliveCount += (int)($elem+1 < self::UniverseSize  && $this->currentUniverse[$line][$elem+1]);

        if ($line+1 < self::UniverseSize) {
            $aliveCount += (int)($elem && $this->currentUniverse[$line+1][$elem-1]);
            $aliveCount += (int)($this->currentUniverse[$line+1][$elem]);
            $aliveCount += (int)($elem + 1 < self::UniverseSize && $this->currentUniverse[$line+1][$elem+1]);
        }

        return $aliveCount;
    }

    private function printUniverse()
    {
        $output = '';
        foreach ($this->currentUniverse as $lineNumber => $line) {
            foreach ($line as $elemNumber => $elem) {
                $output .=  $elem ? "\e[" . $this->outputColors['alive'] . "m  " : "\e[" . $this->outputColors['died'] . "m  ";
            }
            $output .= "\e[0m";
            $output .= "\n";
        }
        $output .= "\n";

        echo $output;
    }
}
