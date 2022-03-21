@extends('master')

@section('header')
    Wyznaczanie optymalnej trasy</h2>
    <p>Powered by: Service © openrouteservice.org | Map data © OpenStreetMap contributors</p><h2>
@stop

@section('content')
    {{Form::open(array('method' => 'put', 'url' => 'TSP/change'))}}
    {{csrf_field()}}
    <div class="way">
        <table class="table table-striped table-hover table-sm table-responsive">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Numer przesyłki (SSCC)</th>
                    <th scope="col">Typ adresu</th>
                    <th scope="col">Adres</th>
                    <th scope="col">Szacowana data dostawy/odbioru</th>
                    <th scope="col">Status przesyłki</th>
                    <th scope="col">Kurier</th>
                </tr>
            </thead>
            <tbody>
                @foreach($parcels as $key => $parcel)
                    <tr>
                        <td scope="row">{{{$key}}}</td>
                        <td>
                            @if($parcel["SSCC_number"] !== null)
                                <a href="{{url('parcels/'.$parcel["parcelId"])}}">
                                    <strong>{{{$parcel["SSCC_number"]}}}</strong>
                                </a>
                            @endif
                            {{Form::hidden('parcel[]', $parcel["parcelId"])}}
                        </td>
                        <td>{{{$parcel["addressType"]}}}
                            {{Form::hidden('addressType[]', $parcel["addressType"])}}
                        </td>
                        <td>{{{$parcel["address"]}}}</td>
                        <td>{{{$parcel["date_of_delivery"]}}}</td>
                        <td>{{{$parcel["state_of_delivery"]}}}</td>
                        <td>
                            @if($parcel["courier_id"] !== null)
                                @if(Auth::user()->id == $parcel["courier_id"]
                                    || Auth::user()->isAdmin())
                                    <a href="{{url('users/'.$parcel["courier_id"])}}">
                                        {{{$parcel["courier_name"]}}}</a>
                                @else
                                    {{{$parcel["courier_name"]}}}
                                @endif
                            @else
                                brak
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td scope="row"></td>
                    <td></td>
                    <td>Całkowity dystans:</td>
                    <td>{{{$totalDistance}}} km</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @if(strlen($options[1]) > 0)
                <tr>
                    <td scope="row"></td>
                    <td></td>
                    <td>Całkowity czas:</td>
                    <td>{{{$totalTime}}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endif
                <tr>
                    <td scope="row"></td>
                    <td></td>
                    <td>Odleglości między dwoma punktami:</td>
                    <td>{{{$options[0]}}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @if(strlen($options[1]) > 0)
                <tr>
                    <td scope="row"></td>
                    <td></td>
                    <td>Kryterium wyszukiwania:</td>
                    <td>{{{$options[1]}}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <table class="table table-striped table-sm table-responsive">
        <tr>
            <td>
                Przed zmianą kolejności upewnij się, że wyznaczona trasa <b>ma wspólną lokalizację aktualną, datę i kuriera</b>.
                {{Form::hidden('change', "change")}}
            </td>
        </tr>
        <tr>
            <td>
                {{Form::submit("Zmień kolejność", array("class"=>"btn btn-primary"))}}
                <a href="{{url('TSP/')}}" class="btn btn-secondary">Anuluj</a>
            </td>
        </tr>
    </table>
    {{Form::close()}}
@stop

@section('graphhead')
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">

        var pointAddresses = <?php echo '["' . implode('", "', $pointAddresses) . '"]' ?>;
        var pointXs = <?php echo '["' . implode('", "', $pointXs) . '"]' ?>;
        var pointXGraphs = <?php echo '["' . implode('", "', $pointXGraphs) . '"]' ?>;
        var pointYs = <?php echo '["' . implode('", "', $pointYs) . '"]' ?>;
        var pointYGraphs = <?php echo '["' . implode('", "', $pointYGraphs) . '"]' ?>;
        var pointDistances = <?php echo '["' . implode('", "', $pointDistances) . '"]' ?>;
        var pointDurations = <?php echo '["' . implode('", "', $pointDurations) . '"]' ?>;
        var parcels = pointAddresses;
        var showDurations =  <?php if(strlen($options[1]) > 0){ echo '"yes"';} else { echo '"no"'; } ?>;

        //pointXs = this.convertArrayValuesAsNumbers(pointXs);
        pointXGraphs = this.convertArrayValuesAsNumbers(pointXGraphs);
        //pointYs = this.convertArrayValuesAsNumbers(pointYs);
        pointYGraphs = this.convertArrayValuesAsNumbers(pointYGraphs);
        pointDistances = this.convertArrayValuesAsNumbers(pointDistances);
        //pointDurations = this.convertArrayValuesAsNumbers(pointDurations);

        // Load the Visualization API and the corechart package.
        google.charts.load('current', {'packages':['corechart']});

        // Set a callback to run when the Google Visualization API is loaded.
        google.charts.setOnLoadCallback(drawChart);

        //document.getElementById('chart_div').onresize = function() {drawChart()};

        // Callback that creates and populates a data table,
        // instantiates the scatter chart, passes in the data and
        // draws it.
        function drawChart() {

            // Create the data table.
            var data = new google.visualization.DataTable();
            data.addColumn('number', 'x');
            data.addColumn('number', 'y');
            data.addColumn({type: 'string', role: 'tooltip'});

            for(i = 0; i < parcels.length; i++){
                var nextI = i + 1;
                if(nextI >= parcels.length){
                    nextI = 1;
                }
                if(showDurations == "yes"){
                    data.addRow([
                        pointXGraphs[i],
                        pointYGraphs[i],
                        pointAddresses[i] + "\nx:" + pointXs[i] + "\ny:" + pointYs[i] +
                            "\nDo " + pointAddresses[nextI] + ":\n" + pointDistances[i] +
                            " km\n(" + pointDurations[i] + ")"
                    ]);
                } else {
                    data.addRow([
                        pointXGraphs[i],
                        pointYGraphs[i],
                        pointAddresses[i] + "\nx:" + pointXs[i] + "\ny:" + pointYs[i] +
                            "\nDo " + pointAddresses[nextI] + ":\n" + pointDistances[i] +
                            " km"
                    ]);
                }
            }
            var mostVisiblePoint = this.setFirstAtoBAsMostVisiblePoint(parcels);

            if(mostVisiblePoint !== parcels.length-1){
                if(showDurations == "yes"){
                    data.addRow([
                        pointXGraphs[mostVisiblePoint],
                        pointYGraphs[mostVisiblePoint],
                        pointAddresses[mostVisiblePoint] +
                        "\nx:" + pointXs[mostVisiblePoint] +
                        "\ny:" + pointYs[mostVisiblePoint] +
                        "\nDo " + pointAddresses[mostVisiblePoint+1] +
                        ":\n" + pointDistances[mostVisiblePoint] + " km" +
                        "\n(" + pointDurations[mostVisiblePoint] + ")"
                    ]);
                } else {
                    data.addRow([
                        pointXGraphs[mostVisiblePoint],
                        pointYGraphs[mostVisiblePoint],
                        pointAddresses[mostVisiblePoint] +
                        "\nx:" + pointXs[mostVisiblePoint] +
                        "\ny:" + pointYs[mostVisiblePoint] +
                        "\nDo " + pointAddresses[mostVisiblePoint+1] +
                        ":\n" + pointDistances[mostVisiblePoint] + " km"
                    ]);
                }
            }

            // Set chart options
            var options = {
                'title':'Rozłożenie punktów na płaszczyźnie 2D',
                //'width':500,
                //'height':300,
                'legend':'none',
                'hAxis': {'title': 'x'},
                'vAxis': {'title': 'y'},
                'explorer': {},
                'lineWidth': 2,
            //'responsive': true,
                'pointSize': 18
            };

            // Instantiate and draw our chart, passing in some options.
            var chart = new google.visualization.ScatterChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }

        function setFirstAtoBAsMostVisiblePoint(parcels) {
            for(i = 0; i < parcels.length; i++){
                point = parcels[i];
                if(i >= parcels.length-1){
                    point2 = parcels[0];
                } else {
                    point2 = parcels[i + 1];
                }
                if(point.address !== point2.address){
                    return i;
                }
            }
            return parcels.length-1;
        }

        function convertArrayValuesAsNumbers(array) {
            newArray = [];
            for (i = 0; i < array.length; i++){
                newArray[i] = parseFloat(array[i].trim());
            }
            return newArray;
        }

        $(window).resize(function(){
            drawChart();
        });
    </script>
@stop

@section('graphbody')
    <!--Div that will hold the scatter chart-->
    <div id="chart_div" style="margin: 0% auto; position: relative; height:50vw; width:75vw; text-align: center"></div>
@stop


