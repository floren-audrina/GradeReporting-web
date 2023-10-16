<?php 
    require "connect.php";
    require "check_integrity.php";
    
    
    $imgLogoContent = "";
    $imgPosterContent = "";
    $visi = $_POST["visi"];
    $misi = $_POST["misi"];
    $deskripsi = $_POST["deskripsi"];
    $ig = $_POST["ig"];
    $linkWeb = $_POST["link_web"];
    $linkYt = $_POST["link_yt"];
    $oa = $_POST["oa"];

    $visi =str_replace('\'','\'\'',$visi);
    $misi =str_replace('\'','\'\'',$misi);
    $deskripsi =str_replace('\'','\'\'',$deskripsi);
    $ig =str_replace('\'','\'\'',$ig);
    $linkWeb =str_replace('\'','\'\'',$linkWeb);
    $linkYt =str_replace('\'','\'\'',$linkYt);
    $oa =str_replace('\'','\'\'',$oa);
    


    $logo =false;
    $poster =false;
    $oversizeLogo = false;
    $oversizePoster = false;


    if($_FILES["logo"]["size"]>2000000|| $_FILES["logo"]["size"]==0){
        $oversizeLogo = true;
    }

    if($_FILES["poster"]["size"]>2000000|| $_FILES["poster"]["size"]==0){
        $oversizePoster = true;
    }

    if($_FILES["logo"]["name"]!=null && !$oversizeLogo){
        $name = $_FILES["logo"]["name"];
        $image_temp_name = $_FILES['logo']['tmp_name']; 
        $img_ex = pathinfo($name, PATHINFO_EXTENSION);
        $img_ex_lc = strtolower($img_ex); 
        $allowTypes = array('jpg','png','jpeg','gif'); 
        if(in_array($img_ex_lc, $allowTypes)){ 
            $new_img_logo_name = "uploads/" . uniqid("IMG-", true).'.'.$img_ex_lc;
            $path = "../dashboard/" . $new_img_logo_name;
            move_uploaded_file($image_temp_name, $path);
        }
        $logo = true;
    }

    if($_FILES["poster"]["name"]!=null && !$oversizePoster){
        $name = $_FILES["poster"]["name"];
        $image_temp_name = $_FILES['poster']['tmp_name']; 
        $img_ex = pathinfo($name, PATHINFO_EXTENSION);
        $img_ex_lc = strtolower($img_ex); 
        $allowTypes = array('jpg','png','jpeg','gif'); 
        if(in_array($img_ex_lc, $allowTypes)){ 
            $new_img_poster_name = "uploads/" . uniqid("IMG-", true).'.'.$img_ex_lc;
            $path = "../dashboard/" . $new_img_poster_name;
            move_uploaded_file($image_temp_name, $path);
        }
        $poster = true;
    }

    // if(!empty($_FILES["logo"]["name"])) { 
    //     $fileName = basename($_FILES["logo"]["name"]); 
    //     $fileType = pathinfo($fileName, PATHINFO_EXTENSION); 

    //     $allowTypes = array('jpg','png','jpeg','gif'); 
    //     if(in_array($fileType, $allowTypes)){ 
    //         $image = $_FILES['logo']['tmp_name']; 
    //         $imgLogoContent = addslashes(file_get_contents($image)); 
    //     }
    // }

    // if(!empty($_FILES["poster"]["name"])) { 
    //     $fileName = basename($_FILES["poster"]["name"]); 
    //     $fileType = pathinfo($fileName, PATHINFO_EXTENSION); 

    //     $allowTypes = array('jpg','png','jpeg','gif'); 
    //     if(in_array($fileType, $allowTypes)){ 
    //         $image = $_FILES['poster']['tmp_name']; 
    //         $imgPosterContent = addslashes(file_get_contents($image)); 
    //     }
    // }



    $lk = $_SESSION["nrp"];

    if ($logo && $poster){
        $query = "UPDATE lk SET deskripsi = '$deskripsi', visi = '$visi', misi = '$misi', poster = '$new_img_poster_name', logo = '$new_img_logo_name',
        instagram = '$ig', website = '$linkWeb', youtube = '$linkYt', oa = '$oa' WHERE nama_lk = '$lk'";
    }else if(!$logo && $poster){
        $query = "UPDATE lk SET deskripsi = '$deskripsi', visi = '$visi', misi = '$misi', poster = '$new_img_poster_name',
        instagram = '$ig', website = '$linkWeb', youtube = '$linkYt', oa = '$oa' WHERE nama_lk = '$lk'";
    }else if($logo && !$poster){
        $query = "UPDATE lk SET deskripsi = '$deskripsi', visi = '$visi', misi = '$misi', logo = '$new_img_logo_name',
        instagram = '$ig', website = '$linkWeb', youtube = '$linkYt', oa = '$oa' WHERE nama_lk = '$lk'";
    }else{
        $query = "UPDATE lk SET deskripsi = '$deskripsi', visi = '$visi', misi = '$misi',
        instagram = '$ig', website = '$linkWeb', youtube = '$linkYt', oa = '$oa' WHERE nama_lk = '$lk'";
    }


    if($conn -> query($query) === True){
        if ($_FILES["logo"]["name"]==null && $_FILES["poster"]["name"]==null){ 
            header("Location: ../dashboard/keteranganLK.php?status=1");
        }else if($_FILES["logo"]["name"]!=null && $_FILES["poster"]["name"]==null){
            if($logo){
                header("Location: ../dashboard/keteranganLK.php?status=1");
            }else{
                header("Location: ../dashboard/keteranganLK.php?status=5");
            }
        }else if($_FILES["logo"]["name"]==null && $_FILES["poster"]["name"]!=null){
            if($poster){
                header("Location: ../dashboard/keteranganLK.php?status=1");
            }else{
                header("Location: ../dashboard/keteranganLK.php?status=5");
            }
        }else if($_FILES["logo"]["name"]!=null && $_FILES["poster"]["name"]!=null){
            if($logo || $poster){
                if($logo && $poster){
                    header("Location: ../dashboard/keteranganLK.php?status=1");
                }else if(!$logo && $poster){
                    header("Location: ../dashboard/keteranganLK.php?status=6");
                }else if($logo && !$poster){
                    header("Location: ../dashboard/keteranganLK.php?status=7");
                }
            }else{
                header("Location: ../dashboard/keteranganLK.php?status=5");
            }
        }
    }else{
        header("Location: ../dashboard/keteranganLK.php?status=8");
    }
?>