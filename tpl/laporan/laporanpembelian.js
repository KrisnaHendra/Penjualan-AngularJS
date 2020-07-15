app.controller("laporanbeliCtrl", function ($scope, Data, $rootScope) {
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
        var param = {
            month: moment($scope.form.bulan).format("MM"),
            bulan: moment($scope.form.bulan).format("MMMM"),
            year: moment($scope.form.bulan).format("YYYY"),
            day: moment($scope.form.bulan).daysInMonth()
        };
        if (tableState.search.predicateObject) {
            param["filter"] = tableState.search.predicateObject;
        }
        Data.get("laporanbeli/index", param).then(function (response) {
            $scope.displayed = response.data.list;
            tableState.pagination.numberOfPages = Math.ceil(
                response.data.totalItems / limit
            );

        });
        // console.log(param['month']);


        // function printData() {
        //     var divToPrint = document.getElementById("printData");
        //     newWin = window.open("");
        //     newWin.document.write(divToPrint.outerHTML);
        //     newWin.print();
        //     newWin.close();
        // }

        $scope.export = function () {
            exportToExcel();
        }
        $scope.isLoading = false;


    };



    $scope.filterData = function () {
        var param = {
            month: moment($scope.form.bulan).format("MM"),
            bulan: moment($scope.form.bulan).format("MMMM"),
            year: moment($scope.form.bulan).format("YYYY"),
            day: moment($scope.form.bulan).daysInMonth()
        };
        $scope.tampilkan = true;
        Data.get("laporanbeli/index", param).then(function (response) {
            $scope.displayed = response.data.list;
        });
        $scope.dataBulan = param['bulan'];
        $scope.dataTahun = param['year'];
    }

    $scope.getTotal = function () {
        var total = 0;
        for (var i = 0; i < $scope.displayed.length; i++) {
            var row = $scope.displayed[i];
            total += parseFloat(row.harga_satuan * row.jumlah);
        }
        return total;
    }
    $scope.getTotalKel = function () {
        var total = 0;
        for (var i = 0; i < $scope.displayed.length; i++) {
            var row = $scope.displayed[i];
            total += parseInt(row.tot_keluar);
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

    function exportToExcel() {
        var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
        var textRange;
        var j = 0;
        tab = document.getElementById('tabelData'); // id of table

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

    $scope.printData = function () {
        var param = {
            bulan: moment($scope.form.bulan).format("MMMM"),
            month: moment($scope.form.bulan).format("MM"),
            year: moment($scope.form.bulan).format("YYYY")
        };
        window.open("api/laporanbeli/laporan?bulan=" + param['bulan'] + "&month=" + param['month'] + "&year=" + param['year']);
    }



});