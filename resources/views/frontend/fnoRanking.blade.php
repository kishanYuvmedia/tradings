@extends('frontend.layouts.master')
@section('title', 'BankNifty')
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
                    <th style="color:white"><b>Sr. No.</b></th>
                    <th style="color:white">Products</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $index => $product)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="color:white">{{ $product }}</td>
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
