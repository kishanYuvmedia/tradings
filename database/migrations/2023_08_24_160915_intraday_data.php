<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IntradayData extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('Intradays', function (Blueprint $table) {
            $table->increments('id'); // Auto-incrementing primary key
            $table->string('AVERAGETRADEDPRICE'); // String column
            $table->float('BUYPRICE'); // Float column
            $table->float('BUYQTY')->nullable(); // Nullable float column
            $table->float('CLOSE')->nullable(); // Nullable float column
            $table->string('EXCHANGE')->nullable(); // Nullable string column
            $table->float('HIGH')->nullable(); // Nullable float column
            $table->string('INSTRUMENTIDENTIFIER')->nullable(); // Nullable string column
            $table->float('LASTTRADEPRICE')->nullable(); // Nullable float column
            $table->float('LASTTRADEQTY')->nullable(); // Nullable float column
            $table->float('LASTTRADETIME')->nullable(); // Nullable float column
            $table->float('LOW')->nullable(); // Nullable float column
            $table->float('OPEN')->nullable(); // Nullable float column
            $table->float('OPENINTEREST')->nullable(); // Nullable float column
            $table->boolean('PREOPEN')->nullable()->default(false); // Nullable boolean column with default value
            $table->float('SELLQTY')->nullable(); // Nullable float column
            $table->float('SERVERTIME')->nullable(); // Nullable float column
            $table->float('TOTALQTYTRADED')->nullable(); // Nullable float column
            $table->float('PRICECHANGE')->nullable(); // Nullable float column
            $table->float('PRICECHANGEPERCENTAGE')->nullable(); // Nullable float column
            $table->float('OPENINTERESTCHANGE')->nullable(); // Nullable float column
            $table->float('TOTALPUT')->nullable(); // Nullable float column
            $table->float('TOTALCALL')->nullable(); // Nullable float column
            $table->timestamps(); // Created at and updated at timestamps
        });
        
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Intradays');
    }
}
