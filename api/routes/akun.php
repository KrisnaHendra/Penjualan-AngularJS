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
                     "kode"  => "required",
            );
    $cek = validate($data, $validasi, $custom);
    return $cek;
}
/**
 * Ambil semua akun
 */
$app->get("/akun/index", function ($request, $response) {
    $params = $request->getParams();
    $db     = $this->db;
                $db->select("*")
        ->from("akun");
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
 * Save akun
 */
$app->post("/akun/save", function ($request, $response) {
    $data     = $request->getParams();
    $db       = $this->db;
    $validasi = validasi($data);
    if ($validasi === true) {
        try {
            if (isset($data["id"])) {
                $model = $db->update("akun", $data, ["id" => $data["id"]]);
            } else {
                $model = $db->insert("akun", $data);
            }
            return successResponse($response, $model);
        } catch (Exception $e) {
            return unprocessResponse($response, ["terjadi masalah pada server"]);
        }
    }
    return unprocessResponse($response, $validasi);
});
/**
 * Hapus akun
 */
$app->post("/akun/hapus", function ($request, $response) {
    $data     = $request->getParams();
    $db       = $this->db;
    try {
        $model = $db->delete("akun", ["id" => $data["id"]]);
                return successResponse($response, $model);
    } catch (Exception $e) {
        return unprocessResponse($response, ["terjadi masalah pada server"]);
    }
    return unprocessResponse($response, $validasi);
});
