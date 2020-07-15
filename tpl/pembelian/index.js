app.controller("mpembelianCtrl", function ($scope, Data, $rootScope) {
    /**
     * Inialisasi
     */
    var tableStateRef;
    $scope.formtittle = "";
    $scope.displayed = [];
    $scope.form = {};
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.loading = false;
    /**
     * End inialisasi
     */
    $scope.callServer = function callServer(tableState) {
        tableStateRef = tableState;
        $scope.isLoading = true;
        var offset = tableState.pagination.start || 0;
        var limit = tableState.pagination.number || 10;
        var param = {
            offset: offset,
            limit: limit
        };
        if (tableState.sort.predicate) {
            param["sort"] = tableState.sort.predicate;
            param["order"] = tableState.sort.reverse;
        }
        if (tableState.search.predicateObject) {
            param["filter"] = tableState.search.predicateObject;
        }
        Data.get("pembelian/index", param).then(function (response) {
            $scope.displayed = response.data.list;
            tableState.pagination.numberOfPages = Math.ceil(
                response.data.totalItems / limit
            );
        });
        $scope.isLoading = false;
    };
    $scope.getDetail = function (id) {
        Data.get("pembelian/view?m_pembelian_id=" + id).then(function (response) {
            $scope.listDetail = response.data;
        });
    };
    $scope.listDetail = [{}];
    $scope.addDetail = function (val) {
        var comArr = eval(val);
        var newDet = {};
        val.push(newDet);
    };
    $scope.removeDetail = function (val, paramindex) {
        var comArr = eval(val);
        if (comArr.length > 1) {
            val.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };
    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtittle = "Form Tambah Data";
        $scope.form = {};
        $scope.form.tanggal = new Date();
        Data.get("pembelian/supplier").then(function (response) {
            $scope.supplier = response.data;
        })
        Data.get("pembelian/barang").then(function (response) {
            $scope.barang = response.data;
        })
        Data.get("pembelian/kode").then(function (result) {
            $scope.form.no_invoice = "INV/0" + result.data.id + "/PBL/00" + result.data.list.kode;
        })
    };

    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtittle = "Edit Data : " + form.tanggal;
        $scope.form = form;
        $scope.getDetail(form.id);
        $scope.form.tanggal = new Date(form.tanggal);
        Data.get("pembelian/supplier").then(function (response) {
            $scope.supplier = response.data;
        })
        Data.get("pembelian/barang").then(function (response) {
            $scope.barang = response.data;
        })
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtittle = "Lihat Data : " + form.no_invoice;
        $scope.form = form;
        $scope.form.tanggal = new Date(form.tanggal);
        $scope.getDetail(form.id);
        Data.get("pembelian/supplier").then(function (response) {
            $scope.supplier = response.data;
        })
        Data.get("pembelian/barang").then(function (response) {
            $scope.barang = response.data;
        })
    };
    $scope.save = function (form) {
        $scope.loading = true;
        var form = {
            data: form,
            detail: $scope.listDetail
        }
        // console.log(form);
        Data.post("pembelian/save", form).then(function (result) {
            if (result.status_code == 200) {
                $rootScope.alert("Berhasil", "Data berhasil disimpan", "success");
                $scope.cancel();
            } else {
                $rootScope.alert("Terjadi Kesalahan", setErrorMessage(result.errors), "error");
            }
            $scope.loading = false;
        });
    };
    $scope.cancel = function () {
        $scope.is_edit = false;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.callServer(tableStateRef);
    };
    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan Menghapus item ini ?")) {
            row.is_deleted = 0;
            Data.post("pembelian/hapus", row).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.getTotal = function () {
        var total = 0;
        for (var i = 0; i < $scope.listDetail.length; i++) {
            var v = $scope.listDetail[i];
            total += parseInt(v.harga_satuan * v.jumlah);
        }
        //
        $scope.form.total = total;
    }
    // $scope.subTotal() = function () {
    //     var total = 0;
    //     $scope.testotal = total;
    // }
});