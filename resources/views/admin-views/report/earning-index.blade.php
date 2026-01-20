@extends('layouts.admin.app')

@section('title', translate('Earning Report'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/earning_report.png')}}" alt="{{ translate('earning-report') }}">
                {{translate('earning_Report')}}
            </h2>
        </div>

        <div class="card card-body mb-3">
            <div class="media gap-3 flex-column flex-sm-row align-items-sm-center">
                <div class="avatar avatar-xl avatar-4by3">
                    <img class="avatar-img" src="{{asset('public/assets/admin/svg/illustrations/earnings.png')}}"
                         alt="{{ translate('earnings') }}">
                </div>

                <div class="media-body">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                        <div class="text-capitalize">
                            <h2 class="page-header-title">{{translate('earning')}} {{translate('report')}} {{translate('overview')}}</h2>

                            <div class="meida flex-column gap-3">
                                <div>
                                    <span>{{translate('admin')}}:</span>
                                    <a href="#">{{auth('admin')->user()->f_name.' '.auth('admin')->user()->l_name}}</a>
                                </div>

                                <div class="media-body">
                                    <div class="d-flex align-items-center text-nowrap gap-2">
                                        <div>{{translate('date')}}:</div>
                                        <div>( {{session('from_date')}} - {{session('to_date')}} )</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a class="btn btn-icon btn-primary rounded-circle" href="{{route('admin.dashboard')}}">
                            <i class="tio-home-outlined"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form action="{{route('admin.report.set-date')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-2">
                                <label for="exampleInputEmail1" class="form-label">{{translate('show_data_by_data_range')}}</label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <input type="date" name="from" id="from_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <input type="date" name="to" id="to_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary btn-block">{{translate('show')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            @php
                $from = session('from_date');
                $to = session('to_date');
                $totalTax=\App\Models\Order::where(['order_status'=>'delivered'])
                    ->whereBetween('created_at', [$from, $to])
                    ->sum('total_tax_amount');

               if($totalTax==0){
                   $totalTax=0.01;
               }
            @endphp
            <div class="col-sm-6 mb-3">
                @php
                    $totalSold=\App\Models\Order::where(['order_status'=>'delivered'])
                        ->whereBetween('created_at', [$from, $to])->sum('order_amount');

                    if($totalSold==0){
                        $totalSold=.01;
                    }
                @endphp
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="media">
                                    <i class="tio-dollar-outlined nav-icon"></i>

                                    <div class="media-body">
                                        <h4 class="mb-1">{{translate('total')}} {{translate('sold')}}</h4>
                                        <span class="font-size-sm text-success">
                                          <i class="tio-trending-up"></i> {{ Helpers::set_symbol(round(abs($totalSold-$totalTax))) }}
                                        </span>
                                    </div>

                                </div>
                            </div>

                            <div class="col-auto">
                                <div class="js-circle"
                                     data-hs-circles-options='{
                                       "value": {{$totalSold=='.01'?0:round((($totalSold-$totalTax)/$totalSold)*100)}},
                                       "maxValue": 100,
                                       "duration": 2000,
                                       "isViewportInit": true,
                                       "colors": ["#e7eaf3", "green"],
                                       "radius": 25,
                                       "width": 3,
                                       "fgStrokeLinecap": "round",
                                       "textFontSize": 14,
                                       "additionalText": "%",
                                       "textClass": "circle-custom-text",
                                       "textColor": "green"
                                     }'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="media">
                                    <i class="tio-money nav-icon"></i>

                                    <div class="media-body">
                                        <h4 class="mb-1">{{translate('total')}} {{translate('tax')}}</h4>
                                        <span class="font-size-sm text-warning">
                                          <i class="tio-trending-up"></i> {{ Helpers::set_symbol($totalTax) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-auto">
                                <div class="js-circle" data-hs-circles-options='{
                                   "value": {{$totalTax=='0.01'?0:round(((abs($totalTax))/$totalSold)*100)}},
                                   "maxValue": 100,
                                   "duration": 2000,
                                   "isViewportInit": true,
                                   "colors": ["#e7eaf3", "#ec9a3c"],
                                   "radius": 25,
                                   "width": 3,
                                   "fgStrokeLinecap": "round",
                                   "textFontSize": 14,
                                   "additionalText": "%",
                                   "textClass": "circle-custom-text",
                                   "textColor": "#ec9a3c"
                                 }'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                @php
                    $yearlySold=\App\Models\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [date('y-01-01'), date('y-12-31')])->sum('order_amount')
                @endphp
                <h6 class="card-subtitle mb-0">{{translate('Total sale of')}} {{date('Y')}} :<span
                        class="h3 ml-sm-2"> {{ Helpers::set_symbol($yearlySold) }}</span>
                </h6>

                <div class="hs-unfold">
                    <a class="js-hs-unfold-invoker btn btn-white d-flex gap-1 align-items-center"
                       href="{{route('admin.orders.list',['status'=>'all'])}}">
                        <i class="tio-shopping-cart-outlined"></i> {{translate('orders')}}
                    </a>
                </div>
            </div>

            @php
                $sold=[];
                    for ($i=1;$i<=12;$i++){
                        $from = date('Y-'.$i.'-01');
                        $to = date('Y-'.$i.'-30');
                        $sold[$i]=\App\Models\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->sum('order_amount');
                    }
            @endphp

            @php
                $tax=[];
                    for ($i=1;$i<=12;$i++){
                        $from = date('Y-'.$i.'-01');
                        $to = date('Y-'.$i.'-30');
                        $tax[$i]=\App\Models\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->sum('total_tax_amount');
                    }
            @endphp

            <div class="card-body">
                <div class="chartjs-custom height-18rem">
                    <canvas class="js-chart"
                            data-hs-chartjs-options='{
                        "type": "line",
                        "data": {
                           "labels": ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                           "datasets": [{
                            "data": [{{$sold[1]}},{{$sold[2]}},{{$sold[3]}},{{$sold[4]}},{{$sold[5]}},{{$sold[6]}},{{$sold[7]}},{{$sold[8]}},{{$sold[9]}},{{$sold[10]}},{{$sold[11]}},{{$sold[12]}}],
                            "backgroundColor": ["rgba(55, 125, 255, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "green",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "green",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#377dff"
                          },
                          {
                            "data": [{{$tax[1]}},{{$tax[2]}},{{$tax[3]}},{{$tax[4]}},{{$tax[5]}},{{$tax[6]}},{{$tax[7]}},{{$tax[8]}},{{$tax[9]}},{{$tax[10]}},{{$tax[11]}},{{$tax[12]}}],
                            "backgroundColor": ["rgba(0, 201, 219, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "#ec9a3c",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "#ec9a3c",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#00c9db"
                          }]
                        },
                        "options": {
                          "gradientPosition": {"y1": 200},
                           "scales": {
                              "yAxes": [{
                                "gridLines": {
                                  "color": "#e7eaf3",
                                  "drawBorder": false,
                                  "zeroLineColor": "#e7eaf3"
                                },
                                "ticks": {
                                  "min": 0,
                                  "max": {{Helpers::max_earning()}},
                                  "stepSize": {{round(Helpers::max_earning()/5)}},
                                  "fontColor": "#97a4af",
                                  "fontFamily": "Open Sans, sans-serif",
                                  "padding": 10,
                                  "postfix": " {{Helpers::currency_symbol()}}"
                                }
                              }],
                              "xAxes": [{
                                "gridLines": {
                                  "display": false,
                                  "drawBorder": false
                                },
                                "ticks": {
                                  "fontSize": 12,
                                  "fontColor": "#97a4af",
                                  "fontFamily": "Open Sans, sans-serif",
                                  "padding": 5
                                }
                              }]
                          },
                          "tooltips": {
                            "prefix": "",
                            "postfix": "",
                            "hasIndicator": true,
                            "mode": "index",
                            "intersect": false,
                            "lineMode": true,
                            "lineWithLineColor": "rgba(19, 33, 68, 0.075)"
                          },
                          "hover": {
                            "mode": "nearest",
                            "intersect": true
                          }
                        }
                      }'>
                    </canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

    <script src="{{asset('public/assets/admin/vendor/chart.js/dist/Chart.min.js')}}"></script>
    <script src="{{asset('public/assets/admin/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js')}}"></script>
    <script src="{{asset('public/assets/admin/js/hs.chartjs-matrix.js')}}"></script>

    <script>
        "use strict";

        $(document).on('ready', function () {

            $('.js-flatpickr').each(function () {
                $.HSCore.components.HSFlatpickr.init($(this));
            });


            $('.js-nav-scroller').each(function () {
                new HsNavScroller($(this)).init()
            });

            $('.js-daterangepicker').daterangepicker();

            $('.js-daterangepicker-times').daterangepicker({
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'M/DD hh:mm A'
                }
            });

            let start = moment();
            let end = moment();

            function cb(start, end) {
                $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(start.format('MMM D') + ' - ' + end.format('MMM D, YYYY'));
            }

            $('#js-daterangepicker-predefined').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);

            $('.js-chart').each(function () {
                $.HSCore.components.HSChartJS.init($(this));
            });

            let updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

            $('[data-toggle="chart"]').click(function (e) {
                let keyDataset = $(e.currentTarget).attr('data-datasets')

                updatingChart.data.datasets.forEach(function (dataset, key) {
                    dataset.data = updatingChartDatasets[keyDataset][key];
                });
                updatingChart.update();
            })

            function generateHoursData() {
                let data = [];
                let dt = moment().subtract(365, 'days').startOf('day');
                let end = moment().startOf('day');
                while (dt <= end) {
                    data.push({
                        x: dt.format('YYYY-MM-DD'),
                        y: dt.format('e'),
                        d: dt.format('YYYY-MM-DD'),
                        v: Math.random() * 24
                    });
                    dt = dt.add(1, 'day');
                }
                return data;
            }

            $.HSCore.components.HSChartMatrixJS.init($('.js-chart-matrix'), {
                data: {
                    datasets: [{
                        label: 'Commits',
                        data: generateHoursData(),
                        width: function (ctx) {
                            let a = ctx.chart.chartArea;
                            return (a.right - a.left) / 70;
                        },
                        height: function (ctx) {
                            let a = ctx.chart.chartArea;
                            return (a.bottom - a.top) / 10;
                        }
                    }]
                },
                options: {
                    tooltips: {
                        callbacks: {
                            title: function () {
                                return '';
                            },
                            label: function (item, data) {
                                let v = data.datasets[item.datasetIndex].data[item.index];

                                if (v.v.toFixed() > 0) {
                                    return '<span class="font-weight-bold">' + v.v.toFixed() + ' hours</span> on ' + v.d;
                                } else {
                                    return '<span class="font-weight-bold">No time</span> on ' + v.d;
                                }
                            }
                        }
                    },
                    scales: {
                        xAxes: [{
                            position: 'bottom',
                            type: 'time',
                            offset: true,
                            time: {
                                unit: 'week',
                                round: 'week',
                                displayFormats: {
                                    week: 'MMM'
                                }
                            },
                            ticks: {
                                "labelOffset": 20,
                                "maxRotation": 0,
                                "minRotation": 0,
                                "fontSize": 12,
                                "fontColor": "rgba(22, 52, 90, 0.5)",
                                "maxTicksLimit": 12,
                            },
                            gridLines: {
                                display: false
                            }
                        }],
                        yAxes: [{
                            type: 'time',
                            offset: true,
                            time: {
                                unit: 'day',
                                parser: 'e',
                                displayFormats: {
                                    day: 'ddd'
                                }
                            },
                            ticks: {
                                "fontSize": 12,
                                "fontColor": "rgba(22, 52, 90, 0.5)",
                                "maxTicksLimit": 2,
                            },
                            gridLines: {
                                display: false
                            }
                        }]
                    }
                }
            });

            $('.js-clipboard').each(function () {
                let clipboard = $.HSCore.components.HSClipboard.init(this);
            });


            $('.js-circle').each(function () {
                let circle = $.HSCore.components.HSCircles.init($(this));
            });
        });

        $('#from_date,#to_date').change(function () {
            let fr = $('#from_date').val();
            let to = $('#to_date').val();
            if (fr != '' && to != '') {
                if (fr > to) {
                    $('#from_date').val('');
                    $('#to_date').val('');
                    toastr.error('{{ translate("Invalid date range!") }}', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }

        })
    </script>
@endpush
