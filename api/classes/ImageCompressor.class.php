<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
/**
 * *********************************************************************************************************
 * @_forProject: General Use | Developed At: TAMMA CORPORATION
 * @_purpose: (resize, compress and save images) 
 * @_version Release: package_one
 * @_created Date: 1/29/2020
 * @_author(s):
 *   1) Mr. Michael kaiva Nimley. (Hercules d Newbie)
 *      @contact Phone: (+231) 777-007-009
 *      @contact Mail: michaelkaivanimley.com@gmail.com, mnimley6@gmail.com, mnimley@tammacorp.com
 *   -------------------------------------------------------------------------------------------------------
 *   2) Mr. Enoch C. Jallah 
 *      @contact Phone: (+231) 775-901-684
 *      @contact Mail: ejallah@tammacorp.com
 * *********************************************************************************************************
 */

class ImageCompressor
{
    public $options;

    public $finialImage;
    public  $new_width;
    public  $new_height;
    public  $finalImage;
    public  $imageType;
    public  $imageCreatedFromFile;
    public  $requestedDimension;
    private $resizedImage;
    private $dest_image;

    // NOTE: supported image sizes
    public $supportedDimension = array(
        "thumbnail_xs"  =>  array( "width" => 125, "height" => 125 ),
        "thumbnail_sm"  =>  array( "width" => 200, "height" => 200 ),
        "thumbnail_m"   =>  array( "width" => 400, "height" => 400 ),
        "thumbnail_lg"  =>  array( "width" => 600, "height" => 600 )
    );

    function __construct(Object $options) {
        $this->options = $options;
    }

    public function getResizedImage() {
        $this->resizedImage = $this->dimensionValidator();
        return $this->resizedImage;
    }

    private function dimensionValidator() {
        switch ($this->options->dimension) {
            case 'thumbnail_xs':
            case 'thumbnail_sm':
            case 'thumbnail_m':
            case 'thumbnail_lg':
                $this->new_width  = $this->supportedDimension[$this->options->dimension]['width'];
                $this->new_height = $this->supportedDimension[$this->options->dimension]['height'];
                return $this->processImageByType();
                break;
            default:
                if (is_array($this->options->dimension)) {
                    if ( 
                        !empty($this->options->dimension['width']) && 
                        !empty($this->options->dimension['height']) 
                    ) {
                        $this->new_width  = $this->options->dimension['width'];
                        $this->new_height = $this->options->dimension['height'];
                        return $this->processImageByType();
                    } else {
                        return [
                            "status" => false,
                            "body" => [
                                "message" => "Custom dimensions must be array and contain a specified width and height",
                                "result"  => null
                            ]
                        ];                    
                    }
                } else {
                    return [
                        "status" => false,
                        "body" => [
                            "message" => "The supplied dimension is unsupported. Supported dimensions: thumbnail_xs, thumbnail_sm, thumbnail_m, thumbnail_lg or custom",
                            "result"  => null
                        ]
                    ];
                }
            break;
        }
    }

    private function processImageByType() {
        if ( is_array($this->options->file_path) ) {
            $this->imageType =  $this->options->file_path['type'];
        } else {
            $this->imageType = pathinfo($this->options->file_path)['extension'];
        }
        switch ( $this->imageType ) {
            case 'jpg':
            case 'jpeg':
                $this->imageCreatedFromFile  =  imagecreatefromjpeg( $this->options->file_path );
                break;
            case 'png':
                $this->imageCreatedFromFile  =  imagecreatefrompng( $this->options->file_path );
                break;
            case 'gif':
                $this->imageCreatedFromFile  =  imagecreatefromgif( $this->options->file_path );
                break;
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
            case 'image/png':
            case 'image/gif':
                ini_set('memory_limit', '64M');
                $this->imageType              =   explode("/", $this->imageType)[1];
                $img                          =   file_get_contents( $this->options->file_path['tmp_name'] );
                $this->imageCreatedFromFile   =   imagecreatefromstring( $img );
                break;
            default:
                return [
                    "status" => false,
                    "body"   => [
                        "message" => $this->imageType." is unsupported. Supported file types: jpeg, jpg, png, gif",
                        "result"  => null
                    ]
                ];
        }
        // NOTE: start file compression
        return $this->compressAndResizeImage();
    }

