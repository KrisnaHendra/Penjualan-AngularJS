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
            );
    $cek = validate($data, $validasi, $custom);
    return $cek;
}
/**
 * Ambil semua m kategori
 */
$app->get("/kategori/index", function ($request, $response) {
    $params = $request->getParams();
    $db     = $this->db;
                $db->select("*")
        ->from("m_kategori");
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
 * Save m kategori
 */
$app->post("/kategori/save", function ($request, $response) {
    $data     = $request->getParams();
    $db       = $this->db;
    $validasi = validasi($data);
    if ($validasi === true) {
        try {
            if (isset($data["id"])) {
                $model = $db->update("m_kategori", $data, ["id" => $data["id"]]);
            } else {
                $model = $db->insert("m_kategori", $data);
            }
            return successResponse($response, $model);
        } catch (Exception $e) {
            return unprocessResponse($response, ["terjadi masalah pada server"]);
        }
    }
    return unprocessResponse($response, $validasi);
});
/**
 * Hapus m kategori
 */
$app->post("/kategori/hapus", function ($request, $response) {
    $data     = $request->getParams();
    $db       = $this->db;
    try {
        $model = $db->delete("m_kategori", ["id" => $data["id"]]);
                return successResponse($response, $model);
    } catch (Exception $e) {
        return unprocessResponse($response, ["terjadi masalah pada server"]);
    }
    return unprocessResponse($response, $validasi);
});
