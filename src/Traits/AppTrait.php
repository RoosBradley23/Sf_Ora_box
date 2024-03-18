<?php

namespace App\Traits;

trait AppTrait
{
    public function sortDataArray($request, $data){
        $sort = $request->get('sort');
        $direction = $request->get('direction');
        if($sort != null && $direction != null){
            $keys = array_column($data, $sort);
            array_multisort($keys, $direction == 'desc' ? SORT_DESC : SORT_ASC, $data);
        }
        return $data;
    }

    public function getAccessToken(){
        return $this->getUser()->getAccessToken();
    }
}