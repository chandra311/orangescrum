<?php

App::import('Vendor', 's3', array('file' => 's3' . DS . 'S3.php'));

use RRule\RRule;

class FormatComponent extends Component {

    public $components = array('Session', 'Email', 'Cookie', 'Postcase', 'Tmzone');

    function checkMems($project, $type) {
        if ($type == "uniq_id") {
            $cond = array('Project.uniq_id' => $project, 'ProjectUser.user_id' => SES_ID, 'Project.isactive' => 1, 'Project.company_id' => SES_COMP);
        } else {
            $cond = array('Project.id' => $project, 'ProjectUser.user_id' => SES_ID, 'Project.isactive' => 1, 'Project.company_id' => SES_COMP);
        }
        $ProjectUser = ClassRegistry::init('ProjectUser');
        $ProjectUser->unbindModel(array('belongsTo' => array('User')));
        $checkMem = $ProjectUser->find('count', array('conditions' => $cond, 'fields' => 'DISTINCT Project.id'));
        return $checkMem;
    }

    function generateUniqNumber() {
        $uniq = uniqid(rand());
        return md5($uniq . time());
    }

    function showlink($value) {
        $value = str_replace("a href=", "a style='text-decoration:underline;color:#066D99' target='_blank' href=", $value);
        $value = preg_replace("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", '<a href="http://\\0"target="_blank">\\0</a>', $value);
        if (stristr($value, "http://http://")) {
            $value = str_replace("http://http://", "http://", $value);
        }
        if (stristr($value, "http://http//")) {
            $value = str_replace("http://http//", "http://", $value);
        }
        if (stristr($value, "https://https://")) {
            $value = str_replace("https://https://", "https://", $value);
        }
        if (stristr($value, "https://https//")) {
            $value = str_replace("https://https//", "https://", $value);
        }
        if (stristr($value, "http://https://")) {
            $value = str_replace("http://https://", "https://", $value);
        }
        return stripslashes($value);
    }

    function longstringwrap($string = "") {
        return $string;
        //return preg_replace_callback( '/\w{10,}/ ', create_function( '$matches', 'return chunk_split( $matches[0], 5, "&#8203;" );' ), $string );
    }

    function getUserShortName($uid) {
        $User = ClassRegistry::init('User');
        $User->recursive = -1;
        $usrDtls = $User->find('first', array('conditions' => array('User.id' => $uid), 'fields' => array('User.name', 'User.short_name')));
        return $usrDtls;
    }

    function getUserNameForEmail($uid) {
        $User = ClassRegistry::init('User');
        $User->recursive = -1;
        $usrDtls = $User->find('first', array('conditions' => array('User.id' => $uid, 'User.isactive' => 1, 'User.isemail' => 1), 'fields' => array('User.name', 'User.email', 'User.id')));
        return $usrDtls;
    }

    function getUserNameForWithooutEmailActive($uid) {
        $User = ClassRegistry::init('User');
        $User->recursive = -1;
        $usrDtls = $User->find('first', array('conditions' => array('User.id' => $uid, 'User.isactive' => 1), 'fields' => array('User.name', 'User.email', 'User.id')));
        return $usrDtls;
    }

    function getAllNotifyUser($project_id, $type = NULL) {
        $User = ClassRegistry::init('User');
        $User->recursive = -1;
        if ($type == 'new') {
            $usrDtls = $User->query("SELECT DISTINCT User.id, User.name, User.email FROM users as User,user_notifications as UserNotification,project_users as ProjectUser,company_users as CompanyUser WHERE User.id=UserNotification.user_id AND CompanyUser.user_id=UserNotification.user_id AND CompanyUser.is_active='1' AND CompanyUser.company_id='" . SES_COMP . "' AND UserNotification.new_case='1' AND User.isactive='1' AND ProjectUser.user_id=User.id AND ProjectUser.project_id='" . $project_id . "' AND ProjectUser.default_email='1'");
        } else {
            $usrDtls = $User->query("SELECT DISTINCT User.id, User.name, User.email FROM users as User,user_notifications as UserNotification,project_users as ProjectUser,company_users as CompanyUser WHERE User.id=UserNotification.user_id AND CompanyUser.user_id=UserNotification.user_id AND CompanyUser.is_active='1' AND CompanyUser.company_id='" . SES_COMP . "' AND UserNotification.reply_case='1' AND User.isactive='1' AND ProjectUser.user_id=User.id AND ProjectUser.project_id='" . $project_id . "' AND ProjectUser.default_email='1'");
        }
        return $usrDtls;
    }

    function getMemebersEmail($projId, $search) {
        $ProjectUser = ClassRegistry::init('ProjectUser');

        //$quickMem = $ProjectUser->find('all', array('conditions' => array('Project.uniq_id' => $projId,'Project.company_id' => SES_COMP,'User.isactive' => 1,'User.name LIKE'=>'%'.$search.'%'),'fields' => array('DISTINCT User.id','User.name','User.istype','User.email','User.short_name','User.photo'),'order' => array('User.name')));

        $quickMem = $ProjectUser->query("SELECT DISTINCT User.id, User.name, User.email, User.istype,User.email,User.short_name FROM users as User,project_users as ProjectUser,company_users as CompanyUser,projects as Project WHERE CompanyUser.user_id=ProjectUser.user_id AND CompanyUser.is_active='1' AND CompanyUser.company_id='" . SES_COMP . "' AND Project.uniq_id='" . $projId . "' AND Project.id=ProjectUser.project_id AND User.isactive='1' AND User.name LIKE '%" . $search . "%' AND ProjectUser.user_id=User.id ORDER BY User.short_name");

        return $quickMem;
    }

    function getTypes() {
        $Type = ClassRegistry::init('Type');
        $quickTyp = $Type->find('all', array('order' => array('Type.seq_order')));

        return $quickTyp;
    }

    function uploadPhoto($tmp_name, $name, $size, $path, $count, $type) {
        if ($name) {
            $inkb = $size / 1024;
            $oldname = strtolower($name);
            $ext = substr(strrchr($oldname, "."), 1);
            if (($ext != 'gif') && ($ext != 'jpg') && ($ext != 'jpeg') && ($ext != 'png')) {
                return "ext";
            }
            /* elseif($inkb > 1024) {
              return "size";
              } */ else {
                list($width, $height) = getimagesize($tmp_name);

                if ($width > 800) {
                    try {
                        if ($extname == "png") {
                            $src = imagecreatefrompng($tmp_name);
                        } elseif ($extname == "gif") {
                            $src = imagecreatefromgif($tmp_name);
                        } elseif ($extname == "bmp") {
                            $src = imagecreatefromwbmp($tmp_name);
                        } else {
                            $src = imagecreatefromjpeg($tmp_name);
                        }

                        $newwidth = 800;
                        $newheight = ($height / $width) * $newwidth;
                        $tmp = imagecreatetruecolor($newwidth, $newheight);

                        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

                        $newname = md5(time() . $count) . "." . $ext;
                        $targetpath = $path . $newname;

                        imagejpeg($tmp, $targetpath, 100);
                        imagedestroy($src);
                        imagedestroy($tmp);
                        // s3 bucket  start                                 						 
                        $s3 = new S3(awsAccessKey, awsSecretKey);
                        //$s3->putBucket(BUCKET_NAME, S3::ACL_PUBLIC_READ_WRITE);
                        $s3->putBucket(BUCKET_NAME, S3::ACL_PRIVATE);
                        if ($type == "profile_img") {
                            $folder_orig_Name = 'files/photos/' . trim($newname);
                        } else {
                            $folder_orig_Name = 'files/company/' . trim($newname);
                        }
                        //$s3->putObjectFile($tmp_name,BUCKET_NAME ,$folder_orig_Name ,S3::ACL_PUBLIC_READ_WRITE);
                        $s3->putObjectFile($targetpath, BUCKET_NAME, $folder_orig_Name, S3::ACL_PRIVATE);
                        //s3 bucket end
                        unlink($targetpath);
                    } catch (Exception $e) {
                        return false;
                    }
                } else {
                    $newname = md5(time() . $count) . "." . $ext;
                    $targetpath = $path . $newname;
                    move_uploaded_file($tmp_name, $targetpath);
                    // s3 bucket  start                                 						 
                    $s3 = new S3(awsAccessKey, awsSecretKey);
                    $s3->putBucket(BUCKET_NAME, S3::ACL_PRIVATE);
                    if ($type == "profile_img") {
                        $folder_orig_Name = 'files/photos/' . trim($newname);
                    } else {
                        $folder_orig_Name = 'files/company/' . trim($newname);
                    }
                    //$folder_orig_Name = 'files/photos/'.trim($newname);
                    //$s3->putObjectFile($tmp_name,BUCKET_NAME ,$folder_orig_Name ,S3::ACL_PUBLIC_READ_WRITE);
                    $s3->putObjectFile($targetpath, BUCKET_NAME, $folder_orig_Name, S3::ACL_PRIVATE);
                    //s3 bucket end
                    unlink($targetpath);
                }

                if ($width < 200 || $height < 200) {
                    $im_P = 'convert ' . $targetpath . '  -background white -gravity center -extent 200x200 ' . $targetpath;
                    exec($im_P);
                }

                return $newname;
            }
        } else {
            return false;
        }
    }

    function uploadProfilePhoto($name, $path) {
        if ($name) {
            $oldname = strtolower($name);
            $ext = substr(strrchr($oldname, "."), 1);
            if (($ext != 'gif') && ($ext != 'jpg') && ($ext != 'jpeg') && ($ext != 'png') && ($ext != 'bmp')) {
                return "ext";
            } else {
                $targetpath = $path . $name;
                $newname = $name; //md5(time().$count).".".$ext;			
                if (defined('USE_S3') && USE_S3) {
                    // s3 bucket  start                                 						 
                    $s3 = new S3(awsAccessKey, awsSecretKey);
                    $s3->putBucket(BUCKET_NAME, S3::ACL_PRIVATE);
                    $folder_orig_Name = 'files/photos/' . trim($newname);
                    //$s3->putObjectFile($targetpath,BUCKET_NAME ,$folder_orig_Name ,S3::ACL_PRIVATE);
                    $s3->copyObject(BUCKET_NAME, DIR_USER_PHOTOS_THUMB . trim($newname), BUCKET_NAME, $folder_orig_Name, S3::ACL_PRIVATE);
                    //s3 bucket end
                    //unlink($targetpath);	
                }

                return $newname;
            }
        } else {
            return false;
        }
    }

