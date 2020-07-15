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
        "nama" => "required",
        "id_satuan" => "required",
        "id_kategori" => "required",
        "stok" => "required",
    );
    $cek = validate($data, $validasi, $custom);
    return $cek;
}
/**
 * Ambil semua m barang
 */
$app->get("/barang/index", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("m_barang.*,m_satuan.nama as nama_satuan, m_kategori.nama as nama_kategori")
        ->from("m_barang")
        ->innerJoin("m_satuan", "m_barang.id_satuan = m_satuan.id")
        ->innerJoin("m_kategori", "m_barang.id_kategori = m_kategori.id")
        ->orderBy("m_barang.id_kategori asc");
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
 * Save m barang
 */
$app->post("/barang/save", function ($request, $response) {
    $data = $request->getParams();
    $db = $this->db;
    $validasi = validasi($data);
    if ($validasi === true) {
        try {
            if (isset($data["id"])) {
                $model = $db->update("m_barang", $data, ["id" => $data["id"]]);
            } else {
                $model = $db->insert("m_barang", $data);
            }
            return successResponse($response, $model);
        } catch (Exception $e) {
            return unprocessResponse($response, ["terjadi masalah pada server"]);
        }
    }
    return unprocessResponse($response, $validasi);
});
/**
 * Hapus m barang
 */
$app->post("/barang/hapus", function ($request, $response) {
    $data = $request->getParams();
    $db = $this->db;
    try {
        $model = $db->delete("m_barang", ["id" => $data["id"]]);
        return successResponse($response, $model);
    } catch (Exception $e) {
        return unprocessResponse($response, ["terjadi masalah pada server"]);
    }
    return unprocessResponse($response, $validasi);
});
// GET SATUAN
$app->get("/barang/satuan", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("*")
        ->from("m_satuan");
    // ->innerJoin("kaskeluar", "kasmasuk.id=kaskeluar.id");
    $models = $db->findAll();
    return successResponse($response, $models);
});
// END GET SATUAN

// GET KATEGORI
$app->get("/barang/kategori", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("*")
        ->from("m_kategori");
    // ->innerJoin("kaskeluar", "kasmasuk.id=kaskeluar.id");
    $models = $db->findAll();
    return successResponse($response, $models);
});
// END GET KATEGORI
