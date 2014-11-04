<?php
# =================================================================== #
#
# iMarc PHP Library
# Copyright 1999, 2002 David Fox, Angryrobot Productions 
#                (See below for full license)
# 
# VERSION: 2.1
# LAST UPDATE: 2002-06-04
# CONTENT: PHP file upload class
#
# =================================================================== #
# 
# USAGE and SETUP instructions at the bottom of this page (README)
# 
# =================================================================== #
/*
	METHODS:
		max_filesize() 		- set a max filesize in bytes
		max_image_size() 	- set max pixel dimenstions for image uploads
		upload() 			- checks if file is acceptable, uploads file to server's temp directory
		save_file() 		- moves the uploaded file and renames it depending on the save_file($overwrite_mode)
		
		
		cleanup_text_file()	- (private class function) convert Mac and/or PC line breaks to UNIX
	Error codes:
		$errors[0] - "No file was uploaded"
		$errors[1] - "Maximum file size exceeded"
		$errors[2] - "Maximum image size exceeded"
		$errors[3] - "Only specified file type may be uploaded"
		$errors[4] - "File already exists" (save only)
*/
# ------------------------------------------------------------------- #
# UPLOADER CLASS
# ------------------------------------------------------------------- #

class uploader {
	var $file;
	var $errors;
	var $accepted;
	var $max_filesize;
	var $max_image_width;
	var $max_image_height;
	# ----------------------------------- #
	# FUNCTION: 	max_filesize 
	# DESCRIPTION: 	Set the maximum file size in bytes ($size), allowable by the object.
	#
	# ARGS: 		$size			(int) file size in bytes
	#
	# NOTE: PHP's configuration file also can control the maximum upload size, which is set to 2 or 4 
	# megs by default. To upload larger files, you'll have to change the php.ini file first.
	# ----------------------------------- #
	function max_filesize($size) {
		$this->max_filesize = $size;
	}
	# ----------------------------------- #
	# FUNCTION: 	max_image_size 
	# DESCRIPTION: 	Sets the maximum pixel dimensions for image uploads
	#
	# ARGS: 		$width 			(int) maximum pixel width of image uploads
	#				$height			(int) maximum pixel height of image uploads
	# ----------------------------------- #
	function max_image_size($width, $height) {
		$this->max_image_width = $width;
		$this->max_image_height = $height;
	}
	# ----------------------------------- #
	# FUNCTION: 	upload 
	# DESCRIPTION: 	Checks if the file is acceptable and copies it to 
	# 
	# ARGS: 		$filename		(string) form field name of uploaded file
	#				$accept_type	(string) acceptable mime-types
	#				$extension		(string) default filename extenstion
	# ----------------------------------- #
	function upload($filename = '', $accept_type = '', $extention = '') {
		if (!$filename || $filename == "none") {
			$this->errors[0] = "No file was uploaded";
			$this->accepted = false;
			return false;
		}

		// Copy PHP's global $_FILES array to a local array
		$this->file = $_FILES[$filename];				//$this->file['file'] = $filename;				if (!$this->file) {			$this->file = $_POST[$filename];					} 						AddError(0,"Info :tmp is " . $this->file['tmp_name']);		if (!$this->file) {
			AddError(__FILE__ . __LINE__ , "W: no llega el fichero!");
		} else
			AddError(__FILE__ . __LINE__ , "Info: algo llega");

		// test max size
		if ($this->max_filesize && ($this->file["size"] > $this->max_filesize)) {			AddError(__FILE__ . __LINE__ , "Info: tamaño no permitido");
			$this->errors[1] = "Maximum file size exceeded. File may be no larger than ".($this->max_filesize / 1000)."KB (".$this->max_filesize." bytes).";
			$this->accepted = false;
			return false;
		}				

		if (preg_match("/image/", $this->file["type"])) {

			/* IMAGES */

			$image = getimagesize($this->file["tmp_name"]);
			$this->file["width"] = $image[0];
			$this->file["height"] = $image[1];

			// test max image size
			if (($this->max_image_width || $this->max_image_height) && (($this->file["width"] > $this->max_image_width) || ($this->file["height"] > $this->max_image_height))) {
				$this->errors[2] = "Maximum image size exceeded. Image may be no more than ".$this->max_image_width." x ".$this->max_image_height." pixels";
				$this->accepted = false;
								AddError(__FILE__ . __LINE__ , "W: tamaño de imagen ilegal");
				return false;
			}
			// Image Type is returned from getimagesize() function
			switch ($image[2]) {
				case 1 :
					$this->file["extention"] = ".gif";
					break;
				case 2 :
					$this->file["extention"] = ".jpg";
					break;
				case 3 :
					$this->file["extention"] = ".png";
					break;
				case 4 :
					$this->file["extention"] = ".swf";
					break;
				case 5 :
					$this->file["extention"] = ".psd";
					break;
				case 6 :
					$this->file["extention"] = ".bmp";
					break;
				case 7 :
					$this->file["extention"] = ".tif";
					break;
				case 8 :
					$this->file["extention"] = ".tif";
					break;
				default :
					$this->file["extention"] = $extention;
					break;
			}
		}
		elseif (!preg_match("/(\.)([a-z0-9]{3,5})$/", $this->file["name"]) && !$extention) {
			// Try and autmatically figure out the file type
			// For more on mime-types: http://httpd.apache.org/docs/mod/mod_mime_magic.html
			switch ($this->file["type"]) {
				case "text/plain" :
					$this->file["extention"] = ".txt";
					break;
				case "text/richtext" :
					$this->file["extention"] = ".txt";
					break;
				default :
					break;
			}
		} else {
			$this->file["extention"] = $extention;
		}

