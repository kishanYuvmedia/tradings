@extends('frontend.layouts.master')
@section('title', 'BankNifty')
@section('content')
    @php
        use Carbon\Carbon;
    @endphp




    {{-- -----------------------------------------------------------------------------------------------------Expiry Date Function  --}}

    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-graph1"> </i>
                </div>

                <div style="display: flex">
                    <div class="col-md-11 col-sm-1" style="color:white;margin-top:16px">BankNifty- Option Chain</div>
                    <div class="col-md-1 col-sm-1">
                        <div class="main-card mb-3 card">
                            <div class="card-body" style="width: 915px;">
                                <div class="table-responsive">
                                    <label for="expiry_date" class="lable-expiry-date"><b>Select Expiry:</b></label>
                                    <select style="width: 234px; height: 37px; color: #a37213;background-color:#121419"
                                        id="expiry_date">
                                        <option value="" selected>Options</option>
                                        @if (isset($expAray) && is_array($expAray) && count($expAray) > 0)
                                            @foreach ($expAray as $index => $option)
                                                <option value="{{ $option['option'] }}"
                                                    @if ($option['isUpcomingAfterInitial']) selected @endif>
                                                    {{ $option['option'] }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>




    {{-- -----------------------------------------------------------------------------------------------------Strike Range Function  --}}





    <div class="d-flex">

        <div class="row ">

            <div class="col-md-12 col-sm-12">
                <div class="main-card mb-3 card">
                    <div class="card-body d-flex" style="width: 915px;">
                        <!-- Dropdown for starting strike price -->
                        <div class="table-responsive">
                            <label for="expiry_date">
                                <b style="color: #6c7687"> <span style="color:green">START </span>STRIKE PRICE :</b>
                            </label>
                            <select style="width: 234px; height: 37px; color: #a37213;background-color:#121419"
                                id="starting">
                                <!-- Populate options from $putArr -->
                                @foreach ($putArr as $key => $value)
                                    <option value="{{ $value['value'] }}">{{ $value['value'] }}</option>
                                @endforeach


                            </select>
                        </div>
                        <!-- Dropdown for ending strike price -->
                        <div class="table-responsive">
                            <label for="expiry_date"></label>
                            <b style="color: #6c7687"><span style="color:red">END </span> STRIKEPRICE :</b>
                            </label>

                            <select style="width: 234px; height: 37px; color: #a37213;background-color:#121419"
                                id="ending">
                                <!-- Populate options from $putArr -->
                                @foreach ($putArr as $key => $value)
                                    <option value="{{ $value['value'] }}">{{ $value['value'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Button to trigger the result -->
                        <button type="button" id="result" class="button-29">Result</button>
                    </div>


                    {{-- This is a dynamic PCR  row in bankNifty For View On Top  The Value Get by calculatePCRStrength1 --}}
                    <div id="updated_pcr_container"></div>


                </div>
            </div>

        </div>




    </div>





    <div style="text-align: center;margin:20px">
        <div class="">
            @if (isset($putArr) && !empty($putArr))
                <div class="d-flex">

                    {{-- -------------------------------------------------------------------------------------------------------------------------------Calls Table Function  --}}

                    <table class="nifty-table-call table-striped">
                        <!-- Call options table -->
                        <thead>
                            <tr>
                                <td colspan="6" style=" background-color: #232a34;">
                                    <b style="font-size:16px;float:left;color:white"> Calls Option
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="15.5"
                                            viewBox="0 0 16 13.5">
                                            <path id="Up" d="M8,0l8,13.5L8,10.9,0,13.5Z" fill="#0EDB67"></path>
                                        </svg></b>
                                </td>
                            </tr>
                            <tr style="color: #6c7687">
                                <th style="color:#ffffff">SR</th>
                                <th>Open Intrest</th>
                                <th>OPENINTERESTCHANGE<br> (Change In Oi)</th>
                                <th>TOTALQTYTRADED<br> (Volume)</th>
                                <th>PRICECHANGE%</th>
                                <th>LASTTRADEPRICE</th>
                            </tr>
                        </thead>

                        <!-- Container to display updated data for calls -->
                        <tbody id="updated_call_container"></tbody>

                        <!-- Container to display Current data for calls -->
                        <tbody class="callCurrentData"style="color: white">


                            <!-- Calculation Of Total Counts   -->
                            <?php
                                $totalCallsOpenInterest = 0;
                                $totalCallsOpenInterestChange = 0;
                                $totalCallsTotalQtyTraded = 0;

                                foreach ($callArr as $key => $value) {
                                $totalCallsOpenInterest += $value['OPENINTEREST'];
                                $totalCallsOpenInterestChange += $value['OPENINTERESTCHANGE'];
                                $totalCallsTotalQtyTraded += $value['TOTALQTYTRADED'];
                            ?>

                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    {{ $value['OPENINTEREST'] == 0 ? '-' : $value['OPENINTEREST'] }}
                                </td>
                                <td
                                    style="color: {{ $value['OPENINTERESTCHANGE'] < 0 ? '#ff4c4c' : ($value['OPENINTERESTCHANGE'] > 0 ? '#0edb67' : 'white') }}">
                                    {{ $value['OPENINTERESTCHANGE'] == 0 ? '-' : $value['OPENINTERESTCHANGE'] }}
                                </td>


                                <td>
                                    {{ $value['TOTALQTYTRADED'] == 0 ? '-' : $value['TOTALQTYTRADED'] }}
                                </td>




                                {{-- Get percentageChangeOI --}}

                                <td>
                                    @php
                                        $new_OI = $value['OPENINTEREST'];
                                        $change_in_OI = $value['OPENINTERESTCHANGE'];
                                        $old_OI = $new_OI - $change_in_OI;
                                        
                                        if ($old_OI == 0) {
                                            $percentageChangeOI = 0; // Avoid division by zero
                                        } else {
                                            $percentageChangeOI = ($change_in_OI / $old_OI) * 100;
                                        }
                                        
                                        $roundedPercentage = ceil($percentageChangeOI);
                                    @endphp

                                    @if ($roundedPercentage == 0)
                                        -
                                    @else
                                        {{ $roundedPercentage }}%
                                    @endif
                                </td>







                                <td>{{ $value['LASTTRADEPRICE'] == 0 ? '-' : $value['LASTTRADEPRICE'] }}
                                </td>

                            </tr>
                            <?php } ?>
                            <!-- Add a new row to display the total counts for calls -->

                            <tr>
                                <td style="background-color: #121419;">-</td>
                                <td style="color: #ffb020">
                                    <b>
                                        @if ($totalCallsOpenInterest >= 10000000)
                                            {{ number_format($totalCallsOpenInterest / 10000000, 2) }} Cr [OI]
                                        @elseif ($totalCallsOpenInterest >= 100000)
                                            {{ number_format($totalCallsOpenInterest / 100000, 2) }} L [OI]
                                        @else
                                            {{ $totalCallsOpenInterest }} [OI]
                                        @endif
                                    </b>
                                </td>
                                <td style="color: #ffb020">
                                    <b>
                                        @if ($totalCallsOpenInterestChange >= 10000000)
                                            {{ number_format($totalCallsOpenInterestChange / 10000000, 2) }} Cr [CIOI]
                                        @elseif ($totalCallsOpenInterestChange >= 100000)
                                            {{ number_format($totalCallsOpenInterestChange / 100000, 2) }} L [CIOI]
                                        @else
                                            {{ $totalCallsOpenInterestChange }} [CIOI]
                                        @endif


                                    </b>
                                </td>
                                <td style="color: #ffb020">
                                    <b>
                                        @if ($totalCallsTotalQtyTraded >= 10000000)
                                            {{ number_format($totalCallsTotalQtyTraded / 10000000, 2) }} Cr [Traded]
                                        @elseif ($totalCallsTotalQtyTraded >= 100000)
                                            {{ number_format($totalCallsTotalQtyTraded / 100000, 2) }} L [Traded]
                                        @else
                                            {{ $totalCallsTotalQtyTraded }} [Traded]
                                        @endif
                                    </b>
                                </td>
                                <td style="background-color: #121419;">-</td>
                                <td style="background-color: #121419;">-</td>
                            </tr>

                        </tbody>
                    </table>


                    {{-- --------------------------------------------------------------------------------------------------------------------------------Puts Table Function  --}}


                    <table class="nifty-table-put">
                        <!-- Put options table -->
                        <thead>
                            <tr>
                                <td colspan="6" style="color: red;background-color: #232a34;">
                                    <b style="font-size:16px;float:right;color:white">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="15.5"
                                            viewBox="0 0 16 13.5">
                                            <path id="Down" d="M8,13.5,16,0,8,2.6,0,0Z" fill="#FF4C4C"></path>
                                        </svg>
                                        Puts Option</b>
                                </td>
                            </tr>
                            <tr style="color: #6c7687">

                                <th style="color:rgb(0, 0, 0);background-color:#ffb020">STRIKE PRICE</th>
                                <th>LASTTRADEPRICE</th>
                                <th>PRICECHANGE%</th>
                                <th>TOTALQTYTRADED<br> (Volume)</th>
                                <th>OPENINTERESTCHANGE<br> (Change In Oi)</th>
                                <th>Open Intrest</th>
                            </tr>
                        </thead>

                        <!-- Container to display updated data for Puts -->

                        <tbody id="updated_put_container"></tbody>

                        <!-- Container to display Current data for Calls -->

                        <tbody class="putCurrentData" style="color: white">
                            <?php
                                $totalPutsOpenInterest = 0;
                                $totalPutsOpenInterestChange = 0;
                                $totalPutsTotalQtyTraded = 0;

                                foreach ($putArr as $key => $value) {
                                    $totalPutsOpenInterest += $value['OPENINTEREST'];
                                    $totalPutsOpenInterestChange += $value['OPENINTERESTCHANGE'];
                                    $totalPutsTotalQtyTraded += $value['TOTALQTYTRADED'];
                            ?>
                            <tr style="color: white">

                                <td style="background-color: #22272f;border-bottom:hidden">{{ $value['value'] }}</td>
                                <td>
                                    {{ $value['LASTTRADEPRICE'] == 0 ? '-' : $value['LASTTRADEPRICE'] }}
                                </td>





                                {{-- Get percentageChangeOI --}}







                                <td>
                                    @php
                                        $new_OI = $value['OPENINTEREST'];
                                        $change_in_OI = $value['OPENINTERESTCHANGE'];
                                        $old_OI = $new_OI - $change_in_OI;
                                        
                                        if ($old_OI == 0) {
                                            $percentageChangeOI = 0; // Avoid division by zero
                                        } else {
                                            $percentageChangeOI = ($change_in_OI / $old_OI) * 100;
                                        }
                                        
                                        $roundedPercentage = ceil($percentageChangeOI);
                                    @endphp

                                    @if ($roundedPercentage == 0)
                                        -
                                    @else
                                        {{ $roundedPercentage }}%
                                    @endif
                                </td>





                                <td>
                                    {{ $value['TOTALQTYTRADED'] == 0 ? '-' : $value['TOTALQTYTRADED'] }}
                                </td>
                                <td
                                    style="color: {{ $value['OPENINTERESTCHANGE'] < 0 ? '#ff4c4c' : ($value['OPENINTERESTCHANGE'] > 0 ? '#0edb67' : 'white') }}">
                                    {{ $value['OPENINTERESTCHANGE'] == 0 ? '-' : $value['OPENINTERESTCHANGE'] }}
                                </td>
                                <td>
                                    {{ $value['OPENINTEREST'] == 0 ? '-' : $value['OPENINTEREST'] }}
                                </td>
                            </tr>
                            <?php } ?>
                            <!-- Add a new row to display the total counts for puts -->
                            <tr>
                                <td style="background-color:#ffb020;;color: #000000;"><b>-: Total :-</b></td>
                                <td rowspan="2" style="background-color: #121419">-</td>

                                <td style="background-color: #121419">-</td>






                                <td style="color: #ffb020">
                                    <b>
                                        @if ($totalPutsTotalQtyTraded >= 10000000)
                                            {{ number_format($totalPutsTotalQtyTraded / 10000000, 2) }} Cr [Traded]
                                        @elseif ($totalPutsTotalQtyTraded >= 100000)
                                            {{ number_format($totalPutsTotalQtyTraded / 100000, 2) }} L [Traded]
                                        @else
                                            {{ $totalPutsTotalQtyTraded }} [Traded]
                                        @endif


                                    </b>
                                </td>
                                <td style="color: #ffb020"><b>
                                        @if ($totalPutsOpenInterestChange >= 10000000)
                                            {{ number_format($totalPutsOpenInterestChange / 10000000, 2) }} Cr [CIOI]
                                        @elseif ($totalPutsOpenInterestChange >= 100000)
                                            {{ number_format($totalPutsOpenInterestChange / 100000, 2) }} L [CIOI]
                                        @else
                                            {{ $totalPutsOpenInterestChange }} [CIOI]
                                        @endif
                                </td>
                                <td style="color: #ffb020">
                                    <b>
                                        @if ($totalPutsOpenInterest >= 10000000)
                                            {{ number_format($totalPutsOpenInterest / 10000000, 2) }} Cr [Oi]
                                        @elseif ($totalPutsOpenInterest >= 100000)
                                            {{ number_format($totalPutsOpenInterest / 100000, 2) }} L [Oi]
                                        @else
                                            {{ $totalPutsOpenInterest }} [Oi]
                                        @endif
                                    </b>
                                </td>
                            </tr>
                        </tbody>
                    </table>






                </div>
            @else
                <p>No option chain data available</p>
            @endif

            <div>
                {{-- --------------------------------------------------------------------------------------------------------------Calculate PCR and PCR strength --}}

                <?php
                function calculatePCRStrength1($totalCallsOpenInterest, $totalPutsOpenInterest)
                {
                    $PCR = $totalPutsOpenInterest / $totalCallsOpenInterest;
                
                    if ($PCR >= 3) {
                        return ['PCR' => $PCR, 'PCRStrength' => 'Strong Bullish (Strong Support)'];
                    } elseif ($PCR > 1 && $PCR < 3) {
                        return ['PCR' => $PCR, 'PCRStrength' => 'Bullish'];
                    } elseif ($PCR == 1) {
                        return ['PCR' => $PCR, 'PCRStrength' => 'Neutral'];
                    } elseif ($PCR > 0.33 && $PCR < 1) {
                        return ['PCR' => $PCR, 'PCRStrength' => 'Bearish'];
                    } elseif ($PCR <= 0.33) {
                        return ['PCR' => $PCR, 'PCRStrength' => 'Strong Bearish (Strong Resistance)'];
                    } else {
                        return ['PCR' => $PCR, 'PCRStrength' => 'NOT A NUMBER'];
                    }
                }
                
                $PCRData = calculatePCRStrength1($totalCallsOpenInterest, $totalPutsOpenInterest);
                $PCR = $PCRData['PCR'];
                $PCRStrength = $PCRData['PCRStrength'];
                
                ?>



                <!-- -------------------------------------------------------------------------------------------------------------Display the data in another table -->


                <!-- Send Both Velue On Top For Display PCR Data -->



                <!-- Container to display updated data for calls -->
                <div id="updated_call_container"></div>
                <!-- Container to display updated data for puts -->
                <div id="updated_put_container"></div>
                <!-- Container to display updated PCR and PCR strength -->




            </div>
        </div>
    </div>



    {{-- ------------------------------------------------------------------------------------------------------------------------------------------------Expiry Date Function & Strike Price Function --}}


    {{-- This methode For Display data In Frontend  --}}

    <script type="text/javascript">
        $(document).ready(function() {


            $("#updated_pcr_container").html(
                '<div class="d-flex"><table><tr><td style="color:#ffffff;background:#ffb020">PCR</td><td style="color:#ffffff;background:#ffb020">PCR Strength</td></tr><tr><td style="color:#ffffff;" ><?php echo number_format($PCR, 2); ?></td><td style="color:#ffffff;"><?php echo $PCRStrength; ?></td></tr></table></div>'
            );

            // Choosing starting strike updates ending strike.
            // Function to update the ending strike price dropdown when starting strike price changes
            $("#starting").change(function() {
                // Get the selected value of the starting strike price dropdown
                const selectedStartingStrikePrice = parseFloat($(this).val());

                // Show only options in the ending strike price dropdown that are strictly greater than the selected value
                $("#ending option").each(function() {
                    const endingStrikePrice = parseFloat($(this).val());
                    if (endingStrikePrice >= selectedStartingStrikePrice) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });



                // If the currently selected value in the ending strike price dropdown is less than or equal to the selected value in the starting strike price dropdown, change the selected value to the first greater option
                const selectedEndingStrikePrice = parseFloat($("#ending").val());
                if (selectedEndingStrikePrice < selectedStartingStrikePrice) {
                    let found = false;
                    $("#ending option").each(function() {
                        const endingStrikePrice = parseFloat($(this).val());

                        if (endingStrikePrice >= selectedStartingStrikePrice && !found) {
                            $("#ending").val(endingStrikePrice);
                            found = true; // Break the loop after setting the new value
                        }
                    });
                }
            });

        });






        // This function calculatePCRStrength2 

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



        $("#expiry_date").change(function() {

            // to get real strikeprice in dropdown 
            let strikePrice = [];

            const selectedOption = $(this).val();
            $.ajax({
                url: '{{ URL::to('get-bankniftywithDt') }}/' + selectedOption,
                type: 'GET',
                success: function(response) {

                    // Display data after change Expiry date  

                    let updatedHtml = '<div class="d-flex "><table>';
                    response.callArr.forEach(function(item, key) {
                        updatedHtml += '<tr>';

                        updatedHtml += '<td style="color:white">' + (key + 1) +
                            '</td>';
                        updatedHtml += '<td style="color:white">' + (item
                            .OPENINTEREST == 0 ?
                            '-' : item.OPENINTEREST) + '</td>';

                        updatedHtml += '<td style="color: ' + (item
                            .OPENINTERESTCHANGE < 0 ?
                            '#ff4c4c' : (item.OPENINTERESTCHANGE > 0 ?
                                '#0edb67' : 'white')
                        ) + '">';
                        updatedHtml += (item.OPENINTERESTCHANGE == 0 ? '-' : item
                            .OPENINTERESTCHANGE);
                        updatedHtml += '</td>';

                        updatedHtml += '<td style="color:white">' + (item
                            .TOTALQTYTRADED == 0 ?
                            '-' : item.TOTALQTYTRADED) + '</td>';
                        updatedHtml += '<td style="color:white">' + (item
                            .PRICECHANGEPERCENTAGE == 0 ? '-' : item
                            .PRICECHANGEPERCENTAGE
                        ) + '</td>';
                        updatedHtml += '<td style="color:white">' + (item
                            .LASTTRADEPRICE == 0 ?
                            '-' : item.LASTTRADEPRICE) + '</td>';
                        updatedHtml += '</tr>';
                    });

                    updatedHtml += '</table></div>';

                    $("#updated_call_container").html(updatedHtml);
                    $(".callCurrentData").hide();

                    let strikeRange = '';
                    let updatedHtml1 = '<div class="d-flex "><table>';

                    response.putArr.forEach(function(item) {
                        // to get real strikeprice in dropdown  And Display In View File 

                        strikeRange += '<option>' + item.value + '</option>';
                        strikePrice.push(item.value);

                        $('#starting').html('<option></option>')

                        updatedHtml1 += '<tr>';
                        updatedHtml1 +=
                            '<td style="color:white; background-color: #22272f; border-bottom: hidden;">' +
                            item.value + '</td>';
                        updatedHtml1 += '<td style="color:white">' + (item
                            .LASTTRADEPRICE == 0 ?
                            '-' : item.LASTTRADEPRICE) + '</td>';
                        updatedHtml1 += '<td style="color:white">' + (item
                            .PRICECHANGEPERCENTAGE == 0 ? '-' : item
                            .PRICECHANGEPERCENTAGE
                        ) + '</td>';
                        updatedHtml1 += '<td style="color:white">' + (item
                            .TOTALQTYTRADED == 0 ?
                            '-' : item.TOTALQTYTRADED) + '</td>';
                        updatedHtml1 += '<td style="color: ' + (item
                            .OPENINTERESTCHANGE < 0 ?
                            '#ff4c4c' : (item.OPENINTERESTCHANGE > 0 ?
                                '#0edb67' : 'white')
                        ) + '">';
                        updatedHtml1 += (item.OPENINTERESTCHANGE == 0 ? '-' : item
                            .OPENINTERESTCHANGE);
                        updatedHtml += '</td>';
                        updatedHtml1 += '<td style="color:white">' + (item
                            .OPENINTEREST == 0 ?
                            '-' : item.OPENINTEREST) + '</td>';
                        updatedHtml1 += '</tr>';
                    });
                    $('#starting').html(strikeRange);
                    $('#ending').html(strikeRange);
                    updatedHtml1 += '</table></div>';
                    $("#updated_put_container").html(updatedHtml1);
                    $(".putCurrentData").hide();

                    // Calculate PCR and PCR strength
                    let totalCallsOpenInterest = 0;
                    let totalPutsOpenInterest = 0;
                    let totalCallsOpenInterestChange = 0;
                    let totalCallsTotalQtyTraded = 0;
                    response.callArr.forEach(function(item) {
                        totalCallsOpenInterest += item.OPENINTEREST;
                        totalCallsOpenInterestChange += item.OPENINTERESTCHANGE;
                        totalCallsTotalQtyTraded += item.TOTALQTYTRADED;
                    });


                    let totalPutsOpenInterestChange = 0;
                    let totalPutsTotalQtyTraded = 0;

                    response.putArr.forEach(function(item) {
                        totalPutsOpenInterest += item.OPENINTEREST;
                        totalPutsOpenInterestChange += item.OPENINTERESTCHANGE;
                        totalPutsTotalQtyTraded += item.TOTALQTYTRADED;
                    });
                    let totalCallsHtml = '<tr>';
                    totalCallsHtml += '<td></td>';

                    // -----------------------------------total value in ajax function

                    function formatInterest(value) {
                        if (value >= 10000000) {
                            return (value / 10000000).toFixed(2) + ' Cr';
                        } else if (value >= 100000) {
                            return (value / 100000).toFixed(2) + ' L';
                        } else {
                            return value + ' ';
                        }
                    }

                    // -----------------------------------Total Formatted Calls Value In Ajax Function


                    var formattedCallsOpenInterest = formatInterest(totalCallsOpenInterest);
                    var formattedCallsOpenInterestChange = formatInterest(totalCallsOpenInterestChange);
                    var formattedCallsTotalQtyTraded = formatInterest(totalCallsTotalQtyTraded);



                    var formattedPutsTotalQtyTraded = formatInterest(totalPutsTotalQtyTraded);
                    var formattedPutsOpenInterestChange = formatInterest(totalPutsOpenInterestChange);
                    var formattedPutsOpenInterest = formatInterest(totalPutsOpenInterest);



                    totalCallsHtml += '<td style="color: #ffb020"> ' + formattedCallsOpenInterest +
                        ' oi</td>';
                    totalCallsHtml += '<td  style="color: #ffb020">' +
                        formattedCallsOpenInterestChange +
                        ' cioi</td>';

                    totalCallsHtml += '<td  style="color: #ffb020">' +
                        formattedCallsTotalQtyTraded +
                        ' Traded</td>';

                    totalCallsHtml += '<td style="color:white">-</td>';
                    totalCallsHtml += '<td style="color:white">-</td>';
                    totalCallsHtml += '</tr>';


                    let totalPutsHtml = '<tr>';
                    totalPutsHtml +=
                        '<td style="background-color:#ffb020;;color: #000000;">-: Total :-</td>';
                    totalPutsHtml += '<td style="color:white">-</td>';
                    totalPutsHtml += '<td style="color:white">-</td>';
                    totalPutsHtml += '<td style="color: #ffb020">' +
                        formattedPutsTotalQtyTraded +
                        ' Traded</td>';
                    totalPutsHtml += '<td style="color: #ffb020">' +
                        formattedPutsOpenInterestChange +
                        ' cioi</td>';
                    totalPutsHtml += '<td style="color: #ffb020">' + formattedPutsOpenInterest +
                        ' oi</td>';
                    totalPutsHtml += '</tr>';
                    // Append the total counts to the table
                    $("#updated_call_container").append(totalCallsHtml);
                    $("#updated_put_container").append(totalPutsHtml);


                    // Calculate PCR and PCR strength
                    let PCRData = calculatePCRStrength2(totalCallsOpenInterest,
                        totalPutsOpenInterest);
                    var PCR = PCRData['PCR'].toFixed(2);
                    let PCRStrength = PCRData['PCRStrength'];

                    // Update the PCR value in the table and hide the old PCR value
                    $("#updated_pcr_container").html(
                        '<div class="d-flex "><table><tr><td style="color:#ffffff;background:#ffb020;">PCR</td><td style="color:#ffffff;background:#ffb020;"">PCR Strength</td><td style="color:#ffffff;background:#ffb020;"">Updated ?</td></tr><tr><td style="color:#ffffff;">' +
                        PCR +
                        '</td><td style="color:#ffffff; " >' + PCRStrength +
                        '</td><td style="color:#ffffff; " >' + "YES" +
                        '</td></tr></table></div>');
                    console.log(strikePrice)
                },
                error: function(error) {
                    console.log(error);
                }
            });


        });



        // ----------------------------------------------------------------------------------------------------------------------------------- Strike Price  Range 

        $("#result").click(function(e) {

            let starting = $("#starting").val();
            let ending = $("#ending").val();
            let selectedDt = $("#expiry_date").val();

            $.ajax({
                url: '{{ URL::to('get-bankniftywithDt') }}/' + selectedDt,
                type: 'GET',
                data: {
                    starting: starting,
                    ending: ending
                },
                success: function(response) {
                    let updatedHtml = '<div class="d-flex "><table>';

                    let totalCallsOpenInterest1 = 0;
                    let totalPutsOpenInterest1 = 0;

                    response.callArr.forEach(function(item, key) {
                        totalCallsOpenInterest1 += item.OPENINTEREST;
                        updatedHtml += '<tr>';
                        updatedHtml += '<td style="color:white">' + parseInt(key +
                            1) + '</td>';
                        updatedHtml += '<td style="color:white">' + (item
                            .OPENINTEREST == 0 ?
                            '-' : item.OPENINTEREST) + '</td>';
                        updatedHtml += '<td style="color: ' + (item
                            .OPENINTERESTCHANGE < 0 ?
                            '#ff4c4c' : (item.OPENINTERESTCHANGE > 0 ?
                                '#0edb67' : 'white')
                        ) + '">';
                        updatedHtml += (item.OPENINTERESTCHANGE == 0 ? '-' : item
                            .OPENINTERESTCHANGE);
                        updatedHtml += '</td>';
                        updatedHtml += '<td style="color:white">' + (item
                            .TOTALQTYTRADED == 0 ?
                            '-' : item.TOTALQTYTRADED) + '</td>';
                        updatedHtml += '<td style="color:white">' + (item
                            .PRICECHANGEPERCENTAGE == 0 ? '-' : item
                            .PRICECHANGEPERCENTAGE
                        ) + '</td>';
                        updatedHtml += '<td style="color:white">' + (item
                            .LASTTRADEPRICE == 0 ?
                            '-' : item.LASTTRADEPRICE) + '</td>';
                        updatedHtml += '</tr>';
                    });
                    updatedHtml += '</table></div>';
                    $("#updated_call_container").html(updatedHtml);
                    $(".callCurrentData").hide();

                    let updatedHtml1 = '<div class="d-flex "><table>';
                    response.putArr.forEach(function(item) {
                        totalPutsOpenInterest1 += item.OPENINTEREST;



                        updatedHtml1 += '<tr>';
                        updatedHtml1 +=
                            '<td  style="color:white; background-color: #22272f; border-bottom: hidden;" >' +
                            item.value + '</td>';
                        updatedHtml1 += '<td style="color:white">' + (item
                            .LASTTRADEPRICE == 0 ?
                            '-' : item.LASTTRADEPRICE) + '</td>';
                        updatedHtml1 += '<td style="color:white">' + (item
                            .PRICECHANGEPERCENTAGE == 0 ? '-' : item
                            .PRICECHANGEPERCENTAGE
                        ) + '</td>';
                        updatedHtml1 += '<td style="color:white">' + (item
                            .TOTALQTYTRADED == 0 ?
                            '-' : item.TOTALQTYTRADED) + '</td>';
                        updatedHtml1 += '<td style="color: ' + (item
                            .OPENINTERESTCHANGE < 0 ?
                            '#ff4c4c' : (item.OPENINTERESTCHANGE > 0 ?
                                '#0edb67' : 'white')
                        ) + '">';
                        updatedHtml1 += (item.OPENINTERESTCHANGE == 0 ? '-' : item
                            .OPENINTERESTCHANGE);
                        updatedHtml += '</td>';
                        updatedHtml1 += '<td style="color:white">' + (item
                            .OPENINTEREST == 0 ?
                            '-' : item.OPENINTEREST) + '</td>';
                        updatedHtml1 += '</tr>';
                    });


                    let PCRData = calculatePCRStrength2(totalCallsOpenInterest1,
                        totalPutsOpenInterest1);
                    var PCR = PCRData['PCR'].toFixed(2);
                    let PCRStrength = PCRData['PCRStrength'];


                    updatedHtml1 += '</table></div>';

                    $("#updated_pcr_container").html(
                        '<div class="d-flex "><table><tr><td style="color:#ffffff;background:#ffb020;">PCR</td><td style="color:#ffffff;background:#ffb020;"">PCR Strength</td><td style="color:#ffffff;background:#ffb020;"">Updated ?</td></tr><tr><td style="color:#ffffff;">' +
                        PCR +
                        '</td><td style="color:#ffffff; " >' + PCRStrength +
                        '</td><td style="color:#ffffff; " >' + "YES" +
                        '</td></tr></table></div>');


                    console.log(updatedHtml1)
                    $("#updated_put_container").html(updatedHtml1);
                    $(".putCurrentData").hide();


                    //     ----------------------------------------------------total count final code---------------------------------------

                    // Update the total counts for calls
                    let totalCallsOpenInterest = 0;
                    let totalCallsOpenInterestChange = 0;
                    let totalCallsTotalQtyTraded = 0;
                    response.callArr.forEach(function(item) {
                        totalCallsOpenInterest += item.OPENINTEREST;
                        totalCallsOpenInterestChange += item.OPENINTERESTCHANGE;
                        totalCallsTotalQtyTraded += item.TOTALQTYTRADED;
                    });

                    // Update the total counts for puts
                    let totalPutsOpenInterest = 0;
                    let totalPutsOpenInterestChange = 0;
                    let totalPutsTotalQtyTraded = 0;
                    response.putArr.forEach(function(item) {
                        totalPutsOpenInterest += item.OPENINTEREST;
                        totalPutsOpenInterestChange += item.OPENINTERESTCHANGE;
                        totalPutsTotalQtyTraded += item.TOTALQTYTRADED;
                    });











                    function formatInterest(value) {
                        if (value >= 10000000) {
                            return (value / 10000000).toFixed(2) + ' Cr';
                        } else if (value >= 100000) {
                            return (value / 100000).toFixed(2) + ' L';
                        } else {
                            return value + ' ';
                        }
                    }

                    // -----------------------------------Total Formatted Calls Value In Ajax Function


                    var formattedCallsOpenInterest = formatInterest(totalCallsOpenInterest);
                    var formattedCallsOpenInterestChange = formatInterest(totalCallsOpenInterestChange);
                    var formattedCallsTotalQtyTraded = formatInterest(totalCallsTotalQtyTraded);



                    var formattedPutsTotalQtyTraded = formatInterest(totalPutsTotalQtyTraded);
                    var formattedPutsOpenInterestChange = formatInterest(totalPutsOpenInterestChange);
                    var formattedPutsOpenInterest = formatInterest(totalPutsOpenInterest);



                    // Update the total counts for calls and puts in the table
                    let totalCallsHtml = '<tr>';
                    totalCallsHtml += '<td style="color:white">-</td>';
                    totalCallsHtml += '<td style="color:#ffb020">' +
                        formattedCallsOpenInterest +
                        ' oi</td>';
                    totalCallsHtml += '<td style="color:#ffb020">' +
                        formattedCallsOpenInterestChange +
                        ' cioi</td>';
                    totalCallsHtml += '<td style="color:#ffb020">' +
                        formattedCallsTotalQtyTraded +
                        ' Traded</td>';
                    totalCallsHtml += '<td style="color:white">-</td>';
                    totalCallsHtml += '<td style="color:white">-</td>';
                    totalCallsHtml += '</tr>';

                    let totalPutsHtml = '<tr>';
                    totalPutsHtml +=
                        '<td style="background-color:#ffb020;;color: #000000;">-: Total :-</td>';
                    totalPutsHtml += '<td style="color:white">-</td>';
                    totalPutsHtml += '<td style="color:white">-</td>';
                    totalPutsHtml += '<td style="color:#ffb020">' +
                        formattedPutsTotalQtyTraded +
                        ' Traded</td>';
                    totalPutsHtml += '<td style="color:#ffb020">' +
                        formattedPutsOpenInterestChange +
                        ' cioi</td>';
                    totalPutsHtml += '<td style="color:#ffb020">' + formattedPutsOpenInterest +
                        ' oi</td>';
                    totalPutsHtml += '</tr>';

                    // Append the total counts to the table
                    $("#updated_call_container").append(totalCallsHtml);
                    $("#updated_put_container").append(totalPutsHtml);

                    console.log(response);


                    //---------------------------------------------------------------------------END---------------------------------------------








                },
                error: function(error) {

                    console.log(error);
                }
            });

        })
    </script>


@endsection
