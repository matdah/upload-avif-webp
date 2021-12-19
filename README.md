#  Uppladdning och konvertering av bilder
Exempelkod på att ladda upp bilder med PHP, och samtidgt skapa miniatyrbilder.
Konverterar - om möjligt - till följande bildformat:
*  JPEG
*  WebP
*  AVIF (kräver PHP version > 8.1)

##  Installation
Kopiera/klona filerna till din dator. Se till att "images"-katalogen har både läs- och skriv-rättigheter.

##  UML-diagram
https://github.com/matdah/upload-avif-webp/blob/master/images/uml.jpg

```mermaid
classDiagram
class Image 
-String imagepath
-Int width_thumbnail
-Int height_thumbnail
-Int jpeg_quality
-Int web_quality
-Int avif_quality
-Int avif_speed
+uploadImage(File image) bool
+setImage(File image) bool
+showImageInfo(String filename) void
-saveToJson(String filename) void
+getImages() array
+isImageAllowed(File image) bool
+createFileName() String
+filenameAvailable(String filename) : bool
+displayInfo() void
+deleteAllImages() bool
```

##  Properties
*  imagepath - sökvägen till bilder - default satt i constructor: /images
*  width_thumbnail - maximal bredd på miniatyrer - default: 500px
*  height_thumbnail - maximal höjd på miniatyrer - default: 400px
*  jpeg_quality - kompressionsnivå för JPEG (0 - 100) - default: 80
*  webp_quality - kompressionsnivå för WebP (0 - 100) - default: 60
*  avif_quality - kompressionsnivå för AVIF (0 - 100) - default: 50
*  avif_speed - hastighet för komprimering (0 - 10 (0 långsammast)) - default: 5 

##  Metoder
*  uploadImage - ladda upp bild, tar ett fil-objekt som argument
*  setImage - laddar upp själva bilden, skapar miniatyrer samt konverterar till tillgängliga bild-format
*  showImageInfo - skriver ut information om bild
*  saveToJson - lagrar information om uppladdade bilder till JSON-fil
*  getImages - returnerar uppladdade bilder
*  isImageAllowed - kontrollerar om uppladdad bild är godkänd
*  createFilename - genererar ett unikt filnamn
*  filenameAvailable - kontrollerar om filnamn redan existerar
*  displayInfo - skriver ut information om PHP-version samt om möjlighet till komprimering finns för: JPEG, WebP, AVIF
*  deleteAllImage - raderar alla bildfiler från filsystemet och nollställer JSON-fil

###  Av
Av Mattias Dahlgren, 2021
E-post: mattias.dahlgren@miun.se