    function showuploadImage($tmp_name, $name, $size, $path, $count) {
        if ($name) {
            $image = strtolower($name);
            $extname = substr(strrchr($image, "."), 1);
            if (($extname != 'gif') && ($extname != 'jpg') && ($extname != 'jpeg') && ($extname != 'png') && ($extname != 'bmp')) {
                return false;
            } else {
                list($width, $height) = getimagesize($tmp_name);
                //$checkSize = round($size/1024);
                if (($width < 100 && $height < 100) || ($width < 100) || ($height < 100)) {
                    return 'small size image';
                } else {
                    if ($width > 200) {
                        try {
                            $type = exif_imagetype($tmp_name);
                            switch ($type) {
                                case 1 :
                                    $src = imagecreatefromgif($tmp_name);
                                    break;
                                case 2 :
                                    $src = imagecreatefromjpeg($tmp_name);
                                    break;
                                case 3 :
                                    $src = imagecreatefrompng($tmp_name);
                                    break;
                                case 6 :
                                    $src = imagecreatefromwbmp($tmp_name);
                                    break;
                                default:
                                    $src = imagecreatefromjpeg($tmp_name);
                                    break;
                            }

                            /* if($extname == "png") {
                              $src = imagecreatefrompng($tmp_name);
                              }
                              elseif($extname == "gif") {
                              $src = imagecreatefromgif($tmp_name);
                              }
                              elseif($extname == "bmp") {
                              $src = imagecreatefromwbmp($tmp_name);
                              }
                              else {
                              $src = imagecreatefromjpeg($tmp_name);
                              } */

                            $newwidth = 200;
                            $newheight = ($height / $width) * $newwidth;
                            //$newheight = 600;

                            $tmp = imagecreatetruecolor($newwidth, $newheight);

                            imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                            $time = time() . $count;
                            $filepath = md5($time) . "." . $extname;
                            $targetpath = $path . $filepath;
                            imagejpeg($tmp, $targetpath, 100);
                            imagedestroy($src);
                            imagedestroy($tmp);
                        } catch (Exception $e) {
                            return false;
                        }
                    } else {
                        $time = time() . $count;
                        $filepath = md5($time) . "." . $extname;
                        $targetpath = $path . $filepath;
                        if (!is_dir($path)) {
                            mkdir($path);
                        }
                        move_uploaded_file($tmp_name, $targetpath);
                    }
                    if (file_exists($targetpath)) {
                        return $filepath;
                    } else {
                        return false;
                    }
                }
            }
        }
    }

    function caseKeywordSearch($caseSrch, $type) {
        $searchcase = "";
        if (trim(urldecode($caseSrch))) {
            $srchstr1 = addslashes(trim(urldecode($caseSrch)));
            if (substr($srchstr1, 0, 1) == "#") {
                $srchstr1 = substr($srchstr1, 1, strlen($srchstr1));
            } else {
                $srchstr1 = $srchstr1;
            }
            if (!ereg('[^0-9]', $srchstr1)) {
                $searchcase = "AND (Easycase.title LIKE '%$srchstr1%' OR Easycase.case_no LIKE '$srchstr1%')";
            } else {
                if (ereg('[^A-Za-z -()@$&,]', $srchstr1) && !strstr($srchstr1, " ") && !strstr($srchstr1, "-") && !strstr($srchstr1, ",") && !strstr($srchstr1, "/") && !strstr($srchstr1, "_") && !strstr($srchstr1, "_") && !strstr($srchstr1, ":") && !strstr($srchstr1, ".") && !strstr($srchstr1, "&")) {
                    $projshortname = ereg_replace("[^A-Za-z]", "", $srchstr1);
                    $caseno = ereg_replace("[^0-9]", "", $srchstr1);
                    $searchcase = "AND (Easycase.case_no LIKE '$caseno%' OR Easycase.title LIKE '%$srchstr1%')";
                } else {
                    if (strstr($srchstr1, " ") && $type == "full") {
                        /* $expsrch = explode(" ",$srchstr1);
                          foreach($expsrch as $newsrchstr) {
                          $searchcase.= "Easycase.title LIKE '%$newsrchstr%' OR Easycase.message LIKE '%$newsrchstr%' OR ";
                          }
                          $searchcase = substr($searchcase,0,-3);
                          $searchcase = "AND (".$searchcase.")"; */
                        $searchcase = "AND (Easycase.title LIKE '%$srchstr1%' OR Easycase.message LIKE '%$srchstr1%')";
                    } elseif ($type == "half") {
                        $searchcase = "AND (Easycase.title LIKE '%$srchstr1%' OR Easycase.message LIKE '%$srchstr1%')";
                    } elseif ($type == "title") {
                        $searchcase = "AND Easycase.title LIKE '%$srchstr1%'";
                    } else {
                        $searchcase .= "AND (Easycase.title LIKE '%$srchstr1%' OR Easycase.message LIKE '%$srchstr1%')";
                    }
                }
            }
        }
        return $searchcase;
    }

    function statusFilter($caseStatus) {
        $qry = "";
        $caseStatus = $caseStatus . "-";
        $stsArr = explode("-", $caseStatus);
        foreach ($stsArr as $chksts) {
            if (trim($chksts)) {
                if ($chksts == "attch" || $chksts == "upd") {
                    if ($chksts == "attch") {
                        $qry .= "format=1 OR ";
                    }
                    if ($chksts == "upd") {
                        $qry .= "type_id=10 OR ";
                    }
                } elseif ($chksts == 2) {
                    $qry .= "legend=2 OR legend=4 OR ";
                } else {
                    $qry .= "legend=" . $chksts . " OR ";
                }
            }
        }
        $qry = substr($qry, 0, -3);
        if ($qry) {
            $qry = " AND (" . trim($qry) . ")";
        }
        return $qry;
    }

    function typeFilter($caseTypes) {
        $qry = "";
        $qryTyp = "";
        if ($caseTypes != "all") {
            if (strstr($caseTypes, "-")) {
                $typArr = explode("-", $caseTypes);
                foreach ($typArr as $typChk) {
                    $qryTyp .= "Easycase.type_id=" . $typChk . " OR ";
                }
                $qryTyp = substr($qryTyp, 0, -3);
                $qry .= " AND (" . $qryTyp . ")";
            } else {
                $qry .= " AND Easycase.type_id=" . $caseTypes;
            }
        }
        return $qry;
    }

    function priorityFilter($priorityFil, $caseTypes) {
        $qry = "";
        $qryPri = "";
        if ($priorityFil != "all") {
            if (strstr($priorityFil, "-")) {
                $priArr = explode("-", $priorityFil);
                foreach ($priArr as $priChk) {
                    if ($priChk) {
                        if ($priChk == "High") {
                            $qryPri .= "Easycase.priority=0 OR ";
                        } else if ($priChk == "Medium") {
                            $qryPri .= "Easycase.priority=1 OR ";
                        } else {
                            $qryPri .= "Easycase.priority>=2 OR ";
                        }
                    }
                }
                $qryPri = substr($qryPri, 0, -3);
                $qry .= " AND (" . $qryPri . ")";
            } else {
                if ($priorityFil == "High") {
                    $qry .= " AND priority=0";
                } else if ($priorityFil == "Medium") {
                    $qry .= " AND priority=1";
                } else {
                    $qry .= " AND priority>=2";
                }
            }
            if ($caseTypes != 10) {
                $qry .= " AND type_id != 10";
            }
        }
        return $qry;
    }

    function memberFilter($caseUserId) {
        $qry = "";
        $qryMem = "";
        if ($caseUserId != "all") {
            if (strstr($caseUserId, "-")) {
                $memArr = explode("-", $caseUserId);
                foreach ($memArr as $memChk) {
                    $qryMem .= "Easycase.user_id=" . $memChk . " OR ";
                }
                $qryMem = substr($qryMem, 0, -3);
                $qry .= " AND (" . $qryMem . ")";
            } else {
                $qry .= " AND Easycase.user_id=" . $caseUserId;
            }
        }
        return $qry;
    }

    function assigntoFilter($caseAssignTo) {
        $qry = "";
        $qryAsn = "";
        if ($caseAssignTo != "all") {
            if (strstr($caseAssignTo, "-")) {
                $asnArr = explode("-", $caseAssignTo);
                foreach ($asnArr as $asnChk) {
                    $qryAsn .= "Easycase.assign_to=" . $asnChk . " OR ";
                }
                $qryAsn = substr($qryAsn, 0, -3);
                $qry .= " AND (" . $qryAsn . ")";
            } else {
                $qry .= " AND Easycase.assign_to=" . $caseAssignTo;
            }
        }
        return $qry;
    }

    function filterMilestone($milestoneUid = '') {
        if ($milestoneUid) {
            $mlst_cls = ClassRegistry::init('Milestone');
            $mlist = $mlst_cls->find('first', array('conditions' => array('Milestone.uniq_id' => $milestoneUid), 'fields' => 'Milestone.id,Milestone.title'));
            return ' AND EasycaseMilestone.milestone_id=' . $mlist['Milestone']['id'];
        } else {
            return '';
        }
    }

    function find_file($dirname, $fname, &$file_path) {
        if (file_exists($dirname . $fname)) {
            return $dirname . $fname;
        } else {
            return false;
        }
    }

    function emailBodyFilter($value) {
        $pattern = array("/\n/", "/\r/", "/content-type:/i", "/to:/i", "/from:/i", "/cc:/i");
        $value = preg_replace($pattern, "", $value);
        return $value;
    }

    function validateEmail($email) {
        $at = strrpos($email, "@");
        if ($at && ($at < 1 || ($at + 1) == strlen($email)))
            return false;
        if (preg_match("/(\.{2,})/", $email))
            return false;
        $local = substr($email, 0, $at);
        $domain = substr($email, $at + 1);
        $locLen = strlen($local);
        $domLen = strlen($domain);
        if ($locLen < 1 || $locLen > 64 || $domLen < 4 || $domLen > 255)
            return false;
        if (preg_match("/(^\.|\.$)/", $local) || preg_match("/(^\.|\.$)/", $domain))
            return false;
        if (!preg_match('/^"(.+)"$/', $local)) {
            if (!preg_match('/^[-a-zA-Z0-9!#$%*\/?|^{}`~&\'+=_\.]*$/', $local))
                return false;
        }
        if (!preg_match("/^[-a-zA-Z0-9\.]*$/", $domain) || !strpos($domain, "."))
            return false;
        return true;
    }

    function generatePassword($length) {
        $vowels = 'aeuy';
        $consonants = '3@Z6!29G7#$QW4';
        $password = '';
        $alt = time() % 2;
        for ($i = 0; $i < $length; $i++) {
            if ($alt == 1) {
                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 0;
            } else {
                $password .= $vowels[(rand() % strlen($vowels))];
                $alt = 1;
            }
        }
        return $password;
    }

