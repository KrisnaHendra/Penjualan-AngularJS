<?php

/**
 * Ambil Data kasmasuk
 */
$app->get("/rekap/index", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("kasmasuk.*")
        ->from("kasmasuk");
    // ->groupBy("masuk")
    // ->innerJoin("kaskeluar", "kasmasuk.id=kaskeluar.id");

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
    if (isset($params['month'])) {
        $db->where("MONTH(kasmasuk.tanggal)", "=", $params['month']);
        $db->where("YEAR(kasmasuk.tanggal)", "=", $params['year']);
    }
    // if (isset($params['year'])) {
    //     $db->where("YEAR(kasmasuk.tanggal)","=",$params['year']);
    // }
    $models = $db->findAll();
    $bulan = $params['month'];
    $tahun = $params['year'];
    $tgl = date("d", strtotime('-1 second', strtotime('+1 month', strtotime($bulan . '/01/' . $tahun . ' 00:00:00'))));

    $totalItem = $db->count();

    return successResponse($response, ["list" => $models, "totalItems" => $totalItem, "listTanggal" => $tgl]);
});

$app->get("/rekap/laporan", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("*")
        ->from("kasmasuk");
    // ->innerJoin("kaskeluar", "kasmasuk.id=kaskeluar.id");
    $models = $db->findAll();
    return successResponse($response, $models);
});

$app->get("/rekap/contohSusunanArray", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    // $get_bulan = $params['month'];
    $year = $params['year'];
    $month = $params['month'];
    $bulan_ini = date($year . "-" . $month . "-d");

    // Mendapatkan list hari di bulan ini
    for ($i = 1; $i <= date("t", strtotime($bulan_ini)); $i++) {
        // add the date to the dates array
        $dates[] = date($year) . "-" . date($month) . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
    }
    // Mendapatkan list hari di bulan ini - END

    $db->select("
      kasmasuk.masuk,
      kasmasuk.tanggal,
      SUM(kasmasuk.total) as total,kaskeluar.total as tot_keluar
      ")
        ->from("kasmasuk")
        ->innerJoin("kaskeluar", "kasmasuk.id = kaskeluar.id")
        ->where("MONTH(kasmasuk.tanggal)", "=", date("m", strtotime($bulan_ini)))
        ->andWhere("YEAR(kasmasuk.tanggal)", "=", date("Y", strtotime($bulan_ini)))
        ->groupBy("masuk, kasmasuk.tanggal");
    $getKasMasuk = $db->findAll();
    // $getKasMasuk = $db->findAll("select masuk,tanggal,total from kasmasuk GROUP BY masuk UNION select keluar, tanggal, total as tot_keluar from kaskeluar GROUP BY keluar");
    // pd($getKasMasuk);

    $listResult = [];
    foreach ($getKasMasuk as $key => $value) {
        /*
        Susunan Array
        Nama Akun => Tanggal => Nominal Total
         */
        $listResult[$value->masuk][$value->tanggal] = $value->total - $value->tot_keluar;
    }
    // Memberi default value jika pada tanggal tertentu tidak ada transaksi
    foreach ($listResult as $key => $value) {
        foreach ($dates as $value2) {
            if (!isset($listResult[$key][$value2])) {
                $listResult[$key][$value2] = 0;
            }
        }
        ksort($listResult[$key]);
    }

    // Memberi default value jika pada tanggal tertentu tidak ada transaksi - END

    return successResponse($response, $listResult);
});

function pd($params = [])
{
    echo "<pre>";
    // asort($params);
    print_r($params);
    die;
}
