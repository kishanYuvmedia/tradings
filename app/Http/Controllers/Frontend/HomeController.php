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
        curl_setopt($curlExp, CURLOPT_URL, $expiryDT);
        curl_setopt($curlExp, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlExp, CURLOPT_SSL_VERIFYPEER, false);
        $expApiResult = curl_exec($curlExp);
        curl_close($curlExp);
        $expApiResult = json_decode($expApiResult, true);

        //  old Code
        // $expDt = $expApiResult['EXPIRYDATES'];

        //  New Code
        $expDt = isset($expApiResult['EXPIRYDATES']) ? $expApiResult['EXPIRYDATES'] : [];

        $initialDateSet = false; // Variable to track if initial date is set
        $expAray = [];

        if (isset($expDt) && is_array($expDt) && count($expDt) > 0) {
            foreach ($expDt as $index => $option) {
                $dateString = $option;
                $carbonDate = Carbon::createFromFormat('dMY', $dateString);
                $timestamp = $carbonDate->timestamp;

                // Get the current timestamp
                $currentTimestamp = Carbon::now()->timestamp;

                // Compare the timestamps to determine if the date is upcoming or current
                $isUpcomingOrCurrent = $timestamp >= $currentTimestamp;

                // Check if the current option is the upcoming date after 25th July 2023
                $isUpcomingAfterInitial = !$initialDateSet && $isUpcomingOrCurrent;

                // Set initial date selection only once
                if ($isUpcomingAfterInitial) {
                    $initialDateSet = true;
                }
                $expAray[] = [
                    'option' => $option,
                    'isUpcomingAfterInitial' => $isUpcomingAfterInitial,
                ];
            }
        }

        $selectedDate = null;

        foreach ($expAray as $item) {
            if ($item['isUpcomingAfterInitial'] == 1) {
                $selectedDate = $item['option'];
                break;
            }
        }

        $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=NIFTY&expiry=' . $selectedDate;
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $apiEndpoint); // Set the URL
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return the response instead of printing it
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (not recommended in production)

            // Execute the cURL request
            $apiResult = curl_exec($curl);
            curl_close($curl);

            // Convert the JSON response to an associative array
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

    public function BankNifty()
    {
        $expiryDT = 'http://nimblerest.lisuns.com:4531/GetExpiryDates/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=BANKNIFTY';
        $curlExp = curl_init();
        curl_setopt($curlExp, CURLOPT_URL, $expiryDT);
        curl_setopt($curlExp, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlExp, CURLOPT_SSL_VERIFYPEER, false);
        $expApiResult = curl_exec($curlExp);
        curl_close($curlExp);
        $expApiResult = json_decode($expApiResult, true);

        //  old Code
        // $expDt = $expApiResult['EXPIRYDATES'];

        //  New Code
        $expDt = isset($expApiResult['EXPIRYDATES']) ? $expApiResult['EXPIRYDATES'] : [];

        $initialDateSet = false; // Variable to track if initial date is set
        $expAray = [];

        if (isset($expDt) && is_array($expDt) && count($expDt) > 0) {
            foreach ($expDt as $index => $option) {
                $dateString = $option;
                $carbonDate = Carbon::createFromFormat('dMY', $dateString);
                $timestamp = $carbonDate->timestamp;

                // Get the current timestamp
                $currentTimestamp = Carbon::now()->timestamp;

                // Compare the timestamps to determine if the date is upcoming or current
                $isUpcomingOrCurrent = $timestamp >= $currentTimestamp;

                // Check if the current option is the upcoming date after 25th July 2023
                $isUpcomingAfterInitial = !$initialDateSet && $isUpcomingOrCurrent;

                // Set initial date selection only once
                if ($isUpcomingAfterInitial) {
                    $initialDateSet = true;
                }
                $expAray[] = [
                    'option' => $option,
                    'isUpcomingAfterInitial' => $isUpcomingAfterInitial,
                ];
            }
        }

        $selectedDate = null;

        foreach ($expAray as $item) {
            if ($item['isUpcomingAfterInitial'] == 1) {
                $selectedDate = $item['option'];
                break;
            }
        }

        $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=BANKNIFTY&expiry=' . $selectedDate;
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $apiEndpoint); // Set the URL
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return the response instead of printing it
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (not recommended in production)

            // Execute the cURL request
            $apiResult = curl_exec($curl);
            curl_close($curl);

            // Convert the JSON response to an associative array
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

            return view('frontend.banknifty', compact('putArr', 'callArr', 'expAray'));
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return view('frontend.banknifty', ['data' => null]);
        }
    }

    public function FinNifty()
    {
        $expiryDT = 'http://nimblerest.lisuns.com:4531/GetExpiryDates/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=FINNIFTY';
        $curlExp = curl_init();
        curl_setopt($curlExp, CURLOPT_URL, $expiryDT);
        curl_setopt($curlExp, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlExp, CURLOPT_SSL_VERIFYPEER, false);
        $expApiResult = curl_exec($curlExp);
        curl_close($curlExp);
        $expApiResult = json_decode($expApiResult, true);
        //  old Code
        // $expDt = $expApiResult['EXPIRYDATES'];

        //  New Code
        $expDt = isset($expApiResult['EXPIRYDATES']) ? $expApiResult['EXPIRYDATES'] : [];

        $initialDateSet = false; // Variable to track if initial date is set
        $expAray = [];

        if (isset($expDt) && is_array($expDt) && count($expDt) > 0) {
            foreach ($expDt as $index => $option) {
                $dateString = $option;
                $carbonDate = Carbon::createFromFormat('dMY', $dateString);
                $timestamp = $carbonDate->timestamp;

                // Get the current timestamp
                $currentTimestamp = Carbon::now()->timestamp;

                // Compare the timestamps to determine if the date is upcoming or current
                $isUpcomingOrCurrent = $timestamp >= $currentTimestamp;

                // Check if the current option is the upcoming date after 25th July 2023
                $isUpcomingAfterInitial = !$initialDateSet && $isUpcomingOrCurrent;

                // Set initial date selection only once
                if ($isUpcomingAfterInitial) {
                    $initialDateSet = true;
                }
                $expAray[] = [
                    'option' => $option,
                    'isUpcomingAfterInitial' => $isUpcomingAfterInitial,
                ];
            }
        }

        $selectedDate = null;

        foreach ($expAray as $item) {
            if ($item['isUpcomingAfterInitial'] == 1) {
                $selectedDate = $item['option'];
                break;
            }
        }

        $apiEndpoint = 'http://nimblerest.lisuns.com:4531/GetLastQuoteOptionChain/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&product=FINNIFTY&expiry=' . $selectedDate;
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $apiEndpoint); // Set the URL
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return the response instead of printing it
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (not recommended in production)

            // Execute the cURL request
            $apiResult = curl_exec($curl);
            curl_close($curl);

            // Convert the JSON response to an associative array
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
