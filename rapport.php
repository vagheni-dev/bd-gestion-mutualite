<?php
require_once('tcpdf_include.php');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database_name";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from database
$sql = "SELECT nom, poste, genre, age, fonction, photo FROM agents";
$result = $conn->query($sql);

$agents = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $agents[] = $row;
    }
} else {
    echo "0 results";
    exit;
}
$conn->close();

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('mutualite');
$pdf->SetTitle('Carte de Service');
$pdf->SetSubject('Carte de Service');

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'mutualite', 'Carte de Service');

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 10);

// Define the number of cards per page
$cards_per_page = 6;
$card_width = 90;
$card_height = 50;
$margin = 10;

// Loop through agents and create cards
foreach ($agents as $index => $agent) {
    if ($index % $cards_per_page == 0 && $index != 0) {
        $pdf->AddPage();
    }

    $x = ($index % 2) * ($card_width + $margin) + $margin;
    $y = (floor($index / 2) % 3) * ($card_height + $margin) + $margin;

    // Draw card border
    $pdf->SetXY($x, $y);
    $pdf->Cell($card_width, $card_height, '', 1, 1, 'C', 0, '', 0, false, 'T', 'M');

    // Add photo
    $pdf->SetXY($x + 5, $y + 5);
    // Check if the photo file exists
    $photo_path = 'images/' . $agent['photo'];
    if (file_exists($photo_path)) {
        $pdf->Image($photo_path, $x + 5, $y + 5, 20, 20, '', '', '', false, 300, '', false, false, 0, false, false, false);
    } else {
        // If the photo does not exist, add a placeholder image
        $pdf->Image('images/placeholder.png', $x + 5, $y + 5, 20, 20, '', '', '', false, 300, '', false, false, 0, false, false, false);
    }

    // Add text
    $pdf->SetXY($x + 30, $y + 5);
    $pdf->MultiCell(55, 5, "Nom: " . $agent['nom'] . "\nPoste: " . $agent['poste'] . "\nGenre: " . $agent['genre'] . "\nAge: " . $agent['age'] . "\nFonction: " . $agent['fonction'], 0, 'L', 0, 1, '', '', true);
}

// Close and output PDF document
$pdf->Output('carte_de_service.pdf', 'I');
?>
