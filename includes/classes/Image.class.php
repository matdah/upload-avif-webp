<?php
/**
 * Examle code for uploading images and creating miniatures.
 * Converts and outputs to: JPEG, Webp, AVIF
 * @author Mattias Dahlgren, 2021 <mattias.dahlgren@miun.se>
 * @version 1.3
 */

class Image {
    private $imagepath;
    private $width_thumbnail;
    private $height_thumbnail;   
    private $jpeg_quality;
    private $webp_quality;
    private $avif_quality;
    private $avif_speed;

    public function __construct(
        // Default values
        $imagepath = 'images/', 
        $width_thumbnail = 500, 
        $height_thumbnail = 400, 
        $jpeg_quality = 80,
        $webp_quality = 60,
        $avif_quality = 50,
        $avif_speed = 5) 
    {
        $this->imagepath = $imagepath;
        $this->width_thumbnail = $width_thumbnail;
        $this->height_thumbnail = $height_thumbnail;
        $this->jpeg_quality = $jpeg_quality;
        $this->webp_quality = $webp_quality;
        $this->avif_quality = $avif_quality;
        $this->avif_speed = $avif_speed;
    }

    /**
     * Upload image
     * @param $file image
     * @return bool
     */
    public function uploadImage(array $image) : bool {
        
        if($this->setImage($image)) { 
            return true;
        } else {
            return false;
        }
    }

