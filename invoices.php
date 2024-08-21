<?php
require('fpdf/fpdf.php'); // Include FPDF library for PDF generation

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'inventory_system';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch sales and invoice data
if (isset($_GET['invoice_id'])) {
    $invoice_id = $_GET['invoice_id'];
    $invoice = $conn->query("
        SELECT invoices.*, sales.*, products.product_name, products.price 
        FROM invoices 
        JOIN sales ON invoices.sale_id = sales.id 
        JOIN products ON sales.product_id = products.id 
        WHERE invoices.id = $invoice_id
    ")->fetch_assoc();
} else {
    die("Invoice not found.");
}

// Generate PDF invoice
if (isset($_GET['action']) && $_GET['action'] == 'generate_pdf') {
    $pdf = new FPDF();
    $pdf->AddPage();

    // Invoice Header
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(190, 10, 'Invoice', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(190, 10, 'Company Name', 0, 1, 'C');
    $pdf->Cell(190, 10, 'Address, City, ZIP', 0, 1, 'C');
    $pdf->Cell(190, 10, 'Phone: +1234567890 | Email: info@company.com', 0, 1, 'C');
    $pdf->Ln(10);

    // Customer Information
    $pdf->Cell(100, 10, 'Customer Name: ' . $invoice['customer_name'], 0, 1);
    $pdf->Cell(100, 10, 'Customer Email: ' . $invoice['customer_email'], 0, 1);
    $pdf->Cell(100, 10, 'Invoice Date: ' . date('d-m-Y', strtotime($invoice['issue_date'])), 0, 1);
    $pdf->Ln(10);

    // Invoice Details
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(90, 10, 'Product Name', 1);
    $pdf->Cell(30, 10, 'Quantity', 1);
    $pdf->Cell(30, 10, 'Unit Price', 1);
    $pdf->Cell(40, 10, 'Total Price', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(90, 10, $invoice['product_name'], 1);
    $pdf->Cell(30, 10, $invoice['quantity'], 1);
    $pdf->Cell(30, 10, '$' . number_format($invoice['price'], 2), 1);
    $pdf->Cell(40, 10, '$' . number_format($invoice['total_price'], 2), 1);
    $pdf->Ln(20);

    // Footer with total price
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(150, 10, 'Total Amount:', 0, 0, 'R');
    $pdf->Cell(40, 10, '$' . number_format($invoice['total_price'], 2), 0, 1, 'R');

    $pdf->Output('I', 'Invoice_' . $invoice_id . '.pdf');
    exit;
}

// HTML display for the invoice
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        /* Invoice styling */
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .invoice-container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .invoice-header, .invoice-details, .invoice-footer {
            margin-bottom: 20px;
        }
        .invoice-header {
            text-align: center;
        }
        .invoice-details th, .invoice-details td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .invoice-footer {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
        }
        .print-button, .pdf-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #ff5f5f;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .print-button:hover, .pdf-button:hover {
            background: #ff2d2d;
        }
    </style>
</head>
<body>

<div class="invoice-container">
    <h1>Invoice</h1>
    <div class="invoice-header">
        <p>Company Name</p>
        <p>Address, City, ZIP</p>
        <p>Phone: +1234567890 | Email: info@company.com</p>
    </div>

    <div class="invoice-customer">
        <p><strong>Customer Name:</strong> <?php echo $invoice['customer_name']; ?></p>
        <p><strong>Customer Email:</strong> <?php echo $invoice['customer_email']; ?></p>
        <p><strong>Invoice Date:</strong> <?php echo date('d-m-Y', strtotime($invoice['issue_date'])); ?></p>
    </div>

    <table class="invoice-details" width="100%">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $invoice['product_name']; ?></td>
                <td><?php echo $invoice['quantity']; ?></td>
                <td>$<?php echo number_format($invoice['price'], 2); ?></td>
                <td>$<?php echo number_format($invoice['total_price'], 2); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="invoice-footer">
        <p>Total Amount: $<?php echo number_format($invoice['total_price'], 2); ?></p>
    </div>

    <a href="javascript:window.print()" class="print-button">Print Invoice</a>
    <a href="invoices.php?invoice_id=<?php echo $invoice_id; ?>&action=generate_pdf" class="pdf-button">Download PDF</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