		  //AddError(__FILE__ . __LINE__ , "Info: extension: '" . $this->file["extention"] . "' , file: '". $this->file['file']."'");

		// check to see if the file is of type specified
		if ($accept_type) {
			if (preg_match(strtolower($accept_type), strtolower($this->file["type"]))) {
				$this->accepted = TRUE;
			} else {
				$this->accepted = FALSE;
				$this->errors[3] = "Only ".preg_replace("/\|/i", " or ", $accept_type)." files may be uploaded";
			}
		} else {
			$this->accepted = TRUE;
		}
		
		  //AddError(__FILE__ . __LINE__ , "Info: raw_name: '" . $this->file["raw_name"] . "' , filename: '". $this->file['filename']."'");		
		return $this->accepted;
	}
	# ----------------------------------- #
	# FUNCTION: 	save_file 
	# DESCRIPTION: 	Cleans up the filename, copies the file from PHP's temp location to $path, 
	#				and checks the overwrite_mode
	#
	# ARGS:			$path			(string) File path to your upload directory
	#				$overwrite_mode	(int) 	1 = overwrite existing file
	#										2 = rename if filename already exists (file.txt becomes file_copy0.txt)
	#										3 = do nothing if a file exists
	# ----------------------------------- #
	function save_file($path, $overwrite_mode = "3") {
		$this->path = $path;

		if ($this->accepted) {

			AddError(__FILE__.__LINE__, "Info: Salva file en '$path', con name '".$this->file["name"])."'";

			// Clean up file name (only lowercase letters, numbers and underscores)
			$this->file["name"] = preg_replace("/[^a-z0-9._]/i", "", str_replace(" ", "_", str_replace("%20", "_", strtolower($this->file["name"]))));

			// Clean up text file breaks
			if (preg_match("/text/", $this->file["type"])) {
				$this->cleanup_text_file($this->file["tmp_name"]);
			}

			// get the raw name of the file (without it's extenstion)
			if (preg_match("/(\.)([a-z0-9]{2,5})$/", $this->file["name"])) {
				$pos = strrpos($this->file["name"], ".");
				if (!$this->file["extention"]) {
					$this->file["extention"] = substr($this->file["name"], $pos, strlen($this->file["name"]));
				}
				$this->file['raw_name'] = substr($this->file["name"], 0, $pos);
			} else {
				$this->file['raw_name'] = $this->file["name"];
				if ($this->file["extention"]) {
					$this->file["name"] = $this->file["name"].$this->file["extention"];
				}
			}


			# Terminan en el directorio del nuke, mas el dir especificado en path
			$log = "Status: path: ".$this->path." - name: ".$this->file["name"]." - tmp:".$this->file["tmp_name"]." - raw:".$this->file["raw_name"];
			AddError(__FILE__.__LINE__, "Info: con $log");

			$copy = '';  
			$n    = 0;
			switch ($overwrite_mode) {
				case 1 : // overwrite mode
					$aok = copy($this->file["tmp_name"], $this->path.$this->file["name"]);
					break;
				case 2 : // create new with incremental extention
					while (file_exists($this->path.$this->file['raw_name'].$copy.$this->file["extention"])) {
						$copy = "_copy".$n;
						$n ++;
					}
					$this->file["name"] = $this->file['raw_name'].$copy.$this->file["extention"];
                    if($this->file["tmp_name"]==""){
                        return;
                    }
					$aok = copy($this->file["tmp_name"], $this->path.$this->file["name"]);
					break;
				case 3 : // do nothing if exists, highest protection
					if (file_exists($this->path.$this->file["name"])) {
						$this->errors[4] = "File &quot".$this->path.$this->file["name"]."&quot already exists";
						$aok = null;
					} else {
						$aok = copy($this->file["tmp_name"], $this->path.$this->file["name"]);
					}
					break;
				default :
					break;
			}

			# Terminan en el directorio del nuke, mas el dir especificado en path
			AddError(__FILE__.__LINE__, "Info-- path: ".$this->path." - name: ".$this->file["name"]." - tmp:".$this->file["tmp_name"]." - raw:".$this->file["raw_name"]);

			if (!$aok) {
				unset ($this->file['tmp_name']);
			}
			return $aok;
		} else {

			$this->errors[3] = "Only ".preg_replace("/\|/i", " or ", $accept_type)." files may be uploaded";
			return FALSE;
		}
	}

	# ----------------------------------- #
	# FUNCTION: 	cleanup_text_file 
	# DESCRIPTION: 	Convert Mac and/or PC line breaks to UNIX
	#
	# ARGS: 		$file	(string) Path and name of text file
	# ----------------------------------- #
	function cleanup_text_file($file) {
		// chr(13)  = CR (carridge return) = Macintosh
		// chr(10)  = LF (line feed)       = Unix
		// Win line break = CRLF
		$new_file = '';
		$old_file = '';
		$fcontents = file($file);
		while (list ($line_num, $line) = each($fcontents)) {
			$old_file .= $line;
			$new_file .= str_replace(chr(13), chr(10), $line);
		}
		if ($old_file != $new_file) {
			// Open the uploaded file, and re-write it with the new changes
			$fp = fopen($file, "w");
			fwrite($fp, $new_file);
			fclose($fp);
		}
	}
}
/*
<readme>
	fileupload.class can be used to upload files of any type
	to a web server using a web browser. The uploaded file's name will 
	get cleaned up - special characters will be deleted, and spaces 
	get replaced with underscores, and moved to a specified 
	directory (on your server). fileupload.class also does its best to 
	determine the file's type (text, GIF, JPEG, etc). If the user 
	has named the file with the correct extension (.txt, .gif, etc), 
	then the class will use that, but if the user tries to upload 
	an extensionless file, PHP does can identify text, gif, jpeg, 
	and png files for you. As a last resort, if there is no 
	specified extension, and PHP can not determine the type, you 
	can set a default extension to be added.
	
	SETUP:
		Make sure that the directory that you plan on uploading 
		files to has enough permissions for your web server to 
		write/upload to it. (usually, this means making it world writable)
			- cd /your/web/dir
			- chmod 777 <fileupload_dir>
		
		The HTML FORM used to upload the file should look like this:
		<form method="post" action="upload.php" enctype="multipart/form-data">
			<input type="file" name="userfile"> 
			<input type="submit" value="Submit">
		</form>
	USAGE:
		// Create a new instance of the class
		$my_uploader = new uploader;
		
		// OPTIONAL: set the max filesize of uploadable files in bytes
		$my_uploader->max_filesize(90000);
		// OPTIONAL: if you're uploading images, you can set the max pixel dimensions 
		$my_uploader->max_image_size(150, 300); // max_image_size($width, $height)
		
		// UPLOAD the file
		// $my_uploader->upload($upload_file_name, $acceptable_file_types, $default_extension)
		$success = $my_uploader->upload("userfile", "", ".jpg");
		
		if ($success) {
			// MOVE THE FILE to it's final destination
			
			//	$overwrite_mode = 1 ::	overwrite existing file
			//	$overwrite_mode = 2 ::	rename new file if a file
			//	             			with the same name already 
			//              			exists: file.txt becomes file_copy0.txt
			//	$overwrite_mode = 3 ::	do nothing if a file with the
			//	             			same name already exists
			$success = $my_uploader->save_file("/your/web/dir/fileupload_dir", int $overwrite_mode);
		}
		
		if ($success) {
			// Successful upload!
			$file_name = $my_uploader->file['name'];
			print($file_name . " was successfully uploaded!");
		} else {
			// ERROR uploading...
 			if($my_uploader->errors) {
				while(list($key, $var) = each($my_uploader->errors)){
				echo $var . "<br>";
			}
 		}
</readme>
<license>
	///// fileupload.class /////
	Copyright (c) 1999, 2002 David Fox, Angryrobot Productions
	(http://www.angryrobot.com) All rights reserved.
	
	Redistribution and use in source and binary forms, with or without 
	modification, are permitted provided that the following conditions 
	are met:
	1. Redistributions of source code must retain the above copyright 
	   notice, this list of conditions and the following disclaimer.
	2. Redistributions in binary form must reproduce the above 
	   copyright notice, this list of conditions and the following 
	   disclaimer in the documentation and/or other materials provided 
	   with the distribution.
	3. Neither the name of author nor the names of its contributors 
	   may be used to endorse or promote products derived from this 
	   software without specific prior written permission.
	DISCLAIMER:
	THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS "AS IS" 
	AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED 
	TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A 
	PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE AUTHOR OR 
	CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, 
	SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT 
	LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF 
	USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
	AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
	LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING 
	IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF 
	THE POSSIBILITY OF SUCH DAMAGE.
</license>
*/
?>
