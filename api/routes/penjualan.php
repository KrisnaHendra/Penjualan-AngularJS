<?php
/**
 * Validasi
 * @param  array $data
 * @param  array $custom
 * @return array
 */
function validasi($data, $custom = array())
{
    $validasi = array(
        "tanggal" => "required",
        "no_invoice" => "required",
        // "id_customer" => "required",
        "total" => "required",
    );
    $cek = validate($data, $validasi, $custom);
    return $cek;
}
/**
 * Ambil detail m penjualan
 */
$app->get("/penjualan/view", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("*")
        ->from("m_penjualan_det")
        ->where("m_penjualan_id", "=", $params["m_penjualan_id"]);
    $models = $db->findAll();
    return successResponse($response, $models);
});
/**
 * Ambil semua m penjualan
 */
$app->get("/penjualan/index", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("m_penjualan.*,m_customer.nama as nama_customer, m_user.nama as nama_user")
        ->from("m_penjualan")
        ->innerJoin("m_customer", "m_penjualan.id_customer=m_customer.id")
        ->innerJoin("m_user", "m_penjualan.created_by=m_user.id")
        ->orderBy("no_invoice asc");
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
/**
 * Save m penjualan
 */
$app->post("/penjualan/save", function ($request, $response) {
    $data = $request->getParams();
    $db = $this->db;
    $validasi = validasi($data["data"]);
    if ($validasi === true) {
        try {
            if (isset($data["data"]["id"])) {
                $model = $db->update("m_penjualan", $data["data"], ["id" => $data["data"]["id"]]);
                $db->delete("m_penjualan_det", ["m_penjualan_id" => $data["data"]["id"]]);
            } else {
                $model = $db->insert("m_penjualan", $data["data"]);
            }
            /**
             * Simpan detail
             */
            if (isset($data["detail"]) && !empty($data["detail"])) {
                foreach ($data["detail"] as $key => $val) {
                    $detail["id"] = isset($val["id"]) ? $val["id"] : '';
                    $detail["id_barang"] = isset($val["id_barang"]) ? $val["id_barang"] : '';
                    $detail["harga_satuan"] = isset($val["harga_satuan"]) ? $val["harga_satuan"] : '';
                    $detail["jumlah"] = isset($val["jumlah"]) ? $val["jumlah"] : '';
                    $detail["m_penjualan_id"] = $model->id;
                    $db->insert("m_penjualan_det", $detail);
                }
            }
            return successResponse($response, $model);
        } catch (Exception $e) {
            return unprocessResponse($response, ["terjadi masalah pada server"]);
        }
    }
    return unprocessResponse($response, $validasi);
});
/**
 * Hapus m penjualan
 */
$app->post("/penjualan/hapus", function ($request, $response) {
    $data = $request->getParams();
    $db = $this->db;
    try {
        $model = $db->delete("m_penjualan", ["id" => $data["id"]]);
        $modelDetail = $db->delete("m_penjualan_det", ["m_penjualan_id" => $data["id"]]);
        return successResponse($response, $model);
    } catch (Exception $e) {
        return unprocessResponse($response, ["terjadi masalah pada server"]);
    }
    return unprocessResponse($response, $validasi);
});

// GET CUSTOMER
$app->get("/penjualan/customer", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("*")
        ->from("m_customer");
    $models = $db->findAll();
    return successResponse($response, $models);
});
// END GET CUSTOMER
// GET BARANG
$app->get("/penjualan/barang", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("*")
        ->from("m_barang")
        ->orderBy("nama asc");
    $models = $db->findAll();
    return successResponse($response, $models);
});
// END GET BARANG
// GET KODE
$app->get("/penjualan/kode", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("max(id) as kode")
        ->from("m_penjualan");
    $models = $db->find();
    $id = $_SESSION['user']['id'];
    return successResponse($response, ["list" => $models, "id" => $id]);
});
// END GET KODE
