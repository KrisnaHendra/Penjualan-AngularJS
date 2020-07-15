<?php

$array = array
    (
    "INV/01/PBL/0001" => array
    (
        "2020-05-03" => array
        (
            "Haifa Shalsabella" => array
            (
                "Indomie" => array
                (
                    "2400" => 20,
                ),

                "Downy" => array
                (
                    "10000" => 5,
                ),

            ),

        ),

    ),

    "INV/01/PBL/0006" => array
    (
        "2020-05-04" => array
        (
            "Haifa Shalsabella" => array
            (
                "Beras" => array
                (
                    "15000" => 7,
                ),

            ),

        ),

    ),

    "INV/01/PBL/0007" => array
    (
        "2020-05-07" => array
        (
            "Haifa Shalsabella" => array
            (
                "Sprite" => array
                (
                    "5200" => 15,
                ),

            ),

        ),

    ),

    "INV/01/PBL/0005" => array
    (
        "2020-05-04" => array
        (
            "Calista Ardelia" => array
            (
                "Downy" => array
                (
                    "20000" => 8,
                ),

            ),

        ),

    ),

    "INV/01/PBL/0002" => array
    (
        "2020-05-04" => array
        (
            "Arel Dwiffa" => array
            (
                "Panci" => array
                (
                    "43000" => 2,
                ),

                "Gula" => array
                (
                    "12000" => 1,
                ),

            ),

        ),

    ),

    "INV/01/PBL/0009" => array
    (
        "2020-05-13" => array
        (
            "Nabila Rahmadanti" => array
            (
                "Aquarium" => array
                (
                    "78500" => 7,
                ),

            ),

        ),

    ),

);

echo "<pre>";
foreach ($array as $key => $value) {
    echo "<br>";
    echo $key;
    echo " ";
    foreach ($value as $key2 => $value2) {
        echo $key2;
        foreach ($value2 as $key3 => $value4) {
            echo " " . $key3 . " ";
            foreach ($value4 as $key4 => $value5) {
                echo $key4 . " ";
                foreach ($value5 as $key5 => $value6) {
                    echo $key5 . " ";
                    echo $value6 . " ";
                }
            }
        }
    }
}
echo "<br>";
// print_r($array);
for($i=0;$i<=1;$i++){
    for($j=0; $j<=9; $j++){
        echo "A";
        for($k=0; $k<=2; $k++){
            echo $k;
        }
    }echo "<br>";
}