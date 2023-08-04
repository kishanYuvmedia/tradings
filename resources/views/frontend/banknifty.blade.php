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
                                    <select style="width: 234px; height: 37px; color: #a37213;background-color:#121419""
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
                        <div class="table-responsive">
                            <label for="expiry_date"><b style="color: #6c7687"> <span style="color:green">START </span>
                                    STRIKE PRICE :</b></label>
                            <select   style="width: 234px; height: 37px; color: #a37213;background-color:#121419"
                                id="starting">
                                @foreach ($putArr as $key => $value)
                                    <option value="{{ $value['value'] }}">{{ $value['value'] }}</option>
                                @endforeach


                            </select>
                        </div>
                        <div class="table-responsive">
                            <label for="expiry_date"><b style="color: #6c7687"> <span style="color:red">END </span> STRIKE
                                    PRICE :</b></></label>

                            <select style="width: 234px; height: 37px; color: #a37213;background-color:#121419"
                                id="ending">
                                @foreach ($putArr as $key => $value)
                                    <option value="{{ $value['value'] }}">{{ $value['value'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" id="result" class="button-29">Result</button>
                         
                    </div>
                     <div id="updated_pcr_container"></div>
                </div>
            </div>

        </div>




    </div>





    <div style="text-align: center;margin:20px">
        <div class="">
            @if (isset($putArr) && !empty($putArr))
                <div class="d-flex  ">



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
                        <tbody id="updated_call_container"></tbody>
                        <tbody class="callCurrentData"style="color: white">

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
                                <td> {{ $value['PRICECHANGEPERCENTAGE'] == 0 ? '-' : $value['PRICECHANGEPERCENTAGE'] }}
                                </td>
                                <td>{{ $value['LASTTRADEPRICE'] == 0 ? '-' : $value['LASTTRADEPRICE'] }}
                                </td>

                            </tr>
                            <?php } ?>
                            <!-- Add a new row to display the total counts for calls -->

                            <tr>
                                <td style="background-color: #121419;">-</td>
                                <td style="color: #ffb020"><b>{{ $totalCallsOpenInterest }} oi</b></td>
                                <td style="color: #ffb020"><b>{{ $totalCallsOpenInterestChange }}cioi</b></td>
                                <td style="color: #ffb020"><b>{{ $totalCallsTotalQtyTraded }} </b> Traded </td>
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

                        <tbody id="updated_put_container"></tbody>
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
                                <td>
                                    {{ $value['PRICECHANGEPERCENTAGE'] == 0 ? '-' : $value['PRICECHANGEPERCENTAGE'] }}
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
                                <td style="color: #ffb020"><b> {{ $totalPutsTotalQtyTraded }} Traded</td>
                                <td style="color: #ffb020"><b>{{ $totalPutsOpenInterestChange }} cioi</b></td>
                                <td style="color: #ffb020"><b>{{ $totalPutsOpenInterest }} oi</b></td>
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

                <!-- Container to display updated data for calls -->
                <div id="updated_call_container"></div>
                <!-- Container to display updated data for puts -->
                <div id="updated_put_container"></div>
                <!-- Container to display updated PCR and PCR strength -->
               

                

            </div>
        </div>
    </div>



    {{-- ------------------------------------------------------------------------------------------------------------------------------------------------Expiry Date Function & Strike Price Function --}}

    <script type="text/javascript">
         
         $(document).ready(function(){
          
           
         $("#updated_pcr_container").html('<div class="d-flex"><table><tr><td style="color:#ffffff;background:#ffb020">PCR</td><td style="color:#ffffff;background:#ffb020">PCR Strength</td></tr><tr><td style="color:#ffffff;" ><?php echo $PCR; ?></td><td style="color:#ffffff;"><?php echo $PCRStrength; ?></td></tr></table></div>');
        
        })

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
             let strikePrice = [];
            const selectedOption = $(this).val();
            $.ajax({
                url: '{{ URL::to('get-bankniftywithDt') }}/' + selectedOption,
                type: 'GET',
                success: function(response) {

                    let updatedHtml = '<div class="d-flex "><table>';
                    response.callArr.forEach(function(item, key) {
                        updatedHtml += '<tr>';

                        updatedHtml += '<td style="color:white">' + (key + 1) + '</td>';
                        updatedHtml += '<td style="color:white">' + (item.OPENINTEREST == 0 ?
                            '-' : item.OPENINTEREST) + '</td>';

                        updatedHtml += '<td style="color: ' + (item.OPENINTERESTCHANGE < 0 ?
                            '#ff4c4c' : (item.OPENINTERESTCHANGE > 0 ? '#0edb67' : 'white')
                        ) + '">';
                        updatedHtml += (item.OPENINTERESTCHANGE == 0 ? '-' : item
                            .OPENINTERESTCHANGE);
                        updatedHtml += '</td>';

                        updatedHtml += '<td style="color:white">' + (item.TOTALQTYTRADED == 0 ?
                            '-' : item.TOTALQTYTRADED) + '</td>';
                        updatedHtml += '<td style="color:white">' + (item
                            .PRICECHANGEPERCENTAGE == 0 ? '-' : item.PRICECHANGEPERCENTAGE
                        ) + '</td>';
                        updatedHtml += '<td style="color:white">' + (item.LASTTRADEPRICE == 0 ?
                            '-' : item.LASTTRADEPRICE) + '</td>';
                        updatedHtml += '</tr>';
                    });
                    updatedHtml += '</table></div>';
                    $("#updated_call_container").html(updatedHtml);
                    $(".callCurrentData").hide();
                    let strikeRange = '';
                    let updatedHtml1 = '<div class="d-flex "><table>';
                    response.putArr.forEach(function(item) {
                            strikeRange+='<option>'+item.value+'</option>';
                           strikePrice.push(item.value);

                           $('#starting').html('<option></option>')

                        updatedHtml1 += '<tr>';
                        updatedHtml1 +=
                            '<td style="color:white; background-color: #22272f; border-bottom: hidden;">' +
                            item.value + '</td>';
                        updatedHtml1 += '<td style="color:white">' + (item.LASTTRADEPRICE == 0 ?
                            '-' : item.LASTTRADEPRICE) + '</td>';
                        updatedHtml1 += '<td style="color:white">' + (item
                            .PRICECHANGEPERCENTAGE == 0 ? '-' : item.PRICECHANGEPERCENTAGE
                        ) + '</td>';
                        updatedHtml1 += '<td style="color:white">' + (item.TOTALQTYTRADED == 0 ?
                            '-' : item.TOTALQTYTRADED) + '</td>';
                        updatedHtml1 += '<td style="color: ' + (item.OPENINTERESTCHANGE < 0 ?
                            '#ff4c4c' : (item.OPENINTERESTCHANGE > 0 ? '#0edb67' : 'white')
                        ) + '">';
                        updatedHtml1 += (item.OPENINTERESTCHANGE == 0 ? '-' : item
                            .OPENINTERESTCHANGE);
                        updatedHtml += '</td>';
                        updatedHtml1 += '<td style="color:white">' + (item.OPENINTEREST == 0 ?
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
                    totalCallsHtml += '<td style="color: #ffb020"> ' + totalCallsOpenInterest +
                        ' oi</td>';
                    totalCallsHtml += '<td  style="color: #ffb020">' + totalCallsOpenInterestChange +
                        ' cioi</td>';
                    totalCallsHtml += '<td  style="color: #ffb020">' + totalCallsTotalQtyTraded +
                        ' Traded</td>';
                    totalCallsHtml += '<td style="color:white">-</td>';
                    totalCallsHtml += '<td style="color:white">-</td>';
                    totalCallsHtml += '</tr>';


                      let totalPutsHtml = '<tr>';
                    totalPutsHtml +=
                        '<td style="background-color:#ffb020;;color: #000000;">-: Total :-</td>';
                    totalPutsHtml += '<td style="color:white">-</td>';
                    totalPutsHtml += '<td style="color:white">-</td>';
                    totalPutsHtml += '<td style="color: #ffb020">' + totalPutsTotalQtyTraded +
                        ' Traded</td>';
                    totalPutsHtml += '<td style="color: #ffb020">' + totalPutsOpenInterestChange +
                        ' cioi</td>';
                    totalPutsHtml += '<td style="color: #ffb020">' + totalPutsOpenInterest + ' oi</td>';
                    totalPutsHtml += '</tr>';
                    // Append the total counts to the table
                    $("#updated_call_container").append(totalCallsHtml);
                    $("#updated_put_container").append(totalPutsHtml);


                    // Calculate PCR and PCR strength
                    let PCRData = calculatePCRStrength2(totalCallsOpenInterest, totalPutsOpenInterest);
                    let PCR = PCRData['PCR'];
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
                        updatedHtml += '<td style="color:white">' + parseInt(key + 1) + '</td>';
                        updatedHtml += '<td style="color:white">' + (item.OPENINTEREST == 0 ?
                            '-' : item.OPENINTEREST) + '</td>';
                        updatedHtml += '<td style="color: ' + (item.OPENINTERESTCHANGE < 0 ?
                            '#ff4c4c' : (item.OPENINTERESTCHANGE > 0 ? '#0edb67' : 'white')
                        ) + '">';
                        updatedHtml += (item.OPENINTERESTCHANGE == 0 ? '-' : item
                            .OPENINTERESTCHANGE);
                        updatedHtml += '</td>';
                        updatedHtml += '<td style="color:white">' + (item.TOTALQTYTRADED == 0 ?
                            '-' : item.TOTALQTYTRADED) + '</td>';
                        updatedHtml += '<td style="color:white">' + (item
                            .PRICECHANGEPERCENTAGE == 0 ? '-' : item.PRICECHANGEPERCENTAGE
                        ) + '</td>';
                        updatedHtml += '<td style="color:white">' + (item.LASTTRADEPRICE == 0 ?
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
                        updatedHtml1 += '<td style="color:white">' + (item.LASTTRADEPRICE == 0 ?
                            '-' : item.LASTTRADEPRICE) + '</td>';
                        updatedHtml1 += '<td style="color:white">' + (item
                            .PRICECHANGEPERCENTAGE == 0 ? '-' : item.PRICECHANGEPERCENTAGE
                        ) + '</td>';
                        updatedHtml1 += '<td style="color:white">' + (item.TOTALQTYTRADED == 0 ?
                            '-' : item.TOTALQTYTRADED) + '</td>';
                        updatedHtml1 += '<td style="color: ' + (item.OPENINTERESTCHANGE < 0 ?
                            '#ff4c4c' : (item.OPENINTERESTCHANGE > 0 ? '#0edb67' : 'white')
                        ) + '">';
                        updatedHtml1 += (item.OPENINTERESTCHANGE == 0 ? '-' : item
                            .OPENINTERESTCHANGE);
                        updatedHtml += '</td>';
                        updatedHtml1 += '<td style="color:white">' + (item.OPENINTEREST == 0 ?
                            '-' : item.OPENINTEREST) + '</td>';
                        updatedHtml1 += '</tr>';
                    });


                    let PCRData = calculatePCRStrength2(totalCallsOpenInterest1, totalPutsOpenInterest1);
                    let PCR = PCRData['PCR'];
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

                    // Update the total counts for calls and puts in the table
                    let totalCallsHtml = '<tr>';
                    totalCallsHtml += '<td style="color:white">-</td>';
                    totalCallsHtml += '<td style="color:#ffb020">' + totalCallsOpenInterest +
                        ' oi</td>';
                    totalCallsHtml += '<td style="color:#ffb020">' + totalCallsOpenInterestChange +
                        ' cioi</td>';
                    totalCallsHtml += '<td style="color:#ffb020">' + totalCallsTotalQtyTraded +
                        ' Traded</td>';
                    totalCallsHtml += '<td style="color:white">-</td>';
                    totalCallsHtml += '<td style="color:white">-</td>';
                    totalCallsHtml += '</tr>';

                    let totalPutsHtml = '<tr>';
                    totalPutsHtml +=
                        '<td style="background-color:#ffb020;;color: #000000;">-: Total :-</td>';
                    totalPutsHtml += '<td style="color:white">-</td>';
                    totalPutsHtml += '<td style="color:white">-</td>';
                    totalPutsHtml += '<td style="color:#ffb020">' + totalPutsTotalQtyTraded +
                        ' Traded</td>';
                    totalPutsHtml += '<td style="color:#ffb020">' + totalPutsOpenInterestChange +
                        ' cioi</td>';
                    totalPutsHtml += '<td style="color:#ffb020">' + totalPutsOpenInterest + ' oi</td>';
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
