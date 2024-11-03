<?php
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use App\Models\{
    User,
    userProfile,
};
use Illuminate\Support\Facades\{
    DB,
};


function createUser($username, $email, $password, $brand)
{
    $brandName = "";
    if ($brand == "Aurora") {
        $brandName = "AHV";
    } else {
        $brandName = "E2";
    }

    $user = new User();
    $user->username = $username;
    $user->email = $email;
    $user->password = Hash::make($password);
    $user->employee_id = $brandName;
    $user->brand = $brand;
    $user->save();

    $id = DB::table('users')
        ->select('employee_id')
        ->orderBy('id', 'desc')
        ->first();
    $eid = $id->employee_id;
    return $eid;

}


function uploadImage($image, $upath = '', $prefix = '')
{
    if (!$image instanceof UploadedFile) {
        throw new \Exception('Invalid file upload.');
    }

    $path = ($upath == '') ? 'images/' : $upath;
    $storepath = Storage::disk('public')->path($path);

    if (!is_dir($storepath)) {
        File::makeDirectory($storepath, 0777, true);
    }

    // Generate a unique file name
    $imageName = time() . '-' . Str::random(5) . '.' . $image->extension();
    $imagePath = $storepath . '/' . $imageName;

    // Check the mime type and load the appropriate image creation function
    $mimeType = $image->getMimeType();

    switch ($mimeType) {
        case 'image/jpeg':
        case 'image/jpg':
            $src = imagecreatefromjpeg($image->getRealPath());
            break;
        case 'image/png':
            $src = imagecreatefrompng($image->getRealPath());
            break;
        case 'image/webp':
            $src = imagecreatefromwebp($image->getRealPath());
            break;
        default:
            throw new \Exception('Unsupported image format.');
    }

    // Get original dimensions
    list($width, $height) = getimagesize($image->getRealPath());

    // Set target width (e.g., 800px) and calculate the new height while keeping the aspect ratio
    $targetWidth = 800;
    $targetHeight = ($height / $width) * $targetWidth;

    // Create a new empty image with the target dimensions
    $tmp = imagecreatetruecolor($targetWidth, $targetHeight);

    // Preserve transparency for PNG images
    if ($mimeType == 'image/png' || $mimeType == 'image/webp') {
        imagealphablending($tmp, false);
        imagesavealpha($tmp, true);
    }

    // Copy and resize the old image into the new image
    imagecopyresampled($tmp, $src, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

    // Save the image with reduced quality (75%)
    switch ($mimeType) {
        case 'image/jpeg':
        case 'image/jpg':
            imagejpeg($tmp, $imagePath, 75); // 75% quality for JPEG
            break;
        case 'image/png':
            imagepng($tmp, $imagePath, 6); // Compression level for PNG (0 = no compression, 9 = max)
            break;
        case 'image/webp':
            imagewebp($tmp, $imagePath, 75); // 75% quality for WebP
            break;
    }

    // Free up memory
    imagedestroy($src);
    imagedestroy($tmp);

    return $path . '/' . $imageName;
}


function getImageUrl($image, $prefix = null)
{
    if ($image != null) {

        return Storage::disk('public')->url($image);
    }

    return null;
}

function getUser($id)
{
    // Find the user by ID
    $user = User::find($id);

    // Check if user exists and return the username
    return $user ? $user->username : null; // Return null if not found
}
