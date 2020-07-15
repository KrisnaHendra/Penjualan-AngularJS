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
                     "nama"  => "required",
                     "alamat"  => "required",
                     "telepon"  => "required",
            );
    $cek = validate($data, $validasi, $custom);
    return $cek;
}
/**
 * Ambil semua m supplier
 */
$app->get("/supplier/index", function ($request, $response) {
    $params = $request->getParams();
    $db     = $this->db;
                $db->select("*")
        ->from("m_supplier");
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
    $models    = $db->findAll();
    $totalItem = $db->count();
    return successResponse($response, ["list" => $models, "totalItems" => $totalItem]);
});
/**
 * Save m supplier
 */
$app->post("/supplier/save", function ($request, $response) {
    $data     = $request->getParams();
    $db       = $this->db;
    $validasi = validasi($data);
    if ($validasi === true) {
        try {
            if (isset($data["id"])) {
                $model = $db->update("m_supplier", $data, ["id" => $data["id"]]);
            } else {
                $model = $db->insert("m_supplier", $data);
            }
            return successResponse($response, $model);
        } catch (Exception $e) {
            return unprocessResponse($response, ["terjadi masalah pada server"]);
        }
    }
    return unprocessResponse($response, $validasi);
});
/**
 * Hapus m supplier
 */
$app->post("/supplier/hapus", function ($request, $response) {
    $data     = $request->getParams();
    $db       = $this->db;
    try {
        $model = $db->delete("m_supplier", ["id" => $data["id"]]);
                return successResponse($response, $model);
    } catch (Exception $e) {
        return unprocessResponse($response, ["terjadi masalah pada server"]);
    }
    return unprocessResponse($response, $validasi);
});
