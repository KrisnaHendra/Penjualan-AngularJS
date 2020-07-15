app.controller("laporanmasukCtrl", function ($scope, Data, $rootScope) {
    /**
     * Inialisasi
     */
    var tableStateRef;
    $scope.formtittle = "";
    $scope.displayed = [];
    $scope.form = {};
    $scope.filter_tanggal = {};
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.loading = false;
    $scope.cari = {
        periode: {
            endDate: moment().add(1, 'M'),
            startDate: moment().subtract(1, 'M')
        }
    };
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
            limit: limit,
            start_date: moment($scope.cari.periode.startDate).format("YYYY-MM-DD"),
            end_date: moment($scope.cari.periode.endDate).format("YYYY-MM-DD")
        };
        if (tableState.sort.predicate) {
            param["sort"] = tableState.sort.predicate;
            param["order"] = tableState.sort.reverse;
        }
        if (tableState.search.predicateObject) {
            param["filter"] = tableState.search.predicateObject;
        }
        // if (param["filter"] == undefined) {
        //     param["filter"] = {}
        // }
        if ($scope.filterTanggal != undefined) {
            start_date: moment($scope.cari.periode.startDate).format("YYYY-MM-DD");
            end_date: moment($scope.cari.periode.endDate).format("YYYY-MM-DD");
        }

        Data.get("laporanmasuk/index", param).then(function (response) {
            $scope.displayed = response.data.list;
            tableState.pagination.numberOfPages = Math.ceil(
                response.data.totalItems / limit
            );
        });
        $scope.isLoading = false;
    };
    $scope.getDetail = function (id) {
        Data.get("kasmasuk/view?kasmasuk_id=" + id).then(function (response) {
            $scope.listDetail = response.data;
        });
    };

    $scope.filterTanggal = function () {
        // console.log($scope.cari.periode);
        $scope.callServer(tableStateRef);
    }

    $scope.getTotal = function () {
        var total = 0;
        for (var i = 0; i < $scope.displayed.length; i++) {
            var row = $scope.displayed[i];
            total += parseFloat(row.total);
        }
        return total;
    }

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
        // $scope.form.no_transaksi = "01/KM/0004";
        Data.get("laporanmasuk/kode").then(function (result) {
            // console.log(result);
            $scope.form.no_transaksi = "01/KM/00" + result.data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtittle = "Edit Data : " + form.no_transaksi;
        $scope.form = form;
        $scope.getDetail(form.id);
        // $scope.form.no_transaksi = "AAAA";
        $scope.form.tanggal = new Date();
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtittle = "Lihat Data : " + form.no_transaksi;
        $scope.form = form;
        $scope.form.tanggal = new Date(form.tanggal);
        $scope.getDetail(form.id);
    };
    $scope.save = function (form) {
        $scope.loading = true;
        var form = {
            data: form,
            detail: $scope.listDetail
        }
        Data.post("laporanmasuk/save", form).then(function (result) {
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
            Data.post("laporanmasuk/hapus", row).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };

});
