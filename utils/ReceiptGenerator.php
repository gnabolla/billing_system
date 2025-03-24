<?php

require_once __DIR__ . '/../vendor/fpdf/fpdf.php';

class ReceiptGenerator extends FPDF
{
    private $company;
    private $payment;
    private $statement;
    private $subscriber;
    private $items;
    
    public function __construct($company, $payment, $statement, $subscriber, $items)
    {
        parent::__construct();
        $this->company = $company;
        $this->payment = $payment;
        $this->statement = $statement;
        $this->subscriber = $subscriber;
        $this->items = $items;
    }
    
    public function generate()
    {
        $this->AddPage();
        
        // Add header
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, $this->company['company_name'], 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, $this->company['address'] ?? '', 0, 1, 'C');
        $this->Cell(0, 6, 'Phone: ' . ($this->company['contact_phone'] ?? ''), 0, 1, 'C');
        $this->Cell(0, 6, 'Email: ' . ($this->company['contact_email'] ?? ''), 0, 1, 'C');
        
        $this->Ln(10);
        
        // Receipt title
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'OFFICIAL RECEIPT', 0, 1, 'C');
        
        $this->Ln(5);
        
        // Receipt details
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(40, 8, 'OR Number:', 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(60, 8, $this->payment['or_no'], 0);
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(40, 8, 'OR Date:', 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(60, 8, date('F d, Y', strtotime($this->payment['or_date'])), 0, 1);
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(40, 8, 'Payment Date:', 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(60, 8, date('F d, Y', strtotime($this->payment['payment_date'])), 0);
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(40, 8, 'Payment Method:', 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(60, 8, $this->payment['payment_method'], 0, 1);
        
        $this->Ln(5);
        
        // Subscriber details
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Received From:', 0, 1);
        
        $this->SetFont('Arial', '', 10);
        if (!empty($this->subscriber['company_name'])) {
            $this->Cell(0, 6, $this->subscriber['company_name'], 0, 1);
        } else {
            $this->Cell(0, 6, $this->subscriber['first_name'] . ' ' . $this->subscriber['last_name'], 0, 1);
        }
        $this->Cell(0, 6, 'Account No: ' . $this->subscriber['account_no'], 0, 1);
        $this->Cell(0, 6, $this->subscriber['address'], 0, 1);
        
        $this->Ln(5);
        
        // Statement details
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Payment For:', 0, 1);
        
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, 'Statement No: ' . $this->statement['statement_no'], 0, 1);
        $this->Cell(0, 6, 'Billing Period: ' . date('M d', strtotime($this->statement['bill_period_start'])) . ' to ' . 
                          date('M d, Y', strtotime($this->statement['bill_period_end'])), 0, 1);
        
        $this->Ln(5);
        
        // Create table header
        $this->SetFillColor(230, 230, 230);
        $this->SetFont('Arial', 'B', 10);
        
        $this->Cell(100, 8, 'Description', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Amount', 1, 0, 'C', true);
        $this->Cell(20, 8, 'Tax', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Total', 1, 1, 'C', true);
        
        // Add items
        $this->SetFont('Arial', '', 10);
        
        foreach ($this->items as $item) {
            $this->Cell(100, 8, $item['description'], 1);
            $this->Cell(30, 8, 'P' . number_format($item['amount'], 2), 1, 0, 'R');
            $this->Cell(20, 8, 'P' . number_format($item['tax_amount'], 2), 1, 0, 'R');
            $this->Cell(30, 8, 'P' . number_format($item['total_amount'], 2), 1, 1, 'R');
        }
        
        // Add totals
        $this->SetFont('Arial', 'B', 10);
        
        $this->Cell(150, 8, 'Total Statement Amount:', 1, 0, 'R');
        $this->Cell(30, 8, 'P' . number_format($this->statement['total_amount'], 2), 1, 1, 'R');
        
        $this->Cell(150, 8, 'Amount Paid:', 1, 0, 'R');
        $this->Cell(30, 8, 'P' . number_format($this->payment['paid_amount'], 2), 1, 1, 'R');
        
        if ($this->statement['unpaid_amount'] > 0) {
            $this->Cell(150, 8, 'Remaining Balance:', 1, 0, 'R');
            $this->Cell(30, 8, 'P' . number_format($this->statement['unpaid_amount'], 2), 1, 1, 'R');
        }
        
        $this->Ln(10);
        
        // Signatures
        $this->Cell(90, 8, 'Received By:', 0, 0);
        $this->Cell(90, 8, 'Customer Signature:', 0, 1);
        
        $this->Ln(15);
        
        $this->Cell(90, 0, '', 'T', 0, 'C');
        $this->Cell(20, 0, '', 0, 0);
        $this->Cell(70, 0, '', 'T', 1, 'C');
        
        // Footer with note
        $this->Ln(10);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 6, 'Thank you for your payment. This receipt is evidence of payment.', 0, 1, 'C');
        $this->Cell(0, 6, 'Please keep this receipt for your records.', 0, 1, 'C');
        
        // Return the PDF as a string
        return $this->Output('S');
    }
}