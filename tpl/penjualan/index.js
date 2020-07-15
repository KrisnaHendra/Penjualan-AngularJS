app.controller("mpenjualanCtrl", function ($scope, Data, $rootScope, $uibModal) {
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
        Data.get("penjualan/index", param).then(function (response) {
            $scope.displayed = response.data.list;
            tableState.pagination.numberOfPages = Math.ceil(
                response.data.totalItems / limit
            );
        });
        $scope.isLoading = false;
    };
    $scope.getDetail = function (id) {
        Data.get("penjualan/view?m_penjualan_id=" + id).then(function (response) {
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
        Data.get("penjualan/customer").then(function (response) {
            // console.log(response);
            $scope.customer = response.data;
        })
        Data.get("penjualan/barang").then(function (response) {
            $scope.barang = response.data;
        });
        Data.get("penjualan/kode").then(function (result) {
            // console.log(result);
            $scope.form.no_invoice = "INV/0" + result.data.id + "/PJL/00" + result.data.list.kode;
        })
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtittle = "Edit Data : " + form.no_invoice;
        $scope.form = form;
        $scope.form.tanggal = new Date(form.tanggal);
        $scope.getDetail(form.id);
        Data.get("penjualan/customer").then(function (response) {
            console.log(response);
            $scope.customer = response.data;
        })
        Data.get("penjualan/barang").then(function (response) {
            $scope.barang = response.data;
        });
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtittle = "Lihat Data : " + form.no_invoice;
        $scope.form = form;
        $scope.form.tanggal = new Date(form.tanggal);
        $scope.getDetail(form.id);
        Data.get("penjualan/customer").then(function (response) {
            console.log(response);
            $scope.customer = response.data;
        })
        Data.get("penjualan/barang").then(function (response) {
            $scope.barang = response.data;
        });
    };
    $scope.save = function (form) {
        $scope.loading = true;
        var form = {
            data: form,
            detail: $scope.listDetail
        }
        Data.post("penjualan/save", form).then(function (result) {
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
            Data.post("penjualan/hapus", row).then(function (result) {
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

    $scope.changeCustomer = function (val) {
        // Data.get("customer/index").then(function (data) {
        //     $scope.customer = data.data.list;
        // });
        $scope.form.id_customer = val;
        $scope.form.alamat = val.alamat;
        $scope.form.nama = val.nama;
        $scope.form.telepon = val.telepon;
    };

    app.controller("addCustomerCtrl", function ($state, $scope, Data, $uibModalInstance, form, $rootScope) {
        $scope.formcustomer = {};
        $scope.save = function (form) {
            // $scope.loading = true;
            Data.get("customer/index").then(function (data) {
                $scope.customer = data.data.list;
            });
            Data.post("customer/save", form).then(function (result) {
                if (result.status_code == 200) {
                    $rootScope.alert("Berhasil", "Data berhasil tersimpan", "success");
                    $uibModalInstance.close(result.data);
                } else {
                    $rootScope.alert("Terjadi Kesalahan", setErrorMessage(result.errors), "error");
                }
                // $scope.loading = false;
            });
        };

        $scope.close = function () {
            $uibModalInstance.dismiss("cancel");
        };
    });
    app.controller("editCustomerCtrl", function ($state, $scope, Data, $uibModalInstance, form, $rootScope) {
        $scope.form = {};
        $scope.formModal = form;
        $scope.update = function (formdata) {
            // $scope.loading = true;
            Data.post("customer/editCustomer", formdata).then(function (result) {
                if (result.status_code == 200) {
                    $uibModalInstance.close(result.data);
                    $rootScope.alert("Berhasil", "Data berhasil Diupdate", "success");
                    $scope.close(formdata);
                } else {
                    $rootScope.alert("Terjadi Kesalahan", setErrorMessage(result.errors), "error");
                }
            });
        };

        $scope.close = function () {
            $uibModalInstance.dismiss("cancel");
        };
    });
    // NANAK
    app.controller("editJurnalHppCtrl", function ($filter, $state, $scope, toaster, Data, $uibModalInstance, form) {
        $scope.form = {};
        $scope.formModal = form;
        $scope.editjurnalhpp = function (formdata) {
            Data.post("t_penjualan/editJurnalHpp", formdata).then(function (data) {
                if (data.data) {
                    swal("Berhasil", "Berhasil di Edit", "success");
                    $scope.close();
                } else {
                    toaster.pop("error", "Terjadi Kesalahan", data.data.error);
                }
            });
        };
        $scope.close = function () {
            $uibModalInstance.dismiss("cancel");
        };
    });
    // END

    $scope.addCustomer = function () {
        var form = "";
        var modalInstance = $uibModal.open({
            templateUrl: "tpl/penjualan/modal_tambah_customer.html",
            controller: "addCustomerCtrl",
            size: "lg",
            backdrop: "static",
            resolve: {
                form: function () {
                    return form;
                }
            }
        });
        modalInstance.result.then(function (result) {
            $scope.changeCustomer(result);
        }, function () {});
    };

    $scope.editCustomer = function (form) {
        // form.id_customer = id_customer;
        var modalInstance = $uibModal.open({
            templateUrl: "tpl/penjualan/modal_edit_customer.html",
            controller: "editCustomerCtrl",
            size: "lg",
            backdrop: "static",
            resolve: {
                form: function () {
                    return form;
                }
            }
        });
        modalInstance.result.then(function (result) {
            $scope.changeCustomer(result);
        }, function () {});
    };

});