<?php

/**
 * Ambil Data kasmasuk
 */
$app->get("/rankpemasukan/index", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("masuk,sum(total) as jumlah")
        ->from("kasmasuk")
        ->groupBy("masuk")
        ->orderBy("jumlah desc");
    /**
     * Filter
     */
    if (isset($params["filter"])) {
        $filter = (array) json_decode($params["filter"]);
        foreach ($filter as $key => $val) {
            $db->where($key, "LIKE", $val);
        }
    }
    /**
     * Set limit dan offset
     */
    if (isset($params["limit"]) && !empty($params["limit"])) {
        $db->limit($params["limit"]);
    }
    if (isset($params["offset"]) && !empty($params["offset"])) {
        $db->offset($params["offset"]);
    }
    $models = $db->findAll();
    $totalItem = $db->count();
    return successResponse($response, ["list" => $models, "totalItems" => $totalItem]);
});
$app->get("/rankpemasukan/rank", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("masuk,SUM(total) as jumlah")
        ->from("kasmasuk")
        ->groupBy("masuk")
        ->orderBy("jumlah desc");
    $models = $db->findAll();
    return successResponse($response, $models);
});
