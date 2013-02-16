<?php

## Function to set file permissions to 0644 and folder permissions to 0755

function AllDirChmod( $dir = "./", $dirModes = 0755, $fileModes = 0644 ){
   $d = new RecursiveDirectoryIterator( $dir );
   foreach( new RecursiveIteratorIterator( $d, 1 ) as $path ){
      if( $path->isDir() ) chmod( $path, $dirModes );
      else if( is_file( $path ) ) chmod( $path, $fileModes );
  }
}

## Function to clean out the contents of specified directory

function cleandir($dir) {

    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..' && is_file($dir.'/'.$file)) {
                if (unlink($dir.'/'.$file)) { }
                else { echo $dir . '/' . $file . ' (file) NOT deleted!<br />'; }
            }
            else if ($file != '.' && $file != '..' && is_dir($dir.'/'.$file)) {
                cleandir($dir.'/'.$file);
                if (rmdir($dir.'/'.$file)) { }
                else { echo $dir . '/' . $file . ' (directory) NOT deleted!<br />'; }
            }
        }
        closedir($handle);
    }

}

function isDirEmpty($dir){
     return (($files = @scandir($dir)) && count($files) <= 2);
}

echo "----------------------- CLEANUP START -------------------------<br/>";
$start = (float) array_sum(explode(' ',microtime()));
echo "<br/>*************** SETTING PERMISSIONS ***************<br/>";
echo "Setting all folder permissions to 755<br/>";
echo "Setting all file permissions to 644<br/>";
AllDirChmod( "." );
echo "Setting pear permissions to 550<br/>";
chmod("pear", 550);

echo "<br/>****************** CLEARING CACHE ******************<br/>";

if (file_exists("var/cache")) {
    echo "Clearing var/cache<br/>";
    cleandir("var/cache");
}

if (file_exists("var/session")) {
    echo "Clearing var/session<br/>";
    cleandir("var/session");
}

if (file_exists("var/minifycache")) {
    echo "Clearing var/minifycache<br/>";
    cleandir("var/minifycache");
}

if (file_exists("downloader/pearlib/cache")) {
    echo "Clearing downloader/pearlib/cache<br/>";
    cleandir("downloader/pearlib/cache");
}

if (file_exists("downloader/pearlib/download")) {
    echo "Clearing downloader/pearlib/download<br/>";
    cleandir("downloader/pearlib/download");
}

if (file_exists("downloader/pearlib/pear.ini")) {
    echo "Removing downloader/pearlib/pear.ini<br/>";
    unlink ("downloader/pearlib/pear.ini");
}

echo "<br/>************** CHECKING FOR EXTENSIONS ***********<br/>";
If (!isDirEmpty("app/code/local/")) { 
    echo "-= WARNING =- Overrides or extensions exist in the app/code/local folder<br/>";
}
If (!isDirEmpty("app/code/community/")) { 
    echo "-= WARNING =- Overrides or extensions exist in the app/code/community folder<br/>";
}
$end = (float) array_sum(explode(' ',microtime()));
echo "<br/>------------------- CLEANUP COMPLETED in:". sprintf("%.4f", ($end-$start))." seconds ------------------<br/>";
?>
