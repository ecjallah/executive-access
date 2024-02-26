<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
// session_start();
/**
 * *********************************************************************************************************
 * @_forProject: eHealth | Developed By: TAMMA CORPORATION
 * @_purpose: This class handles all file uploads.
 * @_version Release: 1.0
 * @_created Date: 10/20/2021
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class FilesHandler{
    function __construct(string $location, $saveLocation, $files, string $fileName, $file_repair_name = null){
        if(isset($_SESSION['user_id'])){
            $this->userId          = $_SESSION['user_id'];
            $this->user_type       = $_SESSION['user_type'];

            $this->method          = $_SERVER['REQUEST_METHOD'];
            $this->url             = $_SERVER['REQUEST_URI'];
                
            $this->location           = $location;
            $this->fileLocation       = $saveLocation;
            $this->file               = $files;
            $this->fileName           = $fileName;
            $this->file_new_name      = '';
            $this->repair_file_name   = $file_repair_name;
        }
    }

    //This function processes the newly uploaded file
    public function filesProcessor(){
        //Get file extension type
        // $fileExtension    = pathinfo($this->fileName, PATHINFO_EXTENSION);
        // if($fileExtension == 'jpeg' || $fileExtension == 'png' || $fileExtension == 'jpg' || $fileExtension == 'jfif'){
        //     $this->file_new_name  = sha1_file($this->file['tmp_name']);
        //     return $this->imageFileCompressor();

        // }else if($fileExtension == 'doc' || $fileExtension == 'pdf' || $fileExtension == 'txt' || $fileExtension == 'xls' || $fileExtension == 'xlsx'|| $fileExtension == 'ppt' || $fileExtension == 'pptx'){
        //     // $this->fileCompressor($this->file);
        // }else{
        //     return 'Unsupported file type';
        //     // return $fileExtension;
        // }

        if($this->repair_file_name == null){
            $this->file_new_name  = sha1_file($this->file['tmp_name']);
        }else{
            $this->file_new_name  = $this->repair_file_name;
        }

        return $this->imageFileCompressor();
    }

    //This function send image to the image conpressor class
    private function imageFileCompressor(){
        $imageObject      =   (object)[
            "file_path"   =>  $this->file,
            "dimension"   =>  ['width'=>'default', 'height' => 'auto'],
            "save"        =>  $this->location.'/'.$this->file_new_name,
            "quality"     =>  "high"
        ];

        $imageCompressor  = new ImageCompressor($imageObject);
        $returnedImage    = $imageCompressor->getResizedImage();
        
        $fileExt          = $imageCompressor->get_extension();
        return  $this->fileLocation.$this->file_new_name.'.'.$fileExt;
    }

    //This function handles file conpression
    private function fileCompressor(){}
}


// STEP 1) instantiate compression class
    // $resizedImage = new ResizeAndCompressImage((object)[
    //     "file_path"   =>  $_FILES['image'],  // path to image absolute/relative
    //     "dimension"   =>  "thumbnail_xs",    // options: [thumbnail_xs], [thumbnail_sm], [thumbnail_m], [thumbnail_lg] or custom: array("width"=>240,"height"=>70)
    //     "save"        =>  "base64",          // options: [base64], [file_path and neme without extension]. if left empty, resized and compressed file resource id will be returned
    //     "saveBase64"  =>  true               // optional: saves base64 to base64 folder
    //     "quality"     =>  "low"              // options: [high]/[low]
    // ]);
    // STEP 2) make call to [getResizedImage] method
    // $img_thumb = $resizedImage->getResizedImage();