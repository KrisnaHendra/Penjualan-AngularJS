app.controller("laporankeluarCtrl", function ($scope, Data, $rootScope) {
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
        Data.get("laporankeluar/index", param).then(function (response) {
            $scope.displayed = response.data.list;
            tableState.pagination.numberOfPages = Math.ceil(
                response.data.totalItems / limit
            );
        });
        $scope.isLoading = false;
    };


    $scope.getTotal = function () {
        var total = 0;
        for (var i = 0; i < $scope.displayed.length; i++) {
            var row = $scope.displayed[i];
            total += parseFloat(row.total);
        }
        return total;
    }

    $scope.filterTanggal = function () {
        // console.log($scope.cari.periode);
        $scope.callServer(tableStateRef);
    }


    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtittle = "Lihat Data : " + form.no_transaksi;
        $scope.form = form;
        $scope.getDetail(form.id);
    };

    $scope.cancel = function () {
        $scope.is_edit = false;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.callServer(tableStateRef);
    };


});