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
        "id_supplier" => "required",
        "total" => "required",
    );
    $cek = validate($data, $validasi, $custom);
    return $cek;
}
/**
 * Ambil detail m pembelian
 */
$app->get("/pembelian/view", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("*")
        ->from("m_pembelian_det")
        ->where("m_pembelian_id", "=", $params["m_pembelian_id"]);
    $models = $db->findAll();
    return successResponse($response, $models);
});
/**
 * Ambil semua m pembelian
 */
$app->get("/pembelian/index", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("m_pembelian.*,m_supplier.nama as nama_supplier, m_user.nama as nama_user")
        ->from("m_pembelian")
        ->innerJoin("m_supplier", "m_pembelian.id_supplier = m_supplier.id")
        ->innerJoin("m_user", "m_pembelian.created_by=m_user.id")
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
 * Save m pembelian
 */
$app->post("/pembelian/save", function ($request, $response) {
    $data = $request->getParams();
    $db = $this->db;
    $validasi = validasi($data["data"]);
    if ($validasi === true) {
        try {
            if (isset($data["data"]["id"])) {
                $model = $db->update("m_pembelian", $data["data"], ["id" => $data["data"]["id"]]);
                $db->delete("m_pembelian_det", ["m_pembelian_id" => $data["data"]["id"]]);
            } else {
                $model = $db->insert("m_pembelian", $data["data"]);
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
                    $detail["m_pembelian_id"] = $model->id;
                    // print_r($detail);die;
                    $tes = $db->insert("m_pembelian_det", $detail);
                    // print_r($tes);die;
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
 * Hapus m pembelian
 */
$app->post("/pembelian/hapus", function ($request, $response) {
    $data = $request->getParams();
    $db = $this->db;
    try {
        $model = $db->delete("m_pembelian", ["id" => $data["id"]]);
        $modelDetail = $db->delete("m_pembelian_det", ["m_pembelian_id" => $data["id"]]);
        return successResponse($response, $model);
    } catch (Exception $e) {
        return unprocessResponse($response, ["terjadi masalah pada server"]);
    }
    return unprocessResponse($response, $validasi);
});

// GET SUPPLIER
$app->get("/pembelian/supplier", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("*")
        ->from("m_supplier");
    $models = $db->findAll();
    return successResponse($response, $models);
});
// END GET SUPPLIER

// GET BARANG
$app->get("/pembelian/barang", function ($request, $response) {
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
$app->get("/pembelian/kode", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("max(id) as kode")
        ->from("m_pembelian");
    $models = $db->find();
    $id = $_SESSION['user']['id'];
    return successResponse($response, ["list" => $models, "id" => $id]);
});
// END GET KODE
