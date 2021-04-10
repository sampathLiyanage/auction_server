<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Configuration;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function show($userId) {
        return Configuration::where('user_id', '=', $userId)->first();
    }

    public function update(Request $request, $userId) {
        $configValue = $request->all();
        if ($configuration = $this->show($userId)) {
            $configuration->configuration = json_encode($configValue);
            $configuration->save();
        } else {
            $configuration = Configuration::create(['user_id'=>$userId, 'configuration'=>json_encode($configValue)]);
        }
        return response()->json($configuration, 200);
    }
}
