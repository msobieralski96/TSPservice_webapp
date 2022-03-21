<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Khill\Lavacharts\Lavacharts;

class ChartController extends Controller{

    public function getData($points) {

        $pointAddresses = array_column($points, 'address');
        $pointXs = array_column($points, 'x');
        $pointXGraphs = array_column($points, 'xGraph');
        $pointYs = array_column($points, 'y');
        $pointYGraphs = array_column($points, 'yGraph');
        $pointDistances = array_column($points, 'distance');
        $pointDurations = array_column($points, 'duration');

        $lava = new Lavacharts;

        $chart = $lava->DataTable();

        $chart->addNumberColumn('x')
              ->addNumberColumn('y')
              ->addRoleColumn('string', 'tooltip');

        for($i = 0; $i < count($points); $i++){
            $nextI = $i + 1;
            if($nextI >= count($points)){
                $nextI = 1;
            }
            $chart->addRow([$pointXGraphs[$i], $pointYGraphs[$i],
                $pointAddresses[$i]."\nx:".$pointXs[$i]."\ny:".$pointYs[$i].
                    "\nDo ".$pointAddresses[$nextI].":\n".$pointDistances[$i].
                    " km\n(".$pointDurations[$i]." min)"]);
        }
        $mostVisiblePoint = $this->setFirstAtoBAsMostVisiblePoint($points);
        if($mostVisiblePoint !== count($points)-1){
            $chart->addRow([$pointXGraphs[$mostVisiblePoint],
                $pointYGraphs[$mostVisiblePoint],
                $pointAddresses[$mostVisiblePoint].
                "\nx:".$pointXs[$mostVisiblePoint].
                "\ny:".$pointYs[$mostVisiblePoint].
                "\nDo ".$pointAddresses[$mostVisiblePoint+1].
                ":\n".$pointDistances[$mostVisiblePoint]." km".
                "\n(".$pointDurations[$mostVisiblePoint]." min)"]);
        }

        $scatterChart = $lava->ScatterChart('TSPGraph', $chart, [
            //'responsive' => true,
            //'width' => 800,
            //'height' => 600,
            'legend' => [
                'position' => 'none'
            ],
            'lineWidth' => 2,
            'pointSize' => 18
        ]);
        return $lava;
    }

    public function setFirstAtoBAsMostVisiblePoint($points) {
        for($i = 0; $i < count($points); $i++){
            $point = $points[$i];
            if($i >= count($points)-1){
                $point2 = $points[0];
            } else {
                $point2 = $points[$i + 1];
            }
            if($point["address"] !== $point2["address"]){
                return $i;
            }
        }
        return count($points)-1;
    }

}
