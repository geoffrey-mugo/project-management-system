<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daily Report</title>

    <style type="text/css">
        @page {
            margin: 0px;
        }

        body {
            margin: 0px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        a {
            color: black;
            text-decoration: none;
        }

        table {
            font-size: x-small;
        }

        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }

        .report-table th,
        .report-table td {
            padding: 4px;
            border: 1px solid #949494;
            text-align: left;
            max-width: 150px;
            font-size: 12px !important;
            word-wrap: break-word;
        }


        .report-table th,
        .report-table tfoot {
            border: none !important;
            font-weight: bold;
            font-size: 12px !important;
        }

        .report-table th {
            text-transform: uppercase;
            color: #60A7A6;
        }

        .report-table tfoot {
            border: none !important;
            background: #60A7A6;
        }

        .report-table table {
            width: 100%;
            border-collapse: collapse;
            padding: 5px;
        }

        .information {
            background-color: #60A7A6;
            color: #FFF;
        }

        .information table {
            padding: 10px;
        }
    </style>

</head>

<body>

    <div class="information">
        <table width="100%">
            <tr>
                <td align="left" style="width: 40%;">
                    <h3>Productivity Reporting System</h3>
                    <div>
                        @if (empty($header) || trim($header['user']) === "")
                            General Daily Report
                        @else
                            Daily report for {{ $header['user'] }}
                        @endif
                        <br>
                        Generated by {{ ucfirst(auth()->user()->firstname) }}
                        <br> {{ now() }}
                        <br /><br />
                        @if (isset($header['date']))
                            Date: {{ $header['date'] }}
                        @elseif (isset($header['project']))
                            Project: {{ $header['project'] }}
                        @elseif (isset($header['task']))
                            Task: {{ $header['task'] }}
                        @elseif (isset($header['perfomance']))
                            Perfomance: {{ $header['perfomance'] }}
                        @endif
                    </div>

                </td>
                <td align="center">
                </td>
                <td align="right" style="width: 40%;">
                    <h3>Digital Divide Data</h3>
                    <pre>
                    www.ddd.com

                    Kimathi Street Hse26
                    Nairobi City
                    Kenya
                </pre>
                </td>
            </tr>

        </table>
    </div>

    @php
        $totalUnits = 0;
        $totalDuration = \Carbon\CarbonInterval::create(0, 0, 0, 0); // Initialize the totalDuration as a CarbonInterval;
        $totalUnitshr = 0;

        $totalProjects = $reports
            ->pluck('project.name')
            ->unique()
            ->count();
        $totalTasks = $reports
            ->pluck('task.name')
            ->unique()
            ->count();

        $aboveTarget = $reports->filter(fn($report) => str_contains($report->perfomance, 'Above Target'))->count();
        $belowTarget = $reports->filter(fn($report) => str_contains($report->perfomance, 'Below Target'))->count();
        $onTarget = $reports->filter(fn($report) => str_contains($report->perfomance, 'On Target'))->count();
        $pendingTarget = $reports->filter(fn($report) => str_contains($report->perfomance, 'pending'))->count();

    @endphp

    <div class="report-table">
        <table width="100%">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Task</th>
                    <th>Duration</th>
                    <th>Perfomance</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $report)
                    @php
                        $totalUnits += $report->task->unit_type->name === 'HOUR' ? 0 : (int) $report->units_completed;
                        $totalUnitshr += $report->hourlyRate;
                        $totalDuration = $totalDuration->add($report->duration); // Add the durations together
                    @endphp
                    <tr>

                        <td>
                            <a
                                href="{{ route('projects.tasks.show', $report->project->slug) }}">{{ ucfirst($report->project->name) }}</a>
                        </td>
                        <td>{{ ucfirst($report->task->name) }}</td>

                        <td>
                            @if ($report->task->unit_type->name === 'HOUR')
                                <span style="font-size:12px;">{{ number_format($report->duration->totalMinutes, 2) }}
                                    mins</span>
                            @else
                                {{ $report->duration->forHumans(['short' => true]) }}
                            @endif

                        </td>

                        <td style="font-size:12px;">

                            <span style="color: {{ $report->perfomanceColor }}">
                                {{ $report->perfomance }}
                            </span>
                            <br>
                            <span class="uk-text-small">Target: {{ $report->task->target }}
                                <span> {{ $report->formattedTarget }} </span></span>
                        </td>

                        <td>{{ $report->started_at->format('H:i:s') }}</td>
                        <td>{{ $report->ended_at->format('H:i:s') }}</td>
                        <td>{{ $report->reported_at->format('d/m/Y') }}</td>

                    </tr>

                @empty
                    <tr class="empty-row">
                        <td colspan="7">You don't have any reports at the moment</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot style="font-size:12px;">
                <tr style="font-weight: 800">
                    <td>Total:{{ $totalProjects <= 0 ? 'N/A' : $totalProjects }} </td>
                    <td>Total:{{ $totalTasks <= 0 ? 'N/A' : $totalTasks }}</td>
                    <td>Total:{{ $totalDuration->forHumans(['short' => true]) }}</td>
                    <td>Above Target: {{ $aboveTarget ?? 'N/A' }} <br> Below Target: {{ $belowTarget ?? 'N/A' }} <br>
                        On Target: {{ $onTarget ?? 'N/A' }} <br> Pending: {{ $pendingTarget }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>

        </table>
    </div>

    <div class="information" style="width:100%; position: absolute; bottom: 0;">
        <table width="100%">
            <tr>
                <td align="left" style="width: 50%;">
                    &copy; {{ date('Y') }} {{ config('app.name') }} - All rights reserved.
                </td>
                <td align="right" style="width: 50%;">
                    Compiled by - Project Management System
                </td>
            </tr>

        </table>
    </div>
</body>

</html>
