@extends('frontend.layouts.master')
@section('title', 'NFO-Ranking')
@section('content')
    @php
        use Carbon\Carbon;
    @endphp


    <!DOCTYPE html>
<html>
<head>
    <title >FNO Ranking</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
            padding: 8px;
        }
    </style>
</head>
<body>
    <h1 style="color:white">FNO Ranking</h1>
    @if(count($products) > 0)
        <table>
            <thead>
                <tr>
                    <th style="color:white;text-align: center;"><b>Sr. No.</b></th>
                    <th style="color:white;text-align: center;" >~ Products ~</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $index => $product)
                    <tr>
                        <td style="color:white;text-align: center;">{{ $index + 1 }}</td>
                        <td style="color:white;text-align: center;">{{ $product }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No products found.</p>
    @endif
</body>
</html>

    @endsection
