<?php

/**
 * Ambil Data kasmasuk
 */
$app->get("/rekappenjualan/index", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("m_penjualan.*")
        ->from("m_penjualan");

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
    // if (isset($params['month'])) {
    //     $db->where("MONTH(kasmasuk.tanggal)", "=", $params['month']);
    //     $db->where("YEAR(kasmasuk.tanggal)", "=", $params['year']);
    // }
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

// $app->get("/rekappenjualan/total", function ($request, $response) {
//     $params = $request->getParams();
//     $db = $this->db;
//     $db->select("b.nama, p.tanggal, sum(jumlah) as sum")
//         ->from("m_penjualan p")
//         ->innerJoin("m_penjualan_det d", "p.id=d.m_penjualan_id")
//         ->innerJoin("m_barang b", "d.id_barang=b.id")
//         ->where("MONTH(p.tanggal)", "=", "05")
//         ->groupBy("b.nama");
//     $models = $db->findAll();
//     return successResponse($response, $models);
// });
$app->get("/rekappenjualan/sumTotal", function ($request, $response) {
    $params = $request->getParams();
    $month = $params['month'];
    $year = $params['year'];

    $db = $this->db;
    $db->select("sum(jumlah) as totalSum")
        ->from("m_penjualan p")
        ->innerJoin("m_penjualan_det d", "p.id=d.m_penjualan_id")
        ->innerJoin("m_barang b", "d.id_barang=b.id")
        ->where("MONTH(p.tanggal)", "=", $month)
        ->andWhere("YEAR(p.tanggal)", "=", $year);
    $models = $db->find();
    return successResponse($response, $models);
});

$app->get("/rekappenjualan/dataArray", function ($request, $response) {
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
        m_penjualan.*,
        m_penjualan_det.m_penjualan_id,
        m_barang.*,
        sum(m_penjualan_det.jumlah) as jumlahAll
    ")
        ->from("m_penjualan")
        ->innerJoin("m_penjualan_det", "m_penjualan.id=m_penjualan_det.m_penjualan_id")
        ->innerJoin("m_barang", "m_penjualan_det.id_barang=m_barang.id")
        ->where("MONTH(m_penjualan.tanggal)", "=", date("m", strtotime($bulan_ini)))
        ->where("YEAR(m_penjualan.tanggal)", "=", date("Y", strtotime($bulan_ini)))
        ->groupBy("m_barang.nama, m_penjualan.tanggal");

    $getDataPembelian = $db->findAll();
    $getSum = $db->findAll("
    select b.id as id_barang,b.nama as nama_barang, p.tanggal, sum(jumlah) as sum from m_penjualan p join m_penjualan_det d on p.id=d.m_penjualan_id join m_barang b on d.id_barang=b.id where month(p.tanggal) ='" . $params['month'] . "'group by b.nama
    ");

    $listSum = [];
    foreach ($getSum as $key => $value) {
        $listSum[$value->nama_barang] = $value->sum;
    }

    $listResult = [];
    foreach ($getDataPembelian as $key => $value) {
        /*
        Susunan Array
        Nama Akun => Tanggal => Nominal Total
         */
        $listResult[$value->nama][$value->tanggal] = $value->jumlahAll;
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
    return successResponse($response, ["listResult" => $listResult, "listSum" => $listSum]);
});

function pd($params = [])
{
    echo "<pre>";
    print_r($params);
    die;
}
