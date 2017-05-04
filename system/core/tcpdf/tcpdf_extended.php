<?php

error_reporting(E_ALL & ~E_NOTICE);
require_once 'tcpdf.php';
class MYPDF extends TCPDF
{
    public function Footer()
    {
        $this->SetY(-15);
        //$regular = $this->addTTFfont(SYS_PATH . 'core/tcpdf/fonts/OpenSans-Light.ttf');
        //$this->SetFont($regular, '', 9, '', false);
        //$this->Cell(0, 10, $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        //$this->Cell(0, 10, $this->getAliasNumPage(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

?>