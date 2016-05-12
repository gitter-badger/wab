<?php

/**
* Simple excel generating from PHP5
*
* @package Utilities
* @license http://www.opensource.org/licenses/mit-license.php
* @author Oliver Schwarz <oliver.schwarz@gmail.com>
* @version 1.0
*/

/**
* Generating excel documents on-the-fly from PHP5
*
* Uses the excel XML-specification to generate a native
* XML document, readable/processable by excel.
*
* @package Utilities
* @subpackage Excel
* @author Oliver Schwarz <oliver.schwarz@vaicon.de>
* @version 1.1
* @todo Issue #4: Internet Explorer 7 does not work well with the given header
* @todo Add option to give out first line as header (bold text)
* @todo Add option to give out last line as footer (bold text)
* @todo Add option to write to file
*/
class Excel_XML {
    /**
    * Header (of document)
    *
    * @var string
    */
    private $header = "<?xml version=\"1.0\" encoding=\"%s\"?\>\n<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:html=\"http://www.w3.org/TR/REC-html40\">";

    /**
    * Footer (of document)
    *
    * @var string
    */
    private $footer = "</Workbook>";

    /**
    * Lines to output in the excel document
    *
    * @var array
    */
    private $lines = array();

    /**
    * Used encoding
    *
    * @var string
    */
    private $sEncoding;

    /**
    * Convert variable types
    *
    * @var boolean
    */
    private $bConvertTypes;

    /**
    * Worksheet title
    *
    * @var string
    */
    private $sWorksheetTitle;

    /**
    * Constructor
    *
    * The constructor allows the setting of some additional
    * parameters so that the library may be configured to
    * one's needs.
    *
    * On converting types:
    * When set to true, the library tries to identify the type of
    * the variable value and set the field specification for Excel
    * accordingly. Be careful with article numbers or postcodes
    * starting with a '0' (zero)!
    *
    * @param string $sEncoding Encoding to be used (defaults to UTF-8)
    * @param boolean $bConvertTypes Convert variables to field specification
    * @param string $sWorksheetTitle Title for the worksheet
    */
    public function __construct($sEncoding = 'UTF-8', $bConvertTypes = false, $sWorksheetTitle = 'Table1') {
        $this->bConvertTypes = $bConvertTypes;
        $this->setEncoding($sEncoding);
        $this->setWorksheetTitle($sWorksheetTitle);
    }

    /**
    * Set encoding
    *
    * @param string $ Encoding type to set
    */
    public function setEncoding($sEncoding) {
        $this->sEncoding = $sEncoding;
    }

    /**
    * Set worksheet title
    *
    * Strips out not allowed characters and trims the
    * title to a maximum length of 31.
    *
    * @param string $title Title for worksheet
    */
    public function setWorksheetTitle ($title) {
        $title = preg_replace ("/[\\\|:|\/|\?|\*|\[|\]]/", "", $title);
        $title = substr ($title, 0, 31);
        $this->sWorksheetTitle = $title;
    }

    /**
    * Add row
    *
    * Adds a single row to the document. If set to true, self::bConvertTypes
    * checks the type of variable and returns the specific field settings
    * for the cell.
    *
    * @param array $array One-dimensional array with row content
    */
    private function addRow ($array) {
        $cells = "";
        foreach ($array as $k => $v):
        $type = 'String';
        if ($this->bConvertTypes === true && is_numeric($v)):
            $type = 'Number';
        endif;
        $v = htmlentities($v, ENT_COMPAT, $this->sEncoding);
        $cells .= "<Cell><Data ss:Type=\"$type\">" . $v . "</Data></Cell>\n";
        endforeach;
        $this->lines[] = "<Row>\n" . $cells . "</Row>\n";
    }

    /**
    * Add an array to the document
    *
    * @param array $ 2-dimensional array
    */
    public function addArray ($array) {
        foreach ($array as $k => $v)
        $this->addRow ($v);
    }

