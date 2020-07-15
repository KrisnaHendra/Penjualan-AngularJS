app.controller("rekappenjualanCtrl", function ($scope, Data, $rootScope) {
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
    $scope.tampilkan = false;


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
            bulan: moment($scope.form.bulan).format("MMMM"),
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
        $scope.colspan = param['day'] + 2;
        $scope.colspanBulan = param['day'];
        $scope.dataBulan = param['bulan'];
        $scope.dataTahun = param['year'];

        Data.get("rekappenjualan/index", param).then(function (response) {
            $scope.displayed = response.data.list;
            tableState.pagination.numberOfPages = Math.ceil(
                response.data.totalItems / limit
            );
        });
        // Data.get("rekappenjualan/total").then(function (response) {
        //     $scope.listTotal = response.data;
        // });
        Data.get("rekappenjualan/dataArray", param).then(function (response) {
            $scope.listKas = response.data.listResult;
            $scope.listSum = response.data.listSum;
        });
        Data.get("rekappenjualan/sumTotal", param).then(function (result) {
            $scope.totalSum = result.data.totalSum;
            // console.log(result.data.totalSum);
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

    $scope.filterData = function () {
      $scope.tampilkan = true;
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

    // console.log($scope.listSum);


    $scope.listDetail = [{}];

    $scope.cancel = function () {
        $scope.is_edit = false;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.callServer(tableStateRef);
    };

    $scope.export = function () {
        ExportToExcel();
    }

    function ExportToExcel() {
        var tab_text = "<table border='2px'><tr bgcolor='yellow'>";
        var textRange;
        var j = 0;
        tab = document.getElementById('tabelRekap'); // id of table

        for (j = 0; j < tab.rows.length; j++) {
            tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
            //tab_text=tab_text+"</tr>";
        }

        tab_text = tab_text + "</table>";

        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");

        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) // If Internet Explorer
        {
            txtArea1.document.open("txt/html", "replace");
            txtArea1.document.write(tab_text);
            txtArea1.document.close();
            txtArea1.focus();
            sa = txtArea1.document.execCommand("SaveAs", true, "Say Thanks to Sumit.xls");
        } else //other browser not tested on IE 11
            sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
        return (sa);
    }

    $scope.print = function () {
        printData();
    }

    function printData() {
        var divToPrint = document.getElementById("rekapPenjualan");
        newWin = window.open("");
        newWin.document.write(divToPrint.outerHTML);
        newWin.print();
        newWin.close();
    }

});
