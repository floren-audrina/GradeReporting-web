<?php
    include("../api/connect.php");

    if (isset($_GET["angkatan"])) {
        $angkatan = $_GET['angkatan'];
    }

    if (isset($_GET["tahun"])) {
        $tahun = $_GET['tahun'];
    }

    if (isset($_GET["periode"])) {
        $periode = $_GET['periode'];
    }
    if (isset($_GET["val"])) {
        $val = $_GET['val'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Data CPL</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../css.css">

    <!-- lock screen, spy tdk bisa di swipe kanan kiri -->
    <style>
        body {
            overflow-x: hidden;
        } th {
        cursor: pointer;
        };
    </style>
</head>
<body>
    <!-- navbar -->
    <?php include "../navbar/navbar_after_login.php"; ?>

    <!-- bread crumbs -->
    <div class="row">
        <div class="col-md-9  col-xs-9">
            <ul id="breadcrumb" class="breadcrumb">
                <li class="breadcrumb-item"><a href="home_cpl.php">Home</a></li>
                <li class="breadcrumb-item active">Rata-rata Nilai</li>
            </ul>
        </div>

    <!-- HARUS INI DULU SOALNYA NANTI VARIABEL NYA MAU DI POST KE HALAMAN LAIN -->
    <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Mengambil nilai dropdown yang dipilih
            $selectedValue = $_POST['filtering'];

            // Membuat pernyataan if berdasarkan nilai dropdown
            if ($selectedValue == 'Daftar Mahasiswa Dibawah Rata-rata Nilai') {
                header("location: ../cpl/reporting.php?angkatan=$angkatan&&tahun=$tahun&&periode=$periode&&val=$selectedValue");
                exit;
            } 
            else if ($selectedValue == 'List Data') {
                header("location: ../cpl/data_cpl.php?angkatan=$angkatan&&tahun=$tahun&&periode=$periode&&val=$selectedValue");
                exit;
            } 
            else if ($selectedValue == 'Jumlah Mahasiswa Mengulang MK') {
                header("location: ../cpl/jumlah.php?angkatan=$angkatan&&tahun=$tahun&&periode=$periode&&val=$selectedValue");
                exit;
            } 
            else if ($selectedValue == 'Distribusi Nilai') {
                header("location: ../cpl/distribusi.php?angkatan=$angkatan&&tahun=$tahun&&periode=$periode&&val=$selectedValue");
                exit;
            } 
        }   
    ?>

    <!-- isi -->
    <div class="container">
        <div class="row">
                <div class="col-md-7 col-xs-7">
                    <p class="semester">Semester <span><?php echo $periode; ?></span> || Angkatan <span><?php echo $angkatan; ?></span> || Tahun <span><?php echo $tahun; ?></span></p>
                </div>

                <div class="col-md-4 col-xs-4">
                    <form action="" method="post">
                        <div class="col-md-10 col-xs-10">
                                <select name="filtering" id="filtering" class="form-control" onchange="redirectPage()">
                                    <option value="selected value"><?php echo $val; ?></option>
                                    <option value="List Data">List Data</option>
                                    <option value="Distribusi Nilai">Distribusi Nilai</option>
                                    <option value="Jumlah Mahasiswa Mengulang MK">Jumlah Mahasiswa Mengulang MK</option>
                                    <!-- <option value="Rata-rata Nilai">Rata-rata Nilai</option> -->
                                    <option value="Daftar Mahasiswa Dibawah Rata-rata Nilai">Daftar Mahasiswa Dibawah Rata-rata Nilai</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-xs-2">
                                <input type="submit" value="Kirim" class="btn btn-primary">
                            </div>
                    </form>
                </div>

                <div class="col-md-1 col-xs-1">
                        <div class="col-md-1 col-xs-1">
                    <svg id="downloadCSV" onclick="downloadCSV()" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" style="cursor: pointer;">
                        <path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/>
                    </svg>
                </div>
            </div>

        <!-- RATA-RATA CPL, BELOM BERDASARKAN TAHUN, ANGKATAN-->
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <table class="table" id="rata2_cpl">
                        <tr>
                            <th class="bordered-header" scope="col" onclick="sorTable(0)">CPL</th>
                            <th class="bordered-header" scope="col" onclick="sortTable(1)">Tahun</th>
                            <th class="bordered-header" scope="col" onclick="sortTable(2)">Angkatan</th>
                            <th class="bordered-header" scope="col" onclick="sortTable(3)">Semester</th>
                            <th class="bordered-header" scope="col" onclick="sortTable(4)">Rata-rata Nilai</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT ikcpl.id_cpl, periode.tahun, mhsw.tahun AS angkatan, periode.semester, AVG(nilai) AS rata_nilai
                            FROM periode, kelas, mhsw, kelas_nilaicpmk, kelas_cpmk, ikcpl
                            WHERE mhsw.nrp_hash = kelas_nilaicpmk.nrp_hash
                            AND periode.id_periode = kelas.id_periode
                            AND kelas.id_kelas = kelas_cpmk.id_kelas
                            AND kelas_nilaicpmk.id_cpmk = kelas_cpmk.id_cpmk
                            AND ikcpl.id_ikcpl = kelas_cpmk.id_ikcpl";
                        
                            if ($periode !== "All") {
                                $sql .= " AND periode.semester = :periode";
                            }
                            
                            if ($angkatan !== "All") {
                                $sql .= " AND mhsw.tahun = :angkatan";
                            }
                            
                            if ($tahun !== "All") {
                                $sql .= " AND periode.tahun = :tahun";
                            }
                            
                            $sql .= " GROUP BY ikcpl.id_cpl, periode.tahun, mhsw.tahun, periode.semester";
                            $sql .= " ORDER BY mhsw.tahun ASC, ikcpl.id_cpl ASC";
                            
                            $query = $conn->prepare($sql);
                            
                            if ($periode !== "All") {
                                $query->bindParam(':periode', $periode, PDO::PARAM_STR);
                            }
                            
                            if ($angkatan !== "All") {
                                $query->bindParam(':angkatan', $angkatan, PDO::PARAM_STR);
                            }
                            
                            if ($tahun !== "All") {
                                $query->bindParam(':tahun', $tahun, PDO::PARAM_STR);
                            }
                            
                            $query->execute();
                            $result = $query->fetchAll();
                            
                            
                            $query->execute();
                            $result = $query->fetchAll();

                            if ($result) {
                                foreach ($result as $row) {
                                    echo '<tr>
                                    <td class="bordered-cell">' . $row['id_cpl'] . '</td>
                                    <td class="bordered-cell">' . $row['tahun'] . '</td>
                                    <td class="bordered-cell">' . $row['angkatan'] . '</td>
                                    <td class="bordered-cell">' . $row['semester'] . '</td>
                                    <td class="bordered-cell">' . $row['rata_nilai'] . '</td>
                                    </tr>';
                                }
                            } else {
                                echo "Tidak ada data yang ditemukan.";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
        var sort = "ascending";
        function sortTable(n) {

            var table, rows, switching, i, x, y, shouldSwap;
            table = document.getElementById("rata2_cpl");
            switching = true;
            rows = table.getElementsByTagName("TR");
            // console.log(sort);
            for (i = 1; i < (rows.length - 1); i++) {
                if (n==0 || n==1){
                    max = rows[1].getElementsByTagName("TD")[1].textContent.toString();
                    min = "";
                }else{
                    max = 0;
                    min = Infinity;
                }

                for (j = i; j < (rows.length); j++) {
                    shouldSwap = false;
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[j].getElementsByTagName("TD")[n];

                    if (n==0 || n==2){
                        xValue = parseInt(x.textContent.toString());
                        yValue = parseInt(y.textContent.toString());
                    }else{
                        xValue = x.textContent.toLowerCase();
                        yValue = y.textContent.toLowerCase();
                    }
                    
                    if(sort == "ascending"){
                        if (max < yValue) {
                            max = yValue;
                            index = j;
                        }
                    }else if (sort == "descending"){
                        if (min > yValue) {
                            min = yValue;
                            index = j;
                        }
                    }
                    
                }
                if (sort == "ascending") {
                    // console.log(max);  
                    if (xValue <= max){
                        rows[i].parentNode.insertBefore(rows[index], rows[i]);
                    }
                }else{
                    // console.log(min);
                    if (xValue >= min){
                        rows[i].parentNode.insertBefore(rows[index], rows[i]);
                    }
                }
            }
            if(sort == "ascending"){
                sort = "descending";
            }else{
                sort = "ascending";
            }
            // console.log(rows)
        }
            function downloadCSV() {
            var table = document.querySelector('table'); // Get the table element
            var rows = Array.from(table.querySelectorAll('tr')); // Get all rows in the table
            
            // Create a CSV content string
            var csvContent = rows.map(function(row) {
                var rowData = Array.from(row.querySelectorAll('th, td'))
                    .map(function(cell) {
                        return cell.textContent;
                    })
                    .join(',');
                return rowData;
            }).join('\n');
            
            // Create a Blob object with the CSV content
            var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            if (navigator.msSaveBlob) {
                // For IE and Edge browsers
                navigator.msSaveBlob(blob, 'table.csv');
            } else {
                // For other browsers
                var link = document.createElement('a');
                if (link.download !== undefined) {
                    var url = URL.createObjectURL(blob);
                    link.setAttribute('href', url);
                    link.setAttribute('download', 'Rata_Rata.csv');
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            }
        }
    </script>
</body>
</html>
