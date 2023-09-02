<?php

namespace App\Http\Controllers\Frontend;
use Illuminate\Support\Collection;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Intraday;
use View;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
use Ratchet\Client\WebSocket;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        return view('frontend.index');
    }

    public function Nifty()
    {
        $expiryApiUrl = 'http://nimblerest.lisuns.com:4531/GetExpiryDates/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=NIFTY';
        $expApiResult = Http::get($expiryApiUrl)->json();
        $expDt = $expApiResult['EXPIRYDATES'] ?? [];

        $currentTimestamp = time();
        $expArray = [];
        $selectedDate = null;

        foreach ($expDt as $option) {
            $carbonDate = Carbon::createFromFormat('dMY', $option);
            $timestamp = $carbonDate->timestamp;
            $isUpcomingOrCurrent = $timestamp >= $currentTimestamp;
            $isUpcomingAfterInitial = empty($selectedDate) && $isUpcomingOrCurrent;

            if ($isUpcomingAfterInitial) {
                $selectedDate = $option;
            }

            $expArray[] = [
                'option' => $option,
                'isUpcomingAfterInitial' => $isUpcomingAfterInitial,
            ];
        }

        $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=NIFTY&expiry=' . $selectedDate;

        try {
            $apiResult = Http::get($apiEndpoint)->json();

            $putArr = [];
            $callArr = [];

            foreach ($apiResult as $result) {
                $identi = explode('_', $result['INSTRUMENTIDENTIFIER']);
                $value = end($identi);

                if ($identi[3] == 'CE') {
                    $callArr[] = array_merge($result, ['value' => $value]);
                } elseif ($identi[3] == 'PE') {
                    $putArr[] = array_merge($result, ['value' => $value]);
                }
            }

            $putArr = array_map(function ($item) {
                $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                $item['value'] = end($identi);
                return $item;
            }, $putArr);

            $callArr = array_map(function ($item) {
                $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                $item['value'] = end($identi);
                return $item;
            }, $callArr);

            return view('frontend.nifty', compact('putArr', 'callArr', 'expArray'));
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return view('frontend.nifty', ['putArr' => [], 'callArr' => [], 'expArray' => []]);
        }
    }
    /**
     * Fetches and displays Bank Nifty option chain data.
     *
     * This function retrieves expiry dates for Bank Nifty options, selects the upcoming or current
     * expiry date, fetches the option chain data for that date, and prepares the data for display
     * on the frontend.
     *
     *
     */
    public function BankNifty()
    {
        // Retrieve expiry dates from the API
        $expiryApiUrl = 'http://nimblerest.lisuns.com:4531/GetExpiryDates/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=BANKNIFTY';
        $expApiResult = Http::get($expiryApiUrl)->json();
        $expDt = $expApiResult['EXPIRYDATES'] ?? [];

        // Get the current timestamp
        $currentTimestamp = time();

        // Prepare an array to store expiry dates and information about their upcoming status
        $expArray = [];
        $selectedDate = null;

        // Iterate through expiry dates to find the upcoming or current one
        foreach ($expDt as $option) {
            $carbonDate = Carbon::createFromFormat('dMY', $option);
            $timestamp = $carbonDate->timestamp;
            $isUpcomingOrCurrent = $timestamp >= $currentTimestamp;
            $isUpcomingAfterInitial = empty($selectedDate) && $isUpcomingOrCurrent;

            if ($isUpcomingAfterInitial) {
                $selectedDate = $option;
            }

            $expArray[] = [
                'option' => $option,
                'isUpcomingAfterInitial' => $isUpcomingAfterInitial,
            ];
        }

        // Prepare the API endpoint for the selected expiry date
        $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=BANKNIFTY&expiry=' . $selectedDate;

        try {
            // Fetch option chain data using LARAVEl Function And Removed cURL request
            $apiResult = Http::get($apiEndpoint)->json();

            // Prepare arrays to store call and put options data
            $putArr = [];
            $callArr = [];

            // Iterate through the API result to categorize options as call or put
            foreach ($apiResult as $result) {
                $identi = explode('_', $result['INSTRUMENTIDENTIFIER']);
                $value = end($identi);

                if ($identi[3] == 'CE') {
                    $callArr[] = array_merge($result, ['value' => $value]);
                } elseif ($identi[3] == 'PE') {
                    $putArr[] = array_merge($result, ['value' => $value]);
                }
            }

            // Extract option values and map them to the corresponding options
            $putArr = array_map(function ($item) {
                $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                $item['value'] = end($identi);
                return $item;
            }, $putArr);

            $callArr = array_map(function ($item) {
                $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                $item['value'] = end($identi);
                return $item;
            }, $callArr);

            return view('frontend.banknifty', compact('putArr', 'callArr', 'expArray'));
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return view('frontend.banknifty', ['putArr' => [], 'callArr' => [], 'expArray' => []]);
        }
    }

    public function FinNifty()
    {
        $expiryDT = 'http://nimblerest.lisuns.com:4531/GetExpiryDates/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=FINNIFTY';

        $expApiResult = Http::get($expiryDT)->json();

        $expDt = isset($expApiResult['EXPIRYDATES']) ? $expApiResult['EXPIRYDATES'] : [];
        $expArray = [];
        $selectedDate = null;
        $currentTimestamp = time();

        foreach ($expDt as $option) {
            $carbonDate = Carbon::createFromFormat('dMY', $option);
            $timestamp = $carbonDate->timestamp;
            $isUpcomingOrCurrent = $timestamp >= $currentTimestamp;
            $isUpcomingAfterInitial = empty($selectedDate) && $isUpcomingOrCurrent;

            if ($isUpcomingAfterInitial) {
                $selectedDate = $option;
            }

            $expArray[] = [
                'option' => $option,
                'isUpcomingAfterInitial' => $isUpcomingAfterInitial,
            ];
        }

        $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=FINNIFTY&expiry=' . $selectedDate;

        try {
            $apiResult = Http::get($apiEndpoint)->json();

            $putArr = [];
            $callArr = [];

            foreach ($apiResult as $result) {
                $identi = explode('_', $result['INSTRUMENTIDENTIFIER']);
                $value = end($identi);

                if ($identi[3] == 'CE') {
                    $callArr[] = array_merge($result, ['value' => $value]);
                } elseif ($identi[3] == 'PE') {
                    $putArr[] = array_merge($result, ['value' => $value]);
                }
            }

            return view('frontend.finnifty', compact('putArr', 'callArr', 'expArray'));
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return view('frontend.finnifty', ['putArr' => [], 'callArr' => [], 'expArray' => []]);
        }
    }

    public function Midcap()
    {
        $expiryDT = 'http://nimblerest.lisuns.com:4531/GetExpiryDates/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=MIDCPNIFTY';

        $expApiResult = Http::get($expiryDT)->json();

        $expDt = isset($expApiResult['EXPIRYDATES']) ? $expApiResult['EXPIRYDATES'] : [];
        $expArray = [];
        $selectedDate = null;
        $currentTimestamp = time();

        foreach ($expDt as $option) {
            $carbonDate = Carbon::createFromFormat('dMY', $option);
            $timestamp = $carbonDate->timestamp;
            $isUpcomingOrCurrent = $timestamp >= $currentTimestamp;
            $isUpcomingAfterInitial = empty($selectedDate) && $isUpcomingOrCurrent;

            if ($isUpcomingAfterInitial) {
                $selectedDate = $option;
            }

            $expArray[] = [
                'option' => $option,
                'isUpcomingAfterInitial' => $isUpcomingAfterInitial,
            ];
        }

        $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=MIDCPNIFTY&expiry=' . $selectedDate;

        try {
            $apiResult = Http::get($apiEndpoint)->json();

            $putArr = [];
            $callArr = [];

            foreach ($apiResult as $result) {
                $identi = explode('_', $result['INSTRUMENTIDENTIFIER']);
                $value = end($identi);

                if ($identi[3] == 'CE') {
                    $callArr[] = array_merge($result, ['value' => $value]);
                } elseif ($identi[3] == 'PE') {
                    $putArr[] = array_merge($result, ['value' => $value]);
                }
            }

            return view('frontend.midcap', compact('putArr', 'callArr', 'expArray'));
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return view('frontend.midcap', ['putArr' => [], 'callArr' => [], 'expArray' => []]);
        }
    }

    public function getFinNiftywithDt($id)
    {
        $starting = request()->query('starting');
        $ending = request()->query('ending');

        try {
            $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=FINNIFTY&expiry=' . $id;
            $apiResult = Http::get($apiEndpoint)->json();

            $putArr = [];
            $callArr = [];

            foreach ($apiResult as $key => $result) {
                $identi = explode('_', $result['INSTRUMENTIDENTIFIER']);

                if ($identi[3] == 'CE') {
                    array_push($callArr, $result);
                } elseif ($identi[3] == 'PE') {
                    array_push($putArr, $result);
                }
            }

            // Extract the desired value from the INSTRUMENTIDENTIFIER

            if ($starting !== null && $ending !== null) {
                $putArr1 = array_map(
                    function ($item) {
                        $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                        $item['value'] = end($identi);
                        return $item;
                    },
                    array_filter($putArr, function ($item) use ($starting, $ending) {
                        $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                        return end($identi) >= $starting && end($identi) <= $ending;
                    }),
                );

                $callArr1 = array_filter($callArr, function ($item) use ($starting, $ending) {
                    $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                    return end($identi) >= $starting && end($identi) <= $ending;
                });

                return response()->json([
                    'putArr' => array_values($putArr1), // Reset array keys after filtering
                    'callArr' => array_values($callArr1), // Reset array keys after filtering
                ]);
            } else {
                $putArr = array_map(function ($item) {
                    $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                    $item['value'] = end($identi);
                    return $item;
                }, $putArr);

                $callArr = array_map(function ($item) {
                    $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                    $item['value'] = end($identi);
                    return $item;
                }, $callArr);

                return response()->json([
                    'putArr' => $putArr,
                    'callArr' => $callArr,
                ]);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return response()->json(
                [
                    'error' => 'An error occurred while processing the request.',
                ],
                500,
            );
        }
    }
    public function getBankNiftywithDt($id)
    {
        $starting = request()->query('starting');
        $ending = request()->query('ending');

        try {
            $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=BANKNIFTY&expiry=' . $id;
            $apiResult = Http::get($apiEndpoint)->json();

            $putArr = [];
            $callArr = [];

            foreach ($apiResult as $key => $result) {
                $identi = explode('_', $result['INSTRUMENTIDENTIFIER']);

                if ($identi[3] == 'CE') {
                    array_push($callArr, $result);
                } elseif ($identi[3] == 'PE') {
                    array_push($putArr, $result);
                }
            }

            // Extract the desired value from the INSTRUMENTIDENTIFIER

            if ($starting !== null && $ending !== null) {
                $putArr1 = array_map(
                    function ($item) {
                        $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                        $item['value'] = end($identi);
                        return $item;
                    },
                    array_filter($putArr, function ($item) use ($starting, $ending) {
                        $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                        return end($identi) >= $starting && end($identi) <= $ending;
                    }),
                );

                $callArr1 = array_filter($callArr, function ($item) use ($starting, $ending) {
                    $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                    return end($identi) >= $starting && end($identi) <= $ending;
                });

                return response()->json([
                    'putArr' => array_values($putArr1), // Reset array keys after filtering
                    'callArr' => array_values($callArr1), // Reset array keys after filtering
                ]);
            } else {
                $putArr = array_map(function ($item) {
                    $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                    $item['value'] = end($identi);
                    return $item;
                }, $putArr);

                $callArr = array_map(function ($item) {
                    $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                    $item['value'] = end($identi);
                    return $item;
                }, $callArr);

                return response()->json([
                    'putArr' => $putArr,
                    'callArr' => $callArr,
                ]);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return response()->json(
                [
                    'error' => 'An error occurred while processing the request.',
                ],
                500,
            );
        }
    }
    public function getNiftywithDt($id)
    {
        $starting = request()->query('starting');
        $ending = request()->query('ending');

        try {
            $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=NIFTY&expiry=' . $id;
            $apiResult = Http::get($apiEndpoint)->json();

            $putArr = [];
            $callArr = [];

            foreach ($apiResult as $key => $result) {
                $identi = explode('_', $result['INSTRUMENTIDENTIFIER']);

                if ($identi[3] == 'CE') {
                    array_push($callArr, $result);
                } elseif ($identi[3] == 'PE') {
                    array_push($putArr, $result);
                }
            }

            // Extract the desired value from the INSTRUMENTIDENTIFIER

            if ($starting !== null && $ending !== null) {
                $putArr1 = array_map(
                    function ($item) {
                        $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                        $item['value'] = end($identi);
                        return $item;
                    },
                    array_filter($putArr, function ($item) use ($starting, $ending) {
                        $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                        return end($identi) >= $starting && end($identi) <= $ending;
                    }),
                );

                $callArr1 = array_filter($callArr, function ($item) use ($starting, $ending) {
                    $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                    return end($identi) >= $starting && end($identi) <= $ending;
                });

                return response()->json([
                    'putArr' => array_values($putArr1), // Reset array keys after filtering
                    'callArr' => array_values($callArr1), // Reset array keys after filtering
                ]);
            } else {
                $putArr = array_map(function ($item) {
                    $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                    $item['value'] = end($identi);
                    return $item;
                }, $putArr);

                $callArr = array_map(function ($item) {
                    $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                    $item['value'] = end($identi);
                    return $item;
                }, $callArr);

                return response()->json([
                    'putArr' => $putArr,
                    'callArr' => $callArr,
                ]);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return response()->json(
                [
                    'error' => 'An error occurred while processing the request.',
                ],
                500,
            );
        }
    }
    public function getMidcapwithDt($id)
    {
        $starting = request()->query('starting');
        $ending = request()->query('ending');

        try {
            $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=MIDCPNIFTY&expiry=' . $id;
            $apiResult = Http::get($apiEndpoint)->json();

            $putArr = [];
            $callArr = [];

            foreach ($apiResult as $key => $result) {
                $identi = explode('_', $result['INSTRUMENTIDENTIFIER']);

                if ($identi[3] == 'CE') {
                    array_push($callArr, $result);
                } elseif ($identi[3] == 'PE') {
                    array_push($putArr, $result);
                }
            }

            // Extract the desired value from the INSTRUMENTIDENTIFIER

            if ($starting !== null && $ending !== null) {
                $putArr1 = array_map(
                    function ($item) {
                        $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                        $item['value'] = end($identi);
                        return $item;
                    },
                    array_filter($putArr, function ($item) use ($starting, $ending) {
                        $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                        return end($identi) >= $starting && end($identi) <= $ending;
                    }),
                );

                $callArr1 = array_filter($callArr, function ($item) use ($starting, $ending) {
                    $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                    return end($identi) >= $starting && end($identi) <= $ending;
                });

                return response()->json([
                    'putArr' => array_values($putArr1), // Reset array keys after filtering
                    'callArr' => array_values($callArr1), // Reset array keys after filtering
                ]);
            } else {
                $putArr = array_map(function ($item) {
                    $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                    $item['value'] = end($identi);
                    return $item;
                }, $putArr);

                $callArr = array_map(function ($item) {
                    $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
                    $item['value'] = end($identi);
                    return $item;
                }, $callArr);

                return response()->json([
                    'putArr' => $putArr,
                    'callArr' => $callArr,
                ]);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return response()->json(
                [
                    'error' => 'An error occurred while processing the request.',
                ],
                500,
            );
        }
    }
    public function FnoRanking()
    {
        // Replace with your API URL
        $apiUrl = 'http://nimblerest.lisuns.com:4531/GetProducts?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO';

        // Initialize Guzzle client
        $client = new Client();

        try {
            // Make API request
            $response = $client->get($apiUrl);

            // Decode the JSON response
            $data = json_decode($response->getBody(), true);

            // Pass the PRODUCTS array to the view
            return view('frontend.fnoRanking', ['products' => $data['PRODUCTS']]);
        } catch (\Exception $e) {
            return view('frontend.fnoRanking', ['products' => [], 'error' => $e->getMessage()]);
        }
    }
    public function Getdata($type)
    {
        $currentNftData = 'http://nimblerest.lisuns.com:4531/GetLastQuote/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&instrumentIdentifier=' . $type . '-I';
        $currentNftDataresult = Http::get($currentNftData)->json();
        $currentOptionSrike = $currentNftDataresult?$currentNftDataresult['AVERAGETRADEDPRICE']:null;
        
        $expiryApiUrl = 'http://nimblerest.lisuns.com:4531/GetExpiryDates/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=' . $type;
        $expApiResult = Http::get($expiryApiUrl)->json();
        $expDt = $expApiResult['EXPIRYDATES'] ?? [];
        $typeNft = $type;
        $currentTimestamp = time();
        $expArray = [];
        $selectedDate = null;
        foreach ($expDt as $option) {
            $carbonDate = Carbon::createFromFormat('dMY', $option);
            $timestamp = $carbonDate->timestamp;
            $isUpcomingOrCurrent = $timestamp >= $currentTimestamp;
            $isUpcomingAfterInitial = empty($selectedDate) && $isUpcomingOrCurrent;
            if ($isUpcomingAfterInitial) {
                $selectedDate = $option;
            }
            $expArray[] = [
                'option' => $option,
                'isUpcomingAfterInitial' => $isUpcomingAfterInitial,
            ];
        }
        
        $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=' . $type . '&expiry=' . $selectedDate;
        try {
            $apiResult = Http::get($apiEndpoint)->json();
            $putArr = [];
            $callArr = [];
            $finalcallputvalue = [];
            foreach ($apiResult as $result) {
                $identi = explode('_', $result['INSTRUMENTIDENTIFIER']);
                $value = end($identi);
                if ($result['SERVERTIME'] > 0) {
                    if ($identi[3] == 'CE') {
                        $callArr[] = array_merge($result, ['value' => (int)$value,'optionType'=>$identi[3],'optionDate'=>$identi[2]]);
                    } elseif ($identi[3] == 'PE') {
                        $putArr[] = array_merge($result, ['value' => (int)$value,'optionType'=>$identi[3],'optionDate'=>$identi[2]]);
                    }
                }
            }
            $closest = 0;
            foreach ($callArr as $item) {
                if ($closest === null || abs($currentOptionSrike - $closest) > abs($item['value'] - $currentOptionSrike)) {
                    $closest = $item['value'];
                }
            }
            $index = -1;
            // Iterate through the array and search for the value
            foreach ($callArr as $key => $subArray) {
                if (in_array($closest, $subArray)) {
                    $index = $key;
                    break;
                }
            }
            if ($index !== -1) {
                $dataList = [];
                for ($i = $index - 6; $i < $index + 7; $i++) {
                    $dataList[] = [
                        'put' => $putArr[$i],
                        'call' => $callArr[$i],
                        'strike' => $closest,
                    ];
                }
               
                return view('frontend.derivatives', compact('dataList', 'typeNft', 'currentNftDataresult'));
            } else {
                return view('frontend.derivatives', compact('dataList', 'typeNft', 'currentNftDataresult'));
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }
    public function getOpenInterestChartData()
{
    // Prepare data (example data)
    $timeLabels = ['Session 1', 'Session 2', 'Session 3', 'Session 4', 'Session 5'];
    $amountData = [5000000, 15000000, 20000000, 21500000, 24000000];
    $zeroData = [0, 0, 0, 0, 0];
    return response()->json([
        'labels' => $timeLabels,
        'data' => $amountData,
        'zero'=>  $zeroData,
    ]);
}
public function getOpenInterestChartDatatwo()
{
    // Prepare data (example data)
    $timeLabels = ['Session 1', 'Session 2', 'Session 3', 'Session 4', 'Session 5'];
    $amountData = [5000000, 15000000, 20000000, 21500000, 24000000];
    $zeroData = [0, 0, 0, 0, 0];
    return response()->json([
        'labels' => $timeLabels,
        'data' => $amountData,
        'zero'=>  $zeroData,
    ]);
}
public function getcurrentstrike($type)
    {
      $currentNftData = 'http://nimblerest.lisuns.com:4531/GetLastQuote/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&instrumentIdentifier=' . $type . '-I';
      $currentNftDataresult = Http::get($currentNftData)->json();
      return response()->json([$currentNftDataresult]);
    }
    public function getexpdate($type)
    {
        $expiryApiUrl = 'http://nimblerest.lisuns.com:4531/GetExpiryDates/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=' . $type;
        $expApiResult = Http::get($expiryApiUrl)->json();
        $expDt = $expApiResult['EXPIRYDATES'] ?? [];
        $typeNft = $type;
        $currentTimestamp = time();
        $expArray = [];
        $selectedDate = null;
        foreach ($expDt as $option) {
            $carbonDate = Carbon::createFromFormat('dMY', $option);
            $timestamp = $carbonDate->timestamp;
            $isUpcomingOrCurrent = $timestamp >= $currentTimestamp;
            $isUpcomingAfterInitial = empty($selectedDate) && $isUpcomingOrCurrent;
            if ($isUpcomingAfterInitial) {
                $selectedDate = $option;
            }
            $expArray[] = [
                'option' => $option,
                'isUpcomingAfterInitial' => $isUpcomingAfterInitial,
            ];
        }
        return response()->json(['date'=>$selectedDate]);
    }
    public function getoptiondata($type,$currentOptionSrike)
    {
        $expiryApiUrl = 'http://nimblerest.lisuns.com:4531/GetExpiryDates/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=' . $type;
        $expApiResult = Http::get($expiryApiUrl)->json();
        $expDt = $expApiResult['EXPIRYDATES'] ?? [];
        $typeNft = $type;
        $currentTimestamp = time();
        $expArray = [];
        $selectedDate = null;
        foreach ($expDt as $option) {
            $carbonDate = Carbon::createFromFormat('dMY', $option);
            $timestamp = $carbonDate->timestamp;
            $isUpcomingOrCurrent = $timestamp >= $currentTimestamp;
            $isUpcomingAfterInitial = empty($selectedDate) && $isUpcomingOrCurrent;
            if ($isUpcomingAfterInitial) {
                $selectedDate = $option;
            }
            $expArray[] = [
                'option' => $option,
                'isUpcomingAfterInitial' => $isUpcomingAfterInitial,
            ];
        }
        $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=' . $type . '&expiry=' . $selectedDate;
        try {
            $apiResult = Http::get($apiEndpoint)->json();
            $putArr = [];
            $callArr = [];
            $finalcallputvalue = [];
            foreach ($apiResult as $result) {
                $identi = explode('_', $result['INSTRUMENTIDENTIFIER']);
                $value = end($identi);      
                if ($result['SERVERTIME'] > 0) {
                    if ($identi[3] == 'CE') {
                        $callArr[] = array_merge($result, ['value' => (int)$value,'optionType' =>$identi[3],'optiondate' =>$identi[2]]);
                    } elseif ($identi[3] == 'PE') {
                        $putArr[] = array_merge($result, ['value' =>  (int)$value,'optionType' =>$identi[3],'optiondate' =>$identi[2]]);
                    }
                }
            }
            
            $closest = 0;
          foreach ($callArr as $item) {
              if ($closest === null || abs($currentOptionSrike - $closest) > abs($item['value'] - $currentOptionSrike)) {
                  $closest = $item['value'];
              }
          }
          $index = -1;
          // Iterate through the array and search for the value
          foreach ($callArr as $key => $subArray) {
              if (in_array($closest, $subArray)) {
                  $index = $key;
                  break;
              }
          }
          $otchangeCE=0;
          $otchangePE=0;
          if ($index !== -1) {
              $dataList = [];
              for ($i = $index - 6; $i < $index + 7; $i++) {
                $otchangeCE+=$callArr[$i]['OPENINTERESTCHANGE'];
                $otchangePE+=$putArr[$i]['OPENINTERESTCHANGE'];
               
              }
              $dataList[] = [
                'put' => $otchangePE,
                'call' =>$otchangeCE,
                'strike' => $closest,
            ];
          }
        return response()->json(['data'=>$dataList]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        
    }
    public function getChartData($type)
{
    $currentNftData = 'http://nimblerest.lisuns.com:4531/GetLastQuote/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&instrumentIdentifier=' . $type . '-I';
        $currentNftDataresult = Http::get($currentNftData)->json();
        $currentOptionSrike = $currentNftDataresult?$currentNftDataresult['AVERAGETRADEDPRICE']:null;
        
        $expiryApiUrl = 'http://nimblerest.lisuns.com:4531/GetExpiryDates/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=' . $type;
        $expApiResult = Http::get($expiryApiUrl)->json();
        $expDt = $expApiResult['EXPIRYDATES'] ?? [];
        $typeNft = $type;
        $currentTimestamp = time();
        $expArray = [];
        $selectedDate = null;
        foreach ($expDt as $option) {
            $carbonDate = Carbon::createFromFormat('dMY', $option);
            $timestamp = $carbonDate->timestamp;
            $isUpcomingOrCurrent = $timestamp >= $currentTimestamp;
            $isUpcomingAfterInitial = empty($selectedDate) && $isUpcomingOrCurrent;
            if ($isUpcomingAfterInitial) {
                $selectedDate = $option;
            }
            $expArray[] = [
                'option' => $option,
                'isUpcomingAfterInitial' => $isUpcomingAfterInitial,
            ];
        }
        
        $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=' . $type . '&expiry=' . $selectedDate;
        try {
            $apiResult = Http::get($apiEndpoint)->json();
            $putArr = [];
            $callArr = [];
            $finalcallputvalue = [];
            foreach ($apiResult as $result) {
                $identi = explode('_', $result['INSTRUMENTIDENTIFIER']);
                $value = end($identi);
                if ($result['SERVERTIME'] > 0) {
                    if ($identi[3] == 'CE') {
                        $callArr[] = array_merge($result, ['value' => (int)$value,'optionType'=>$identi[3],'optionDate'=>$identi[2]]);
                    } elseif ($identi[3] == 'PE') {
                        $putArr[] = array_merge($result, ['value' => (int)$value,'optionType'=>$identi[3],'optionDate'=>$identi[2]]);
                    }
                }
            }
            $closest = 0;
            foreach ($callArr as $item) {
                if ($closest === null || abs($currentOptionSrike - $closest) > abs($item['value'] - $currentOptionSrike)) {
                    $closest = $item['value'];
                }
            }
            $index = -1;
            // Iterate through the array and search for the value
            foreach ($callArr as $key => $subArray) {
                if (in_array($closest, $subArray)) {
                    $index = $key;
                    break;
                }
            }
            if ($index !== -1) {
                $dataList = [];
                for ($i = $index - 6; $i < $index + 7; $i++) {
                    $dataList[] = [
                        'put' => $putArr[$i]['OPENINTERESTCHANGE'],
                        'call' => $callArr[$i]['OPENINTERESTCHANGE'],
                        'putoi' => $putArr[$i]['OPENINTEREST'],
                        'calloi' => $callArr[$i]['OPENINTEREST'],
                        'label' => $callArr[$i]['value'],
                    ];
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    return response()->json($dataList);
}
}
