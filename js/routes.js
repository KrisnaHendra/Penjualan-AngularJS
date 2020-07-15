angular.module("app").config(["$stateProvider", "$urlRouterProvider", "$ocLazyLoadProvider", "$breadcrumbProvider",
    function ($stateProvider, $urlRouterProvider, $ocLazyLoadProvider, $breadcrumbProvider) {
        $urlRouterProvider.otherwise("/dashboard");
        $ocLazyLoadProvider.config({
            debug: false
        });
        $breadcrumbProvider.setOptions({
            prefixStateName: "app.main",
            includeAbstract: true,
            template: '<li class="breadcrumb-item" ng-repeat="step in steps" ng-class="{active: $last}" ng-switch="$last || !!step.abstract"><a ng-switch-when="false" href="{{step.ncyBreadcrumbLink}}">{{step.ncyBreadcrumbLabel}}</a><span ng-switch-when="true">{{step.ncyBreadcrumbLabel}}</span></li>'
        });
        $stateProvider.state("app", {
            abstract: true,
            templateUrl: "tpl/common/layouts/full.html",
            ncyBreadcrumb: {
                label: "Root",
                skip: true
            },
            resolve: {
                loadCSS: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load(["fontawesome", "simplelineicon"]);
                    }
                ],
            }
        }).state("app.main", {
            url: "/dashboard",
            templateUrl: "tpl/dashboard/dashboard.html",
            ncyBreadcrumb: {
                label: "Home"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load(["chart.js"]).then(() => {
                            return $ocLazyLoad.load({
                                files: ["tpl/dashboard/dashboard.js"]
                            });
                        });
                    }
                ]
            }
        }).state("app.generator", {
            url: "/generator",
            templateUrl: "tpl/generator/index.html",
            ncyBreadcrumb: {
                label: "Home"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load(["chart.js"]).then(() => {
                            return $ocLazyLoad.load({
                                files: ["tpl/generator/index.js"]
                            });
                        });
                    }
                ]
            }
        }).state("app.pemasukan", {
            url: "/pemasukan",
            templateUrl: "api/vendor/cahkampung/landa-acc/tpl/t_pemasukan/index.html",
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["api/vendor/cahkampung/landa-acc/tpl/t_pemasukan/index.js"]
                        });
                    }
                ]
            }
        }).state("app.akun", {
            url: "/akun",
            templateUrl: "tpl/akun/index.html",
            ncyBreadcrumb: {
                label: "Akun"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/akun/index.js"]
                        });
                    }
                ]
            }
        }).state("app.kontak", {
            url: "/kontak",
            templateUrl: "tpl/kontak/index.html",
            ncyBreadcrumb: {
                label: "Kontak"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/kontak/index.js"]
                        });
                    }
                ]
            }
        }).state("app.kasmasuk", {
            url: "/kasmasuk",
            templateUrl: "tpl/kasmasuk/index.html",
            ncyBreadcrumb: {
                label: "Kas Masuk"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/kasmasuk/index.js"]
                        });
                    }
                ]
            }
        }).state("app.kaskeluar", {
            url: "/kaskeluar",
            templateUrl: "tpl/kaskeluar/index.html",
            ncyBreadcrumb: {
                label: "Kas Keluar"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/kaskeluar/index.js"]
                        });
                    }
                ]
            }
        }).state("app.rekap", {
            url: "/rekap",
            templateUrl: "tpl/rekap/rekap.html",
            ncyBreadcrumb: {
                label: "Rekap"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load(['daterangepicker']).then(() => {
                            return $ocLazyLoad.load({
                                cache: false,
                                files: ["tpl/rekap/rekap.js"]
                            });
                        });
                    }
                ]
            }
        }).state("app.laporanpemasukan", {
            url: "/laporanpemasukan",
            templateUrl: "tpl/rekap/laporanpemasukan.html",
            ncyBreadcrumb: {
                label: "Laporan Pemasukan"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load(['daterangepicker']).then(() => {
                            return $ocLazyLoad.load({
                                cache: false,
                                files: ["tpl/rekap/laporanpemasukan.js"]
                            });
                        });
                    }
                ]
            }
        }).state("app.laporanpengeluaran", {
            url: "/laporanpengeluaran",
            templateUrl: "tpl/rekap/laporanpengeluaran.html",
            ncyBreadcrumb: {
                label: "Laporan Pengeluaran"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load(['daterangepicker']).then(() => {
                            return $ocLazyLoad.load({
                                cache: false,
                                files: ["tpl/rekap/laporanpengeluaran.js"]
                            });
                        });
                    }
                ]
            }
        }).state("app.rankpemasukan", {
            url: "/rankpemasukan",
            templateUrl: "tpl/rekap/rankpemasukan.html",
            ncyBreadcrumb: {
                label: "Laporan Pengeluaran"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/rekap/rankpemasukan.js"]
                        });
                    }
                ]
            }
        }).state("app.barang", {
            url: "/barang",
            templateUrl: "tpl/barang/index.html",
            ncyBreadcrumb: {
                label: "Data Barang"
            },
            resolve: {
                loadCSS: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load(["ngFileUpload", "angularFileUpload"]);
                    }
                ],
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/barang/index.js"]
                        });
                    }
                ]
            }
        }).state("app.satuan", {
            url: "/satuan",
            templateUrl: "tpl/satuan/index.html",
            ncyBreadcrumb: {
                label: "Data Satuan"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/satuan/index.js"]
                        });
                    }
                ]
            }
        }).state("app.kategori", {
            url: "/kategori",
            templateUrl: "tpl/kategori/index.html",
            ncyBreadcrumb: {
                label: "Data Kategori"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/kategori/index.js"]
                        });
                    }
                ]
            }
        }).state("app.supplier", {
            url: "/supplier",
            templateUrl: "tpl/supplier/index.html",
            ncyBreadcrumb: {
                label: "Data Supplier"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/supplier/index.js"]
                        });
                    }
                ]
            }
        }).state("app.customer", {
            url: "/customer",
            templateUrl: "tpl/customer/index.html",
            ncyBreadcrumb: {
                label: "Data Customer"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/customer/index.js"]
                        });
                    }
                ]
            }
        }).state("app.pembelian", {
            url: "/pembelian",
            templateUrl: "tpl/pembelian/index.html",
            ncyBreadcrumb: {
                label: "Pembelian"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/pembelian/index.js"]
                        });
                    }
                ]
            }
        }).state("app.penjualan", {
            url: "/penjualan",
            templateUrl: "tpl/penjualan/index.html",
            ncyBreadcrumb: {
                label: "Penjualan"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/penjualan/index.js"]
                        });
                    }
                ]
            }
        }).state("app.laporanpembelian", {
            url: "/laporanpembelian",
            templateUrl: "tpl/laporan/laporanpembelian.html",
            ncyBreadcrumb: {
                label: "Laporan Pembelian"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load(['daterangepicker']).then(() => {
                            return $ocLazyLoad.load({
                                cache: false,
                                files: ["tpl/laporan/laporanpembelian.js"]
                            });
                        });
                    }
                ]
            }
        }).state("app.rekappenjualan", {
            url: "/rekappenjualan",
            templateUrl: "tpl/laporan/rekappenjualan.html",
            ncyBreadcrumb: {
                label: "Rekap Penjualan"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load(['daterangepicker']).then(() => {
                            return $ocLazyLoad.load({
                                cache: false,
                                files: ["tpl/laporan/rekappenjualan.js"]
                            });
                        });
                    }
                ]
            }
        }).state("app.penjualanpertahun", {
            url: "/penjualanpertahun",
            templateUrl: "tpl/laporan/penjualanpertahun.html",
            ncyBreadcrumb: {
                label: "Data Customer"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/laporan/penjualanpertahun.js"]
                        });
                    }
                ]
            }
        }).state("pengguna", {
            abstract: true,
            templateUrl: "tpl/common/layouts/full.html",
            ncyBreadcrumb: {
                label: "User Login"
            },
            resolve: {
                loadCSS: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load(["fontawesome", "simplelineicon", "iconflag"]);
                    }
                ],
                loadPlugin: ["$ocLazyLoad", function ($ocLazyLoad) {}],
                authenticate: authenticate
            }
        }).state("pengguna.akses", {
            url: "/hak-akses",
            templateUrl: "tpl/m_akses/index.html",
            ncyBreadcrumb: {
                label: "Hak Akses"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/m_akses/index.js"]
                        });
                    }
                ]
            }
        }).state("pengguna.user", {
            url: "/user",
            templateUrl: "tpl/m_user/index.html",
            ncyBreadcrumb: {
                label: "Pengguna"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/m_user/index.js"]
                        });
                    }
                ]
            }
        }).state("pengguna.profil", {
            url: "/profil",
            templateUrl: "tpl/m_user/profile.html",
            ncyBreadcrumb: {
                label: "Profil Pengguna"
            },
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/m_user/profile.js"]
                        });
                    }
                ]
            }
        }).state("page", {
            abstract: true,
            templateUrl: "tpl/common/layouts/blank.html",
            resolve: {
                loadCSS: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load(["fontawesome", "simplelineicon"]);
                    }
                ]
            }
        }).state("page.login", {
            url: "/login",
            templateUrl: "tpl/common/pages/login.html",
            resolve: {
                loadMyCtrl: ["$ocLazyLoad",
                    function ($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            files: ["tpl/site/login.js"]
                        });
                    }
                ]
            }
        }).state("page.404", {
            url: "/404",
            templateUrl: "tpl/common/pages/404.html"
        }).state("page.500", {
            url: "/500",
            templateUrl: "tpl/common/pages/500.html"
        });

        function authenticate($q, UserService, $state, $transitions, $location, $rootScope) {
            var deferred = $q.defer();
            if (UserService.isAuth()) {
                deferred.resolve();
                var fromState = $state;
                var globalmenu = ["page.login", "pengguna.profil", "app.main", "page.500", "app.generator"];
                $transitions.onStart({}, function ($transition$) {
                    var toState = $transition$.$to();
                    if ($rootScope.user.akses[toState.name.replace(".", "_")] || globalmenu.indexOf(toState.name)) {} else {
                        $state.target("page.500")
                    }
                });
            } else {
                $location.path("/login");
            }
            return deferred.promise;
        }
    }
]);
