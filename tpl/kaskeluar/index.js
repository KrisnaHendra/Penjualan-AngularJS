app.controller("kaskeluarCtrl", function ($scope, Data, $rootScope) {
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
        Data.get("kaskeluar/index", param).then(function (response) {
            $scope.displayed = response.data.list;
            tableState.pagination.numberOfPages = Math.ceil(
                response.data.totalItems / limit
            );
        });
        $scope.isLoading = false;
    };
    $scope.getDetail = function (id) {
        Data.get("kaskeluar/view?kaskeluar_id=" + id).then(function (response) {
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

    $scope.getTotal = function () {
        var total = 0;
        for (var i = 0; i < $scope.listDetail.length; i++) {
            var v = $scope.listDetail[i];
            total += parseInt(v.nominal);
        }

        $scope.form.total = total;
    }

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtittle = "Form Tambah Data";
        $scope.form = {};
        $scope.form.tanggal = new Date();
        Data.get("kaskeluar/kode").then(function (result) {
            // console.log(result);
            $scope.form.no_transaksi = "01/KKL/00" + result.data.kode;
        });
        Data.get("kaskeluar/akun").then(function (response) {
            $scope.akuns = response.data;
        })
        Data.get("kaskeluar/kontak").then(function (response) {
            $scope.kontak = response.data;
        })
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtittle = "Edit Data : " + form.no_transaksi;
        $scope.form = form;
        $scope.getDetail(form.id);
        $scope.form.tanggal = new Date(form.tanggal);
        Data.get("kaskeluar/akun").then(function (response) {
            $scope.akuns = response.data;
        })
        Data.get("kaskeluar/kontak").then(function (response) {
            $scope.kontak = response.data;
        })
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtittle = "Lihat Data : " + form.no_transaksi;
        $scope.form = form;
        $scope.form.tanggal = new Date(form.tanggal);
        $scope.getDetail(form.id);
        Data.get("kaskeluar/akun").then(function (response) {
            $scope.akuns = response.data;
        })
        Data.get("kaskeluar/kontak").then(function (response) {
            $scope.kontak = response.data;
        })
    };
    $scope.save = function (form) {
        $scope.loading = true;
        var form = {
            data: form,
            detail: $scope.listDetail
        }
        Data.post("kaskeluar/save", form).then(function (result) {
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
            Data.post("kaskeluar/hapus", row).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
});