    /**
    * Generate the excel file
    *
    * @param string $filename Name of excel file to generate (...xls)
    */
    public function generateXML ($filename = 'excel-export') {
        // correct/validate filename
        $filename = preg_replace('/[^aA-zZ0-9\_\-]/', '', $filename);
        // deliver header (as recommended in php manual)
        header("Content-Type: application/vnd.ms-excel; charset=" . $this->sEncoding);
        header("Content-Disposition: inline; filename=\"" . $filename . ".xls\"");
        // print out document to the browser
        // need to use stripslashes for the damn ">"
        echo stripslashes (sprintf($this->header, $this->sEncoding));
        echo "\n<Worksheet ss:Name=\"" . $this->sWorksheetTitle . "\">\n<Table>\n";
        foreach ($this->lines as $line)
        echo $line;

        echo "</Table>\n</Worksheet>\n";
        echo $this->footer;
    }
}

/*
   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*

   Filename	: export.xls.class.php
   Description	: A small light weight PHP class to allow the creation of simple xls excel spreadsheets from array data.
   Version 	: 1.01
   Author 		: Leenix
   Website		: http://www.leenix.co.uk
*/

/*
   Change Log
   V1 - First Release
   1.01 - Fixed UTF8 Issue
*/

class ExportXLS {
    private $filename; //Filename which the excel file will be returned as
    private $headerArray; // Array which contains header information
    private $bodyArray; // Array with the spreadsheet body
    private $rowNo = 0; // Keep track of the row numbers

    // Class constructor
    function __construct($filename) {
        $this->filename = $filename;
    }

    /*
	   -------------------------
	   START OF PUBLIC FUNCTIONS
	   -------------------------
	*/

    public function addHeader($header) {
        // Accepts an array or var which gets added to the top of the spreadsheet as a header.
        if (is_array($header)) {
            $this->headerArray[] = $header;
        }else {
            $this->headerArray[][0] = $header;
        }
    }

    public function addRow($row) {
        // Accepts an array or var which gets added to the spreadsheet body
        if (is_array($row)) {
            // check for multi dim array
            if (is_array($row[0])) {
                foreach($row as $key => $array) {
                    $this->bodyArray[] = $array;
                }
            }else {
                $this->bodyArray[] = $row;
            }
        }else {
            $this->bodyArray[][0] = $row;
        }
    }

    public function returnSheet() {
        // returns the spreadsheet as a variable
        // build the xls
        return $this->buildXLS();
    }

    public function sendFile() {
        // build the xls
        $xls = $this->buildXLS();
        // send headers
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=" . $this->filename);
        header("Content-Transfer-Encoding: binary ");

        echo $xls;

        exit;
    }

    /*
	   --------------------------
	   START OF PRIVATE FUNCTIONS
	   --------------------------
	*/

    private function buildXLS() {
        // build and return the xls
        // Excel BOF
        $xls = pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
        // build headers
        if (is_array($this->headerArray)) {
            $xls .= $this->build($this->headerArray);
        }
        // build body
        if (is_array($this->bodyArray)) {
            $xls .= $this->build($this->bodyArray);
        }

        $xls .= pack("ss", 0x0A, 0x00);

        return $xls;
    }

    private function build($array) {
        // build and return the headers
        foreach($array as $key => $row) {
            $colNo = 0;
            foreach($row as $key2 => $field) {
                if (is_numeric($field)) {
                    $build .= $this->numFormat($this->rowNo, $colNo, $field);
                }else {
                    $build .= $this->textFormat($this->rowNo, $colNo, $field);
                }

                $colNo++;
            }
            $this->rowNo++;
        }

        return $build;
    }

    private function textFormat($row, $col, $data) {
        // format and return the field as a header
        $data = utf8_decode($data);
        $length = strlen($data);
        $field = pack("ssssss", 0x204, 8 + $length, $row, $col, 0x0, $length);
        $field .= $data;

        return $field;
    }

    private function numFormat($row, $col, $data) {
        // format and return the field as a header
        $field = pack("sssss", 0x203, 14, $row, $col, 0x0);
        $field .= pack("d", $data);

        return $field;
    }
}

?>