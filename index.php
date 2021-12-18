<?php
    include_once("includes/config.php");
    $image = new Image();
?>
<!DOCTYPE html>
<html lang="se">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image upload</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        .error { color: red; }
        section { padding: 1em; margin: 1em 0 1em 0; border: 1px solid #ccc; }
        img { width: 100%; max-width: 400px; margin: 1em;}
        .unavailable { color: red; letter-spacing: 2px; }
    </style>
</head>
<body>
    <div class="container">

        <h1>Bild-uppladdning</h1>
        
        <div>
            <?php 
                if(isset($_GET['delete'])) {
                    if($image->deleteAllImages()) {
                        echo "<p>Alla bilder raderade!</p>";
                    } else {
                        echo "<p>Bilder raderades ej!</p>";
                    }
                }
            ?>
        </div>

        <section id="uploadform">
            <h2>Ladda upp bilder</h2>
            <?php         
                if(isset($_FILES['file'])) {
                    $file = $_FILES['file'];

                    if($image->uploadImage($file))
                        echo "<p>Bilden laddades upp!</p>";
                    else
                        echo "<p>Bilden laddades ej upp!</p>";

                }
            ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                <label for="file"><strong>Bildfil (JPEG):</strong></label>
                <input type="file" name="file" id="file" />
                <input class="btn btn-primary" type="submit" value="Ladda upp" />	
            </form>
        </section>

        <section>
            <h3>Uppladdade bilder</h3>
            <?php
            $imagelist = $image->getImages();

            if($imagelist != null && count($imagelist) > 0) {
                foreach($imagelist as $filename) {
                    ?>
                    <div>
                        <picture>
                            <?php if(file_exists("images/" . $filename . ".avif")) {
                                ?>
                            <source srcset="images/<?php echo $filename; ?>.avif" type="media/avif">
                                <?php
                            } if(file_exists("images/" . $filename . ".webp")) {
                                ?>
                            <source srcset="images/<?php echo $filename; ?>.webp" type="media/webp">
                                <?php
                            }?>   
                            <img src="images/<?php echo $filename; ?>.jpg" alt="" />
                        </picture>

                        <div class="image-info">
                            <?php $image->showImageInfo($filename); ?>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>Inga bilder uppladdade ännu!</p>";
            }
            ?>
        </section>
        <section>
            <h3>Server-information</h3>
            <?php echo $image->displayInfo(); ?>
        </section>
        <section>
                <a class="btn btn-danger" href="<?php echo $_SERVER['PHP_SELF']; ?>?delete">Radera alla bilder</a>
        </section>
    </div>
</body>
</html>