    private function compressAndResizeImage() {   
        // get image orginal width and height
        $img_original_width   =  imagesx($this->imageCreatedFromFile);
        $img_original_height  =  imagesy($this->imageCreatedFromFile);
        $makeImage            =  ($this->imageType == "jpg") ? 'imagejpeg' : 'image'.$this->imageType;

        if($this->new_width == 'default'){
            $this->new_width = $img_original_width;
        }
        if($this->new_height == 'default'){
            $this->new_height = $img_original_height;
        }

        if($this->new_width == 'auto' || $this->new_height == 'auto'){
            $this->new_width  = $this->new_width == 'auto' && $this->new_height !="auto" ? $this->new_height : $this->new_width;
            $this->new_height = $this->new_height == 'auto' &&  $this->new_width != 'auto' ?  $this->new_width : $this->new_height;

            if($img_original_width > $img_original_height) 
            {
                $this->new_height    =   $img_original_height*($this->new_height/$img_original_width);
            }

            if($img_original_width < $img_original_height) 
            {
                $this->new_width    =   $img_original_width*($this->new_width/$img_original_height);
            }
        }
        
        $dest_imagex = $this->new_width;
        $dest_imagey = $this->new_height;
        $this->dest_image  = imagecreatetruecolor($dest_imagex, $dest_imagey);

        // specify quality of output
        if ( $this->options->quality == "low" ) {
            imagecopyresized($this->dest_image, $this->imageCreatedFromFile, 0, 0, 0, 0, $dest_imagex, $dest_imagey, $img_original_width, $img_original_height);
        } 
        else if ( $this->options->quality == "high" ) {
            imagecopyresampled($this->dest_image, $this->imageCreatedFromFile, 0, 0, 0, 0, $dest_imagex, $dest_imagey, $img_original_width, $img_original_height);
        }

        imagedestroy($this->imageCreatedFromFile);
        // scale image down to requested dimension
        $this->finialImage = imagescale ( $this->dest_image, $this->new_width, $this->new_height,  IMG_BILINEAR_FIXED );
        // $this->finialImage = imagescale ( $this->imageCreatedFromFile, $this->new_width, $this->new_height,  IMG_BILINEAR_FIXED );


        // NOTE: to be able to process image and place it back into $_FILES
        // $tmpfname = tempnam("/tmp", "UL_IMAGE"); // create location and name of new file in tmp storage
        // $img = file_get_contents($url); // get saved file
        // file_put_contents($tmpfname, $img); // place saved file into newly created tmp storage

        if ( empty($this->options->save) ) {
            return [
                "status" => true,
                "body" => [
                    "message" => "your image was resized and compressed. for useage instructions see ['implementation_guide']",
                    "result"  => $this->finialImage,
                    "implementation_guide" => [
                        "save" => '  $img_thumb = $resizedImage->getResizedImage();
                        header("Content-Type: image/jpeg"); 
                        imagejpeg($img_thumb, "file_path"."image_name.extension")',
                        "display_in_browser" => '  $img_thumb = $resizedImage->getResizedImage();
                        header("Content-Type: image/jpeg"); 
                        imagejpeg($img_thumb)',
                    ]
                ]
            ];
        } 
        elseif ( $this->options->save == "base64" ) {
            ob_start (); 
                $makeImage( $this->finialImage );
                $image_data = ob_get_contents(); 
            ob_end_clean ();
            return [
                "status" => true,
                "body" => [
                    "message" => "your image was resized, compressed and converted to base64",
                    "result"  => "data:image/x-icon;base64,".base64_encode($image_data),
                    "implementation_guide" => [
                        "display_in_browser" =>  htmlspecialchars( '$img_thumb = $resizedImage->getResizedImage(); '. 
                        "<img src=". '<?php echo $img_thumb["body"]["result"]; ?> alt="">')
                    ]
                ]
            ];
        }
        else {
            ini_set('memory_limit', '128M');
            $result       = $makeImage( $this->finialImage, $this->options->save.".".$this->imageType );
            if ( $result == 1 ) {
                return [
                    "status" => true,
                    "body" => [
                        "message" => "your image was resized, compressed and saved",
                        "result"  => null
                    ]
                ];
            } else {
                return [
                    "status" => false,
                    "body" => [
                        "message" => "image resize and compression failed",
                        "result"  => null
                    ]
                ];
            }
        }
    }

    public function unsave(){
        if(file_exists($this->options->save.".".$this->imageType) === true){
            return unlink($this->options->save.".".$this->imageType);
        }
    }

    public function get_extension(){
        return $this->imageType;
    }
}