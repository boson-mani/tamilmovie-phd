<?php
use Illuminate\Support\Facades\URL;

class Movie extends Eloquent {

    protected $table = 'movie';

    function randomPrefix() 
	{ 	
		$result = '';
		 $valLength =5;
		$moduleLength = 40;   // we use sha1, so module is 40 chars
		$steps = round(($valLength/$moduleLength) + 0.5);

		for( $i=0; $i<$steps; $i++ ) {
			$result .= sha1( uniqid() . md5( rand() . uniqid() ) );
		}

		return substr( $result, 0, $valLength );
		
	}
	function resizeImage($filename, $max_width =400, $max_height =400)
	{
		list($orig_width, $orig_height) = getimagesize($filename);

		$width = $orig_width;
		$height = $orig_height;

		# taller
		if ($height > $max_height) {
			$width = ($max_height / $height) * $width;
			$height = $max_height;
		}

		# wider
		if ($width > $max_width) {
			$height = ($max_width / $width) * $height;
			$width = $max_width;
		}

		$thumb = imagecreatetruecolor($width, $height);
		imagesavealpha($thumb, true);
		$color = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
		imagefill($thumb, 0, 0, $color);
		if(preg_match("/.jpg/i", $filename)){
		$source = imagecreatefromjpeg($filename);
		}
		if(preg_match("/.jpeg/i", $filename)){
		$source = imagecreatefromjpeg($filename);
		}
		if(preg_match("/.jpeg/i", $filename)){
		$source = Imagecreatefromjpeg($filename);
		}
		if(preg_match("/.png/i", $filename)){
		$source = imagecreatefrompng($filename);
		}
		if(preg_match("/.gif/i", $filename)){
		$source = imagecreatefromgif($filename);
		}
		
		//$image = imagecreatefromjpeg($filename);

		imagecopyresampled($thumb, $source, 0, 0, 0, 0, 
										 $width, $height, $orig_width, $orig_height);
		if(preg_match("/.jpg/i", $filename)){
		return imagejpeg($thumb,$filename);
		}
		if(preg_match("/.jpeg/i", $filename)){
		return imagejpeg($thumb,$filename);
		}
		if(preg_match("/.jpeg/i", $filename)){
		return imagejpeg($thumb,$filename);
		}
		if(preg_match("/.png/i", $filename)){
		return imagepng($thumb,$filename);
		}
		if(preg_match("/.gif/i", $filename)){
		return imagegif($thumb,$filename);
		}
		//imagejpeg($thumb,$filename);
	}
	public function url()
	{
		return Url::to('movie/'.$this->id.'/view');
	}
	public function author()
	{
		return $this->belongsTo('User', 'user_id');
	}
	public function content()
	{
		return nl2br($this->review);
	}
	 public function date($date=null)
    {
        if(is_null($date)) {
            $date = $this->created_at;
        }

        return String::date($date);
    }
    public function created_at()
	{
		return $this->date($this->created_at);
	}
	public function updated_at()
	{
        return $this->date($this->updated_at);
	}
}