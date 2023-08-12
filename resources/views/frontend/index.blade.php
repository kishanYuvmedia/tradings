@extends('frontend.layouts.master')

@section('title', 'Home')
@section('content')








    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-graph2"> </i>
                </div>

                <div style="display: flex">
                    <div class="col-md-11 col-sm-1" style="color:white;font-family: -webkit-body;font-size: 49px;margin-top: -20px;color: powderblue;">Trend Sarthi</div>
                    <div class="col-md-1 col-sm-1">
                        <div class="main-card mb-3 card">
                            <div class="card-body" style="width: 915px;">
                                

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <div class="table-responsive" style="font-size: 60px;    text-align: center;    /* margin: 0px; */">
                    🚧🛠️
                        <!-- <label for="expiry_date"><b>Select Expiry:</b></label>
                        <select style="width: 234px; height: 37px; color: #a37213;" id="expiry_date">
                            <option value="" selected>Options</option>


                            @if (isset($expiryDate1))



                                @foreach ($expiryDate1 as $option)
                                    <option value="{{ $option }}">{{ date('d-M-Y', strtotime($option)) }}</option>
                                @endforeach
                            @endif
                        </select> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    

   
 

@endsection