    function generateTemporaryURL($resource) {
        $bucketname = BUCKET_NAME;
        $awsAccessKey = awsAccessKey;
        $awsSecretKey = awsSecretKey;
        $expires = strtotime('+1 day'); //1.day.from_now.to_i; 
        $s3_key = explode(BUCKET_NAME, $resource);
        $x = $s3_key[1];
        $s3_key[1] = substr($x, 1);
        $string = "GET\n\n\n{$expires}\n/{$bucketname}/{$s3_key[1]}";
        $signature = urlencode(base64_encode((hash_hmac("sha1", utf8_encode($string), $awsSecretKey, TRUE))));
        //echo $expires."=====";echo $signature;
        return "{$resource}?AWSAccessKeyId={$awsAccessKey}&Signature={$signature}&Expires={$expires}";
        //https://s3.amazonaws.com/orangescrum-dev/files/case_files/1.jpg?AWSAccessKeyId=AKIAJAVFGWOGKGBOWPWQ&Signature=gZ90JslqYADtRK6haMVR9e2guko%3D&Expires=1360239119
    }

    function downloadFile($filename) {
        set_time_limit(0);
        ob_clean();
        if (!isset($filename) || empty($filename)) {
            $var = "<table align='center' width='100%'><tr><td style='font:bold 14px verdana;color:#FF0000;' align='center'>" . __('Please specify a file name for download.') . "</td></tr></table>";
            die($var);
        }
        if (USE_S3 == 0) {
            if (strpos($filename, "\0") !== FALSE)
                die('');
            $fname = basename($filename);
            if (file_exists(DIR_CASE_FILES . $fname)) {
                $file_path = DIR_CASE_FILES . $fname;
            } else {
                $var = "<table align='center' width='100%'><tr><td style='font:bold 12px verdana;color:#FF0000;' align='center'>" . __('Oops! File not found.') . "<br/> " . __('File may be deleted or make sure you specified correct file name.') . "</td></tr></table>";
                die($var);
            }
        } else {
            $s3 = new S3(awsAccessKey, awsSecretKey);
            $info = $s3->getObjectInfo(BUCKET_NAME, DIR_CASE_FILES_S3_FOLDER . $filename);
            if ($info) {
                $fileurl = $this->generateTemporaryURL(DIR_CASE_FILES_S3 . $filename);
                //$file_path = DIR_CASE_FILES_S3.$filename;
                $file_path = $fileurl;
            } else {
                $var = "<table align='center' width='100%'><tr><td style='font:bold 12px verdana;color:#FF0000;' align='center'>" . __('Oops! File not found.') . "<br/> " . __('File may be deleted or make sure you specified correct file name.') . "</td></tr></table>";
                die($var);
            }
        }

        /* Figure out the MIME type | Check in array */
        $known_mime_types = array(
            "pdf" => "application/pdf",
            "txt" => "text/plain",
            "html" => "text/html",
            "htm" => "text/html",
            "exe" => "application/octet-stream",
            "zip" => "application/zip",
            "doc" => "application/msword",
            "xls" => "application/vnd.ms-excel",
            "ppt" => "application/vnd.ms-powerpoint",
            "gif" => "image/gif",
            "png" => "image/png",
            "jpeg" => "image/jpg",
            "jpg" => "image/jpg",
            "php" => "text/plain"
        );
        $file_extension = strtolower(substr(strrchr($filename, "."), 1));
        if (array_key_exists($file_extension, $known_mime_types)) {
            $mime_type = $known_mime_types[$file_extension];
        } else {
            $mime_type = "application/force-download";
        };
        // Send file headers
        header("Content-type: $mime_type");
        header("Content-Disposition: attachment;filename=$filename");
        header('Pragma: no-cache');
        header("Content-Length: " . filesize($file_path));
        header('Expires: 0');
        //$file_path = DIR_CASE_FILES_S3.$filename;
        // Send the file contents.
        readfile($file_path);
    }

