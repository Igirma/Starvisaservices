<?php
    /**
	
	* Onderstaande variabelen in de model zetten:
	
	public $photos = array();
	public $photo_type = 'string (constante: CONTROLLER)';
	public $image_settings = array(
		array(
			'sub_folder' => 'thumb',
			'width' => 50,
			'height' => 50,
			'type' => 'crop'
		),
		array(
			'sub_folder' => 'max',
			'width' => 800,
			'height' => 600,
			'type' => 'resize'
		)
	);

	* Waar de afhandeling gebeurt (model van de controller):

	new Photo(
		$_FILES['Photo'], 
		$arrayimagessettings, 
		'path Photo map', 
		'phototype', 
		'table_id', 
		'filename'
	);

    **/    
        
	class photo
	{	
		public $image_name = '';
		public $image_tmp_name = '';
		public $image_type = '';
		public $image_size = '';
		public $image_error = '';
		public $main_folder = '';
		public $image_extension = '';
		public $image_original_height = '';
		public $image_original_width = '';
		public $image_source = '';
		public $image_settings = array();
        public $photo_type;
        public $tablename = 'media';
        public $idName = 'media_id';
        public $count = 0;
        public $filename;
        public $db;
		
		public function __construct(array $image, array $image_settings, $main_folder, $photo_type = '', $table_id = 0, $album_id = 0, $filename = '', $album_thumb = 0, $media_type_id = 1)
		{
			$this->db =& load_class('db', 'core');
			$this->image_name = $image['name'];
			$this->image_tmp_name = $image['tmp_name'];
			$this->image_type = $image['type'];
			$this->image_size = $image['size'];
			$this->image_error = $image['error'];
			$this->image_settings = $image_settings;
            $this->photo_type = $photo_type;
            $this->main_folder = $main_folder . $photo_type . '/';
            $this->table_id = $table_id;
			$this->album_id = $album_id;
			$this->album_thumb = $album_thumb;
			$this->min_w = 0;
			$this->min_h = 0;
			
			$this->checkExtension($this->image_type);

			$this->gen_filename($this->image_name);
			
			if(CONTROLLER == 'slider')
			{
				$this->min_w = MIN_SLIDER_WIDTH_UPLOAD;
				$this->min_h = MIN_SLIDER_HEIGHT_UPLOAD;
			}
			else
			{
				$this->min_w = MIN_WIDTH_UPLOAD;
				$this->min_h = MIN_HEIGHT_UPLOAD;
			}

			$this->insertPhoto($media_type_id);		
		}
                
        function gen_filename($name)
        {
	        for($x = 0; $x > -1; $x++)
	       	{
		        if($x == 0)
		        {
		       	 	if(!$this->is_in_dir($name))
		        	{
		        		$this->image_name = $name;
		        		return;
		        	}
		        }
		        else
		        {
		        	if(!$this->is_in_dir($x . '_' . $name))
		       		{
		        		$this->image_name = $x . '_'  . $name;
		        		return;
		        	}
		        }
	        }
        }
                
        function is_in_dir($file)
        {
        	if(file_exists($this->main_folder . 'crop_original/' . $file))
        	{
        		return true;
        	}
	        else
	        {
	        	return false;
	        }
        }
		
	
		public function insertPhoto($media_type_id)
		{
			$CI = get_instance();
			
            if (!is_dir($this->main_folder))
			{
				mkdir($this->main_folder, 0777);
			}
			
			if (!is_dir($this->main_folder.'raw'))
			{
				mkdir($this->main_folder.'raw', 0777);
			}
			
			$check = getimagesize($this->image_tmp_name);
			
			if($check[0] > $this->min_w && $check[1] > $this->min_h)
			{
				move_uploaded_file($this->image_tmp_name, $this->main_folder.'raw/'.$this->image_name); //Verplaats de afbeelding naar de map 'raw'
			}
			else
			{
				$CI->alert->add($CI->lang->line('error_file_to_small'), 'error');
				return;
			}
			
			chmod($this->main_folder.'raw/'.$this->image_name, 0777); //Geef de afbeelding alle rechten
			
			$this->image_settings[] = array(
				'sub_folder' => 'crop_original',
				'width' => USER_CROP_BASE_W,
				'height' => USER_CROP_BASE_W,
				'type' => 'resize'
			);
			
			foreach($this->image_settings as $image_setting)
			{
				$this->createPhoto($image_setting);
			}
			
			@unlink($this->main_folder.'raw/'.$this->image_name);
			
            if($this->photo_type != '')
            {
				if($this->album_id == 0)
				{
					$sql = '
						SELECT MAX(`media`.order) AS `order`
						FROM `media`
						WHERE `media`.table_id = ?
					';
					
					$order = $this->db->query($sql, array($this->table_id));
				}
				else
				{
					$sql = '
						SELECT MAX(`media`.order) AS `order`
						FROM `media`
						WHERE `media`.album_id = ?
					';
					
					$order = $this->db->query($sql, array($this->album_id));
				}
				
				//TODO exclude default language and insert default language with set options, than loop through languages
				$sql = 'SELECT `language`.language_id FROM `language`';
				
				$language = $this->db->query($sql);
				
             	$sql = '
            	INSERT INTO `media`
            	(
            		`media`.table_id,
					`media`.album_id,
            		`media`.media_type_id,
            		`media`.filename,
            		`media`.order,
            		`media`.controller,
					`media`.album_thumb
            	)
            	VALUES
            	(
            		:table_id,
					:album_id,
            		:media_type_id,
            		:filename,
            		:order,
            		:controller,
					:album_thumb
            	)
            	';
            	
            	$this->db->query($sql, array(
            		'table_id' 		=> $this->table_id,
					'album_id'		=> $this->album_id,
            		'media_type_id' => $media_type_id,
            		'filename' 		=> $this->image_name,
            		'order' 		=> $order[0]['order'] + 1,
            		'controller' 	=> $this->photo_type,
					'album_thumb'	=> $this->album_thumb
            	));
            	
            	$id = $this->db->last_insert_id;
				
				foreach($language as $lang)
				{
					$sql = '
					INSERT INTO `media_content`
					(
						`media_content`.media_id,
						`media_content`.language_id,
						`media_content`.title
					)
					VALUES
					(
						:media_id,
						:language_id,
						:title
					)
					';
            	 
					$this->db->query($sql, array(
            			'media_id' 		=> $id,
            			'language_id' 	=> $lang['language_id'],
            			'title' 		=> $this->image_name
					)); 
				}
            }
		}
		
		public function createPhoto(array $image_settings)
		{
			list($this->image_original_width, $this->image_original_height) = getimagesize($this->main_folder.'raw/'.$this->image_name);
		
			if($this->image_extension == '.jpg')
            {
                $this->image_source = imagecreatefromjpeg($this->main_folder.'raw/'.$this->image_name);	
            }
            elseif($this->image_extension == '.png')
            {
                $this->image_source = imagecreatefrompng($this->main_folder.'raw/'.$this->image_name);	
            }
            elseif($this->image_extension == '.gif')
            {
                $this->image_source = imagecreatefromgif($this->main_folder.'raw/'.$this->image_name);	
            }
            else
            {
                // extentie fout    
            }
            
            // landscape of portrait origineel
            if($this->image_original_width < $this->image_original_height)
            {
				$original_orientation = 'p'; 
			}
			elseif($this->image_original_width > $this->image_original_height)
			{
				$original_orientation = 'l';
			}
            else
            {
                // vierkant
                $original_orientation = 'v';
            }
            // landscape of portrait resultaat
            if($image_settings['width'] < $image_settings['height'])
            {
				$result_orientation = 'p'; 
			}
			elseif($image_settings['width'] > $image_settings['height'])
			{
				$result_orientation = 'l';
			}
            else
            {
                // vierkant
                $result_orientation = 'v';
            }
            
            if($image_settings['type'] == 'crop')
            {
                $ratio = $this->getAspectratio($image_settings['width'], $image_settings['height']);
                $ratio_image =  $this->getAspectratio($this->image_original_width, $this->image_original_height);
                
                if(
                    /** fout **/ //($ratio_image > $ratio && $original_orientation == 'p' && $result_orientation == 'l') ||
                    
                    //($ratio_image < $ratio && $original_orientation == 'p' && $result_orientation == 'l') ||
                    /** org. or. omgewisseld **/($ratio_image < $ratio && $original_orientation == 'l' && $result_orientation == 'v') ||
                    
                    /** ratio omgewisseld **/ ($ratio_image < $ratio && $original_orientation == 'l' && $result_orientation == 'l') ||
  
                    /** org. or. omgewisseld **/($ratio_image < $ratio && $original_orientation == 'l' && $result_orientation == 'p') ||
                                        
                    /** res. or. omgewisseld **/($ratio_image == 1 /*betekend dat hij vierkant is*/ && $result_orientation == 'p') ||
                    //($ratio_image == $ratio && $original_orientation == 'p' && $result_orientation == 'l') ||
                    ($ratio_image == $ratio && $original_orientation == 'l' && $result_orientation == 'l') ||
                    ($image_settings['width'] == 0)
                )
                {
                    //Nieuwe foto moet portrait of vierkant worden
                    $new_height = $image_settings['height'];
    				$new_width = $this->image_original_width / ($this->image_original_height / $image_settings['height']);
                    
                    if($new_width < $image_settings['width'] || $image_settings['width'] == 0)
                    {
                        $image_settings['width'] = $new_width;
                    }
                    
                    $pos_x = ($new_width - $image_settings['width']) / 2;
    				$pos_y = 0;
                }
                else
                {                        
                    //Nieuwe foto moet landscape worden
                    $new_width = $image_settings['width'];
    				$new_height = $this->image_original_height / ($this->image_original_width / $image_settings['width']);
                    
                    if($new_height < $image_settings['height'] || $image_settings['height'] == 0)
                    {
                        $image_settings['height'] = $new_height;
                    }
                    
                    $pos_y = ($new_height - $image_settings['height']) / 2;
    				$pos_x = 0;
                }  
            }
            elseif($image_settings['type'] == 'resize')
            {
				$ratio = $this->getAspectratio($image_settings['width'], $image_settings['height']);
                $ratio_image =  $this->getAspectratio($this->image_original_width, $this->image_original_height);
                
                if($this->image_original_width <= $image_settings['width'] && $this->image_original_height <= $image_settings['height'])
                {
                    $new_height = $this->image_original_height;
                    $new_width = $this->image_original_width;
                }
                else
                {
                    if(
                        ($ratio_image > $ratio && $original_orientation == 'p' && $result_orientation == 'l' && $ratio > 0) ||
                        ($ratio_image < $ratio && $original_orientation == 'p' && $result_orientation == 'l') ||
                        ($ratio_image < $ratio && $original_orientation == 'p' && $result_orientation == 'v') ||
                        
                        //($ratio_image > $ratio && $original_orientation == 'l' && $result_orientation == 'l') ||
                        /** deze vervangt bovenstaande regel **/
                        ($ratio_image > $ratio && $image_settings['height'] != 0 && $original_orientation == 'l' && $result_orientation == 'l') ||
                        
                        ($ratio_image < $ratio && $original_orientation == 'p' && $result_orientation == 'p') ||
                        ($ratio_image == 1 /*betekend dat hij vierkant is*/ && $result_orientation == 'l') ||
                        ($ratio_image == $ratio && $original_orientation == 'p' && $result_orientation == 'l') ||
                        ($ratio_image == $ratio && $original_orientation == 'l' && $result_orientation == 'l') ||
                        ($image_settings['width'] == 0)
                    )
                    {
                        /**
                            Zie het excel bestand met de uitwerking van deze if, else
                        **/
                        $new_height = $image_settings['height'];
                        $new_width = $this->image_original_width / ($this->image_original_height / $image_settings['height']);
                    }
                    else
                    {                        
                        $new_width = $image_settings['width'];
    				    $new_height = $this->image_original_height / ($this->image_original_width / $image_settings['width']);
                    }
                }   
                
                $pos_y = 0;
                $pos_x = 0;
            }
            
			$new_image = imagecreatetruecolor($new_width, $new_height);
			
			if($this->image_extension == '.png')
			{
				imagealphablending($new_image, false);
				$colorTransparent = imagecolorallocatealpha($new_image, 255, 255, 255, 0);
				imagefill($new_image, 0, 0, $new_image);
				imagesavealpha($new_image, true);
			}
			imagecopyresampled($new_image, $this->image_source, 0, 0, 0, 0, $new_width, $new_height, $this->image_original_width, $this->image_original_height);
			
			if($image_settings['type'] == 'crop')
			{
				$new_image_2 = imagecreatetruecolor($image_settings['width'], $image_settings['height']);
				if($this->image_extension == '.png')
				{
					imagealphablending($new_image_2, false);
					$colorTransparent = imagecolorallocatealpha($new_image_2, 255, 255, 255, 0);
					imagefill($new_image_2, 0, 0, $new_image_2);
					imagesavealpha($new_image_2, true);
				}
				imagecopy($new_image_2, $new_image, 0, 0, $pos_x, $pos_y, $new_width, $new_height);
			}
			else
			{
				$new_image_2 = $new_image;
			}
			
			if(!is_dir($this->main_folder.'/'.$image_settings['sub_folder']))
			{
				mkdir($this->main_folder.'/'.$image_settings['sub_folder'], 0777);
			}
			
			if($this->image_extension == '.jpg')
            {
                imagejpeg($new_image_2, $this->main_folder.'/'.$image_settings['sub_folder'].'/'.$this->image_name, 100);	
            }
            elseif($this->image_extension == '.png')
            {
                imagepng($new_image_2, $this->main_folder.'/'.$image_settings['sub_folder'].'/'.$this->image_name, 9);
            }
            elseif($this->image_extension == '.gif')
            {
                imagegif($new_image_2, $this->main_folder.'/'.$image_settings['sub_folder'].'/'.$this->image_name);
            }
            else
            {
                // extentie fout    
            }
			
			chmod($this->main_folder.'/'.$image_settings['sub_folder'].'/'.$this->image_name, 0777);								
		}
        
		public function checkExtension($extension)
		{
			if ($extension == 'image/jpeg' || $extension == 'image/pjpeg')
			{
				$this->image_extension = '.jpg';
			}
			elseif($extension == 'image/gif')
			{
				$this->image_extension = '.gif';
			}
			elseif($extension == 'image/png')
			{
				$this->image_extension = '.png';
			}
			else
			{
				// TODO: alert
				echo 'Extensie is niet juist: '. $this->image_extension;
			}			
		}
        
        public function getAspectratio($height, $width)
        {
            if($width <= $height)
            {
                $ratio = $width / $height;
            }
            else
            {
                $ratio = $height / $width;
            }
            
            return $ratio;
        }	
	}
	
