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
        "masuk" => "required",
    );
    $cek = validate($data, $validasi, $custom);
    return $cek;
}
/**
 * Ambil detail kasmasuk
 */
$app->get("/kasmasuk/view", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("*")
        ->from("kasmasuk_det")
        ->where("kasmasuk_id", "=", $params["kasmasuk_id"]);
    $models = $db->findAll();
    return successResponse($response, $models);
});
$app->get("/kasmasuk/kode", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("max(id) as kode")
        ->from("kasmasuk");
    $models = $db->find();
    $id = $_SESSION['user']['id'];
    return successResponse($response, ["list" => $models, "id" => $id]);
});
$app->get("/kasmasuk/akun", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("*")
        ->from("akun");
    $models = $db->findAll();
    return successResponse($response, $models);
});
$app->get("/kasmasuk/kontak", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("*")
        ->from("kontak")
        ->orderBy("nama asc");
    $models = $db->findAll();
    return successResponse($response, $models);
});

/**
 * Ambil semua kasmasuk
 */
$app->get("/kasmasuk/index", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("kasmasuk.*,m_user.nama as nama_user")
        ->from("kasmasuk")
        ->innerJoin("m_user", "m_user.id=kasmasuk.created_by");

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
    $id = $_SESSION['user']['id'];
    return successResponse($response, ["list" => $models, "totalItems" => $totalItem, "kode" => $id]);
});
/**
 * Save kasmasuk
 */
$app->post("/kasmasuk/save", function ($request, $response) {
    $data = $request->getParams();
    $db = $this->db;
    $validasi = validasi($data["data"]);
    if ($validasi === true) {
        try {
            if (isset($data["data"]["id"])) {
                $model = $db->update("kasmasuk", $data["data"], ["id" => $data["data"]["id"]]);
                $db->delete("kasmasuk_det", ["kasmasuk_id" => $data["data"]["id"]]);
            } else {
                $model = $db->insert("kasmasuk", $data["data"]);
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
                    $detail["kasmasuk_id"] = $model->id;
                    $db->insert("kasmasuk_det", $detail);
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
 * Hapus kasmasuk
 */
$app->post("/kasmasuk/hapus", function ($request, $response) {
    $data = $request->getParams();
    $db = $this->db;
    try {
        $model = $db->delete("kasmasuk", ["id" => $data["id"]]);
        $modelDetail = $db->delete("kasmasuk_det", ["kasmasuk_id" => $data["id"]]);
        return successResponse($response, $model);
    } catch (Exception $e) {
        return unprocessResponse($response, ["terjadi masalah pada server"]);
    }
    return unprocessResponse($response, $validasi);
});
