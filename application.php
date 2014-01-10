<?php
/*
1. Receive coordinates data from a source (source.php)
2. decode the coordinates received 
3. stream to a file and echo out

*/
$i=1;
while($i >0)
{
$data = `php source.php`;
$response = decodeGGA($data);
echo json_encode($response);
writeToFile(json_encode($response));
echo "\n";
sleep(3);
$i++;
}
function decodeGGA($data)
{
//we decode here and give back a response 
$ggaArr = explode(",",$data);
//we expect to have fifteen items
if(count($ggaArr)!= 15)
return 'no data';

if(getNmeaType($data)!="GPGGA")
return "invalid data type";
/*Array
(
    [0] => $GPGGA
    [1] => 140107.346
    [2] => 6506.037
    [3] => N
    [4] => 00520.323
    [5] => E
    [6] => 0
    [7] => 00
    [8] => 
    [9] => 
    [10] => M
    [11] => 
    [12] => M
    [13] => 
    [14] => *45 
)
*/
// we 
$tt = explode(".",$ggaArr[1]);
$response['time'] = @date("H.i.s",strtotime($tt[0])) ;
$response['latitude']= @convertCordinates($ggaArr[2],$ggaArr[3]) +0;
$response['ns'] = @$ggaArr[3] ;
$response['longtitude'] = @convertCordinates($ggaArr[4],$ggaArr[5]) +0;
$response['ew'] =@$ggaArr[5] ;
$response['gpsQuality']=getGPSQuality($ggaArr[6]);
$response['numsat']=$ggaArr[7];
 $response['hdp']=$ggaArr[8];
 $response['alt']=$ggaArr[9];
$response['un_alt']=$ggaArr[10];
$response['geoidal']=$ggaArr[11];
$response['un_geoidal']=$ggaArr[12];
$response['dgps']=$ggaArr[13];
return $response;
}
function getGPSQuality($qual)
{
$gpsqual="Fix not available";

if($qual==0)
 $gpsqual="Fix not available";
if($qual==1) 
$gpsqual="GPS fix";
if($qual==2)
 $gpsqual="Differential GPS fix";

return $gpsqual;	

}
function convertCordinates($degree,$direction)
{
                $deg=(int)($degree/100); 
                $min= $degree-($deg*100);
                $dot=$min/60;
                $decimal=$deg +$dot;
                //South latitudes and West longitudes need to return a negative result
        if (($direction=="S") or ($direction=="W"))
        {
                    $decimal=$decimal*(-1);
                }
            $decimal=number_format($decimal,3,'.',''); //truncate decimal to $precision places
            return $decimal;
        }      

//find the type of imea data we have 
function getNmeaType($data){
$type ="";	
	if(eregi("GPGGA",$data)) $type="GPGGA";
	return $type;
	}
function writeToFile( $string = '')
{
   $date = date("Y-m-d H:i:s");
 $file ="coordinates.log";
                if($fo = fopen($file, 'ab'))
                {
                        fwrite($fo, "$date | $string\n");
                        fclose($fo);
                }
                else
                {
                        trigger_error("flog Cannot log '$string' to file '$file' ", E_USER_WARNING);
                }
        }

