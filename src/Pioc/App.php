<?php
namespace Pioc;

use Pioc\Base\App as BaseApp;

class App extends BaseApp {

    public function actionSynchronize() {
        $response = array();
        $result = array();

        $result['time'] = time();

        $device = $this->device;
        $deviceId = $device->id;

        if ($deviceId > 0) {
            $deviceToken = $device->token;
        } else {
            $agentId = $this->agent->id;
            $deviceToken = $device->generateToken($agentId);
        }




        $response['res'] = $result;
        $response['device'] = $deviceToken;
        $this->response->json = $response;
    }

}
