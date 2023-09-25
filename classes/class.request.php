<?php

class Request extends Handler {
    function __construct() {
        parent::__construct();
    }

    function __destruct() {

    }

    public function process($data) {

        $clientRequest = $data["REQUEST_URI"];
        $clientRequestArray = explode("/", ltrim($clientRequest, "/"));
        $requestMethod = isset($data["REQUEST_METHOD"]) ? $data["REQUEST_METHOD"] : "GET";

        $resource = isset($clientRequestArray[0]) ? $clientRequestArray[0] : -1;

        $this->log->info('request received --'.$resource.'--');

        $service = null;

        $response["rc"] = -1;
        $response["message"] = "Invalid Request";

        switch ($resource) {
            case "applicants" : 
                $service = new Applicants($clientRequestArray);
                break;
            default: 
                $this->log->info("unknown resource request...");
        }

        if($service) {
            $response = $service->$requestMethod($clientRequestArray);
        }

        echo json_encode($response);
    }

}

?>