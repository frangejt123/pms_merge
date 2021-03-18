<?php
date_default_timezone_set("Asia/Manila");

//foreach($kit as $ind => $row){
//	if(isset($row["parent"])){
//		foreach($row["parent"] as $pind => $prow) {
//			if(!isset($prow["price"])){
//				print_r($prow);
//			}
//		}
//	}
//}
//return;

class PDFREPORT extends TCPDF {

	var $htmlHeader;
	var $htmlHeaderAddress;
	var $periodDate;

	public function setHtmlHeader($htmlHeader) {
		$this->htmlHeader = $htmlHeader;
	}

	public function setHtmlHeaderAddress($htmlHeaderAddress) {
		$this->htmlHeaderAddress = $htmlHeaderAddress;
	}

	public function setPeriodDate($periodDate) {
		$this->htmlPeriodDate = $periodDate;
	}

	public function Header() {
		$this->setFont("Helvetica", 'B', 15);
		$this->writeHTMLCell(0, 0, 0, 4,
			'RIBSHACK GRILL', 0, 1, 0, '', 'C');
		$this->setFont("Helvetica", '', 10);
		$this->writeHTMLCell(0, 0, 0, 10,
			'OPERATED BY: '.$this->htmlHeader, 0, 1, 0, '', 'C');
		$this->writeHTMLCell(0, 0, 0, 15,
			$this->htmlHeaderAddress, 0, 1, 0, '', 'C');
		//$currentdate = date("F d, Y h:iA");
		$this->writeHTML("<hr>", true, false, false, false, '');
		//$this->writeHTMLCell(0, 0, 6.5, 25, "DRINK PERCENTAGE", 0, 1, 0, '', 'L');
		//$this->writeHTMLCell(0, 0, 0, 25, $this->htmlPeriodDate, 0, 1, 0, '', 'R');
	}

	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 7);
		// Page number
		$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().' / '.$this->getAliasNbPages(), 0, false, 'L', 0, '', 0, false, 'T', 'M');
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		$this->Cell(0, 10, date("F d, Y h:iA"),
			0, false, 'R', 0, '', 0, false, 'T', 'M');
		$this->Cell(0, 17, 'Printed By: '.$_SESSION["rgc_firstname"]." ".$_SESSION["rgc_lastname"],
			0, false, 'R', 0, '', 0, false, 'T', 'M');
	}

}
// create new PDF document
$pdf = new PDFREPORT("P", 'mm', 'LETTER', true, 'UTF-8', false);

// set auto page breaks
$pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);
$pdf->SetTitle('RIBSHACK DRINK PERCENTAGE');
// set default header data
$pdf->setHtmlHeader($operated_by);
$pdf->setHtmlHeaderAddress($address);
//$pdf->setPeriodDate($period_date);

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 7);
$pdf->writeHTMLCell(50, 0, 5, 34, "CODE", 1, 1, 0, 'top', '');
$pdf->writeHTMLCell(100, 0, 55, 34, "DESCRIPTION", 1, 1, 0, 'top', '');
$pdf->writeHTMLCell(20, 0, 155, 34, "PRICE", 1, 1, 0, 'top', '');

$y = 42;
$counter = 1;
//$meals_possold = 0;
foreach($kit as $ind => $row){
	$pdf->SetFont('helvetica', '', 12);
	$pdf->writeHTMLCell(50, 0, 5, $y, $row["product_code"], 0, 1, 0, 'top', '');
	$pdf->writeHTMLCell(100, 0, 55, $y, $row["description"], 0, 1, 0, 'top', '');
	$pdf->writeHTMLCell(20, 0, 155, $y, $row["price"], 0, 1, 0, 'top', '');

	if(isset($row["parent"])){
		$y += 5;
		$pdf->SetFont('helvetica', '', 9);
		$pdf->writeHTMLCell(53, 0, 8, $y, "KIT CODE", 0, 1, 0, 'top', '');
		$pdf->writeHTMLCell(100, 0, 58, $y, "KIT DESCRIPTION", 0, 1, 0, 'top', '');
		$pdf->writeHTMLCell(20, 0, 158, $y, "QUANTITY", 0, 1, 0, 'top', '');
			foreach($row["parent"] as $pind => $prow) {
				$pdf->SetFont('helvetica', '', 8);
				$y += 5;
				$pdf->writeHTMLCell(53, 0, 8, $y, $prow["parent_id"], 0, 1, 0, 'top', '');
				$pdf->writeHTMLCell(100, 0, 58, $y, $prow["description"], 0, 1, 0, 'top', '');
				$pdf->writeHTMLCell(20, 0, 158, $y, $prow["quantity"], 0, 1, 0, 'top', '');
			}
	}

//	$meals_possold += $row["pos_total"];
	$y += 7;
	if($counter >= 10){
		$pdf->AddPage();
		$y = 42;
		$counter = 1;
	}else{
		$counter++;
	}

	$pdf->writeHTMLCell(200, 0, 3, $y, "<hr />", 0, 1, 0, 'top', '');
}

$pdf->SetFont('helvetica', 'B', 15);
// write the first column
//$pdf->writeHTMLCell(100, 0, 5, 34, 'Total Meals : '.$meals_possold, 0, 1, 0, 'top', '');
// write the second column
//$pdf->writeHTMLCell(100, 0, 110, 34, 'Total Beverages : '.$beverage_possold, 0, 1, 0, 'top', '');

//$pecentheight = 0;
//if($height > $bheight){
//	$pecentheight = $height + 5;
//}else{
//	$pecentheight = $bheight + 5;
//}

//$pecentage = 0;
//if($beverage_possold > 0 || $meals_possold > 0)
//	$pecentage = number_format((($beverage_possold / $meals_possold) * 100), 2);

$pdf->SetFont('helvetica', 'B', 20);
//$pdf->writeHTMLCell(180, 0, 5, $pecentheight, 'Total Percentage : '.$pecentage.'%', 0, 1, 0, 'top', '');


$pdf->Output();
// $pdf->Write(5, 'Some sample text');

?>