class crop
{
	// *** Class variables
	private $image;
	private $file;
	private $image_dir;
	private $width;
	private $height;
	private $imageResized;

	function __construct($fileName, $image_dir)
	{
		// *** Open up the file
		$fileName = BASE_PATH . ELEM_DIR . 'media/' . CONTROLLER . '/crop_original/' . $fileName;
		$this->image_dir = $image_dir;
		$this->file = $fileName;
		$this->image = $this->openImage($fileName);

		// *** Get width and height
		$this->width  = imagesx($this->image);
		$this->height = imagesy($this->image);
	}

	## --------------------------------------------------------

	private function openImage($file)
	{
		// *** Get extension
		$extension = strtolower(strrchr($file, '.'));

		switch($extension)
		{
			case '.jpg':
			case '.jpeg':
				$img = @imagecreatefromjpeg($file);
				break;
			case '.gif':
				$img = @imagecreatefromgif($file);
				break;
			case '.png':
				$img = @imagecreatefrompng($file);
				break;
			default:
				$img = false;
				break;
		}
		
		return $img;
	}

	## --------------------------------------------------------

	public function resizeImage($newWidth, $newHeight, $x, $y)
	{		
		$this->crop($newWidth, $newHeight, $newWidth, $newHeight, $x, $y);
		
		// *** Resample - create image canvas of x, y size
		$this->imageResized = $this->image;

		$this->imageResized = imagecreatetruecolor($newWidth, $newHeight);
		
		$extension = strtolower(strrchr($this->file, '.'));
		if($extension == '.png')
		{
			imagealphablending($this->imageResized, false);
			$colorTransparent = imagecolorallocatealpha($this->imageResized, 255, 255, 255, 0);
			imagefill($this->imageResized, 0, 0, 0);
			imagesavealpha($this->imageResized, true);
		}
		
		imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $_POST['w'], $_POST['h']);
	}

	## --------------------------------------------------------

	private function getSizeByFixedHeight($newHeight)
	{
		$ratio = $this->width / $this->height;
		$newWidth = $newHeight * $ratio;
		return $newWidth;
	}

	private function getSizeByFixedWidth($newWidth)
	{
		$ratio = $this->height / $this->width;
		$newHeight = $newWidth * $ratio;
		return $newHeight;
	}

	private function getSizeByAuto($newWidth, $newHeight)
	{
		if ($this->height < $this->width)
		// *** Image to be resized is wider (landscape)
		{
			$optimalWidth = $newWidth;
			$optimalHeight= $this->getSizeByFixedWidth($newWidth);
		}
		elseif ($this->height > $this->width)
		// *** Image to be resized is taller (portrait)
		{
			$optimalWidth = $this->getSizeByFixedHeight($newHeight);
			$optimalHeight= $newHeight;
		}
		else
		// *** Image to be resizerd is a square
		{
			if ($newHeight < $newWidth) {
				$optimalWidth = $newWidth;
				$optimalHeight= $this->getSizeByFixedWidth($newWidth);
			} else if ($newHeight > $newWidth) {
				$optimalWidth = $this->getSizeByFixedHeight($newHeight);
				$optimalHeight= $newHeight;
			} else {
				// *** Sqaure being resized to a square
				$optimalWidth = $newWidth;
				$optimalHeight= $newHeight;
			}
		}

		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}

	## --------------------------------------------------------

	private function getOptimalCrop($newWidth, $newHeight)
	{

		$heightRatio = $this->height / $newHeight;
		$widthRatio  = $this->width /  $newWidth;

		if ($heightRatio < $widthRatio) {
			$optimalRatio = $heightRatio;
		} else {
			$optimalRatio = $widthRatio;
		}

		$optimalHeight = $this->height / $optimalRatio;
		$optimalWidth  = $this->width  / $optimalRatio;

		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}

	## --------------------------------------------------------

	private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight, $x, $y)
	{
		$cropStartX = $x;
		$cropStartY = $y;

		$crop = $this->image;

		// *** Now crop from center to exact requested size
		$this->image = imagecreatetruecolor($_POST['w'] , $_POST['h']);
		
		$extension = strtolower(strrchr($this->file, '.'));
		
		if($extension == '.png')
		{
			imagealphablending($this->image, false);
			$colorTransparent = imagecolorallocatealpha($this->image, 255, 255, 255, 0);
			imagefill($this->image, 0, 0, 0);
			imagesavealpha($this->image, true);
		}
		
		
		imagecopyresampled($this->image, $crop , 0, 0, $cropStartX, $cropStartY, $_POST['w'], $_POST['h'] , $_POST['w'], $_POST['h']);
	}

	## --------------------------------------------------------

	public function saveImage($savePath, $imageQuality="100")
	{
		// *** Get extension
		$extension = strrchr($savePath, '.');
		$extension = strtolower($extension);
		
		$savePath = BASE_PATH . ELEM_DIR . 'media/' . CONTROLLER . '/' . $this->image_dir . '/' . $savePath;

		switch($extension)
		{
			case '.jpg':
			case '.jpeg':
				if (imagetypes() & IMG_JPG) {
					imagejpeg($this->imageResized, $savePath, $imageQuality);
				}
				break;

			case '.gif':
				if (imagetypes() & IMG_GIF) {
					imagegif($this->imageResized, $savePath);
				}
				break;

			case '.png':
				// *** Scale quality from 0-100 to 0-9
				$scaleQuality = round(($imageQuality/100) * 9);

				// *** Invert quality setting as 0 is best, not 9
				$invertScaleQuality = 9 - $scaleQuality;

				//if (imagetypes() & IMG_PNG) {
				imagepng($this->imageResized, $savePath, $invertScaleQuality);
				//}
				break;

			// ... etc

			default:
				// *** No extension - No save.
				break;
		}

		imagedestroy($this->imageResized);
	}
	## --------------------------------------------------------
}
?>