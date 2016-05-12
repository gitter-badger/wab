<?php
/*
   Copyright 2011-2013 Jason Harris KJ4IWX

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.



   See the Wiki page for usage information at https://bitbucket.org/kj4iwx/phpadifparser/wiki/Home



*/

class ADIF_Parser {
    var $data; //the adif data
    var $i; //the iterator
    var $current_line; //stores information about the current qso
    var $headers = array();

    private function __construct() {
    }
    public function feed($input_data) { // allows the parser to be fed a string
        $this->data = $input_data;
    }

    public function loadFromFile($fname) { // allows the user to accept a filename as input
        $this->data = file_get_contents($fname);
    }
    public function initialize() { // this function locates the <EOH>
        $pos = stripos($this->data, "<eoh>");
        if ($pos == false) { // did we find the end of headers?
            echo "Error: Adif_Parser Already Initialized or No <EOH> in ADIF File";
            return 0;
        } ;
        // get headers
        $this->i = 0;
        $in_tag = false;
        $tag = "";
        $value_length = "";
        $value = "";

        while ($this->i < $pos) {
            // skip comments
            if ($this->data[$this->i] == "#") {
                while ($this->i < $pos) {
                    if ($this->data[$this->i] == "\n") {
                        break;
                    }

                    $this->i++;
                }
            } else {
                // find the beginning of a tag
                if ($this->data[$this->i] == "<") {
                    $this->i++;
                    // record the key
                    while ($this->data[$this->i] < $pos && $this->data[$this->i] != ':') {
                        $tag = $tag . $this->data[$this->i];
                        $this->i++;
                    }

                    $this->i++; //iterate past the :
                    // find out how long the value is
                    while ($this->data[$this->i] < $pos && $this->data[$this->i] != '>') {
                        $value_length = $value_length . $this->data[$this->i];
                        $this->i++;
                    }

                    $this->i++; //iterate past the >
                    $len = (int)$value_length;
                    // copy the value into the buffer
                    while ($len > 0 && $this->i < $pos) {
                        $value = $value . $this->data[$this->i];
                        $len--;
                        $this->i++;
                    } ;

                    $this->headers[strtolower(trim($tag))] = $value; //convert it to lowercase and trim it in case of \r
                    // clear all of our variables
                    $tag = "";
                    $value_length = "";
                    $value = "";
                }
            }

            $this->i++;
        } ;

        $this->i = $pos + 5; //iterate past the <eoh>
        if ($this->i >= strlen($this->data)) { // is this the end of the file?
            echo "Error: ADIF File Does Not Contain Any QSOs";
            return 0;
        } ;
        return 1;
    }
    // the following function does the processing of the array into its key and value pairs
    public function recordToArray($record) {
        $return = array();
        for($a = 0; $a < strlen($record); $a++) {
            if ($record[$a] == '<') { // find the start of the tag
                $tag_name = "";
                $value = "";
                $len_str = "";
                $len = 0;
                $a++; //go past the <
                while ($record[$a] != ':') { // get the tag
                    $tag_name = $tag_name . $record[$a]; //append this char to the tag name
                    $a++;
                } ;
                $a++; //iterate past the colon
                while ($record[$a] != '>' && $record[$a] != ':') {
                    $len_str = $len_str . $record[$a];
                    $a++;
                } ;
                if ($record[$a] == ':') {
                    while ($record[$a] != '>') {
                        $a++;
                    } ;
                } ;
                $len = (int)$len_str;
                while ($len > 0) {
                    $a++;
                    $value = $value . $record[$a];
                    $len--;
                } ;
                $return[strtolower($tag_name)] = $value;
            } ;
            // skip comments
            if ($record[$a] == "#") {
                while ($a < strlen($record)) {
                    if ($record[$a] == "\n") {
                        break;
                    }
                    $a++;
                }
            }
        } ;
        // print_a(($return));
        return $return;
    }
    // finds the next record in the file
    public function getRecord() {
        if ($this->i >= strlen($this->data)) {
            return array(); //return nothing
        } ;
        $end = stripos($this->data, "<eor>", $this->i);
        if ($end == false) { // is this the end?
            return array(); //return nothing
        } ;
        $record = substr($this->data, $this->i, $end - $this->i);
        $this->i = $end + 5;
        return $this->recordToArray($record); //process and return output
    }

