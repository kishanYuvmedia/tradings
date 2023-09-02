<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController;
use DB;

class PublicApiController extends ResponseController
{
    public function users(Request $request)
    {
        $users = User::all();

        if ($users) {
            return $this->sendResponse($users, 'success');
        } else {
            return $this->sendError('No records have found');
        }

    }
    public function getintraday($type)
    {
      // Call the API and retrieve data
      $currentNftData = 'http://nimblerest.lisuns.com:4531/GetLastQuote/?accessKey=988dcf72-de6b-4637-9af7-fddbe9bfa7cd&exchange=NFO&instrumentIdentifier=' . $type . '-I';
      $currentNftDataresult = Http::get($currentNftData)->json();
      return response()->json([$currentNftDataresult]);
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
                      $callArr[] = array_merge($result, ['value' => $value]);
                  } elseif ($identi[3] == 'PE') {
                      $putArr[] = array_merge($result, ['value' => $value]);
                  }
              }
          }
          $putArr = array_map(function ($item) {
              $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
              $item['value'] = (int) end($identi);
              $item['optionType'] = $identi[3];
              $item['optionDate'] = $identi[2];
              return $item;
          }, $putArr);
          $callArr = array_map(function ($item) {
              $identi = explode('_', $item['INSTRUMENTIDENTIFIER']);
              $item['value'] = (int) end($identi);
              $item['optionType'] = $identi[3];
              $item['optionDate'] = $identi[2];
              return $item;
          }, $callArr);
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
          }
          return response()->json($dataList);
        }catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }
}
