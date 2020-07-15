<?php

/**
 * Ambil Data kasmasuk
 */
$app->get("/penjualanpertahun/index", function ($request, $response) {
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


$app->get("/penjualanpertahun/sumTotal", function ($request, $response) {
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

$app->get("/penjualanpertahun/dataArray", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $year = $params['year'];
    
    // Mendapatkan list bulan
    for ($i = 1; $i <= 12; $i++) {
        $dates[]=$year.'-'.sprintf("%02d",$i);
    }
    // Mendapatkan list hari di bulan ini - END

    $db->select("
        date_format(m_penjualan.tanggal,'%Y-%m') as bulan,
        m_kategori.nama,
        sum(m_penjualan_det.harga_satuan*m_penjualan_det.jumlah) as total
    ")
        ->from("m_penjualan")
        ->innerJoin("m_penjualan_det", "m_penjualan.id = m_penjualan_det.m_penjualan_id")
        ->innerJoin("m_barang", "m_penjualan_det.id_barang=m_barang.id")
        ->innerJoin("m_kategori","m_barang.id_kategori=m_kategori.id")
        ->where("YEAR(m_penjualan.tanggal)","=",$year);
        if (isset($params['kategori'])) {
          $db->andWhere("m_kategori.nama","LIKE","%".$params['kategori']."%");
        }
        $db->groupBy("m_kategori.nama,bulan");
    $getDataPenjualan = $db->findAll();
    // pd($getDataPenjualan);

    $getSum = $db->findAll("SELECT k.nama,
      sum(d.harga_satuan*d.jumlah) as totalSum
      FROM m_penjualan p
      JOIN m_penjualan_det d on p.id=d.m_penjualan_id
      JOIN m_barang b on d.id_barang=b.id
      JOIN m_kategori k on b.id_kategori=k.id
      WHERE YEAR(p.tanggal)='".$year."'
      GROUP BY k.nama");
    $totPerBulan = $db->findAll("SELECT date_format(p.tanggal,'%Y-%m') as bulan,
    sum(d.harga_satuan*d.jumlah) as total
    FROM m_penjualan p
    JOIN m_penjualan_det d on p.id=d.m_penjualan_id
    JOIN m_barang b on d.id_barang=b.id
    JOIN m_kategori k on b.id_kategori=k.id
    WHERE YEAR(p.tanggal)='".$year."' GROUP BY bulan");
    $listTotPerBulan = [];
    foreach($totPerBulan as $key=>$value){
      // Susunan Array Total Per Bulan (Tahun-Bulan => Total)
      $listTotPerBulan[$value->bulan]=$value->total;
    }
    foreach($dates as $key=>$value){
      // Memberi Nilai 0 Jika Tidak ada data
        if (!isset($listTotPerBulan[$value])) {
            $listTotPerBulan[$value] = 0;
        }
        ksort($listTotPerBulan);
    }

    $listSum = [];
    foreach($getSum as $key => $value){
      // Susunan Array Untuk Satu Barang per Tahun (Nama => Total)
      $listSum[$value->nama]=$value->totalSum;
    }

    $listResult = [];
    foreach ($getDataPenjualan as $key => $value) {
        /* Susunan Array (Nama Akun => Tanggal => Nominal Total) */
        $listResult[$value->nama][$value->bulan] = $value->total;
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
    // pd($listResult);
    // Memberi default value jika pada tanggal tertentu tidak ada transaksi - END
    return successResponse($response, ["listResult" => $listResult,"listSum" =>$listSum,"SumPerBulan"=>$listTotPerBulan]);
});

function pd($params = [])
{
    echo "<pre>";
    print_r($params);
    die;
}