    function downloadFile1($filename) {
        set_time_limit(0);
        if (!isset($filename) || empty($filename)) {
            $var = "<table align='center' width='100%'><tr><td style='font:bold 14px verdana;color:#FF0000;' align='center'>" . __('Please specify a file name for download.') . "</td></tr></table>";
            die($var);
        }

        if (strpos($filename, "\0") !== FALSE)
            die('');
        $fname = basename($filename);

        if (file_exists(DIR_CASE_FILES . $fname)) {
            $file_path = DIR_CASE_FILES . $fname;
        } else {
            $var = "<table align='center' width='100%'><tr><td style='font:bold 12px verdana;color:#FF0000;' align='center'>" . __('Oops! File not found.') . "<br/> " . __('File may be deleted or make sure you specified correct file name.') . "</td></tr></table>";
            die($var);
        }
        $fsize = filesize($file_path);

        $fext = strtolower(substr(strrchr($fname, "."), 1));

        if (!isset($_GET['fc']) || empty($_GET['fc'])) {
            $asfname = $fname;
        } else {
            $asfname = str_replace(array('"', "'", '\\', '/'), '', $_GET['fc']);
            if ($asfname === '')
                $asfname = 'NoName';
        }

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Type: ");
        header("Content-Disposition: attachment; filename=\"$asfname\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . $fsize);

        $file = @fopen($file_path, "rb");
        if ($file) {
            while (!feof($file)) {
                print(fread($file, 1024 * 8));
                flush();
                if (connection_status() != 0) {
                    @fclose($file);
                    die();
                }
            }
            @fclose($file);
        }
    }

    function chnageUploadedFileName($filename) {
        $output = preg_replace('/[^(\x20-\x7F)]*/', '', $filename);
        $rep1 = str_replace("~", "_", $output);
        $rep2 = str_replace("!", "_", $rep1);
        $rep3 = str_replace("@", "_", $rep2);
        $rep4 = str_replace("#", "_", $rep3);
        $rep5 = str_replace("%", "_", $rep4);
        $rep6 = str_replace("^", "_", $rep5);
        $rep7 = str_replace("&", "_", $rep6);
        $rep11 = str_replace("+", "_", $rep7);
        $rep13 = str_replace("=", "_", $rep11);
        $rep14 = str_replace(":", "_", $rep13);
        $rep15 = str_replace("|", "_", $rep14);
        $rep16 = str_replace("\"", "_", $rep15);
        $rep17 = str_replace("?", "_", $rep16);
        $rep18 = str_replace(",", "_", $rep17);
        $rep19 = str_replace("'", "_", $rep18);
        $rep20 = str_replace("$", "_", $rep19);
        $rep21 = str_replace(";", "_", $rep20);
        $rep22 = str_replace("`", "_", $rep21);
        $rep23 = str_replace(" ", "_", $rep22);
        $rep28 = str_replace("/", "_", $rep23);
        $rep29 = str_replace("�", "_", $rep28);
        $rep30 = str_replace("�", "_", $rep29);
        return $rep30;
    }

    function validateFileExt($ext) {
        $extList = array("bat", "com", "cpl", "dll", "exe", "msi", "msp", "pif", "shs", "sys", "cgi", "reg", "bin", "torrent", "yps", "mp4", "mpeg", "mpg", "3gp", "dat", "mod", "avi", "flv", "xvid", "scr", "com", "pif", "chm", "cmd", "cpl", "crt", "hlp", "hta", "inf", "ins", "isp", "jse?", "lnk", "mdb", "ms", "pcd", "pif", "scr", "sct", "shs", "vb", "ws", "vbs");

        $ext = strtolower($ext);
        if (!in_array($ext, $extList)) {
            return "success";
        } else {
            //alert("Invalid input file format! Should be txt, doc, docx, xls, xlsx, pdf, odt, ppt, jpeg, tif, gif, psd, jpg or png");
            return "." . $ext;
        }
    }

    function todo_typ($type, $title) {
        $disp_type = '<img src="' . HTTP_IMAGES . 'images/types/' . $type . '.png" title="' . $title . '" alt="' . $type . '" rel="tooltip"/>';
        return $disp_type;
    }

    function formatText($value) {
        $value = str_replace("�", "\"", $value);
        $value = str_replace("�", "\"", $value);
        $value = preg_replace('/[^(\x20-\x7F)\x0A]*/', '', $value);
        $value = stripslashes($value);
        $value = html_entity_decode($value, ENT_QUOTES);
        $trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
        $value = strtr($value, $trans);
        $value = stripslashes(trim($value));
        return $value;
    }

    function chgdate($val) {
        $dt = explode("/", $val);
        $dateformat = $dt['2'] . "-" . $dt['0'] . "-" . $dt['1'];
        return $dateformat;
    }

    function dateFormatReverse($output_date) {
        if ($output_date != "") {
            if (strstr($output_date, " ")) {
                $exp = explode(" ", $output_date);
                $od = $exp[0];
                $date_ex2 = explode("-", $od);
                $dateformated_input = $date_ex2[1] . "/" . $date_ex2[2] . "/" . $date_ex2[0] . " " . $exp[1];
            } else {
                $exp = explode("-", $output_date);
                $dateformated_input = $exp[1] . "/" . $exp[2] . "/" . $exp[0];
            }
            return $dateformated_input;
        }
    }

    function makeSeoUrl($url) {
        if ($url) {
            $url = trim(strtolower($url));
            $url = str_replace(' ', '', $url); // Replaces all spaces .
            $value = preg_replace('/[^A-Za-z0-9\-]/', '', $url); // Removes special chars.
            //$value = preg_replace("![^a-z0-9]+!i", "", $url);
            $url = trim($value);
        }
        return $url;
    }

    function makeShortName($first, $last) {
        if (stristr($first, " ")) {
            $firstexp = explode(" ", $first);
            $let1 = substr($firstexp[0], 0, 1);
            $let2 = substr($firstexp[1], 0, 1);
        } else {
            $let1 = substr($first, 0, 2);
        }
        $let3 = substr($last, 0, 1);

        return strtoupper($let1 . $let2 . $let3);
    }

    function displayStatus($st) {
        if ($st == 1) {
            $status = __("New", true);
        } elseif ($st == 2) {
            $status = __("In Progress", true);
        } elseif ($st == 3) {
            $status = __("Closed", true);
        } elseif ($st == 4) {
            $status = __("Started", true);
        } elseif ($st == 5) {
            $status = __("Resolved", true);
        } elseif ($st == "hctta") {
            $status = __("Files", true);
        } elseif ($st == "dpu") {
            $status = __("Updates", true);
        } else {
            $status_cls = ClassRegistry::init('Status');
            $status_list = $status_cls->find('first', array('conditions' => array('Status.id' => $st)));
            $status = $status_list['Status']['name'];
        }
        return $status;
    }

    function caseBcMems($uid) {
        $User = ClassRegistry::init('User');
        $User->recursive = -1;
        $usrDtls = $User->find('first', array('conditions' => array('User.id' => $uid, 'User.isactive' => 1), 'fields' => array('User.short_name')));
        return $usrDtls['User']['short_name'];
    }

    function caseMemsList($uid) {
        $User = ClassRegistry::init('User');
        $User->recursive = -1;
        $usrDtls = $User->find('list', array('conditions' => array('User.id' => $uid, 'User.isactive' => 1), 'fields' => array('User.short_name')));
        if (count($usrDtls) == 1) {
            $memlist = array_values($usrDtls);
            return $memlist[0];
        } else {
            return $usrDtls;
        }
    }

    function caseProjectList($pid) {
        $Project = ClassRegistry::init('Project');
        $Project->recursive = -1;
        $project = $Project->find('list', array('conditions' => array('Project.id' => $pid, 'Project.isactive' => 1), 'fields' => array('Project.short_name')));
        if (count($project) == 1) {
            $prolist = array_values($project);
            return $prolist[0];
        } else {
            return $project;
        }
    }

    function caseBcTypes($typ) {
        if (strlen($typ) == 2 && $typ == 01) {
            $typ = 10;
        }
        $Type = ClassRegistry::init('Type');
        $cstype = $Type->find('first', array('conditions' => array('Type.id' => $typ), 'fields' => array('Type.short_name')));
        return $cstype['Type']['short_name'];
    }

    function fullSpace($used, $totalsize = 1024) {
        $full = $used * 100 / $totalsize;
        $used = round($full, 1);
        return $used;
    }

    function usedSpace($curProjId = NULL, $company_id = SES_COMP) {
        $CaseFiles = ClassRegistry::init('CaseFiles');
        $this->recursive = -1;
        $cond = " 1 ";
        if ($company_id) {
            $cond .= " AND CaseFile.company_id=" . $company_id;
        }
        if ($curProjId) {
            $cond .= " AND CaseFile.project_id=" . $curProjId;
        }
        if (defined('TLG') && TLG == 1) {
            $sql = "SELECT SUM(file_size) AS file_size  FROM case_files  AS CaseFile LEFT JOIN easycases AS Easycase ON Easycase.id=CaseFile.easycase_id AND Easycase.project_id=CaseFile.project_id WHERE Easycase.isactive=1 AND CaseFile.isactive=1 AND" . $cond;
        } else {
            $sql = "SELECT SUM(file_size) AS file_size  FROM case_files AS CaseFile WHERE " . $cond;
        }
        $res1 = $CaseFiles->query($sql);
        $filesize = $res1['0']['0']['file_size'] / 1024;
        return number_format($filesize, 2);

        /* if(!$company_id) {
          $company_id = SES_COMP;
          }

          if($curProjId) {
          $cid = $this->getAllCsId($curProjId);
          }
          else {
          $Project = ClassRegistry::init('Project');
          $Project->recursive = -1;

          $curProjId = array();

          $allProjIds = $Project->find('all', array('conditions'=>array('Project.company_id' => $company_id),'fields' => array('Project.id')));
          foreach($allProjIds as $pjIds) {
          $curProjId[] = $pjIds['Project']['id'];
          }
          $cid = $this->getAllCsId($curProjId);
          }

          $Easycase = ClassRegistry::init('Easycase');
          $Easycase->recursive = -1;
          $caseSize = $Easycase->find('all', array('conditions'=>array('Easycase.project_id' => $curProjId,'Easycase.isactive' => 1),'fields' => array('SUM(LENGTH(message)) as msg','SUM(LENGTH(title)) as titl')));

          App::import('Model','CaseFile'); $CaseFile = new CaseFile();
          $CaseFile->recursive = -1;
          $caseFileSize = $CaseFile->find('all', array('conditions'=>array('CaseFile.easycase_id' => $cid,'CaseFile.isactive' => 1), 'fields'=>array('SUM(file_size) AS filesize','SUM(LENGTH(file)) as filelength')));

          $totalsize = $caseSize['0']['0']['msg']+$caseSize['0']['0']['titl']+$caseFileSize['0']['0']['filelength'];
          $totalsizeInKB = $totalsize/1024;

          $filesizeInKb = $caseFileSize['0']['0']['filesize'];
          $allTotsizeinKb = $filesizeInKb+$totalsizeInKB;
          $allTotsizeinMb = round($allTotsizeinKb/1024,2);

          return $allTotsizeinMb; */
    }

    function shortLength($value, $len) {
        $value_format = $this->formatText($value);
        $value_raw = html_entity_decode($value_format, ENT_QUOTES);
        if (strlen($value_raw) > $len) {
            $value_strip = substr($value_raw, 0, $len);
            $value_strip = $this->formatText($value_strip);
            $lengthvalue = "<span title='" . $value_format . "' >" . $value_strip . "...</span>";
        } else {
            $lengthvalue = $value_format;
        }
        return $lengthvalue;
    }

    function getAllCsId($pid) {
        $Easycase = ClassRegistry::init('Easycase');
        $Easycase->recursive = -1;
        $caseIds = $Easycase->find('all', array('conditions' => array('Easycase.project_id' => $pid), 'fields' => 'id'));
        $ids = array();
        foreach ($caseIds as $csid) {
            array_push($ids, $csid['Easycase']['id']);
        }
        return $ids;
    }

    function dateFormatOutputdateTime_day($date_time, $curdate = NULL, $type = NULL) {
        if ($date_time != "") {
            $date_time = date("Y-m-d H;i:s", strtotime($date_time));
            $output = explode(" ", $date_time);
            $date_ex2 = explode("-", $output[0]);

            $dateformated = $date_ex2[1] . "/" . $date_ex2[2] . "/" . $date_ex2[0];
            if ($date_ex2[2] != "00") {
                $displayWeek = 0;
                $timeformat = date('g:i a', strtotime($date_time));

                $week1 = date("l", mktime(0, 0, 0, $date_ex2[1], $date_ex2[2], $date_ex2[0]));
                $week_sub1 = substr($week1, "0", "3");

                $yesterday = date("Y-m-d", strtotime($curdate . "-1 days"));

                if ($dateformated == $this->dateFormatReverse($curdate)) {
                    $dateTime_Format = "Today";
                } elseif ($dateformated == $this->dateFormatReverse($yesterday)) {
                    $dateTime_Format = "Y'day";
                } else {
                    $CurYr = date("Y", strtotime($curdate));
                    $DateYr = date("Y", strtotime($dateformated));
                    if ($CurYr == $DateYr) {
                        $dateformated = date("M d", strtotime($dateformated));
                        $dtformated = date("M d", strtotime($dateformated)) . ", " . date("D", strtotime($dateformated));
                        $displayWeek = 1;
                    } else {
                        $dateformated = date("M d, Y", strtotime($dateformated));
                        $dtformated = date("M d, Y", strtotime($dateformated));
                    }
                    $dateTime_Format = $dateformated;
                }

                if ($type == 'date') {
                    return $dateTime_Format;
                } elseif ($type == 'time') {
                    return $dateTime_Format . " " . $timeformat;
                } elseif ($type == 'week') {
                    if ($dateTime_Format == "Today" || $dateTime_Format == "Y'day" || !$displayWeek) {
                        //return $dateTime_Format;
                        return $dtformated;
                    } else {
                        return $dateTime_Format . ", " . date("D", strtotime($dateformated));
                    }
                } else {
                    if ($dateTime_Format == "Today" || $dateTime_Format == "Y'day") {
                        return $dateTime_Format . " " . $timeformat;
                    } else {
                        //return $dateTime_Format.", ".date("D",strtotime($dateformated))." ".$timeformat;
                        //return $dateTime_Format.", ".date("Y",strtotime($dateformated))." ".$timeformat;
                        return $dtformated . " " . $timeformat;
                    }
                }
            }
        }
    }

    function GetDateTime($timezoneid, $gmt_offset, $dst_offset, $timezone_code, $db_date, $type) {
        $dst = 1;
        if (!$timezoneid) {
            return date('Y-m-d H:i');
        }
        if ($type == "revdate") {
            $exp = explode(" ", $db_date);
            $exp_d = explode("-", $exp[0]);
            $exp_t = explode(":", $exp[1]);

            if ($gmt_offset != 0) {
                $sign1 = substr($gmt_offset, 0, 1);
                $value = substr($gmt_offset, 1, -4);

                if ($this->isDaylightSaving($timezoneid, $gmt_offset)) {
                    $value = $value - $dst_offset;
                } else {
                    $value = $value + $dst_offset;
                }
                if ($sign1 == "+") {

                    return date("Y-m-d", mktime($exp_t[0] - $value, $exp_t[1], $exp_t[2], $exp_d[1], $exp_d[2], $exp_d[0]));
                } elseif ($sign1 == "-") {
                    return date("Y-m-d", mktime($exp_t[0] - $value, $exp_t[1], $exp_t[2], $exp_d[1], $exp_d[2], $exp_d[0]));
                } else {
                    return date("Y-m-d", mktime($exp_t[0] - $value, $exp_t[1], $exp_t[2], $exp_d[1], $exp_d[2], $exp_d[0]));
                }
            } else {
                return date("Y-m-d", mktime($exp_t[0], $exp_t[1], $exp_t[2], $exp_d[1], $exp_d[2], $exp_d[0]));
            }
        } else {
            if ($dst_offset > 0) {
                if (!($dst)) {
                    $dst_offset = 0;
                } else if (!$this->isDaylightSaving($timezoneid, $gmt_offset)) {
                    $dst_offset = 0;
                }
            }
            $dst_offset *= 60;
            $gmt_offset *= 60;

            $exp = explode(" ", $db_date);
            $exp_d = explode("-", $exp[0]);
            $exp_t = explode(":", $exp[1]);

            $gmt_hour = $exp_t[0];
            $gmt_minute = $exp_t[1];
            $gmt_secs = $exp_t[2];



            $time = $gmt_hour * 60 + $gmt_minute + $gmt_offset + $dst_offset;
            if ($type == "datetime") {
                return date('Y-m-d H:i:s', mktime($time / 60, $time % 60, $gmt_secs, $exp_d[1], $exp_d[2], $exp_d[0]));
            } elseif ($type == "date") {

                return date('Y-m-d', mktime($time / 60, $time % 60, $gmt_secs, $exp_d[1], $exp_d[2], $exp_d[0]));
            } elseif ($type == "time") {
                return date('H-i-s', mktime($time / 60, $time % 60, $gmt_secs, $exp_d[1], $exp_d[2], $exp_d[0]));
            } elseif ($type == "dateFormat") {
                return date('m/d/Y', mktime($time / 60, $time % 60, $gmt_secs, $exp_d[1], $exp_d[2], $exp_d[0]));
            } elseif ($type == "header") {
                return date('l, F j Y h:i A', mktime($time / 60, $time % 60, $gmt_secs, $exp_d[1], $exp_d[2], $exp_d[0]));
            } elseif ($type == "td") {
                return date('"G.i"', mktime($time / 60, $time % 60, $gmt_secs, $exp_d[1], $exp_d[2], $exp_d[0]));
            }
        }
    }

    function getProjectName($pid) {
        $shortName = "";
        $Project = ClassRegistry::init('Project');
        $Project->recursive = -1;
        $pjArr = $Project->find('first', array('conditions' => array('Project.id' => $pid, 'Project.isactive' => 1, 'Project.company_id' => SES_COMP), 'fields' => array('Project.name')));
        return $pjArr['Project']['name'];
        //return $pjArr;
    }

    function getProjectShortName($pid) {
        $shortName = "";
        $Project = ClassRegistry::init('Project');
        $Project->recursive = -1;
        $pjArr = $Project->find('first', array('conditions' => array('Project.id' => $pid, 'Project.isactive' => 1, 'Project.company_id' => SES_COMP), 'fields' => array('Project.short_name')));
        return $pjArr['Project']['short_name'];
        //return $pjArr;
    }

    function getRequireUserName($UserId = NULL, $is_email = NULL) {
        $User = ClassRegistry::init('User');
        $User->recursive = -1;
        $usrDtls = $User->query("SELECT `name`, `last_name`, `email` FROM `users` WHERE `id`='" . $UserId . "'");
        $fullname = $usrDtls[0]['users']['name'] . " " . $usrDtls[0]['users']['last_name'];
        if (isset($is_email)) {
            $fullname = $usrDtls[0]['users']['email'];
        }
        return $fullname;
    }

    function getRequireTypeName($TypeId = NULL) {
        $Type = ClassRegistry::init('Type');
        $Type->recursive = -1;

        $typDtls = $Type->query("SELECT `name` FROM `types` WHERE `id`='" . $TypeId . "'");


        $typename = $typDtls[0]['types']['name'];

        //echo "<pre>";print_r($usrDtls);echo $typename;exit;

        return $typename;
    }

    function dateFormatOutputdateTime_day_EXPORT($date_time, $curdate = NULL, $type = NULL) {
        if ($date_time != "") {
            $date_time = date("Y-m-d H:i:s", strtotime($date_time));
            $output = explode(" ", $date_time);
            $date_ex2 = explode("-", $output[0]);

            $dateformated = $date_ex2[1] . "/" . $date_ex2[2] . "/" . $date_ex2[0];
            if ($date_ex2[2] != "00") {
                $displayWeek = 0;
                $timeformat = date('g:i a', strtotime($date_time));

                $week1 = date("l", mktime(0, 0, 0, $date_ex2[1], $date_ex2[2], $date_ex2[0]));
                $week_sub1 = substr($week1, "0", "3");

                $yesterday = date("Y-m-d", strtotime($curdate . "-1 days"));

                if ($dateformated == $this->dateFormatReverse($curdate)) {
                    $dateTime_Format = "Today";
                } elseif ($dateformated == $this->dateFormatReverse($yesterday)) {
                    $dateTime_Format = "Y'day";
                } else {
                    $CurYr = date("Y", strtotime($curdate));
                    $DateYr = date("Y", strtotime($dateformated));
                    if ($CurYr == $DateYr) {
                        $dateformated = date("m/d", strtotime($dateformated));
                        $displayWeek = 1;
                    } else {
                        $dateformated = date("M d Y", strtotime($dateformated));
                    }
                    $dateTime_Format = $dateformated;
                }

                if ($type == 'date') {
                    return $dateTime_Format;
                } elseif ($type == 'time') {
                    return $dateTime_Format . " " . $timeformat;
                } elseif ($type == 'week') {
                    if ($dateTime_Format == "Today" || $dateTime_Format == "Y'day" || !$displayWeek) {
                        return $dateTime_Format;
                    } else {
                        return $dateTime_Format . ", " . date("D", strtotime($dateformated));
                    }
                } else {
                    if ($dateTime_Format == "Today" || $dateTime_Format == "Y'day") {
                        return $dateTime_Format . " " . $timeformat;
                    } else {
                        return $dateTime_Format . ", " . date("D", strtotime($dateformated)) . " " . $timeformat;
                    }
                }
            }
        }
    }

    function mdyFormat($date_time, $type = NULL) {
        if ($date_time != "") {
            $date_time = date("Y-m-d H:i:s", strtotime($date_time));
            $output = explode(" ", $date_time);
            $date_ex2 = explode("-", $output[0]);

            $dateformated = $date_ex2[1] . "/" . $date_ex2[2] . "/" . $date_ex2[0];
            if ($date_ex2[2] != "00") {

                $timeformat = date('g:i a', strtotime($date_time));
                $dateformated = date("m/d/Y", strtotime($dateformated));
                $dateTime_Format = $dateformated;

                if ($type == 'time') {
                    return $dateTime_Format . " " . $timeformat;
                } else {
                    return $dateTime_Format;
                }
            }
        }
    }

    function checkEmailExists($betaEmail) {
        $BetaUser = ClassRegistry::init('BetaUser');
        $BetaUser->recursive = -1;

        $findUserEmail = $BetaUser->find('first', array('conditions' => array('BetaUser.email' => $betaEmail), 'fields' => array('BetaUser.id', 'BetaUser.is_approve')));

        $id = $findUserEmail['BetaUser']['id'];
        $is_approve = $findUserEmail['BetaUser']['is_approve'];

        if ($id) {
            $User = ClassRegistry::init('User');
            $User->recursive = -1;
            $findUser = $User->find('count', array('conditions' => array('User.email' => $betaEmail), 'fields' => array('User.id')));

            if ($findUser) {
                return 1; //Present in both user table and betauser table  //User Already Exists
            } else {
                if ($is_approve == 1) {
                    return 2; //Present in beta table but not in user table and is_approve in 1  //Your beta user has been approved
                } else {
                    return 3; //Present in beta table but not in user table and is_approve in 0  //Your beta user has been disapproved
                }
            }
        } else {
            $User = ClassRegistry::init('User');
            $User->recursive = -1;
            $findUser = $User->find('count', array('conditions' => array('User.email' => $betaEmail), 'fields' => array('User.id')));

            if ($findUser) {
                return 4; //Present in user table and not present in betauser table  //User Already Exists
            } else {
                return 5; //Not present in both user and beta user table
            }
        }
    }

    function isValidDateTime($dateTime) {
        if (preg_match("/^(\d{1,2})\/(\d{1,2})\/(\d{2,4})$/", $dateTime, $matches)) {
            if (checkdate($matches[1], $matches[2], $matches[3])) {
                return true;
            }
        }
        return false;
    }

    function isValidDateHours($hour, $chk = null) {
        if ($chk) {
            $exp = '/^[0-9]{0,2}:[0-9]{2}[ap]m$/';
        } else {
            $exp = '/^[0-9]{0,2}:[0-9]{2}$/';
        }
        if (preg_match($exp, $hour)) {
            return true;
        }
        return false;
    }

    function isValidTlDateHours($hour, $chk) {
        $exp = '/^[0-9]{0,2}:[0-9]{2}\s?[apAP][mM]$/';
        if (preg_match($exp, $hour)) {
            return true;
        }
        return false;
    }

    function convert_ascii($string) {
        // Replace Single Curly Quotes
        $search[] = chr(226) . chr(128) . chr(152);
        $replace[] = "'";
        $search[] = chr(226) . chr(128) . chr(153);
        $replace[] = "'";

        // Replace Smart Double Curly Quotes
        $search[] = chr(226) . chr(128) . chr(156);
        $replace[] = '\"';
        $search[] = chr(226) . chr(128) . chr(157);
        $replace[] = '\"';

        // Replace En Dash
        $search[] = chr(226) . chr(128) . chr(147);
        $replace[] = '--';

        // Replace Em Dash
        $search[] = chr(226) . chr(128) . chr(148);
        $replace[] = '---';

        // Replace Bullet
        $search[] = chr(226) . chr(128) . chr(162);
        $replace[] = '*';

        // Replace Middle Dot
        $search[] = chr(194) . chr(183);
        $replace[] = '*';

        // Replace Ellipsis with three consecutive dots
        $search[] = chr(226) . chr(128) . chr(166);
        $replace[] = '...';

        $search[] = chr(150);
        $replace[] = "-";

        // Apply Replacements
        $string = str_replace($search, $replace, $string);

        // Remove any non-ASCII Characters
        //$string = preg_replace("/[^\x01-\x7F]/","", $string);
        return $string;
    }

    function getSqlFields($arr, $prj_unq_id) {
        $qry = '';
        if (isset($arr)) {
            //Filter by date
            $case_date = $arr['date'];
            if (trim($case_date) == '1') {
                $one_date = date('Y-m-d H:i:s', time() - 3600);
                $qry .= " AND Easycase.dt_created >='" . $one_date . "'";
            } else if (trim($case_date) == '24') {
                $day_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s"))) . " -1 day"));
                $qry .= " AND Easycase.dt_created >='" . $day_date . "'";
            } else if (trim($case_date) == 'week') {
                $week_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s"))) . " -1 week"));
                $qry .= " AND Easycase.dt_created >='" . $week_date . "'";
            } else if (trim($case_date) == 'month') {
                $month_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s"))) . " -1 month"));
                $qry .= " AND Easycase.dt_created >='" . $month_date . "'";
            } else if (trim($case_date) == 'year') {
                $year_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s"))) . " -1 year"));
                $qry .= " AND Easycase.dt_created >='" . $year_date . "'";
            } else if (strstr(trim($case_date), ":")) {
                //echo $case_date;exit;
                $ar_dt = explode(":", trim($case_date));
                $frm_dt = $ar_dt['0'];
                $to_dt = $ar_dt['1'];
                $qry .= " AND DATE(Easycase.dt_created) >= '" . date('Y-m-d H:i:s', strtotime($frm_dt)) . "' AND DATE(Easycase.dt_created) <= '" . date('Y-m-d H:i:s', strtotime($to_dt)) . "'";
            }

//	if($arr['date'] =='1'){
//	    $qry .=" AND Easycase.dt_created >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
//	}elseif($arr['date'] =='24'){
//	    $qry .=" AND Easycase.dt_created >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
//	}elseif($arr['date'] =='week'){
//	    $qry .=" AND Easycase.dt_created >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
//	}elseif($arr['date'] =='month'){
//	    $qry .=" AND Easycase.dt_created >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
//	}elseif($arr['date'] =='year'){
//	    $qry .=" AND Easycase.dt_created >= DATE_SUB(NOW(), INTERVAL 12 MONTH)";
//	}elseif($arr['date'] =='cst_rng'){
//	    $fm_date = explode("/", $arr['from']);
//	    $from_date = $fm_date['2']."-".$fm_date['0']."-".$fm_date['1'];
//
//	    $t_date = explode("/", $arr['to']);
//	    $to_date = $t_date['2']."-".$t_date['0']."-".$t_date['1'];
//
//	    $qry .=" AND Easycase.dt_created >= ".$from_date." AND Easycase.dt_created <=".$to_date;
//	}
            //Filter by status
            if (intval($arr['status'])) {
                if ($arr['status'] == 2) {
                    $qry .= " AND (Easycase.legend='" . $arr['status'] . "' OR Easycase.legend='4')";
                } else {
                    $qry .= " AND Easycase.legend='" . $arr['status'] . "'";
                }
            } elseif ($arr['status'] == 'attach') {
                $qry .= " AND Easycase.format='1'";
            } elseif ($arr['status'] == 'update') {
                $qry .= " AND Easycase.type_id='10'";
            }
            if ($arr['types'] == 'all' && $arr['status'] != 'update') {
                $qry .= " AND Easycase.type_id !='10'";
            }
            //Filter by types
            if (intval($arr['types'])) {
                $qry .= " AND Easycase.type_id='" . $arr['types'] . "'";
            }
            //Filter by priority
            if ($arr['priority'] != 'all') {
                $qry .= " AND Easycase.priority='" . $arr['priority'] . "'";
            }

            if (isset($prj_unq_id) && $prj_unq_id != 'all') {
                //Filter by members
                if (intval($arr['members'])) {
                    $qry .= " AND Easycase.user_id='" . $arr['members'] . "'";
                }
                //Filter by assign to
                if (intval($arr['assign_to'])) {
                    $qry .= " AND Easycase.assign_to='" . $arr['assign_to'] . "'";
                }
                //Filter by milestone
                if (intval($arr['milestone'])) {
                    $qry .= " AND EasycaseMilestone.milestone_id='" . $arr['milestone'] . "'";
                }
            }
            return $qry;
        }
    }

    /**
     * @method public iptolocation(string $ip) Detect the location from IP
     * @author GDR<support@ornagescrum.com>
     * @return string  Location fromt the ip
     */
    function validate_ip($ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }
        return true;
    }

