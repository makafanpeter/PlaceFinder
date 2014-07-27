<?php

class LocSearchModel extends BaseModel {

    public function index() {
        return;
    }

    public function getService() {
        $data = explode('?', $_SERVER['REQUEST_URI'])[1];
        $addrLoc = strpos($data, 'address=');
        error_log("addrLoc $addrLoc");
        if ($addrLoc) {
            $addr = substr($data, $addrLoc + 8);
            $latlng = $this->getGeoCodeFromAddress($addr);
            $data = str_replace('address=' . $addr, 'location=' . $latlng, $data);
        }
        $data .= "&key=" . BaseModel::GOOGLE_API_KEY;
        return $this->callAPI('GET', BaseModel::GOOGLE_BASE_API . BaseModel::GOOGLE_PLACE_API . 'nearbysearch/json', $data);
    }

    public function getLocation() {
        $data = explode('?', $_SERVER['REQUEST_URI'])[1];
        $data .= "&key=" . BaseModel::GOOGLE_API_KEY;
        return $this->callAPI('GET', BaseModel::GOOGLE_BASE_API . BaseModel::GOOGLE_GEOCODE_API . 'json', $data);
    }

    public function getDirection() {
        $data = explode('?', $_SERVER['REQUEST_URI'])[1];
        $data .= "&key=" . BaseModel::GOOGLE_API_KEY;
        return $this->callAPI('GET', BaseModel::GOOGLE_BASE_API . BaseModel::GOOGLE_DIRECTIONS_API . 'json', $data);
    }

    protected function getGeoCodeFromAddress($addr) {
        $qryStr = "address=" . $addr . "&key=" . BaseModel::GOOGLE_API_KEY;
        $data = json_decode($this->callAPI('GET', BaseModel::GOOGLE_BASE_API . BaseModel::GOOGLE_GEOCODE_API . 'json', $qryStr));

        if (isset($data->status) && ($data->status === 'OK')) {
            $results = $data->results[0]->geometry->location;
            return $results->lat . ',' . $results->lng;
        }

        return false;
    }

}
