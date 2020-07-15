app.controller("penjualanpertahunCtrl", function ($scope, Data, $rootScope) {
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
     // $scope.callServer = function callServer(tableState) {
     //     tableStateRef = tableState;
     //     $scope.isLoading = true;
     //     var param = {
     //         month: moment($scope.form.bulan).format("MM"),
     //         bulan: moment($scope.form.bulan).format("MMMM"),
     //         year: moment($scope.form.bulan).format("YYYY"),
     //         day: moment($scope.form.bulan).daysInMonth()
     //     };
     //     if (tableState.search.predicateObject) {
     //         param["filter"] = tableState.search.predicateObject;
     //     }
     //     Data.get("laporanbeli/index", param).then(function (response) {
     //         $scope.displayed = response.data.list;
     //         tableState.pagination.numberOfPages = Math.ceil(
     //             response.data.totalItems / limit
     //         );
     //
     //     });
     //     $scope.isLoading = false;
     //
     // };

    $scope.filterData = function () {
      $scope.tampilkan = true;
      var param = {
             month: moment($scope.form.bulan).format("MM"),
             year: moment($scope.form.bulan).format("YYYY"),
             kategori: $scope.form.nama_kategori
         };
      console.log(param['kategori']);
      $scope.tahun = param['year'];
      Data.get("penjualanpertahun/dataArray", param).then(function (response) {
             $scope.listData = response.data.listResult;
             $scope.listSum = response.data.listSum;
             $scope.totalPerBulan = response.data.SumPerBulan;
         });
    }

    $scope.listBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];


    $scope.getTotal = function () {
        var total = 0;
        for (var i = 0; i < $scope.displayed.length; i++) {
            var row = $scope.displayed[i];
            total += parseFloat(row.total);
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
