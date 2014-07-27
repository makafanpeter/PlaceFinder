<?php

class LocSearchController extends BaseController {

    protected function main() {
        $locModel = new LocSearchModel();
        $this->view($locModel->index(), 'main');
    }

    protected function getService() {
        $locModel = new LocSearchModel();
        $modelData = json_decode($locModel->getService());

        $viewData = Array();
        $data = Array();
        if (isset($modelData->status) && $modelData->status === 'OK') {
            $data = $modelData->results;
        }

        foreach ($data as $key => $data) {
            array_push($viewData, Array('name' => $data->name, 'address' => $data->vicinity, 'lat' => $data->geometry->location->lat, 'lng' => $data->geometry->location->lng));
        }

        $this->view($viewData, 'find');
    }

    protected function getLocation() {
        $locModel = new LocSearchModel();
        $modelData = json_decode($locModel->getLocation());

        $viewData = Array();
        $data = Array();
        if (isset($modelData->status) && $modelData->status === 'OK') {
            $data = $modelData->results;
            if (isset($data[0]->formatted_address)) {
                $viewData = Array('addr' => $data[0]->formatted_address);
            }
        }

        $this->view($viewData, 'find');
    }

    protected function getDirection() {
        $locModel = new LocSearchModel();
        $modelData = json_decode($locModel->getDirection());

        $viewData = Array();
        $data = Array();
        if (isset($modelData->status) && $modelData->status === 'OK') {
            $data = $modelData->routes[0];
            if (isset($data->legs[0])) {
                $routeData = $data->legs[0];
                $viewData['totDist'] = $routeData->distance->text;
                $viewData['totTime'] = $routeData->duration->text;
                foreach ($routeData->steps as $key => $obj) {
                    $viewData['steps'][$key]['dist'] = $obj->distance->text;
                    $viewData['steps'][$key]['time'] = $obj->duration->text;
                    $viewData['steps'][$key]['text'] = $obj->html_instructions;
                    $viewData['steps'][$key]['mode'] = $obj->travel_mode;
                }
            }
        }

        $this->view($viewData, 'find');
    }

}
