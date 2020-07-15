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
        "alamat" => "required",
        "telepon" => "required",
    );
    $cek = validate($data, $validasi, $custom);
    return $cek;
}
/**
 * Ambil semua m customer
 */
$app->get("/customer/index", function ($request, $response) {
    $params = $request->getParams();
    $db = $this->db;
    $db->select("*")
        ->from("m_customer");
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
 * Save m customer
 */
$app->post("/customer/save", function ($request, $response) {
    $data = $request->getParams();
    $db = $this->db;
    $validasi = validasi($data);
    if ($validasi === true) {
        try {
            if (isset($data["id"])) {
                $model = $db->update("m_customer", $data, ["id" => $data["id"]]);
            } else {
                $model = $db->insert("m_customer", $data);
            }
            return successResponse($response, $model);
        } catch (Exception $e) {
            return unprocessResponse($response, ["terjadi masalah pada server"]);
        }
    }
    return unprocessResponse($response, $validasi);
});
/**
 * Hapus m customer
 */
$app->post("/customer/hapus", function ($request, $response) {
    $data = $request->getParams();
    $db = $this->db;
    try {
        $model = $db->delete("m_customer", ["id" => $data["id"]]);
        return successResponse($response, $model);
    } catch (Exception $e) {
        return unprocessResponse($response, ["terjadi masalah pada server"]);
    }
    return unprocessResponse($response, $validasi);
});

// $app->post('/customer/editCustomer', function ($request, $response) {
//     $params = $request->getParams();
//     $db = $this->db;
//     $tes['nama'] = $params['id_customer']['nama'];
//     $tes['alamat'] = $params['id_customer']['alamat'];
//     $tes['telepon'] = $params['id_customer']['telepon'];
//     // print_r($tes);die;

//     $mod = $db->update("m_customer", $tes, array("id" => $params['id_customer']['id']));

//     return successResponse($response, ['data' => $mod]);
// });
$app->post('/customer/editCustomer', function ($request, $response) {
    $data = $request->getParams();

    $db = $this->db;

    $validasi = validasi($data);

    if ($validasi === true) {
        try {
            // $data['m_penjualan'] = $data['m_penjualan']['id'];
            $model = $db->update("m_customer", $data, array('id' => $data['id_customer']['id']));
            return successResponse($response, $model);
        } catch (Exception $e) {
            return unprocessResponse($response, ['Data Gagal Di Simpan']);
        }
    }
    return unprocessResponse($response, $validasi);
});