    /**
     * Set image-file (JPEG)
     * @param file $image
     * @return string filename
    */
    public function setImage($image) : bool {
        if($this->isImageAllowed($image)) {
            // Create filename
            $filename = $this->createFileName();

            //Flyttar filen till rätt katalog      
            move_uploaded_file($image["tmp_name"], $this->imagepath . $filename);
    
            //Spar namn på originalbild och miniatyr i variabler
            $storedfile = $filename;
            $thumbnail = "thumb_" . $filename;

            //Maximal storlek i höjd och bredd för miniatyr
            $width_thumbnail = $this->width_thumbnail;
            $height_thumbnail = $this->height_thumbnail;                     

            //Läser in originalstorleken på den uppladdade bilden, och spar 
            //den i variablerna width_orig, height_orig
            list($width_thumbnail_orig, $height_thumbnail_orig) = getimagesize($this->imagepath . $storedfile);
            
            //Räknar ut förhållandet mellan höjd och bredd (sk "ratio")
            //Detta för att kunna få samma höjd- breddförhållande på miniatyren
            $ratio_orig = $width_thumbnail_orig / $height_thumbnail_orig;				                       
            
            //Räknar ut storlek på miniatyr
            if ($width_thumbnail / $height_thumbnail > $ratio_orig) {
                $width_thumbnail = $height_thumbnail * $ratio_orig;
                $height_thumbnail = $width_thumbnail / $ratio_orig;
            } else {
                $height_thumbnail = $width_thumbnail / $ratio_orig;
                $width_thumbnail = $height_thumbnail * $ratio_orig;
            }

            // Konvertera till heltal
            $width_thumbnail = (int)$width_thumbnail;
            $height_thumbnail = (int)$height_thumbnail;

            // Skapar WebP-bild
            if(function_exists("imagewebp")) {
                $image_webp = imagecreatefromjpeg($this->imagepath . $storedfile);
                $filename_webp = pathinfo($storedfile)['filename'] . '.webp';
                imagewebp($image_webp, $this->imagepath . $filename_webp, $this->webp_quality);
            }                   

            //Skapar en ny bild miniatyrbild 
            $image_p = imagecreatetruecolor($width_thumbnail, $height_thumbnail);
            $image = imagecreatefromjpeg($this->imagepath . $storedfile);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width_thumbnail, $height_thumbnail, $width_thumbnail_orig, $height_thumbnail_orig);

            //Sparar miniatyr - JPEG
            imagejpeg($image_p, $this->imagepath . $thumbnail, $this->jpeg_quality);

            // Sparar miniatyr - WebP
            if(function_exists("imagewebp")) {
                $filename_webp = pathinfo($thumbnail)['filename'] . '.webp';
                imagewebp($image_p, $this->imagepath . $filename_webp, $this->webp_quality);
            }            

            // Skapar AVIF-bild
            if(function_exists("imageavif")) {
                $image_avif = imagecreatefromjpeg($this->imagepath . $storedfile);
                $filename_avif = pathinfo($storedfile)['filename'] . '.avif';
                imageavif($image_avif, $this->imagepath . $filename_avif, $this->avif_quality, $this->avif_speed);
            }      

            // Sparar miniatyr - AVIF
            if(function_exists("imageavif")) {
                $filename_avif = pathinfo($thumbnail)['filename'] . '.avif';
                imageavif($image_p, $this->imagepath . $filename_avif, $this->avif_quality, $this->avif_speed);
            }     
            
            // Lagra till JSON
            $this->saveToJson($storedfile);

            return true;
        } else {
            return false;
        }   
    }

    /**
     * Show info about image
     */
    public function showImageInfo($filename) {
        $output = "<section>\n<h3>Information</h3>\n";
        foreach(glob($this->imagepath . $filename . "*") as $file) {
            
            if(function_exists("mime_content_type")) {
                $mime = mime_content_type($file);
            } else {
                $mime = "(Okänd fityp)";
            }
            $output .= "<strong>File type: " . $mime . "</strong><br>\n";

            $filesize = filesize($file);
            // Convert to kb
            $filesize = $filesize / 1024;
            // Round
            $filesize = round($filesize);
            $output .= "File size: " . $filesize . " kilobytes<br>\n";          
            
            $output .= "Filename: <a href='$file' target='_blank'>$file</a><br>\n";  
            
            $output .= "<hr>\n";
        }

        $output .= "</section>\n";
        echo $output;
    }

    /** 
     * Save to JSON
     * @param string $filename
     * @return void
     */
    private function saveToJson($filename) : void {
        // Remove file extension
        $filename = pathinfo($filename)['filename'];

        $json = json_decode(file_get_contents('images.json'), true);
        $json[] = $filename;

        // Inverse array
        $json = array_reverse($json);
        
        file_put_contents('images.json', json_encode($json, JSON_PRETTY_PRINT));
    }

    /**
     * Return images from JSON content as array
     * @return array
     */
    public function getImages() : array {
        return json_decode(file_get_contents('images.json'), true);
    }

    /**
     * Check if image file is allowed
     * @param file $image
     * @return bool
     */
    public function isImageAllowed($image) : bool {
        $type = $image['type'];
        if($type != "image/jpeg") {
            return false;
        } else {
            return true;
        }
    }

    /**
	* Generate non-taken filenamn
	* @return string $filename
	*/
    public function createFileName() : string {
        do {
            $random_filename = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 12)), 0, 12);
            $random_filename = $random_filename . ".jpg";
        } while(!$this->FilenameAvailable(($random_filename)));

        return $random_filename;
    }

    /**
	* Check if filename alreade exist
	* @param string $filename
	* @return bool
	*/
	public function FilenameAvailable($filename) : bool {
        if(file_exists($this->imagepath . $filename)) {
            return false;
        } else {
            return true;
        }		 
   }

   /**
    * Show info about installed capabilities
    * @return void
    */
    public function displayInfo() : void {
        echo "<ul class='capabilities'>\n";

        echo "<li>PHP version: " . phpversion() . "</li>\n";

        if(function_exists("imagejpeg")) {
            echo "<li>imagejpeg: ok</li>\n";
        } else {
            echo "<li class='unavailable'>imagejpeg: Not available</li>\n";
        }
        
        if(function_exists("imagewebp")) {
            echo "<li>imagewebp: ok</li>\n";
        } else {
            echo "<li class='unavailable'>imagewebp: Not available</li>\n";
        }

        if(function_exists("imageavif")) {
            echo "<li>imageavif: ok</li>\n";
        } else {
            echo "<li class='unavailable'>imageavif: Not available</li>\n";
        }

        echo "</ul>\n";
    }

    /**
     * Delete all files in image directory
     * @return bool
     */
    public function deleteAllImages() : bool {
        $files = glob($this->imagepath . '*'); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file))
                unlink($file); // delete file
        }

        // Empty JSON-file
        file_put_contents('images.json', '[]');
        return true;
    }
}
