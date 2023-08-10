<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
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
        $expiryDT = 'http://nimblerest.lisuns.com:4531/GetExpiryDates/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=NIFTY';
        $curlExp = curl_init();
        curl_setopt_array($curlExp, [
            CURLOPT_URL => $expiryDT,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $expApiResult = json_decode(curl_exec($curlExp), true);
        curl_close($curlExp);

        $expDt = isset($expApiResult['EXPIRYDATES']) ? $expApiResult['EXPIRYDATES'] : [];
        $expAray = [];
        $selectedDate = null;

        foreach ($expDt as $option) {
            $carbonDate = Carbon::createFromFormat('dMY', $option);
            $timestamp = $carbonDate->timestamp;
            $currentTimestamp = time();
            $isUpcomingOrCurrent = $timestamp >= $currentTimestamp;
            $isUpcomingAfterInitial = empty($selectedDate) && $isUpcomingOrCurrent;

            if ($isUpcomingAfterInitial) {
                $selectedDate = $option;
            }

            $expAray[] = [
                'option' => $option,
                'isUpcomingAfterInitial' => $isUpcomingAfterInitial,
            ];
        }

        $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=NIFTY&expiry=' . $selectedDate;

        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $apiEndpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $apiResult = json_decode(curl_exec($curl), true);
            curl_close($curl);

            $putArr = [];
            $callArr = [];

            foreach ($apiResult as $result) {
                $identi = explode('_', $result['INSTRUMENTIDENTIFIER']);

                if ($identi[3] == 'CE') {
                    $callArr[] = array_merge($result, ['value' => end($identi)]);
                } elseif ($identi[3] == 'PE') {
                    $putArr[] = array_merge($result, ['value' => end($identi)]);
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

            return view('frontend.nifty', compact('putArr', 'callArr', 'expAray'));
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return view('frontend.nifty', ['data' => null]);
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
        $expApiResult = json_decode(file_get_contents($expiryApiUrl), true);
        $expDt = $expApiResult['EXPIRYDATES'] ?? [];

        // Get the current timestamp
        $currentTimestamp = time();

        // Prepare an array to store expiry dates and information about their upcoming status
        $expAray = [];
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

            $expAray[] = [
                'option' => $option,
                'isUpcomingAfterInitial' => $isUpcomingAfterInitial,
            ];
        }

        // Prepare the API endpoint for the selected expiry date
        $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=BANKNIFTY&expiry=' . $selectedDate;

        try {
            // Fetch option chain data using cURL
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $apiEndpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $apiResult = json_decode(curl_exec($curl), true);
            curl_close($curl);

            // Prepare arrays to store call and put options data
            $putArr = [];
            $callArr = [];

            // Iterate through the API result to categorize options as call or put
            foreach ($apiResult as $result) {
                $identi = explode('_', $result['INSTRUMENTIDENTIFIER']);

                if ($identi[3] == 'CE') {
                    $callArr[] = array_merge($result, ['value' => end($identi)]);
                } elseif ($identi[3] == 'PE') {
                    $putArr[] = array_merge($result, ['value' => end($identi)]);
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

            // Return the view with prepared data for frontend display
            return view('frontend.banknifty', compact('putArr', 'callArr', 'expAray'));
        } catch (\Exception $e) {
            // Log errors and return view with data set to null in case of exceptions
            error_log($e->getMessage());
            return view('frontend.banknifty', ['data' => null]);
        }
    }

    public function FinNifty()
    {
        $expiryDT = 'http://nimblerest.lisuns.com:4531/GetExpiryDates/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=FINNIFTY';
        $curlExp = curl_init();
        curl_setopt_array($curlExp, [
            CURLOPT_URL => $expiryDT,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $expApiResult = json_decode(curl_exec($curlExp), true);
        curl_close($curlExp);

        $expDt = isset($expApiResult['EXPIRYDATES']) ? $expApiResult['EXPIRYDATES'] : [];
        $expAray = [];
        $selectedDate = null;

        foreach ($expDt as $option) {
            $carbonDate = Carbon::createFromFormat('dMY', $option);
            $timestamp = $carbonDate->timestamp;
            $currentTimestamp = time();
            $isUpcomingOrCurrent = $timestamp >= $currentTimestamp;
            $isUpcomingAfterInitial = empty($selectedDate) && $isUpcomingOrCurrent;

            if ($isUpcomingAfterInitial) {
                $selectedDate = $option;
            }

            $expAray[] = [
                'option' => $option,
                'isUpcomingAfterInitial' => $isUpcomingAfterInitial,
            ];
        }

        $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=FINNIFTY&expiry=' . $selectedDate;

        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $apiEndpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $apiResult = json_decode(curl_exec($curl), true);
            curl_close($curl);

            $putArr = [];
            $callArr = [];

            foreach ($apiResult as $result) {
                $identi = explode('_', $result['INSTRUMENTIDENTIFIER']);

                if ($identi[3] == 'CE') {
                    $callArr[] = array_merge($result, ['value' => end($identi)]);
                } elseif ($identi[3] == 'PE') {
                    $putArr[] = array_merge($result, ['value' => end($identi)]);
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

            return view('frontend.finnifty', compact('putArr', 'callArr', 'expAray'));
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return view('frontend.finnifty', ['data' => null]);
        }
    }

    public function getFinNiftywithDt($id)
    {
        $starting = request()->query('starting');
        $ending = request()->query('ending');
        try {
            $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=FINNIFTY&expiry=' . $id;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $apiResult = curl_exec($curl);
            curl_close($curl);
            $apiResult = json_decode($apiResult, true);
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
                    $item['dd'] = 2;

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
        }
    }

    public function getBankNiftywithDt($id)
    {
        $starting = request()->query('starting');
        $ending = request()->query('ending');
        try {
            $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=BANKNIFTY&expiry=' . $id;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $apiResult = curl_exec($curl);
            curl_close($curl);
            $apiResult = json_decode($apiResult, true);
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
                    $item['dd'] = 2;

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
        }
    }

    public function getNiftywithDt($id)
    {
        $starting = request()->query('starting');
        $ending = request()->query('ending');
        try {
            $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=NIFTY&expiry=' . $id;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $apiResult = curl_exec($curl);
            curl_close($curl);
            $apiResult = json_decode($apiResult, true);
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
                    $item['dd'] = 2;

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
            // Handle any exceptions here (e.g., API error, connection error)
            return view('frontend.fnoRanking', ['products' => []]);
        }
    }
}
