<?php

/**
 * Ambil Data kasmasuk
 */
$app->get("/laporanbeli/index", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("m_pembelian.*,m_pembelian.id as id_beli,m_supplier.nama as nama_supplier,m_barang.nama as nama_barang, m_pembelian_det.*")
        ->from("m_pembelian")
        ->innerJoin("m_supplier", "m_supplier.id = m_pembelian.id_supplier")
        ->innerJoin("m_pembelian_det", "m_pembelian_det.m_pembelian_id=m_pembelian.id")
        ->innerJoin("m_barang", "m_barang.id=m_pembelian_det.id_barang")
        ->orderBy("m_pembelian.no_invoice");
    // ->groupBy("m_pembelian.no_invoice");

    /**
     * Filter
     */
    if (isset($params["filter"])) {
        $filter = (array) json_decode($params["filter"]);
        foreach ($filter as $key => $val) {
            $db->where($key, "LIKE", $val);
        }
    }

    if (isset($params['month'])) {
        $db->where("MONTH(m_pembelian.tanggal)", "=", $params['month']);
    }
    if (isset($params['year'])) {
        $db->where("YEAR(m_pembelian.tanggal)", "=", $params['year']);
    }

    // print_r($params["start_date"]);die;
    /**
     * Set limit dan offset
     */

    $models = $db->findAll();
    $totalItem = $db->count();

    $listResult = [];
    foreach ($models as $key => $value) {
        /*
        Susunan Array
        Nama Akun => Tanggal => Nominal Total
         */
        $listResult[$value->no_invoice][$value->tanggal][$value->nama_supplier][$value->nama_barang][$value->harga_satuan] = $value->jumlah;
    }
    // pd($listResult);
    return successResponse($response, ["list" => $models, "totalItems" => $totalItem, "listResult" => $listResult]);
});

function pd($params = [])
{
    echo "<pre>";
    // asort($params);
    print_r($params);
    die;
}

// $app->get("/laporanbeli/print", function ($request, $response) {
//     $params = $request->getParams();
//     $db = $this->db;

//     $params['startDate'] = $params['tanggal'] . "-01";
//     $params['endDate'] = date("Y-m-t", strtotime($params['tanggal']));

//     $db->select("m_pembelian.*,m_pembelian.id as id_beli,m_supplier.nama as nama_supplier,m_barang.nama as nama_barang, m_pembelian_det.*")
//         ->from("m_pembelian")
//         ->innerJoin("m_supplier", "m_supplier.id = m_pembelian.id_supplier")
//         ->innerJoin("m_pembelian_det", "m_pembelian_det.m_pembelian_id=m_pembelian.id")
//         ->innerJoin("m_barang", "m_barang.id=m_pembelian_det.id_barang")
//         ->orderBy("m_pembelian.no_invoice")
//         ->where("MONTH(m_pembelian.tanggal)", "=", $params['month'])
//         ->andWhere("YEAR(m_pembelian.tanggal)", "=", $params['year']);
//     $data = $db->findAll();

//     $view = twigView();
//     $content = $view->fetch("print/laporanpembelian.html", [
//         'data' => $data,
//         'listResult' => $listResult,
//         // 'css' => modulUrl() . '/assets/css/style.css',
//     ]);
//     echo $content;
//     echo '<script type="text/javascript">window.print();setTimeout(function () { window.close(); }, 500);</script>';
//     return successResponse($response, ['data' => $data, 'listResult' => $listResult]);

// });

$app->get("/laporanbeli/laporan", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;

    // $params['startDate'] = $params['tanggal'] . "-01";
    // $params['endDate'] = date("Y-m-t", strtotime($params['tanggal']));

    $db->select("m_pembelian.*,m_pembelian.id as id_beli,m_supplier.nama as nama_supplier,m_barang.nama as nama_barang, m_pembelian_det.*")
        ->from("m_pembelian")
        ->innerJoin("m_supplier", "m_supplier.id = m_pembelian.id_supplier")
        ->innerJoin("m_pembelian_det", "m_pembelian_det.m_pembelian_id=m_pembelian.id")
        ->innerJoin("m_barang", "m_barang.id=m_pembelian_det.id_barang")
        ->orderBy("m_pembelian.no_invoice")
        ->where("MONTH(m_pembelian.tanggal)", "=", $params['month'])
        ->andWhere("YEAR(m_pembelian.tanggal)", "=", $params['year']);
    $data = $db->findAll();

    $total = $db->findAll("SELECT DATE_FORMAT(p.tanggal,'%M/%Y') as periode, SUM(d.jumlah*d.harga_satuan) as total
    FROM m_pembelian p join m_pembelian_det d on p.id=d.m_pembelian_id
    WHERE MONTH(p.tanggal) = '" . $params['month'] . "'
    AND YEAR(p.tanggal) = '" . $params['year'] . "'"
    );
    // pd($total);

    $bulan = $params['bulan'];
    $month = $params['month'];
    $year = $params['year'];
    $content = $this->view->fetch("print/laporanpembelian.html", [
        'data' => $data,
        'total' => $total,
        'bulan' => $bulan,
        'month' => $month,
        'year' => $year,
    ]);
    echo $content;
    echo '<script type="text/javascript">window.print();setTimeout(function () { window.close(); }, 500);</script>';
    exit;
    return successResponse($response, $listResult);

});
