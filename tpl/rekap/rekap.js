app.controller("rekapCtrl", function ($scope, Data, $rootScope) {
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
    $scope.form.bulan = new Date();


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
            month: moment($scope.form.bulan).format("MM"),
            year: moment($scope.form.bulan).format("YYYY"),
            day: moment($scope.form.bulan).daysInMonth()
        };

        if (tableState.sort.predicate) {
            param["sort"] = tableState.sort.predicate;
            param["order"] = tableState.sort.reverse;
        }
        if (tableState.search.predicateObject) {
            param["filter"] = tableState.search.predicateObject;
        }
        // console.log(param['month']);
        // console.log(param['year']);
        // console.log(param['day']);

        Data.get("rekap/index", param).then(function (response) {
            $scope.displayed = response.data.list;
            tableState.pagination.numberOfPages = Math.ceil(
                response.data.totalItems / limit
            );
        });
        Data.get("rekap/contohSusunanArray", param).then(function (response) {
            $scope.listKas = response.data;
        });

        $scope.isLoading = false;
        $scope.tgl = param['day'];

        $scope.range = function (step) {
            step = step || 1;
            var input = [];
            for (var i = 1; i <= param['day']; i += step) {
                input.push(i);
            }
            return input;
        };
    };





    // $scope.repeat = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29];
    // $scope.filterData = function () {
    //     Data.get("rekap/laporan", $scope.form).then(function (response) {
    //         $scope.laporan = response.data;
    //     });
    // }
    $scope.filterData = function () {
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
    $scope.getTotalKel = function () {
        var total = 0;
        for (var i = 0; i < $scope.displayed.length; i++) {
            var row = $scope.displayed[i];
            total += parseFloat(row.tot_keluar);
        }
        return total;
    }

    $scope.listDetail = [{}];


    $scope.cancel = function () {
        $scope.is_edit = false;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.callServer(tableStateRef);
    };

});