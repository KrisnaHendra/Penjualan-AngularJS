<?php

/**
 * Ambil Data kasmasuk
 */
$app->get("/laporankeluar/index", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("keluar, tanggal, sum(total) as total")
        ->from("kaskeluar")
        ->groupBy("keluar")
        ->orderBy("keluar", "ASC");
    // ->groupBy("masuk");

    /**
     * Filter
     */
    if (isset($params["filter"])) {
        $filter = (array) json_decode($params["filter"]);
        foreach ($filter as $key => $val) {
            $db->where($key, "LIKE", $val);
        }
    }

    if (isset($params['start_date'])) {
        $db->where("tanggal", ">=", $params['start_date']);
    }
    if (isset($params['end_date'])) {
        $db->where("tanggal", "<=", $params['end_date']);
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
