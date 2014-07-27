<?php

class EAN13 {
    private $PARITY_KEY = array(
        0 => "000000",
        1 => "001011",
        2 => "001101",
        3 => "001110",
        4 => "010011",
        5 => "011001",
        6 => "011100",
        7 => "010101",
        8 => "010110",
        9 => "011010"
    );

    private $LEFT_PARITY = array(
        // Odd Encoding
        0 => array (
            0 => "0001101",
            1 => "0011001",
            2 => "0010011",
            3 => "0111101",
            4 => "0100011",
            5 => "0110001",
            6 => "0101111",
            7 => "0111011",
            8 => "0110111",
            9 => "0001011"
        ),
        // Even Encoding
        1 => array (
            0 => "0100111",
            1 => "0110011",
            2 => "0011011",
            3 => "0100001",
            4 => "0011101",
            5 => "0111001",
            6 => "0000101",
            7 => "0010001",
            8 => "0001001",
            9 => "0010111"
        )
    );

    private $RIGHT_PARITY = array(
        0 => "1110010",
        1 => "1100110",
        2 => "1101100",
        3 => "1000010",
        4 => "1011100",
        5 => "1001110",
        6 => "1010000",
        7 => "1000100",
        8 => "1001000",
        9 => "1110100"
    );

    private $GUARD =   array(
        'start' => "101",
        'middle' => "01010",
        'end' => "101",
    );

    private $_key;
    private $_checksum;
    private $_number;

    function __construct($number) {
        // Get the parity key, which is based on the first digit.
        $this->_key = $this->PARITY_KEY[substr($number,0,1)];

        // The checksum is appended to the 12 digit string
        $this->_checksum = $this->ean_checksum($number);
        $this->_number = $number . $this->_checksum;
    }

    public function get_number_ckecksum() {
        return $this->_number;
    }

    public function get_bars($number) {
        return $this->_encode();
    }

    /**
    * The following incantations use the parity key (based off the
    * first digit of the unencoded number) to encode the first six
    * digits of the barcode. The last 6 use the same parity.
    *
    * So, if the key is 010101, the first digit (of the first six
    * digits) uses odd parity encoding. The second uses even. The
    * third uses odd, and so on.
    */

    protected function _encode() {
        $barcode[] = $this->GUARD['start'];
        for($i=1;$i<=strlen($this->_number)-1;$i++) {
            if($i<7) {
                $barcode[] = $this->LEFT_PARITY[$this->_key[$i-1]][substr($this->_number, $i, 1)];
            } else {
                $barcode[] = $this->RIGHT_PARITY[substr($this->_number, $i, 1)];
            }
            if($i==6) {
                $barcode[] = $this->GUARD['middle'];
            }
        }
        $barcode[] = $this->GUARD['end'];

        return $barcode;
   }


    private function ean_checksum($ean){
        $ean=(string)$ean;
        $even=true; $esum=0; $osum=0;

        for ($i=strlen($ean)-1;$i>=0;$i--){
            if ($even) {
                $esum+=$ean[$i];
            } else {
                $osum+=$ean[$i];
            }
            $even=!$even;

        }
        return (10-((3*$esum+$osum)%10))%10;
    }

}
