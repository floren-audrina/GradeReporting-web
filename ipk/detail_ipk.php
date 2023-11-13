<?php
include("../api/connect.php");


if (isset($_POST["nrp"])) {
    $nrp = $_POST['nrp'];
}

if (isset($_POST["year"])) {
    $year = $_POST['year'];
}

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
        <title>Detail CPL</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
        <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> -->
        <link rel="stylesheet" type="text/css" href="../css.css">
        <script type="text/javascript" src="../chartjs/Chart.js"></script>
    </head>
    <body>
        <!-- navbar -->
            <?php include "../navbar/navbar_after_login.php";?>
        <!-- bread crumbs -->
            <div class="row">
            <ul id="breadcrumb" class="breadcrumb">
                <li class="breadcrumb-item"><a href="home_cpl.php">Home</a></li>
                <li class="breadcrumb-item active"><a href="data_cpl.php?angkatan=$angkatan&&tahun=$tahun&&periode=$periode&&val=$selectedValue">Data</a></li>
                <li class="breadcrumb-item active">Detail data</li>               
            </ul>
            </div>
        <!-- isi -->
        <div class="container">
            <div class="row g-2" style="margin-bottom:20px;">
                <!-- <div class="col-md-6">
                    <div class="p-3">Nama:</div>
                </div>
                <div class="col-md-6">
                    <div class="p-3">Program Studi:</div>
                </div> -->
                <div class="col-md-6">
                    <div class="p-3">NRP: <?php echo $nrp; ?> </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3">Angkatan: <?php echo $year; ?> </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Mata Kuliah </th>
                        <th>Nilai Angka </th>
                        <th>Nilai Huruf </th>
                    </tr>
                </thead>
            </table>

            <?php
                // $nrp='$nrp';
                
                $query = "SELECT SUM((kelas_cpmk.persentase/100)*kelas_nilaicpmk.nilai) AS 'nilai CPL', kelas_cpmk.persentase, ikcpl.id_ikcpl, ikcpl.id_cpl, kelas_nilaicpmk.nilai, mk.mk, mhsw.nrp_hash, periode.tahun
                FROM kelas_cpmk
                JOIN kelas_nilaicpmk ON kelas_cpmk.id_cpmk = kelas_nilaicpmk.id_cpmk
                JOIN ikcpl ON kelas_cpmk.id_ikcpl = ikcpl.id_ikcpl
                JOIN kelas ON kelas_cpmk.id_kelas = kelas.id_kelas
                JOIN mk ON kelas.id_mk = mk.id_mk
                JOIN mhsw ON kelas_nilaicpmk.nrp_hash = mhsw.nrp_hash
                JOIN periode ON kelas.id_periode = periode.id_periode  
                JOIN cpl ON ikcpl.id_cpl = cpl.id_cpl
                WHERE mhsw.nrp_hash = ?
                GROUP BY mk.mk, kelas_nilaicpmk.nrp_hash
                ORDER BY `mhsw`.`nrp_hash` ASC";

                $query = $conn->prepare($query);
                $query->execute([$nrp]);

                
                $nilai_huruf='';
                $nilai_num=0;
                while ($row = $query->fetch()) {
                    if ($row['nilai CPL'] >= 86 AND $row['nilai CPL'] <= 100) {
                        $nilai_huruf = 'A';
                        $nilai_num = 4.0;
                    }
                    else if ($row['nilai CPL'] >= 76 AND $row['nilai CPL'] <= 85) {
                        $nilai_huruf = 'B+';
                        $nilai_num = 3.5;
                    }
                    else if ($row['nilai CPL'] >= 69 AND $row['nilai CPL'] <= 75) {
                        $nilai_huruf = 'B';
                        $nilai_num = 3.0;
                    }
                    else if ($row['nilai CPL'] >= 61 AND $row['nilai CPL'] <= 68) {
                        $nilai_huruf = 'C+';
                        $nilai_num = 2.5;
                    }
                    else if ($row['nilai CPL'] >= 56 AND $row['nilai CPL'] <= 60) {
                        $nilai_huruf = 'C';
                        $nilai_num = 2.0;
                    }
                    else if ($row['nilai CPL'] >= 41 AND $row['nilai CPL'] <= 55) {
                        $nilai_huruf = 'D';
                        $nilai_num = 1.0;
                    }
                    else if ($row['nilai CPL'] >= 0 AND $row['nilai CPL'] <= 40) {
                        $nilai_huruf = 'E';
                        $nilai_num = 0;
                    }
                    // echo $row['mk']."<br>";
                    // echo $nilai_huruf." ".$nilai_num."<br>";
                    // echo "<tr>
                    //         <td>".$row['mk']."</td>
                    //         <td>".$nilai_huruf."</td>
                    //         <td>".$nilai_num."</td>
                    //     </tr>"."<break";

                    echo '<tr>
                            <td>'.$row['mk'].'</td>
                            <td>'.$nilai_num.'</td>
                            <td>'.$nilai_huruf.'</td>
                        </tr>';
                }
            
            ?>
            
        </div>
    </body>
</html>