    public function getHeader($key) {
        if (array_key_exists(strtolower($key), $this->headers)) {
            return $this->headers[strtolower($key)];
        } else {
            return null;
        }
    }
}
/**
* WabAdifParser
*
* @package
* @author Barry
* @copyright Copyright (c) 2015
* @version $Id$
* @access public
*/
class WabAdifParser extends ADIF_Parser {
    /**
    * WabAdifParser::__construct()
    */
    function __construct() {
        // parent:__construct();
        $this->sql = e107::getDb();
    }
    function adifExport($myLog = 0, $user = 0) {
        $myLog = 2;
        $user = 1; // temp
    	 $qry = "SELECT * from #wabloglist
    	 where wabLogListWabUserfk = {$user} and wabLogListID={$myLog} limit 1";
    	$this->sql->gen($qry, true);
    	$row=$this->sql->fetch();
        $row['title']= $row['wabLogTitle'] ;
        $qry = "SELECT * from #wablog
        jeft join #wabuser on wabLogUserfk=wabUserID
        left join #wabcountry on wabCountryCode=wabLogCountryfk
        left join #wabareas on wabLogAreaWorkedFK=wabareaID
where wabLogMyLog={$myLog} and wabLogUserfk={$user}";
      $row['numrecs']=  $this->sql->gen($qry, true);
      $row['bookid']= $myLog ;
      $row['title']= $row['wabLogTitle'] ;
        $fp = fopen("export/file.adi", "w+");
    	var_dump($fp);
    	var_dump($numrecs);
        fwrite($fp, $this->adifHeader($row));
        while ($row = $this->sql->fetch()) {
            fwrite($fp, $this->adifDetail($row));
        }
        fwrite($fp, $this->adifFooter());

        fclose($fp);
        // <QSO_DATE:8>19000101<TIME_ON:6>000000<CALL:5>VY2ZM<FREQ:6>28.458<MODE:3>SSB<RST_SENT:5>59002<RST_RCVD:5>59205<TX_PWR:3>100<QSL_SENT:1>N<QSL_RCVD:1>N<eor>
        // QRZLogbook download for g4hdu
        // Date: Tue Mar 10 12:22:33 2015
        // Bookid: 80859
        // Records: 7
        // <ADIF_VER:5>3.0.4
        // <PROGRAMID:10>QRZLogbook
        // <PROGRAMVERSION:3>2.0
        // <eoh>
        // <app_qrzlog_logid:9>130130924<app_qrzlog_status:1>N<band:3>40m<band_rx:3>40m<call:5>GB4MW<comment:110>Thank you Kevin for the QSO, shame the QSB kept taking you into the noise. Hopefully catch you again. 73 Barry<cont:2>EU<country:7>England<cqz:2>14<distance:3>305<dxcc:3>223<email:29>g4hrc@haveringradioclub.co.uk<freq:5>7.108<freq_rx:5>7.108<gridsquare:6>JO01ep<ituz:2>27<lat:11>N051 38.750<lon:11>E000 22.500<mode:3>SSB<my_city:8>Maghull <my_country:7>England<my_cq_zone:2>14<my_gridsquare:6>IO83mm<my_itu_zone:2>27<my_lat:11>N053 30.917<my_lon:11>W002 56.158<my_name:15>Revd Barry Keal<name:64>Mountnessing Windmill Official 2012 Windmills on the Air station<operator:5>G4HDU<qrzcom_qso_upload_date:8>20120512<qrzcom_qso_upload_status:1>Y<qsl_rcvd:1>N<qsl_sent:1>N<qsl_via:15>VIA RSGB BUREAU<qso_date:8>20120512<qso_date_off:8>20120512<qth:18>Essex UK - JO01EP <rst_rcvd:2>59<rst_sent:2>59<station_callsign:5>G4HDU<time_off:4>1503<time_on:4>1503<tx_pwr:3>100<web:34>http://www.haveringradioclub.co.uk<eor>
    }
    private function adifHeader($logEntry) {
        $retval = "G4HDU Wablog download for {$logEntry['title']}\n";
        $retval .= "Date: ".date("D M j H:i:s Y")."\n";
    	// Tue Mar 10 12:22:33 2015
        $retval .= "Bookid: {$logEntry['bookid']}\n";
        $retval .= "Records: {$logEntry['numrecs']}\n";
        $retval .= "<ADIF_VER:5>3.0.4\n";
        $retval .= "<PROGRAMID:12>G4HDU Wablog\n";
        $retval .= "<PROGRAMVERSION:3>1.0\n";
        $retval .= "<eoh>\n";
        return $retval;
    }
    private function adifFooter() {
        return "<eor>\n";
    }
    private function adifItem($entry = '', $value = '') {
        if (empty($value) || empty($entry)) {
            return'';
        } else {
            return "<{$entry}:" . strlen($value) . ">" . $value;
        }
    }
    private function adifDetail($logEntry) {
        $detail[] = $this->adifItem("app_wablogID", $logEntry['wablogID']);
        $detail[] = $this->adifItem("app_workedfk",  $logEntry['wabLogAreaWorkedFK']);
        $detail[] = $this->adifItem("app_fromfk",  $logEntry['wabLogAreaOpFromFK']);
        $detail[] = $this->adifItem("qso_date", date('Ymd', $logEntry['wabLogStartDate']));
        $detail[] = $this->adifItem("qso_date_off", date('Ymd', $logEntry['wabLogEndDate']));
        $detail[] = $this->adifItem("time_on", date('Hi', $logEntry['wabLogStartDate']));
        $detail[] = $this->adifItem("time_off", date('Hi', $logEntry['wabLogEndDate']));
        $detail[] = $this->adifItem("call", $logEntry['wabLogCallsign']);
        $detail[] = $this->adifItem("freq", $logEntry['wabLogFreq']);
        $detail[] = $this->adifItem("freq_rx", $logEntry['wabLogFreq']);
        $detail[] = $this->adifItem("band", $logEntry['wabBandsName']);
        $detail[] = $this->adifItem("band_rx", $logEntry['wabBandsName']);
        $detail[] = $this->adifItem("rst_rcvd", date('Hi', $logEntry['wabLogRSIn']));
        $detail[] = $this->adifItem("rst_sent", date('Hi', $logEntry['wabLogRSOut']));
        $detail[] = $this->adifItem("mode", $logEntry['wabModesName']);
        $detail[] = $this->adifItem("country", $logEntry['wabCountryName']);
        $detail[] = $this->adifItem("app_wabareaID", $logEntry['wabareaID']);
        $detail[] = $this->adifItem("gridsquare", $logEntry['wabLogIARU']);
        $detail[] = $this->adifItem("qsl_rcvd", $logEntry['wabLogQSLIn'] == 1?'Y':'N');
        $detail[] = $this->adifItem("qsl_sent", $logEntry['wabLogQSLOut'] == 1?'Y':'N');
        $detail[] = $this->adifItem("TX_PWR", $logEntry['wabLogPower']);
        $detail[] = $this->adifItem("QTH", $logEntry['wabLogQTH']);
        $detail[] = $this->adifItem("NAME", $logEntry['wabLogName']);
        $detail[] = $this->adifItem("comment", $logEntry['wabLogNotes']);
$detail[]="\n";
        return implode("", $detail);

        /*
		<app_wablogID:9>130130924
		<app_qrzlog_status:1>N
		<band:3>40m
		<band_rx:3>40m
		<call:5>GB4MW
		<comment:110>Thank you Kevin for the QSO, shame the QSB kept taking you into the noise. Hopefully catch you again. 73 Barry
		<cont:2>EU
		<country:7>England
		<cqz:2>14
		<distance:3>305
		<dxcc:3>223
		<email:29>g4hrc@haveringradioclub.co.uk
		<freq:5>7.108
		<freq_rx:5>7.108
		<gridsquare:6>JO01ep
		<ituz:2>27
		<lat:11>N051 38.750
		<lon:11>E000 22.500
		<mode:3>SSB
		<my_city:8>Maghull
		<my_country:7>England
		<my_cq_zone:2>14
		<my_gridsquare:6>IO83mm
		<my_itu_zone:2>27
		<my_lat:11>N053 30.917
		<my_lon:11>W002 56.158
		<my_name:15>Revd Barry Keal
		<name:64>Mountnessing Windmill Official 2012 Windmills on the Air station
		<operator:5>G4HDU
		<qrzcom_qso_upload_date:8>20120512
		<qrzcom_qso_upload_status:1>Y
		<qsl_rcvd:1>N
		<qsl_sent:1>N
		<qsl_via:15>VIA RSGB BUREAU
		<qso_date:8>20120512
		<qso_date_off:8>20120512
		<qth:18>Essex UK - JO01EP
		<rst_rcvd:2>59
		<rst_sent:2>59
		<station_callsign:5>G4HDU
		<time_off:4>1503
		<time_on:4>1503
		<tx_pwr:3>100
		<web:34>http://www.haveringradioclub.co.uk
		<eor>
		   */
    }
} // end class

?>