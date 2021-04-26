<?php
$dir = "upload";



function listFolderFiles($dir){

$mass = scandir($dir);


    foreach ($mass as $key => $value) {
        if($value == '.' or $value == '..' ) {
            //Тут точки

        } else {

            if(is_dir($dir.'/'.$value)){
                //Если это директория
                listFolderFiles($dir.'/'.$value);
            } else {

                // Если это файл
                echo "<br>\n";
                $path = $dir.'/'.$value;
                $fileType = mime_content_type($path);
                    if($fileType == "image/png" or $fileType == "image/jpeg") {

                        // Да, это картинка
                        $pathinfo = pathinfo($path);
                        $image = imagecreatefromstring(file_get_contents($path));

                        $newName = '/'.$pathinfo['filename'].'.webp';

                            $check = file_get_contents($dir.$newName);
                            if($check) {
                                echo "<br>\n";
                                echo "webp уже существует";
                                echo "<br>\n";
                                echo $dir.$newName;
                            } else {


                                imagewebp($image,$dir.$newName,80);
                                $webpCheck = file_get_contents($dir.$newName);
                                if($webpCheck) {
                                    echo "<br>\n";

                                    echo "Исходник: ".$path;
                                    echo "<br>\n";

                                    echo "WEBP IMAGE успешно создана!  ".$dir.$newName;
                                }

                            }


                    } else {
                        // Не картинка
                    }

            }

        }

    }



}
listFolderFiles($dir);










//
//
//
// function listFolderFiles($dir){
//
//     $DOM = scandir($dir);
//
//
//     foreach($DOM as $keyz){
//
//         echo "<br>".$keyz;
//         if($keyz != '.' && $keyz != '..'){
//
//             if(is_dir($dir.'/'.$keyz)) listFolderFiles($dir.'/'.$keyz);
//
//         } else {
//
//         }
//     }
//
// }
?>
