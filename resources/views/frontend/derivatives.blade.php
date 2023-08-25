@extends('frontend.layouts.master')
@section('title', 'Nifty')
@section('content')
    @php
        use Carbon\Carbon;
    @endphp

    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading" style="display: flex">
                <div style="color:white;margin-top:16px">{{ $typeNft }} Option Chain</div>
            </div>
        </div>
    </div>
    <div style="text-align: center;margin:20px">
        <div id="updated_pcr_container"></div>
        <div class="">
            <div class="d-flex">
                <table class="nifty-table-call table-striped">
                    <!-- Call options table -->
                    <thead>
                        <tr>
                            <td colspan="6" style=" background-color: #1b2027;">
                                <b style="font-size:16px;float:left;color:white"> Calls Option
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="15.5"
                                        viewBox="0 0 16 13.5">
                                        <path id="Up" d="M8,0l8,13.5L8,10.9,0,13.5Z" fill="#0EDB67"></path>
                                    </svg></b>
                            </td>
                            <td colspan="6" style="color: red;background-color: #1b2027;">
                                <b style="font-size:16px;float:right;color:white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="15.5"
                                        viewBox="0 0 16 13.5">
                                        <path id="Down" d="M8,13.5,16,0,8,2.6,0,0Z" fill="#FF4C4C"></path>
                                    </svg>
                                    Puts Option</b>
                            </td>
                        </tr>
                        <tr style="color: #6c7687">
                            <th style="color:#ffffff">SR</th>
                            <th>Open int.</th>
                            <th>OPENINTERESTCHANGE<br> (Change In Oi)</th>
                            <th>TOTALQTYTRADED<br> (Volume)</th>
                            <th>PRICECHANGE%</th>
                            <th>LASTTRADEPRICE</th>

                            <th style="color:rgb(0, 0, 0);background-color:#ffb020">STRIKE PRICE</th>
                            <th>LASTTRADEPRICE</th>
                            <th>PRICECHANGE%</th>
                            <th>TOTALQTYTRADED<br> (Volume)</th>
                            <th>OPENINTERESTCHANGE<br> (Change In Oi)</th>
                            <th>Open Intrest</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalPutsOpenInterest = 0;
                            $totalPutsOpenInterestChange = 0;
                            $totalPutsTotalQtyTraded = 0;
                            $totalcallOpenInterest = 0;
                            $totalcallOpenInterestChange = 0;
                            $totalcallTotalQtyTraded = 0;
                            
                        @endphp

                        @foreach ($dataList as $index => $item)
                            <tr style="color:#ffffff">
                                <td style="color:#ffffff">{{ $index + 1 }}
                                    <?php
                                    
                                    $totalPutsOpenInterest += $item['put']['OPENINTEREST'];
                                    $totalPutsOpenInterestChange += $item['put']['OPENINTERESTCHANGE'];
                                    $totalPutsTotalQtyTraded += $item['put']['TOTALQTYTRADED'];
                                    $totalcallOpenInterest += $item['call']['OPENINTEREST'];
                                    $totalcallOpenInterestChange += $item['call']['OPENINTERESTCHANGE'];
                                    $totalcallTotalQtyTraded += $item['call']['TOTALQTYTRADED'];
                                    
                                    ?>
                                </td>
                                <td>{{ $item['call']['OPENINTEREST'] }}</td>

                                <td style="color: {{ $item['call']['OPENINTERESTCHANGE'] >= 0 ? '#0edb61' : '#ff4c4c' }}">
                                    {{ $item['call']['OPENINTERESTCHANGE'] }}
                                </td>




                                <td>{{ $item['call']['TOTALQTYTRADED'] }}</td>
                                <td>
                                    @php
                                        $new_OI = $item['call']['OPENINTEREST'];
                                        $change_in_OI = $item['call']['OPENINTERESTCHANGE'];
                                        $old_OI = $new_OI - $change_in_OI;
                                        $roundedPercentage = $old_OI == 0 ? 0 : ceil(($change_in_OI / $old_OI) * 100);
                                    @endphp
                                    {{ $roundedPercentage == 0 ? '-' : $roundedPercentage . '%' }}
                                </td>
                                <td>{{ $item['call']['LASTTRADEPRICE'] }}</td>

                                <td
                                    style="{{ $item['call']['value'] == $item['strike'] ? 'color:rgb(0, 0, 0);background-color:#ffb020' : 'background-color:#22272f' }}">
                                    {{ $item['call']['value'] }}
                                </td>

                                <td>{{ $item['put']['LASTTRADEPRICE'] }}</td>
                                <td>
                                    @php
                                        $new_OI = $item['put']['OPENINTEREST'];
                                        $change_in_OI = $item['put']['OPENINTERESTCHANGE'];
                                        $old_OI = $new_OI - $change_in_OI;
                                        $roundedPercentage = $old_OI == 0 ? 0 : ceil(($change_in_OI / $old_OI) * 100);
                                    @endphp
                                    {{ $roundedPercentage == 0 ? '-' : $roundedPercentage . '%' }}
                                </td>
                                <td>{{ $item['put']['TOTALQTYTRADED'] }}</td>


                                <td style="color: {{ $item['put']['OPENINTERESTCHANGE'] >= 0 ? '#0edb61' : '#ff4c4c' }}">
                                    {{ $item['put']['OPENINTERESTCHANGE'] }}
                                </td>

                                <td>{{ $item['put']['OPENINTEREST'] }}</td>
                            </tr>
                        @endforeach
                        <tr style="color: #ffb020">


                            @php
                                function displayFormattedNumber($number)
                                {
                                    if ($number >= 10000000) {
                                        return number_format($number / 10000000, 2) . ' CR';
                                    } elseif ($number >= 100000) {
                                        return number_format($number / 100000, 2) . ' L';
                                    } else {
                                        return number_format($number, 2);
                                    }
                                }
                                
                            @endphp


                            <th></th>

                            <th>{{ displayFormattedNumber($totalcallOpenInterest) }}</th>
                            <th>{{ displayFormattedNumber($totalcallOpenInterestChange) }}</th>
                            <th>{{ displayFormattedNumber($totalcallTotalQtyTraded) }}</th>

                            <th></th>
                            <th></th>

                            <th style="color:rgb(0, 0, 0);background-color:#ffb020">Total</th>


                            {{-- CR And L Function  --}}





                            <th></th>
                            <th></th>
                            <th>{{ displayFormattedNumber($totalPutsOpenInterest) }}</th>
                            <th>{{ displayFormattedNumber($totalPutsOpenInterestChange) }}</th>
                            <th>{{ displayFormattedNumber($totalPutsTotalQtyTraded) }}</th>

                        </tr>
                    </tbody>
                </table>



            </div>

            <div>
            </div>
        </div>
    </div>
    <div style="margin-bottom: 50px;margin-top: 50px;margin-left: 19px;margin-right: 17px;">
        <table style="margin:auto;width: -webkit-fill-available;text-align: center;">
            <thead>
                <tr>
                    <td colspan="12" style="background-color: #1b2027;">
                        <b style="font-size: 16px; float: left; color: white;">Intraday Data</b>
                    </td>
                </tr>
                <tr style="color: #6c7687">
                    <th style="color: #ffffff">SR</th>
                    <th>Time</th>
                    <th>Call</th>
                    <th>Put</th>
                    <th>Diff</th>
                    <th>PCR</th>
                    <th>Option Signal</th>
                    <th>VWAP</th>
                    <th>Price</th>
                    <th>VWAP Signal</th>
                </tr>
                {{-- @foreach ($currentNftDataresult as $index => $item) --}}
                <tr style="color: #dfdfdf">
                    <td style="color: #ffffff">1</td>
                    <td> @php
                        $timestamp = !empty($currentNftDataresult['SERVERTIME']) ? $currentNftDataresult['SERVERTIME'] / 1000 : null;
                        echo $timestamp ? \Carbon\Carbon::createFromTimestamp($timestamp)->format('H:i') : '-';
                    @endphp
                    </td>
                    <td>{{ $totalcallOpenInterestChange }}</td>
                    <td>{{ $totalPutsOpenInterestChange }}</td>
                    <td>{{ $totalcallOpenInterestChange - $totalPutsOpenInterestChange }}</td>
                    <td> @php
                        $pcrValue = $totalPutsOpenInterestChange / $totalcallOpenInterestChange;
                        $color = $pcrValue >= 1 ? 'green' : 'red';
                        echo '<span style="color: ' . $color . '">' . number_format($pcrValue, 2) . '</span>';
                    @endphp</td>
                    <td> @php
                        $color = $pcrValue >= 1 ? 'green' : 'red';
                        echo '<p style="color:' . $color . '">' . ($pcrValue >= 1 ? 'BUY' : 'SELL') . '</p>';
                    @endphp</td>
                    <td>{{ $currentNftDataresult['AVERAGETRADEDPRICE'] }}</td>
                    <td>{{ $currentNftDataresult['BUYPRICE'] }}</td>
                    <td>@php
                        $color = $pcrValue >= 1 ? 'green' : 'red';
                        echo '<p style="color:' . $color . '">' . ($pcrValue >= 1 ? 'BUY' : 'SELL') . '</p>';
                    @endphp</td>
                </tr>
                {{-- @endforeach --}}
            </thead>
        </table>



    </div>
    <script>
        // Calculate PCR and PCR strength
        let PCRData = calculatePCRStrength2({{ $totalcallOpenInterest }}, {{ $totalPutsOpenInterest }});
        var PCR = PCRData['PCR'].toFixed(2);
        let PCRStrength = PCRData['PCRStrength'];

        // Update the PCR value in the table and hide the old PCR value
        $("#updated_pcr_container").html(
            '<table><tr><td style="color:#ffffff;background:#ffb020;">PCR</td><td style="color:#ffffff;background:#ffb020;"">PCR Strength</td><td style="color:#ffffff;background:#ffb020;"">Updated ?</td></tr><tr><td style="color:#ffffff;">' +
            PCR +
            '</td><td style="color:#ffffff; " >' + PCRStrength +
            '</td><td style="color:#ffffff; " >' + "YES" +
            '</td></tr></table></div>');

        function calculatePCRStrength2(totalCallsOpenInterest, totalPutsOpenInterest) {
            let PCR = totalPutsOpenInterest / totalCallsOpenInterest;
            if (PCR >= 3) {
                return {
                    PCR: PCR,
                    PCRStrength: 'Strong Bullish (Strong Support)'
                };
            } else if (PCR > 1 && PCR < 3) {
                return {
                    PCR: PCR,
                    PCRStrength: 'Bullish'
                };
            } else if (PCR == 1) {
                return {
                    PCR: PCR,
                    PCRStrength: 'Neutral'
                };
            } else if (PCR > 0.33 && PCR < 1) {
                return {
                    PCR: PCR,
                    PCRStrength: 'Bearish'
                };
            } else if (PCR <= 0.33) {
                return {
                    PCR: PCR,
                    PCRStrength: 'Strong Bearish (Strong Resistance)'
                };
            } else {
                return {
                    PCR: PCR,
                    PCRStrength: 'NOT A NUMBER'
                };
            };
        }
    </script>
@endsection
