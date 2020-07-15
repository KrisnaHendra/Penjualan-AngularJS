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
        "no_transaksi" => "required",
        "tanggal" => "required",
        "total" => "required",
        "keluar" => "required",
    );
    $cek = validate($data, $validasi, $custom);
    return $cek;
}
/**
 * Ambil detail kaskeluar
 */
$app->get("/kaskeluar/view", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("*")
        ->from("kaskeluar_det")
        ->where("kaskeluar_id", "=", $params["kaskeluar_id"]);
    $models = $db->findAll();
    return successResponse($response, $models);
});
$app->get("/kaskeluar/kode", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("max(id) as kode")
        ->from("kaskeluar");
    $models = $db->find();
    return successResponse($response, $models);
});
$app->get("/kaskeluar/akun", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("*")
        ->from("akun");
    $models = $db->findAll();
    return successResponse($response, $models);
});
$app->get("/kaskeluar/kontak", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("*")
        ->from("kontak")
        ->orderBy("nama asc");
    $models = $db->findAll();
    return successResponse($response, $models);
});
/**
 * Ambil semua kaskeluar
 */
$app->get("/kaskeluar/index", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("kaskeluar.*,m_user.nama as nama_user")
        ->from("kaskeluar")
        ->innerJoin("m_user", "m_user.id=kaskeluar.created_by");
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
 * Save kaskeluar
 */
$app->post("/kaskeluar/save", function ($request, $response) {
    $data = $request->getParams();
    $db = $this->db;
    $validasi = validasi($data["data"]);
    if ($validasi === true) {
        try {
            if (isset($data["data"]["id"])) {
                $model = $db->update("kaskeluar", $data["data"], ["id" => $data["data"]["id"]]);
                $db->delete("kaskeluar_det", ["kaskeluar_id" => $data["data"]["id"]]);
            } else {
                $model = $db->insert("kaskeluar", $data["data"]);
            }
            /**
             * Simpan detail
             */
            if (isset($data["detail"]) && !empty($data["detail"])) {
                foreach ($data["detail"] as $key => $val) {
                    $detail["id"] = isset($val["id"]) ? $val["id"] : '';
                    $detail["nama_akun"] = isset($val["nama_akun"]) ? $val["nama_akun"] : '';
                    $detail["keterangan"] = isset($val["keterangan"]) ? $val["keterangan"] : '';
                    $detail["nominal"] = isset($val["nominal"]) ? $val["nominal"] : '';
                    $detail["kaskeluar_id"] = $model->id;
                    $db->insert("kaskeluar_det", $detail);
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
 * Hapus kaskeluar
 */
$app->post("/kaskeluar/hapus", function ($request, $response) {
    $data = $request->getParams();
    $db = $this->db;
    try {
        $model = $db->delete("kaskeluar", ["id" => $data["id"]]);
        $modelDetail = $db->delete("kaskeluar_det", ["kaskeluar_id" => $data["id"]]);
        return successResponse($response, $model);
    } catch (Exception $e) {
        return unprocessResponse($response, ["terjadi masalah pada server"]);
    }
    return unprocessResponse($response, $validasi);
});