    function getRealIpAddr() {
        /* try {
          $ipaddress = file_get_contents("http://www.telize.com/jsonip");
          $ipaddress = json_decode($ipaddress,true);
          if(isset($ipaddress['ip']) && ip2long($ipaddress['ip'])) {
          $ip = $ipaddress['ip'];
          }
          }catch(Exception $e){
          return $ip;
          } */

        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    // trim for safety measures
                    $ip = trim($ip);
                    // attempt to validate IP
                    if ($this->validate_ip($ip)) {
                        return $ip;
                    }
                }
            }
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
    }

    function is_private_ip($ip) {
        if (empty($ip) or ! ip2long($ip)) {
            return false;
        }
        $private_ips = array(
            array('10.0.0.0', '10.255.255.255'),
            array('172.16.0.0', '172.31.255.255'),
            array('192.168.0.0', '192.168.255.255')
        );
        $ip = ip2long($ip);
        foreach ($private_ips as $ipr) {
            $min = ip2long($ipr[0]);
            $max = ip2long($ipr[1]);
            if (($ip >= $min) && ($ip <= $max))
                return true;
        }
        return false;
    }

    /**
     * @method Public hoursspent($project_id) Total hours spent in a project
     * @return int hours spent
     */
    function hoursspent($project_id) {
        $easycasecls = ClassRegistry::init('Easycase');
        $easycasecls->recursive = -1;
        if ($project_id) {
            if (defined('TLG') && TLG == 1) {
                $sql = "SELECT SUM(LogTime.total_hours) AS hours "
                        . "FROM log_times as LogTime "
                        . "LEFT JOIN easycases AS Easycase ON LogTime.task_id=Easycase.id AND LogTime.project_id=Easycase.project_id "
                        . "WHERE LogTime.project_id =" . $project_id . " AND Easycase.isactive=1";
                $result = $easycasecls->query($sql);
                return $this->format_time_hr_min($result['0']['0']['hours']);
            } else {
                $result = $easycasecls->query("SELECT ROUND(SUM(easycases.hours), 1) as hours from easycases WHERE project_id=" . $project_id . " AND istype='2' and isactive='1'");
                return $result['0']['0']['hours'];
            }
        } else {
            $projcls = ClassRegistry::init('Project');
            $projcls->recursive = -1;
            $project_list = $projcls->find('list', array('conditions' => array('isactive' => 1, 'company_id' => SES_COMP), 'fields' => array('id')));
            if (defined('TLG') && TLG == 1) {
                $sql = "SELECT SUM(LogTime.total_hours) AS hours "
                        . "FROM log_times as LogTime "
                        . "LEFT JOIN easycases AS Easycase ON LogTime.task_id=Easycase.id AND LogTime.project_id=Easycase.project_id "
                        . "WHERE LogTime.project_id IN (" . implode(',', $project_list) . ") AND Easycase.isactive=1";
                $result = $easycasecls->query($sql);
                return $this->format_time_hr_min($result['0']['0']['hours']);
            } else {
                $result = $easycasecls->find('all', array('conditions' => array("Easycase.project_id" => $project_list, 'istype' => 2, 'isactive' => 1), 'fields' => "ROUND(SUM(Easycase.hours), 1) as hours"));
                return $result['0']['0']['hours'];
            }
        }
    }

    /**
     * @method PUBLIC generate_invoiceid() 
     */
    function generate_invoiceid() {
        $trnsclas = ClassRegistry::init('Transaction');
        $trnsclas->recursive = -1;
        $trans = $trnsclas->find('first', array('conditions' => ('invoice_id IS NOT NULL'), 'order' => 'id DESC', 'fields' => array('invoice_id')));

        if ($trans) {
            $prv_invoice_id = (int) $trans['Transaction']['invoice_id'];
            if ($prv_invoice_id == 1) {
                $prv_invoice_id = 153702;
            }
            $prv_invoice_id = (int) $trans['Transaction']['invoice_id'] + 1;
        } else {
            $prv_invoice_id = 153700;
        }
        $current_invoice_id = str_pad($prv_invoice_id, 6, 0, STR_PAD_LEFT);
        return $current_invoice_id;
    }

    function getRemoteIP() {
        $ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if ($_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if ($_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if ($_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if ($_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if ($_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

    /**
     * 
     * @param type $source
     * @param type $destination
     * @param string $flag
     * @return boolean
     */
    function zipFile($source, $destination, $flag = '') {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }
        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }
        $source = str_replace('\\', '/', realpath($source));
        if ($flag) {
            $flag = basename($source) . '/';
            //$zip->addEmptyDir(basename($source) . '/');
        }

        if (is_dir($source) === true) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
            foreach ($files as $file) {
                $arr[] = $file->getFileName();
                if ($file->getFileName() == '.' || $file->getFileName() == '..')
                    continue;
                $file = str_replace('\\', '/', realpath($file));
                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace($source . '/', '', $flag . $file . '/'));
                } else if (is_file($file) === true) {
                    $zip->addFromString(str_replace($source . '/', '', $flag . $file), file_get_contents($file));
                }
            }
        } else if (is_file($source) === true) {
            $zip->addFromString($flag . basename($source), file_get_contents($source));
        }
        return $zip->close();
    }

    /* Author: GKM
     * to format sec to hr min
     */

    function format_time_hr_min($totalsecs = '') {
        $hours = floor($totalsecs / 3600) > 0 ? floor($totalsecs / 3600) . " hr" . (floor($totalsecs / 3600) > 1 ? 's' : '') . " " : '';
        $mins = round(($totalsecs % 3600) / 60) > 0 ? "" . round(($totalsecs % 3600) / 60) . " min" . (round(($totalsecs % 3600) / 60) > 1 ? 's' : '') : '';
        return $hours != '' || $mins != '' ? $hours . "" . $mins : '---';
    }

    /* author: GKM
     * it is used to format 24 hr to 12 hr with am / pm format
     */

    function format_24hr_to_12hr($time) {
        $out_time_arr = explode(":", $time);
        $out_mode = intval($out_time_arr[0]) < 12 ? 'am' : 'pm';
        $out_hr = intval($out_time_arr[0]) > 12 ? intval($out_time_arr[0]) - 12 : intval($out_time_arr[0]);
        $out_min = intval($out_time_arr[1]);
        return ($out_hr > 0 ? $out_hr : 12) . ':' . ($out_min < 10 ? '0' : '') . $out_min . '' . $out_mode;
    }

    function api_format_24hr_to_12hr($time) {
        $out_time_arr = explode(":", $time);
        $out_mode = intval($out_time_arr[0]) < 12 ? ' AM' : ' PM';
        $out_hr = intval($out_time_arr[0]) > 12 ? intval($out_time_arr[0]) - 12 : intval($out_time_arr[0]);
        $out_min = intval($out_time_arr[1]);
        return ($out_hr > 0 ? $out_hr : 12) . ':' . ($out_min < 10 ? '0' : '') . $out_min . '' . $out_mode;
    }

    function date_filter($filter = '', $curDateTime = '') {
        $curDateTime = $curDateTime != "" ? $curDateTime : date('Y-m-d H:i:s');
        $data = array();
        $month = date("m", strtotime($curDateTime . ($filter == 'lastquarter' ? " -3 months" : "")));
        if ($month < 4) {
            $start = __('first day of january', true);
            $end = __('last day of march', true);
        } elseif ($month > 3 && $month < 7) {
            $start = __('first day of april', true);
            $end = __('last day of june', true);
        } elseif ($month > 6 && $month < 10) {
            $start = __('first day of july', true);
            $end = __('last day of september', true);
        } elseif ($month > 9) {
            $start = __('first day of october', true);
            $end = __('last day of december', true);
        }
        switch ($filter) {
            case 'today':
                $data['strddt'] = date('Y-m-d', strtotime($curDateTime));
                break;
            case 'yesterday':
                $data['strddt'] = date('Y-m-d', strtotime($curDateTime . ' -1 day'));
                break;
            case 'thisweek':
                $data['strddt'] = date('Y-m-d', strtotime('last monday', strtotime($curDateTime)));
                $data['enddt'] = date('Y-m-d', strtotime($curDateTime));
                break;
            case 'thismonth':
                $data['strddt'] = date('Y-m-d', strtotime('first day of this month', strtotime($curDateTime)));
                $data['enddt'] = date('Y-m-d', strtotime($curDateTime));
                break;
            case 'thismonthall':
                $data['strddt'] = date('Y-m-d', strtotime('first day of this month', strtotime($curDateTime)));
                $data['enddt'] = date('Y-m-d', strtotime('last day of this month', strtotime($curDateTime)));
                break;
            case 'thisquarter':
                $data['strddt'] = date('Y-m-d', strtotime($start, strtotime($curDateTime)));
                $data['enddt'] = date('Y-m-d', strtotime($curDateTime));
                break;
            case 'thisyear':
                $data['strddt'] = date('Y-m-d', strtotime('first day of January', strtotime($curDateTime)));
                $data['enddt'] = date('Y-m-d', strtotime($curDateTime));
                break;
            case 'lastweek':
                $data['strddt'] = date('Y-m-d', strtotime('last monday', strtotime($curDateTime . " -7 days")));
                $data['enddt'] = date('Y-m-d', strtotime('next sunday', strtotime($curDateTime . " -7 days")));
                break;
            case 'lastmonth':
                $data['strddt'] = date('Y-m-d', strtotime('first day of this month', strtotime($curDateTime . " -1 month")));
                $data['enddt'] = date('Y-m-d', strtotime('last day of this month', strtotime($curDateTime . " -1 month")));
                break;
            case 'lastquarter':
                $data['strddt'] = date('Y-m-d', strtotime($start, strtotime($curDateTime . " -3 months")));
                $data['enddt'] = date('Y-m-d', strtotime($end, strtotime($curDateTime . " -3 months")));
                break;
            case 'lastyear':
                $data['strddt'] = date('Y-m-d', strtotime('first day of January', strtotime($curDateTime . ' -1 year')));
                $data['enddt'] = date('Y-m-d', strtotime('last day of December', strtotime($curDateTime . ' -1 year')));
                break;
            case 'last365days':
                $data['strddt'] = date('Y-m-d', strtotime($curDateTime . " -364 days"));
                $data['enddt'] = date('Y-m-d', strtotime($curDateTime));
                break;
            case 'last30days':
                $data['strddt'] = date('Y-m-d', strtotime($curDateTime . " -30 days"));
                $data['enddt'] = date('Y-m-d', strtotime($curDateTime));
                break;
            case 'next30days':
                $data['strddt'] = date('Y-m-d', strtotime($curDateTime));
                $data['enddt'] = date('Y-m-d', strtotime($curDateTime . " +30 days"));
            case 'alldates':
                /* unset($data['strddt']);unset($data['enddt']);$timelog_filter_msg = ''; */
                break;
            case 'custom':break;
            default:break;
        }
        return $data;
    }

    /* By CP
     * used to format price value
     */

    function format_price($price) {
        return number_format($price, 2, '.', '');
    }

    /* by CP
     * for removing special characters
     */

    function seo_url($string = '', $flag = '-') {
        if (trim($string) != '')
            return trim(preg_replace('/[^a-z0-9]+/i', $flag, $string), $flag);
        else
            return '';
    }

    /* by CP
     * To recursively sey permissions to file and folders.
     */

    function recursiveChmod($path, $filePerm = 0644, $dirPerm = 0755) {
        // Check if the path exists
        if (!file_exists($path)) {
            return(false);
        }

        // See whether this is a file
        if (is_file($path)) {
            // Chmod the file with our given filepermissions
            chmod($path, $filePerm);

            // If this is a directory...
        } elseif (is_dir($path)) {
            // Then get an array of the contents
            $foldersAndFiles = scandir($path);

            // Remove "." and ".." from the list
            $entries = array_slice($foldersAndFiles, 2);

            // Parse every result...
            foreach ($entries as $entry) {
                // And call this function again recursively, with the same permissions
                recursiveChmod($path . "/" . $entry, $filePerm, $dirPerm);
            }

            // When we are done with the contents of the directory, we chmod the directory itself
            chmod($path, $dirPerm);
        }

        // Everything seemed to work out well, return true
        return(true);
    }

    function seconds2human($ss) {
        $h = floor($ss / 3600);
        $m = floor($ss / 60 % 60);
        if ($h && $m) {
            $hrs = ($h == 1) ? 'hr' : 'hrs';
            return "$h $hrs $m mins";
        } else if ($h) {
            $hrs = ($h == 1) ? 'hr' : 'hrs';
            return "$h $hrs";
        } else if ($m) {
            return "$m mins";
        } else {
            return "";
        }
    }

    function seconds2fraction($ss) {
        $h = floor($ss / 3600);
        if ($h) {
            $m = floor($ss / 60 % 60);
            $mi = ($m) ? ":" . round($m, 2) : '';
            return $h . $mi . " h";
        } else {
            $m = floor($ss / 60 % 60);
            return round($m, 2) . " m";
        }
    }

    /*
     * Author: Satyajeet
     * Get vacation dates
     */

    function getLeaveDates($start_date, $end_date, $id) {
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));
        $days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);
        $x = floor($days);
        if ($x < 7) {
            $interval = 1;
        } elseif ($x > 80) {
            $interval = ceil($x / 10);
        } else {
            $interval = 7;
        }
        $dt_arr = array();
        for ($i = 0; $i <= $x; $i++) {
            $m = " +" . $i . "day";
            $dt = date('Y-m-d', strtotime(date("Y-m-d", strtotime($start_date)) . $m));
            $dt_arr = array_merge($dt_arr, array($dt => $id));
        }
        return $dt_arr;
    }

    function format_second_hrmin($totalsecs = '') {
        $hours = $mins = '00';
        if (!empty($totalsecs)) {
            $hours = floor($totalsecs / 3600) > 0 ? floor($totalsecs / 3600) : '00';
            $mins = round(($totalsecs % 3600) / 60) > 0 ? round(($totalsecs % 3600) / 60) : '00';
        }
        return $hours . ":" . $mins;
    }

    /*
     * Author Satyajeet

     * To get the Recurrence Rule according to the input given by the user
     */

    function getRRule($recurrenceDetail, $type = 'test') {
        $frequency = $interval = $byday = $bymonthday = $byweekno = $bymonth = '';
        $startDate = $recurrenceDetail['recur_start_date'];
        $endDate = $recurrenceDetail['recurrence_end_type'] == 'date' ? $recurrenceDetail['recur_end_date'] : '';
        $occurrences = $recurrenceDetail['recurrence_end_type'] == 'occurrances' ? $recurrenceDetail['occurrances'] : '10';
        switch ($recurrenceDetail['recur_pattern']) {
            case 'daily':
                $frequency = 'DAILY';
                $interval = $recurrenceDetail['daily_interval'];
                if ($recurrenceDetail['daily_check'] == 'interval') {
                    $byday = '';
                } else {
                    $byday = 'MO,TU,WE,TH,FR';
                }
                break;
            case 'weekly':
                $frequency = 'WEEKLY';
                $interval = $recurrenceDetail['weekly_interval'];
                $byday = $recurrenceDetail['weekly_days'];
                break;
            case 'monthly':
                $frequency = 'MONTHLY';
                $interval = $recurrenceDetail['monthly_interval'];
                $bymonthday = $recurrenceDetail['monthly_date'];
                if ($recurrenceDetail['monthly_check'] == 'complecated') {
                    $byweekno = $recurrenceDetail['monthly_mask'];
                    $byday = $recurrenceDetail['monthly_day'];
                }
                break;
            case 'yearly':
                $frequency = 'YEARLY';
                $interval = $recurrenceDetail['yearly_interval'];
                $bymonthday = $recurrenceDetail['yearly_date'];
                $bymonth = $recurrenceDetail['yearly_month'];
                if ($recurrenceDetail['yearly_check'] == 'complecated') {
                    $byday = $recurrenceDetail['yearly_day'];
                    $byweekno = $recurrenceDetail['yearly_mask'];
                }
                break;
        }
        require ROOT . '/app/Vendor/php-rrule/src/RRuleInterface.php';
        require ROOT . '/app/Vendor/php-rrule/src/RRule.php';
        require ROOT . '/app/Vendor/php-rrule/src/RSet.php';
        $rrule = new RRule(array(
            'FREQ' => $frequency,
            'INTERVAL' => $interval,
            'BYMONTHDAY' => $bymonthday,
            'BYDAY' => $byday,
            'BYWEEKNO' => $byweekno,
            'BYMONTH' => $bymonth,
            'DTSTART' => $startDate,
            'COUNT' => $recurrenceDetail['recurrence_end_type'] == 'date' ? '' : $occurrences,
            'UNTIL' => $endDate
        ));
        return $rrule;
    }

    /*
     * Author: Satyajeet
     * To check current date is in recurring date array or not
     */

    function checkDateInRecurring($recurringDetails, $date) {
        require ROOT . '/app/Vendor/php-rrule/src/RRuleInterface.php';
        require ROOT . '/app/Vendor/php-rrule/src/RRule.php';
        require ROOT . '/app/Vendor/php-rrule/src/RSet.php';
        $rrule = new RRule(array(
            'FREQ' => $recurringDetails['frequency'],
            'INTERVAL' => $recurringDetails['interval'],
            'BYMONTHDAY' => $recurringDetails['bymonthday'],
            'BYDAY' => $recurringDetails['byday'],
            'BYWEEKNO' => $recurringDetails['byweekno'],
            'BYMONTH' => $recurringDetails['bymonth'],
            'DTSTART' => $recurringDetails['start_date'],
            'COUNT' => '',
            'UNTIL' => $date
        ));
        $occurrenceDates = $rrule->getOccurrences();
        foreach ($occurrenceDates as $k => $orrcurrences) {
            $orrcurrence = $orrcurrences->format('Y-m-d');
            if ($date == $orrcurrence) {
                return $k;
            }
        }
        return 0;
    }

    /*
     * Delete all booked and overloaded hours from database
     */

    function delete_booked_hours($data = null) {
        $easycase_id = $data['easycase_id'];
        $project_id = $data['project_id'];
        $ProjectBookedResource = ClassRegistry::init('ProjectBookedResource');
        $ProjectBookedResource->deleteAll(array('ProjectBookedResource.easycase_id' => $easycase_id, 'ProjectBookedResource.project_id' => $project_id));
        $Overload = ClassRegistry::init('Overload');
        $Overload->deleteAll(array('Overload.easycase_id' => $easycase_id, 'Overload.project_id' => $project_id));
        return true;
    }

    /*
     * To check task is active or not
     */

    function taskIsActive($caseId) {
        $Easycase = ClassRegistry::init('Easycase');
        $taskDetails = $Easycase->find('first', array('conditions' => array('Easycase.id' => $caseId), 'fields' => array('Easycase.isactive')));
        return $taskDetails['Easycase']['isactive'];
    }

    public function overloadUsers($callee = NULL) {
        $assigned_Resource_id = $callee['assignTo'];
        $estimated_hrs = $callee['est_hr'];
        $assigned_Resource_project = $callee['projectId'];
        $caseId = $callee['caseId'];
        $perDayWorkSec = $GLOBALS['company_work_hour'] * 3600;
        $caseUniqId = $callee['caseUniqId'];
        $no_of_days = $nof_of_days_lv = ceil(($estimated_hrs * 3600) / $perDayWorkSec);
        $startdate = $assigned_Resource_date = date('Y-m-d', strtotime($callee['str_date']));
        $lastdate = date('Y-m-d', strtotime($assigned_Resource_date . " +" . ($no_of_days - 1) . "days"));
        $Overload = ClassRegistry::init('Overload');
        $ProjectBookedResource = ClassRegistry::init('Timelog.ProjectBookedResource');
        $UserLeave = ClassRegistry::init('UserLeave');
        $leaves = $UserLeave->find('all', array('conditions' => array('UserLeave.company_id' => SES_COMP, 'UserLeave.user_id' => $assigned_Resource_id)));

        $working_dates = array();
        $do = $no_of_days;
        while ($do > 0) {
            $inleave = $this->Postcase->checkDateInLeave($assigned_Resource_date, $leaves);
            if (!$inleave) {
                $working_dates[] = $assigned_Resource_date;
                $do--;
            }
            $assigned_Resource_date = date('Y-m-d', strtotime($assigned_Resource_date . " +" . 1 . " days"));
        }
        $partial_days = array();
        foreach ($working_dates as $key => $value) {
            $query = "SELECT SUM(`ProjectBookedResource`.`booked_hours`) AS booked_hrs, `ProjectBookedResource`.`id`, `ProjectBookedResource`.`company_id`, `ProjectBookedResource`.`project_id`, `ProjectBookedResource`.`easycase_id`, `ProjectBookedResource`.`user_id`, `ProjectBookedResource`.`date` FROM `project_booked_resources` AS `ProjectBookedResource` WHERE `ProjectBookedResource`.`company_id` = " . SES_COMP . " AND `ProjectBookedResource`.`user_id` = " . $assigned_Resource_id . " AND DATE(`ProjectBookedResource`.`date`) = '" . date('Y-m-d', strtotime($value)) . "' GROUP BY `ProjectBookedResource`.`date`";
            $hours_booked = $ProjectBookedResource->query($query);
            if (!empty($hours_booked)) {
                $booked_hours = $hours_booked[0][0]['booked_hrs'];
                $all_hours_taken = ($booked_hours == $perDayWorkSec) ? true : false;
                if (!$all_hours_taken) {
                    $partial_days[$value] = $booked_hours;
                }
            } else {
                $partial_days[$value] = $perDayWorkSec;
            }
        }
        $due_date = date('Y-m-d', strtotime($startdate));
        if (empty($partial_days)) {
            $overload_hours = $ovhrs = ($estimated_hrs / $no_of_days) * 3600;
            $overload = array();
            foreach ($working_dates as $key => $value) {
                $newDate = $due_date = date('Y-m-d', strtotime($value));
                $load_data = $ProjectBookedResource->find('all', array('conditions' => array('ProjectBookedResource.user_id' => $assigned_Resource_id, 'ProjectBookedResource.company_id' => SES_COMP, 'DATE(ProjectBookedResource.date) =' => $newDate)));
                $overload[] = array('date' => $newDate, 'easycase_id' => $caseId, 'project_id' => $assigned_Resource_project, 'user_id' => $assigned_Resource_id, 'company_id' => SES_COMP, 'overload' => $overload_hours);
            }
            $Overload->saveAll($overload);
        } else {
            $estimated_hrss = $estimated_hrs;
            foreach ($partial_days as $key => $value) {
                $pr_bk_hrs['ProjectBookedResource'] = array();
                $newDate = date('Y-m-d', strtotime($key));
                $a = $perDayWorkSec - $value;
                $rest_to_assign = (empty($a)) ? ($perDayWorkSec / 3600) : ($a) / 3600;
                $bookde_val = (empty($a)) ? $perDayWorkSec : ($a);
                $pr_bk_hrs['ProjectBookedResource'] = array('user_id' => $assigned_Resource_id, 'date' => $newDate, 'project_id' => $assigned_Resource_project, 'easycase_id' => $caseId, 'company_id' => SES_COMP, 'booked_hours' => $bookde_val, 'overload' => 0);
                $ProjectBookedResource->create();
                $ProjectBookedResource->save($pr_bk_hrs);
                $estimated_hrss -= $rest_to_assign;
            }
            $overload_hours = ($estimated_hrss / $no_of_days) * 3600;
            $last_date = $working_dates[count($working_dates) - 1];
            $overload = array();
            foreach ($working_dates as $key => $value) {
                $newDate = $due_date = date('Y-m-d', strtotime($value));
                $load_data = $ProjectBookedResource->find('all', array('conditions' => array('ProjectBookedResource.user_id' => $assigned_Resource_id, 'ProjectBookedResource.company_id' => SES_COMP, 'DATE(ProjectBookedResource.date) =' => $newDate)));
                $overload[] = array('date' => $newDate, 'easycase_id' => $caseId, 'project_id' => $assigned_Resource_project, 'user_id' => $assigned_Resource_id, 'company_id' => SES_COMP, 'overload' => $overload_hours);
            }
            $Overload->saveAll($overload);
        }
        $Easycase = ClassRegistry::init('Easycase');
        $Easycase->updateAll(array('Easycase.assign_to' => $assigned_Resource_id, 'Easycase.gantt_start_date' => '"' . GMT_DATETIME . '"', 'Easycase.due_date' => '"' . date('Y-m-d H:i:s', strtotime($working_dates[count($working_dates) - 1])) . '"'), array('Easycase.id' => $caseId));
        return 1;
    }

    function caseMemsName($uid) {
        $User = ClassRegistry::init('User');
        $User->recursive = -1;
        $usrDtls = $User->find('list', array('conditions' => array('User.id' => $uid, 'User.isactive' => 1), 'fields' => array('User.name')));
        if (count($usrDtls) == 1) {
            $memlist = array_values($usrDtls);
            return $memlist[0];
        } else {
            return $usrDtls;
        }
    }

    function caseMilestoneName($mid) {
        $Miles = ClassRegistry::init('Milestone');
        $Miles->recursive = -1;
        $mileDtls = $Miles->find('list', array('conditions' => array('Milestone.id' => $mid, 'Milestone.isactive' => 1), 'fields' => array('Milestone.title')));
        if (count($mileDtls) == 1) {
            $milelist = array_values($mileDtls);
            return $milelist[0];
        } else {
            return $mileDtls;
        }
    }

    /*
     * Author: Satyajeet
     * Get company owner's email
     */

    function getOwnerEmailId($comp_id) {
        $CompanyUser = ClassRegistry::init('CompanyUser');
        $CompanyUser->bindModel(
                array(
                    'belongsTo' => array(
                        'User' => array(
                            'className' => 'User',
                        )
                    )
                )
        );
        $data = $CompanyUser->find('first', array('conditions' => array('CompanyUser.company_id' => $comp_id, 'CompanyUser.user_type' => 1, 'CompanyUser.is_active' => 1), 'fields' => array('User.email')));
        return $data['User']['email'];
    }

    /*
     * Author: Satyajeet
     * Get Company Name
     */

    function getCompanyName($comp_id) {
        $Company = ClassRegistry::init('Company');
        $data = $Company->find('first', array('conditions' => array('Company.id' => $comp_id, 'Company.is_active' => 1), 'fields' => array('Company.name')));
        return $data['Company']['name'];
    }

    function days_differnce($user_date) {
        $from = strtotime($user_date);
        $today = time();
        $difference = $today - $from;
        return floor($difference / (60 * 60 * 24));
    }

    function hours_day_format($hours) {
        $time_more = "";
        if ($hours > 24) {
            $time_more = round($hours / 24) . " days";
        } else {
            $time_more = $hours . " hours";
        }
        return $time_more;
    }

    function datediffernce($start_date, $end_date) {
        $diff = abs(strtotime($end_date) - strtotime($start_date));
        $days = floor($diff / 86400);
        $hours = floor(($diff - ($days * 86400)) / 3600);
        $minutes = floor(($diff - ($days * 86400) - ($hours * 3600)) / 60);
        if ($days > 0) {
            $dffrnce_date = $days . " Days";
        } else {
            $dffrnce_date = $hours . ' hr ' . $minutes . ' mins';
        }
        return $dffrnce_date;
    }

    function getApiStatus($type, $legend) {
        if ($type == 10) {
            return 'Update';
        } else if ($legend == 1) {
            return 'New';
        } else if ($legend == 2 || $legend == 4) {
            return 'In Progress';
        } else if ($legend == 3) {
            return 'Closed';
        } else if ($legend == 5) {
            return 'Resolved';
        }
    }

    function getRecurring($recurringDetails, $date) {
        include_once ROOT . '/app/Vendor/php-rrule/src/RRuleInterface.php';
        include_once ROOT . '/app/Vendor/php-rrule/src/RRule.php';
        include_once ROOT . '/app/Vendor/php-rrule/src/RSet.php';
        $rrule = new RRule(array(
            'FREQ' => $recurringDetails['frequency'],
            'INTERVAL' => $recurringDetails['interval'],
            'BYMONTHDAY' => $recurringDetails['bymonthday'],
            'BYDAY' => $recurringDetails['byday'],
            'BYWEEKNO' => $recurringDetails['byweekno'],
            'BYMONTH' => $recurringDetails['bymonth'],
            'DTSTART' => $recurringDetails['start_date'],
            'COUNT' => ($recurringDetails['occurrences']) ? ($recurringDetails['occurrences'] + 1) : 6
        ));

        $occurrenceDates = $rrule->getOccurrences();
        $occurrenceDates1 = array();
        $newoccurrenceDates = $occurrenceDates;
        rsort($newoccurrenceDates);
        $endDate = date('Y-m-d', strtotime($newoccurrenceDates['0']->format('Y-m-d')));
        /**
         * if (defined('GTLG') && GTLG == 1) {
          $CompanyHoliday = ClassRegistry::init('CompanyHoliday');
          $holidayLists = $CompanyHoliday->find('list', array('fields' => array('id', 'holiday'), 'conditions' => array('company_id' => SES_COMP, 'or' => array('holiday >=' => $date, 'holiday <=' => $endDate)), 'order' => array('created ASC')));
          $newholidayLists = array();
          foreach ($holidayLists as $hKey => $hVal) {
          $holidate = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $hVal, "date");
          $newholidayLists[$hKey] = $holidate;
          }
          $weekendArr = explode(',', $GLOBALS['company_week_ends']);
          }
         * */
        foreach ($occurrenceDates as $k => $v) {
            $day_name = date('w', strtotime($this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $v->format('Y-m-d'), "date")));
            /** if (defined('GTLG') && GTLG == 1) {
              if (!in_array($v->format('Y-m-d'), $newholidayLists) && !in_array($day_name, $weekendArr)) {
              if (strtotime($date) < strtotime($v->format('Y-m-d'))) {
              $occurrenceDates1[] = $v->format('l, d F Y');
              }
              }
              } else { * */
            $weekendArr = array(0, 6);
            if (!in_array($day_name, $weekendArr)) {
                if (strtotime($date) < strtotime($v->format('Y-m-d'))) {
                    $occurrenceDates1[] = $v->format('l, d F Y');
                }
            }
//            }
        }
        return $occurrenceDates1;
    }

}

?>
