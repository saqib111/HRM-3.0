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

function getUserPermissions($user)
{
    if ($user->role == 1) {
        return []; // Superadmin has unrestricted access
    }

    $permissions = \DB::table('user_permissions')
        ->where('user_id', $user->id)
        ->value('permissions');

    return $permissions ? explode(',', $permissions) : [];
}

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

function getName($id)
{
    $username = User::select('username', 'id')->where('id', $id)->first();
    return [$username->username, $username->id];
}

function getUserName($id)
{
    $username = User::select('username', 'id')->where('id', $id)->first();
    return $username->username;
}


function dateSelect($start, $end)
{
    $starray = (explode(" - ", $start));
    $stnew = array_chunk($starray, 2);
    $stfirst = date('Y-m-d H:i A', strtotime($stnew[0][0]));
    $stfirstSplit = (explode(" ", $stfirst));

    $startDate = date('Y-m-d', strtotime($stfirstSplit[0]));
    $startTime = date('H:i A', strtotime($stfirstSplit[1]));
    $startShift = [$startDate, $startTime];

    $endarray = (explode(" - ", $end));
    $endnew = array_chunk($endarray, 2);
    $endfirst = date('Y-m-d H:i A', strtotime($endnew[0][0]));
    $endfirstSplit = (explode(" ", $endfirst));

    $endsecond = date('Y-m-d H:i A', strtotime($endnew[0][1]));
    $endSecondSplit = (explode(" ", $endsecond));
    $endDate = date('Y-m-d', strtotime($endSecondSplit[0]));
    $endTime = date('H:i A', strtotime($endfirstSplit[1]));
    $startShift = [$startDate, $startTime];
    $endShift = [$endDate, $endTime];

    return [$startShift, $endShift];
}

function scheduleInfo($id)
{
    $records = DB::table('schedules')
        ->select(DB::raw('DATE(created_at) as date'))
        ->where('user_id', $id)
        ->where('status', '1')
        ->groupBy(DB::raw('DATE(created_at)'))

        ->latest()
        ->get();

    $schedule = DB::table('schedules')
        ->select('*')
        ->where('user_id', $id)

        ->where('status', '1')
        ->where(DB::raw('DATE(created_at)'), date($records[0]->date))
        ->get();
    return ([$records, $schedule]);
}

function getColorByLeaveType($leaveType)
{
    switch ($leaveType) {
        case '1':
            return '1';
        case '2':
            return '2';
        case '3':
            return '3';
        case '4':
            return '4';
        case '5':
            return '5';
        case '6':
            return '6';
        case '7':
            return '7';
        case '8':
            return '8';
        default:
            return ""; // Default color
    }
}
function calculateDeduction($difference, $week_day)
{
    $deduction = 0;

    if ($week_day == "6") { // Saturday
        $deduction += ($difference <= 15) ? 0.125 : min(4, ceil(($difference - 15) / 15) * 0.125);
    } elseif ($week_day == "5") { // Friday
        $deduction += ($difference <= 15) ? 0.25 : min(4.5, ceil(($difference - 15) / 15) * 0.25);
    }

    return $deduction;
}